<?php
/**
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
}
