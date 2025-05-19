const express = require('express');
const path = require('path');
const sanitizeHtml = require('sanitize-html');

module.exports = (db) => {
    const router = express.Router();

    function requireLogin(req, res, next) {
        if (!req.session.userId) {
            console.log('User not logged in, redirecting to login page');
            return res.redirect('/auth/login');
        }
        next();
    }

    router.get('/wall', requireLogin, (req, res) => {
        console.log(`User ${req.session.userId} is accessing the wall`);
        res.sendFile(path.join(__dirname, '..', 'public', 'wall.html'));
    });

    router.post('/post', requireLogin, (req, res) => {
        let { content } = req.body;
        content = sanitizeHtml(content, {
            allowedTags: ["img", "b", "h1", "style", "h2", "h3", "hr", "p", "em", "pre", "ul", "li", "ol"],
            allowedAttributes: { "img": ["src"], "style": ["color"] },
        });
        console.log(`User ${req.session.userId} is posting a new joke`);
        db.run("INSERT INTO jokes (userId, content, created_at) VALUES (?, ?, datetime('now'))", [req.session.userId, content], (err) => {
            if (err) {
                console.error(`Error posting joke: ${err}`);
                return res.status(500).send("Error posting joke.");
            }
            console.log('Joke posted successfully');
            res.redirect('/jokes/wall');
        });
    });

    router.get('/get', requireLogin, (req, res) => {
        console.log(`User ${req.session.userId} is fetching jokes`);
        const sql = `
      SELECT users.nickname, jokes.content, jokes.created_at 
      FROM jokes 
      JOIN users ON jokes.userId = users.id
      ORDER BY jokes.created_at DESC
    `;
        db.all(sql, (err, rows) => {
            if (err) {
                console.error(`Error fetching jokes: ${err}`);
                return res.status(500).send("Error fetching jokes.");
            }
            console.log('Jokes fetched successfully');
            res.json({ jokes: rows });
        });
    });

    router.get('/friends', requireLogin, (req, res) => {
        console.log(`User ${req.session.userId} is accessing the friends list`);
        res.sendFile(path.join(__dirname, '..', 'public', 'friends.html'));
    });

    router.get('/friend/:nickname', requireLogin, (req, res) => {
        const { nickname } = req.params;
        console.log(`User ${req.session.userId} is adding friend with nickname: ${nickname}`);
        db.get("SELECT id FROM users WHERE nickname = ?", [nickname], (err, row) => {
            if (err || !row) {
                console.error(`Error finding friend with nickname ${nickname}: ${err}`);
                return res.status(500).send("Error adding friend.");
            }
            const friendId = row.id;
            db.run("INSERT INTO friends (userId, friendId) VALUES (?, ?)", [req.session.userId, friendId], (err) => {
                if (err) {
                    console.error(`Error adding friend with ID ${friendId}: ${err}`);
                    return res.status(500).send("Error adding friend.");
                }
                console.log(`Friend with ID ${friendId} added successfully`);
                res.redirect('/jokes/friends');
            });
        });
    });

    router.get('/get-friends', requireLogin, (req, res) => {
        console.log(`User ${req.session.userId} is fetching friends list`);
        const sql = `
      SELECT users.nickname, users.joke
      FROM friends
      JOIN users ON friends.friendId = users.id
      WHERE friends.userId = ?
      UNION
      SELECT users.nickname, users.joke
      FROM friends
      JOIN users ON friends.userId = users.id
      WHERE friends.friendId = ?
    `;
        db.all(sql, [req.session.userId, req.session.userId], (err, rows) => {
            if (err) {
                console.error(`Error fetching friends: ${err}`);
                return res.status(500).send("Error fetching friends.");
            }
            console.log('Friends list fetched successfully:', rows.map(friend => `${friend.nickname} (${friend.joke})`).join(', '));
            res.json({ friends: rows });
        });
    });

    return router;
};
