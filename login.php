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

<<<<<<< HEAD
<style>
/* Custom Auth Layout Overrides for Premium Design */
.auth-wrapper {
    max-width: 1000px !important;
    width: 100%;
    margin: 3rem auto !important;
    padding: 0 1.5rem;
}

.auth-split-card {
    display: flex;
    background-color: var(--white);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-lg);
    overflow: hidden;
    min-height: 600px;
    position: relative;
    transition: var(--transition);
}

.auth-split-card:hover {
    box-shadow: 0 30px 60px -15px rgba(16, 24, 40, 0.15);
    border-color: rgba(18, 69, 197, 0.25);
}

/* Left Brand Panel Styles */
.auth-brand-panel {
    flex: 1.1;
    background: linear-gradient(135deg, #090d16 0%, #1245C5 100%);
    color: #ffffff;
    padding: 3.5rem 3rem;
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.abstract-shape {
    position: absolute;
    border-radius: 50%;
    border: 2px dashed rgba(255, 255, 255, 0.08);
    pointer-events: none;
}

.shape-1 {
    width: 320px;
    height: 320px;
    top: -80px;
    right: -80px;
    animation: rotateShape 60s linear infinite;
}

.shape-2 {
    width: 220px;
    height: 220px;
    bottom: -60px;
    left: -60px;
    animation: rotateShapeInverse 45s linear infinite;
}

@keyframes rotateShape {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

@keyframes rotateShapeInverse {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(-360deg); }
}

.brand-header-mini {
    font-size: 1.35rem;
    font-weight: 800;
    letter-spacing: -0.02em;
    display: flex;
    align-items: center;
    gap: 0.6rem;
    color: #ffffff;
    opacity: 0.95;
}

.brand-header-mini i {
    color: #3b82f6;
    animation: pulseIcon 3s infinite ease-in-out;
}

@keyframes pulseIcon {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

.brand-main-content {
    margin: 2.5rem 0;
}

.brand-title {
    font-size: 2.5rem;
    font-weight: 800;
    line-height: 1.15;
    margin-bottom: 1.25rem;
    color: #ffffff !important;
    letter-spacing: -0.02em;
}

.brand-title span {
    color: #93c5fd;
}

.brand-subtitle {
    font-size: 1.05rem;
    line-height: 1.6;
    color: rgba(255, 255, 255, 0.8) !important;
    margin-bottom: 2.5rem;
    font-weight: 400;
}

/* Badges Stack */
.brand-badges-stack {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.brand-badge-card {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: var(--radius-md);
    padding: 1.1rem 1.25rem;
    display: flex;
    align-items: center;
    gap: 1.25rem;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    transition: var(--transition);
}

.brand-badge-card:hover {
    background: rgba(255, 255, 255, 0.08);
    border-color: rgba(255, 255, 255, 0.2);
    transform: translateY(-3px);
}

.brand-badge-card.highlight-glow {
    border-color: rgba(16, 185, 129, 0.35);
    background: rgba(16, 185, 129, 0.07);
}

.badge-icon {
    width: 44px;
    height: 44px;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
    flex-shrink: 0;
}

.badge-icon.green-shield {
    background-color: rgba(16, 185, 129, 0.2);
    color: #34d399;
}

.badge-text-group {
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
}

.badge-title {
    font-size: 0.95rem;
    font-weight: 700;
    color: #ffffff;
}

.badge-desc {
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.7);
}

.avatar-stack {
    display: flex;
    align-items: center;
}

.avatar-stack-container {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    border: 2px solid #0f1d43;
    margin-right: -10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 700;
    color: #ffffff;
    box-shadow: var(--shadow-sm);
}

.stars-container {
    display: flex;
    flex-direction: column;
}

.stars {
    color: #fbbf24;
    font-size: 0.85rem;
    display: flex;
    gap: 0.15rem;
}

/* Stats Row */
.brand-stats-row {
    display: flex;
    gap: 3rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    padding-top: 1.5rem;
    margin-top: 1rem;
}

.mini-stat {
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
}

.stat-num {
    font-size: 1.85rem;
    font-weight: 800;
    color: #ffffff;
    line-height: 1;
}

.stat-lbl {
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.65);
    font-weight: 500;
}

/* Right Form Panel Styles */
.auth-form-panel {
    flex: 1;
    padding: 4rem 3.5rem 3.5rem 3.5rem;
    display: flex;
    flex-direction: column;
    justify-content: center;
    position: relative;
    background-color: var(--white);
}

/* Language Selector Dropdown */
.lang-dropdown {
    position: absolute;
    top: 1.5rem;
    right: 2rem;
    z-index: 20;
}

.lang-btn {
    background: var(--white);
    border: 1px solid var(--border-color);
    padding: 0.55rem 0.9rem;
    border-radius: var(--radius-md);
    font-family: var(--font-sans);
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--text-dark);
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    box-shadow: var(--shadow-sm);
    transition: var(--transition);
}

.lang-btn:hover {
    background-color: var(--light-bg);
    border-color: var(--primary);
    transform: translateY(-1px);
}

.lang-menu {
    display: none;
    position: absolute;
    top: 100%;
    right: 0;
    margin-top: 0.5rem;
    background-color: var(--white);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-lg);
    width: 170px;
    overflow: hidden;
    animation: slideDown 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes slideDown {
    from { opacity: 0; transform: translateY(-8px); }
    to { opacity: 1; transform: translateY(0); }
}

.lang-menu.show {
    display: block;
}

.lang-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.65rem 1rem;
    font-size: 0.85rem;
    font-weight: 500;
    color: var(--text-dark);
    transition: var(--transition);
    cursor: pointer;
    text-decoration: none;
    border-bottom: 1px solid rgba(0,0,0,0.02);
}

