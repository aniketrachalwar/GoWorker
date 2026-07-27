<?php
/**
 * GoWorker Customer Dashboard
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

// Enforce customer login
requireCustomer();

$pending_count = 0;
$completed_count = 0;
$total_spent = 0.00;
$recent_bookings = [];

if (isset($pdo) && isset($_SESSION['user_id'])) {
    $customer_id = $_SESSION['user_id'];
    try {
        // Fetch stats
        $stmt_stats = $pdo->prepare("
            SELECT 
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_cnt,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_cnt,
                SUM(CASE WHEN status = 'completed' THEN total_price ELSE 0 END) as spent_sum
            FROM bookings 
            WHERE customer_id = ?
        ");
        $stmt_stats->execute([$customer_id]);
        $stats = $stmt_stats->fetch(PDO::FETCH_ASSOC);
        if ($stats) {
            $pending_count = intval($stats['pending_cnt'] ?? 0);
            $completed_count = intval($stats['completed_cnt'] ?? 0);
            $total_spent = floatval($stats['spent_sum'] ?? 0.00);
        }
        
        // Fetch recent bookings (excluding cancelled ones)
        $stmt_recent = $pdo->prepare("
            SELECT b.*, w.id as worker_profile_id, u.full_name as worker_name, c.name as category_name, u.profile_picture as worker_pic
            FROM bookings b
            JOIN worker_profiles w ON b.worker_id = w.user_id
            JOIN users u ON w.user_id = u.id
            JOIN categories c ON w.category_id = c.id
            WHERE b.customer_id = ? AND b.status != 'cancelled'
            ORDER BY b.created_at DESC
            LIMIT 5
        ");
        $stmt_recent->execute([$customer_id]);
        $recent_bookings = $stmt_recent->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error querying customer dashboard stats: " . $e->getMessage());
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
                <div class="avatar-placeholder">
                    <?php 
                    $name_parts = explode(' ', $_SESSION['full_name']);
                    $initials = (count($name_parts) > 1) ? $name_parts[0][0] . $name_parts[1][0] : $name_parts[0][0];
                    echo e(strtoupper($initials)); 
                    ?>
                </div>
                <h3><?php echo e($_SESSION['full_name']); ?></h3>
                <p style="color: var(--text-muted); font-size: 0.9rem;"><i class="fa-solid fa-user"></i> Customer</p>
            </div>
            
            <nav>
                <ul class="sidebar-nav">
                    <li>
                        <a href="customer-dashboard.php" class="sidebar-link active">
                            <i class="fa-solid fa-gauge"></i> Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="find-workers.php" class="sidebar-link">
                            <i class="fa-solid fa-magnifying-glass"></i> Find Workers
                        </a>
                    </li>
                    <li>
                        <a href="customer-bookings.php" class="sidebar-link">
                            <i class="fa-solid fa-calendar-check"></i> My Bookings
                        </a>
                    </li>
                    <li>
                        <a href="customer-chat.php" class="sidebar-link">
                            <i class="fa-solid fa-comments"></i> Messages
                        </a>
                    </li>
                    <li>
                        <a href="customer-notifications.php" class="sidebar-link">
                            <i class="fa-solid fa-bell"></i> Notifications
                        </a>
                    </li>
                    <li>
                        <a href="profile.php" class="sidebar-link">
                            <i class="fa-solid fa-user-gear"></i> Profile Settings
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
                    <p style="color: var(--text-muted);">Manage your service inquiries, active bookings, and hire workers.</p>
                </div>
                <span class="btn btn-secondary" style="pointer-events: none; border-color: var(--success); color: var(--success); background-color: var(--success-light);">
                    <i class="fa-solid fa-circle-check"></i> Account Active
                </span>
            </div>
            
            <!-- Quick Stats Grid -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
                <div class="card" style="padding: 1.5rem; text-align: left;">
                    <div style="font-size: 2rem; color: var(--primary); margin-bottom: 0.5rem;"><i class="fa-solid fa-hourglass-half"></i></div>
                    <h4 style="color: var(--text-muted); font-size: 0.9rem; font-weight: 600; text-transform: uppercase;">Pending Bookings</h4>
                    <p style="font-size: 1.75rem; font-weight: 800; color: var(--dark-navy);"><?php echo $pending_count; ?></p>
                </div>
                
                <div class="card" style="padding: 1.5rem; text-align: left;">
                    <div style="font-size: 2rem; color: var(--success); margin-bottom: 0.5rem;"><i class="fa-solid fa-check-double"></i></div>
                    <h4 style="color: var(--text-muted); font-size: 0.9rem; font-weight: 600; text-transform: uppercase;">Completed Jobs</h4>
                    <p style="font-size: 1.75rem; font-weight: 800; color: var(--dark-navy);"><?php echo $completed_count; ?></p>
                </div>
                
                <div class="card" style="padding: 1.5rem; text-align: left;">
                    <div style="font-size: 2rem; color: var(--primary); margin-bottom: 0.5rem;"><i class="fa-solid fa-wallet"></i></div>
                    <h4 style="color: var(--text-muted); font-size: 0.9rem; font-weight: 600; text-transform: uppercase;">Total Spent</h4>
                    <p style="font-size: 1.75rem; font-weight: 800; color: var(--dark-navy);">₹<?php echo number_format($total_spent, 2); ?></p>
                </div>
            </div>
            
            <!-- Dashboard Main Cards -->
            <div style="display: grid; grid-template-columns: 1fr; gap: 2rem;">
                <!-- Bookings Card -->
                <div class="card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
                        <h3 style="margin-bottom: 0;"><i class="fa-solid fa-calendar-days" style="color: var(--primary); margin-right: 0.5rem;"></i> Recent Bookings</h3>
                        <a href="find-workers.php" class="btn btn-outline" style="padding: 0.5rem 1rem; font-size: 0.85rem;"><i class="fa-solid fa-magnifying-glass"></i> Hire a Worker</a>
                    </div>
                    
                    <?php if (empty($recent_bookings)): ?>
                    <div class="text-center" style="padding: 3rem 1.5rem;">
                        <div style="font-size: 3rem; color: var(--text-muted); margin-bottom: 1.5rem;"><i class="fa-regular fa-folder-open"></i></div>
                        <h4 style="margin-bottom: 0.5rem;">No Bookings Found</h4>
                        <p style="color: var(--text-muted); max-width: 400px; margin: 0 auto 1.5rem auto; font-size: 0.95rem;">You haven't booked any services yet. Find trusted local workers and book your first service today!</p>
                    </div>
                    <?php else: ?>
                    <div style="display: grid; grid-template-columns: 1fr; gap: 16px;">
                        <?php foreach ($recent_bookings as $booking): ?>
                          <?php
                          $worker_profile_id = $booking['worker_profile_id'];
                          $virtual_id = 'GW-W-' . str_pad($worker_profile_id, 4, '0', STR_PAD_LEFT);
                          
                          $status_class = '';
                          $status_label = '';
                          if ($booking['status'] === 'confirmed') {
                              $status_class = 'status-ongoing';
                              $status_label = 'Ongoing';
                          } else if ($booking['status'] === 'completed') {
                              $status_class = 'status-completed';
                              $status_label = 'Completed';
                          } else if ($booking['status'] === 'pending') {
                              $status_class = 'status-pending';
                              $status_label = 'Pending';
                          } else {
                              $status_class = 'status-cancelled';
                              $status_label = 'Cancelled';
                          }
                          
                          $worker_img = $booking['worker_pic'];
                          if (empty($worker_img)) {
                              if (strpos($booking['worker_name'], 'Sohan') !== false) {
                                  $worker_img = 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&fit=crop';
                              } else {
                                  $worker_img = 'https://images.unsplash.com/photo-1540569014015-19a7be504e3a?w=100&fit=crop';
                              }
                          }
                          ?>
                          <div class="booking-item-card" style="border: 1px solid var(--border-color); padding: 20px; border-radius: var(--radius-lg); margin-bottom: 0; background: var(--white); box-shadow: var(--shadow-sm); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; text-align: left;">
                            <div style="display: flex; gap: 16px; align-items: center;">
                              <img src="<?php echo htmlspecialchars($worker_img); ?>" alt="<?php echo htmlspecialchars($booking['worker_name']); ?>" style="width: 48px; height: 48px; border-radius: 50%; object-fit: cover;">
                              <div>
                                <h4 style="font-size: 15px; font-weight: 700; margin: 0 0 4px 0; color: var(--dark-navy);"><?php echo htmlspecialchars($booking['worker_name']); ?> <span class="virtual-id-badge" style="font-size: 10px; background: var(--primary-light); color: var(--primary); padding: 1px 5px; border-radius: 4px; font-weight: 600; margin-left: 6px; vertical-align: middle;"><?php echo $virtual_id; ?></span></h4>
                                <p style="font-size: 13px; color: var(--secondary-text); margin: 0 0 4px 0;"><?php echo htmlspecialchars($booking['description'] ? $booking['description'] : ($booking['category_name'] . ' Services')); ?></p>
                                <p style="font-size: 12px; color: var(--secondary-text); margin: 0;"><i class="fa-solid fa-calendar"></i> <?php echo date('F j, Y', strtotime($booking['booking_date'])); ?> • <?php echo htmlspecialchars($booking['time_slot']); ?></p>
                              </div>
                            </div>
                            <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 8px;">
                              <span class="status-badge <?php echo $status_class; ?>"><?php echo $status_label; ?></span>
                              <a href="customer-bookings.php" class="btn btn-outline" style="padding: 0.4rem 0.8rem; font-size: 0.8rem; border: 1.5px solid var(--primary); color: var(--primary); text-decoration: none;">View Details</a>
                            </div>
                          </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Requirement Inquiry Card -->
                <div class="card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
                        <h3 style="margin-bottom: 0;"><i class="fa-solid fa-clipboard-list" style="color: var(--primary); margin-right: 0.5rem;"></i> Posted Requirements</h3>
                        <button class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.85rem; pointer-events: none;" disabled><i class="fa-solid fa-plus"></i> Post Requirement (Phase 2)</button>
                    </div>
                    
                    <div class="text-center" style="padding: 3rem 1.5rem;">
                        <div style="font-size: 3rem; color: var(--text-muted); margin-bottom: 1.5rem;"><i class="fa-regular fa-clipboard"></i></div>
                        <h4 style="margin-bottom: 0.5rem;">No Posted Requirements</h4>
                        <p style="color: var(--text-muted); max-width: 400px; margin: 0 auto; font-size: 0.95rem;">Need something specific? Post a job requirement and let local workers send you inquiries and rates (Coming in Phase 2!).</p>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
