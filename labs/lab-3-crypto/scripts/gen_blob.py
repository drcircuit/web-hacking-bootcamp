#!/usr/bin/env python3
import base64
from itertools import cycle

plaintext = b"lovely :::: access :::: thanos"
key = b"loveit"

xored = bytes([p ^ k for p, k in zip(plaintext, cycle(key))])
b64_blob = base64.b64encode(xored).decode()

print(b64_blob)
