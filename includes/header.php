<?php
/**
 * Shared Header with language and routing updates
 */
require_once __DIR__ . '/functions.php';

// Handle language switch via query parameter and session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_GET['lang'])) {
    $allowed_langs = ['en', 'hi', 'mr', 'gu', 'ta', 'kn', 'te', 'bho', 'bn'];
    $selected_lang = $_GET['lang'];
    if (in_array($selected_lang, $allowed_langs)) {
        $_SESSION['lang'] = $selected_lang;
    }
    // Redirect to the clean page URL to keep the browser address bar tidy
    $clean_url = strtok($_SERVER["REQUEST_URI"], '?');
    $params = $_GET;
    unset($params['lang']);
    if (!empty($params)) {
        $clean_url .= '?' . http_build_query($params);
    }
    header("Location: " . $clean_url);
    exit();
}

$current_lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'en';

$lang_details = [
    'en' => ['name' => 'English', 'flag' => '🇬🇧'],
    'hi' => ['name' => 'Hindi', 'flag' => '🇮🇳'],
    'mr' => ['name' => 'Marathi', 'flag' => '🇮🇳'],
    'gu' => ['name' => 'Gujarati', 'flag' => '🇮🇳'],
    'ta' => ['name' => 'Tamil', 'flag' => '🇮🇳'],
    'kn' => ['name' => 'Kannada', 'flag' => '🇮🇳'],
    'te' => ['name' => 'Telugu', 'flag' => '🇮🇳'],
    'bho' => ['name' => 'Bhojpuri', 'flag' => '🇮🇳'],
    'bn' => ['name' => 'Bengali', 'flag' => '🇮🇳']
];

$active_lang = isset($lang_details[$current_lang]) ? $lang_details[$current_lang] : $lang_details['en'];

// Helper to keep other query parameters intact during language switch
function get_lang_switch_url($lang) {
    $params = $_GET;
    $params['lang'] = $lang;
    return strtok($_SERVER["REQUEST_URI"], '?') . '?' . http_build_query($params);
}

// Determine the current page basename to apply the 'active' class to nav links
$current_page = basename($_SERVER['SCRIPT_NAME']);
?>
<!DOCTYPE html>
<html lang="<?php echo e($current_lang); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoWorker - Trusted Local Services</title>
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Global CSS -->
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<header class="main-header">
    <div class="nav-container-premium">
        <!-- Logo Section -->
        <a href="index.php" class="logo-container-premium">
            <img src="images/logo_icon.png" alt="GoWorker Logo" class="logo-icon-premium">
            <div class="logo-text-group-premium">
                <span class="logo-brand-name-premium">GoWorker</span>
                <span class="logo-tagline-premium"><?php echo e(__('tagline')); ?></span>
            </div>
        </a>
        
        <!-- Navigation Menu Tabs -->
        <ul class="nav-menu-premium" id="nav-menu">
            <li><a href="index.php" class="nav-link-premium <?php echo ($current_page === 'index.php' || $current_page === '') ? 'active' : ''; ?>"><?php echo e(__('home')); ?></a></li>
            <li><a href="find-workers.php" class="nav-link-premium <?php echo $current_page === 'find-workers.php' ? 'active' : ''; ?>"><?php echo e(__('find_workers')); ?></a></li>
            <li><a href="become-worker.php" class="nav-link-premium <?php echo $current_page === 'become-worker.php' ? 'active' : ''; ?>"><?php echo e(__('become_worker')); ?></a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if ($_SESSION['user_type'] === 'customer'): ?>
                    <li><a href="booking.php" class="nav-link-premium <?php echo $current_page === 'booking.php' ? 'active' : ''; ?>"><?php echo e(__('my_bookings')); ?></a></li>
                    <li><a href="customer-dashboard.php" class="nav-link-premium <?php echo $current_page === 'customer-dashboard.php' ? 'active' : ''; ?>"><?php echo e(__('dashboard')); ?></a></li>
                <?php else: ?>
                    <li><a href="worker-dashboard.php" class="nav-link-premium <?php echo $current_page === 'worker-dashboard.php' ? 'active' : ''; ?>"><?php echo e(__('dashboard')); ?></a></li>
                <?php endif; ?>
                <li><a href="profile.php" class="nav-link-premium <?php echo $current_page === 'profile.php' ? 'active' : ''; ?>"><?php echo e(__('profile')); ?></a></li>
            <?php endif; ?>
            <li><a href="index.php#how-it-works" class="nav-link-premium"><?php echo e(__('how_it_works')); ?></a></li>
            <li><a href="#" class="nav-link-premium"><?php echo e(__('about_us')); ?></a></li>
            <li><a href="#" class="nav-link-premium"><?php echo e(__('contact_us')); ?></a></li>
        </ul>
        
        <!-- Actions Area -->
        <div class="nav-actions-premium">
            <!-- Language Dropdown Component -->
            <div class="lang-dropdown-premium">
                <button class="lang-btn-premium" aria-label="Select Language">
                    <span class="lang-btn-content-premium">
                        <!-- Globe SVG Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-globe"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                        <span><?php echo e($active_lang['name']); ?></span>
                    </span>
                    <!-- Chevron Down SVG Icon -->
                    <svg class="icon-chevron" xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </button>
                <ul class="lang-menu-premium">
                    <?php foreach ($lang_details as $code => $detail): ?>
                        <li>
                            <a href="<?php echo e(get_lang_switch_url($code)); ?>" class="lang-option-premium <?php echo $current_lang === $code ? 'active' : ''; ?>">
                                <span class="lang-flag-label">
                                    <span><?php echo e($detail['flag']); ?></span> <?php echo e($detail['name']); ?>
                                </span>
                                <?php if ($current_lang === $code): ?>
                                    <!-- Blue Checkmark SVG Icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="icon-check" style="color: #1245C5;"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                <?php endif; ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <!-- Logged in details & Logout -->
                <span class="user-greeting" style="font-family: 'Inter', sans-serif; font-size: 14px; font-weight: 600; color: #64748B; margin-right: 8px;">
                    Hi, <?php echo e(strtok($_SESSION['full_name'], ' ')); ?>
                </span>
                <a href="logout.php" class="btn-login-premium"><?php echo e(__('logout')); ?></a>
            <?php else: ?>
                <!-- Outlined Login & Filled Sign Up buttons -->
                <a href="login.php" class="btn-login-premium"><?php echo e(__('login')); ?></a>
                <a href="signup.php" class="btn-signup-premium"><?php echo e(__('signup')); ?></a>
            <?php endif; ?>
        </div>
        
        <!-- Animated Hamburger Button (visible < 768px) -->
        <button class="hamburger-btn-premium" id="drawer-toggle" aria-label="Toggle Navigation">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="4" y1="12" x2="20" y2="12"></line><line x1="4" y1="6" x2="20" y2="6"></line><line x1="4" y1="18" x2="20" y2="18"></line></svg>
        </button>
    </div>
