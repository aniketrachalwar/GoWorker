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

<!-- SECTION 1: HERO SECTION -->
<section class="hero-premium">
    <div class="hero-grid-premium">
        <!-- Hero Left Content Column -->
        <div class="hero-left-premium fade-in-up-premium">
            <h1 class="hero-headline-premium">
                <?php echo __('hero_title'); ?>
            </h1>
            <p class="hero-paragraph-premium">
                <?php echo e(__('hero_para')); ?>
            </p>
            
            <!-- Horizontal Search Card Form -->
            <form action="find-workers.php" method="GET" class="hero-search-card-premium">
                <!-- Column 1: Category Dropdown -->
                <div class="search-col-cat-premium">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                    <select name="category" aria-label="Select Category">
                        <option value=""><?php echo e(__('select_category')); ?></option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo e($cat['id']); ?>"><?php echo e($cat['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <!-- Custom chevron down SVG -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </div>
                
                <!-- Column 2: Location Input -->
                <div class="search-col-loc-premium">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                    <input type="text" name="location" placeholder="<?php echo e(__('enter_location')); ?>" aria-label="Enter Location">
                    <!-- GPS target location SVG -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="action-location-target" title="Get Current Location"><circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="3"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line></svg>
                </div>
                
                <!-- Column 3: Blue Submit Button -->
                <button type="submit" class="search-btn-premium">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <span><?php echo e(__('search_workers')); ?></span>
                </button>
            </form>
            
            <!-- Three Columns Feature Highlights -->
            <div class="hero-features-row-premium">
                <!-- Highlight Item 1 -->
                <div class="feature-item-premium">
                    <div class="feature-icon-wrapper-premium">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    </div>
                    <div class="feature-text-group-premium">
                        <h4 class="feature-title-premium"><?php echo e(__('trusted_workers')); ?></h4>
                        <span class="feature-subtitle-premium"><?php echo e(__('trusted_workers_desc')); ?></span>
                    </div>
                </div>
                
                <!-- Highlight Item 2 -->
                <div class="feature-item-premium">
                    <div class="feature-icon-wrapper-premium">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                    </div>
                    <div class="feature-text-group-premium">
                        <h4 class="feature-title-premium"><?php echo e(__('direct_contact')); ?></h4>
                        <span class="feature-subtitle-premium"><?php echo e(__('direct_contact_desc')); ?></span>
                    </div>
                </div>
                
                <!-- Highlight Item 3 -->
                <div class="feature-item-premium">
                    <div class="feature-icon-wrapper-premium">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3h12M6 8h12M6 13h8.5a4.5 4.5 0 0 0 0-9H6M6 13l8.5 8"></path></svg>
                    </div>
                    <div class="feature-text-group-premium">
                        <h4 class="feature-title-premium"><?php echo e(__('no_hidden_charges')); ?></h4>
                        <span class="feature-subtitle-premium"><?php echo e(__('no_hidden_charges_desc')); ?></span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Hero Right Column Graphic Illustrative Panel -->
        <div class="hero-right-premium fade-in-up-premium">
            <div class="hero-image-wrapper-premium">
                <!-- Background decorative elements -->
                <div class="hero-bg-gradient-premium"></div>
                <div class="hero-circle-deco-premium"></div>
                <div class="hero-dots-deco-premium dots-top-left"></div>
                <div class="hero-dots-deco-premium dots-bottom-right"></div>
                
                <!-- High-fidelity worker illustration image -->
                <img src="images/workers_hero.png" alt="Trusted local service workers" class="hero-worker-img-premium">
                
                <!-- Float Card 1: ID Verified status -->
                <div class="hero-badge-verified-premium">
                    <div class="badge-icon-verified-premium">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><polyline points="9 11 11 13 15 9"></polyline></svg>
                    </div>
                    <div class="badge-text-verified-premium">
                        <h4><?php echo e(__('id_verified')); ?></h4>
                        <p><?php echo e(__('id_verified_desc')); ?></p>
                    </div>
                </div>
                
                <!-- Float Card 2: Rating overview -->
                <div class="hero-badge-rating-premium">
                    <span class="badge-rating-text-premium"><?php echo e(__('rating_text')); ?></span>
                    <div class="badge-stars-row-premium">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                    </div>
                    <div class="badge-avatars-row-premium">
                        <span class="avatar-badge-premium" style="background-color: #EF4444; z-index: 5;">A</span>
                        <span class="avatar-badge-premium" style="background-color: #3B82F6; z-index: 4;">S</span>
                        <span class="avatar-badge-premium" style="background-color: #10B981; z-index: 3;">V</span>
                        <span class="avatar-badge-premium" style="background-color: #F59E0B; z-index: 2;">R</span>
                        <span class="avatar-badge-premium" style="background-color: #8B5CF6; z-index: 1;">K</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SECTION 2: POPULAR CATEGORIES -->
