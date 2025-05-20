import sys
import base64

if len(sys.argv) < 3:
    print("Usage: python recover_key.py <base64_blob> <known_plaintext>")
    sys.exit(1)

blob_b64 = sys.argv[1]
known_plaintext = sys.argv[2].encode()

cipher = base64.b64decode(blob_b64)
length = len(known_plaintext)

print("Searching for XOR key fragments matching known plaintext...")

for offset in range(0, len(cipher) - length + 1):
    segment = cipher[offset:offset + length]
    candidate = bytes([c ^ p for c, p in zip(segment, known_plaintext)])
    try:
        ascii_key = candidate.decode("ascii")
        if all(32 <= ord(c) <= 126 for c in ascii_key):  # printable
            print(f"[offset {offset}] key fragment: {ascii_key}")
    except:
        continue