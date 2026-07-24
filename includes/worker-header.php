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
    session_write_close();
    if (!headers_sent()) {
        header("Location: " . $clean_url);
    } else {
        echo "<script>window.location.href='" . addslashes($clean_url) . "';</script>";
    }
    exit();
}

if (!isset($_SESSION['lang'])) {
    $detected_lang = 'en';
    if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        $langs = [];
        preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $lang_parse);
        if (count($lang_parse[1]) > 0) {
            $langs = array_combine($lang_parse[1], $lang_parse[4]);
            foreach ($langs as $lang => $val) {
                if ($val === '') $langs[$lang] = 1.0;
            }
            arsort($langs, SORT_NUMERIC);
            
            $allowed_langs = ['en', 'hi', 'mr', 'gu', 'ta', 'kn', 'te', 'bho', 'bn'];
            foreach (array_keys($langs) as $lang) {
                $lang_prefix = strtolower(substr($lang, 0, 2));
                if (in_array($lang_prefix, $allowed_langs)) {
                    $detected_lang = $lang_prefix;
                    break;
                }
            }
        }
    }
    $_SESSION['lang'] = $detected_lang;
}

$current_lang = $_SESSION['lang'];

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
if (!function_exists('get_lang_switch_url')) {
    function get_lang_switch_url($lang) {
        $params = $_GET;
        $params['lang'] = $lang;
        return strtok($_SERVER["REQUEST_URI"], '?') . '?' . http_build_query($params);
    }
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
    
    <style>
    /* Reusable Premium Sticky Header Styles */
    header.main-header {
      position: sticky !important;
      top: 0 !important;
      left: 0 !important;
      width: 100% !important;
      background-color: #FFFFFF !important;
      border-bottom: 1.5px solid #E5E7EB !important;
      z-index: 9999 !important; /* Header: z-index 9999 */
      box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04) !important;
      display: flex !important;
      justify-content: center !important;
      align-items: center !important;
      box-sizing: border-box !important;
      height: 80px !important;
      transition: all 0.3s ease !important;
    }

    body.dark-mode header.main-header {
      background-color: #111827 !important;
      border-color: #374151 !important;
    }

    header.main-header nav.navbar {
      width: 100% !important;
      max-width: 1400px !important;
      height: 100% !important;
      display: flex !important;
      justify-content: space-between !important;
      align-items: center !important;
      padding: 0 40px !important;
      margin: 0 auto !important;
      box-sizing: border-box !important;
    }

    /* Logo Image Sizing */
    header.main-header .logo {
      display: flex !important;
      align-items: center !important;
    }

    header.main-header .logo a {
      display: flex !important;
      align-items: center !important;
      min-height: 44px !important;
      min-width: 44px !important;
      text-decoration: none !important;
    }

    header.main-header .logo-img {
      height: 45px !important;
      width: auto !important;
      object-fit: contain !important;
      border-radius: 8px !important;
      display: block !important;
      transition: height 0.3s ease !important;
    }

    /* Desktop Navigation Links */
    header.main-header .nav-links-desktop {
      display: flex !important;
      list-style: none !important;
      gap: 30px !important;
      margin: 0 !important;
      padding: 0 !important;
      align-items: center !important;
    }

    header.main-header .nav-links-desktop li {
      display: block !important;
    }

    header.main-header .nav-links-desktop a {
      text-decoration: none !important;
      color: #1F2937 !important;
      font-weight: 500 !important;
      font-size: 14px !important;
      font-family: 'Inter', sans-serif !important;
      transition: color 0.3s ease !important;
      padding: 12px 6px !important;
      display: inline-flex !important;
      align-items: center !important;
      min-height: 44px !important;
      box-sizing: border-box !important;
    }

    body.dark-mode header.main-header .nav-links-desktop a {
      color: #F3F4F6 !important;
    }

    header.main-header .nav-links-desktop a:hover,
    header.main-header .nav-links-desktop a.active {
      color: #0D4DFF !important;
    }

    /* Desktop Right Section spacing */
    header.main-header .nav-right-desktop {
      display: flex !important;
      align-items: center !important;
      gap: 16px !important;
    }

    header.main-header .language-dropdown {
      position: relative !important;
    }

    header.main-header .language-btn {
      background: rgba(243, 244, 246, 0.8) !important;
      border: 1px solid #E5E7EB !important;
      border-radius: 12px !important;
      padding: 10px 14px !important;
      color: #1F2937 !important;
      display: flex !important;
      align-items: center !important;
      gap: 6px !important;
      font-family: inherit !important;
      font-size: 13.5px !important;
      font-weight: 500 !important;
      cursor: pointer !important;
      min-height: 44px !important;
      box-sizing: border-box !important;
      transition: all 0.3s ease !important;
    }

    body.dark-mode header.main-header .language-btn {
      background: rgba(31, 41, 55, 0.8) !important;
      border-color: #374151 !important;
      color: #F3F4F6 !important;
    }

    header.main-header .language-btn:hover {
      border-color: #0D4DFF !important;
      background: #FFFFFF !important;
    }

    body.dark-mode header.main-header .language-btn:hover {
      background: #1F2937 !important;
    }

    header.main-header .dropdown-content {
      display: none !important;
      position: absolute !important;
      top: calc(100% + 6px) !important;
      right: 0 !important;
      background: #FFFFFF !important;
      border: 1px solid #E5E7EB !important;
      border-radius: 12px !important;
      overflow: hidden !important;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08) !important;
      min-width: 160px !important;
      z-index: 1010 !important;
    }

    body.dark-mode header.main-header .dropdown-content {
      background: #1F2937 !important;
      border-color: #374151 !important;
    }

    header.main-header .dropdown-content a {
      display: block !important;
      padding: 10px 14px !important;
      text-decoration: none !important;
      color: #1F2937 !important;
      font-size: 13.5px !important;
      transition: background 0.2s ease !important;
    }

    body.dark-mode header.main-header .dropdown-content a {
      color: #F3F4F6 !important;
    }

    header.main-header .dropdown-content a:hover {
      background: rgba(13, 77, 255, 0.08) !important;
      color: #0D4DFF !important;
    }

    header.main-header .language-dropdown:hover .dropdown-content {
      display: block !important;
    }

    header.main-header .login-btn {
      padding: 10px 20px !important;
      background: #FFFFFF !important;
      border: 1.5px solid #0D4DFF !important;
      color: #0D4DFF !important;
      border-radius: 12px !important;
      font-weight: 600 !important;
      font-size: 13.5px !important;
      cursor: pointer !important;
      min-height: 44px !important;
      display: inline-flex !important;
      align-items: center !important;
      justify-content: center !important;
      box-sizing: border-box !important;
      transition: all 0.3s ease !important;
    }

    header.main-header .login-btn:hover {
      background: #0D4DFF !important;
      color: #FFFFFF !important;
    }

    header.main-header .signup-btn {
      padding: 10px 20px !important;
      background: linear-gradient(135deg, #0D4DFF 0%, #4A7BFF 100%) !important;
      color: #FFFFFF !important;
      border: none !important;
      border-radius: 12px !important;
      font-weight: 600 !important;
      font-size: 13.5px !important;
      cursor: pointer !important;
      min-height: 44px !important;
      display: inline-flex !important;
      align-items: center !important;
      justify-content: center !important;
      box-sizing: border-box !important;
      transition: all 0.3s ease !important;
    }

    header.main-header .signup-btn:hover {
      box-shadow: 0 6px 15px rgba(13, 77, 255, 0.3) !important;
    }

    /* Hamburger menu controls */
    header.main-header .hamburger-menu-btn,
    #drawer-toggle {
      display: none !important;
      background: transparent !important;
      border: none !important;
      font-size: 24px !important;
      color: #1F2937 !important;
      cursor: pointer !important;
      padding: 10px !important;
      min-height: 44px !important;
      min-width: 44px !important;
      align-items: center !important;
      justify-content: center !important;
      box-sizing: border-box !important;
    }

    body.dark-mode header.main-header .hamburger-menu-btn,
    body.dark-mode #drawer-toggle {
      color: #F3F4F6 !important;
    }

    /* Mobile Sidebar Menu Drawer style */
    .mobile-sidebar-menu,
    #mobile-drawer {
      position: fixed !important;
      top: 0 !important;
      right: -300px !important;
      width: 280px !important;
      height: 100vh !important;
      background-color: #FFFFFF !important;
      box-shadow: -5px 0 25px rgba(0, 0, 0, 0.08) !important;
      z-index: 10000 !important; /* Mobile Menu: z-index 10000 */
      display: flex !important;
      flex-direction: column !important;
      box-sizing: border-box !important;
      transition: right 0.3s cubic-bezier(0.4, 0, 0.2, 1), visibility 0.3s !important;
      padding: 20px !important;
      pointer-events: auto !important; /* Ensure links are clickable */
      visibility: hidden !important;
    }

    body.dark-mode .mobile-sidebar-menu,
    body.dark-mode #mobile-drawer {
      background-color: #111827 !important;
      box-shadow: -5px 0 25px rgba(0, 0, 0, 0.3) !important;
    }

    .mobile-sidebar-menu.open,
    #mobile-drawer.open {
      right: 0 !important;
      visibility: visible !important;
    }

    .mobile-sidebar-header {
      display: flex !important;
      justify-content: space-between !important;
      align-items: center !important;
      margin-bottom: 24px !important;
      border-bottom: 1.5px solid #F3F4F6 !important;
      padding-bottom: 16px !important;
    }

    body.dark-mode .mobile-sidebar-header {
      border-bottom-color: #1F2937 !important;
    }

    .mobile-sidebar-close-btn,
    #drawer-close {
      background: transparent !important;
      border: none !important;
      font-size: 24px !important;
      color: #4B5563 !important;
      cursor: pointer !important;
      min-width: 44px !important;
      min-height: 44px !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
    }

    body.dark-mode .mobile-sidebar-close-btn,
    body.dark-mode #drawer-close {
      color: #D1D5DB !important;
    }

    .mobile-nav-links {
      list-style: none !important;
      padding: 0 !important;
      margin: 0 !important;
      display: flex !important;
      flex-direction: column !important;
      gap: 8px !important;
      overflow-y: auto !important;
      flex: 1 !important;
      scrollbar-width: none !important;
      -ms-overflow-style: none !important;
    }
    .mobile-nav-links::-webkit-scrollbar {
      display: none !important;
    }

    .mobile-nav-links li {
      width: 100% !important;
    }

    .mobile-nav-links a {
      display: flex !important;
      align-items: center !important;
      gap: 12px !important;
      padding: 12px 14px !important;
      text-decoration: none !important;
      color: #374151 !important;
      font-weight: 500 !important;
      font-size: 15px !important;
      border-radius: 8px !important;
      min-height: 44px !important;
      box-sizing: border-box !important;
      transition: all 0.2s ease !important;
    }

    body.dark-mode .mobile-nav-links a {
      color: #D1D5DB !important;
    }

    .mobile-nav-links a:hover,
    .mobile-nav-links a.active {
      background-color: rgba(13, 77, 255, 0.08) !important;
      color: #0D4DFF !important;
    }

    .mobile-nav-divider {
      height: 1px !important;
      background-color: #E5E7EB !important;
      margin: 12px 0 !important;
    }

    body.dark-mode .mobile-nav-divider {
      background-color: #374151 !important;
    }

    .mobile-lang-item {
      display: flex !important;
      flex-direction: column !important;
      gap: 8px !important;
      padding: 8px 14px !important;
    }

    .mobile-lang-title {
      font-size: 13.5px !important;
      color: #6B7280 !important;
      font-weight: 500 !important;
      display: flex !important;
      align-items: center !important;
      gap: 10px !important;
    }

    body.dark-mode .mobile-lang-title {
      color: #9CA3AF !important;
    }

    .mobile-lang-select {
      width: 100% !important;
      padding: 10px !important;
      border: 1px solid #D1D5DB !important;
      border-radius: 8px !important;
      background-color: #F9FAFB !important;
      color: #1F2937 !important;
      font-size: 14px !important;
      font-family: inherit !important;
      min-height: 44px !important;
    }

    body.dark-mode .mobile-lang-select {
      background-color: #1F2937 !important;
      border-color: #374151 !important;
      color: #F3F4F6 !important;
    }

    /* Backdrop overlay style */
    .mobile-sidebar-overlay,
    #drawer-overlay {
      position: fixed !important;
      top: 0 !important;
      left: 0 !important;
      width: 100vw !important;
      height: 100vh !important;
      background-color: rgba(0, 0, 0, 0.4) !important;
      z-index: 9998 !important; /* Overlay: z-index 9998 */
      opacity: 0 !important;
      visibility: hidden !important;
      transition: all 0.3s ease !important;
    }

    .mobile-sidebar-overlay.active,
    .mobile-sidebar-overlay.open,
    #drawer-overlay.active,
    #drawer-overlay.open {
      opacity: 1 !important;
      visibility: visible !important;
    }

    /* MEDIA QUERIES FOR RESPONSIVE BEHAVIOR */
    @media (max-width: 1024px) {
      header.main-header nav.navbar {
        padding: 0 24px !important;
      }
      header.main-header .nav-links-desktop {
        gap: 16px !important;
      }
    }

    @media (max-width: 900px) {
      header.main-header .nav-links-desktop,
      header.main-header .nav-right-desktop {
        display: none !important;
      }
      header.main-header .hamburger-menu-btn,
      #drawer-toggle {
        display: flex !important;
      }
      header.main-header .logo-img {
        height: 38px !important; /* Scale correctly on mobile */
      }
    }

    @media (max-width: 768px) {
      body {
        overflow-x: hidden !important;
      }
    }
    </style>
