#!/usr/bin/env python3
"""
MemberWing-X License Generator (interactive, PRIVATE - never distribute!)

    python3 license_generator.py

Generates offline license keys for MemberWing-X 8.700+ in the format
MWX1.<base64url JSON payload>.<base64url Ed25519 signature>, verified by the
plugin against the public key embedded in mwx-admin.php (MWX_LICENSE_PUBLIC_KEY).

- Uses the same keys/private.key as the PHP tool (libsodium 64-byte secret key,
  base64) - keys from either tool are interchangeable.
- Pure Python 3 (RFC 8032 Ed25519 implementation), no packages to install.
- Every generated key is self-verified before it is shown, and appended to
  issued-licenses.log for your records.

Anyone holding keys/private.key can mint licenses. Guard this folder.
"""

import base64
import hashlib
import json
import os
import re
import sys
from datetime import datetime, timezone

KEYS_DIR = os.path.join(os.path.dirname(os.path.abspath(__file__)), "keys")
PRIVATE_KEY_FILE = os.path.join(KEYS_DIR, "private.key")
PUBLIC_KEY_FILE = os.path.join(KEYS_DIR, "public.key")
LOG_FILE = os.path.join(os.path.dirname(os.path.abspath(__file__)), "issued-licenses.log")

# ---------------------------------------------------------------------------
# Ed25519 (RFC 8032 reference implementation, pure Python).
# Not constant-time - fine here: it only ever runs on the license owner's machine.
# ---------------------------------------------------------------------------

P = 2 ** 255 - 19
L = 2 ** 252 + 27742317777372353535851937790883648493


def _sha512(data):
    return hashlib.sha512(data).digest()


def _inv(x):
    return pow(x, P - 2, P)


