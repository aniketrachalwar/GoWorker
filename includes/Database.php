<?php
/**
 * GoWorker - Database Connection Manager
 * 
 * Implements a Singleton pattern for database connections with automated
 * reconnect retries, error logging, and developer recovery pages.
 */

class Database {
    private static $instance = null;
    private $pdo = null;
    private $connection_time = 0; // Connection time in ms

    private function __construct() {
        if (!defined('DB_HOST')) {
            require_once __DIR__ . '/../config.php';
        }

        // Handle configuration saves from the offline recovery page
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_config') {
            $new_config = [
                'db_host' => trim($_POST['db_host'] ?? ''),
                'db_port' => trim($_POST['db_port'] ?? ''),
                'db_name' => trim($_POST['db_name'] ?? ''),
                'db_user' => trim($_POST['db_user'] ?? ''),
                'db_pass' => $_POST['db_pass'] ?? '',
            ];
            $local_config_path = __DIR__ . '/../config_local.json';
            file_put_contents($local_config_path, json_encode($new_config, JSON_PRETTY_PRINT));
            
            // Redirect to clean POST inputs and reload the active page
            $redirect_url = strtok($_SERVER["REQUEST_URI"], '?');
            header("Location: " . $redirect_url);
            exit();
        }

        $host = DB_HOST;
        $port = DB_PORT;
        $db   = DB_NAME;
        $user = DB_USER;
        $pass = DB_PASS;
        $charset = DB_CHARSET;
        $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_TIMEOUT            => DB_CONNECT_TIMEOUT,
        ];

        $attempt = 1;
        $connected = false;
        $start_time = microtime(true);
        $last_exception = null;

        while ($attempt <= DB_MAX_RETRIES && !$connected) {
            try {
                $this->pdo = new PDO($dsn, $user, $pass, $options);
                $connected = true;
                $this->connection_time = round((microtime(true) - $start_time) * 1000, 2);
            } catch (PDOException $e) {
                $last_exception = $e;
                $this->logError($attempt, $e->getMessage());
                if ($attempt < DB_MAX_RETRIES) {
                    sleep(DB_RETRY_DELAY);
                }
                $attempt++;
            }
        }

        if (!$connected) {
            $this->renderRecoveryPage($dsn, $user, $last_exception);
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }

    public function getConnectionTime() {
        return $this->connection_time;
    }

    private function logError($attempt, $message) {
        $timestamp = date('Y-m-d H:i:s');
        // Log Format: Date | Time | Host | Username | Database | Exception Message
        $logMessage = "[$timestamp] Attempt $attempt | Host: " . DB_HOST . ":" . DB_PORT . " | User: " . DB_USER . " | DB: " . DB_NAME . " | Msg: $message\n";
        
        $logDir = dirname(LOG_FILE);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
        
        file_put_contents(LOG_FILE, $logMessage, FILE_APPEND);
    }

    private function renderRecoveryPage($dsn, $user, $exception) {
        if (ob_get_level() > 0) {
            ob_clean();
        }

        $error_message = $exception ? $exception->getMessage() : 'Unknown Database Error';
        $error_code = $exception ? $exception->getCode() : 0;
        
        // Analyze exception to determine detailed user-friendly error codes
        $category = 'Unknown Connection Failure';
        $solution_tips = [];

        if (strpos($error_message, 'Access denied for user') !== false) {
            if ($user === 'root') {
                $category = 'Access Denied (Root Password Mismatch)';
                $solution_tips[] = 'XAMPP root user has a password set. Update the password field below.';
            } else {
                $category = 'Access Denied (Wrong User or Password)';
                $solution_tips[] = 'Ensure the MySQL user has been created on the Server Laptop.';
                $solution_tips[] = 'Verify that the password corresponds to the credentials in config.php.';
            }
        } elseif (strpos($error_message, 'Unknown database') !== false) {
            $category = 'Missing Database';
            $solution_tips[] = 'The database schema has not been initialized on the MySQL server.';
            $solution_tips[] = 'Run the SQL creation scripts listed below in phpMyAdmin or command line.';
        } elseif (strpos($error_message, 'Connection timed out') !== false || strpos($error_message, 'Connection refused') !== false || strpos($error_message, '2002') !== false) {
            $category = 'Host Unreachable / Connection Timeout';
            $solution_tips[] = 'Check if MySQL/MariaDB server is running on the Server Laptop.';
            $solution_tips[] = 'Ensure the host IP address matches the Server Laptop\'s network IP.';
            $solution_tips[] = 'Ensure the Server Laptop\'s Windows Defender Firewall allows inbound TCP requests on port 3306.';
        }

        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>GoWorker - Database Diagnostics & Offline Mode</title>
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
            <style>
                :root {
                    --dark-navy: #090d16;
                    --primary: #1245C5;
                    --primary-hover: #0d3494;
                    --primary-light: rgba(18, 69, 197, 0.06);
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
                    padding: 40px 20px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                .recovery-layout {
                    max-width: 900px;
                    width: 100%;
                    display: grid;
                    grid-template-columns: 1.2fr 1fr;
                    gap: 30px;
                }
                .card {
                    background-color: var(--white);
                    border: 1px solid var(--border-color);
                    border-radius: var(--radius-lg);
                    padding: 35px;
                    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
                }
                .error-header {
                    text-align: center;
                    margin-bottom: 24px;
                }
                .error-icon {
                    width: 64px;
                    height: 64px;
                    background-color: var(--danger-light);
                    color: var(--danger);
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 28px;
                    margin: 0 auto 16px auto;
                }
                h1 {
                    font-size: 22px;
                    font-weight: 700;
                    color: var(--dark-navy);
                }
                .badge-error-type {
                    display: inline-block;
                    background-color: var(--danger-light);
                    color: var(--danger);
                    border: 1.5px solid rgba(239, 68, 68, 0.15);
                    padding: 6px 12px;
                    border-radius: 6px;
                    font-size: 13px;
                    font-weight: 600;
                    margin-top: 10px;
                }
                .section-title {
                    font-size: 15px;
                    font-weight: 700;
                    color: var(--dark-navy);
                    margin: 20px 0 12px 0;
                    display: flex;
                    align-items: center;
                    gap: 8px;
                }
                .exception-box {
                    background-color: #0f172a;
                    color: #e2e8f0;
                    font-family: monospace;
                    font-size: 12px;
                    padding: 15px;
                    border-radius: 8px;
                    overflow-x: auto;
                    line-height: 1.5;
                    border: 1px solid #1e293b;
                    margin-bottom: 16px;
                }
                .tip-list {
                    list-style-type: none;
                    font-size: 13px;
                    line-height: 1.6;
                    color: var(--text-muted);
                }
                .tip-list li {
                    margin-bottom: 8px;
                    padding-left: 20px;
                    position: relative;
                }
                .tip-list li::before {
                    content: "→";
                    position: absolute;
                    left: 0;
                    color: var(--primary);
                    font-weight: 700;
                }
                .form-group {
                    margin-bottom: 16px;
                }
                .form-group label {
                    display: block;
                    font-size: 12px;
                    font-weight: 600;
                    color: var(--text-muted);
                    margin-bottom: 6px;
                }
                .form-control {
                    width: 100%;
                    padding: 10px 14px;
                    border: 1.5px solid var(--border-color);
                    border-radius: 6px;
                    font-size: 13px;
                    color: var(--text-dark);
                    font-weight: 500;
                    transition: all 0.2s ease;
                }
                .form-control:focus {
                    border-color: var(--primary);
                    outline: none;
                }
                .btn {
                    width: 100%;
                    padding: 12px;
                    border-radius: 6px;
                    font-size: 14px;
                    font-weight: 600;
                    cursor: pointer;
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    gap: 8px;
                    border: none;
                    transition: all 0.2s ease;
                }
                .btn-primary {
                    background-color: var(--primary);
                    color: var(--white);
                    box-shadow: 0 4px 12px rgba(18, 69, 197, 0.15);
                }
                .btn-primary:hover {
                    background-color: var(--primary-hover);
                }
                .btn-secondary {
                    background-color: #f1f5f9;
                    color: var(--text-dark);
                    border: 1px solid var(--border-color);
                    margin-top: 10px;
                }
                .btn-secondary:hover {
                    background-color: #e2e8f0;
                }
                .countdown-text {
                    font-size: 12px;
                    color: var(--text-muted);
                    text-align: center;
                    margin-top: 12px;
                }
                .sql-copy-box {
                    position: relative;
                    margin-top: 15px;
                }
                .sql-code {
                    background-color: #f8fafc;
                    border: 1px solid var(--border-color);
                    padding: 12px;
                    font-family: monospace;
                    font-size: 11px;
                    color: var(--text-dark);
                    border-radius: 6px;
                    display: block;
                    white-space: pre-wrap;
                }
                @media (max-width: 800px) {
                    .recovery-layout {
                        grid-template-columns: 1fr;
                    }
                }
            </style>
        </head>
        <body>
            <div class="recovery-layout">
                <!-- Left panel: diagnostics and copy-paste SQL scripts -->
                <div class="card">
                    <div class="error-header">
                        <div class="error-icon">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <h1>Database Offline</h1>
                        <span class="badge-error-type"><?= htmlspecialchars($category) ?></span>
                    </div>

                    <div class="section-title"><i class="fa-solid fa-code"></i> PDO Exception Message</div>
                    <div class="exception-box"><?= htmlspecialchars($error_message) ?></div>

                    <?php if (!empty($solution_tips)): ?>
                        <div class="section-title"><i class="fa-solid fa-wrench"></i> Troubleshooting Checklist</div>
                        <ul class="tip-list">
                            <?php foreach ($solution_tips as $tip): ?>
                                <li><?= htmlspecialchars($tip) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>

                    <!-- Copy-paste scripts for developer setup -->
                    <div class="section-title"><i class="fa-solid fa-database"></i> Setup SQL Helpers (Copy/Paste)</div>
                    <p style="font-size: 12px; color: var(--text-muted); margin-bottom: 8px;">Run this in phpMyAdmin SQL tab to quickly initialize the database and LAN developer user:</p>
                    <div class="sql-copy-box">
                        <span class="sql-code">CREATE DATABASE IF NOT EXISTS `<?= htmlspecialchars(DB_NAME) ?>` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '<?= htmlspecialchars(DB_USER) ?>'@'%' IDENTIFIED BY '<?= htmlspecialchars(DB_PASS) ?>';
GRANT ALL PRIVILEGES ON `<?= htmlspecialchars(DB_NAME) ?>`.* TO '<?= htmlspecialchars(DB_USER) ?>'@'%';
FLUSH PRIVILEGES;</span>
                    </div>
                </div>

                <!-- Right panel: live config override modifier -->
                <div class="card">
                    <div class="section-title" style="margin-top:0;"><i class="fa-solid fa-sliders"></i> Modify DB Settings</div>
                    <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 20px;">These changes are saved locally to <code style="font-family: monospace;">config_local.json</code> and will not affect Git commits.</p>
                    
                    <form method="POST">
                        <input type="hidden" name="action" value="save_config">
                        
                        <div class="form-group">
                            <label for="db_host">Database Host (IP / Localhost)</label>
                            <input type="text" id="db_host" name="db_host" class="form-control" value="<?= htmlspecialchars(DB_HOST) ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="db_port">Port</label>
                            <input type="text" id="db_port" name="db_port" class="form-control" value="<?= htmlspecialchars(DB_PORT) ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="db_name">Database Name</label>
                            <input type="text" id="db_name" name="db_name" class="form-control" value="<?= htmlspecialchars(DB_NAME) ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="db_user">Database Username</label>
                            <input type="text" id="db_user" name="db_user" class="form-control" value="<?= htmlspecialchars(DB_USER) ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="db_pass">Database Password</label>
                            <input type="password" id="db_pass" name="db_pass" class="form-control" value="<?= htmlspecialchars(DB_PASS) ?>">
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-floppy-disk"></i> Save & Reconnect
                        </button>
                        
                        <button type="button" class="btn btn-secondary" onclick="window.location.reload();">
                            <i class="fa-solid fa-rotate-right"></i> Retry Connection
                        </button>
                    </form>
                    
                    <div class="countdown-text" id="countdown-wrapper">
                        Auto-retrying connection in <span id="seconds-countdown">5</span> seconds...
                    </div>
                </div>
            </div>

            <script>
                let seconds = 5;
                const countdownEl = document.getElementById('seconds-countdown');
                const interval = setInterval(() => {
                    seconds--;
                    countdownEl.textContent = seconds;
                    if (seconds <= 0) {
                        clearInterval(interval);
                        window.location.reload();
                    }
                }, 1000);
            </script>
        </body>
        </html>
        <?php
        exit();
    }
}
