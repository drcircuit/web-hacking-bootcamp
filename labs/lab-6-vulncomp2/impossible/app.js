const express = require('express');
const path = require('path');
const fs = require('fs');
const md5 = require('md5');

const app = express();
const port = 3000;

app.use(express.static(path.join(__dirname, 'public')));

app.get('/', (req, res) => {
  res.sendFile(path.join(__dirname, 'public', 'index.html'));
});

app.get('/timehash', (req, res) => {
  const currentTime = new Date().toTimeString().split(' ')[0]; // Get HH:MM:SS
  const hash = md5(currentTime);
  console.log(`Current time: ${currentTime}, Hash: ${hash}`);
  res.json({ msg: `${currentTime} ${hash}` });
});

app.get('/secret/:hash', (req, res) => {
  const hash = req.params.hash;
  const currentTime = new Date().toTimeString().split(' ')[0];
  const timeHash = md5(currentTime);

  if (hash === timeHash) {
    const flag = fs.readFileSync(path.join(__dirname, 'flag.txt'), 'utf8');
    res.send(`<h1>Flag: ${flag}</h1>`);
  } else {
    res.status(404).send('<h1>404 Not Found</h1>');
  }
});

app.listen(port, () => {
  console.log(`Web challenge app listening at http://localhost:${port}`);
});
