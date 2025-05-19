const express = require('express');
const bodyParser = require('body-parser');
const session = require('express-session');
const path = require('path');

const initializeDatabase = require('./db');
const db = initializeDatabase();

const app = express();
const port = 3000;

app.use(bodyParser.urlencoded({ extended: true }));
app.use(session({
  secret: 'secret-key',
  resave: false,
  saveUninitialized: true
}));

const catalogRoutes = require('./routes/catalog')(db);
const adminRoutes = require('./routes/admin')(db);

app.use('/catalog', catalogRoutes);
app.use('/admin', adminRoutes);
app.use('/img', express.static(path.join(__dirname, 'img')));
app.use('/css', express.static(path.join(__dirname, '/public/css')));

app.get('/', (req, res) => {
  res.redirect('/catalog');
});

app.listen(port, () => {
  console.log(`Server running at http://localhost:${port}`);
});
