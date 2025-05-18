import base64
import hmac
import hashlib
import json

def base64url_encode(data: bytes) -> str:
    return base64.urlsafe_b64encode(data).rstrip(b'=').decode()

header = {"alg": "HS256", "typ": "JWT"}
payload = {"user_id": 5, "role" : "mail_server_admin"}
secret = b"evilcorp"

header_enc = base64url_encode(json.dumps(header).encode())
payload_enc = base64url_encode(json.dumps(payload).encode())
message = f"{header_enc}.{payload_enc}".encode()

sig = hmac.new(secret, message, hashlib.sha256).digest()
sig_enc = base64url_encode(sig)

token = f"{header_enc}.{payload_enc}.{sig_enc}"
print("JWT:", token)
