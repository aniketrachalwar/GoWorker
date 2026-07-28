<?php
/**
 * GoWorker - Cancel Booking API
 */
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/includes/auth.php';

header('Content-Type: application/json');

// Enforce customer login
if (!is_logged_in() || current_user_type() !== 'customer') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit();
}

// Read JSON input
$input = json_decode(file_get_contents('php://input'), true);
$booking_id = intval($input['booking_id'] ?? 0);

if ($booking_id <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid booking ID']);
    exit();
}

$customer_id = $_SESSION['user_id'];

if (isset($pdo)) {
    try {
        // Retrieve booking details to verify ownership and state
        $stmt_check = $pdo->prepare("SELECT customer_id, status FROM bookings WHERE id = ?");
        $stmt_check->execute([$booking_id]);
        $booking = $stmt_check->fetch(PDO::FETCH_ASSOC);
        
        if (!$booking) {
            echo json_encode(['success' => false, 'error' => 'Booking not found']);
            exit();
        }
        
        if (intval($booking['customer_id']) !== $customer_id) {
            echo json_encode(['success' => false, 'error' => 'Permission denied: You do not own this booking']);
            exit();
        }
        
        if ($booking['status'] === 'cancelled') {
            echo json_encode(['success' => false, 'error' => 'Booking is already cancelled']);
            exit();
        }
        
        if ($booking['status'] === 'completed') {
            echo json_encode(['success' => false, 'error' => 'Completed bookings cannot be cancelled']);
            exit();
        }
        
        // Perform database status update
        $stmt_update = $pdo->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ? AND customer_id = ?");
        $stmt_update->execute([$booking_id, $customer_id]);
        
        if ($stmt_update->rowCount() > 0) {
            echo json_encode(['success' => true]);
            exit();
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to update booking status']);
            exit();
        }
    } catch (PDOException $e) {
        error_log("Database error in api/cancel-booking.php: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => 'Database operation error occurred']);
        exit();
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Database connection unavailable']);
    exit();
}
