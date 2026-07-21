<?php
/**
 * GoWorker User Signup
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

// Redirect to dashboard if already logged in
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_type'] === 'customer') {
        redirect('customer-dashboard.php');
    } else {
        redirect('worker-dashboard.php');
    }
}

$errors = [];
$full_name = '';
$email = '';
$phone = '';
$location = '';
$user_type = 'customer'; // Default role

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF Check
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($csrf_token)) {
        $errors[] = 'Invalid request verification (CSRF token mismatch). Please try again.';
    } else {
        // Collect and sanitize inputs
        $full_name = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $user_type = $_POST['user_type'] ?? 'customer';

        // Validation checks
        if (empty($full_name)) {
            $errors[] = 'Full Name is required.';
        }
        if (empty($email)) {
            $errors[] = 'Email address is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        }
        
        if (empty($password)) {
            $errors[] = 'Password is required.';
        } elseif (strlen($password) < 6) {
            $errors[] = 'Password must be at least 6 characters long.';
        }

        if ($password !== $confirm_password) {
            $errors[] = 'Password confirmation does not match.';
        }

        if (!in_array($user_type, ['customer', 'worker'])) {
            $errors[] = 'Invalid user type selected.';
        }

        // Basic phone validation (optional check but nice to check for digits/spaces)
        if (!empty($phone) && !preg_match('/^[0-9+\s\-()]{7,20}$/', $phone)) {
            $errors[] = 'Please enter a valid phone number.';
        }

        // Check if email already exists in DB
        if (empty($errors)) {
            try {
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
                $stmt->execute(['email' => $email]);
                if ($stmt->fetch()) {
                    $errors[] = 'This email address is already registered.';
                }
            } catch (PDOException $e) {
                error_log("Database check error on signup.php: " . $e->getMessage());
                $errors[] = 'A database error occurred. Please try again later.';
            }
        }

        // Register the user
        if (empty($errors)) {
            try {
                $pdo->beginTransaction();

                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Insert user
                $stmt = $pdo->prepare("
                    INSERT INTO users (full_name, email, password, phone, location, user_type) 
                    VALUES (:full_name, :email, :password, :phone, :location, :user_type)
                ");
                $stmt->execute([
                    'full_name' => $full_name,
                    'email' => $email,
                    'password' => $hashed_password,
                    'phone' => !empty($phone) ? $phone : null,
                    'location' => !empty($location) ? $location : null,
                    'user_type' => $user_type
                ]);

                $user_id = $pdo->lastInsertId();

                // If user is a worker, create profile record
                if ($user_type === 'worker') {
                    $stmt = $pdo->prepare("
                        INSERT INTO worker_profiles (user_id, location, experience_years) 
                        VALUES (:user_id, :location, 0)
                    ");
                    $stmt->execute([
                        'user_id' => $user_id,
                        'location' => !empty($location) ? $location : null
                    ]);
                }

                $pdo->commit();

                // Flash success message and redirect
                flash('success', 'Registration successful! Please log in below.');
                redirect('login.php');

            } catch (PDOException $e) {
                $pdo->rollBack();
                error_log("Database insert error on signup.php: " . $e->getMessage());
                $errors[] = 'Failed to register. Please check database settings and try again.';
            }
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<style>
/* Reset global header/footer */
header, footer, .container[style*="margin-top: 1.5rem; margin-bottom: -1.5rem;"] {
    display: none !important;
}

/* Base Reset and Layout */
body {
    background-color: #FFFFFF !important;
    min-height: 100vh;
    margin: 0;
    padding: 0;
    overflow-x: hidden;
    display: block;
}

.signup-container {
    display: flex;
    min-height: 100vh;
    width: 100%;
}