.lang-item:last-child {
    border-bottom: none;
}

.lang-item:hover {
    background-color: var(--light-bg);
    color: var(--primary);
    padding-left: 1.15rem;
}

.lang-item.active {
    background-color: var(--primary-light);
    color: var(--primary);
    font-weight: 700;
}

.lang-item .check-icon {
    font-size: 0.75rem;
}

.auth-form-header {
    margin-bottom: 2.25rem;
}

.auth-form-header h2 {
    font-size: 2rem;
    font-weight: 800;
    color: var(--dark-navy);
    margin-bottom: 0.5rem;
    letter-spacing: -0.02em;
}

.auth-form-header p {
    font-size: 0.95rem;
    color: var(--text-muted);
}

/* Modern Input Styling */
.input-icon-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.input-prefix-icon {
    position: absolute;
    left: 1rem;
    color: var(--text-muted);
    font-size: 1rem;
    transition: var(--transition);
    pointer-events: none;
}

.form-control {
    width: 100%;
    padding: 0.85rem 1rem 0.85rem 2.85rem;
    background-color: var(--white);
    border: 1.5px solid var(--border-color);
    border-radius: var(--radius-md);
    color: var(--text-dark);
    font-size: 0.95rem;
    transition: var(--transition);
}

.form-control:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 4px var(--primary-light);
    outline: none;
}

.form-control:focus + .input-prefix-icon {
    color: var(--primary);
}

.password-toggle-btn {
    position: absolute;
    right: 1rem;
    background: none;
    border: none;
    color: var(--text-muted);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.05rem;
    padding: 0.25rem;
    transition: var(--transition);
}

.password-toggle-btn:hover {
    color: var(--primary);
}

/* Remember & Forgot Pass */
.form-options {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    font-size: 0.88rem;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--text-dark);
    cursor: pointer;
    font-weight: 500;
}

.checkbox-label input {
    width: 16px;
    height: 16px;
    border-radius: 4px;
    border: 1.5px solid var(--border-color);
    cursor: pointer;
}

.forgot-pass-link {
    color: var(--primary);
    font-weight: 600;
    text-decoration: none;
    transition: var(--transition);
}

.forgot-pass-link:hover {
    color: var(--primary-hover);
    text-decoration: underline;
}

/* Button & Footer Overrides */
.btn-primary-auth {
    background-color: var(--primary);
    color: #ffffff !important;
    width: 100%;
    padding: 0.85rem 1.5rem;
    border-radius: var(--radius-md);
    font-weight: 700;
    font-size: 1rem;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0.5rem;
    border: none;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(18, 69, 197, 0.15);
    transition: var(--transition);
}

.btn-primary-auth:hover {
    background-color: var(--primary-hover);
    transform: translateY(-1px);
    box-shadow: 0 8px 20px rgba(18, 69, 197, 0.25);
}

.btn-primary-auth:active {
    transform: translateY(0);
}

.auth-footer {
    text-align: center;
    margin-top: 2rem;
    color: var(--text-muted);
    font-size: 0.95rem;
}

