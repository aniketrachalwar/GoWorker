<?php
/**
 * GoWorker User Login
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
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF Check
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($csrf_token)) {
        $errors[] = 'Invalid request verification (CSRF token mismatch). Please try again.';
    } else {
        // Collect and sanitize inputs
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validation checks
        if (empty($email)) {
            $errors[] = 'Email is required.';
        }
        if (empty($password)) {
            $errors[] = 'Password is required.';
        }

        // Authenticate user
        if (empty($errors)) {
            try {
                $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
                $stmt->execute(['email' => $email]);
                $user = $stmt->fetch();

                if ($user && password_verify($password, $user['password'])) {
                    // Password is correct, set up session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['full_name'] = $user['full_name'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['user_type'] = $user['user_type'];

                    // Flash welcome message and redirect based on role
                    flash('success', "Welcome back, " . e($user['full_name']) . "!");
                    
                    if ($user['user_type'] === 'customer') {
                        redirect('customer-dashboard.php');
                    } else {
                        redirect('worker-dashboard.php');
                    }
                } else {
                    // Invalid email or password
                    $errors[] = 'Invalid email or password.';
                }
            } catch (PDOException $e) {
                error_log("Database connection error on login.php: " . $e->getMessage());
                $errors[] = 'A connection error occurred. Please try again later.';
            }
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<link rel="stylesheet" href="css/login.css">

<div class="login-page-wrapper">
    <!-- Left Panel -->
    <div class="left-panel">
        <!-- Top Logo Area -->
        <div class="left-logo-area">
            <a href="index.php" class="left-logo">
                <svg width="46" height="40" viewBox="0 0 46 40" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-right: 8px;">
                    <path d="M20 10C12 10 7 15 7 23C7 31 12 36 20 36C26 36 30 32 31 27H20V21H37C37.5 29 34 38 22 39C10 40 1 32 1 21C1 10 10 1 23 1C30 1 35 4 38 8L32 13C30 11 26 10 20 10Z" fill="white"/>
                    <path d="M26 15L29 28L32 15L35 28L38 15H42L37 36H33L30 23L27 36H23L18 15H22" fill="white"/>
                    <path d="M38 10L42 6C42.5 5.5 43.5 5.5 44 6L45 7C45.5 7.5 45.5 8.5 45 9L41 13H38V10Z" fill="white"/>
                </svg>
                Go<span>Worker</span>
            </a>
            <div class="left-logo-tagline">Work. Done. Right.</div>
        </div>

        <!-- Hero Content -->
        <div class="left-hero-content">
            <h1>Find Trusted Local<br>Workers. <span class="accent">Anytime,</span><br><span class="accent">Anywhere.</span></h1>
            <p class="left-hero-desc">
                Connect with skilled and unskilled workers in your area. Negotiate directly and get your work done right.
            </p>
        </div>

        <!-- Features Section -->
        <div class="features-list">
            <!-- Feature 1 -->
            <div class="feature-item">
                <div class="feature-icon-wrapper">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div class="feature-text-wrapper">
                    <h3>Verified & Trusted</h3>
                    <p>All workers are ID verified and customer-reviewed.</p>
                </div>
            </div>
            <!-- Feature 2 -->
            <div class="feature-item">
                <div class="feature-icon-wrapper">
                    <i class="fa-regular fa-comment-dots"></i>
                </div>
                <div class="feature-text-wrapper">
                    <h3>Direct Contact</h3>
                    <p>Talk directly with workers and negotiate easily.</p>
                </div>
            </div>
            <!-- Feature 3 -->
            <div class="feature-item">
                <div class="feature-icon-wrapper">
                    <i class="fa-solid fa-indian-rupee-sign"></i>
                </div>
                <div class="feature-text-wrapper">
                    <h3>No Hidden Charges</h3>
                    <p>You pay what you agree with the worker.</p>
                </div>
            </div>
        </div>

        <!-- Workers Graphic Image -->
        <div class="workers-graphic-container">
            <img src="assets/images/login-workers.png" alt="GoWorker Professionals" class="workers-graphic" onerror="this.parentElement.style.display='none';">
        </div>

        <!-- Testimonial Card -->
        <div class="testimonial-card">
            <p class="testimonial-quote">GoWorker helped me find a trusted electrician within minutes. Great experience!</p>
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
        <!-- Top Right Header: Theme and Language -->
        <div class="right-panel-top">
            <!-- Theme Toggle Icon -->
            <button class="theme-toggle-btn" id="login-theme-toggle" title="Toggle Theme" aria-label="Toggle Theme">
                <i class="fa-solid fa-sun"></i>
            </button>
            
            <!-- Language Dropdown -->
            <div class="language-dropdown-container">
                <select class="language-select" aria-label="Language Selector">
                    <option value="en">English</option>
                    <option value="hi">Hindi</option>
                    <option value="mr">Marathi</option>
                </select>
            </div>
        </div>

        <!-- Form Area -->
        <div class="form-container">
            <!-- Welcome Header -->
            <div class="welcome-header">
                <h2>Welcome Back!</h2>
                <p>Login to continue to your account</p>
            </div>

            <!-- Error alerts if any -->
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger" style="display: block; margin-bottom: 1.5rem; border-radius: 8px; font-size: 0.9rem;">
                    <ul style="padding-left: 1rem; margin: 0;">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Tabs Switcher -->
            <div class="login-tabs">
                <button type="button" class="tab-btn active" id="tab-customer">
                    <i class="fa-regular fa-user"></i> Customer Login
                </button>
                <button type="button" class="tab-btn" id="tab-worker">
                    <i class="fa-solid fa-briefcase"></i> Worker Login
                </button>
            </div>

            <!-- Login Form -->
            <form action="login.php" method="POST" class="auth-form-custom">
                <?php echo csrf_field(); ?>
                
                <!-- Hidden field for User Role Switcher UI -->
                <input type="hidden" name="user_role" id="user_role" value="customer">

                <!-- Email or Mobile field -->
                <div class="form-group-custom">
                    <label for="email" class="form-label-custom">Email or Mobile Number</label>
                    <div class="input-icon-wrapper">
                        <i class="fa-regular fa-envelope prefix-icon"></i>
                        <input type="email" id="email" name="email" class="form-control-custom" placeholder="Enter your email or mobile number" value="<?php echo e($email); ?>" required autocomplete="email">
                    </div>
                </div>

                <!-- Password field -->
                <div class="form-group-custom">
                    <label for="password" class="form-label-custom">Password</label>
                    <div class="input-icon-wrapper">
                        <i class="fa-solid fa-lock prefix-icon"></i>
                        <input type="password" id="password" name="password" class="form-control-custom password-input" placeholder="Enter your password" required autocomplete="current-password">
                        <button type="button" class="password-toggle-btn" id="toggle-password" aria-label="Toggle Password Visibility">
                            <i class="fa-regular fa-eye"></i>
                        </button>
                    </div>
                    <!-- Forgot password -->
                    <a href="#" class="forgot-password-link">Forgot Password?</a>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-login-custom">Login</button>
            </form>

            <!-- Divider -->
            <div class="divider-custom">OR</div>

            <!-- Google Continue button -->
            <button type="button" class="btn-google-custom">
                <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg" alt="Google Logo"> Continue with Google
            </button>

            <!-- Bottom Signup Text -->
            <div class="signup-redirect-text">
                Don't have an account? <a href="signup.php">Sign Up</a>
            </div>
        </div>

        <!-- Bottom Security Badging -->
        <div class="right-panel-bottom">
            <i class="fa-solid fa-shield-halved"></i>
            <span>Your data is <strong>safe and secure</strong> with us.</span>
        </div>
    </div>
</div>

<script src="js/login.js"></script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
