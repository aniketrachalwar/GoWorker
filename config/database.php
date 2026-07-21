<?php
/**
 * Database Configuration File
 * Establishes a PDO connection to the MySQL database.
 */

// Database connection parameters - Check for cloud database URLs or standard environment variables
if (getenv('DATABASE_URL')) {
    $dbparts = parse_url(getenv('DATABASE_URL'));
    $host = $dbparts['host'] ?? 'localhost';
    $user = $dbparts['user'] ?? 'root';
    $pass = $dbparts['pass'] ?? '';
    $db   = ltrim($dbparts['path'] ?? 'goworker', '/');
} else {
    $host = getenv('DB_HOST') ?: 'localhost';
    $db   = getenv('DB_NAME') ?: 'goworker';
    $user = getenv('DB_USER') ?: 'root';
    $pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';
}
$charset = 'utf8mb4';

// Data Source Name
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// PDO options
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // In production/local development connection failures, log the actual message 
    // and show a user-friendly error without exposing passwords or configuration details.
    error_log("Database connection error: " . $e->getMessage());
    $db_connection_error = "Unable to connect to the database. Please verify your MySQL server is running in XAMPP and the database 'goworker' exists.";
}
