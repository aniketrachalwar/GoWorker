<?php
/**
<<<<<<< HEAD
 * Database Configuration File - Standard XAMPP Setup
 * Establishes a PDO connection to the MySQL database.
 */

$host = "localhost";
$dbname = "goworker";
$username = "root";
$password = "";

$pdo = null;

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database connection error: " . $e->getMessage());
    $db_connection_error = "Database 'goworker' not found. Please import database/goworker.sql in phpMyAdmin.";
=======
 * GoWorker - Database Bridge Configuration
 * 
 * Central database bridge. Delegates connection management to the new
 * centralized includes/Database.php class, preserving global variables
 * for backward compatibility.
 */

// Load the centralized project configuration
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/Database.php';

// Expose individual connection parameter variables for reference
$host = DB_HOST;
$db   = DB_NAME;
$user = DB_USER;
$pass = DB_PASS;
$charset = DB_CHARSET;

try {
    // Fetch the Singleton database connection
    $pdo = Database::getInstance()->getConnection();
} catch (Exception $e) {
    error_log("Database bridge connection error: " . $e->getMessage());
    $db_connection_error = "Unable to connect to the database. Please verify your connection configuration.";
>>>>>>> a52a51d990b13a5026bd6eb55d95ed265ddac057
}
?>
