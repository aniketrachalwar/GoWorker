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

<div class="auth-wrapper">
    <div class="card">
        <div class="auth-header">
            <a href="index.php" class="auth-logo" style="display: inline-block; margin-bottom: 1.5rem;">
                <img src="images/logo.jpg" alt="GoWorker" style="height: 72px; width: auto; object-fit: contain; border-radius: 10px;">
            </a>
            <h2>Welcome Back</h2>
            <p style="color: var(--text-muted);">Log in to access your GoWorker dashboard</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger" style="display: block; margin-bottom: 1.5rem;">
                <ul style="padding-left: 1rem; margin: 0;">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST" class="auth-form">
            <?php echo csrf_field(); ?>

            <!-- Email Address -->
            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="john@example.com" value="<?php echo e($email); ?>" required autocomplete="email">
            </div>

            <!-- Password -->
            <div class="form-group">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <label for="password" class="form-label">Password</label>
                </div>
                <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required autocomplete="current-password">
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                <i class="fa-solid fa-right-to-bracket"></i> Login
            </button>
        </form>

        <div class="auth-footer">
            Don't have an account? <a href="signup.php">Register here</a>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
