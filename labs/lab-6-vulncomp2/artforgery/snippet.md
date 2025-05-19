
```javascript
function customJwtSign(data, secret) {
  const json = JSON.stringify(data);
  const payload = Buffer.from(json).toString('base64');
  const timestamp = Date.now();
  const sharedSecret = (SUPER_SERVER_SECRET_KEY ^ timestamp) + Number(data.age);
  const signature = createSignature(`${payload}:${sharedSecret}`, sharedSecret.toString());
  return `${payload}.${signature}`;
}
```