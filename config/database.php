<?php
/**
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
}
?>
