<?php
/**
 * GoWorker API - Favorites Handler
 * Manages saving/removing favorite workers for users
 */
header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$response = ['status' => 'error', 'message' => 'Invalid request'];

$user_id = $_SESSION['user_id'] ?? 0;
$action = $_REQUEST['action'] ?? '';

if ($action === 'list') {
    if (!$user_id) {
        echo json_encode(['status' => 'success', 'favorites' => []]);
        exit;
    }
    if (isset($pdo)) {
        try {
            $stmt = $pdo->prepare("SELECT worker_id FROM favorites WHERE customer_id = ?");
            $stmt->execute([$user_id]);
            $favs = $stmt->fetchAll(PDO::FETCH_COLUMN);
            echo json_encode(['status' => 'success', 'favorites' => $favs]);
            exit;
        } catch (PDOException $e) {
            echo json_encode(['status' => 'success', 'favorites' => []]);
            exit;
        }
    } else {
        echo json_encode(['status' => 'success', 'favorites' => []]);
        exit;
    }
}

if ($action === 'toggle' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $worker_id = intval($_POST['worker_id'] ?? 0);
    if (!$worker_id) {
        echo json_encode(['status' => 'error', 'message' => 'Worker ID required']);
        exit;
    }

    if (!$user_id) {
        // Guest user fallback - success status for client-side localStorage handling
        echo json_encode(['status' => 'success', 'is_favorite' => true, 'guest' => true]);
        exit;
    }

    if (isset($pdo)) {
        try {
            // Check if already favorited
            $stmt = $pdo->prepare("SELECT id FROM favorites WHERE customer_id = ? AND worker_id = ?");
            $stmt->execute([$user_id, $worker_id]);
            $exists = $stmt->fetch();

            if ($exists) {
                // Remove
                $del = $pdo->prepare("DELETE FROM favorites WHERE customer_id = ? AND worker_id = ?");
                $del->execute([$user_id, $worker_id]);
                echo json_encode(['status' => 'success', 'is_favorite' => false, 'message' => 'Removed from favorites']);
            } else {
                // Insert
                $ins = $pdo->prepare("INSERT INTO favorites (customer_id, worker_id) VALUES (?, ?)");
                $ins->execute([$user_id, $worker_id]);
                echo json_encode(['status' => 'success', 'is_favorite' => true, 'message' => 'Saved to favorites']);
            }
            exit;
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Database error']);
            exit;
        }
    }
}

echo json_encode($response);
