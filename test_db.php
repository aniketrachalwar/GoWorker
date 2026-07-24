<?php
require_once __DIR__ . '/config/database.php';
echo "WORKER PROFILES:\n";
$stmt = $pdo->query("SELECT w.id, w.user_id, u.full_name FROM worker_profiles w JOIN users u ON w.user_id = u.id");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    print_r($row);
}

echo "\nBOOKINGS:\n";
try {
    $stmt2 = $pdo->query("SELECT * FROM bookings");
    while ($row = $stmt2->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
    }
} catch (Exception $e) {
    echo "No bookings table: " . $e->getMessage() . "\n";
}
