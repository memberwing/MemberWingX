# MemberWing-X

WordPress membership plugin (premium content protection, digital store, affiliate
network) by Gleb Esman. This repo holds the plugin restored to run on modern
WordPress and PHP, plus its offline license tooling.

## Layout

| Path | What it is |
|------|-----------|
| `memberwing-x/` | **The plugin source.** Version 8.700 — modernized for WordPress 7.x / PHP 8.3. This folder is what gets zipped and installed. |
| `license-generator/` | **License key tooling** (PRIVATE — never distribute). Interactive Python script and a PHP CLI tool that mint the Ed25519-signed keys the plugin verifies. Holds the signing keypair in `keys/`. |
| `dist/` | Built, installable plugin zip (`memberwing-x-8.700.zip`). |
| `legacy/` | Original unmodified 8.601 builds, kept for reference only. Not installable on modern stacks. |

## Installing the plugin

Upload `dist/memberwing-x-8.700.zip` via **WP Admin → Plugins → Add New → Upload
Plugin**, then activate. Rebuild the zip after editing source with:

```sh
zip -rq dist/memberwing-x-8.700.zip memberwing-x -x "*.DS_Store"
```

## Licensing a site

1. Generate a key for the site's domain:
   ```sh
   cd license-generator
   python3 license_generator.py
   ```
2. In **WP Admin → MemberWingX → General Settings**, paste the whole key into the
   first row ("MemberWing-X License Code") and click **Validate MemberWing-X License**.

Keys are verified locally by the plugin against an embedded public key — no
license server is contacted. See `license-generator/README.md` for details.

## What changed in 8.700

Full list in `memberwing-x/changelog.txt`. Headline: fixed every PHP 8 / WP 7
fatal (widget constructor, string-offset TypeErrors, `WP_User_Search`, phpass,
PayPal IPN host), and replaced the dead phone-home license server with offline
Ed25519-signed keys.
