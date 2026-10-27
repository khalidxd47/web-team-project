<?php
/**
 * Database connection (PDO)
 *
 * Include this file in any page that needs the database:
 *     require_once __DIR__ . '/config/db.php';
 * It creates a ready-to-use $pdo object.
 */

// Connection settings (XAMPP/WAMP defaults: user "root", empty password)
define('DB_HOST', 'localhost');
define('DB_NAME', 'club_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4'); // full Unicode, including Arabic text

// DSN (Data Source Name): tells PDO which driver, server, database and charset to use
$dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;

$options = [
    // Throw exceptions on SQL errors so they can be caught with try/catch
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    // Return rows as associative arrays: $row['name'] instead of $row[1]
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    // Use real prepared statements in MySQL instead of emulating them in PHP
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    // Log the real error for the developer, but never show credentials or details to visitors
    error_log('Database connection failed: ' . $e->getMessage());
    http_response_code(500);
    exit('Sorry, the website is temporarily unavailable. Please try again later.');
}

/*
 * Example usage with a prepared statement (safe from SQL injection):
 *
 *     $stmt = $pdo->prepare('SELECT * FROM events WHERE id = :id');
 *     $stmt->execute(['id' => $_GET['id']]);
 *     $event = $stmt->fetch();
 */
