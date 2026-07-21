<?php
/**
 * GoWorker - Database Connection Test Page
 * 
 * Tests the database connection and displays diagnostics: host, user,
 * MySQL version, database name, and server status.
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/Database.php';

$connected = false;
$error_message = '';
$db_name = DB_NAME;
$mysql_version = 'Unknown';
$current_user = 'Unknown';
$current_host = 'Unknown';
$server_ip = $_SERVER['SERVER_ADDR'] ?? '127.0.0.1';
$start_time = microtime(true);
$connection_time = 0;

try {
    $pdo = Database::getInstance()->getConnection();
    $connected = true;
    
    // Measure connection time
    $connection_time = Database::getInstance()->getConnectionTime();
    
    // Query MySQL Server information
    $mysql_version = $pdo->getAttribute(PDO::ATTR_SERVER_VERSION);
    $current_user = $pdo->query("SELECT CURRENT_USER()")->fetchColumn();
    $current_host = $pdo->query("SELECT @@hostname")->fetchColumn();
} catch (Exception $e) {
    $error_message = $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoWorker - Database Test Status</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        :root {
            --dark-navy: #090d16;
            --primary: #1245C5;
            --primary-hover: #0d3494;
            --primary-light: rgba(18, 69, 197, 0.08);
            --border-color: #e2e8f0;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --white: #ffffff;
            --success: #22c55e;
            --danger: #ef4444;
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
        .status-card {
            background-color: var(--white);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            max-width: 600px;
            width: 100%;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .status-indicator {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            margin: 0 auto 16px auto;
        }
        .status-connected {
            background-color: rgba(34, 197, 94, 0.1);
            color: var(--success);
        }
        .status-disconnected {
            background-color: rgba(239, 68, 68, 0.1);
            color: var(--danger);
        }
        h1 {
            font-size: 22px;
            font-weight: 700;
            color: var(--dark-navy);
        }
        .diagnostic-list {
            margin-top: 24px;
            border-top: 1px solid var(--border-color);
            padding-top: 20px;
        }
        .diag-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .diag-item:last-child {
            border-bottom: none;
        }
        .diag-label {
            font-size: 14px;
            color: var(--text-muted);
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .diag-label i {
            width: 18px;
            text-align: center;
            color: var(--primary);
        }
        .diag-value {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-dark);
            font-family: monospace;
        }
        .diag-success-text {
            color: var(--success);
            font-weight: 700;
        }
        .btn-retry {
            margin-top: 30px;
            width: 100%;
            background-color: var(--primary);
            color: var(--white);
            border: none;
            padding: 14px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(18, 69, 197, 0.2);
            text-decoration: none;
        }
        .btn-retry:hover {
            background-color: var(--primary-hover);
        }
        .error-box {
            background-color: #0f172a;
            color: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            font-family: monospace;
            font-size: 12px;
            margin-top: 20px;
            border: 1px solid #1e293b;
            line-height: 1.5;
            white-space: pre-wrap;
        }
    </style>
</head>
<body>
    <div class="status-card">
        <div class="header">
            <?php if ($connected): ?>
                <div class="status-indicator status-connected">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <h1>MySQL Connection Success</h1>
            <?php else: ?>
                <div class="status-indicator status-disconnected">
                    <i class="fa-solid fa-circle-xmark"></i>
                </div>
                <h1>MySQL Connection Failed</h1>
            <?php endif; ?>
        </div>

        <div class="diagnostic-list">
            <div class="diag-item">
                <span class="diag-label"><i class="fa-solid fa-heartbeat"></i> Status:</span>
                <span class="diag-value <?= $connected ? 'diag-success-text' : '' ?>">
                    <?= $connected ? '✓ Connected Successfully' : '✗ Disconnected' ?>
                </span>
            </div>
            <div class="diag-item">
                <span class="diag-label"><i class="fa-solid fa-network-wired"></i> Database Host:</span>
                <span class="diag-value"><?= htmlspecialchars(DB_HOST) ?></span>
            </div>
            <div class="diag-item">
                <span class="diag-label"><i class="fa-solid fa-plug"></i> Database Port:</span>
                <span class="diag-value"><?= htmlspecialchars(DB_PORT) ?></span>
            </div>
            <div class="diag-item">
                <span class="diag-label"><i class="fa-solid fa-database"></i> Database Name:</span>
                <span class="diag-value"><?= htmlspecialchars($db_name) ?></span>
            </div>
            <div class="diag-item">
                <span class="diag-label"><i class="fa-solid fa-user-gear"></i> MySQL User:</span>
                <span class="diag-value"><?= htmlspecialchars($current_user) ?></span>
            </div>
            <div class="diag-item">
                <span class="diag-label"><i class="fa-solid fa-server"></i> Server IP:</span>
                <span class="diag-value"><?= htmlspecialchars($server_ip) ?></span>
            </div>
            <?php if ($connected): ?>
                <div class="diag-item">
                    <span class="diag-label"><i class="fa-solid fa-code-branch"></i> MySQL Version:</span>
                    <span class="diag-value"><?= htmlspecialchars($mysql_version) ?></span>
                </div>
                <div class="diag-item">
                    <span class="diag-label"><i class="fa-solid fa-stopwatch"></i> Connection Time:</span>
                    <span class="diag-value"><?= htmlspecialchars($connection_time) ?> ms</span>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!$connected): ?>
            <div class="error-box"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>

        <a href="test-db.php" class="btn-retry">
            <i class="fa-solid fa-rotate-right"></i> Run Diagnostic Test Again
        </a>
        <a href="index.php" class="btn-retry" style="background-color: #f1f5f9; color: var(--text-dark); border: 1px solid var(--border-color); box-shadow: none; margin-top: 10px;">
            <i class="fa-solid fa-house"></i> Return to Homepage
        </a>
    </div>
</body>
</html>
