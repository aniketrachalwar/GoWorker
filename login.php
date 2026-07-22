<?php
/**
 * GoWorker User Login
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

// Redirect to profile if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: profile.php');
    exit();
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
            if (!isset($pdo) || !$pdo) {
                $errors[] = isset($db_connection_error) ? $db_connection_error : "Database 'goworker' not found. Please import database/goworker.sql in phpMyAdmin.";
            } else {
                try {
                    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
                    $stmt->execute(['email' => $email]);
                    $user = $stmt->fetch();

                    if ($user && password_verify($password, $user['password'])) {
                        $user_name = $user['name'] ?? ($user['full_name'] ?? 'User');

                        // Password is correct, set up session variables
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['full_name'] = $user_name;
                        $_SESSION['email'] = $user['email'];
                        $_SESSION['user_type'] = $user['user_type'];

                        header('Location: profile.php');
                        exit();
                    } else {
                        // Invalid email or password
                        $errors[] = 'Invalid email or password.';
                    }
                } catch (PDOException $e) {
                    error_log("Database query error on login.php: " . $e->getMessage());
                    $errors[] = "Database 'goworker' not found. Please import database/goworker.sql in phpMyAdmin.";
                }
            }
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

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
    background: linear-gradient(135deg, #090d16 0%, #2563eb 100%);
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
            
            <div class="brand-header-mini" style="display: flex; align-items: center; gap: 8px;">
                <img src="images/logo_icon.png" alt="GoWorker Logo" style="height: 28px; width: auto; object-fit: contain;" onerror="this.src='images/logo.jpg';"> GoWorker
            </div>
            
            <div class="brand-main-content">
                <h1 class="brand-title"><?php echo __('brand_title'); ?></h1>
                <p class="brand-subtitle"><?php echo e(__('brand_subtitle')); ?></p>
                
                <!-- Trust Badges -->
                <div class="brand-badges-stack">
                    <div class="brand-badge-card highlight-glow">
                        <div class="badge-icon green-shield">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <div class="badge-text-group">
                            <div class="badge-title"><?php echo e(__('brand_badge_title_1')); ?></div>
                            <div class="badge-desc"><?php echo e(__('brand_badge_desc_1')); ?></div>
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
                                <div class="badge-title"><?php echo e(__('brand_badge_title_2')); ?></div>
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
                    <div class="stat-lbl"><?php echo e(__('brand_stats_lbl_1')); ?></div>
                </div>
                <div class="mini-stat">
                    <div class="stat-num">10K+</div>
                    <div class="stat-lbl"><?php echo e(__('brand_stats_lbl_2')); ?></div>
                </div>
            </div>
        </div>
        
        <!-- Right Side: Login Form Panel -->
        <div class="auth-form-panel">
            <!-- Custom Language Dropdown (as shown in design) -->
            <div class="lang-dropdown">
                <button type="button" class="lang-btn" id="lang-btn">
                    <i class="fa-solid fa-globe"></i>
                    <span id="current-lang"><?php echo e($active_lang['name']); ?></span>
                    <i class="fa-solid fa-chevron-down"></i>
                </button>
                <div class="lang-menu" id="lang-menu">
                    <?php foreach ($lang_details as $code => $detail): ?>
                        <a href="?lang=<?php echo $code; ?>" class="lang-item <?php echo $current_lang === $code ? 'active' : ''; ?>">
                            <span><?php echo e($detail['flag'] . ' ' . $detail['name']); ?></span>
                            <?php if ($current_lang === $code): ?>
                                <i class="fa-solid fa-check check-icon"></i>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="auth-form-header">
                <h2><?php echo e(__('welcome_back')); ?></h2>
                <p><?php echo e(__('login_header_p')); ?></p>
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
                    <label for="email" class="form-label"><?php echo e(__('email_address')); ?></label>
                    <div class="input-icon-wrapper">
                        <i class="fa-solid fa-envelope input-prefix-icon"></i>
                        <input type="email" id="email" name="email" class="form-control" placeholder="<?php echo e(__('email_address_placeholder')); ?>" value="<?php echo e($email); ?>" required autocomplete="email">
                    </div>
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password" class="form-label"><?php echo e(__('password')); ?></label>
                    <div class="input-icon-wrapper">
                        <i class="fa-solid fa-lock input-prefix-icon"></i>
                        <input type="password" id="password" name="password" class="form-control" placeholder="<?php echo e(__('password_placeholder')); ?>" required autocomplete="current-password">
                        <button type="button" class="password-toggle-btn" id="password-toggle-btn" aria-label="Toggle password visibility">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Form Options -->
                <div class="form-options">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember" id="remember">
                        <?php echo e(__('remember_me')); ?>
                    </label>
                    <a href="javascript:void(0);" class="forgot-pass-link"><?php echo e(__('forgot_password')); ?></a>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-primary-auth">
                    <i class="fa-solid fa-right-to-bracket"></i> <?php echo e(__('login')); ?>
                </button>
                <p style="font-size: 0.8rem; color: var(--text-muted); text-align: center; margin-top: 1rem;">
                    By logging in, you agree to the <strong>GoWorker™</strong> <a href="#" onclick="alert('Terms & Conditions:\n1. All service requests are simulations.\n2. Users must respect safety guidelines.\n3. Data is managed securely.')" style="text-decoration: underline; color: var(--primary);">Terms & Conditions</a> and <a href="#" onclick="alert('Privacy Policy:\n1. We protect user profile data.\n2. No third-party data sharing.')" style="text-decoration: underline; color: var(--primary);">Privacy Policy</a>.
                </p>
            </form>

            <div class="auth-footer">
                <?php echo e(__('no_account')); ?> <a href="signup.php"><?php echo e(__('register_here')); ?></a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // --- Custom Language Switcher Dropdown Logic ---
    const langBtn = document.getElementById('lang-btn');
    const langMenu = document.getElementById('lang-menu');

    if (langBtn && langMenu) {
        // Toggle dropdown open/close
        langBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            langMenu.classList.toggle('show');
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

<?php
require_once __DIR__ . '/includes/footer.php';
?>
