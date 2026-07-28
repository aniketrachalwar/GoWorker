<?php
/**
 * GoWorker Worker Dashboard
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

// Enforce worker login
requireWorker();

$pending_count = 0;
$completed_count = 0;
$avg_rating = 0.0;
$active_bookings = [];

if (isset($pdo) && isset($_SESSION['user_id'])) {
    $worker_uid = $_SESSION['user_id'];
    try {
        // Fetch stats
        $stmt_pending = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE worker_id = ? AND status = 'pending'");
        $stmt_pending->execute([$worker_uid]);
        $pending_count = intval($stmt_pending->fetchColumn());
        
        // Completed
        $stmt_completed = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE worker_id = ? AND status = 'completed'");
        $stmt_completed->execute([$worker_uid]);
        $completed_count = intval($stmt_completed->fetchColumn());
        
        // Avg rating
        $stmt_rating = $pdo->prepare("SELECT AVG(rating) FROM reviews WHERE worker_id = ?");
        $stmt_rating->execute([$worker_uid]);
        $avg_rating = round(floatval($stmt_rating->fetchColumn() ?: 0.0), 1);
        
        // Fetch active bookings (excluding cancelled/completed ones)
        $stmt_active = $pdo->prepare("
            SELECT b.*, u.full_name as customer_name 
            FROM bookings b
            JOIN users u ON b.customer_id = u.id
            WHERE b.worker_id = ? AND b.status IN ('pending', 'confirmed')
            ORDER BY b.booking_date ASC, b.time_slot ASC
        ");
        $stmt_active->execute([$worker_uid]);
        $active_bookings = $stmt_active->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error querying worker dashboard: " . $e->getMessage());
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<link rel="stylesheet" href="booking-history.css">

<div class="container container-fluid">
    <div class="dashboard-layout">
        <!-- Sidebar Navigation -->
        <aside class="dashboard-sidebar">
            <div class="user-profile-summary">
                <div class="avatar-placeholder" style="background-color: var(--primary-light); color: var(--primary);">
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
                        <a href="worker-dashboard.php" class="sidebar-link active">
                            <i class="fa-solid fa-gauge"></i> Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="worker-jobs.php" class="sidebar-link">
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
        
        <!-- Main Dashboard Content -->
        <main class="dashboard-content">
            <div class="dashboard-header">
                <div>
                    <h1 style="margin-bottom: 0.25rem;">Welcome, <?php echo e($_SESSION['full_name']); ?>!</h1>
                    <p style="color: var(--text-muted);">Manage your services, accept customer bookings, and check task requests.</p>
                </div>
                <span class="btn btn-secondary" style="pointer-events: none; border-color: var(--success); color: var(--success); background-color: var(--success-light);">
                    <i class="fa-solid fa-circle-check"></i> Account Verified
                </span>
            </div>
            
            <!-- Quick Stats Grid -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
                <div class="card" style="padding: 1.5rem; text-align: left;">
                    <div style="font-size: 2rem; color: var(--primary); margin-bottom: 0.5rem;"><i class="fa-solid fa-calendar-day"></i></div>
                    <h4 style="color: var(--text-muted); font-size: 0.9rem; font-weight: 600; text-transform: uppercase;">Pending Bookings</h4>
                    <p style="font-size: 1.75rem; font-weight: 800; color: var(--dark-navy);"><?php echo $pending_count; ?></p>
                </div>
                
                <div class="card" style="padding: 1.5rem; text-align: left;">
                    <div style="font-size: 2rem; color: var(--success); margin-bottom: 0.5rem;"><i class="fa-solid fa-circle-check"></i></div>
                    <h4 style="color: var(--text-muted); font-size: 0.9rem; font-weight: 600; text-transform: uppercase;">Completed Jobs</h4>
                    <p style="font-size: 1.75rem; font-weight: 800; color: var(--dark-navy);"><?php echo $completed_count; ?></p>
                </div>
                
                <div class="card" style="padding: 1.5rem; text-align: left;">
                    <div style="font-size: 2rem; color: var(--warning); margin-bottom: 0.5rem;"><i class="fa-solid fa-star"></i></div>
                    <h4 style="color: var(--text-muted); font-size: 0.9rem; font-weight: 600; text-transform: uppercase;">Average Rating</h4>
                    <p style="font-size: 1.75rem; font-weight: 800; color: var(--dark-navy);"><?php echo $avg_rating; ?> / 5</p>
                </div>
            </div>
            
            <!-- Dashboard Main Cards -->
            <div style="display: grid; grid-template-columns: 1fr; gap: 2rem;">
                <!-- Booking Requests Card -->
                <div class="card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
                        <h3 style="margin-bottom: 0;"><i class="fa-solid fa-clock-rotate-left" style="color: var(--primary); margin-right: 0.5rem;"></i> Active Job Bookings</h3>
                        <a href="profile.php" class="btn btn-outline" style="padding: 0.5rem 1rem; font-size: 0.85rem;"><i class="fa-solid fa-user-pen"></i> Update Profile</a>
                    </div>
                    
                    <?php if (empty($active_bookings)): ?>
                    <div class="text-center" style="padding: 3rem 1.5rem;">
                        <div style="font-size: 3rem; color: var(--text-muted); margin-bottom: 1.5rem;"><i class="fa-solid fa-circle-exclamation"></i></div>
                        <h4 style="margin-bottom: 0.5rem;">No Active Bookings</h4>
                        <p style="color: var(--text-muted); max-width: 400px; margin: 0 auto; font-size: 0.95rem;">You don't have any customer bookings scheduled right now. Make sure your profile details are fully updated to attract bookings.</p>
                    </div>
                    <?php else: ?>
                    <div style="display: grid; grid-template-columns: 1fr; gap: 16px;">
                        <?php foreach ($active_bookings as $booking): ?>
                          <?php
                          $status_class = '';
                          $status_label = '';
                          if ($booking['status'] === 'confirmed') {
                              $status_class = 'status-ongoing';
                              $status_label = 'Confirmed';
                          } else {
                              $status_class = 'status-pending';
                              $status_label = 'Pending';
                          }
                          ?>
                          <div class="booking-item-card" style="border: 1px solid var(--border-color); padding: 20px; border-radius: var(--radius-lg); margin-bottom: 0; background: var(--white); box-shadow: var(--shadow-sm); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; text-align: left;">
                            <div>
                              <h4 style="font-size: 15px; font-weight: 700; margin: 0 0 4px 0; color: var(--dark-navy);">Customer: <?php echo htmlspecialchars($booking['customer_name']); ?> <span style="font-size: 11px; color: var(--primary); font-weight: 600; margin-left: 6px;">ID: #GW-<?php echo $booking['id']; ?></span></h4>
                              <p style="font-size: 13px; color: var(--secondary-text); margin: 0 0 4px 0; font-weight: 600;"><?php echo htmlspecialchars($booking['description']); ?></p>
                              <p style="font-size: 12px; color: var(--secondary-text); margin: 0;"><i class="fa-solid fa-calendar"></i> <?php echo date('F j, Y', strtotime($booking['booking_date'])); ?> • <?php echo htmlspecialchars($booking['time_slot']); ?></p>
                              <p style="font-size: 12px; color: var(--secondary-text); margin: 4px 0 0 0;"><i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($booking['address']); ?></p>
                            </div>
                            <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 8px;">
                              <span class="status-badge <?php echo $status_class; ?>"><?php echo $status_label; ?></span>
                              <a href="worker-jobs.php" class="btn btn-outline" style="padding: 0.4rem 0.8rem; font-size: 0.8rem; border: 1.5px solid var(--primary); color: var(--primary); text-decoration: none;">Manage Jobs</a>
                            </div>
                          </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Job Feed Card -->
                <div class="card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
                        <h3 style="margin-bottom: 0;"><i class="fa-solid fa-rss" style="color: var(--primary); margin-right: 0.5rem;"></i> Public Job Board</h3>
                        <button class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.85rem; pointer-events: none;" disabled>View Feed (Phase 2)</button>
                    </div>
                    
                    <div class="text-center" style="padding: 3rem 1.5rem;">
                        <div style="font-size: 3rem; color: var(--text-muted); margin-bottom: 1.5rem;"><i class="fa-regular fa-lightbulb"></i></div>
                        <h4 style="margin-bottom: 0.5rem;">Job Feed Coming Soon</h4>
                        <p style="color: var(--text-muted); max-width: 400px; margin: 0 auto; font-size: 0.95rem;">In Phase 2, customer job requests/inquiries in your matching category will show up here, enabling you to send direct responses and secure jobs.</p>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
