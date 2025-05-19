#!/usr/bin/env python3

import sys
from jwt.utils import base64url_decode
from binascii import hexlify


def jwt2john(jwt):
    jwt_bytes = jwt.encode('ascii')
    parts = jwt_bytes.split(b'.')

    if len(parts) != 3:
        raise ValueError("Invalid JWT format: expected 3 parts")

    data = parts[0] + b'.' + parts[1]
    signature = hexlify(base64url_decode(parts[2]))

    return (data + b'#' + signature).decode('ascii')


if __name__ == "__main__":
    if len(sys.argv) != 2:
        print(f"Usage: {sys.argv[0]} <token.txt>")
        sys.exit(1)

    with open(sys.argv[1], 'r') as f:
        token = f.read().strip()

    try:
        print(jwt2john(token))
    except Exception as e:
        print(f"[!] Error: {e}")
        sys.exit(2)
