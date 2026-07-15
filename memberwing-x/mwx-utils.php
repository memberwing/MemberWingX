<?php
/*
      Wordpress-independent functions
*/
/* **************************************************************************
This software is provided "as is" without any express or implied warranties,
including, but not limited to, the implied warranties of merchantibility and
fitness for any purpose.
In no event shall the copyright owner, website owner or contributors be liable
for any direct, indirect, incidental, special, exemplary, or consequential
damages (including, but not limited to, procurement of substitute goods or services;
loss of use, data, rankings with any search engines, any penalties for usage of
this software or loss of profits; or business interruption) however caused and
on any theory of liability, whether in contract, strict liability, or
tort(including negligence or otherwise) arising in any way out of the use of
this software, even if advised of the possibility of such damage.
To request source code for MemberWing please contact http://www.memberwing.com/contact
************************************************************************** */


//===========================================================================
function MWX__ErrorExit ($_file, $_line, $extra_message)
{
   $error_message =<<<TTT
<html>
   <head>
      <title>Wordpress Membership plugin - MemberWing-X</title>
   </head>
   <body>
      <h1 align="center" style="font-size: 70%;"><a href="http://www.memberwing.com/">Wordpress Membership plugin - MemberWing-X</a></h1>
      <h2>$extra_message</h2>
      <div align="center">
         <h2><a href="{$_SERVER['HTTP_REFERER']}">Back</a></h2>
      </div>
   </body>
</html>
TTT;

   MWX__log_event ($_file, $_line, "ERROR: Adaptive Payment Script: " . strip_tags($extra_message));
   exit ($error_message);
}
//===========================================================================

//===========================================================================
// Redirects buyer to PayPal.com site to complete purchase.
// @param string $pay_key pay key issued by previous adaptive API call.
function MWX__RedirectToPayPal ($mwx_settings, $pay_key)
{
   // Redirect to paypal.com here
   if ($mwx_settings['paypal_sandbox_enabled'])
      $paypal_url = "https://www.sandbox.paypal.com/webscr?cmd=_ap-payment&paykey=" . $pay_key;
   else
      $paypal_url = "https://www.paypal.com/webscr?cmd=_ap-payment&paykey=" . $pay_key;

   header("Location: ". $paypal_url);
}
//===========================================================================

//===========================================================================
//
// Get array of MemberWing-X Affiliate Network API Endpoints

function MWX__GetMWXAN_API_Endpoints ($mwx_settings)
{
   if ($mwx_settings['paypal_sandbox_enabled'])
      $api_call_type = 'sandbox';
   else
      $api_call_type = 'live';

   $api_endpoints = array (
      "http://$api_call_type.memberwingx.com/affiliate-network/pay",
      "http://$api_call_type.memberwing-x.com/affiliate-network/pay",
      );

   return ($api_endpoints);
}
//===========================================================================

//===========================================================================
// Allows debug break with ActiveState Komodo Environment

function MWX__URL_DebugStr ($mwx_settings)
{
   if ($mwx_settings['paypal_sandbox_enabled'] && MWX__DebuggingComputer ($mwx_settings))
      return ('XDEBUG_SESSION_START=1');

   return "";
}

// If you use ActiveState Komodo for debugging on your local machine - put your IP address here
// Advanced developers only, don't bother
function MWX__DebuggingComputer ($mwx_settings)
{
   if ($_SERVER['REMOTE_ADDR'] == $mwx_settings['sandbox_machine_ip_address'])
      return TRUE;
   return FALSE;
}
//===========================================================================

//===========================================================================
// Store custom data to be sent with payment button form, Adaptive Pay call or retrieved from IPN
// $aff_raw_id  - ID of affiliate as taken from cookie or from query string. Could be email or numeric ID.
// $total_sale_amt - optional. Will be taken from IPN variables, unless it is missing like with Adaptive IPN - then it will be taken from custom.

function MWX__PackCustomData ($passkey, $aff_raw_id, $total_sale_amt="0")
{
   $aff_raw_id = str_replace (',', '-', $aff_raw_id); // Replace commas with '-'. Commas could separate multi-tiered sequence

   // passkey - security measure against forged/fabricated requests.

   // Pack string
   $custom_data = array (
// Eliminated for now to save space (max 127 chars limit). R-value will do randomization purpose
//      "t1:"             .  time(),                    // Page load time in browser. Page maybe cached.
      "evt:"            .  "purchase",                // Event type: 'purchase',
      "ip:"             .  str_replace ('.', '-', $_SERVER['REMOTE_ADDR']),   // To save space. urlencode() leaves '-' untouched
      "ari:"            .  $aff_raw_id,            // Affiliate's raw ID. Might be multi-tiered, such as: "52,8,132"
      "tsa:"            .  $total_sale_amt,
      );
   $custom_data = implode (',', $custom_data);

   $calc_passkey = substr(md5($custom_data . $passkey), -4);

   // Note: max=256 chars.
   //
   // t:1262879366,ip:123.123.123.123,ari:aff@blah.com,tsa:12.34|12345678
   $custom_data .= "|$calc_passkey" . 'R09AF';  // Append randomization value to make it unique. R... will be replaced by Adaptive call to make sure custom data is really unique.
   return $custom_data;
}
//===========================================================================

