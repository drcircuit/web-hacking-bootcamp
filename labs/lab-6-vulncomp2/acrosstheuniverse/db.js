const sqlite3 = require('sqlite3').verbose();

function initializeDatabase() {
  const db = new sqlite3.Database(':memory:');

  const resetDatabase = () => {
    db.serialize(() => {
      db.run("DROP TABLE IF EXISTS users");
      db.run("DROP TABLE IF EXISTS jokes");
      db.run("DROP TABLE IF EXISTS friends");

      db.run("CREATE TABLE users (id INTEGER PRIMARY KEY, nickname TEXT, name TEXT, joke TEXT, password TEXT, isExternal INTEGER DEFAULT 0)", (err) => {
        if (err) {
          console.error(`Error creating users table: ${err}`);
        } else {
          console.log('Users table created successfully');
        }
      });
      db.run("CREATE TABLE jokes (id INTEGER PRIMARY KEY, userId INTEGER, content TEXT, created_at DATETIME DEFAULT CURRENT_TIMESTAMP)", (err) => {
        if (err) {
          console.error(`Error creating jokes table: ${err}`);
        } else {
          console.log('Jokes table created successfully');
        }
      });
      db.run("CREATE TABLE friends (id INTEGER PRIMARY KEY, userId INTEGER, friendId INTEGER)", (err) => {
        if (err) {
          console.error(`Error creating friends table: ${err}`);
        } else {
          console.log('Friends table created successfully');
        }
      });

      // Insert admin user
      const adminName = 'Neo';
      const adminJoke = `flag{Why_do_programmers_prefer_dark_mode?_Because_light_attracts_bugs!}`;
      db.run("INSERT INTO users (nickname, name, joke, password, isExternal) VALUES (?, ?, ?, ?, 0)", ['chosen_one', adminName, adminJoke, 'adminpassword#2024'], (err) => {
        if (err) {
          console.error(`Error inserting admin user: ${err}`);
        } else {
          console.log('Admin user inserted successfully');
        }
      });

      // Insert random users
      const users = [
        { nickname: 'hacker_alice', name: 'Alice', joke: 'Why don’t skeletons fight each other? They don’t have the guts.', password: 'password1' },
        { nickname: 'cyber_bob', name: 'Bob', joke: 'What do you call fake spaghetti? An impasta!', password: 'password2' },
        { nickname: 'charlie_crypt', name: 'Charlie', joke: 'How many tickles does it take to make an octopus laugh? Ten tickles.', password: 'password3' },
        { nickname: 'dark_dave', name: 'Dave', joke: 'Why did the scarecrow win an award? Because he was outstanding in his field.', password: 'password4' },
        { nickname: 'evil_eve', name: 'Eve', joke: 'I would avoid the sushi if I was you. It’s a little fishy.', password: 'password5' },
        { nickname: 'frank_fury', name: 'Frank', joke: 'Want to hear a joke about construction? I’m still working on it.', password: 'password6' },
        { nickname: 'grace_giggles', name: 'Grace', joke: 'Why don’t scientists trust atoms? Because they make up everything.', password: 'password7' },
        { nickname: 'hank_hax', name: 'Hank', joke: 'What do you call cheese that isn’t yours? Nacho cheese.', password: 'password8' },
        { nickname: 'ivy_invader', name: 'Ivy', joke: 'How does a penguin build its house? Igloos it together.', password: 'password9' },
        { nickname: 'jack_jester', name: 'Jack', joke: 'Why did the math book look sad? Because it had too many problems.', password: 'password10' }
      ];

      const insertUser = db.prepare("INSERT INTO users (nickname, name, joke, password, isExternal) VALUES (?, ?, ?, ?, 0)");
      users.forEach(user => {
        insertUser.run(user.nickname, user.name, user.joke, user.password, (err) => {
          if (err) {
            console.error(`Error inserting user ${user.nickname}: ${err}`);
          } else {
            console.log(`User ${user.nickname} inserted successfully`);
          }
        });
      });
      insertUser.finalize();
    });
  };

  resetDatabase();

  // Reset the database every 5 minutes
  setInterval(() => {
    console.log('Resetting the database...');
    resetDatabase();
  }, 5 * 60 * 1000);

  return db;
}

module.exports = initializeDatabase;
