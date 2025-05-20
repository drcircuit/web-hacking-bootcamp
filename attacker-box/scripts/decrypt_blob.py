# decrypt_blob.py
import sys
import base64
from itertools import cycle

if len(sys.argv) < 3:
    print("Usage: python decrypt_blob.py <base64_blob> <key>")
    sys.exit(1)

blob_b64 = sys.argv[1]
key = sys.argv[2].encode()

cipher = base64.b64decode(blob_b64)
decrypted = bytes([c ^ k for c, k in zip(cipher, cycle(key))])

print("Decrypted message:")
print(decrypted.decode(errors='replace'))