//===========================================================================
// Store custom data to be sent with "manual" affiliate payout.
// $aff_raw_id  - ID of affiliate. Numeric ID of already existing affiliate
// $total_sale_amt - optional. Will be taken from IPN variables, unless it is missing like with Adaptive IPN - then it will be taken from custom.

function MWX__PackCustomData2 ($passkey, $aff_id, $aff_payout)
{
   $aff_id = str_replace (',', '-', $aff_id); // Replace commas with '-'. Commas could separate multi-tiered sequence

   // passkey - security measure against forged/fabricated requests.

   // Pack string
   $custom_data = array (
// Eliminated for now to save space (max 127 chars limit). R-value will do randomization purpose
//      "t1:"             .  time(),                    // Page load time in browser. Page maybe cached.
      "evt:"            .  "aff_payout",              // Event type: 'aff_payout',
      "aff_id:"         .  $aff_id,
      "aff_payout:"     .  $aff_payout,
      );
   $custom_data = implode (',', $custom_data);

   $calc_passkey = substr(md5($custom_data . $passkey), -4);

   // Note: max=256 chars.
   //
   // t:1262879366,ip:123.123.123.123,ari:aff@blah.com,tsa:12.34|12345678
   $custom_data .= "|$calc_passkey" . 'R09AF';  // Append randomization value to make it unique. R... will be replaced by Adaptive call to make sure custom data is really unique.
   return $custom_data;
}
//===========================================================================

//===========================================================================
// Unpacks data, validate passkey
function MWX__UnpackCustomData ($passkey, $packed_data)
{
   $custom_data = preg_replace ('@R[0-9A-Fa-f]+$@', '', $packed_data);

   $custom_data = explode ('|', stripslashes($custom_data));
   if (count($custom_data) != 2)
      {
      MWX__log_event (__FILE__, __LINE__, "Warning: Invalid custom data (1).", $packed_data);
      return array();
      }

   $calc_passkey = substr(md5($custom_data[0] . $passkey), -4);
   if ($calc_passkey != $custom_data[1])
      {
      MWX__log_event (__FILE__, __LINE__, "Warning: Invalid custom data - passkeys mistmatch. Possible fraud/spoof? embedded passkey={$custom_data[1]}, calculated passkey=$calc_passkey, custom data:", $packed_data);
      return array();
      }

   // Validation successful
   $custom_data_arr = array();
   foreach (explode(',', $custom_data[0]) as $el)
       {
       $el = explode (':', $el);
       if (count($el) == 2)
           $custom_data_arr[urldecode($el[0])] = urldecode($el[1]);
       }

   // Decode IP address
   if (isset($custom_data_arr['ip']))
      $custom_data_arr['ip'] = str_replace ('-', '.', $custom_data_arr['ip']);

   if (isset($custom_data_arr['aff_id']))
      $custom_data_arr['aff_id'] = str_replace ('-', ',', $custom_data_arr['aff_id']); // Restore possible comma in aff raw id.
   if (isset($custom_data_arr['ari']))
      $custom_data_arr['ari'] = str_replace ('-', ',', $custom_data_arr['ari']);       // Restore possible comma in aff raw id.

   return ($custom_data_arr);
}
//===========================================================================

//===========================================================================
// Change random trailer [R=...]
function MWX__RepackCustomData ($packed_data)
{
   if (preg_match ('@R[0-9A-Fa-f]+$@', $packed_data))
      $custom_data = preg_replace ('@R[0-9A-Fa-f]+$@', 'R' . substr(md5(microtime()), -4), $packed_data);
   else
      $custom_data = $packed_data . 'R' . substr(md5(microtime()), -4);

   return $custom_data;
}
//===========================================================================

//===========================================================================
//
// Function packs response information from Paypal Adaptive reply into assoc array.
// Sample of response:
//    'responseEnvelope.timestamp=2009-12-24T17%3A42%3A12.286-08%3A00&responseEnvelope.ack=Success&responseEnvelope.correlationId=d2e5e7c33db87&responseEnvelope.build=1095776&payKey=AP-9AE02925M23353634&paymentExecStatus=COMPLETED';
function MWX__UnwrapAdaptiveResponse ($response)
{
   $res_arr = array();
   foreach (explode('&', $response) as $el)
       {
       $el = explode ('=', $el);
       if (count($el) == 2)
           $res_arr[urldecode($el[0])] = urldecode($el[1]);
       }
   return ($res_arr);
}
//===========================================================================

