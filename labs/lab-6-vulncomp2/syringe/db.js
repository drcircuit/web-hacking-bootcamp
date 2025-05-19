const sqlite3 = require('sqlite3').verbose();
const bcrypt = require('bcrypt');
const fs = require('fs');
const path = require('path');
const saltRounds = 10;

function initializeDatabase() {
  const db = new sqlite3.Database(':memory:');

  db.serialize(() => {
    db.run("CREATE TABLE users (id INTEGER PRIMARY KEY, username TEXT, password TEXT, hash TEXT, isAdmin INTEGER)");
    db.run("CREATE TABLE products (id INTEGER PRIMARY KEY, name TEXT, description TEXT, price REAL, image TEXT)");

    const insertUser = db.prepare("INSERT INTO users (username, hash, password, isAdmin) VALUES (?, ?, ?, ?)");
    const adminPassword = 'supersecretpassword#!';
    const adminHash = bcrypt.hashSync(adminPassword, saltRounds);
    insertUser.run("thanos", adminHash, adminPassword, 1);
    insertUser.finalize();

    const products = JSON.parse(fs.readFileSync(path.join(__dirname, 'products.json'), 'utf-8'));

    const insertProduct = db.prepare("INSERT INTO products (name, description, price, image) VALUES (?, ?, ?, ?)");
    products.forEach(product => {
      insertProduct.run(product.name, product.description, (Math.random() * 100).toFixed(2), product.image);
    });
    insertProduct.finalize();
  });

  return db;
}

module.exports = initializeDatabase;
