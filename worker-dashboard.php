<?php
/**
 * GoWorker Worker Dashboard
 */
require_once __DIR__ . '/includes/auth.php';

// Enforce worker login
requireWorker();

require_once __DIR__ . '/includes/header.php';
?>

<div class="container">
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
                        <a href="become-worker.php" class="sidebar-link">
                            <i class="fa-solid fa-briefcase"></i> Service Settings
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
                    <p style="font-size: 1.75rem; font-weight: 800; color: var(--dark-navy);">0</p>
                </div>
                
                <div class="card" style="padding: 1.5rem; text-align: left;">
                    <div style="font-size: 2rem; color: var(--success); margin-bottom: 0.5rem;"><i class="fa-solid fa-circle-check"></i></div>
                    <h4 style="color: var(--text-muted); font-size: 0.9rem; font-weight: 600; text-transform: uppercase;">Completed Jobs</h4>
                    <p style="font-size: 1.75rem; font-weight: 800; color: var(--dark-navy);">0</p>
                </div>
                
                <div class="card" style="padding: 1.5rem; text-align: left;">
                    <div style="font-size: 2rem; color: var(--warning); margin-bottom: 0.5rem;"><i class="fa-solid fa-star"></i></div>
                    <h4 style="color: var(--text-muted); font-size: 0.9rem; font-weight: 600; text-transform: uppercase;">Average Rating</h4>
                    <p style="font-size: 1.75rem; font-weight: 800; color: var(--dark-navy);">0.0 / 5</p>
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
                    
                    <div class="text-center" style="padding: 3rem 1.5rem;">
                        <div style="font-size: 3rem; color: var(--text-muted); margin-bottom: 1.5rem;"><i class="fa-solid fa-circle-exclamation"></i></div>
                        <h4 style="margin-bottom: 0.5rem;">No Active Bookings</h4>
                        <p style="color: var(--text-muted); max-width: 400px; margin: 0 auto; font-size: 0.95rem;">You don't have any customer bookings scheduled right now. Make sure your profile details are fully updated to attract bookings.</p>
                    </div>
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