//===========================================================================
// Replacement for: if ($product['product_status'] == 'active')
function MWX__is_product_active ($product_status)
{
   if ($product_status == 'active' || $product_status == 'active-ending')
      return true;
   return false;
}
//===========================================================================

//===========================================================================
function MWX__log_event ($filename, $linenum, $message, $extra_text="")
{
   $log_filename   = dirname(__FILE__) . '/__log.php';
   $logfile_header = '<?php include_once (dirname(__FILE__) . "/mwx-include-all.php"); $mwx_settings = MWX__get_settings (); if ($mwx_settings["mwx_api_key"] != @$_POST["mwx_api_key"]) { header("Location: /"); exit();} ?>' . "\r\n" . '/* =============== MemberWing-X LOG file =============== */' . "\r\n";
   $logfile_tail   = "\r\nEND";

   // Delete too long logfiles.
//   if (@file_exists ($log_filename) && MWX__filesize($log_filename)>1000000)
//      unlink ($log_filename);

   $filename = basename ($filename);

   if (@file_exists ($log_filename))
      {
      // 'r+' non destructive R/W mode.
      $fhandle = @fopen ($log_filename, 'r+');
      if ($fhandle)
         @fseek ($fhandle, -strlen($logfile_tail), SEEK_END);
      }
   else
      {
      $fhandle = @fopen ($log_filename, 'w');
      if ($fhandle)
         @fwrite ($fhandle, $logfile_header);
      }

   if ($fhandle)
      {
      @fwrite ($fhandle, "\r\n// " . $_SERVER['REMOTE_ADDR'] . '(' . $_SERVER['REMOTE_PORT'] . ')' . ' -> ' . date("Y-m-d, G:i:s") . "|" . MEMBERWING_X_VERSION . "/" . MEMBERWING_X_EDITION . "|$filename($linenum)|: " . $message . ($extra_text?"\r\n//    Extra Data: $extra_text":"") . $logfile_tail);
      @fclose ($fhandle);
      }
}
//===========================================================================

//===========================================================================
function MWX__file_exists_include_path ($filename)
{
   // Check for absolute path
   if (realpath($filename) == $filename)
      return true;

   // Otherwise, treat as relative path
   $paths = explode (PATH_SEPARATOR, get_include_path());
   foreach ($paths as $path)
      {
      if (file_exists (rtrim($path, '/') . '/' . ltrim($filename, '/')))
         return true;
      }

   return false;
}
//===========================================================================

