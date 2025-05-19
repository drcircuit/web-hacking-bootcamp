# a script that breaks RSA keys using Fermat's factorization method - useful with close primes P and Q
# usage --e <n> --keysize <keysize> --n <public key> --c <ciphertext> --o <output file>

import argparse
import math

from Crypto.Util.number import long_to_bytes, inverse

def fermat_factorization(n):
    """Fermat's factorization method to find two prime factors of n."""
    a = math.isqrt(n) + 1
    b2 = a * a - n
    while True:
        b = math.isqrt(b2)
        if b * b == b2:
            break
        a += 1
        b2 = a * a - n
    p = a - b
    q = a + b
    return p, q

def decrypt_rsa(n, e, c):
    """Decrypt RSA ciphertext using Fermat's factorization."""
    p, q = fermat_factorization(n)
    phi = (p - 1) * (q - 1)
    d = inverse(e, phi)
    m = pow(c, d, n)
    return long_to_bytes(m)

def main():
    parser = argparse.ArgumentParser(description="Break RSA using Fermat's factorization.")
    parser.add_argument('--n', type=int, required=True, help='Public key (n)')
    parser.add_argument('--e', type=int, default=65537, help='Public exponent (default: 65537)')
    parser.add_argument('--c', type=int, required=True, help='Ciphertext (c)')
    parser.add_argument('--o', type=str, required=True, help='Output file for decrypted message')
    
    args = parser.parse_args()

    # Decrypt the ciphertext
    decrypted_message = decrypt_rsa(args.n, args.e, args.c)

    # Write the decrypted message to the output file
    with open(args.o, 'wb') as f:
        f.write(decrypted_message)
    
    print(f"Decrypted message written to {args.o}")
if __name__ == "__main__":
    main()