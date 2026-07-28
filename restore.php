<?php
/**
 * GoWorker Database Restore Utility
 * 
 * Safe schema restoration and database backup recovery using pure PHP PDO SQL parsing.
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/Database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$message = $_SESSION['restore_message'] ?? null;
$message_type = $_SESSION['restore_message_type'] ?? 'info';
unset($_SESSION['restore_message']);

// Initialize connection
try {
    $pdo = Database::getInstance()->getConnection();
} catch (Exception $e) {
    $pdo = null;
}

// ----------------------------------------------------
// RESTORE / IMPORT PARSER ENGINE
// ----------------------------------------------------
function executeSqlFile($pdo, $filepath) {
    if (!file_exists($filepath)) {
        return [false, "File not found at $filepath"];
    }

    try {
        $pdo->exec("SET FOREIGN_KEY_CHECKS=0;");
        
        // Open file handler for efficient parsing
        $handle = fopen($filepath, "r");
        if (!$handle) {
            return [false, "Unable to read SQL file."];
        }

        $query = '';
        while (($line = fgets($handle)) !== false) {
            $trimmed = trim($line);
            
            // Skip comments and empty lines
            if (empty($trimmed) || strpos($trimmed, '--') === 0 || strpos($trimmed, '#') === 0) {
                continue;
            }
            
            $query .= $line;
            
            // End of SQL statement reached (marked by a semicolon at the end of line)
            if (substr($trimmed, -1) === ';') {
                $pdo->exec($query);
                $query = '';
            }
        }
        
        fclose($handle);
        $pdo->exec("SET FOREIGN_KEY_CHECKS=1;");
        return [true, "Schema restored successfully."];
    } catch (PDOException $e) {
        $pdo->exec("SET FOREIGN_KEY_CHECKS=1;");
        return [false, "Database execution error during import: " . $e->getMessage()];
    }
}

// Handle Form Submissions
$action = $_GET['action'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'upload_import') {
    if (!$pdo) {
        $_SESSION['restore_message'] = "Cannot connect to database to import.";
        $_SESSION['restore_message_type'] = "danger";
        redirect("restore.php");
    }

    if (isset($_FILES['sql_file']) && $_FILES['sql_file']['error'] === UPLOAD_ERR_OK) {
        $temp_path = $_FILES['sql_file']['tmp_name'];
        $filename = basename($_FILES['sql_file']['name']);
        
        if (pathinfo($filename, PATHINFO_EXTENSION) === 'sql') {
            $result = executeSqlFile($pdo, $temp_path);
            if ($result[0]) {
                $_SESSION['restore_message'] = "Uploaded SQL file successfully executed and database is restored.";
                $_SESSION['restore_message_type'] = "success";
            } else {
                $_SESSION['restore_message'] = "Error importing SQL file: " . $result[1];
                $_SESSION['restore_message_type'] = "danger";
            }
        } else {
            $_SESSION['restore_message'] = "Invalid file type. Only standard .sql files are supported.";
            $_SESSION['restore_message_type'] = "danger";
        }
    $_SESSION['restore_message'] = "File upload failed or no file selected.";
        $_SESSION['restore_message_type'] = "danger";
    }
    redirect("restore.php");
}

if ($action === 'restore_local') {
    if (!$pdo) {
        $_SESSION['restore_message'] = "Cannot connect to database to restore.";
        $_SESSION['restore_message_type'] = "danger";
        redirect("restore.php");
    }

    $file = $_GET['file'] ?? null;
    $filepath = BACKUP_DIR . '/' . basename($file);
    
    if ($file && file_exists($filepath)) {
        $result = executeSqlFile($pdo, $filepath);
        if ($result[0]) {
            $_SESSION['restore_message'] = "Database successfully restored from archive file: $file";
            $_SESSION['restore_message_type'] = "success";
        } else {
            $_SESSION['restore_message'] = "Failed to restore database from backup file: " . $result[1];
            $_SESSION['restore_message_type'] = "danger";
        }
        $_SESSION['restore_message'] = "The selected backup file does not exist.";
        $_SESSION['restore_message_type'] = "danger";
    }
    redirect("restore.php");
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
                'size' => round(filesize($filepath) / 1024, 2),
                'date' => date('Y-m-d H:i:s', filemtime($filepath))
            ];
        }
    }
}

usort($backups, function($a, $b) {
    return strcmp($b['date'], $a['date']);
});

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoWorker - Restore Wizard</title>
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
        .dashboard-header h1 i {
            color: var(--primary);
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
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 8px;
        }
        .form-control-file {
            border: 1.5px dashed var(--border-color);
            padding: 16px;
            border-radius: 8px;
            width: 100%;
            background-color: #f8fafc;
            cursor: pointer;
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
        <!-- Header -->
        <div class="dashboard-header">
            <h1><i class="fa-solid fa-rotate-left"></i> GoWorker Database Restore Wizard</h1>
            <div>
                <a href="db-status.php" class="btn btn-secondary"><i class="fa-solid fa-chart-network"></i> Status Panel</a>
                <a href="backup.php" class="btn btn-secondary"><i class="fa-solid fa-cloud-arrow-down"></i> Backups Panel</a>
            </div>
        </div>

        <!-- Alert box -->
        <?php if ($message): ?>
            <div class="alert alert-<?= htmlspecialchars($message_type) ?>">
                <i class="fa-solid <?= $message_type === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation' ?>"></i>
                <span><?= htmlspecialchars($message) ?></span>
            </div>
        <?php endif; ?>

        <!-- Import File upload form -->
        <div class="card">
            <div class="card-title">
                <i class="fa-solid fa-upload"></i> Upload & Import SQL File
            </div>
            <form action="restore.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="upload_import">
                <div class="form-group">
                    <label for="sql_file">Choose SQL Database File (.sql)</label>
                    <input type="file" id="sql_file" name="sql_file" class="form-control-file" accept=".sql" required>
                </div>
                <button type="submit" class="btn btn-primary" onclick="return confirm('WARNING: Importing SQL file will truncate or re-create schema and overwrite existings records. Proceed?')">
                    <i class="fa-solid fa-file-import"></i> Upload and Import Database
                </button>
            </form>
        </div>

        <!-- Archived backups restore list -->
        <div class="card">
            <div class="card-title">
                <i class="fa-solid fa-folder-tree"></i> Restore from local Backups Archive
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
                                <th>Size</th>
                                <th>Creation Date</th>
                                <th style="text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($backups as $bk): ?>
                                <tr>
                                    <td style="font-weight: 500; font-family: monospace; color: var(--dark-navy);"><?= htmlspecialchars($bk['name']) ?></td>
                                    <td><?= htmlspecialchars($bk['size']) ?> KB</td>
                                    <td style="color: var(--text-muted);"><?= htmlspecialchars($bk['date']) ?></td>
                                    <td style="text-align: right;">
                                        <a href="restore.php?action=restore_local&file=<?= urlencode($bk['name']) ?>" class="btn btn-secondary" style="padding: 6px 12px; font-size: 12px; border-color: var(--primary); color: var(--primary);" onclick="return confirm('WARNING: Restoring will overwrite existing tables and data. Proceed?')">
                                            <i class="fa-solid fa-rotate-left"></i> Restore schema
                                        </a>
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