//===========================================================================
function MWX__send_email ($email_to, $email_from, $subject, $plain_body)
{
//DANIEL//
   $mwx_settings = MWX__get_settings ();
   if ($mwx_settings['mwx_disable_all_emails'])
      {
      MWX__log_event (__FILE__, __LINE__, "NOTE: Skipping sending email - all outgoing emails are disabled via Integration settings");
      return;
      }

   $email_from_with_name = '"'.$mwx_settings['welcome_email_from_name'] .'" <'.$email_from.'>';

   $use_smtp      = $mwx_settings['smtp_enabled']==1;
   $smtp_host     = $mwx_settings['smtp_host'];
   $smtp_username = $mwx_settings['smtp_username'];
   $smtp_password = $mwx_settings['smtp_password'];
   $smtp_port     = $mwx_settings['smtp_port'];
   $smtp_auth     = $mwx_settings['smtp_use_authentication'];

   $message = "
   <html>
   <head>
   <title>$subject</title>
   </head>
   <body>" . $plain_body . "
   </body>
   </html>
   ";

   // Strip tags feature.
   $message_text = preg_replace ('@<br[^>]*>@', "\n", $message);
   $message_text = trim(preg_replace ('@<[^>]+>@', " ",    $message_text));



   if ($use_smtp && (!MWX__file_exists_include_path("Mail.php") || !MWX__file_exists_include_path("Mail/mime.php")))
      {
      MWX__log_event (__FILE__, __LINE__, "Warning: SMTP is enabled but no Pear libraries are installed. Mail.php and Mail/mime.php must exists for SMTP support to work. Trying to use mail()...");
      $use_smtp = false;
      }

   if ($use_smtp)
      {
      require_once ("Mail.php");
      require_once ("Mail/mime.php");

      $headers["From"]    = $email_from_with_name;
      $headers["To"]      = $email_to;
      $headers["Subject"] = $subject;


      $params["host"] = $smtp_host;
      $params["port"] = $smtp_port;
      $params["auth"] = $smtp_auth==1;
      $params["username"] = $smtp_username;
      $params["password"] = $smtp_password;

      $mime = new Mail_mime("\n");
      $mime->setTXTBody($message_text);
      $mime->setHTMLBody($message);

      $body = $mime->get();
      $headers = $mime->headers($headers);

      // Create the mail object using the Mail::factory method
      $mail_object = Mail::factory("smtp", $params);
      $bRetCode = $mail_object->send($email_to, $headers, $body);
      if ($bRetCode == true)
         MWX__log_event (__FILE__, __LINE__, "Successfully sent SMTP email from: $email_from_with_name to: $email_to.");
      else
         MWX__log_event (__FILE__, __LINE__, "ERROR: SMTP mail send failed. Error sending email from: $email_from_with_name to: $email_to.");
      }
   else
      {
      // To send HTML mail, the Content-type header must be set
      $headers  = 'MIME-Version: 1.0' . "\r\n";
      $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

      // Additional headers
// Not needed - duplication.
//      $headers .= "To: " . $email_to . "\r\n";        //"To: Mary <mary@example.com>, Kelly <kelly@example.com>" . "\r\n";
      $headers .= "From: " . $email_from_with_name . "\r\n";    //"From: Birthday Reminder <birthday@example.com>" . "\r\n";
                                       // $headers .= "Cc: birthdayarchive@example.com" . "\r\n";
                                       // $headers .= "Bcc: birthdaycheck@example.com" . "\r\n";
      // Mail it
      $bRetCode = @mail ($email_to, $subject, $message, $headers);
      if ($bRetCode)
        MWX__log_event (__FILE__, __LINE__, "Successfully sent email from: $email_from_with_name to: $email_to. (mail() returned true)");
      else
        MWX__log_event (__FILE__, __LINE__, "ERROR: mail() failed. Error sending email from: $email_from_with_name to: $email_to.");
      }
//DANIEL

/*
This is old code - before Daniel Waltrous SMTP addition

$message = "
   <html>
   <head>
   <title>$subject</title>
   </head>
   <body>" . $plain_body . "
   </body>
   </html>
   ";

   // To send HTML mail, the Content-type header must be set
   $headers  = 'MIME-Version: 1.0' . "\r\n";
   $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

   // Additional headers
   $headers .= "To: " . $email_to . "\r\n";        //"To: Mary <mary@example.com>, Kelly <kelly@example.com>" . "\r\n";
   $headers .= "From: " . $email_from . "\r\n";    //"From: Birthday Reminder <birthday@example.com>" . "\r\n";
                                                // $headers .= "Cc: birthdayarchive@example.com" . "\r\n";
                                                // $headers .= "Bcc: birthdaycheck@example.com" . "\r\n";
   // Mail it
   $bRetCode = mail ($email_to, $subject, $message, $headers);
   if ($bRetCode)
      MWX__log_event (__FILE__, __LINE__, "Successfully sent email from: $email_from to: $email_to. (mail() returned true)");
   else
      MWX__log_event (__FILE__, __LINE__, "ERROR: mail() failed. Error sending email from: $email_from to: $email_to.");
*/
}
//===========================================================================

//===========================================================================
// Save _GET, _POST, _SERVER, _COOKIE in new .html file.

function MWX__log_vars()
{
   $var1 = MWX__get_var ($_SERVER,    '$_SERVER');
   $var2 = MWX__get_var ($_GET,       '$_GET');
   $var3 = MWX__get_var ($_POST,      '$_POST');
   $var4 = MWX__get_var ($_COOKIE,    '$_COOKIE');

   $output =<<<OUTOUT
   <html>
       <body>
           $var3
           $var2
           $var1
           $var4
       </body>
   </html>
OUTOUT;

   // Save output into unique file
   $log_file_num   = 0;

   do
      {
      $log_file_num ++;
      $log_filename   = dirname(__FILE__) . "/__log_vars_$log_file_num.html";
      }
   while (@file_exists ($log_filename));

   $fhandle = fopen ($log_filename, 'w');
   if ($fhandle)
      {
      fwrite ($fhandle, $output);
      fclose ($fhandle);
      }

   return $output;
}

function MWX__get_var ($var, $varname)
{
   $style='"font:12px Verdana;color:blue;"';
   $output = MWX__output_varname ($varname);
   foreach ($var as $key => $value)
      {
      $output .= ("&nbsp;&nbsp;&nbsp;<span style=$style>$varname</span>" . '[\'' . MWX__output_key($key) . '\']=\'' . MWX__output_value($value) . "'");
      $output .= '<br />';
      }

   return $output;
}