/* Left Panel */
.left-panel {
    width: 40%;
    background: linear-gradient(135deg, #0D4DFF 0%, #4A7BFF 100%);
    color: #FFFFFF;
    padding: 3rem 3rem 0 3rem;
    display: flex;
    flex-direction: column;
    position: relative;
    overflow: hidden;
}

/* Right Panel */
.right-panel {
    width: 60%;
    background-color: #FFFFFF;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 2rem 3rem;
    position: relative;
}

/* Left Panel Content Styling */
.left-logo-area {
    margin-bottom: 2rem;
}

.left-logo {
    font-family: 'Outfit', sans-serif;
    font-size: 1.8rem;
    font-weight: 800;
    color: #FFFFFF;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
}

.left-logo span {
    font-weight: 400;
}

.left-logo-tagline {
    font-size: 0.75rem;
    color: rgba(255, 255, 255, 0.7);
    margin-left: 2.3rem;
    margin-top: -0.25rem;
    letter-spacing: 0.05em;
    font-family: 'Inter', sans-serif;
}

.left-hero-content h1 {
    font-family: 'Outfit', sans-serif;
    font-size: 2.4rem;
    font-weight: 700;
    line-height: 1.25;
    color: #FFFFFF;
    margin-bottom: 1rem;
}

.left-hero-content h1 span.accent {
    color: #86A3FF;
}

.left-hero-desc {
    font-family: 'Inter', sans-serif;
    font-size: 0.95rem;
    line-height: 1.5;
    color: rgba(255, 255, 255, 0.8);
    margin-bottom: 1.5rem;
    max-width: 90%;
}

.left-divider {
    height: 1px;
    background-color: rgba(255, 255, 255, 0.15);
    margin-bottom: 1.5rem;
    width: 100%;
}

/* Features */
.features-list {
    display: flex;
    flex-direction: column;
    gap: 1.2rem;
    margin-bottom: 2rem;
    position: relative;
    z-index: 2;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.feature-icon-wrapper {
    background: rgba(255, 255, 255, 0.1);
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.feature-icon-wrapper i {
    color: #FFFFFF;
    font-size: 1.1rem;
}

.feature-text-wrapper h3 {
    font-family: 'Outfit', sans-serif;
    font-size: 1rem;
    font-weight: 600;
    color: #FFFFFF;
    margin-bottom: 0.1rem;
}

.feature-text-wrapper p {
    font-family: 'Inter', sans-serif;
    font-size: 0.85rem;
    color: rgba(255, 255, 255, 0.75);
    margin: 0;
}

/* Workers Graphic */
.workers-graphic-container {
    position: absolute;
    bottom: 0;
    right: 10px;
    width: 260px;
    z-index: 1;
    pointer-events: none;
    display: flex;
    align-items: flex-end;
}

.workers-graphic {
    width: 100%;
    height: auto;
    object-fit: contain;
    display: block;
}

/* Testimonial Card */
.testimonial-card {
    background: rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 16px;
    padding: 1rem 1.25rem;
    width: 320px;
    position: absolute;
    bottom: 2rem;
    left: 3rem;
    z-index: 3;
}

.testimonial-quote {
    font-size: 0.8rem;
    line-height: 1.4;
    color: #FFFFFF;
    margin-bottom: 8px;
    font-style: italic;
}

.testimonial-author-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.testimonial-author {
    font-size: 0.75rem;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.9);
}

.testimonial-stars {
    display: flex;
    gap: 2px;
}

.testimonial-stars i {
    color: #FFC738;
    font-size: 0.7rem;
}

/* Right Panel Elements */
.right-panel-top {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    margin-bottom: 1.5rem;
}

.language-dropdown-container {
    position: relative;
}

.language-select {
    appearance: none;
    background-color: #FFFFFF;
    border: 1px solid #E5E7EB;
    border-radius: 8px;
    padding: 8px 32px 8px 12px;
    font-size: 0.85rem;
    color: #4B5563;
    font-weight: 500;
    cursor: pointer;
    outline: none;
    transition: all 0.2s ease;
}

.language-select:hover {
    border-color: #D1D5DB;
}

.language-dropdown-container::after {
    content: "\f078";
    font-family: "Font Awesome 6 Free";
    font-weight: 900;
    font-size: 0.65rem;
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #9CA3AF;
    pointer-events: none;
}

/* Form Container */
.form-container {
    width: 100%;
    max-width: 520px;
    margin: auto;
}

.signup-header {
    margin-bottom: 1.5rem;
}

.signup-header h2 {
    font-family: 'Inter', sans-serif;
    font-size: 1.8rem;
    font-weight: 700;
    color: #1F2937;
    margin-bottom: 4px;
}

.signup-header p {
    font-size: 0.9rem;
    color: #6B7280;
    margin: 0;
}

/* Tabs */
.signup-tabs {
    display: flex;
    background-color: #F3F4F6;
    padding: 4px;
    border-radius: 10px;
    margin-bottom: 1.5rem;
}

.tab-btn {
    flex: 1;
    border: none;
    background: none;
    padding: 10px;
    font-size: 0.85rem;
    font-weight: 600;
    color: #4B5563;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    transition: all 0.2s ease;
}

.tab-btn.active {
    background-color: #ffffff;
    color: #0D3FB7;
    box-shadow: 0 4px 10px rgba(0,0,0,0.05);
}

/* Form inputs */
.auth-form-custom {
    display: flex;
    flex-direction: column;
}

.form-grid-custom {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-bottom: 1.25rem;
}

.form-group-full {
    grid-column: span 2;
}

.form-group-custom {
    display: flex;
    flex-direction: column;
}

.form-label-custom {
    font-size: 0.8rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 6px;
}

.input-icon-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.prefix-icon {
    position: absolute;
    left: 14px;
    color: #9CA3AF;
    font-size: 0.95rem;
    pointer-events: none;
}

.form-control-custom {
    width: 100%;
    padding: 11px 14px 11px 40px;
    border: 1.5px solid #E5E7EB;
    border-radius: 8px;
    font-size: 0.85rem;
    color: #1F2937;
    outline: none;
    transition: all 0.2s ease;
}

.form-control-custom:focus {
    border-color: #0D3FB7;
    box-shadow: 0 0 0 3px rgba(13,63,183,0.1);
}

.password-toggle-btn {
    position: absolute;
    right: 14px;
    background: none;
    border: none;
    cursor: pointer;
    color: #9CA3AF;
    font-size: 0.95rem;
    padding: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.password-toggle-btn:hover {
    color: #4B5563;
}

/* Buttons */
.btn-signup-custom {
    background-color: #0D3FB7;
    color: #ffffff;
    border: none;
    border-radius: 8px;
    padding: 12px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(13,63,183,0.25);
}

.btn-signup-custom:hover {
    background-color: #0B359C;
    box-shadow: 0 6px 16px rgba(13,63,183,0.35);
}

/* Divider */
.divider-custom {
    display: flex;
    align-items: center;
    text-align: center;
    color: #9CA3AF;
    font-size: 0.75rem;
    font-weight: 600;
    letter-spacing: 0.05em;
    margin: 1.25rem 0;
}

.divider-custom::before, .divider-custom::after {
    content: '';
    flex: 1;
    border-bottom: 1.5px solid #F3F4F6;
}

.divider-custom::before {
    margin-right: 12px;
}

.divider-custom::after {
    margin-left: 12px;
}

/* Google Sign-in */
.btn-google-custom {
    background-color: #ffffff;
    color: #374151;
    border: 1.5px solid #E5E7EB;
    border-radius: 8px;
    padding: 11px;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.2s ease;
}

.btn-google-custom:hover {
    background-color: #F9FAFB;
    border-color: #D1D5DB;
}

.btn-google-custom img {
    width: 16px;
    height: 16px;
}

/* Redirect text */
.login-redirect-text {
    text-align: center;
    font-size: 0.85rem;
    color: #4B5563;
    margin-top: 1.25rem;
}

.login-redirect-text a {
    color: #0D3FB7;
    text-decoration: none;
    font-weight: 600;
}

.login-redirect-text a:hover {
    text-decoration: underline;
}

/* Security footer */
.right-panel-bottom {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    font-size: 0.75rem;
    color: #9CA3AF;
    margin-top: 1.25rem;
}

.right-panel-bottom i {
    color: #10B981;
    font-size: 1rem;
}

.right-panel-bottom span strong {
    color: #1749D7;
    font-weight: 600;
}

/* Dark Mode Theme overrides */
[data-theme="dark"] body {
    background-color: #090d16 !important;
}

[data-theme="dark"] .right-panel {
    background-color: #121b2d;
    border-left: 1px solid #1e293b;
}

[data-theme="dark"] .language-select {
    background-color: #121b2d;
    border-color: #1e293b;
    color: var(--text-dark, #e2e8f0);
}

[data-theme="dark"] .signup-header h2 {
    color: #F9FAFB;
}

[data-theme="dark"] .signup-header p {
    color: #9CA3AF;
}

[data-theme="dark"] .signup-tabs {
    background-color: #1e293b;
}

[data-theme="dark"] .tab-btn.active {
    background-color: #121b2d;
    color: #4B8DFF;
    border-color: #4B8DFF;
}

[data-theme="dark"] .form-label-custom {
    color: #F9FAFB;
}

[data-theme="dark"] .form-control-custom {
    background-color: #1e293b;
    border-color: #334155;
    color: var(--text-dark, #e2e8f0);
}

[data-theme="dark"] .form-control-custom:focus {
    border-color: #4B8DFF;
    box-shadow: 0 0 0 3px rgba(75, 141, 255, 0.15);
}

[data-theme="dark"] .password-toggle-btn:hover {
    color: #F9FAFB;
}

[data-theme="dark"] .divider-custom::before, [data-theme="dark"] .divider-custom::after {
    border-color: #334155;
}

[data-theme="dark"] .btn-google-custom {
    background-color: #1e293b;
    border-color: #334155;
    color: var(--text-dark, #e2e8f0);
}

[data-theme="dark"] .btn-google-custom:hover {
    background-color: #334155;
}

[data-theme="dark"] .login-redirect-text a {
    color: #4B8DFF;
}

[data-theme="dark"] .right-panel-bottom {
    color: #9CA3AF;
}

[data-theme="dark"] .right-panel-bottom span strong {
    color: #4B8DFF;
}

/* Responsiveness overrides */
@media (max-width: 992px) {
    .signup-container {
        flex-direction: column;
        min-height: 100vh;
    }
    .left-panel {
        width: 100%;
        padding: 30px;
        min-height: auto;
    }
    .right-panel {
        width: 100%;
        padding: 30px 20px;
        min-height: auto;
    }
    .workers-graphic-container {
        display: none !important;
    }
    .testimonial-card {
        position: relative;
        bottom: 0;
        left: 0;
        width: 100%;
        margin-top: 1.5rem;
    }
}

@media (max-width: 768px) {
    .left-panel {
        display: none !important;
    }
    .right-panel {
        width: 100%;
        padding: 40px 15px;
    }
    .form-grid-custom {
        grid-template-columns: 1fr;
    }
    .form-group-full {
        grid-column: span 1;
    }
}
</style>

<div class="signup-container">
    <!-- Left Panel -->
    <div class="left-panel">
        <!-- Top Logo Area -->
        <div class="left-logo-area">
            <a href="index.php" class="left-logo logo">
                <i class="fa-solid fa-screwdriver-wrench"></i> Go<span>Worker</span>
            </a>
            <div class="left-logo-tagline"><?php echo e(__('tagline')); ?></div>
        </div>

        <!-- Hero Content -->
        <div class="left-hero-content">
            <h1><?php echo __('welcome_title'); ?></h1>
            <p class="left-hero-desc">
                <?php echo e(__('welcome_desc')); ?>
            </p>
        </div>

        <div class="left-divider"></div>

        <!-- Features Section -->
        <div class="features-list">
            <!-- Feature 1 -->
            <div class="feature-item">
                <div class="feature-icon-wrapper">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div class="feature-text-wrapper">
                    <h3><?php echo e(__('trusted_workers')); ?></h3>
                    <p><?php echo e(__('trusted_workers_desc')); ?></p>
                </div>
            </div>
            <!-- Feature 2 -->
            <div class="feature-item">
                <div class="feature-icon-wrapper">
                    <i class="fa-regular fa-comment-dots"></i>
                </div>
                <div class="feature-text-wrapper">
                    <h3><?php echo e(__('direct_contact')); ?></h3>
                    <p><?php echo e(__('direct_contact_desc')); ?></p>
                </div>
            </div>
            <!-- Feature 3 -->
            <div class="feature-item">
                <div class="feature-icon-wrapper">
                    <i class="fa-solid fa-indian-rupee-sign"></i>
                </div>
                <div class="feature-text-wrapper">
                    <h3><?php echo e(__('no_hidden_charges')); ?></h3>
                    <p><?php echo e(__('no_hidden_charges_desc')); ?></p>
                </div>
            </div>
        </div>

        <!-- Workers Graphic Image -->
        <div class="workers-graphic-container">
            <img src="assets/images/login-workers.png" alt="GoWorker Professionals" class="workers-graphic" onerror="this.parentElement.style.display='none';">
        </div>

        <!-- Testimonial Card -->
        <div class="testimonial-card">
            <p class="testimonial-quote">“GoWorker helped me find a trusted electrician within minutes. Great experience!”</p>
            <div class="testimonial-author-row">
                <span class="testimonial-author">— Priya S., Pune</span>
                <div class="testimonial-stars">
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Panel -->
    <div class="right-panel">
        <!-- Top Right Controls -->
        <div class="right-panel-top">
            <div class="language-dropdown-container">
                <select class="language-select" aria-label="Language Selector" onchange="window.location.href='?lang='+this.value">
                    <?php foreach ($lang_details as $code => $detail): ?>
                        <option value="<?php echo $code; ?>" <?php echo $current_lang === $code ? 'selected' : ''; ?>>
                            <?php echo $detail['name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Form Area -->
        <div class="form-container">
            <div class="signup-header">
                <h2><?php echo e(__('create_account')); ?></h2>
                <p><?php echo e(__('signup_tagline')); ?></p>
            </div>

            <!-- Error alerts if any -->
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger" style="display: block; margin-bottom: 1.5rem; border-radius: 12px; font-size: 0.9rem;">
                    <ul style="padding-left: 1rem; margin: 0;">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Tabs Switcher -->
            <div class="signup-tabs">
                <button type="button" class="tab-btn active" id="tab-customer">
                    <i class="fa-regular fa-user"></i> <?php echo e(__('customer_signup')); ?>
                </button>
                <button type="button" class="tab-btn" id="tab-worker">
                    <i class="fa-solid fa-briefcase"></i> <?php echo e(__('worker_signup')); ?>
                </button>
            </div>

            <!-- Signup Form -->
            <form action="signup.php" method="POST" class="auth-form-custom">
                <?php echo csrf_field(); ?>
                
                <!-- Hidden Field for role selection -->
                <input type="hidden" name="user_type" id="user_type" value="<?php echo e($user_type); ?>">

                <div class="form-grid-custom">
                    <!-- Full Name -->
                    <div class="form-group-custom form-group-full">
                        <label for="full_name" class="form-label-custom"><?php echo e(__('full_name')); ?></label>
                        <div class="input-icon-wrapper">
                            <i class="fa-regular fa-user prefix-icon"></i>
                            <input type="text" id="full_name" name="full_name" class="form-control-custom" placeholder="<?php echo e(__('fullname_placeholder')); ?>" value="<?php echo e($full_name); ?>" required autocomplete="name">
                        </div>
                    </div>

                    <!-- Email Address -->
                    <div class="form-group-custom form-group-full">
                        <label for="email" class="form-label-custom"><?php echo e(__('email_address')); ?></label>
                        <div class="input-icon-wrapper">
                            <i class="fa-regular fa-envelope prefix-icon"></i>
                            <input type="email" id="email" name="email" class="form-control-custom" placeholder="<?php echo e(__('email_address_placeholder')); ?>" value="<?php echo e($email); ?>" required autocomplete="email">
                        </div>
                    </div>

                    <!-- Phone Number -->
                    <div class="form-group-custom">
                        <label for="phone" class="form-label-custom"><?php echo e(__('mobile_number')); ?></label>
                        <div class="input-icon-wrapper">
                            <i class="fa-solid fa-mobile-screen-button prefix-icon"></i>
                            <input type="tel" id="phone" name="phone" class="form-control-custom" placeholder="<?php echo e(__('mobile_placeholder')); ?>" value="<?php echo e($phone); ?>" autocomplete="tel">
                        </div>
                    </div>

                    <!-- Location -->
                    <div class="form-group-custom">
                        <label for="location" class="form-label-custom"><?php echo e(__('location')); ?></label>
                        <div class="input-icon-wrapper">
                            <i class="fa-solid fa-location-dot prefix-icon"></i>
                            <input type="text" id="location" name="location" class="form-control-custom" placeholder="<?php echo e(__('location_placeholder')); ?>" value="<?php echo e($location); ?>">
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="form-group-custom">
                        <label for="password" class="form-label-custom"><?php echo e(__('password')); ?></label>
                        <div class="input-icon-wrapper">
                            <i class="fa-solid fa-lock prefix-icon"></i>
                            <input type="password" id="password" name="password" class="form-control-custom password-input" placeholder="<?php echo e(__('password_placeholder')); ?>" required autocomplete="new-password">
                            <button type="button" class="password-toggle-btn" id="toggle-password" aria-label="Toggle Password Visibility">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-group-custom">
                        <label for="confirm_password" class="form-label-custom"><?php echo e(__('confirm_password')); ?></label>
                        <div class="input-icon-wrapper">
                            <i class="fa-solid fa-lock prefix-icon"></i>
                            <input type="password" id="confirm_password" name="confirm_password" class="form-control-custom password-input" placeholder="<?php echo e(__('confirm_password_placeholder')); ?>" required autocomplete="new-password">
                            <button type="button" class="password-toggle-btn" id="toggle-confirm-password" aria-label="Toggle Password Visibility">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-signup-custom"><?php echo e(__('create_account_btn')); ?></button>
                <p style="font-size: 0.8rem; color: var(--text-muted); text-align: center; margin-top: 1rem;">
                    By signing up, you agree to the <strong>GoWorker™</strong> <a href="#" onclick="alert('Terms & Conditions:\n1. All service requests are simulations.\n2. Users must respect safety guidelines.\n3. Data is managed securely.')" style="text-decoration: underline; color: var(--primary);">Terms & Conditions</a> and <a href="#" onclick="alert('Privacy Policy:\n1. We protect user profile data.\n2. No third-party data sharing.')" style="text-decoration: underline; color: var(--primary);">Privacy Policy</a>.
                </p>
            </form>

            <!-- Divider -->
            <div class="divider-custom"><?php echo e(__('or_divider')); ?></div>

            <!-- Google Continue -->
            <button type="button" class="btn-google-custom">
                <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg" alt="Google Logo"> <?php echo e(__('continue_google')); ?>
            </button>

            <!-- Login Redirect -->
            <div class="login-redirect-text">
                <?php echo e(__('already_account')); ?> <a href="login.php"><?php echo e(__('login')); ?></a>
            </div>
        </div>

        <!-- Bottom Security badging -->
        <div class="right-panel-bottom">
            <i class="fa-solid fa-shield-halved"></i>
            <span><?php echo __('secure_data'); ?></span>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // --- Tab Switching Logic ---
    const customerTab = document.getElementById('tab-customer');
    const workerTab = document.getElementById('tab-worker');
    const roleInput = document.getElementById('user_type');

    if (customerTab && workerTab && roleInput) {
        customerTab.addEventListener('click', () => {
            customerTab.classList.add('active');
            workerTab.classList.remove('active');
            roleInput.value = 'customer';
        });

        workerTab.addEventListener('click', () => {
            workerTab.classList.add('active');
            customerTab.classList.remove('active');
            roleInput.value = 'worker';
        });
        
        // Sync initial state from hidden field if already set
        if (roleInput.value === 'worker') {
            workerTab.classList.add('active');
            customerTab.classList.remove('active');
        } else {
            customerTab.classList.add('active');
            workerTab.classList.remove('active');
        }
    }

    // --- Password Toggle Visibility Logic (Password Field) ---
    const togglePassword = document.getElementById('toggle-password');
    const passwordInput = document.getElementById('password');

    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', () => {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            const icon = togglePassword.querySelector('i');
            if (icon) {
                if (type === 'text') {
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            }
        });
    }

    // --- Password Toggle Visibility Logic (Confirm Password Field) ---
    const toggleConfirmPassword = document.getElementById('toggle-confirm-password');
    const confirmPasswordInput = document.getElementById('confirm_password');

    if (toggleConfirmPassword && confirmPasswordInput) {
        toggleConfirmPassword.addEventListener('click', () => {
            const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmPasswordInput.setAttribute('type', type);
            
            const icon = toggleConfirmPassword.querySelector('i');
            if (icon) {
                if (type === 'text') {
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            }
        });
    }
});
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
