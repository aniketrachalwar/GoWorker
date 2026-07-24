<?php
/**
 * GoWorker Customer Dashboard
 */
require_once __DIR__ . '/includes/auth.php';

// Enforce customer login
requireCustomer();

require_once __DIR__ . '/includes/header.php';
?>

<div class="container">
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
                    <p style="font-size: 1.75rem; font-weight: 800; color: var(--dark-navy);">0</p>
                </div>
                
                <div class="card" style="padding: 1.5rem; text-align: left;">
                    <div style="font-size: 2rem; color: var(--success); margin-bottom: 0.5rem;"><i class="fa-solid fa-check-double"></i></div>
                    <h4 style="color: var(--text-muted); font-size: 0.9rem; font-weight: 600; text-transform: uppercase;">Completed Jobs</h4>
                    <p style="font-size: 1.75rem; font-weight: 800; color: var(--dark-navy);">0</p>
                </div>
                
                <div class="card" style="padding: 1.5rem; text-align: left;">
                    <div style="font-size: 2rem; color: var(--primary); margin-bottom: 0.5rem;"><i class="fa-solid fa-wallet"></i></div>
                    <h4 style="color: var(--text-muted); font-size: 0.9rem; font-weight: 600; text-transform: uppercase;">Total Spent</h4>
                    <p style="font-size: 1.75rem; font-weight: 800; color: var(--dark-navy);">$0.00</p>
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
                    
                    <div class="text-center" style="padding: 3rem 1.5rem;">
                        <div style="font-size: 3rem; color: var(--text-muted); margin-bottom: 1.5rem;"><i class="fa-regular fa-folder-open"></i></div>
                        <h4 style="margin-bottom: 0.5rem;">No Bookings Found</h4>
                        <p style="color: var(--text-muted); max-width: 400px; margin: 0 auto 1.5rem auto; font-size: 0.95rem;">You haven't booked any services yet. Find trusted local workers and book your first service today!</p>
                    </div>
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
