<?php
/**
 * GoWorker Database Backup Utility
 * 
 * Generates database backups (schema + data) using pure PHP PDO queries
 * and provides options for manual backups, auto-backups, and live downloads.
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/Database.php';

// Flash message helper
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$message = $_SESSION['backup_message'] ?? null;
$message_type = $_SESSION['backup_message_type'] ?? 'info';
unset($_SESSION['backup_message']);

// Initialize PDO
try {
    $pdo = Database::getInstance()->getConnection();
} catch (Exception $e) {
    $pdo = null;
}

// ----------------------------------------------------
// EXPORT / BACKUP ENGINE FUNCTIONS
// ----------------------------------------------------
function generateDatabaseBackupSql($pdo) {
    if (!$pdo) return false;

    $dbName = DB_NAME;
    $output = "-- GoWorker Database Backup\n";
    $output .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
    $output .= "-- MySQL Server Version: " . $pdo->getAttribute(PDO::ATTR_SERVER_VERSION) . "\n";
    $output .= "-- Database Name: $dbName\n\n";
    $output .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

    // Get all tables
    $tables = [];
    $stmt = $pdo->query("SHOW TABLES");
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        $tables[] = $row[0];
    }

    foreach ($tables as $table) {
        $output .= "-- --------------------------------------------------\n";
        $output .= "-- TABLE STRUCTURE FOR `$table`\n";
        $output .= "-- --------------------------------------------------\n";
        $output .= "DROP TABLE IF EXISTS `$table`;\n";

        // Get table creation statement
        $createStmt = $pdo->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_NUM);
        $output .= $createStmt[1] . ";\n\n";

        // Get table records
        $output .= "-- Dumping data for `$table`\n";
        $dataStmt = $pdo->query("SELECT * FROM `$table`");
        $rowCount = $dataStmt->rowCount();

        if ($rowCount > 0) {
            $output .= "INSERT INTO `$table` VALUES\n";
            $i = 0;
            while ($row = $dataStmt->fetch(PDO::FETCH_NUM)) {
                $output .= "(";
                $values = [];
                foreach ($row as $val) {
                    if ($val === null) {
                        $values[] = "NULL";
                    } elseif (is_numeric($val)) {
                        $values[] = $val;
                    } else {
                        $values[] = $pdo->quote($val);
                    }
                }
                $output .= implode(", ", $values);
                $output .= ")";
                
                $i++;
                if ($i < $rowCount) {
                    $output .= ",\n";
                } else {
                    $output .= ";\n";
                }
            }
        }
        $output .= "\n";
    }

    $output .= "SET FOREIGN_KEY_CHECKS=1;\n";
    return $output;
}

// Handle Form Submissions
$action = $_GET['action'] ?? null;

if ($action === 'create') {
    if (!$pdo) {
        $_SESSION['backup_message'] = "Cannot connect to database to create backup.";
        $_SESSION['backup_message_type'] = "danger";
        header("Location: backup.php");
        exit();
    }

    $sql = generateDatabaseBackupSql($pdo);
    if ($sql) {
        $filename = 'backup_' . DB_NAME . '_' . date('Ymd_His') . '.sql';
        $filepath = BACKUP_DIR . '/' . $filename;
        
        if (!is_dir(BACKUP_DIR)) {
            mkdir(BACKUP_DIR, 0777, true);
        }

        if (file_put_contents($filepath, $sql) !== false) {
            $_SESSION['backup_message'] = "Backup successfully created: $filename";
            $_SESSION['backup_message_type'] = "success";
        } else {
            $_SESSION['backup_message'] = "Failed to write backup file to disk. Check directory permissions.";
            $_SESSION['backup_message_type'] = "danger";
        }
    } else {
        $_SESSION['backup_message'] = "An error occurred while generating backup SQL content.";
        $_SESSION['backup_message_type'] = "danger";
    }
    header("Location: backup.php");
    exit();
}

if ($action === 'download_live') {
    if (!$pdo) {
        die("Database connection failed. Cannot export SQL.");
    }
    
    $sql = generateDatabaseBackupSql($pdo);
    if ($sql) {
        $filename = 'backup_' . DB_NAME . '_' . date('Ymd_His') . '.sql';
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $filename);
        header('Pragma: no-cache');
        header('Expires: 0');
        echo $sql;
        exit();
    } else {
        die("Failed to generate backup.");
    }
}

if ($action === 'download_file') {
    $file = $_GET['file'] ?? null;
    $filepath = BACKUP_DIR . '/' . basename($file);
    if ($file && file_exists($filepath)) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($filepath));
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
        exit();
    } else {
        $_SESSION['backup_message'] = "The requested file does not exist.";
        $_SESSION['backup_message_type'] = "danger";
        header("Location: backup.php");
        exit();
    }
}

if ($action === 'delete') {
    $file = $_GET['file'] ?? null;
    $filepath = BACKUP_DIR . '/' . basename($file);
    if ($file && file_exists($filepath)) {
        if (unlink($filepath)) {
            $_SESSION['backup_message'] = "Backup file deleted successfully.";
            $_SESSION['backup_message_type'] = "success";
        } else {
            $_SESSION['backup_message'] = "Failed to delete backup file.";
            $_SESSION['backup_message_type'] = "danger";
        }
    }
    header("Location: backup.php");
    exit();
}

// Fetch list of files
$backups = [];
if (is_dir(BACKUP_DIR)) {
    $files = scandir(BACKUP_DIR);
    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
            $filepath = BACKUP_DIR . '/' . $file;
            $backups[] = [
                'name' => $file,
                'size' => round(filesize($filepath) / 1024, 2), // in KB
                'date' => date('Y-m-d H:i:s', filemtime($filepath))
            ];
        }
    }
}

// Sort backups by date descending
usort($backups, function($a, $b) {
    return strcmp($b['date'], $a['date']);
});

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoWorker - Backup Manager</title>
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
            --danger: #ef4444;
            --success: #22c55e;
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
            max-width: 900px;
            margin: 0 auto;
        }
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .dashboard-header h1 {
            font-size: 26px;
            font-weight: 700;
            color: var(--dark-navy);
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
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
        .btn-danger {
            background-color: rgba(239, 68, 68, 0.1);
            color: var(--danger);
        }
        .btn-danger:hover {
            background-color: var(--danger);
            color: var(--white);
        }
        .btn-success {
            background-color: var(--success);
            color: var(--white);
        }
        .alert {
            padding: 14px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            border: 1px solid transparent;
        }
        .alert-success {
            background-color: rgba(34, 197, 94, 0.08);
            border-color: rgba(34, 197, 94, 0.15);
            color: var(--success);
        }
        .alert-danger {
            background-color: rgba(239, 68, 68, 0.08);
            border-color: rgba(239, 68, 68, 0.15);
            color: var(--danger);
        }
        .card {
            background-color: var(--white);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
            margin-bottom: 30px;
        }
        .card-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--dark-navy);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 12px;
        }
        .backups-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            margin-top: 15px;
        }
        .backups-table th, .backups-table td {
            padding: 14px 16px;
            font-size: 14px;
            border-bottom: 1px solid var(--border-color);
        }
        .backups-table th {
            color: var(--text-muted);
            font-weight: 600;
        }
        .backups-table tr:last-child td {
            border-bottom: none;
        }
        .action-cell {
            display: flex;
            gap: 8px;
        }
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .badge-live {
            background-color: var(--primary-light);
            color: var(--primary);
        }
        .no-backups {
            text-align: center;
            color: var(--text-muted);
            padding: 30px 0;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header Section -->
        <div class="dashboard-header">
            <h1><i class="fa-solid fa-cloud-arrow-down" style="color: var(--primary);"></i> GoWorker Database Backups</h1>
            <div>
                <a href="db-status.php" class="btn btn-secondary"><i class="fa-solid fa-chart-network"></i> Status Panel</a>
                <a href="restore.php" class="btn btn-secondary"><i class="fa-solid fa-rotate-left"></i> Restore Wizard</a>
            </div>
        </div>

        <!-- Alert Notification Box -->
        <?php if ($message): ?>
            <div class="alert alert-<?= htmlspecialchars($message_type) ?>">
                <i class="fa-solid <?= $message_type === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation' ?>"></i>
                <span><?= htmlspecialchars($message) ?></span>
            </div>
        <?php endif; ?>

        <!-- Backup Generator Actions Card -->
        <div class="card">
            <div class="card-title">
                <i class="fa-solid fa-gears"></i> Backup Generator Controls
            </div>
            <p style="font-size: 14px; color: var(--text-muted); margin-bottom: 24px; line-height: 1.6;">
                Save complete SQL database structural templates and records. The exported scripts contain dynamic table creations, dependencies, primary/foreign indices, and user-seeded testing profiles.
            </p>
            <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                <a href="backup.php?action=create" class="btn btn-primary">
                    <i class="fa-solid fa-file-export"></i> Run Local Server Backup
                </a>
                <a href="backup.php?action=download_live" class="btn btn-success">
                    <i class="fa-solid fa-download"></i> Download Live SQL File
                </a>
            </div>
        </div>

        <!-- Backup Archive List Card -->
        <div class="card" style="padding-bottom: 15px;">
            <div class="card-title">
                <i class="fa-solid fa-folder-open"></i> Backup History List
            </div>
            
            <?php if (empty($backups)): ?>
                <div class="no-backups">
                    <i class="fa-regular fa-folder-open" style="font-size: 32px; display: block; margin-bottom: 12px;"></i>
                    No archived backups located in backups directory yet.
                </div>
            <?php else: ?>
                <div style="overflow-x: auto;">
                    <table class="backups-table">
                        <thead>
                            <tr>
                                <th>Backup Filename</th>
                                <th>Size (KB)</th>
                                <th>Creation Date</th>
                                <th style="text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($backups as $bk): ?>
                                <tr>
                                    <td style="font-weight: 500; font-family: monospace; color: var(--dark-navy);"><?= htmlspecialchars($bk['name']) ?></td>
                                    <td><?= htmlspecialchars($bk['size']) ?> KB</td>
                                    <td style="color: var(--text-muted);"><?= htmlspecialchars($bk['date']) ?></td>
                                    <td style="text-align: right;">
                                        <div class="action-cell" style="justify-content: flex-end;">
                                            <a href="backup.php?action=download_file&file=<?= urlencode($bk['name']) ?>" class="btn btn-secondary" style="padding: 6px 12px; font-size: 12px;" title="Download Backup">
                                                <i class="fa-solid fa-download"></i>
                                            </a>
                                            <a href="restore.php?action=restore_local&file=<?= urlencode($bk['name']) ?>" class="btn btn-secondary" style="padding: 6px 12px; font-size: 12px; border-color: var(--primary); color: var(--primary);" title="Restore this backup" onclick="return confirm('WARNING: Restoring will overwrite existing data. Proceed?')">
                                                <i class="fa-solid fa-rotate-left"></i> Restore
                                            </a>
                                            <a href="backup.php?action=delete&file=<?= urlencode($bk['name']) ?>" class="btn btn-danger" style="padding: 6px 12px; font-size: 12px;" title="Delete Backup" onclick="return confirm('Are you sure you want to delete this backup file?')">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
