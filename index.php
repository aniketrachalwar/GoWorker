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
    $allowed_langs = ['en', 'hi', 'mr', 'gu', 'ta', 'kn', 'te', 'bho', 'bn'];
    $selected_lang = $_GET['lang'];
    if (in_array($selected_lang, $allowed_langs)) {
        $_SESSION['lang'] = $selected_lang;
    }
    // Redirect to clean URL
    session_write_close();
    if (!headers_sent()) {
        header("Location: index.php");
    } else {
        echo "<script>window.location.href='index.php';</script>";
    }
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

if (empty($categories)) {
    $categories = [
        ['id' => 1, 'name' => 'Electrician', 'icon_class' => 'fa-bolt'],
        ['id' => 2, 'name' => 'Plumber', 'icon_class' => 'fa-faucet'],
        ['id' => 3, 'name' => 'Carpenter', 'icon_class' => 'fa-hammer'],
        ['id' => 4, 'name' => 'Painter', 'icon_class' => 'fa-paint-roller'],
        ['id' => 5, 'name' => 'Cleaner', 'icon_class' => 'fa-broom'],
        ['id' => 6, 'name' => 'Appliance Repair', 'icon_class' => 'fa-screwdriver-wrench'],
        ['id' => 7, 'name' => 'Mechanic', 'icon_class' => 'fa-gears'],
        ['id' => 8, 'name' => 'Mason (Mistri)', 'icon_class' => 'fa-trowel-bricks'],
        ['id' => 9, 'name' => 'Construction Labour', 'icon_class' => 'fa-person-digging'],
        ['id' => 10, 'name' => 'Helper Labour', 'icon_class' => 'fa-hands-holding'],
        ['id' => 11, 'name' => 'Tile Fitter', 'icon_class' => 'fa-border-all'],
        ['id' => 12, 'name' => 'POP Worker', 'icon_class' => 'fa-paint-roller'],
        ['id' => 13, 'name' => 'Painter Labour', 'icon_class' => 'fa-fill-drip'],
        ['id' => 14, 'name' => 'Concrete Worker', 'icon_class' => 'fa-cubes'],
        ['id' => 15, 'name' => 'Scaffolding Worker', 'icon_class' => 'fa-building-shield'],
        ['id' => 16, 'name' => 'Wireman', 'icon_class' => 'fa-plug'],
        ['id' => 17, 'name' => 'CCTV Installer', 'icon_class' => 'fa-video'],
        ['id' => 18, 'name' => 'Inverter Technician', 'icon_class' => 'fa-car-battery'],
        ['id' => 19, 'name' => 'Solar Panel Technician', 'icon_class' => 'fa-solar-panel'],
        ['id' => 20, 'name' => 'Borewell Technician', 'icon_class' => 'fa-water'],
        ['id' => 21, 'name' => 'Water Tank Cleaner', 'icon_class' => 'fa-faucet-drip'],
        ['id' => 22, 'name' => 'Furniture Assembler', 'icon_class' => 'fa-couch'],
        ['id' => 23, 'name' => 'Modular Furniture Installer', 'icon_class' => 'fa-chair'],
        ['id' => 24, 'name' => 'House Cleaner', 'icon_class' => 'fa-broom'],
        ['id' => 25, 'name' => 'Deep Cleaning Service', 'icon_class' => 'fa-soap'],
        ['id' => 26, 'name' => 'Sofa Cleaner', 'icon_class' => 'fa-couch'],
        ['id' => 27, 'name' => 'Carpet Cleaner', 'icon_class' => 'fa-rug'],
        ['id' => 28, 'name' => 'Bathroom Cleaner', 'icon_class' => 'fa-shower'],
        ['id' => 29, 'name' => 'AC Technician', 'icon_class' => 'fa-snowflake'],
        ['id' => 30, 'name' => 'Refrigerator Repair', 'icon_class' => 'fa-temperature-arrow-down'],
        ['id' => 31, 'name' => 'Washing Machine Repair', 'icon_class' => 'fa-soap'],
        ['id' => 32, 'name' => 'TV Repair', 'icon_class' => 'fa-tv'],
        ['id' => 33, 'name' => 'Microwave Repair', 'icon_class' => 'fa-fire-burner'],
        ['id' => 34, 'name' => 'Water Purifier Repair', 'icon_class' => 'fa-filter'],
        ['id' => 35, 'name' => 'Gardener', 'icon_class' => 'fa-seedling'],
        ['id' => 36, 'name' => 'Pest Control', 'icon_class' => 'fa-bugs'],
        ['id' => 37, 'name' => 'Security Guard', 'icon_class' => 'fa-user-shield'],
        ['id' => 38, 'name' => 'Driver', 'icon_class' => 'fa-car'],
        ['id' => 39, 'name' => 'Cook', 'icon_class' => 'fa-utensils'],
        ['id' => 40, 'name' => 'Maid', 'icon_class' => 'fa-broom'],
        ['id' => 41, 'name' => 'Babysitter', 'icon_class' => 'fa-baby'],
        ['id' => 42, 'name' => 'Elder Care Assistant', 'icon_class' => 'fa-user-nurse'],
        ['id' => 43, 'name' => 'Welder', 'icon_class' => 'fa-fire'],
        ['id' => 44, 'name' => 'Fabricator', 'icon_class' => 'fa-industry'],
        ['id' => 45, 'name' => 'Steel Worker', 'icon_class' => 'fa-cubes'],
        ['id' => 46, 'name' => 'Aluminium Worker', 'icon_class' => 'fa-sheet-plastic'],
        ['id' => 47, 'name' => 'Loader', 'icon_class' => 'fa-box-open'],
        ['id' => 48, 'name' => 'Unloader', 'icon_class' => 'fa-dolly'],
        ['id' => 49, 'name' => 'Packers & Movers', 'icon_class' => 'fa-truck-ramp-box'],
        ['id' => 50, 'name' => 'Tempo Service', 'icon_class' => 'fa-truck-pickup'],
        ['id' => 51, 'name' => 'Truck Helper', 'icon_class' => 'fa-truck-front'],
        ['id' => 52, 'name' => 'Bike Repair', 'icon_class' => 'fa-motorcycle'],
        ['id' => 53, 'name' => 'Car Washing', 'icon_class' => 'fa-car-wash'],
        ['id' => 54, 'name' => 'Puncture Repair', 'icon_class' => 'fa-circle-dot'],
        ['id' => 55, 'name' => 'Computer Repair', 'icon_class' => 'fa-desktop'],
        ['id' => 56, 'name' => 'Laptop Repair', 'icon_class' => 'fa-laptop'],
        ['id' => 57, 'name' => 'Mobile Repair', 'icon_class' => 'fa-mobile-screen-button'],
        ['id' => 58, 'name' => 'Network Technician', 'icon_class' => 'fa-wifi'],
        ['id' => 59, 'name' => 'Farm Labour', 'icon_class' => 'fa-wheat-awn'],
        ['id' => 60, 'name' => 'Tractor Driver', 'icon_class' => 'fa-tractor'],
        ['id' => 61, 'name' => 'Irrigation Worker', 'icon_class' => 'fa-droplet'],
        ['id' => 62, 'name' => 'Dairy Worker', 'icon_class' => 'fa-cow'],
        ['id' => 63, 'name' => 'Photographer', 'icon_class' => 'fa-camera'],
        ['id' => 64, 'name' => 'Videographer', 'icon_class' => 'fa-video'],
        ['id' => 65, 'name' => 'DJ Service', 'icon_class' => 'fa-music'],
        ['id' => 66, 'name' => 'Event Decorator', 'icon_class' => 'fa-icons'],
        ['id' => 67, 'name' => 'Beautician', 'icon_class' => 'fa-scissors'],
        ['id' => 68, 'name' => 'Hair Stylist', 'icon_class' => 'fa-scissors'],
        ['id' => 69, 'name' => 'Makeup Artist', 'icon_class' => 'fa-wand-magic-sparkles'],
        ['id' => 70, 'name' => 'Mehendi Artist', 'icon_class' => 'fa-hand-sparkles']
    ];
}
?>
<?php
require_once __DIR__ . '/includes/header.php';
?>
<link rel="stylesheet" href="style.css">
<!-- Google Font -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

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
                    <option value="<?php echo e($cat['id']); ?>"><?php echo e(translate_category_name($cat['name'])); ?></option>
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
        <img src="assets/images/login-workers.png" alt="Workers" onerror="this.src='images/workers_hero.png';">

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