</head>
<body>

<header class="main-header">
    <nav class="navbar">
        <!-- Logo Left -->
        <div class="logo">
            <a href="index.php">
                <img src="images/logo_icon.png" alt="GoWorker Logo" class="logo-img" onerror="this.src='images/logo.jpg';">
            </a>
        </div>

        <!-- Center links desktop -->
        <ul class="nav-links-desktop">
            <li><a href="worker-dashboard.php" class="<?php echo $current_page === 'worker-dashboard.php' ? 'active' : ''; ?>">Dashboard</a></li>
            <li><a href="worker-jobs.php" class="<?php echo $current_page === 'worker-jobs.php' ? 'active' : ''; ?>">Jobs</a></li>
            <li><a href="worker-requests.php" class="<?php echo $current_page === 'worker-requests.php' ? 'active' : ''; ?>">Requests</a></li>
            <li><a href="worker-earnings.php" class="<?php echo $current_page === 'worker-earnings.php' ? 'active' : ''; ?>">Earnings</a></li>
            <li><a href="worker-id-card.php" class="<?php echo $current_page === 'worker-id-card.php' ? 'active' : ''; ?>">Virtual ID Card</a></li>
            <li><a href="profile.php" class="<?php echo $current_page === 'profile.php' ? 'active' : ''; ?>">Profile</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>

        <!-- Right navigation items desktop -->
        <div class="nav-right-desktop">
            <div class="language-dropdown">
                <button class="language-btn">
                    <i class="fa-solid fa-globe"></i>
                    <span><?php echo $active_lang['name']; ?></span>
                    <i class="fa-solid fa-chevron-down"></i>
                </button>
                <div class="dropdown-content">
                    <?php foreach ($lang_details as $code => $detail): ?>
                        <a href="<?php echo e(get_lang_switch_url($code)); ?>">
                            <?php echo $detail['flag'] . ' ' . $detail['name']; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Hamburger Icon for Mobile (Right) -->
        <button id="drawer-toggle" class="hamburger-menu-btn" aria-label="Toggle Navigation Menu">
            <i class="fa-solid fa-bars"></i>
        </button>
    </nav>
 
    <!-- Mobile Slide-out Menu Drawer -->
    <div id="mobile-drawer" class="mobile-sidebar-menu">
        <div class="mobile-sidebar-header">
            <div class="logo">
                <a href="index.php">
                    <img src="images/logo_icon.png" alt="GoWorker Logo" style="height: 38px; width: auto; object-fit: contain; border-radius: 8px;" onerror="this.src='images/logo.jpg';">
                </a>
            </div>
            <button id="drawer-close" class="mobile-sidebar-close-btn" aria-label="Close Navigation Menu">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        
        <ul class="mobile-nav-links">
            <li><a href="worker-dashboard.php" class="<?php echo $current_page === 'worker-dashboard.php' ? 'active' : ''; ?>"><i class="fa-solid fa-gauge"></i> Dashboard</a></li>
            <li><a href="worker-jobs.php" class="<?php echo $current_page === 'worker-jobs.php' ? 'active' : ''; ?>"><i class="fa-solid fa-briefcase"></i> Jobs</a></li>
            <li><a href="worker-requests.php" class="<?php echo $current_page === 'worker-requests.php' ? 'active' : ''; ?>"><i class="fa-solid fa-list-check"></i> Requests</a></li>
            <li><a href="worker-earnings.php" class="<?php echo $current_page === 'worker-earnings.php' ? 'active' : ''; ?>"><i class="fa-solid fa-wallet"></i> Earnings</a></li>
            <li><a href="worker-id-card.php" class="<?php echo $current_page === 'worker-id-card.php' ? 'active' : ''; ?>"><i class="fa-solid fa-id-card"></i> Virtual ID Card</a></li>
            <li><a href="profile.php" class="<?php echo $current_page === 'profile.php' ? 'active' : ''; ?>"><i class="fa-solid fa-user-gear"></i> Profile</a></li>
            <li><a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
            
            <li class="mobile-nav-divider"></li>
            
            <li class="mobile-lang-item">
                <span class="mobile-lang-title"><i class="fa-solid fa-globe"></i> Language:</span>
                <select onchange="location.href=this.value" class="mobile-lang-select" aria-label="Language Selector">
                    <?php foreach ($lang_details as $code => $detail): ?>
                        <option value="<?php echo e(get_lang_switch_url($code)); ?>" <?php echo ($current_lang === $code) ? 'selected' : ''; ?>>
                            <?php echo $detail['flag'] . ' ' . $detail['name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </li>
        </ul>
    </div>
    
    <div id="drawer-overlay" class="mobile-sidebar-overlay"></div>
</header>

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
