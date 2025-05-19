const express = require('express');
const path = require('path');
const bcrypt = require('bcrypt');
const router = express.Router();

module.exports = (db) => {
  router.get('/', (req, res) => {
    res.sendFile(path.join(__dirname, '..', 'public', 'admin.html'));
  });

  router.post('/login', (req, res) => {
    const { username, password } = req.body;
    db.get("SELECT * FROM users WHERE username = ?", [username], (err, row) => {
      if (err || !row) {
        return res.status(401).send("Invalid credentials.");
      }
      bcrypt.compare(password, row.hash, (err, result) => {
        if (result) {
          if (row.isAdmin) {
            req.session.isAdmin = true;
            res.redirect('/admin/flag');
          } else {
            res.status(403).send("Access denied.");
          }
        } else {
          res.status(401).send("Invalid credentials.");
        }
      });
    });
  });

  router.get('/flag', (req, res) => {
    if (!req.session.isAdmin) {
      return res.status(403).send("Access denied.");
    }
    res.sendFile(path.join(__dirname, '..', 'public', 'flag.html'));
  });

  return router;
};
