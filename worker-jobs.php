<?php
/**
 * GoWorker - Worker Jobs Management Page
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
$bookings = [];

if (isset($pdo)) {
    try {
        $stmt = $pdo->prepare("
            SELECT b.*, u.full_name as customer_name 
            FROM bookings b
            JOIN users u ON b.customer_id = u.id
            WHERE b.worker_id = ?
            ORDER BY b.booking_date DESC, b.time_slot DESC
        ");
        $stmt->execute([$userId]);
        $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching bookings in worker-jobs.php: " . $e->getMessage());
    }
}

// Fallback to professional mockup jobs if database lists are empty
if (empty($bookings)) {
    $bookings = [
        [
            'id' => 201,
            'customer_name' => 'Aniket Verma',
            'description' => 'AC repair and troubleshooting service',
            'booking_date' => date('Y-m-d'),
            'time_slot' => '10:00 AM - 11:00 AM',
            'address' => 'A-402, Shanti Vihar, Pune',
            'total_price' => 350.00,
            'status' => 'confirmed'
        ],
        [
            'id' => 202,
            'customer_name' => 'Sunita Patil',
            'description' => 'Complete home electrical checkup',
            'booking_date' => date('Y-m-d', strtotime('+1 day')),
            'time_slot' => '02:00 PM - 03:00 PM',
            'address' => 'B-12, Green Glen Layout, Pune',
            'total_price' => 500.00,
            'status' => 'pending'
        ],
        [
            'id' => 203,
            'customer_name' => 'Riddhi Katkar',
            'description' => 'Switchboard repairs and installation',
            'booking_date' => date('Y-m-d', strtotime('-2 days')),
            'time_slot' => '11:00 AM - 12:00 PM',
            'address' => 'Flat 5, Rosewood Apartments, Pune',
            'total_price' => 299.00,
            'status' => 'completed'
        ]
    ];
}

// Separate jobs by category status
$pending_jobs = array_filter($bookings, function($job) { return $job['status'] === 'pending'; });
$accepted_jobs = array_filter($bookings, function($job) { return $job['status'] === 'confirmed' && strtotime($job['booking_date']) >= strtotime(date('Y-m-d')); });
$inprogress_jobs = array_filter($bookings, function($job) { return $job['status'] === 'confirmed' && strtotime($job['booking_date']) < strtotime(date('Y-m-d')); });
$completed_jobs = array_filter($bookings, function($job) { return $job['status'] === 'completed'; });

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
                        <a href="worker-jobs.php" class="sidebar-link active">
                            <i class="fa-solid fa-briefcase"></i> Jobs
                        </a>
                    </li>
                    <li>
                        <a href="worker-requests.php" class="sidebar-link">
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
                    <h1 style="margin-bottom: 0.25rem;">My Booked Jobs</h1>
                    <p style="color: var(--text-muted);">Review status categorizations for pending, accepted, ongoing, and finished jobs.</p>
                </div>
            </div>

            <!-- Tabs Grid -->
            <div style="display: grid; grid-template-columns: 1fr; gap: 2rem; margin-top: 1.5rem;">
                
                <!-- Pending Section -->
                <div class="card" style="padding: 24px;">
                    <h3 style="border-bottom: 2px solid #E2E8F0; padding-bottom: 8px; margin-bottom: 16px; color: #3b82f6;"><i class="fa-solid fa-clock"></i> Pending Jobs (<?php echo count($pending_jobs); ?>)</h3>
                    <?php if (empty($pending_jobs)): ?>
                        <p style="color: var(--text-muted); font-size: 14px;">No jobs pending customer action or confirmation.</p>
                    <?php else: ?>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
                            <?php foreach ($pending_jobs as $job): ?>
                                <div class="card" style="border: 1px solid var(--border-color); padding: 16px; box-shadow: none;">
                                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px;">
                                        <span style="font-weight: 700; color: var(--primary); font-size: 13px;">ID: #GW-<?php echo $job['id']; ?></span>
                                        <span style="background: rgba(59, 130, 246, 0.1); color: #3b82f6; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; text-transform: uppercase;">Pending</span>
                                    </div>
                                    <h4 style="margin: 0 0 4px 0; font-size: 15px;"><?php echo e($job['customer_name']); ?></h4>
                                    <p style="margin: 0 0 10px 0; font-size: 13px; color: var(--text-dark); font-weight: 600;"><?php echo e($job['description']); ?></p>
                                    <div style="font-size: 12px; color: var(--text-muted); display: flex; flex-direction: column; gap: 4px;">
                                        <span><i class="fa-regular fa-calendar"></i> <?php echo date('M d, Y', strtotime($job['booking_date'])) . ' • ' . $job['time_slot']; ?></span>
                                        <span><i class="fa-solid fa-location-dot"></i> <?php echo e($job['address']); ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Accepted Section -->
                <div class="card" style="padding: 24px;">
                    <h3 style="border-bottom: 2px solid #E2E8F0; padding-bottom: 8px; margin-bottom: 16px; color: #10B981;"><i class="fa-solid fa-circle-check"></i> Accepted Jobs (<?php echo count($accepted_jobs); ?>)</h3>
                    <?php if (empty($accepted_jobs)): ?>
                        <p style="color: var(--text-muted); font-size: 14px;">No upcoming accepted jobs.</p>
                    <?php else: ?>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
                            <?php foreach ($accepted_jobs as $job): ?>
                                <div class="card" style="border: 1px solid var(--border-color); padding: 16px; box-shadow: none;">
                                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px;">
                                        <span style="font-weight: 700; color: var(--primary); font-size: 13px;">ID: #GW-<?php echo $job['id']; ?></span>
                                        <span style="background: rgba(16, 185, 129, 0.1); color: #10B981; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; text-transform: uppercase;">Accepted</span>
                                    </div>
                                    <h4 style="margin: 0 0 4px 0; font-size: 15px;"><?php echo e($job['customer_name']); ?></h4>
                                    <p style="margin: 0 0 10px 0; font-size: 13px; color: var(--text-dark); font-weight: 600;"><?php echo e($job['description']); ?></p>
                                    <div style="font-size: 12px; color: var(--text-muted); display: flex; flex-direction: column; gap: 4px;">
                                        <span><i class="fa-regular fa-calendar"></i> <?php echo date('M d, Y', strtotime($job['booking_date'])) . ' • ' . $job['time_slot']; ?></span>
                                        <span><i class="fa-solid fa-location-dot"></i> <?php echo e($job['address']); ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- In Progress Section -->
                <div class="card" style="padding: 24px;">
                    <h3 style="border-bottom: 2px solid #E2E8F0; padding-bottom: 8px; margin-bottom: 16px; color: #F59E0B;"><i class="fa-solid fa-spinner fa-spin"></i> In Progress / Ongoing Jobs (<?php echo count($inprogress_jobs); ?>)</h3>
                    <?php if (empty($inprogress_jobs)): ?>
                        <p style="color: var(--text-muted); font-size: 14px;">No active ongoing bookings at this moment.</p>
                    <?php else: ?>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
                            <?php foreach ($inprogress_jobs as $job): ?>
                                <div class="card" style="border: 1px solid var(--border-color); padding: 16px; box-shadow: none;">
                                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px;">
                                        <span style="font-weight: 700; color: var(--primary); font-size: 13px;">ID: #GW-<?php echo $job['id']; ?></span>
                                        <span style="background: rgba(245, 158, 11, 0.1); color: #F59E0B; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; text-transform: uppercase;">In Progress</span>
                                    </div>
                                    <h4 style="margin: 0 0 4px 0; font-size: 15px;"><?php echo e($job['customer_name']); ?></h4>
                                    <p style="margin: 0 0 10px 0; font-size: 13px; color: var(--text-dark); font-weight: 600;"><?php echo e($job['description']); ?></p>
                                    <div style="font-size: 12px; color: var(--text-muted); display: flex; flex-direction: column; gap: 4px;">
                                        <span><i class="fa-regular fa-calendar"></i> <?php echo date('M d, Y', strtotime($job['booking_date'])) . ' • ' . $job['time_slot']; ?></span>
                                        <span><i class="fa-solid fa-location-dot"></i> <?php echo e($job['address']); ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Completed Section -->
                <div class="card" style="padding: 24px;">
                    <h3 style="border-bottom: 2px solid #E2E8F0; padding-bottom: 8px; margin-bottom: 16px; color: #10B981;"><i class="fa-solid fa-circle-check"></i> Completed Jobs (<?php echo count($completed_jobs); ?>)</h3>
                    <?php if (empty($completed_jobs)): ?>
                        <p style="color: var(--text-muted); font-size: 14px;">No completed jobs registered in history.</p>
                    <?php else: ?>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
                            <?php foreach ($completed_jobs as $job): ?>
                                <div class="card" style="border: 1px solid var(--border-color); padding: 16px; box-shadow: none;">
                                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px;">
                                        <span style="font-weight: 700; color: var(--primary); font-size: 13px;">ID: #GW-<?php echo $job['id']; ?></span>
                                        <span style="background: rgba(16, 185, 129, 0.1); color: #10B981; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; text-transform: uppercase;">Completed</span>
                                    </div>
                                    <h4 style="margin: 0 0 4px 0; font-size: 15px;"><?php echo e($job['customer_name']); ?></h4>
                                    <p style="margin: 0 0 10px 0; font-size: 13px; color: var(--text-dark); font-weight: 600;"><?php echo e($job['description']); ?></p>
                                    <div style="font-size: 12px; color: var(--text-muted); display: flex; flex-direction: column; gap: 4px;">
                                        <span><i class="fa-regular fa-calendar"></i> <?php echo date('M d, Y', strtotime($job['booking_date'])) . ' • ' . $job['time_slot']; ?></span>
                                        <span><i class="fa-solid fa-location-dot"></i> <?php echo e($job['address']); ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </main>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
