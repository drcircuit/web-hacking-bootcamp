const express = require('express');
const path = require('path');
const bodyParser = require('body-parser');
const session = require('express-session');
const SQLiteStore = require('connect-sqlite3')(session);
const puppeteer = require('puppeteer');

const initializeDatabase = require('./db');

const app = express();
const port = 3000;

const db = initializeDatabase();

app.use(bodyParser.urlencoded({ extended: true }));
app.use(session({
    store: new SQLiteStore,
    secret: 'secret-key',
    resave: false,
    saveUninitialized: false
}));

app.use(express.static(path.join(__dirname, 'public')));

const authRoutes = require('./routes/auth')(db);
const jokeRoutes = require('./routes/jokes')(db);
app.use('/auth', authRoutes);
app.use('/jokes', jokeRoutes);

app.get('/', (req, res) => {
    console.log('Serving index.html');
    res.sendFile(path.join(__dirname, 'public', 'index.html'));
});

// Admin and random users posting jokes at random intervals
const jokes = [
    "Why do programmers prefer dark mode? Because light attracts bugs!",
    "There are 10 types of people in the world: those who understand binary and those who don’t.",
    "Why do Java developers wear glasses? Because they don’t C#.",
    "Real programmers count from 0.",
    "I would tell you a UDP joke, but you might not get it.",
    "A SQL query walks into a bar, walks up to two tables and asks... 'Can I join you?'",
    "Why do programmers hate nature? It has too many bugs.",
    "Algorithm: A word used by programmers when they don’t want to explain what they did.",
    "Why was the JavaScript developer sad? Because he didn’t know how to 'null' his feelings."
];

async function postRandomJoke(userId) {
    const randomJoke = jokes[Math.floor(Math.random() * jokes.length)];
    db.run("INSERT INTO jokes (userId, content, created_at) VALUES (?, ?, datetime('now'))", [userId, randomJoke], (err) => {
        if (err) {
            console.error(`Error posting joke for user ${userId}: ${err}`);
        } else {
            console.log(`User ${userId} posted a joke`);
        }
    });
}

async function simulateBrowsing(user) {
    
    const browser = await puppeteer.launch({ headless: true, args: ['--no-sandbox'] });
    const page = await browser.newPage();
    console.log(`${user.nickname} is visiting the login page.`);
    await page.goto('http://localhost:3000/auth/login');
    // Log in
    await page.type('#nickname', user.nickname);
    await page.type('#password', user.password);
    await page.click('button[type="submit"]');
    await page.waitForNavigation();
    // investigate timeout errors
    browser.on('error', (err) => {
        console.error('Error: ', err);
    });
    page.on('response', response => {
        const status = response.status()
        if ((status >= 300) && (status <= 399)) {
            console.log('Redirect from', response.url(), 'to', response.headers()['location'])
        }
    });
    // Visit the wall page
    await page.goto('http://localhost:3000/jokes/wall');
    console.log(`${user.nickname} is visiting the wall page.`);

    // Sometimes post a joke
    if (Math.random() < 0.5) {
        const randomJoke = jokes[Math.floor(Math.random() * jokes.length)];
        await page.type('#content', randomJoke);
        await page.click('button[type="submit"]');
        console.log(`${user.nickname} posted a joke.`);
    }

    await browser.close();
}

function adminActions() {
    // Admin posts a joke randomly every 1 to 5 minutes
    setTimeout(async () => {
        try {

            await postRandomJoke(1); // Admin has userId 1
            await simulateBrowsing({ nickname: 'chosen_one', password: 'adminpassword#2024' });
        } catch (e) {
            console.error(`Error simulating browsing for admin: ${e}`);
        }
        adminActions();

    }, (Math.random() * 5 + 1) * 60 * 100);
}

function randomUserActions() {
    // Random user posts a joke every 1 to 3 minutes
    setTimeout(async () => {
        db.get("SELECT id, nickname, password FROM users WHERE isExternal = 0 and nickname != 'chosen_one' ORDER BY RANDOM() LIMIT 1", async (err, user) => {
            if (err) {
                console.error(`Error selecting random internal user: ${err}`);
                return;
            }
            await postRandomJoke(user.id);
            try {
                await simulateBrowsing({ nickname: user.nickname, password: user.password });
            } catch (e) {
                console.error(`Error simulating browsing for user ${user.nickname}: ${e}`);
            }
            randomUserActions();
        });
    }, (Math.random() * 3 + 1) * 60 * 100);
}

// Start the actions
adminActions();
randomUserActions();

app.listen(port, () => {
    console.log(`Server running at http://localhost:${port}`);
});