D = -121665 * _inv(121666) % P
I = pow(2, (P - 1) // 4, P)


def _xrecover(y):
    xx = (y * y - 1) * _inv(D * y * y + 1)
    x = pow(xx, (P + 3) // 8, P)
    if (x * x - xx) % P != 0:
        x = x * I % P
    if x % 2 != 0:
        x = P - x
    return x


_By = 4 * _inv(5) % P
_Bx = _xrecover(_By)
_B = (_Bx % P, _By % P)


def _edwards_add(p1, p2):
    x1, y1 = p1
    x2, y2 = p2
    x3 = (x1 * y2 + x2 * y1) * _inv(1 + D * x1 * x2 * y1 * y2)
    y3 = (y1 * y2 + x1 * x2) * _inv(1 - D * x1 * x2 * y1 * y2)
    return (x3 % P, y3 % P)


def _scalarmult(point, e):
    q = (0, 1)
    while e > 0:
        if e & 1:
            q = _edwards_add(q, point)
        point = _edwards_add(point, point)
        e >>= 1
    return q


def _encodepoint(point):
    x, y = point
    return (y | ((x & 1) << 255)).to_bytes(32, "little")


def _decodepoint(s):
    raw = int.from_bytes(s, "little")
    y = raw & ((1 << 255) - 1)
    x = _xrecover(y)
    if (x & 1) != ((raw >> 255) & 1):
        x = P - x
    point = (x, y)
    if (-x * x + y * y - 1 - D * x * x * y * y) % P != 0:
        raise ValueError("point not on curve")
    return point


def _clamp(h32):
    a = int.from_bytes(h32, "little")
    a &= (1 << 254) - 8
    a |= 1 << 254
    return a


def ed25519_publickey(seed):
    a = _clamp(_sha512(seed)[:32])
    return _encodepoint(_scalarmult(_B, a))


def ed25519_sign(message, seed, public):
    h = _sha512(seed)
    a = _clamp(h[:32])
    prefix = h[32:]
    r = int.from_bytes(_sha512(prefix + message), "little") % L
    r_point = _encodepoint(_scalarmult(_B, r))
    k = int.from_bytes(_sha512(r_point + public + message), "little") % L
    s = (r + k * a) % L
    return r_point + s.to_bytes(32, "little")


def ed25519_verify(signature, message, public):
    if len(signature) != 64:
        return False
    try:
        r_point = _decodepoint(signature[:32])
        a_point = _decodepoint(public)
    except ValueError:
        return False
    s = int.from_bytes(signature[32:], "little")
    if s >= L:
        return False
    k = int.from_bytes(_sha512(signature[:32] + public + message), "little") % L
    left = _scalarmult(_B, s)
    right = _edwards_add(r_point, _scalarmult(a_point, k))
    return left == right


# ---------------------------------------------------------------------------
# Key file handling (libsodium format: secret key = 32-byte seed + 32-byte public)
# ---------------------------------------------------------------------------

def b64url(data):
    return base64.urlsafe_b64encode(data).rstrip(b"=").decode("ascii")


def load_or_create_keys():
    if os.path.exists(PRIVATE_KEY_FILE):
        raw = base64.b64decode(open(PRIVATE_KEY_FILE).read().strip())
        if len(raw) != 64:
            sys.exit(f"ERROR: {PRIVATE_KEY_FILE} is not a 64-byte libsodium secret key.")
        seed, public = raw[:32], raw[32:]
        derived = ed25519_publickey(seed)
        if derived != public:
            sys.exit("ERROR: private.key is corrupt (public half doesn't match seed).")
        print(f"Private key loaded: {PRIVATE_KEY_FILE}")
        return seed, public

    print(f"No private key found at {PRIVATE_KEY_FILE}")
    answer = input("Create a NEW keypair? Existing plugin builds will only accept keys\n"
                   "signed by the key already embedded in them. [y/N]: ").strip().lower()
    if answer != "y":
        sys.exit("Aborted - no key created.")

    seed = os.urandom(32)
    public = ed25519_publickey(seed)
    os.makedirs(KEYS_DIR, exist_ok=True)
    with open(PRIVATE_KEY_FILE, "w") as f:
        f.write(base64.b64encode(seed + public).decode() + "\n")
    os.chmod(PRIVATE_KEY_FILE, 0o600)
    with open(PUBLIC_KEY_FILE, "w") as f:
        f.write(base64.b64encode(public).decode() + "\n")
    print("\nKeypair created.")
    print("Embed this public key in the plugin (MWX_LICENSE_PUBLIC_KEY in mwx-admin.php):\n")
    print("  " + base64.b64encode(public).decode() + "\n")
    return seed, public


# ---------------------------------------------------------------------------
# Interactive prompts
# ---------------------------------------------------------------------------

def ask_domain():
    print("Licensed domain - one of:")
    print("  example.com      that domain only (www. is ignored)")
    print("  *.example.com    domain + all its subdomains")
    print("  *                any domain (unlimited license)")
    while True:
        domain = input("Domain: ").strip().lower()
        if re.fullmatch(r"\*|(\*\.)?[a-z0-9][a-z0-9.-]*", domain or ""):
            return domain
        print("  That doesn't look like a valid domain - try again.")


def ask_expiry():
    while True:
        raw = input("Expiry date YYYY-MM-DD (Enter = never expires): ").strip()
        if raw == "":
            return "never"
        try:
            when = datetime.strptime(raw, "%Y-%m-%d")
        except ValueError:
            print("  Use YYYY-MM-DD format, e.g. 2027-12-31.")
            continue
        if when.date() < datetime.now(timezone.utc).date():
            if input("  That date is in the PAST (key will be born expired). Keep it? [y/N]: ").strip().lower() != "y":
                continue
        return raw


def ask_edition():
    choice = input("Edition: [1] GA - standard  [2] TSI  (Enter = GA): ").strip()
    return "TSI" if choice == "2" else "GA"


def ask_name():
    return input("Licensee name/email (optional, shown in admin): ").strip()


# ---------------------------------------------------------------------------

def make_key(seed, public, domain, expiry, edition, name):
    payload = json.dumps(
        {
            "v": 1,
            "d": domain,
            "x": expiry,
            "e": edition,
            "n": name,
            "i": b64url(os.urandom(6)),
            "t": datetime.now(timezone.utc).strftime("%Y-%m-%d"),
        },
        separators=(",", ":"),
    ).encode("utf-8")
    signature = ed25519_sign(payload, seed, public)
    if not ed25519_verify(signature, payload, public):
        sys.exit("ERROR: self-verification failed - key NOT issued.")
    return f"MWX1.{b64url(payload)}.{b64url(signature)}"


def main():
    print("=" * 64)
    print("            MemberWing-X License Generator")
    print("=" * 64)
    seed, public = load_or_create_keys()
    print()

    while True:
        domain = ask_domain()
        expiry = ask_expiry()
        edition = ask_edition()
        name = ask_name()

        key = make_key(seed, public, domain, expiry, edition, name)

        print()
        print(f"License key for '{domain}' (edition {edition}, expires {expiry}):")
        print("-" * 64)
        print(key)
        print("-" * 64)
        print("Self-check: signature VALID")
        print("Customer pastes this into WP Admin -> MemberWingX -> General Settings")
        print('-> license code field -> "Validate MemberWing-X License".')

        with open(LOG_FILE, "a") as f:
            stamp = datetime.now(timezone.utc).strftime("%Y-%m-%d %H:%M:%S UTC")
            f.write(f"{stamp} | {domain} | expires={expiry} | {edition} | {name}\n{key}\n\n")
        print(f"Recorded in {os.path.basename(LOG_FILE)}")

        print()
        if input("Generate another key? [y/N]: ").strip().lower() != "y":
            break


if __name__ == "__main__":
    try:
        main()
    except (KeyboardInterrupt, EOFError):
        print("\nBye.")