function MWX__output_varname ($varname)
{
    $style='"font:14px Verdana bold;color:blue;"';
    return "<hr />" . "<p style=$style>$varname:</p>";
}
function MWX__output_key ($key)
{
    $style='"font:10px Verdana;color:green;"';
    return "<span style=$style>$key</span>";
}
function MWX__output_value ($value)
{
    $style='"font:10px Verdana;color:red;"';
    return "<span style=$style>$value</span>";
}
//===========================================================================

//===========================================================================
//
// Determine file MIME type
// Ex: 'text/plain'
//

$_mime_exts = array
   (
   'ai' => 'application/postscript',
   'aif' => 'audio/x-aiff',
   'aifc' => 'audio/x-aiff',
   'aiff' => 'audio/x-aiff',
   'asc' => 'text/plain',
   'au' => 'audio/basic',
   'avi' => 'video/x-msvideo',
   'bcpio' => 'application/x-bcpio',
   'bin' => 'application/octet-stream',
   'bmp' => 'image/bmp',
   'cdf' => 'application/x-netcdf',
   'class' => 'application/octet-stream',
   'cpio' => 'application/x-cpio',
   'cpt' => 'application/mac-compactpro',
   'csh' => 'application/x-csh',
   'css' => 'text/css',
   'dcr' => 'application/x-director',
   'dir' => 'application/x-director',
   'djv' => 'image/vnd.djvu',
   'djvu' => 'image/vnd.djvu',
   'dll' => 'application/octet-stream',
   'dms' => 'application/octet-stream',
   'doc' => 'application/msword',
   'dvi' => 'application/x-dvi',
   'dxr' => 'application/x-director',
   'eps' => 'application/postscript',
   'etx' => 'text/x-setext',
   'exe' => 'application/octet-stream',
   'ez' => 'application/andrew-inset',
   'gif' => 'image/gif',
   'gtar' => 'application/x-gtar',
   'hdf' => 'application/x-hdf',
   'hqx' => 'application/mac-binhex40',
   'htm' => 'text/html',
   'html' => 'text/html',
   'ice' => 'x-conference/x-cooltalk',
   'ief' => 'image/ief',
   'iges' => 'model/iges',
   'igs' => 'model/iges',
   'jpe' => 'image/jpeg',
   'jpeg' => 'image/jpeg',
   'jpg' => 'image/jpeg',
   'js' => 'application/x-javascript',
   'kar' => 'audio/midi',
   'latex' => 'application/x-latex',
   'lha' => 'application/octet-stream',
   'lzh' => 'application/octet-stream',
   'm3u' => 'audio/x-mpegurl',
   'man' => 'application/x-troff-man',
   'me' => 'application/x-troff-me',
   'mesh' => 'model/mesh',
   'mid' => 'audio/midi',
   'midi' => 'audio/midi',
   'mif' => 'application/vnd.mif',
   'mov' => 'video/quicktime',
   'movie' => 'video/x-sgi-movie',
   'mp2' => 'audio/mpeg',
   'mp3' => 'audio/mpeg',
   'mpe' => 'video/mpeg',
   'mpeg' => 'video/mpeg',
   'mpg' => 'video/mpeg',
   'mpga' => 'audio/mpeg',
   'ms' => 'application/x-troff-ms',
   'msh' => 'model/mesh',
   'mxu' => 'video/vnd.mpegurl',
   'nc' => 'application/x-netcdf',
   'oda' => 'application/oda',
   'pbm' => 'image/x-portable-bitmap',
   'pdb' => 'chemical/x-pdb',
   'pdf' => 'application/pdf',
   'pgm' => 'image/x-portable-graymap',
   'pgn' => 'application/x-chess-pgn',
   'png' => 'image/png',
   'pnm' => 'image/x-portable-anymap',
   'ppm' => 'image/x-portable-pixmap',
   'ppt' => 'application/vnd.ms-powerpoint',
   'ps' => 'application/postscript',
   'qt' => 'video/quicktime',
   'ra' => 'audio/x-realaudio',
   'ram' => 'audio/x-pn-realaudio',
   'rar' => 'application/x-rar-compressed',
   'ras' => 'image/x-cmu-raster',
   'rgb' => 'image/x-rgb',
   'rm' => 'audio/x-pn-realaudio',
   'roff' => 'application/x-troff',
   'rpm' => 'audio/x-pn-realaudio-plugin',
   'rtf' => 'text/rtf',
   'rtx' => 'text/richtext',
   'sgm' => 'text/sgml',
   'sgml' => 'text/sgml',
   'sh' => 'application/x-sh',
   'shar' => 'application/x-shar',
   'silo' => 'model/mesh',
   'sit' => 'application/x-stuffit',
   'skd' => 'application/x-koan',
   'skm' => 'application/x-koan',
   'skp' => 'application/x-koan',
   'skt' => 'application/x-koan',
   'smi' => 'application/smil',
   'smil' => 'application/smil',
   'snd' => 'audio/basic',
   'so' => 'application/octet-stream',
   'spl' => 'application/x-futuresplash',
   'src' => 'application/x-wais-source',
   'sv4cpio' => 'application/x-sv4cpio',
   'sv4crc' => 'application/x-sv4crc',
   'swf' => 'application/x-shockwave-flash',
   't' => 'application/x-troff',
   'tar' => 'application/x-tar',
   'tcl' => 'application/x-tcl',
   'tex' => 'application/x-tex',
   'texi' => 'application/x-texinfo',
   'texinfo' => 'application/x-texinfo',
   'tif' => 'image/tiff',
   'tiff' => 'image/tiff',
   'tr' => 'application/x-troff',
   'tsv' => 'text/tab-separated-values',
   'txt' => 'text/plain',
   'ustar' => 'application/x-ustar',
   'vcd' => 'application/x-cdlink',
   'vrml' => 'model/vrml',
   'wav' => 'audio/x-wav',
   'wbmp' => 'image/vnd.wap.wbmp',
   'wbxml' => 'application/vnd.wap.wbxml',
   'wml' => 'text/vnd.wap.wml',
   'wmlc' => 'application/vnd.wap.wmlc',
   'wmls' => 'text/vnd.wap.wmlscript',
   'wmlsc' => 'application/vnd.wap.wmlscriptc',
   'wrl' => 'model/vrml',
   'xbm' => 'image/x-xbitmap',
   'xht' => 'application/xhtml+xml',
   'xhtml' => 'application/xhtml+xml',
   'xls' => 'application/vnd.ms-excel',
   'xml' => 'text/xml',
   'xpm' => 'image/x-xpixmap',
   'xsl' => 'text/xml',
   'xwd' => 'image/x-xwindowdump',
   'xyz' => 'chemical/x-xyz',
   'zip' => 'application/zip'
   );