<section class="categories-section-premium">
    <div class="categories-container-premium fade-in-up-premium">
        <!-- Header area link redirection -->
        <div class="categories-header-premium">
            <h2 class="cat-title-premium"><?php echo e(__('popular_categories')); ?></h2>
            <a href="find-workers.php" class="cat-link-premium">
                <span><?php echo e(__('view_all')); ?></span>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
            </a>
        </div>
        
        <!-- Grid layout mapping cards -->
        <div class="categories-grid-premium">
            <!-- 1. Electrician -->
            <a href="find-workers.php?category=1" class="cat-card-premium">
                <div class="cat-card-icon-premium">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                </div>
                <span><?php echo e(__('cat_electrician')); ?></span>
            </a>
            
            <!-- 2. Plumber -->
            <a href="find-workers.php?category=2" class="cat-card-premium">
                <div class="cat-card-icon-premium">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v13M18 6h-6M12 10H6a3 3 0 0 0-3 3v2M12 13h5a3 3 0 0 1 3 3v2M6 21h12"></path></svg>
                </div>
                <span><?php echo e(__('cat_plumber')); ?></span>
            </a>
            
            <!-- 3. Carpenter -->
            <a href="find-workers.php?category=3" class="cat-card-premium">
                <div class="cat-card-icon-premium">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.77 3.77z"></path></svg>
                </div>
                <span><?php echo e(__('cat_carpenter')); ?></span>
            </a>
            
            <!-- 4. Painter -->
            <a href="find-workers.php?category=4" class="cat-card-premium">
                <div class="cat-card-icon-premium">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="16" height="6" rx="2"></rect><path d="M6 8v4h12V8M12 12v9"></path></svg>
                </div>
                <span><?php echo e(__('cat_painter')); ?></span>
            </a>
            
            <!-- 5. Cleaner -->
            <a href="find-workers.php?category=5" class="cat-card-premium">
                <div class="cat-card-icon-premium">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 3L6 15M9 12l9-9M6 15l2 5 3-3-5-2zM3 21h18"></path></svg>
                </div>
                <span><?php echo e(__('cat_cleaner')); ?></span>
            </a>
            
            <!-- 6. Appliance Repair -->
            <a href="find-workers.php?category=6" class="cat-card-premium">
                <div class="cat-card-icon-premium">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"></rect><path d="M6 8h.01M10 8h.01M14 8h.01M18 8h.01M6 12h12M6 16h12"></path></svg>
                </div>
                <span><?php echo e(__('cat_appliance')); ?></span>
            </a>
            
            <!-- 7. Mechanic -->
            <a href="find-workers.php?category=7" class="cat-card-premium">
                <div class="cat-card-icon-premium">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                </div>
                <span><?php echo e(__('cat_mechanic')); ?></span>
            </a>
            
            <!-- 8. More Link -->
            <a href="find-workers.php" class="cat-card-premium">
                <div class="cat-card-icon-premium">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                </div>
                <span><?php echo e(__('cat_more')); ?></span>
            </a>
        </div>
    </div>
</section>

<!-- SECTION 3: HOW IT WORKS -->
<section class="how-section-premium" id="how-it-works">
    <div class="how-container-premium fade-in-up-premium">
        <!-- Subtitles and Headings -->
        <span class="how-subtitle-premium"><?php echo e(__('how_it_works_subtitle')); ?></span>
        <h2 class="how-title-premium"><?php echo e(__('how_it_works_title')); ?></h2>
        
        <!-- Step grid with dashed connectors -->
        <div class="how-grid-premium">
            <div class="how-connector-line-premium"></div>
            
            <!-- Step 1 -->
            <div class="how-step-premium">
                <div class="step-circle-premium">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </div>
                <h3><?php echo e(__('step1_title')); ?></h3>
                <p><?php echo e(__('step1_desc')); ?></p>
            </div>
            
            <!-- Step 2 -->
            <div class="how-step-premium">
                <div class="step-circle-premium">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                </div>
                <h3><?php echo e(__('step2_title')); ?></h3>
                <p><?php echo e(__('step2_desc')); ?></p>
            </div>
            
            <!-- Step 3 -->
            <div class="how-step-premium">
                <div class="step-circle-premium">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                </div>
                <h3><?php echo e(__('step3_title')); ?></h3>
                <p><?php echo e(__('step3_desc')); ?></p>
            </div>
        </div>
    </div>
</section>

<!-- SECTION 4: BLUE STATS BAR -->
<section class="stats-bar-premium">
    <div class="stats-container-premium fade-in-up-premium">
        <!-- Rounded Gradient Card Banner -->
        <div class="stats-gradient-card-premium">
            <!-- Stat 1: Customers -->
            <div class="stats-item-premium">
                <div class="stats-icon-wrapper-premium">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                </div>
                <div class="stats-text-group-premium">
                    <span class="stats-number-premium">10K+</span>
                    <span class="stats-label-premium"><?php echo e(__('stat_happy_customers')); ?></span>
                </div>
            </div>
            
            <!-- Stat 2: Workers -->
            <div class="stats-item-premium">
                <div class="stats-icon-wrapper-premium">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                </div>
                <div class="stats-text-group-premium">
                    <span class="stats-number-premium">15K+</span>
                    <span class="stats-label-premium"><?php echo e(__('stat_verified_workers')); ?></span>
                </div>
            </div>
            
            <!-- Stat 3: Jobs -->
            <div class="stats-item-premium">
                <div class="stats-icon-wrapper-premium">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                </div>
                <div class="stats-text-group-premium">
                    <span class="stats-number-premium">25K+</span>
                    <span class="stats-label-premium"><?php echo e(__('stat_jobs_completed')); ?></span>
                </div>
            </div>
            
            <!-- Stat 4: Rating -->
            <div class="stats-item-premium">
                <div class="stats-icon-wrapper-premium">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                </div>
                <div class="stats-text-group-premium">
                    <span class="stats-number-premium">4.8/5</span>
                    <span class="stats-label-premium"><?php echo e(__('stat_avg_rating')); ?></span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer scripts only, omitting full visual footer element -->
<script src="js/main.js"></script>
</body>
</html>
