const express = require('express');
const bodyParser = require('body-parser');
const session = require('express-session');
const crypto = require('crypto');
const path = require('path');
const fs = require('fs');
const sqlite3 = require('sqlite3').verbose();
const cookieParser = require('cookie-parser');

const app = express();
const port = 3000;
const SECRET_KEY = 'supersecretkey';
const pieces = JSON.parse(fs.readFileSync(path.join(__dirname, 'pieces.json'), 'utf-8'));

function createSignature(payload, secret) {
  return crypto.createHmac('sha256', secret).update(payload).digest('hex');
}

function customJwtSign(data, secret) {
  const json = JSON.stringify(data);
  const payload = Buffer.from(json).toString('base64');
  const timestamp = Date.now();
  const superSecret = (SECRET_KEY ^ timestamp) + Number(data.age);
  const signature = createSignature(`${payload}:${superSecret}`, superSecret.toString());
  // log token, plus signature
  console.log(`${payload}:${superSecret}.${signature}`);
  return `${payload}.${signature}`;
}

function customJwtVerify(token, secret) {
  const parts = token.split('.');
  if (parts.length !== 2) return null;

  const payload = Buffer.from(parts[0], 'base64').toString('utf-8');
  const data = JSON.parse(payload);
  const payload2 = Buffer.from(JSON.stringify(data)).toString('base64');
  const signature = parts[1];
  const superSecret = (SECRET_KEY ^ data.timestamp) + Number(data.age);

  const expectedSignature = createSignature(`${payload2}:${superSecret}`, superSecret.toString());
  console.log(`${payload2}:${superSecret}.${expectedSignature}`);

  if (signature !== expectedSignature) {
    return null;
  }
  return data;
}

// Initialize database
const db = new sqlite3.Database(':memory:');
db.serialize(() => {
  db.run("CREATE TABLE users (id INTEGER PRIMARY KEY, username TEXT, password TEXT, role TEXT, nickname TEXT, age TEXT, interests TEXT)");
  db.run("INSERT INTO users (username, password, role) VALUES ('admin', 'admin', 'admin')");
});

app.use(bodyParser.urlencoded({ extended: true }));
app.use(cookieParser());
app.use(session({
  secret: 'secret-key',
  resave: false,
  saveUninitialized: true
}));

// Serve static files
app.use('/img', express.static(path.join(__dirname, 'img')));
app.use('/css', express.static(path.join(__dirname, 'public/css')));

// Middleware to check JWT
function authenticateToken(req, res, next) {
  const token = req.cookies.token || '';
  if (token === '') {
    return res.sendStatus(401);
  }
  const secret = Buffer.from(token.split('.')[1], 'base64').toString('utf-8');
  const user = customJwtVerify(token, secret);
  if (!user) {
    return res.sendStatus(403);
  }
  req.user = user;
  next();
}

// Vulnerable JWT token handling
app.post('/login', (req, res) => {
  const { username, password } = req.body;
  db.get("SELECT * FROM users WHERE username = ? AND password = ?", [username, password], (err, row) => {
    if (row) {
      const user = { username: row.username, role: row.role, timestamp: Date.now(), age: row.age, subjectid: row.id };
      const token = customJwtSign(user, SECRET_KEY);
      res.cookie('token', token);
      return res.redirect('/');
    } else {
      return res.send('Invalid credentials');
    }
  });
});

app.post('/register', (req, res) => {
  const { username, password, nickname, age, interests } = req.body;
  db.run("INSERT INTO users (username, password, role, nickname, age, interests) VALUES (?, ?, 'user', ?, ?, ?)", [username, password, nickname, age, interests], function (err) {
    if (err) {
      return res.send('Error registering user');
    }
    const user = { username: username, role: 'user', timestamp: Date.now(), age: age, subjectid: this.lastID };
    const token = customJwtSign(user, SECRET_KEY);
    res.cookie('token', token);
    return res.redirect('/');
  });
});

app.post('/update-profile', authenticateToken, (req, res) => {
  const { username, password, nickname, age, interests, role } = req.body;
  const user = { username: req.user.username, role: req.user.role, timestamp: Date.now(), age: age, subjectid: req.user.subjectid };
  const token = customJwtSign(user, SECRET_KEY);
  res.cookie('token', token);
  db.run("UPDATE users SET username = ?, password = ?, nickname = ?, age = ?, interests = ? WHERE id = ?", [username, password, nickname, age, interests, req.user.subjectid], (err) => {
    if (err) {
      return res.send('Error updating profile');
    }
    res.redirect('/profile');
  });
});

app.get('/admin', authenticateToken, (req, res) => {
  if (req.user.role !== 'admin') {
    return res.sendStatus(403);
  }
  res.sendFile(path.join(__dirname, 'public', 'admin.html'));
});

app.get('/', (req, res) => {
  res.sendFile(path.join(__dirname, 'public', 'index.html'));
});

app.get('/pieces', (req, res) => {
  res.json(pieces);
});

app.get('/register', (req, res) => {
  res.sendFile(path.join(__dirname, 'public', 'register.html'));
});

app.get('/login', (req, res) => {
  res.sendFile(path.join(__dirname, 'public', 'login.html'));
});

app.get('/profile', authenticateToken, (req, res) => {
  db.get("SELECT * FROM users WHERE id = ?", [req.user.subjectid], (err, row) => {
    if (err || !row) {
      return res.send('Error fetching profile');
    }
    res.send(`
      <html>
      <head>
        <title>Profile</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
      </head>
      <body>
        <div class="container">
          <h1>Profile</h1>
          <form method="POST" action="/update-profile">
            <div class="mb-3">
              <label for="username" class="form-label">Username</label>
              <input type="text" class="form-control" id="username" name="username" value="${row.username}" required>
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <input type="password" class="form-control" id="password" name="password" value="${row.password}" required>
            </div>
            <div class="mb-3">
              <label for="nickname" class="form-label">Nickname</label>
              <input type="text" class="form-control" id="nickname" name="nickname" value="${row.nickname}" required>
            </div>
            <div class="mb-3">
              <label for="age" class="form-label">Age</label>
              <input type="text" class="form-control" id="age" name="age" value="${row.age}" required>
            </div>
            <div class="mb-3">
              <label for="interests" class="form-label">Interests</label>
              <input type="text" class="form-control" id="interests" name="interests" value="${row.interests}" required>
            </div>
            <div class="mb-3">
              <label for="role" class="form-label">Role</label>
              <input type="text" class="form-control" id="role" name="role" value="${row.role}" readonly>
            </div>
            <button type="submit" class="btn btn-primary">Update Profile</button>
          </form>
        </div>
      </body>
      </html>
    `);
  });
});

app.listen(port, () => {
  console.log(`Server running at http://localhost:${port}`);
});
