<?php
/**
 * MemberWing-X License Tool (PRIVATE - never distribute with the plugin!)
 *
 * Generates and verifies offline Ed25519-signed license keys for MemberWing-X 8.7+.
 * The plugin verifies keys locally with the embedded PUBLIC key - no license
 * server is needed. Anyone holding keys/private.key can mint licenses, so
 * guard that file.
 *
 * Usage:
 *   php mwx-license-tool.php keygen
 *       One-time: creates keys/private.key + keys/public.key.
 *       Refuses to overwrite existing keys.
 *
 *   php mwx-license-tool.php make --domain=example.com [--expires=2030-12-31|never]
 *                                 [--edition=GA|TSI] [--name="Customer Name <email>"]
 *       Prints a license key. Domain may be:
 *         example.com     - that domain (www. ignored) only
 *         *.example.com   - domain + all subdomains
 *         *               - any domain (unlimited license)
 *
 *   php mwx-license-tool.php verify --key=MWX1.xxxx.yyyy [--domain=example.com]
 *       Decodes and verifies a key (optionally against a domain).
 */

$KEYS_DIR = __DIR__ . '/keys';

function b64url_encode ($bin)  { return rtrim(strtr(base64_encode($bin), '+/', '-_'), '='); }
function b64url_decode ($str)  { return base64_decode(strtr($str, '-_', '+/')); }

function fail ($msg) { fwrite(STDERR, "ERROR: $msg\n"); exit(1); }

if (!extension_loaded('sodium'))
   fail("The 'sodium' PHP extension is required (bundled with PHP 7.2+).");

$args = array();
foreach (array_slice($argv, 2) as $a)
   {
   if (preg_match('/^--([a-z]+)=(.*)$/s', $a, $m))
      $args[$m[1]] = $m[2];
   }

$cmd = isset($argv[1]) ? $argv[1] : '';

switch ($cmd)
   {
   //------------------------------------------------------------------
   case 'keygen':
      if (file_exists("$KEYS_DIR/private.key"))
         fail("$KEYS_DIR/private.key already exists - refusing to overwrite.\n" .
              "Delete it manually ONLY if you intend to invalidate every key ever issued.");

      if (!is_dir($KEYS_DIR))
         mkdir($KEYS_DIR, 0700, true);

      $kp      = sodium_crypto_sign_keypair();
      $private = sodium_crypto_sign_secretkey($kp);
      $public  = sodium_crypto_sign_publickey($kp);

      file_put_contents("$KEYS_DIR/private.key", base64_encode($private) . "\n");
      chmod("$KEYS_DIR/private.key", 0600);
      file_put_contents("$KEYS_DIR/public.key", base64_encode($public) . "\n");

      echo "Keypair created.\n";
      echo "  PRIVATE key (keep secret!): $KEYS_DIR/private.key\n";
      echo "  PUBLIC  key               : $KEYS_DIR/public.key\n\n";
      echo "Embed this public key in the plugin (MWX_LICENSE_PUBLIC_KEY in mwx-admin.php):\n\n";
      echo "  " . base64_encode($public) . "\n";
      break;

   //------------------------------------------------------------------
   case 'make':
      if (!file_exists("$KEYS_DIR/private.key"))
         fail("No private key found. Run: php mwx-license-tool.php keygen");

      $domain = isset($args['domain']) ? strtolower(trim($args['domain'])) : '';
      if ($domain === '')
         fail("--domain is required (use --domain='*' for an unlimited any-domain license).");
      if (!preg_match('/^(\*|(\*\.)?[a-z0-9.-]+)$/', $domain))
         fail("Domain '$domain' doesn't look valid.");

      $expires = isset($args['expires']) ? strtolower(trim($args['expires'])) : 'never';
      if ($expires !== 'never')
         {
         $ts = strtotime($expires . ' 23:59:59');
         if ($ts === false || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $expires))
            fail("--expires must be YYYY-MM-DD or 'never'.");
         }

      $edition = isset($args['edition']) ? strtoupper(trim($args['edition'])) : 'GA';
      if (!in_array($edition, array('GA', 'TSI')))
         fail("--edition must be GA or TSI.");

      $name = isset($args['name']) ? trim($args['name']) : '';

      $payload = json_encode(array(
         'v' => 1,                                  // key format version
         'd' => $domain,                            // licensed domain
         'x' => $expires,                           // expiry date or 'never'
         'e' => $edition,                           // GA | TSI
         'n' => $name,                              // licensee (informational)
         'i' => b64url_encode(random_bytes(6)),     // unique key id
         't' => gmdate('Y-m-d'),                    // issued on
         ), JSON_UNESCAPED_SLASHES);

      $private = base64_decode(trim(file_get_contents("$KEYS_DIR/private.key")));
      $sig     = sodium_crypto_sign_detached($payload, $private);

      $key = 'MWX1.' . b64url_encode($payload) . '.' . b64url_encode($sig);

      echo "License key for domain '$domain' (edition $edition, expires $expires):\n\n";
      echo "$key\n";
      break;

   //------------------------------------------------------------------
   case 'verify':
      if (!isset($args['key']))
         fail("--key is required.");
      if (!file_exists("$KEYS_DIR/public.key"))
         fail("No public key found. Run keygen first.");

      $public = base64_decode(trim(file_get_contents("$KEYS_DIR/public.key")));

      $parts = explode('.', trim($args['key']));
      if (count($parts) !== 3 || $parts[0] !== 'MWX1')
         fail("Malformed key (expected MWX1.<payload>.<signature>).");

      $payload = b64url_decode($parts[1]);
      $sig     = b64url_decode($parts[2]);

      if (!sodium_crypto_sign_verify_detached($sig, $payload, $public))
         fail("SIGNATURE INVALID - this key was not issued with your private key.");

      $info = json_decode($payload, true);
      echo "Signature: VALID\n";
      echo "Payload:\n";
      foreach ($info as $k => $v)
         {
         $label = array('v'=>'format', 'd'=>'domain', 'x'=>'expires', 'e'=>'edition', 'n'=>'licensee', 'i'=>'key id', 't'=>'issued');
         printf("  %-8s: %s\n", isset($label[$k]) ? $label[$k] : $k, $v);
         }

      if ($info['x'] !== 'never' && strtotime($info['x'] . ' 23:59:59') < time())
         echo "NOTE: key is EXPIRED.\n";

      if (isset($args['domain']))
         {
         $host = strtolower(preg_replace('/^www\./', '', $args['domain']));
         $lic  = $info['d'];
         $match = ($lic === '*') ||
                  ($lic === $host) ||
                  (strpos($lic, '*.') === 0 &&
                     ($host === substr($lic, 2) || substr($host, -strlen(substr($lic, 1))) === substr($lic, 1)));
         echo "Domain check vs '{$args['domain']}': " . ($match ? "MATCH" : "NO MATCH") . "\n";
         }
      break;

   //------------------------------------------------------------------
   default:
      echo "Usage: php mwx-license-tool.php keygen | make | verify   (see file header for options)\n";
      exit(1);
   }
