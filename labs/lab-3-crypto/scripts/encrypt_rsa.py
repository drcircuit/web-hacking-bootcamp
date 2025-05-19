from Crypto.Util.number import getPrime, inverse, bytes_to_long
from pathlib import Path

e = 65537
key_size = 1024

# Generate Fermat-vulnerable primes
while True:
    p = getPrime(key_size // 2)
    for delta in range(2, 50, 2):
        q = p + delta
        if q.bit_length() != p.bit_length():
            continue
        phi = (p - 1) * (q - 1)
        try:
            d = inverse(e, phi)
            break
        except ValueError:
            continue
    else:
        continue
    break

n = p * q

# Load plaintext from temp file
plain_path = Path("/tmp/report_plain.txt")
msg = plain_path.read_bytes()
c = pow(bytes_to_long(msg), e, n)

# Write public key to web root
Path("/var/www/html/rsa_pub.txt").write_text(f"n = {n}\ne = {e}\n")

# Write encrypted message to thanos's home folder
Path("/home/thanos/report.txt.enc").write_text(str(c))

# Delete the plaintext
plain_path.unlink()