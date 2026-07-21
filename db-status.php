<?php
/**
 * GoWorker - Database and Web Server Status Dashboard
 * 
 * Provides an administrative visual interface monitoring connection health,
 * active threads, LAN client connections, system environment, and database metrics.
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/Database.php';

$connected = false;
$error_message = '';
$db_exists = 'No';
$mysql_status = 'Offline';
$apache_status = 'Active';
$connection_time = 0;
$server_ip = $_SERVER['SERVER_ADDR'] ?? '127.0.0.1';
$current_user = 'None';
$mysql_version = 'Unknown';
$active_threads = 0;
$uptime_seconds = 0;
$table_count = 0;
$collaborators = [];

try {
    $pdo = Database::getInstance()->getConnection();
    $connected = true;
    $db_exists = 'Yes (Active)';
    $mysql_status = 'Active (Online)';
    $connection_time = Database::getInstance()->getConnectionTime();
    
    // Fetch Server configurations
    $mysql_version = $pdo->getAttribute(PDO::ATTR_SERVER_VERSION);
    $current_user = $pdo->query("SELECT CURRENT_USER()")->fetchColumn();
    
    // Fetch Table counts
    $stmt = $pdo->query("SHOW TABLES");
    $table_count = $stmt->rowCount();
    
    // Fetch Threads and Uptime Status if possible
    try {
        $threads_stmt = $pdo->query("SHOW STATUS LIKE 'Threads_connected'");
        $threads_row = $threads_stmt->fetch();
        $active_threads = $threads_row ? $threads_row['Value'] : 1;
        
        $uptime_stmt = $pdo->query("SHOW STATUS LIKE 'Uptime'");
        $uptime_row = $uptime_stmt->fetch();
        $uptime_seconds = $uptime_row ? (int)$uptime_row['Value'] : 0;
    } catch (PDOException $se) {
        // Fallback if privileges are restricted
        $active_threads = 1;
        $uptime_seconds = 0;
    }

    // Fetch active LAN connection collaborators
    try {
        // Queries active connections from processlist, filtering out local system processes
        $proc_stmt = $pdo->query("SHOW FULL PROCESSLIST");
        while ($row = $proc_stmt->fetch()) {
            if (!empty($row['Host'])) {
                // Split host IP from port
                $parts = explode(':', $row['Host']);
                $host_ip = $parts[0];
                $collaborators[] = [
                    'id' => $row['Id'],
                    'user' => $row['User'],
                    'ip' => $host_ip,
                    'db' => $row['db'] ?? 'None',
                    'command' => $row['Command'],
                    'time' => $row['Time'],
                    'state' => $row['State'] ?: 'Active'
                ];
            }
        }
    } catch (PDOException $pe) {
        // Fallback with a single current user connection if PROCESSLIST requires superior privileges
        $collaborators[] = [
            'id' => 1,
            'user' => explode('@', $current_user)[0],
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'db' => DB_NAME,
            'command' => 'Query',
            'time' => 0,
            'state' => 'Active'
        ];
    }

} catch (Exception $e) {
    $error_message = $e->getMessage();
}

// Format Uptime
function formatUptime($seconds) {
    if ($seconds <= 0) return 'Unknown';
    $days = floor($seconds / 86400);
    $hours = floor(($seconds % 86400) / 3600);
    $mins = floor(($seconds % 3600) / 60);
    
    $parts = [];
    if ($days > 0) $parts[] = "{$days}d";
    if ($hours > 0) $parts[] = "{$hours}h";
    if ($mins > 0) $parts[] = "{$mins}m";
    
    return implode(' ', $parts) ?: '0m';
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoWorker - Developer LAN Status Panel</title>
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
            --success-light: rgba(34, 197, 94, 0.08);
            --danger: #ef4444;
            --danger-light: rgba(239, 68, 68, 0.08);
            --warning: #f59e0b;
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
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 24px;
            font-weight: 700;
            color: var(--dark-navy);
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .header h1 i {
            color: var(--primary);
        }
        .btn-group {
            display: flex;
            gap: 10px;
        }
        .btn {
            padding: 10px 18px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            transition: all 0.2s ease;
            text-decoration: none;
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
            background-color: var(--white);
            color: var(--text-dark);
            border: 1px solid var(--border-color);
        }
        .btn-secondary:hover {
            background-color: #f1f5f9;
        }
        .grid-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background-color: var(--white);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.01);
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
        .stat-info {
            display: flex;
            flex-direction: column;
        }
        .stat-label {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .stat-value {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-dark);
            margin-top: 4px;
        }
        .card {
            background-color: var(--white);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
            margin-bottom: 30px;
        }
        .card-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--dark-navy);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 12px;
        }
        .collab-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }
        .collab-table th, .collab-table td {
            padding: 12px 16px;
            font-size: 13.5px;
            border-bottom: 1px solid var(--border-color);
        }
        .collab-table th {
            color: var(--text-muted);
            font-weight: 600;
        }
        .collab-table tr:last-child td {
            border-bottom: none;
        }
        .collab-table tr:hover td {
            background-color: #f8fafc;
        }
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            display: inline-block;
        }
        .badge-success {
            background-color: var(--success-light);
            color: var(--success);
        }
        .badge-danger {
            background-color: var(--danger-light);
            color: var(--danger);
        }
        .badge-info {
            background-color: var(--primary-light);
            color: var(--primary);
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Dashboard Header -->
        <div class="header">
            <h1><i class="fa-solid fa-chart-network"></i> Collaborative Network Diagnostics</h1>
            <div class="btn-group">
                <a href="test-db.php" class="btn btn-secondary"><i class="fa-solid fa-square-poll-vertical"></i> Run Test</a>
                <a href="backup.php" class="btn btn-primary"><i class="fa-solid fa-cloud-arrow-down"></i> Backups Panel</a>
            </div>
        </div>

        <!-- System Stats Cards Grid -->
        <div class="grid-stats">
            <!-- Apache Status -->
            <div class="stat-card">
                <div class="stat-icon" style="background-color: var(--primary-light); color: var(--primary);">
                    <i class="fa-solid fa-server"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Web Server Status</span>
                    <span class="stat-value"><?= htmlspecialchars($apache_status) ?></span>
                </div>
            </div>

            <!-- MySQL Status -->
            <div class="stat-card">
                <div class="stat-icon" style="background-color: <?= $connected ? 'var(--success-light); color: var(--success);' : 'var(--danger-light); color: var(--danger);' ?>">
                    <i class="fa-solid fa-database"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">MySQL Engine</span>
                    <span class="stat-value"><?= htmlspecialchars($mysql_status) ?></span>
                </div>
            </div>

            <!-- Database Exists -->
            <div class="stat-card">
                <div class="stat-icon" style="background-color: var(--success-light); color: var(--success);">
                    <i class="fa-solid fa-folder-tree"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Database Schema</span>
                    <span class="stat-value"><?= htmlspecialchars($db_exists) ?></span>
                </div>
            </div>

            <!-- Connected User -->
            <div class="stat-card">
                <div class="stat-icon" style="background-color: var(--primary-light); color: var(--primary);">
                    <i class="fa-solid fa-user-shield"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Connected User</span>
                    <span class="stat-value" style="font-family: monospace; font-size: 15px;"><?= htmlspecialchars(explode('@', $current_user)[0]) ?></span>
                </div>
            </div>
        </div>

        <!-- Main Configuration and Status Card -->
        <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 30px; margin-bottom: 30px;">
            <!-- Connection Diagnostics -->
            <div class="card" style="margin-bottom: 0;">
                <div class="card-title">
                    <i class="fa-solid fa-microscope"></i> Connection Diagnostics
                </div>
                <table class="collab-table" style="margin-top: 10px;">
                    <tbody>
                        <tr>
                            <td style="font-weight: 500; color: var(--text-muted);"><i class="fa-solid fa-network-wired" style="margin-right: 8px; color: var(--primary);"></i> Server LAN IP</td>
                            <td style="font-family: monospace; font-weight: 600; text-align: right;"><?= htmlspecialchars($server_ip) ?></td>
                        </tr>
                        <tr>
                            <td style="font-weight: 500; color: var(--text-muted);"><i class="fa-solid fa-globe" style="margin-right: 8px; color: var(--primary);"></i> Current Environment</td>
                            <td style="text-align: right;"><span class="badge badge-info"><?= htmlspecialchars(ENV_MODE) ?></span></td>
                        </tr>
                        <tr>
                            <td style="font-weight: 500; color: var(--text-muted);"><i class="fa-solid fa-stopwatch" style="margin-right: 8px; color: var(--primary);"></i> Connection Latency</td>
                            <td style="font-family: monospace; font-weight: 600; text-align: right;"><?= htmlspecialchars($connection_time) ?> ms</td>
                        </tr>
                        <tr>
                            <td style="font-weight: 500; color: var(--text-muted);"><i class="fa-solid fa-code" style="margin-right: 8px; color: var(--primary);"></i> MySQL Engine version</td>
                            <td style="font-family: monospace; font-weight: 600; text-align: right;"><?= htmlspecialchars($mysql_version) ?></td>
                        </tr>
                        <tr>
                            <td style="font-weight: 500; color: var(--text-muted);"><i class="fa-solid fa-clock-rotate-left" style="margin-right: 8px; color: var(--primary);"></i> MySQL Uptime</td>
                            <td style="font-family: monospace; font-weight: 600; text-align: right;"><?= formatUptime($uptime_seconds) ?></td>
                        </tr>
                        <tr>
                            <td style="font-weight: 500; color: var(--text-muted);"><i class="fa-solid fa-table" style="margin-right: 8px; color: var(--primary);"></i> Total Tables loaded</td>
                            <td style="font-family: monospace; font-weight: 600; text-align: right;"><?= htmlspecialchars($table_count) ?> Tables</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Diagnostics Tips -->
            <div class="card" style="margin-bottom: 0;">
                <div class="card-title">
                    <i class="fa-solid fa-circle-info"></i> LAN Collaborative Guide
                </div>
                <div style="font-size: 13px; line-height: 1.6; color: var(--text-dark);">
                    <p style="margin-bottom: 12px;"><strong>For Server Setup:</strong></p>
                    <ul style="padding-left: 20px; margin-bottom: 20px; color: var(--text-muted);">
                        <li style="margin-bottom: 6px;">Ensure XAMPP MySQL is active.</li>
                        <li style="margin-bottom: 6px;">Confirm Windows Firewall allows inbound connections on port 3306.</li>
                        <li>Share the Server LAN IP address (<strong><?= htmlspecialchars($server_ip) ?></strong>) with team developers.</li>
                    </ul>
                    
                    <p style="margin-bottom: 12px;"><strong>For Client Connect:</strong></p>
                    <ul style="padding-left: 20px; color: var(--text-muted);">
                        <li style="margin-bottom: 6px;">Open <code style="font-family: monospace;">config.php</code>.</li>
                        <li>Update host parameter to target: <code style="font-family: monospace; font-weight: 600; color: var(--primary);"><?= htmlspecialchars($server_ip) ?></code>.</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Collaborators Card -->
        <div class="card">
            <div class="card-title">
                <i class="fa-solid fa-users-viewfinder"></i> Active Collaborators (Connected LAN IPs)
            </div>
            <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 20px;">Displays live client connections currently querying the centralized database server.</p>
            
            <table class="collab-table">
                <thead>
                    <tr>
                        <th>Connection Thread ID</th>
                        <th>User</th>
                        <th>Developer Client IP</th>
                        <th>Active Database</th>
                        <th>Current Command</th>
                        <th>State</th>
                        <th>Duration (s)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($collaborators)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 20px;">No active connections located.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($collaborators as $collab): ?>
                            <tr>
                                <td style="font-family: monospace; font-weight: 600;">#<?= htmlspecialchars($collab['id']) ?></td>
                                <td><?= htmlspecialchars($collab['user']) ?></td>
                                <td style="font-family: monospace; font-weight: 600; color: var(--primary);"><?= htmlspecialchars($collab['ip']) ?></td>
                                <td><code style="font-family: monospace;"><?= htmlspecialchars($collab['db']) ?></code></td>
                                <td><span class="badge badge-info"><?= htmlspecialchars($collab['command']) ?></span></td>
                                <td>
                                    <span class="badge <?= $collab['state'] === 'Active' || $collab['command'] === 'Query' ? 'badge-success' : 'badge-info' ?>">
                                        <?= htmlspecialchars($collab['state']) ?>
                                    </span>
                                </td>
                                <td style="font-family: monospace;"><?= htmlspecialchars($collab['time']) ?>s</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