function MWX__get_mime_type ($filename)
{
   global $_mime_exts;
   $mime = "";

   if (function_exists ('finfo_open'))
      {
      $finfo = finfo_open (FILEINFO_MIME);
      $mime = finfo_file ($finfo, $filename);
      finfo_close ($finfo);
      }

// NOTE: 'mime_content_type' gets confused about uppercased extensions, like '.MPG' and returns 'text/plain'.
//
//   else if (function_exists ('mime_content_type'))
//      {
//      $mime = mime_content_type ($filename);
//      }

   if (!$mime)
      {
      $fileinfo = pathinfo ($filename);
      $extension = strtolower($fileinfo['extension']);
      if (isset ($_mime_exts[$extension]))
         $mime = $_mime_exts[$extension];
      else
         $mime = 'application/octet-stream';
      }

   return $mime;
}
//===========================================================================

//===========================================================================
//
// Function returns array of files under PREMIUM_FILES directory. Format:
//
// $assoc_array = FALSE:
//    /home/expeus/public_html/wp/PREMIUM_FILES/membership/gold/membership_gold.txt
//    /home/expeus/public_html/wp/PREMIUM_FILES/membership/membership.txt
//    /home/expeus/public_html/wp/PREMIUM_FILES/free/premium_free.txt
//    /home/expeus/public_html/wp/PREMIUM_FILES/_FILES/how-to-sell-online.txt
//    /home/expeus/public_html/wp/PREMIUM_FILES/sites.txt
//
// $assoc_array = TRUE:
//    array ('dir'=>array('file1', 'file2'), 'dir/dirx'=>array('file4', 'file5', 'file6'));
//
//
// Syntax:
//    $files_array = array()
//    MWX__enumerate_files_in_dir ('/home/expeus/public_html/wp/PREMIUM_FILES', $files_array);
//    foreach ($files_array as $file) { echo '<br />' . $file; }
//
// '$skip_dirnames_array' = array ('css', 'img', 'js', 'UPLOADS', 'temporary', ); ...

