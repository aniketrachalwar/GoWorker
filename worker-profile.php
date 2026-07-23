<?php
/**
 * GoWorker - Worker Profile Page
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$worker = null;
$is_viewing_self = false;

if (isset($_SESSION['user_id']) && ($_SESSION['user_type'] ?? '') === 'worker') {
    $userId = $_SESSION['user_id'];
    $is_viewing_self = true;
    if (isset($pdo)) {
        try {
            $stmt = $pdo->prepare("
                SELECT w.*, u.id as user_id, u.full_name as worker_name, u.email, u.phone, u.created_at as user_created_at, u.location as user_location, c.name as category_name 
                FROM users u
                LEFT JOIN worker_profiles w ON w.user_id = u.id
                LEFT JOIN categories c ON w.category_id = c.id
                WHERE u.id = ? AND u.user_type = 'worker'
            ");
            $stmt->execute([$userId]);
            $worker = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in worker-profile.php: " . $e->getMessage());
        }
    }
} else {
    $worker_id = intval($_GET['id'] ?? 1);
    if (isset($pdo)) {
        try {
            $stmt = $pdo->prepare("
                SELECT w.*, u.id as user_id, u.full_name as worker_name, u.email, u.phone, u.created_at as user_created_at, u.location as user_location, c.name as category_name 
                FROM worker_profiles w
                JOIN users u ON w.user_id = u.id
                JOIN categories c ON w.category_id = c.id
                WHERE w.id = ?
            ");
            $stmt->execute([$worker_id]);
            $worker = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in worker-profile.php: " . $e->getMessage());
        }
    }
}

// Fallback to dynamic session data if logged in as worker, otherwise static Ramesh
if (!$worker) {
    if (isset($_SESSION['user_id']) && ($_SESSION['user_type'] ?? '') === 'worker') {
        $worker = [
            'id' => $_SESSION['user_id'],
            'user_id' => $_SESSION['user_id'],
            'worker_name' => $_SESSION['full_name'] ?? 'Worker Professional',
            'email' => $_SESSION['email'] ?? '',
            'phone' => '+91 98765 43210',
            'category_name' => 'General Trade',
            'title' => 'Service Specialist',
            'bio' => 'Verified GoWorker professional contractor. Contact for home maintenance and repairs.',
            'hourly_rate' => 299.00,
            'location' => 'Pune',
            'experience_years' => 5,
            'availability' => 'Mon, Tue, Wed, Thu, Fri',
            'skills' => 'Repair, Maintenance',
            'profile_picture' => 'images/avatar_placeholder.png',
            'user_created_at' => date('Y-m-d H:i:s'),
            'user_location' => 'Pune'
        ];
    } else {
        $worker = [
            'id' => 1,
            'user_id' => 2,
            'worker_name' => 'Ramesh Kumar',
            'category_name' => 'Electrician',
            'title' => 'Senior Certified Electrician',
            'bio' => 'I am a certified, professional electrician with over 5 years of experience serving residential and commercial properties in Pune. I specialize in complete house wiring, smart home automation, AC servicing, and emergency electrical troubleshooting.',
            'hourly_rate' => 299.00,
            'location' => 'Pune',
            'experience_years' => 5,
            'availability' => 'Mon, Tue, Wed, Thu, Fri',
            'skills' => 'Wiring, Fuse repairs, Smart Home, Inverter Setup',
            'profile_picture' => 'https://images.unsplash.com/photo-1540569014015-19a7be504e3a?w=200&fit=crop',
            'user_created_at' => '2025-07-15 10:00:00',
            'user_location' => 'Pune'
        ];
    }
}

// Coalesce locations and picture variables for safe rendering
$worker['location'] = $worker['location'] ?: ($worker['user_location'] ?: 'Pune');
$worker['profile_picture'] = $worker['profile_picture'] ?: 'images/avatar_placeholder.png';
$worker['user_id'] = $worker['user_id'] ?: ($worker['id'] ?: 2);

$reviews = [];
$rating_avg = 5.0;
$rating_count = 0;
$rating_breakdown = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];

if (isset($pdo) && $worker) {
    try {
        $stmt = $pdo->prepare("
            SELECT r.*, u.full_name as customer_name 
            FROM reviews r
            JOIN users u ON r.customer_id = u.id
            WHERE r.worker_id = ?
            ORDER BY r.created_at DESC
        ");
        $stmt->execute([$worker['user_id']]);
        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!empty($reviews)) {
            $total_rating = 0;
            foreach ($reviews as $rev) {
                $total_rating += $rev['rating'];
                $rating_breakdown[$rev['rating']] = ($rating_breakdown[$rev['rating']] ?? 0) + 1;
            }
            $rating_count = count($reviews);
            $rating_avg = round($total_rating / $rating_count, 1);
        }
    } catch (PDOException $e) {
        error_log("Database error fetching reviews in worker-profile.php: " . $e->getMessage());
    }
}

// Fallback to static reviews for Ramesh Kumar
if (empty($reviews) && $worker['id'] == 1) {
    $rating_avg = 4.9;
    $rating_count = 128;
    $rating_breakdown = [5 => 115, 4 => 10, 3 => 3, 2 => 0, 1 => 0];
    $reviews = [
        [
            'customer_name' => 'Aniket Rachalwar',
            'created_at' => '2026-07-15 12:00:00',
            'rating' => 5,
            'review_text' => 'Ramesh was excellent! He arrived right on time, diagnosed the short circuit in my living room within ten minutes, and fixed it safely. Will definitely book again.'
        ],
        [
            'customer_name' => 'Sunita Patil',
            'created_at' => '2026-06-28 12:00:00',
            'rating' => 5,
            'review_text' => 'Very polite and highly professional. Did a clean job mounting the AC and cabling.'
        ]
    ];
}

require_once __DIR__ . '/includes/header.php';
?>
<link rel="stylesheet" href="worker-profile.css">

<!-- ================= PROFILE LAYOUT ================= -->
<main class="container" style="min-height: 80vh;">
  <div class="profile-layout">
    
    <!-- LEFT CONTENT AREA (70%) -->
    <section class="main-profile-content">
      <!-- Profile Header Banner -->
      <div class="profile-hero">
        <div class="cover-banner" style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-hover) 100%); height: 160px; border-radius: var(--radius-lg) var(--radius-lg) 0 0;"></div>
        <div class="header-info-bar">
          <div class="avatar-wrapper">
            <img class="avatar-img" src="<?php echo e($worker['profile_picture'] ?: 'images/avatar_placeholder.png'); ?>" alt="<?php echo e($worker['worker_name']); ?>">
            <span class="status-dot status-online"></span>
          </div>
          
          <div class="profile-title-section">
            <h1 style="color: var(--dark-navy); font-weight: 700; margin-bottom: 4px;"><?php echo e($worker['worker_name']); ?> <span class="verified-badge" style="position: static; display: inline-flex; border: none; font-size: 13px; width: auto; height: auto; padding: 4px 8px; border-radius: 6px;"><i class="fa-solid fa-check"></i> Verified</span></h1>
            <p class="profession"><?php echo e($worker['title'] ?: $worker['category_name']); ?></p>
            
            <div class="meta-stats" style="margin-bottom: 8px;">
              <span><i class="fa-solid fa-star" style="color: #F59E0B;"></i> <?php echo $rating_avg; ?> (<?php echo $rating_count; ?> Reviews)</span>
              <span><i class="fa-solid fa-briefcase"></i> <?php echo e($worker['experience_years']); ?>+ Years Experience</span>
              <span><i class="fa-solid fa-location-dot"></i> <?php echo e($worker['location']); ?></span>
            </div>
            <div class="meta-stats">
              <span style="background: rgba(13, 77, 255, 0.06); color: #0D4DFF; padding: 4px 10px; border-radius: 20px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; border: 1px solid rgba(13,77,255,0.12);"><i class="fa-solid fa-id-badge"></i> Worker ID: #GW-<?php echo e(str_pad($worker['id'], 5, '0', STR_PAD_LEFT)); ?></span>
              <span style="background: rgba(16, 185, 129, 0.08); color: #10B981; padding: 4px 10px; border-radius: 20px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; border: 1px solid rgba(16,185,129,0.12);"><i class="fa-solid fa-circle-check"></i> 128 Jobs Completed</span>
            </div>
          </div>

          <div class="action-buttons-group">
            <button class="btn-icon" id="fav-profile-btn" style="border: none;" title="Add to Favorites"><i class="fa-regular fa-heart"></i></button>
            <button class="btn-icon" style="border: none;" title="Share Profile"><i class="fa-solid fa-share-nodes"></i></button>
          </div>
        </div>
      </div>

      <!-- About Me Section -->
      <div class="profile-card" style="background: var(--white); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 24px; margin-bottom: 24px; box-shadow: var(--shadow-sm);">
        <h3 style="color: var(--dark-navy); margin-bottom: 12px;">About <?php echo e(explode(' ', $worker['worker_name'])[0]); ?></h3>
        <p style="font-size: 14px; color: var(--secondary-text); line-height: 1.6; margin-bottom: 12px;">
          <?php echo e($worker['bio'] ?: 'No bio provided yet.'); ?>
        </p>
      </div>

      <!-- Services Section -->
      <div class="profile-card" style="background: var(--white); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 24px; margin-bottom: 24px; box-shadow: var(--shadow-sm);">
        <h3 style="color: var(--dark-navy); margin-bottom: 16px;">Skills & Services</h3>
        <div class="services-list" style="display: flex; flex-direction: column; gap: 16px;">
          <?php 
          $skills_list = array_filter(array_map('trim', explode(',', $worker['skills'] ?? '')));
          if (!empty($skills_list)):
              foreach ($skills_list as $skill):
          ?>
              <div class="service-card" style="display: flex; justify-content: space-between; align-items: center; padding: 16px; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                <div class="service-info">
                  <h4 style="color: var(--dark-navy); margin-bottom: 4px;"><?php echo e($skill); ?></h4>
                  <p style="font-size: 13px; color: var(--secondary-text); margin-bottom: 0;">Verified service specialist.</p>
                </div>
                <div style="display: flex; align-items: center; gap: 20px;">
                  <span style="font-weight: 700; color: var(--primary);">₹<?php echo e($worker['hourly_rate']); ?>/hr</span>
                  <button class="btn-book" onclick="location.href='booking.php?worker=<?php echo $worker['id']; ?>'">Select</button>
                </div>
              </div>
          <?php 
              endforeach;
          else:
          ?>
              <div class="service-card" style="display: flex; justify-content: space-between; align-items: center; padding: 16px; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                <div class="service-info">
                  <h4 style="color: var(--dark-navy); margin-bottom: 4px;"><?php echo e($worker['category_name']); ?> Services</h4>
                  <p style="font-size: 13px; color: var(--secondary-text); margin-bottom: 0;">General service and repairs.</p>
                </div>
                <div style="display: flex; align-items: center; gap: 20px;">
                  <span style="font-weight: 700; color: var(--primary);">₹<?php echo e($worker['hourly_rate']); ?>/hr</span>
                  <button class="btn-book" onclick="location.href='booking.php?worker=<?php echo $worker['id']; ?>'">Select</button>
                </div>
              </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Work Gallery Grid -->
      <div class="profile-card" style="background: var(--white); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 24px; margin-bottom: 24px; box-shadow: var(--shadow-sm);">
        <h3 style="color: var(--dark-navy); margin-bottom: 16px;">Work Portfolio</h3>
        <div class="gallery-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 12px;">
          <div class="gallery-item" style="cursor: pointer;"><img src="https://images.unsplash.com/photo-1621905251189-08b45d6a269e?w=300" alt="Work 1" style="width: 100%; border-radius: var(--radius-sm); object-fit: cover;"></div>
          <div class="gallery-item" style="cursor: pointer;"><img src="https://images.unsplash.com/photo-1581092921461-eab62e97a780?w=300" alt="Work 2" style="width: 100%; border-radius: var(--radius-sm); object-fit: cover;"></div>
          <div class="gallery-item" style="cursor: pointer;"><img src="https://images.unsplash.com/photo-1558346490-a72e53ae2d4f?w=300" alt="Work 3" style="width: 100%; border-radius: var(--radius-sm); object-fit: cover;"></div>
          <div class="gallery-item" style="cursor: pointer;"><img src="https://images.unsplash.com/photo-1473341304170-971dccb5ac1e?w=300" alt="Work 4" style="width: 100%; border-radius: var(--radius-sm); object-fit: cover;"></div>
        </div>
      </div>

      <!-- Reviews Section -->
      <div class="profile-card" style="background: var(--white); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 24px; box-shadow: var(--shadow-sm);">
        <h3 style="color: var(--dark-navy); margin-bottom: 16px;">Ratings & Reviews</h3>
        <div class="review-summary" style="display: flex; gap: 32px; align-items: center; margin-bottom: 24px; flex-wrap: wrap;">
          <div class="rating-big" style="font-size: 42px; font-weight: 800; color: var(--primary-text); line-height: 1;">
            <?php echo $rating_avg; ?>
            <p style="font-size: 12px; color: var(--secondary-text); margin-bottom: 0;">out of 5.0</p>
          </div>
          
          <div class="rating-breakdown" style="flex: 1; display: flex; flex-direction: column; gap: 8px;">
            <?php 
            for ($i = 5; $i >= 3; $i--):
                $percentage = ($rating_count > 0) ? round(($rating_breakdown[$i] / $rating_count) * 100) : 0;
                // Ramesh special case percentage display
                if ($worker['id'] == 1) {
                    if ($i == 5) $percentage = 90;
                    if ($i == 4) $percentage = 8;
                    if ($i == 3) $percentage = 2;
                }
            ?>
                <div class="breakdown-row" style="display: flex; align-items: center; gap: 12px; font-size: 13px;">
                  <span><?php echo $i; ?> Stars</span>
                  <div class="progress-bar-bg" style="flex: 1; height: 6px; background: var(--border-color); border-radius: 3px;"><div class="progress-bar-fill" style="width: <?php echo $percentage; ?>%; height: 100%; background: #F59E0B; border-radius: 3px;"></div></div>
                  <span><?php echo $percentage; ?>%</span>
                </div>
            <?php endfor; ?>
          </div>
        </div>

        <div class="reviews-list" style="display: flex; flex-direction: column; gap: 16px;">
          <?php if (!empty($reviews)): ?>
              <?php foreach ($reviews as $review): ?>
                  <div class="review-card" style="padding: 16px; border-bottom: 1px solid var(--border-color);">
                    <div class="review-header" style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                      <span class="review-author" style="font-weight: 600; color: var(--dark-navy);"><?php echo e($review['customer_name']); ?></span>
                      <span class="review-date" style="font-size: 12px; color: var(--secondary-text);"><?php echo date('F j, Y', strtotime($review['created_at'])); ?></span>
                    </div>
                    <div style="color: #F59E0B; margin-bottom: 8px; font-size: 14px;">
                        <?php echo str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']); ?>
                    </div>
                    <p style="font-size: 14px; color: var(--secondary-text); line-height: 1.6;"><?php echo e($review['review_text']); ?></p>
                  </div>
              <?php endforeach; ?>
          <?php else: ?>
              <p style="color: var(--secondary-text); font-size: 14px; text-align: center; padding: 20px 0;">No reviews yet for this worker.</p>
          <?php endif; ?>
        </div>
      </div>
    </section>

    <!-- RIGHT SIDEBAR (30%) - STICKY BOOKING CARD -->
    <aside class="sticky-booking-sidebar">
      <div class="booking-sidebar-card" style="background: var(--white); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 24px; box-shadow: var(--shadow-sm);">
        <div class="sidebar-price-row">
          <div class="sidebar-price">
            Starting from
            <strong>₹<?php echo e($worker['hourly_rate']); ?>/hr</strong>
          </div>
        </div>

        <div class="availability-banner" style="display: flex; align-items: center; gap: 8px; background: rgba(34, 197, 94, 0.08); color: var(--success); padding: 10px 14px; border-radius: var(--radius-sm); font-size: 13px; font-weight: 600; margin-bottom: 20px;">
          <i class="fa-solid fa-circle-check"></i>
          <span>Available: <?php echo e($worker['availability'] ?: 'Contact for Schedule'); ?></span>
        </div>

        <?php if (($_SESSION['user_type'] ?? '') !== 'worker'): ?>
        <button class="btn-primary-auth" style="width: 100%; margin-bottom: 12px; height: 50px;" onclick="location.href='booking.php?worker=<?php echo $worker['id']; ?>'">
          <span>Book Now</span>
          <i class="fa-solid fa-calendar-days"></i>
        </button>
        
        <button class="btn-google-auth" id="chat-worker-btn" style="width: 100%; margin-bottom: 12px; height: 50px; border-color: var(--primary); color: var(--primary);">
          <i class="fa-regular fa-comment-dots"></i>
          <span>Chat with <?php echo e(explode(' ', $worker['worker_name'])[0]); ?></span>
        </button>

        <button class="btn-google-auth" id="call-worker-btn" style="width: 100%; margin-bottom: 12px; height: 50px;">
          <i class="fa-solid fa-phone"></i>
          <span>Call Worker</span>
        </button>
        <?php endif; ?>

        <!-- Download Virtual ID Card Button -->
        <button class="btn-primary-auth" id="download-id-card-btn" style="width: 100%; height: 50px; background: linear-gradient(135deg, #10B981 0%, #059669 100%); border: none; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);" onclick="downloadIDCard('<?php echo e(addslashes($worker['worker_name'])); ?>', <?php echo $worker['user_id']; ?>, '<?php echo e(addslashes($worker['title'] ?: $worker['category_name'])); ?>', '<?php echo e(addslashes($worker['profile_picture'] ?: 'images/avatar_placeholder.png')); ?>', '<?php echo e(addslashes($worker['location'])); ?>', '<?php echo e($worker['experience_years']); ?>', '<?php echo e(addslashes($worker['email'] ?? '')); ?>', '<?php echo e(addslashes($worker['phone'] ?? '')); ?>', '<?php echo e(date('F Y', strtotime($worker['user_created_at'] ?? 'now'))); ?>')">
          <i class="fa-solid fa-id-card" style="margin-right: 8px;"></i>
          <span>Download ID Card</span>
        </button>
      </div>
    </aside>

  </div>
</main>

<script src="worker-profile.js"></script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>

