<?php
/**
 * GoWorker API - Worker Availability Status Handler
 */
header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$action = $_REQUEST['action'] ?? 'get';
$worker_id = intval($_REQUEST['worker_id'] ?? 0);

if ($action === 'get') {
    if (isset($pdo)) {
        try {
            if ($worker_id > 0) {
                $stmt = $pdo->prepare("SELECT * FROM worker_availability WHERE worker_id = ?");
                $stmt->execute([$worker_id]);
                $data = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$data) {
                    $data = ['worker_id' => $worker_id, 'is_online' => 1, 'status_text' => 'Available Now'];
                }
                echo json_encode(['status' => 'success', 'availability' => $data]);
            } else {
                $stmt = $pdo->query("SELECT * FROM worker_availability");
                $all = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['status' => 'success', 'all' => $all]);
            }
            exit;
        } catch (PDOException $e) {
            echo json_encode(['status' => 'success', 'availability' => ['is_online' => 1, 'status_text' => 'Available Now']]);
            exit;
        }
    } else {
        echo json_encode(['status' => 'success', 'availability' => ['is_online' => 1, 'status_text' => 'Available Now']]);
        exit;
    }
}

if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'] ?? 0;
    $target_worker = $worker_id > 0 ? $worker_id : $user_id;

    if (!$target_worker) {
        echo json_encode(['status' => 'error', 'message' => 'Worker ID required']);
        exit;
    }

    $is_online = intval($_POST['is_online'] ?? 1);
    $status_text = trim($_POST['status_text'] ?? ($is_online ? 'Available Now' : 'Offline'));

    if (isset($pdo)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO worker_availability (worker_id, is_online, status_text) 
                                   VALUES (?, ?, ?) 
                                   ON DUPLICATE KEY UPDATE is_online = VALUES(is_online), status_text = VALUES(status_text)");
            $stmt->execute([$target_worker, $is_online, $status_text]);
            echo json_encode(['status' => 'success', 'is_online' => $is_online, 'status_text' => $status_text]);
            exit;
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Database error']);
            exit;
        }
    }
}

echo json_encode(['status' => 'error', 'message' => 'Invalid endpoint']);
