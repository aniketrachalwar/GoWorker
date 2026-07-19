<?php
/**
 * GoWorker Homepage
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

// Fetch categories from the database for dropdown and listing
$categories = [];
if (isset($pdo)) {
    try {
        $stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
        $categories = $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Database query failed on index.php: " . $e->getMessage());
    }
}

// Include Header layout
require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container hero-content">
        <h1>Find Trusted Local Workers Near You</h1>
        <p>Connect with skilled local workers, compare services, and get your work done with confidence.</p>
        
        <!-- Search Form -->
        <form action="find-workers.php" method="GET" class="search-form">
            <div class="search-field">
                <i class="fa-solid fa-magnifying-glass"></i>
                <select name="category" aria-label="Select Category">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo e($cat['id']); ?>"><?php echo e($cat['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="search-field">
                <i class="fa-solid fa-location-dot"></i>
                <input type="text" name="location" placeholder="Enter location (e.g. Downtown)" aria-label="Enter Location">
            </div>
            
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i> Search Workers</button>
        </form>
    </div>
</section>

<!-- Trust Features Section -->
<section class="section">
    <div class="container">
        <div class="text-center mb-6">
            <h2>Why Choose GoWorker?</h2>
            <p style="color: var(--text-muted); max-width: 600px; margin: 0 auto;">We make hiring local service workers simple, transparent, and secure.</p>
        </div>
        
        <div class="features-grid">
            <!-- Feature 1 -->
            <div class="card feature-card">
                <div class="feature-icon">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <h3>Trusted Workers</h3>
                <p style="color: var(--text-muted);">Verified profiles and reviewed by customers in your community.</p>
            </div>
            
            <!-- Feature 2 -->
            <div class="card feature-card">
                <div class="feature-icon">
                    <i class="fa-solid fa-comments"></i>
                </div>
                <h3>Direct Contact</h3>
                <p style="color: var(--text-muted);">Connect and communicate directly with workers via chat before booking.</p>
            </div>
            
            <!-- Feature 3 -->
            <div class="card feature-card">
                <div class="feature-icon">
                    <i class="fa-solid fa-receipt"></i>
                </div>
                <h3>No Hidden Charges</h3>
                <p style="color: var(--text-muted);">Discuss details and agree on the service rate before starting the job.</p>
            </div>
        </div>
    </div>
</section>

<!-- Popular Categories Section -->
<section class="section" style="background-color: var(--white); border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color);">
    <div class="container">
        <div class="text-center mb-6">
            <h2>Popular Categories</h2>
            <p style="color: var(--text-muted);">Choose from a range of skilled services in your locality.</p>
        </div>
        
        <div class="categories-grid">
            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $cat): ?>
                    <a href="find-workers.php?category=<?php echo e($cat['id']); ?>" class="category-card">
                        <i class="fa-solid <?php echo e($cat['icon_class']); ?>"></i>
                        <span><?php echo e($cat['name']); ?></span>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="grid-column: 1 / -1; text-align: center; color: var(--text-muted);">No categories available at the moment.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="section" id="how-it-works">
    <div class="container">
        <div class="text-center mb-6">
            <h2>How It Works</h2>
            <p style="color: var(--text-muted);">Get your tasks completed in three easy steps.</p>
        </div>
        
        <div class="steps-grid">
            <!-- Step 1 -->
            <div class="step-card">
                <div class="step-number">1</div>
                <h3>1. Search</h3>
                <p style="color: var(--text-muted); max-width: 250px; margin: 0 auto;">Find workers by category and location in your area.</p>
            </div>
            
            <!-- Step 2 -->
            <div class="step-card">
                <div class="step-number">2</div>
                <h3>2. Connect &amp; Negotiate</h3>
                <p style="color: var(--text-muted); max-width: 250px; margin: 0 auto;">Talk directly with workers, ask questions and discuss requirements.</p>
            </div>
            
            <!-- Step 3 -->
            <div class="step-card">
                <div class="step-number">3</div>
                <h3>3. Get Work Done</h3>
                <p style="color: var(--text-muted); max-width: 250px; margin: 0 auto;">Book your preferred worker and complete your service with ease.</p>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats">
    <div class="container stats-grid">
        <div class="stat-item">
            <h3>10K+</h3>
            <p>Happy Customers</p>
        </div>
        <div class="stat-item">
            <h3>15K+</h3>
            <p>Verified Workers</p>
        </div>
        <div class="stat-item">
            <h3>25K+</h3>
            <p>Jobs Completed</p>
        </div>
        <div class="stat-item">
            <h3>4.8/5</h3>
            <p>Average Rating</p>
        </div>
    </div>
</section>

<?php
// Include Footer layout
require_once __DIR__ . '/includes/footer.php';
?>
