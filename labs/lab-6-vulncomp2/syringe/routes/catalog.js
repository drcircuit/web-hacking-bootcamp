const express = require('express');
const path = require('path');
const router = express.Router();

module.exports = (db) => {
  router.get('/', (req, res) => {
    res.sendFile(path.join(__dirname, '..', 'public', 'index.html'));
  });

  router.get('/products', (req, res) => {
    let query = "SELECT * FROM products";
    if (req.query.search) {
      query += ` WHERE name LIKE '%${req.query.search}%'`;
    }
    db.all(query, (err, rows) => {
      if (err) {
        console.error(`Error fetching products: ${err}`);
        return res.status(500).send("Error fetching products.");
      }
      res.json({ products: rows });
    });
  });

  return router;
};
