# MemberWing-X License Generator (PRIVATE)

Generates offline license keys for MemberWing-X 8.700+. Keys are **Ed25519-signed**:
the plugin verifies them locally against the public key embedded in `mwx-admin.php`
(`MWX_LICENSE_PUBLIC_KEY`). No license server is involved — the old
`memberwing.com/LICENSE_VALIDATOR` service is retired and no longer contacted.

## NEVER distribute this folder

Anyone holding `keys/private.key` can mint unlimited licenses. Keep this folder out
of the plugin zip and out of any public repo. `keys/` is covered by `.gitignore`.

## Easiest way: interactive Python script (no dependencies)

```sh
cd license-generator
python3 license_generator.py
```

It walks you through domain / expiry / edition / licensee prompts, self-verifies
the signature, prints the key, and appends every issued key to
`issued-licenses.log`. Pure Python 3 — nothing to install. It uses the same
`keys/private.key` as the PHP tool, so keys from either tool are identical in
format and interchangeable.

## Alternative: PHP CLI tool

Requires PHP 7.2+ CLI (sodium is bundled). No PHP installed? Use Docker:

```sh
alias mwxphp='docker run --rm -v "$PWD":/gen -w /gen php:8.3-cli php'
```

## PHP tool usage

```sh
cd license-generator

# One-time keypair creation (ALREADY DONE - keys/ exists; refuses to overwrite)
php mwx-license-tool.php keygen

# Standard license for one domain (www. and port are ignored when matching)
php mwx-license-tool.php make --domain=customer-site.com --name="Jane Customer <jane@x.com>"

# Domain + all subdomains
php mwx-license-tool.php make --domain='*.customer-site.com' --name="Jane Customer"

# Unlimited license, any domain, TSI edition, no expiry
php mwx-license-tool.php make --domain='*' --edition=TSI --name="VIP Customer"

# Time-limited license
php mwx-license-tool.php make --domain=customer-site.com --expires=2027-12-31

# Inspect / verify any key
php mwx-license-tool.php verify --key='MWX1.xxx.yyy' --domain=customer-site.com
```

Options for `make`:

| Option      | Values                              | Default |
|-------------|-------------------------------------|---------|
| `--domain`  | `site.com`, `*.site.com`, `*`       | (required) |
| `--expires` | `YYYY-MM-DD` or `never`             | `never` |
| `--edition` | `GA` or `TSI`                       | `GA`    |
| `--name`    | free text, shown in admin           | empty   |

The customer pastes the key into
**WP Admin → MemberWingX → General Settings → license code field → "Validate MemberWing-X License"**.

## Key format

`MWX1.<base64url JSON payload>.<base64url Ed25519 signature>`

Payload fields: `v` format version, `d` domain, `x` expiry, `e` edition,
`n` licensee, `i` unique key id, `t` issue date.

Expired keys, keys for other domains, and any tampered payload are rejected by the
plugin with a specific message; sites without a valid key run in the original
"sponsored" mode (premium features disabled, "powered by" backlink shown).

## Rotating keys

Deleting `keys/private.key` and re-running `keygen` invalidates **every key ever
issued** (the new public key must then be re-embedded in `mwx-admin.php`, constant
`MWX_LICENSE_PUBLIC_KEY`). Only do this if the private key leaks.
