<?php
/**
 * GoWorker Database Setup and Initialization Tool
 * 
 * This command-line script initializes the MySQL database for development.
 * It reads credentials from config/database.php, creates the database if it doesn't
 * exist, and runs the goworker.sql schema file.
 */

define('CONFIG_PATH', __DIR__ . '/../config/database.php');
define('SCHEMA_PATH', __DIR__ . '/goworker.sql');

echo "=== GoWorker Database Setup ===\n";

if (!file_exists(CONFIG_PATH)) {
    echo "ERROR: Config file not found at " . CONFIG_PATH . "\n";
    echo "Please ensure you have configured your database connection file.\n";
    exit(1);
}

if (!file_exists(SCHEMA_PATH)) {
    echo "ERROR: Database schema SQL file not found at " . SCHEMA_PATH . "\n";
    exit(1);
}

// Read database parameters from configuration file
// We do this by temporarily overriding connection errors or parsing parameters.
// Since database.php connects immediately on include/require, let's parse or capture.
$configContent = file_get_contents(CONFIG_PATH);

// Simple extraction of parameters using regex so we don't trigger PDO error if db doesn't exist yet
$host = 'localhost';
$db = 'goworker';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

if (preg_match('/\$host\s*=\s*[\'"]([^\'"]+)[\'"]/', $configContent, $matches)) {
    $host = $matches[1];
}
if (preg_match('/\$db\s*=\s*[\'"]([^\'"]+)[\'"]/', $configContent, $matches)) {
    $db = $matches[1];
}
if (preg_match('/\$user\s*=\s*[\'"]([^\'"]+)[\'"]/', $configContent, $matches)) {
    $user = $matches[1];
}
if (preg_match('/\$pass\s*=\s*[\'"]([^\'"]*)[\'"]/', $configContent, $matches)) {
    $pass = $matches[1];
}
if (preg_match('/\$charset\s*=\s*[\'"]([^\'"]+)[\'"]/', $configContent, $matches)) {
    $charset = $matches[1];
}

echo "Detected Configuration:\n";
echo "  Host:      $host\n";
echo "  Database:  $db\n";
echo "  User:      $user\n";
echo "  Password:  " . ($pass === '' ? '(empty)' : '********') . "\n";
echo "  Charset:   $charset\n\n";

try {
    echo "1. Connecting to MySQL server at $host...\n";
    $pdo = new PDO("mysql:host=$host;charset=$charset", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    echo "   [SUCCESS] Connected to MySQL server.\n\n";
} catch (PDOException $e) {
    echo "   [ERROR] Could not connect to MySQL server. Details: " . $e->getMessage() . "\n";
    echo "\nTroubleshooting Tips:\n";
    echo "1. Check if MySQL/MariaDB server is running (e.g. in XAMPP control panel).\n";
    echo "2. Verify host, user, and password in config/database.php.\n";
    exit(1);
}

try {
    echo "2. Re-creating/Verifying database '$db'...\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$db`");
    echo "   [SUCCESS] Database '$db' is ready.\n\n";
} catch (PDOException $e) {
    echo "   [ERROR] Failed to verify/create database: " . $e->getMessage() . "\n";
    exit(1);
}

try {
    echo "3. Importing database schema and seeded data...\n";
    $sql = file_get_contents(SCHEMA_PATH);
    
    // We execute the SQL schema.
    $pdo->exec($sql);
    echo "   [SUCCESS] Schema and categories imported successfully.\n\n";
    echo "=== Setup Completed Successfully! ===\n";
    echo "You can now open the project in your browser, e.g. http://localhost/GoWorker/GoWorker/\n";
} catch (PDOException $e) {
    echo "   [ERROR] Failed to import schema: " . $e->getMessage() . "\n";
    exit(1);
}
