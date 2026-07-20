<?php
/**
 * GoWorker Homepage - Yash's Layout Integrated
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

// Handle language switch via query parameter and session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_GET['lang'])) {
    $allowed_langs = ['en', 'hi', 'mr', 'gu', 'ta', 'kn'];
    $selected_lang = $_GET['lang'];
    if (in_array($selected_lang, $allowed_langs)) {
        $_SESSION['lang'] = $selected_lang;
    }
    // Redirect to clean URL
    header("Location: index.php");
    exit();
}

$current_lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'en';

$lang_details = [
    'en' => ['name' => 'English', 'flag' => '🇬🇧'],
    'hi' => ['name' => 'Hindi', 'flag' => '🇮🇳'],
    'mr' => ['name' => 'Marathi', 'flag' => '🇮🇳'],
    'gu' => ['name' => 'Gujarati', 'flag' => '🇮🇳'],
    'ta' => ['name' => 'Tamil', 'flag' => '🇮🇳'],
    'kn' => ['name' => 'Kannada', 'flag' => '🇮🇳']
];

$active_lang = isset($lang_details[$current_lang]) ? $lang_details[$current_lang] : $lang_details['en'];

// Fetch categories from the database for the dropdown
$categories = [];
if (isset($pdo)) {
    try {
        $stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
        $categories = $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Database query failed on index.php: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo e($current_lang); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoWorker</title>

    <link rel="stylesheet" href="style.css">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body>

<!-- ================= NAVBAR ================= -->
<header>
    <nav class="navbar">
        <div class="logo">
            <a href="index.php">
                <img src="images/logo_icon.png" alt="Logo" style="height: 55px; width: auto; object-fit: contain; border-radius: 8px;" onerror="this.src='assets/logo.jfif';">
            </a>
        </div>

        <ul class="nav-links">
            <li><a href="find-workers.php"><?php echo e(__('find_workers')); ?></a></li>
            <li><a href="become-worker.php"><?php echo e(__('become_worker')); ?></a></li>
            <li><a href="#how-it-works"><?php echo e(__('how_it_works')); ?></a></li>
            <li><a href="#"><?php echo e(__('about_us')); ?></a></li>
            <li><a href="#"><?php echo e(__('contact_us')); ?></a></li>
        </ul>

        <div class="nav-right">
            <!-- Language -->
            <div class="language-dropdown">
                <button class="language-btn">
                    <i class="fa-solid fa-globe"></i>
                    <?php echo $active_lang['name']; ?>
                    <i class="fa-solid fa-chevron-down"></i>
                </button>
                <div class="dropdown-content">
                    <?php foreach ($lang_details as $code => $detail): ?>
                        <a href="?lang=<?php echo $code; ?>">
                            <?php echo $detail['flag'] . ' ' . $detail['name']; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <button class="theme-btn" id="theme-toggle">
                <i class="fa-regular fa-sun"></i>
            </button>

            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if ($_SESSION['user_type'] === 'customer'): ?>
                    <button class="login-btn" onclick="location.href='customer-dashboard.php'"><?php echo e(__('dashboard')); ?></button>
                <?php else: ?>
                    <button class="login-btn" onclick="location.href='worker-dashboard.php'"><?php echo e(__('dashboard')); ?></button>
                <?php endif; ?>
                <button class="signup-btn" onclick="location.href='logout.php'"><?php echo e(__('logout')); ?></button>
            <?php else: ?>
                <button class="login-btn" onclick="location.href='login.php'"><?php echo e(__('login')); ?></button>
                <button class="signup-btn" onclick="location.href='signup.php'"><?php echo e(__('signup')); ?></button>
            <?php endif; ?>
        </div>
    </nav>
</header>

<!-- ================= HERO ================= -->
<section class="hero">
    <div class="hero-left">
        <h1>
            <?php echo __('hero_title'); ?>
        </h1>
        <p>
            <?php echo e(__('hero_para')); ?>
        </p>

        <form action="find-workers.php" method="GET" class="search-box">
            <select name="category" aria-label="Select Category">
                <option value=""><?php echo e(__('select_category')); ?></option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo e($cat['id']); ?>"><?php echo e($cat['name']); ?></option>
                <?php endforeach; ?>
            </select>

            <input type="text" name="location" placeholder="<?php echo e(__('enter_location')); ?>">

            <button type="submit">
                <?php echo e(__('search_workers')); ?>
            </button>
        </form>

        <div class="features">
            <div class="feature">
                <i class="fa-solid fa-users"></i>
                <div>
                    <h3><?php echo e(__('trusted_workers')); ?></h3>
                    <p><?php echo e(__('trusted_workers_desc')); ?></p>
                </div>
            </div>

            <div class="feature">
                <i class="fa-solid fa-shield"></i>
                <div>
                    <h3><?php echo e(__('direct_contact')); ?></h3>
                    <p><?php echo e(__('direct_contact_desc')); ?></p>
                </div>
            </div>

            <div class="feature">
                <i class="fa-solid fa-indian-rupee-sign"></i>
                <div>
                    <h3><?php echo e(__('no_hidden_charges')); ?></h3>
                    <p><?php echo e(__('no_hidden_charges_desc')); ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="hero-right">
        <img src="assets/landing.jpeg" alt="Workers" onerror="this.src='images/workers_hero.png';">

        <!-- Floating Cards -->
        <div class="floating-card rating">
            ⭐ <?php echo e(__('rating_text')); ?>
        </div>

        <div class="floating-card workers">
            👷 15K+ <?php echo e(__('stat_verified_workers')); ?>
        </div>

        <div class="floating-card verified">
            ✔ <?php echo e(__('id_verified')); ?>
        </div>

        <!-- Hero Stats -->
        <div class="hero-stats">
            <div>
                <h3>15K+</h3>
                <p><?php echo e(__('stat_verified_workers')); ?></p>
            </div>
            <div>
                <h3>25K+</h3>
                <p><?php echo e(__('stat_jobs_completed')); ?></p>
            </div>
            <div>
                <h3>4.8★</h3>
                <p><?php echo e(__('stat_avg_rating')); ?></p>
            </div>
        </div>
    </div>
</section>

<!-- ================= CATEGORY ================= -->
<section class="categories">
    <h2><?php echo e(__('popular_categories')); ?></h2>

    <div class="category-grid">
        <a href="find-workers.php?category=1" class="card" style="text-decoration: none; color: inherit;">
            <i class="fa-solid fa-bolt"></i>
            <h3><?php echo e(__('cat_electrician')); ?></h3>
        </a>

        <a href="find-workers.php?category=2" class="card" style="text-decoration: none; color: inherit;">
            <i class="fa-solid fa-faucet"></i>
            <h3><?php echo e(__('cat_plumber')); ?></h3>
        </a>

        <a href="find-workers.php?category=3" class="card" style="text-decoration: none; color: inherit;">
            <i class="fa-solid fa-hammer"></i>
            <h3><?php echo e(__('cat_carpenter')); ?></h3>
        </a>

        <a href="find-workers.php?category=4" class="card" style="text-decoration: none; color: inherit;">
            <i class="fa-solid fa-paint-roller"></i>
            <h3><?php echo e(__('cat_painter')); ?></h3>
        </a>

        <a href="find-workers.php?category=5" class="card" style="text-decoration: none; color: inherit;">
            <i class="fa-solid fa-broom"></i>
            <h3><?php echo e(__('cat_cleaner')); ?></h3>
        </a>

        <a href="find-workers.php?category=6" class="card" style="text-decoration: none; color: inherit;">
            <i class="fa-solid fa-screwdriver-wrench"></i>
            <h3><?php echo e(__('cat_appliance')); ?></h3>
        </a>

        <a href="find-workers.php?category=7" class="card" style="text-decoration: none; color: inherit;">
            <i class="fa-solid fa-gear"></i>
            <h3><?php echo e(__('cat_mechanic')); ?></h3>
        </a>

        <a href="find-workers.php" class="card" style="text-decoration: none; color: inherit;">
            <i class="fa-solid fa-ellipsis"></i>
            <h3><?php echo e(__('cat_more')); ?></h3>
        </a>
    </div>
</section>

<!-- ================= HOW IT WORKS ================= -->
<section class="works" id="how-it-works">
    <h2><?php echo e(__('how_it_works_title')); ?></h2>
    <p class="work-subtitle">
        <?php echo e(__('how_it_works_subtitle')); ?>
    </p>

    <div class="steps">
        <div class="step">
            <div class="step-number">01</div>
            <div class="step-icon">
                <i class="fa-solid fa-magnifying-glass"></i>
            </div>
            <h3><?php echo e(__('step1_title')); ?></h3>
            <p><?php echo e(__('step1_desc')); ?></p>
        </div>

        <div class="step">
            <div class="step-number">02</div>
            <div class="step-icon">
                <i class="fa-solid fa-comments"></i>
            </div>
            <h3><?php echo e(__('step2_title')); ?></h3>
            <p><?php echo e(__('step2_desc')); ?></p>
        </div>

        <div class="step">
            <div class="step-number">03</div>
            <div class="step-icon">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <h3><?php echo e(__('step3_title')); ?></h3>
            <p><?php echo e(__('step3_desc')); ?></p>
        </div>
    </div>
</section>

<!-- ================= STATS ================= -->
<section class="stats">
    <div class="stat">
        <h2>10K+</h2>
        <p><?php echo e(__('stat_happy_customers')); ?></p>
    </div>
    <div class="stat">
        <h2>15K+</h2>
        <p><?php echo e(__('stat_verified_workers')); ?></p>
    </div>
    <div class="stat">
        <h2>25K+</h2>
        <p><?php echo e(__('stat_jobs_completed')); ?></p>
    </div>
    <div class="stat">
        <h2>4.8/5</h2>
        <p><?php echo e(__('stat_avg_rating')); ?></p>
    </div>
</section>

<script src="script.js"></script>

</body>
</html>
