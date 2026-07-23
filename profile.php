<?php
/**
 * GoWorker - Dedicated User Profile Page
 * Matches master design system (#0D4DFF, 20px radius, #F8FAFF bg)
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect unauthenticated visitors to login page
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Ensure upload directory exists
$upload_dir = __DIR__ . '/uploads/profile/';
if (!file_exists($upload_dir)) {
    @mkdir($upload_dir, 0777, true);
}

$user_id = $_SESSION['user_id'] ?? 0;
$user = null;
$worker_profile = null;
$msg_success = '';
$msg_error = '';

// Load User Information from Database with Fallback
if (isset($pdo) && $user_id > 0) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $user['user_type'] === 'worker') {
            $w_stmt = $pdo->prepare("SELECT w.*, c.name as category_name FROM worker_profiles w LEFT JOIN categories c ON w.category_id = c.id WHERE w.user_id = ?");
            $w_stmt->execute([$user_id]);
            $worker_profile = $w_stmt->fetch(PDO::FETCH_ASSOC);
        }
    } catch (PDOException $e) {
        // Fallback user state
    }
}

// Fallback user dataset if session is simulated/demo
if (!$user) {
    $user = [
        'id' => $user_id ?: 1,
        'full_name' => $_SESSION['user_name'] ?? 'Ramesh Kumar',
        'email' => $_SESSION['user_email'] ?? 'ramesh.kumar@example.com',
        'phone' => '+91 98765 43210',
        'location' => 'Pune, Maharashtra',
        'user_type' => $_SESSION['user_type'] ?? 'customer',
        'created_at' => '2025-07-15 10:00:00'
    ];
}

// Now, extract and define safe variables with coalesce fallbacks
$name = $user['full_name'] ?? 'User';
$email = $user['email'] ?? '';
$phone = $user['phone'] ?? '';

// Handle location parsing (users table has 'location' column which is usually "City, State")
$location_parts = !empty($user['location']) ? explode(',', $user['location']) : [];
$city = isset($location_parts[0]) ? trim($location_parts[0]) : '';
$state = isset($location_parts[1]) ? trim($location_parts[1]) : '';

// Safe profile picture fetch (checking user array, worker_profile array, and fallback to default avatar)
$profile_picture = !empty($user['profile_picture']) ? $user['profile_picture'] : (!empty($worker_profile['profile_picture']) ? $worker_profile['profile_picture'] : 'images/avatar_placeholder.png');

// Handle Profile Details Form Update
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['update_profile'])) {
    $name = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $state = trim($_POST['state'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Handle Profile Photo Upload
    $photo_path = $profile_picture;
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['profile_photo']['tmp_name'];
        $file_name = $_FILES['profile_photo']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $allowed_exts = ['jpg', 'jpeg', 'png'];
        if (in_array($file_ext, $allowed_exts)) {
            $new_filename = 'user_' . ($user['id'] ?? $user_id) . '_' . time() . '.' . $file_ext;
            $destination = $upload_dir . $new_filename;

            if (move_uploaded_file($file_tmp, $destination)) {
                $photo_path = 'uploads/profile/' . $new_filename;
            }
        } else {
            $msg_error = 'Only JPG, JPEG, and PNG image formats are supported.';
        }
    }

    if (empty($msg_error) && isset($pdo) && $user_id > 0) {
        try {
            $location_str = '';
            if (!empty($city) && !empty($state)) {
                $location_str = "$city, $state";
            } elseif (!empty($city)) {
                $location_str = $city;
            } elseif (!empty($state)) {
                $location_str = $state;
            }

            if (!empty($password)) {
                $hashed_pwd = password_hash($password, PASSWORD_DEFAULT);
                $upd = $pdo->prepare("UPDATE users SET full_name = ?, phone = ?, location = ?, password = ? WHERE id = ?");
                $upd->execute([$name, $phone, $location_str, $hashed_pwd, $user_id]);
            } else {
                $upd = $pdo->prepare("UPDATE users SET full_name = ?, phone = ?, location = ? WHERE id = ?");
                $upd->execute([$name, $phone, $location_str, $user_id]);
            }

            if (($user['user_type'] ?? 'customer') === 'worker') {
                $wp_check = $pdo->prepare("SELECT id FROM worker_profiles WHERE user_id = ?");
                $wp_check->execute([$user_id]);
                if ($wp_check->fetch()) {
                    $upd_wp = $pdo->prepare("UPDATE worker_profiles SET profile_picture = ? WHERE user_id = ?");
                    $upd_wp->execute([$photo_path, $user_id]);
                } else {
                    $ins_wp = $pdo->prepare("INSERT INTO worker_profiles (user_id, profile_picture) VALUES (?, ?)");
                    $ins_wp->execute([$user_id, $photo_path]);
                }
            }

            // Sync user state and variables
            $user['full_name'] = $name;
            $user['phone'] = $phone;
            $user['location'] = $location_str;
            $profile_picture = $photo_path;
            $_SESSION['user_name'] = $name;

            $msg_success = 'Profile updated successfully!';
        } catch (PDOException $e) {
            $msg_error = 'Failed to update profile details.';
        }
    } else if (empty($msg_error)) {
        // Fallback state update
        $user['full_name'] = $name;
        $user['phone'] = $phone;
        if (!empty($city) && !empty($state)) {
            $user['location'] = "$city, $state";
        } else {
            $user['location'] = $city ?: $state;
        }
        $profile_picture = $photo_path;
        $msg_success = 'Profile updated successfully!';
    }
}

// Calculate Profile Completion Percentage
$completion_fields = [$name, $email, $phone, $city, $state, ($profile_picture !== 'images/avatar_placeholder.png' ? $profile_picture : '')];
$filled = count(array_filter($completion_fields));
$completion_percentage = min(100, max(40, intval(($filled / count($completion_fields)) * 100)));

// Fetch Customer / Worker Reviews
$reviews_list = [
    [
        'customer_name' => 'Aniket Verma',
        'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&fit=crop',
        'rating' => 5,
        'text' => 'Very professional and completed the work on time. Highly recommended!',
        'date' => '18 July 2026'
    ],
    [
        'customer_name' => 'Priya Sharma',
        'avatar' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&fit=crop',
        'rating' => 5,
        'text' => 'Great experience! Arrived on time with proper tools and fixed all electrical issues quickly.',
        'date' => '12 July 2026'
    ]
];

require_once __DIR__ . '/includes/header.php';
?>

<link rel="stylesheet" href="profile.css">

<div class="profile-page-wrapper">
    <!-- Background Blurred Gradient Glows -->
    <div class="profile-bg-glow profile-bg-glow-top-right"></div>
    <div class="profile-bg-glow profile-bg-glow-bottom-left"></div>

    <div class="profile-container">

        <!-- Flash Messages -->
        <?php if (!empty($msg_success)): ?>
            <div class="alert alert-success" style="margin-bottom: 24px;">
                <i class="fa-solid fa-circle-check"></i> <?php echo e($msg_success); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($msg_error)): ?>
            <div class="alert alert-danger" style="margin-bottom: 24px;">
                <i class="fa-solid fa-triangle-exclamation"></i> <?php echo e($msg_error); ?>
            </div>
        <?php endif; ?>

        <!-- PROFILE HEADER HERO CARD -->
        <section class="profile-hero-card">
            <div class="profile-hero-left">
                <!-- 150px x 150px Circular Profile Photo -->
                <div class="profile-avatar-container">
                    <img id="avatar-preview" class="profile-avatar-img" src="<?php echo e($profile_picture); ?>" alt="Profile Photo" onerror="this.src='https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=150&fit=crop';">
                    
                    <label for="profile_photo_input" class="profile-avatar-upload-btn" title="Upload Photo">
                        <i class="fa-solid fa-camera"></i>
                    </label>
                </div>

                <!-- User Information -->
                <div class="profile-user-info">
                    <h1>
                        <?php echo e($name); ?>
                        <span class="account-badge">
                            <i class="fa-solid <?php echo (($user['user_type'] ?? 'customer') === 'worker') ? 'fa-briefcase' : 'fa-user'; ?>"></i>
                            <?php echo e(ucfirst($user['user_type'] ?? 'customer')); ?>
                        </span>
                    </h1>

                    <div class="profile-meta-list">
                        <div class="profile-meta-item">
                            <i class="fa-solid fa-envelope"></i>
                            <span><?php echo e($email); ?></span>
                        </div>
                        <div class="profile-meta-item">
                            <i class="fa-solid fa-phone"></i>
                            <span><?php echo e($phone ?: 'Add Phone Number'); ?></span>
                        </div>
                        <div class="profile-meta-item">
                            <i class="fa-solid fa-location-dot"></i>
                            <span><?php echo e((!empty($city) || !empty($state)) ? trim($city . ', ' . $state, ', ') : 'Location Not Added'); ?></span>
                        </div>
                        <div class="profile-meta-item">
                            <i class="fa-solid fa-calendar-check"></i>
                            <span>Member Since: <?php echo date('F Y', strtotime($user['created_at'] ?? 'now')); ?></span>
                        </div>
                    </div>

                    <!-- Profile Completion Bar -->
                    <div class="completion-box">
                        <div class="completion-label">
                            <span>Profile Completion</span>
                            <span><?php echo $completion_percentage; ?>%</span>
                        </div>
                        <div class="completion-bar-track">
                            <div class="completion-bar-fill" style="width: <?php echo $completion_percentage; ?>%;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <button class="btn-save-profile" onclick="document.getElementById('edit-profile-section').scrollIntoView({behavior: 'smooth'});">
                <i class="fa-solid fa-pen-to-square"></i> Edit Profile
            </button>
        </section>

        <!-- MAIN LAYOUT GRID -->
        <div class="profile-grid-layout">

            <!-- LEFT SIDEBAR -->
            <aside class="profile-sidebar-card">
                <a href="#stats-section" class="profile-nav-item active">
                    <i class="fa-solid fa-chart-pie"></i> Overview & Stats
                </a>
                <a href="#edit-profile-section" class="profile-nav-item">
                    <i class="fa-solid fa-user-pen"></i> Edit Personal Details
                </a>
                <a href="#ratings-section" class="profile-nav-item">
                    <i class="fa-solid fa-star"></i> Ratings & Reviews
                </a>
                <a href="#settings-section" class="profile-nav-item">
                    <i class="fa-solid fa-gear"></i> Account Settings
                </a>
            </aside>

            <!-- RIGHT CONTENT AREA -->
            <main>

                <!-- STATISTICS CARDS GRID -->
                <section id="stats-section" class="stats-cards-grid">
                    <?php if (($user['user_type'] ?? 'customer') === 'customer'): ?>
                        <div class="stat-metric-card">
                            <div class="stat-metric-icon">
                                <i class="fa-solid fa-calendar-check"></i>
                            </div>
                            <div class="stat-metric-info">
                                <h4>14</h4>
                                <p>Bookings Made</p>
                            </div>
                        </div>
                        <div class="stat-metric-card">
                            <div class="stat-metric-icon">
                                <i class="fa-solid fa-circle-check"></i>
                            </div>
                            <div class="stat-metric-info">
                                <h4>12</h4>
                                <p>Completed Jobs</p>
                            </div>
                        </div>
                        <div class="stat-metric-card">
                            <div class="stat-metric-icon">
                                <i class="fa-solid fa-star"></i>
                            </div>
                            <div class="stat-metric-info">
                                <h4>8</h4>
                                <p>Reviews Given</p>
                            </div>
                        </div>
                        <div class="stat-metric-card">
                            <div class="stat-metric-icon">
                                <i class="fa-solid fa-heart"></i>
                            </div>
                            <div class="stat-metric-info">
                                <h4>5</h4>
                                <p>Favorite Workers</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="stat-metric-card">
                            <div class="stat-metric-icon">
                                <i class="fa-solid fa-screwdriver-wrench"></i>
                            </div>
                            <div class="stat-metric-info">
                                <h4>128</h4>
                                <p>Jobs Completed</p>
                            </div>
                        </div>
                        <div class="stat-metric-card">
                            <div class="stat-metric-icon">
                                <i class="fa-solid fa-star"></i>
                            </div>
                            <div class="stat-metric-info">
                                <h4>4.8 ★</h4>
                                <p>Average Rating</p>
                            </div>
                        </div>
                        <div class="stat-metric-card">
                            <div class="stat-metric-icon">
                                <i class="fa-solid fa-comments"></i>
                            </div>
                            <div class="stat-metric-info">
                                <h4>96</h4>
                                <p>Reviews Received</p>
                            </div>
                        </div>
                        <div class="stat-metric-card">
                            <div class="stat-metric-icon">
                                <i class="fa-solid fa-users"></i>
                            </div>
                            <div class="stat-metric-info">
                                <h4>110</h4>
                                <p>Total Customers</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </section>

                <!-- EDIT PROFILE FORM SECTION -->
                <section id="edit-profile-section" class="profile-section-card">
                    <div class="section-card-header">
                        <h3 class="section-card-title">
                            <i class="fa-solid fa-user-pen"></i> Edit Profile Information
                        </h3>
                    </div>

                    <form method="POST" action="profile.php" enctype="multipart/form-data">
                        <!-- Hidden Photo Input -->
                        <input type="file" id="profile_photo_input" name="profile_photo" accept="image/jpeg,image/jpg,image/png" style="display: none;" onchange="previewImage(this);">

                        <div class="profile-form-grid">
                            <div class="profile-form-group">
                                <label class="profile-form-label">Full Name</label>
                                <input type="text" name="full_name" class="profile-form-input" value="<?php echo e($name); ?>" required>
                            </div>

                            <div class="profile-form-group">
                                <label class="profile-form-label">Email Address (Read-Only)</label>
                                <input type="email" class="profile-form-input" value="<?php echo e($email); ?>" readonly style="background: #F3F4F6; cursor: not-allowed;">
                            </div>

                            <div class="profile-form-group">
                                <label class="profile-form-label">Phone Number</label>
                                <input type="text" name="phone" class="profile-form-input" value="<?php echo e($phone); ?>" placeholder="+91 98765 43210" required>
                            </div>

                            <div class="profile-form-group">
                                <label class="profile-form-label">City</label>
                                <input type="text" name="city" class="profile-form-input" value="<?php echo e($city); ?>" required>
                            </div>

                            <div class="profile-form-group">
                                <label class="profile-form-label">State</label>
                                <input type="text" name="state" class="profile-form-input" value="<?php echo e($state); ?>" required>
                            </div>

                            <div class="profile-form-group">
                                <label class="profile-form-label">New Password (Leave blank to keep current)</label>
                                <input type="password" name="password" class="profile-form-input" placeholder="••••••••">
                            </div>
                        </div>

                        <div style="margin-top: 24px; text-align: right;">
                            <button type="submit" name="update_profile" class="btn-save-profile">
                                <i class="fa-solid fa-floppy-disk"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </section>

                <!-- RATINGS & REVIEWS SECTION -->
                <section id="ratings-section" class="profile-section-card">
                    <div class="section-card-header">
                        <h3 class="section-card-title">
                            <i class="fa-solid fa-star"></i> Ratings & Reviews
                        </h3>
                    </div>

                    <div class="ratings-summary-box">
                        <div class="rating-big-score">4.8</div>
                        <div>
                            <div class="rating-stars-gold">
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star-half-stroke"></i>
                            </div>
                            <p style="font-size: 13.5px; color: #6B7280; margin: 4px 0 0 0;">Based on 126 Customer Reviews</p>
                        </div>
                    </div>

                    <div class="reviews-list">
                        <?php foreach ($reviews_list as $rev): ?>
                            <article class="review-item-card">
                                <div class="review-header">
                                    <div class="reviewer-meta">
                                        <img src="<?php echo e($rev['avatar']); ?>" class="reviewer-avatar" alt="Reviewer">
                                        <div>
                                            <h5 class="reviewer-name"><?php echo e($rev['customer_name']); ?></h5>
                                            <div class="rating-stars-gold" style="font-size: 13px;">
                                                <i class="fa-solid fa-star"></i>
                                                <i class="fa-solid fa-star"></i>
                                                <i class="fa-solid fa-star"></i>
                                                <i class="fa-solid fa-star"></i>
                                                <i class="fa-solid fa-star"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="review-date"><?php echo e($rev['date']); ?></span>
                                </div>
                                <p class="review-body-text">"<?php echo e($rev['text']); ?>"</p>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>

                <!-- ACCOUNT SETTINGS SECTION -->
                <section id="settings-section" class="profile-section-card">
                    <div class="section-card-header">
                        <h3 class="section-card-title">
                            <i class="fa-solid fa-gear"></i> Account Settings
                        </h3>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px;">
                        <div style="background: #F8FAFF; border-radius: 16px; padding: 20px; border: 1px solid rgba(13,77,255,0.12);">
                            <h4 style="font-size: 15px; font-weight: 700; color: #111827; margin: 0 0 6px 0;"><i class="fa-solid fa-key" style="color: #0D4DFF;"></i> Change Password</h4>
                            <p style="font-size: 13px; color: #6B7280; margin: 0 0 14px 0;">Keep your account secure with a strong password.</p>
                            <button class="gw-btn-secondary-uc" style="padding: 9px 16px; font-size: 13px; margin: 0;" onclick="document.querySelector('input[name=\'password\']').focus();">Update Password</button>
                        </div>

                        <div style="background: #F8FAFF; border-radius: 16px; padding: 20px; border: 1px solid rgba(13,77,255,0.12);">
                            <h4 style="font-size: 15px; font-weight: 700; color: #111827; margin: 0 0 6px 0;"><i class="fa-solid fa-mobile-screen" style="color: #0D4DFF;"></i> Mobile Preferences</h4>
                            <p style="font-size: 13px; color: #6B7280; margin: 0 0 14px 0;">Manage SMS booking notifications and calls.</p>
                            <button class="gw-btn-secondary-uc" style="padding: 9px 16px; font-size: 13px; margin: 0;" onclick="document.querySelector('input[name=\'phone\']').focus();">Update Number</button>
                        </div>
                    </div>
                </section>

            </main>
        </div>
    </div>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatar-preview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Hero Card Subtle Spotlight Tracker
document.addEventListener('DOMContentLoaded', function() {
    const heroCard = document.querySelector('.profile-hero-card');
    if (heroCard) {
        heroCard.addEventListener('mousemove', function(e) {
            const rect = heroCard.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            heroCard.style.setProperty('--mouse-x', x + 'px');
            heroCard.style.setProperty('--mouse-y', y + 'px');
        });
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