.auth-footer a {
    color: var(--primary);
    font-weight: 700;
    text-decoration: none;
}

.auth-footer a:hover {
    text-decoration: underline;
    color: var(--primary-hover);
}

/* Response/Validation Alerts */
.alert-container-mini {
    margin-bottom: 1.5rem;
}

.alert-mini {
    padding: 0.85rem 1.1rem;
    border-radius: var(--radius-md);
    font-size: 0.9rem;
    font-weight: 500;
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    border: 1.5px solid transparent;
}

.alert-mini-danger {
    background-color: var(--danger-light);
    color: var(--danger);
    border-color: rgba(239, 68, 68, 0.15);
}

.alert-mini-danger ul {
    margin: 0;
    padding-left: 1.1rem;
}

.alert-mini-danger li {
    margin-bottom: 0.25rem;
}

.alert-mini-danger li:last-child {
    margin-bottom: 0;
}

/* Responsive Overrides */
@media (max-width: 900px) {
    .auth-wrapper {
        margin: 2rem auto !important;
    }
    .auth-split-card {
        flex-direction: column;
        min-height: auto;
    }
    .auth-brand-panel {
        padding: 3rem 2rem;
    }
    .brand-main-content {
        margin: 1.5rem 0;
    }
    .brand-title {
        font-size: 2rem;
    }
    .brand-stats-row {
        gap: 2rem;
    }
    .auth-form-panel {
        padding: 3.5rem 2rem;
    }
    .lang-dropdown {
        top: 1.5rem;
        right: 1.5rem;
    }
}
</style>

<div class="auth-wrapper">
    <div class="auth-split-card">
        <!-- Left Side: Brand Panel with Trust & Value Props -->
        <div class="auth-brand-panel">
            <!-- Animated decorative shapes -->
            <div class="abstract-shape shape-1"></div>
            <div class="abstract-shape shape-2"></div>
            
            <div class="brand-header-mini">
                <i class="fa-solid fa-briefcase"></i> GoWorker
            </div>
            
            <div class="brand-main-content">
                <h1 class="brand-title">Find Trusted Local <span>Workers Near You</span></h1>
                <p class="brand-subtitle">Connect with skilled local workers, compare services, and get your work done with confidence.</p>
                
                <!-- Trust Badges -->
                <div class="brand-badges-stack">
                    <div class="brand-badge-card highlight-glow">
                        <div class="badge-icon green-shield">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <div class="badge-text-group">
                            <div class="badge-title">ID Verified &amp; Trusted</div>
                            <div class="badge-desc">100% verified identities &amp; credentials</div>
                        </div>
                    </div>
                    
                    <div class="brand-badge-card">
                        <div class="avatar-stack-container">
                            <div class="avatar-stack">
                                <div class="avatar" style="background-color: #6366f1;">JD</div>
                                <div class="avatar" style="background-color: #ec4899;">AS</div>
                                <div class="avatar" style="background-color: #10b981;">MK</div>
                            </div>
                            <div class="stars-container">
                                <div class="badge-title">4.8 / 5 Rating</div>
                                <div class="stars">
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star-half-stroke"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Statistics -->
            <div class="brand-stats-row">
                <div class="mini-stat">
                    <div class="stat-num">15K+</div>
                    <div class="stat-lbl">Verified Workers</div>
                </div>
                <div class="mini-stat">
                    <div class="stat-num">10K+</div>
                    <div class="stat-lbl">Happy Customers</div>
                </div>
            </div>
        </div>
        
        <!-- Right Side: Login Form Panel -->
        <div class="auth-form-panel">
            <!-- Custom Language Dropdown (as shown in design) -->
            <div class="lang-dropdown">
                <button type="button" class="lang-btn" id="lang-btn">
                    <i class="fa-solid fa-globe"></i>
                    <span id="current-lang">English</span>
                    <i class="fa-solid fa-chevron-down"></i>
                </button>
                <div class="lang-menu" id="lang-menu">
                    <a href="javascript:void(0);" class="lang-item active" data-lang="en">
                        <span>🇺🇸 English</span>
                        <i class="fa-solid fa-check check-icon"></i>
                    </a>
                    <a href="javascript:void(0);" class="lang-item" data-lang="hi">
                        <span>🇮🇳 हिंदी</span>
                    </a>
                    <a href="javascript:void(0);" class="lang-item" data-lang="mr">
                        <span>🇮🇳 मराठी</span>
                    </a>
                    <a href="javascript:void(0);" class="lang-item" data-lang="gu">
                        <span>🇮🇳 ગુજરાતી</span>
                    </a>
                    <a href="javascript:void(0);" class="lang-item" data-lang="ta">
                        <span>🇮🇳 தமிழ்</span>
                    </a>
                    <a href="javascript:void(0);" class="lang-item" data-lang="kn">
                        <span>🇮🇳 ಕನ್ನಡ</span>
                    </a>
                </div>
            </div>
            
            <div class="auth-form-header">
                <h2>Welcome Back</h2>
                <p>Log in to access your GoWorker dashboard</p>
            </div>

            <!-- Error Alerts display -->
            <?php if (!empty($errors)): ?>
                <div class="alert-container-mini">
                    <div class="alert-mini alert-mini-danger">
                        <i class="fa-solid fa-circle-exclamation" style="margin-top: 0.15rem; font-size: 1rem;"></i>
                        <div>
                            <ul style="margin: 0; padding-left: 1rem;">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST" class="auth-form">
                <?php echo csrf_field(); ?>

                <!-- Email Address -->
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-icon-wrapper">
                        <i class="fa-solid fa-envelope input-prefix-icon"></i>
                        <input type="email" id="email" name="email" class="form-control" placeholder="john@example.com" value="<?php echo e($email); ?>" required autocomplete="email">
                    </div>
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-icon-wrapper">
                        <i class="fa-solid fa-lock input-prefix-icon"></i>
                        <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required autocomplete="current-password">
                        <button type="button" class="password-toggle-btn" id="password-toggle-btn" aria-label="Toggle password visibility">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Form Options -->
                <div class="form-options">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember" id="remember">
                        Remember me
                    </label>
                    <a href="javascript:void(0);" class="forgot-pass-link">Forgot Password?</a>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-primary-auth">
                    <i class="fa-solid fa-right-to-bracket"></i> Login
                </button>
            </form>

            <div class="auth-footer">
                Don't have an account? <a href="signup.php">Register here</a>
            </div>