</header>

<!-- Mobile Navigation Drawer Panel -->
<div class="drawer-overlay-premium" id="drawer-overlay"></div>
<div class="mobile-drawer-premium" id="mobile-drawer">
    <div class="drawer-header-premium">
        <a href="index.php" class="logo-container-premium">
            <img src="images/logo_icon.png" alt="GoWorker Logo" class="logo-icon-premium" style="width: 40px; height: 40px;">
            <div class="logo-text-group-premium">
                <span class="logo-brand-name-premium" style="font-size: 26px;">GoWorker</span>
            </div>
        </a>
        <button class="drawer-close-premium" id="drawer-close" aria-label="Close Menu">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>
    
    <!-- Drawer Link Tabs -->
    <ul class="drawer-menu-premium">
        <li><a href="index.php" class="drawer-link-premium <?php echo ($current_page === 'index.php' || $current_page === '') ? 'active' : ''; ?>"><?php echo e(__('home')); ?></a></li>
        <li><a href="find-workers.php" class="drawer-link-premium <?php echo $current_page === 'find-workers.php' ? 'active' : ''; ?>"><?php echo e(__('find_workers')); ?></a></li>
        <li><a href="become-worker.php" class="drawer-link-premium <?php echo $current_page === 'become-worker.php' ? 'active' : ''; ?>"><?php echo e(__('become_worker')); ?></a></li>
        <?php if (isset($_SESSION['user_id'])): ?>
            <?php if ($_SESSION['user_type'] === 'customer'): ?>
                <li><a href="booking.php" class="drawer-link-premium <?php echo $current_page === 'booking.php' ? 'active' : ''; ?>"><?php echo e(__('my_bookings')); ?></a></li>
                <li><a href="customer-dashboard.php" class="drawer-link-premium <?php echo $current_page === 'customer-dashboard.php' ? 'active' : ''; ?>"><?php echo e(__('dashboard')); ?></a></li>
            <?php else: ?>
                <li><a href="worker-dashboard.php" class="drawer-link-premium <?php echo $current_page === 'worker-dashboard.php' ? 'active' : ''; ?>"><?php echo e(__('dashboard')); ?></a></li>
            <?php endif; ?>
            <li><a href="profile.php" class="drawer-link-premium <?php echo $current_page === 'profile.php' ? 'active' : ''; ?>"><?php echo e(__('profile')); ?></a></li>
        <?php endif; ?>
        <li><a href="index.php#how-it-works" class="drawer-link-premium"><?php echo e(__('how_it_works')); ?></a></li>
        <li><a href="#" class="drawer-link-premium"><?php echo e(__('about_us')); ?></a></li>
        <li><a href="#" class="drawer-link-premium"><?php echo e(__('contact_us')); ?></a></li>
    </ul>
    
    <!-- Drawer Responsive Action Buttons -->
    <div class="drawer-actions-premium">
        <!-- Language Selector Dropdown inside mobile drawer -->
        <div class="lang-dropdown-premium">
            <button class="lang-btn-premium" aria-label="Select Language" style="width: 100%;">
                <span class="lang-btn-content-premium">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                    <span><?php echo e($active_lang['flag']); ?> <?php echo e($active_lang['name']); ?></span>
                </span>
                <svg class="icon-chevron" xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </button>
            <ul class="lang-menu-premium" style="width: 100%;">
                <?php foreach ($lang_details as $code => $detail): ?>
                    <li>
                        <a href="<?php echo e(get_lang_switch_url($code)); ?>" class="lang-option-premium <?php echo $current_lang === $code ? 'active' : ''; ?>">
                            <span class="lang-flag-label">
                                <span><?php echo e($detail['flag']); ?></span> <?php echo e($detail['name']); ?>
                            </span>
                            <?php if ($current_lang === $code): ?>
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="icon-check" style="color: #1245C5;"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="logout.php" class="btn-login-premium"><?php echo e(__('logout')); ?></a>
        <?php else: ?>
            <a href="login.php" class="btn-login-premium"><?php echo e(__('login')); ?></a>
            <a href="signup.php" class="btn-signup-premium"><?php echo e(__('signup')); ?></a>
        <?php endif; ?>
    </div>
</div>

<!-- Alert Banner Section -->
<div class="container" style="margin-top: 6rem; margin-bottom: -4rem; position: relative; z-index: 10;">
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
