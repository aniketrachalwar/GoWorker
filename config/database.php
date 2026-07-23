<?php
/**
 * GoWorker - Database Configuration and Connection
 * 
 * Reusable PDO connection optimized for both local development and online hosting environments.
 */

// Load the central configuration
require_once __DIR__ . '/config.php';

// Expose individual connection parameters for reference/backward compatibility
$host = DB_HOST;
$port = DB_PORT;
$db   = DB_NAME;
$user = DB_USER;
$pass = DB_PASS;
$charset = DB_CHARSET;

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
    ];
    
    // Establish the reusable PDO connection
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // Log error internally (without exposing password)
    error_log("Database Connection Failure: " . $e->getMessage());
    
    // Clear output buffer to prevent partial layout rendering
    if (ob_get_level() > 0) {
        ob_clean();
    }
    
    // Return a clean, user-friendly error page without exposing database passwords or PHP fatal stacktraces
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Database Connection Failed</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
        <style>
            :root {
                --dark-navy: #090d16;
                --primary: #1245C5;
                --primary-light: rgba(18, 69, 197, 0.08);
                --border-color: #e2e8f0;
                --text-dark: #1e293b;
                --text-muted: #64748b;
                --white: #ffffff;
                --danger: #ef4444;
                --danger-light: rgba(239, 68, 68, 0.08);
                --radius-lg: 16px;
            }
            * {
                box-sizing: border-box;
                margin: 0;
                padding: 0;
                font-family: 'Poppins', sans-serif;
            }
            body {
                background-color: #f8fafc;
                color: var(--text-dark);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }
            .error-card {
                background-color: var(--white);
                border: 1px solid var(--border-color);
                border-radius: var(--radius-lg);
                max-width: 550px;
                width: 100%;
                padding: 40px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
                text-align: center;
            }
            .error-icon {
                width: 72px;
                height: 72px;
                background-color: var(--danger-light);
                color: var(--danger);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 32px;
                margin: 0 auto 20px auto;
            }
            h1 {
                font-size: 22px;
                font-weight: 700;
                color: var(--dark-navy);
                margin-bottom: 12px;
            }
            p {
                font-size: 14px;
                color: var(--text-muted);
                line-height: 1.6;
            }
        </style>
    </head>
    <body>
        <div class="error-card">
            <div class="error-icon">⚠️</div>
            <h1>Database Connection Failed</h1>
            <p>Database connection failed. Please verify hosting database credentials.</p>
        </div>
    </body>
    </html>
    <?php
    exit();
}
?>
