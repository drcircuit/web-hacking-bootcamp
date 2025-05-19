const express = require('express');
const path = require('path');

module.exports = (db) => {
  const router = express.Router();

  router.get('/register', (req, res) => {
    console.log('Serving register.html');
    res.sendFile(path.join(__dirname, '..', 'public', 'register.html'));
  });

  router.post('/register', (req, res) => {
    const { nickname, name, joke, password } = req.body;
    console.log(`Registering user: ${nickname}`);
    db.run("INSERT INTO users (nickname, name, joke, password, isExternal) VALUES (?, ?, ?, ?, 1)", [nickname, name, joke, password], function(err) {
      if (err) {
        console.error(`Error registering user: ${err}`);
        return res.status(500).send("Error registering user.");
      }
      console.log(`User registered with ID: ${this.lastID}`);
      req.session.userId = this.lastID;
      res.redirect('/jokes/wall');
    });
  });

  router.get('/login', (req, res) => {
    console.log('Serving login.html');
    res.sendFile(path.join(__dirname, '..', 'public', 'login.html'));
  });

  router.post('/login', (req, res) => {
    const { nickname, password } = req.body;
    console.log(`Attempting login for user: ${nickname}`);
    db.get("SELECT id FROM users WHERE nickname = ? AND password = ?", [nickname, password], (err, row) => {
      if (err) {
        console.error(`Error during login: ${err}`);
        return res.status(401).send("Invalid credentials.");
      }
      if (!row) {
        console.error(`Login failed for user: ${nickname}`);
        return res.status(401).send("Invalid credentials.");
      }
      console.log(`User logged in with ID: ${row.id}`);
      req.session.userId = row.id;
      res.redirect('/jokes/wall');
    });
  });

  return router;
};
