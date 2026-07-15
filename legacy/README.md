# Legacy artifacts

Original, unmodified MemberWing-X builds kept purely for reference. **Do not
install these** — they crash on modern WordPress / PHP 8. Use the maintained
source in `../memberwing-x/` (v8.700) and the packaged zip in `../dist/`.

| File | What it is |
|------|-----------|
| `memberwing-x-8.601-original.zip` | Pristine 8.601 build, dated 2016-05-07 (`memberwing-x-8.601_ORIG.zip`). |
| `memberwing-x-8.601-updated.zip`  | 8.601 build dated 2017-04-09. Was originally named `...UPDATED.zip.jpg` (a `.jpg` extension disguising a zip, likely to bypass an upload filter); renamed to a real `.zip` extension here. |
| `memberwing-rap-legacy-2010.zip`  | Old "MemberWing RAP" affiliate-program bundle (2008–2010, separate admin/install/ipn PHP). Was sitting inside the plugin folder as `memberwing_rap.zip` but is unrelated to the 8.700 code and was never part of the build. |

Both archives contain **identical plugin source** (verified) — they differ only
in how the zip itself was packaged. The 8.700 working copy in `../memberwing-x/`
started from this exact source, so a `diff` against either archive shows every
modernization change.