function MWX__enumerate_files_in_dir ($dirname, &$files_array, $assoc_array=FALSE, $skip_dirnames_array=FALSE)
{
   // Enumerate files/dirs in it.
   // for each valid subdir call itself
   if ($assoc_array)
      $files_array[$dirname]=array();

   $dh = @opendir (rtrim($dirname, '/') . '/');
   if ($dh)
      {
      while (($objname = readdir($dh)) !== false)
         {
         $full_objname = rtrim($dirname, '/') . "/$objname";
         if (is_dir($full_objname))
            {
            // This is dir
            if ($objname == '.' || $objname == '..')
               continue;

            $path_parts = explode ('/', $full_objname);
            $this_dir = array_pop ($path_parts);

            if (isset($skip_dirnames_array) && is_array($skip_dirnames_array) && in_array ($this_dir, $skip_dirnames_array))
               continue;   // Skip this directory

            MWX__enumerate_files_in_dir ($full_objname, $files_array, $assoc_array, $skip_dirnames_array);
            }
         else
            {
            // This is file
            if ($objname[0] != '.' && !preg_match ('@_denied(\.|$)@', $objname))
               {
               if ($assoc_array)
                  $files_array[$dirname][] = $objname;
               else
                  $files_array[] = $full_objname; // Skip .htaccess and *_denied.* -type files
               }
            }
         }
      closedir($dh);
      }

   return ($files_array);
}
//===========================================================================

//===========================================================================
function MWX__visit_is_search_engine_spider ()
{
   return preg_match ('#(slurp|bot|sp[iy]der|scrub(by|the)|crawl(er|ing|@)|yandex)#i', (string)@$_SERVER['HTTP_USER_AGENT']);
}
//===========================================================================

//===========================================================================
function MWX__visit_from_search_engine ()
{
   return preg_match ('#^https?://[a-z]+\.(google|aol|live|msn|baidu|yandex|search|ask)\.#i', (string)@$_SERVER['HTTP_REFERER']);
}
//===========================================================================

//===========================================================================
function MWX__get_visitor_REMOTE_ADDR ()
{
		$ip_address = $_SERVER['REMOTE_ADDR'];
		if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER))
		{
    	$forwarded_ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
    	$ip_address = trim(array_pop($forwarded_ips));
		}
		return $ip_address;
}
//===========================================================================

//===========================================================================
function MWX__ip_addresses_matching ($ip_addr1, $ip_addr2, $cidr_mask)
{
	$mask = 0xFFFFFFFF << (32 - $cidr_mask);

	$ip_addr1_masked = ip2long($ip_addr1) & $mask;
	$ip_addr2_masked = ip2long($ip_addr2) & $mask;

	return ($ip_addr1_masked == $ip_addr2_masked);
}
//===========================================================================

//===========================================================================
// Solves 2GB filesize limit on non-windows OS-es.
function MWX__filesize ($full_filename)
{
   // Note: this type of filesize detection is not supported by hosting:
   //       return (trim(`stat -c%s $full_filename`));

   return @filesize ($full_filename);
}
//===========================================================================

//===========================================================================
/*
  Get web page contents with the help of PHP cURL library

  If '$easy' is set to FALSE, function returns assoc array[]:
     "url"          - the last effective URL after redirects
     "http_code"    - the last error/status code
     "content_type" - the content type from the header
     "content"      - the page content (text, image, etc.)
     "errno"        - the CURL error code
     "errmsg"       - the CURL error message

    In this case:
    - "success" is when array['http_code']==200, and return data will be in array['content']
    - "fail"    is when array['http_code']!=200. Error codeas are: array['errno'] and array['errmsg']


  If '$easy' is set to TRUE, function returns page contents or FALSE is some error occured.
*/
function MWX__file_get_contents ($url, $user_agent=FALSE)
{
   if (!function_exists('curl_init'))
      {
      return file_get_contents ($url);
      }

   $options = array(
      CURLOPT_URL            => $url,
      CURLOPT_RETURNTRANSFER => true,     // return web page
      CURLOPT_HEADER         => false,    // don't return headers
//      CURLOPT_FOLLOWLOCATION => true,     // follow redirects
      CURLOPT_ENCODING       => "",       // handle compressed
      CURLOPT_USERAGENT      => $user_agent?$user_agent:'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.2; .NET CLR 1.1.4322)', // who am i
      CURLOPT_AUTOREFERER    => true,     // set referer on redirect
      CURLOPT_CONNECTTIMEOUT => 60,       // timeout on connect
      CURLOPT_TIMEOUT        => 60,       // timeout on response in seconds.
      CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
      );

   $ch      = curl_init   ();

   if (function_exists('curl_setopt_array'))
      {
      curl_setopt_array      ($ch, $options);
      }
   else
      {
      // To accomodate older PHP 5.0.x systems
      curl_setopt ($ch, CURLOPT_URL            , $url);
      curl_setopt ($ch, CURLOPT_RETURNTRANSFER , true);     // return web page
      curl_setopt ($ch, CURLOPT_HEADER         , false);    // don't return headers
      curl_setopt ($ch, CURLOPT_ENCODING       , "");       // handle compressed
      curl_setopt ($ch, CURLOPT_USERAGENT      , $user_agent?$user_agent:'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.2; .NET CLR 1.1.4322)'); // who am i
      curl_setopt ($ch, CURLOPT_AUTOREFERER    , true);     // set referer on redirect
      curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT , 60);       // timeout on connect
      curl_setopt ($ch, CURLOPT_TIMEOUT        , 60);       // timeout on response in seconds.
      curl_setopt ($ch, CURLOPT_MAXREDIRS      , 10);       // stop after 10 redirects
      }

   $content = curl_exec   ($ch);
   $err     = curl_errno  ($ch);
   $header  = curl_getinfo($ch);
   // $errmsg  = curl_error  ($ch);

   curl_close             ($ch);

   if (!$err && $header['http_code']==200)
      return $content;
   else
      return FALSE;
}
//===========================================================================

