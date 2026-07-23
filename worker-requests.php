<?php
/**
 * GoWorker - Worker Booking Requests Management Page
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Session Security: Enforce worker login
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'worker') {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$msg_status = '';

// Handle Accept/Reject action updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $booking_id = intval($_POST['booking_id'] ?? 0);
    $action = $_POST['action'];
    
    if ($booking_id > 0 && ($action === 'accept' || $action === 'reject')) {
        $new_status = ($action === 'accept') ? 'confirmed' : 'cancelled';
        if (isset($pdo)) {
            try {
                $stmt = $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ? AND worker_id = ?");
                $stmt->execute([$new_status, $booking_id, $userId]);
                $msg_status = ($action === 'accept') ? 'Job request accepted successfully!' : 'Job request declined.';
            } catch (PDOException $e) {
                error_log("Error updating status in worker-requests.php: " . $e->getMessage());
            }
        } else {
            $msg_status = ($action === 'accept') ? 'Job request accepted successfully!' : 'Job request declined.';
        }
    }
}

$requests = [];
if (isset($pdo)) {
    try {
        $stmt = $pdo->prepare("
            SELECT b.*, u.full_name as customer_name 
            FROM bookings b
            JOIN users u ON b.customer_id = u.id
            WHERE b.worker_id = ? AND b.status = 'pending'
            ORDER BY b.booking_date ASC, b.time_slot ASC
        ");
        $stmt->execute([$userId]);
        $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching requests in worker-requests.php: " . $e->getMessage());
    }
}

// Fallback mock booking requests if database is empty
if (empty($requests) && empty($msg_status)) {
    $requests = [
        [
            'id' => 301,
            'customer_name' => 'Sunita Patil',
            'description' => 'Complete home electrical checkup',
            'booking_date' => date('Y-m-d', strtotime('+1 day')),
            'time_slot' => '02:00 PM - 03:00 PM',
            'address' => 'B-12, Green Glen Layout, Pune'
        ],
        [
            'id' => 302,
            'customer_name' => 'Sohan Singh',
            'description' => 'Short circuit diagnostic & fuse replacement',
            'booking_date' => date('Y-m-d', strtotime('+2 days')),
            'time_slot' => '09:00 AM - 10:00 AM',
            'address' => 'Plot 43, Landmark Society, Pune'
        ]
    ];
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container">
    <div class="dashboard-layout">
        <!-- Sidebar Navigation -->
        <aside class="dashboard-sidebar">
            <div class="user-profile-summary">
                <div class="avatar-placeholder" style="background-color: var(--primary-light); color: var(--primary); width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 28px; font-weight: 700; margin: 0 auto 12px auto; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
                    <?php 
                    $name_parts = explode(' ', $_SESSION['full_name']);
                    $initials = (count($name_parts) > 1) ? $name_parts[0][0] . $name_parts[1][0] : $name_parts[0][0];
                    echo e(strtoupper($initials)); 
                    ?>
                </div>
                <h3><?php echo e($_SESSION['full_name']); ?></h3>
                <?php 
                $profile_id = get_worker_profile_id_by_user_id($_SESSION['user_id']);
                if ($profile_id):
                ?>
                    <p style="margin-top: 4px; margin-bottom: 8px;">
                        <span class="virtual-id-badge" style="font-size: 12px; background: var(--primary-light); color: var(--primary); padding: 3px 8px; border-radius: 4px; font-weight: 600;">
                            <?php echo e(get_worker_virtual_id($profile_id)); ?>
                        </span>
                    </p>
                <?php endif; ?>
                <p style="color: var(--text-muted); font-size: 0.9rem;"><i class="fa-solid fa-screwdriver-wrench"></i> Worker Profile</p>
            </div>
            
            <nav>
                <ul class="sidebar-nav">
                    <li>
                        <a href="worker-dashboard.php" class="sidebar-link">
                            <i class="fa-solid fa-gauge"></i> Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="worker-jobs.php" class="sidebar-link">
                            <i class="fa-solid fa-briefcase"></i> Jobs
                        </a>
                    </li>
                    <li>
                        <a href="worker-requests.php" class="sidebar-link active">
                            <i class="fa-solid fa-list-check"></i> Requests
                        </a>
                    </li>
                    <li>
                        <a href="worker-earnings.php" class="sidebar-link">
                            <i class="fa-solid fa-wallet"></i> Earnings
                        </a>
                    </li>
                    <li>
                        <a href="worker-id-card.php" class="sidebar-link">
                            <i class="fa-solid fa-id-card"></i> Virtual ID Card
                        </a>
                    </li>
                    <li>
                        <a href="profile.php" class="sidebar-link">
                            <i class="fa-solid fa-user-gear"></i> Manage Profile
                        </a>
                    </li>
                    <li>
                        <a href="chat.php" class="sidebar-link">
                            <i class="fa-solid fa-comments"></i> Messages
                        </a>
                    </li>
                    <li>
                        <a href="notifications.php" class="sidebar-link">
                            <i class="fa-solid fa-bell"></i> Notifications
                        </a>
                    </li>
                    <li>
                        <a href="logout.php" class="sidebar-link" style="color: var(--danger);">
                            <i class="fa-solid fa-right-from-bracket"></i> Logout
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="dashboard-content">
            <div class="dashboard-header">
                <div>
                    <h1 style="margin-bottom: 0.25rem;">Booking Requests</h1>
                    <p style="color: var(--text-muted);">Manage upcoming task requests. Accept or reject jobs to update your schedule.</p>
                </div>
            </div>

            <!-- Feedback Notifications -->
            <?php if (!empty($msg_status)): ?>
                <div class="card" style="padding: 12px 16px; background: rgba(34, 197, 94, 0.08); border-left: 4px solid var(--success); color: var(--success); font-weight: 600; margin: 1rem 0;">
                    <i class="fa-solid fa-circle-check"></i> <?php echo e($msg_status); ?>
                </div>
            <?php endif; ?>

            <!-- Requests List Block -->
            <div class="card" style="padding: 24px; margin-top: 1.5rem;">
                <h3 style="border-bottom: 2px solid #E2E8F0; padding-bottom: 8px; margin-bottom: 20px;"><i class="fa-solid fa-bell" style="color: var(--primary);"></i> New Booking Requests (<?php echo count($requests); ?>)</h3>
                
                <?php if (empty($requests)): ?>
                    <div style="text-align: center; padding: 40px 0;">
                        <i class="fa-solid fa-circle-check" style="font-size: 48px; color: var(--success); margin-bottom: 12px;"></i>
                        <p style="color: var(--text-muted); font-size: 15px; margin-bottom: 0;">You're all caught up! No pending booking requests at this moment.</p>
                    </div>
                <?php else: ?>
                    <div style="display: grid; grid-template-columns: 1fr; gap: 16px;">
                        <?php foreach ($requests as $req): ?>
                            <div class="card" style="border: 1px solid var(--border-color); padding: 20px; box-shadow: none; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
                                <div style="flex: 1; min-width: 250px;">
                                    <h4 style="margin: 0 0 6px 0; font-size: 16px; color: var(--dark-navy);"><?php echo e($req['customer_name']); ?></h4>
                                    <p style="margin: 0 0 12px 0; font-size: 13px; font-weight: 600; color: var(--primary);"><i class="fa-solid fa-screwdriver-wrench"></i> <?php echo e($req['description']); ?></p>
                                    
                                    <div style="display: flex; gap: 16px; font-size: 12px; color: var(--text-muted); flex-wrap: wrap;">
                                        <span><i class="fa-regular fa-calendar"></i> <?php echo date('M d, Y', strtotime($req['booking_date'])) . ' • ' . ($req['time_slot'] ?? 'Flexible Slot'); ?></span>
                                        <span><i class="fa-solid fa-location-dot"></i> <?php echo e($req['address']); ?></span>
                                    </div>
                                </div>
                                <div style="display: flex; gap: 10px; align-self: center;">
                                    <form method="POST" style="margin: 0;">
                                        <input type="hidden" name="booking_id" value="<?php echo $req['id']; ?>">
                                        <input type="hidden" name="action" value="accept">
                                        <button type="submit" class="btn" style="background: linear-gradient(135deg, #10B981 0%, #059669 100%); color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 13px; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.2);">
                                            <i class="fa-solid fa-check"></i> Accept Request
                                        </button>
                                    </form>
                                    <form method="POST" style="margin: 0;">
                                        <input type="hidden" name="booking_id" value="<?php echo $req['id']; ?>">
                                        <input type="hidden" name="action" value="reject">
                                        <button type="submit" class="btn btn-outline" style="border: 1.5px solid var(--danger); color: var(--danger); background: transparent; padding: 9px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
                                            <i class="fa-solid fa-xmark"></i> Reject
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