=======
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
>>>>>>> f1effcd0d191b2b824b0409e7208519f9a9023d1
        </div>
    </div>
</div>

<<<<<<< HEAD
<script>
document.addEventListener('DOMContentLoaded', () => {
    // --- Custom Language Switcher Dropdown Logic ---
    const langBtn = document.getElementById('lang-btn');
    const langMenu = document.getElementById('lang-menu');
    const currentLangText = document.getElementById('current-lang');
    const langItems = document.querySelectorAll('.lang-item');

    if (langBtn && langMenu) {
        // Toggle dropdown open/close
        langBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            langMenu.classList.toggle('show');
        });

        // Handle item selection
        langItems.forEach(item => {
            item.addEventListener('click', (e) => {
                e.stopPropagation();
                
                // Clear active checkmark icon from others
                langItems.forEach(i => {
                    i.classList.remove('active');
                    const check = i.querySelector('.check-icon');
                    if (check) check.remove();
                });

                // Set this item as active
                item.classList.add('active');
                
                // Append checkmark icon
                const checkIcon = document.createElement('i');
                checkIcon.className = 'fa-solid fa-check check-icon';
                item.appendChild(checkIcon);

                // Update trigger button label (strip emoji for brevity)
                const selectedText = item.querySelector('span').innerText;
                currentLangText.innerText = selectedText.split(' ').slice(1).join(' ') || selectedText;
                
                // Close dropdown
                langMenu.classList.remove('show');
            });
        });

        // Close dropdown when clicking anywhere outside
        document.addEventListener('click', (e) => {
            if (!langBtn.contains(e.target) && !langMenu.contains(e.target)) {
                langMenu.classList.remove('show');
            }
        });
    }

    // --- Password Toggle Visibility Logic ---
    const passwordInput = document.getElementById('password');
    const passwordToggleBtn = document.getElementById('password-toggle-btn');

    if (passwordInput && passwordToggleBtn) {
        passwordToggleBtn.addEventListener('click', () => {
            const icon = passwordToggleBtn.querySelector('i');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.className = 'fa-solid fa-eye-slash';
            } else {
                passwordInput.type = 'password';
                icon.className = 'fa-solid fa-eye';
            }
        });
    }
});
</script>
=======
<script src="js/login.js"></script>
>>>>>>> f1effcd0d191b2b824b0409e7208519f9a9023d1

<?php
require_once __DIR__ . '/includes/footer.php';
?>