//===========================================================================
//
// Function returns associative array of complete information on all users, including custom metadata, product sales, and affiliate metadata.

function MWX__Get_Users_Data ()
{
  global $wpdb;

  $rows = $wpdb->get_results ("SELECT $wpdb->users.ID FROM $wpdb->users ORDER BY ID ASC", ARRAY_A);
  if ($rows === FALSE)
    {
    return ("DB Error");
    }
  else
    {
    $all_users = array();
    foreach ($rows as $row)
      {
      $user_data = get_userdata ($row['ID']);

      $all_users[] = array (
         'ID'                    => $user_data->ID,
         'user_login'            => $user_data->user_login,
         'user_email'            => $user_data->user_email,
         'user_registered'       => $user_data->user_registered,
         'user_activation_key'   => $user_data->user_activation_key,
         'first_name'            => $user_data->first_name,
         'last_name'             => $user_data->last_name,
         'mwx_extra_user_data'   => MWX__get_usermeta_array ($row['ID'], 'mwx_extra_user_data'),
         'mwx_purchases'         => MWX__get_usermeta_array ($row['ID'], 'mwx_purchases'),
         'mwx_aff_info'          => MWX__get_usermeta_array ($row['ID'], 'mwx_aff_info'),
         );
      }

    return ($all_users);
    }
}
//===========================================================================

//===========================================================================
//
// for some reason Wordpress': email_exists('new_user@email.com') returns false for just created users. These functions will solve this.
// It will save info about just created user inside of $_inputs array "cache".

function MWX__email_exists ($email)
{
   global $_inputs;

   if (isset($_inputs['__user_info'][$email]) && is_array($_inputs['__user_info'][$email]) && isset($_inputs['__user_info'][$email]['user_id']) && $_inputs['__user_info'][$email]['user_id'])
      {
      MWX__log_event (__FILE__, __LINE__, "Cache hit for: MWX__email_exists($email). Returning cached user_id = " . $_inputs['__user_info'][$email]['user_id']);
      return ($_inputs['__user_info'][$email]['user_id']);
      }

   MWX__log_event (__FILE__, __LINE__, "Cache miss for: MWX__email_exists($email)");

   // Save it in cache after first detection
   $user_id = email_exists ($email);
   if ($user_id)
      $_inputs['__user_info'][$email]['user_id'] = $user_id;

   MWX__log_event (__FILE__, __LINE__, "Returning final user_id = $user_id");
   return ($user_id);
}


function MWX__wp_create_user ($actual_username, $actual_password, $email)
{
   global $_inputs;

   if (isset($_inputs['__user_info'][$email]['user_id']) && $_inputs['__user_info'][$email]['user_id'])
      {
      // Return data from cache
      MWX__log_event (__FILE__, __LINE__, "Cache hit for: MWX__wp_create_user($actual_username, $actual_password, $email)");
      return ($_inputs['__user_info'][$email]['user_id']);
      }

   MWX__log_event (__FILE__, __LINE__, "Cache miss for: MWX__wp_create_user($actual_username, $actual_password, $email). Creating new user via wp_create_user()...");

   $user_id = wp_create_user ($actual_username, $actual_password, $email);
   if (!is_int($user_id))
      {
      // When username or email already registered this function returns Object with warning message inside (instead of FALSE or existing user_id/name - silly!).
      // Prudent thing to do is try to determine existing username.
      $user_id = email_exists ($email);
      MWX__log_event (__FILE__, __LINE__, "MWX__wp_create_user(): wp_create_user() returned this:\n" . serialize($user_id) . "\n             Subsequent call to email_exists($email) returned this user_id: '$user_id'");
      }

   if (is_array($_inputs))
      {
      $_inputs['__user_info'][$email]['user_id'] = $user_id;
      }

   return $user_id;
}
//===========================================================================


?>