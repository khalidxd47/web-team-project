# UQU Computer Science Club Website

CS3810T Web Development project: a club website built with vanilla PHP, JavaScript, CSS and MySQL.

## Structure

```
index.php            Home page
config/db.php        PDO database connection
includes/            Shared header, footer and helper functions
assets/css/          External stylesheet
assets/js/           JavaScript
schema.sql           Database (club_db) + seed data
```

## Getting started

1. Start WAMP (or XAMPP) so Apache and MySQL are running.
2. Copy this folder into `C:\wamp64\www\` (XAMPP: `C:\xampp\htdocs\`).
3. Import `schema.sql` in phpMyAdmin (or run `mysql -u root -p < schema.sql`).
4. Open http://localhost/web-team-project/ in a browser.
