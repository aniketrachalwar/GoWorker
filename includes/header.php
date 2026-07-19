<?php
/**
 * Shared Header
 */
require_once __DIR__ . '/functions.php';

// Determine the current page basename to apply the 'active' class to nav links
$current_page = basename($_SERVER['SCRIPT_NAME']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoWorker - Trusted Local Services</title>
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts: Outfit & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Global CSS -->
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<header>
    <div class="container nav-container">
        <!-- Logo -->
        <a href="index.php" class="logo">
            <i class="fa-solid fa-briefcase"></i> Go<span>Worker</span>
        </a>
        
        <!-- Navigation Menu -->
        <ul class="nav-menu" id="nav-menu">
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if ($_SESSION['user_type'] === 'customer'): ?>
                    <!-- Logged-in Customer -->
                    <li><a href="index.php" class="nav-link <?php echo $current_page == 'index.php' ? 'active' : ''; ?>">Home</a></li>
                    <li><a href="find-workers.php" class="nav-link <?php echo $current_page == 'find-workers.php' ? 'active' : ''; ?>">Find Workers</a></li>
                    <li><a href="booking.php" class="nav-link <?php echo $current_page == 'booking.php' ? 'active' : ''; ?>">My Bookings</a></li>
                    <li><a href="profile.php" class="nav-link <?php echo $current_page == 'profile.php' ? 'active' : ''; ?>">Profile</a></li>
                <?php else: ?>
                    <!-- Logged-in Worker -->
                    <li><a href="index.php" class="nav-link <?php echo $current_page == 'index.php' ? 'active' : ''; ?>">Home</a></li>
                    <li><a href="worker-dashboard.php" class="nav-link <?php echo $current_page == 'worker-dashboard.php' ? 'active' : ''; ?>">Dashboard</a></li>
                    <li><a href="profile.php" class="nav-link <?php echo $current_page == 'profile.php' ? 'active' : ''; ?>">Profile</a></li>
                <?php endif; ?>
            <?php else: ?>
                <!-- Guest Visitors -->
                <li><a href="index.php" class="nav-link <?php echo $current_page == 'index.php' ? 'active' : ''; ?>">Home</a></li>
                <li><a href="find-workers.php" class="nav-link <?php echo $current_page == 'find-workers.php' ? 'active' : ''; ?>">Find Workers</a></li>
                <li><a href="become-worker.php" class="nav-link <?php echo $current_page == 'become-worker.php' ? 'active' : ''; ?>">Become a Worker</a></li>
                <li><a href="index.php#how-it-works" class="nav-link">How It Works</a></li>
            <?php endif; ?>
        </ul>
        
        <!-- Actions (Theme, Login, Signup) -->
        <div class="nav-actions">
            <!-- Theme Toggle -->
            <button class="theme-btn" id="theme-toggle" title="Toggle Theme" aria-label="Toggle Theme">
                <i class="fa-solid fa-moon"></i>
            </button>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <!-- Logged in details & Logout -->
                <span class="user-greeting" style="display: none; font-size: 0.9rem; font-weight:600; color: var(--text-dark); margin-right: 0.5rem;">
                    Hi, <?php echo e($_SESSION['full_name']); ?>
                </span>
                <?php if ($_SESSION['user_type'] === 'customer'): ?>
                    <a href="customer-dashboard.php" class="btn btn-secondary btn-sm" style="padding: 0.5rem 1rem;"><i class="fa-solid fa-gauge"></i> Dashboard</a>
                <?php else: ?>
                    <a href="worker-dashboard.php" class="btn btn-secondary btn-sm" style="padding: 0.5rem 1rem;"><i class="fa-solid fa-gauge"></i> Dashboard</a>
                <?php endif; ?>
                <a href="logout.php" class="btn btn-primary" style="padding: 0.5rem 1rem;"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
            <?php else: ?>
                <!-- Visitor guest buttons -->
                <a href="login.php" class="btn btn-secondary"><i class="fa-solid fa-right-to-bracket"></i> Login</a>
                <a href="signup.php" class="btn btn-primary"><i class="fa-solid fa-user-plus"></i> Sign Up</a>
            <?php endif; ?>
            
            <!-- Mobile Menu Button -->
            <button class="menu-btn" id="menu-toggle" aria-label="Toggle Mobile Menu">
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>
    </div>
</header>

<!-- Alert Banner Section -->
<div class="container" style="margin-top: 1.5rem; margin-bottom: -1.5rem;">
    <?php
    $flashes = flash();
    if ($flashes):
        foreach ($flashes as $type => $msg):
            $alert_class = ($type === 'success') ? 'alert-success' : 'alert-danger';
            $alert_icon = ($type === 'success') ? 'fa-circle-check' : 'fa-circle-exclamation';
    ?>
            <div class="alert <?php echo $alert_class; ?>">
                <i class="fa-solid <?php echo $alert_icon; ?>"></i>
                <span><?php echo e($msg); ?></span>
            </div>
    <?php
        endforeach;
    endif;
    ?>
</div>
