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

<div class="auth-wrapper">
    <div class="card">
        <div class="auth-header">
            <a href="index.php" class="auth-logo">
                <i class="fa-solid fa-briefcase"></i> Go<span>Worker</span>
            </a>
            <h2>Create an Account</h2>
            <p style="color: var(--text-muted);">Join GoWorker to connect with local services</p>
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

        <form action="signup.php" method="POST" class="auth-form">
            <?php echo csrf_field(); ?>

            <!-- Role Selector -->
            <div class="role-selector">
                <div class="role-option">
                    <input type="radio" id="role-customer" name="user_type" value="customer" <?php echo $user_type === 'customer' ? 'checked' : ''; ?>>
                    <label for="role-customer" class="role-label">
                        <i class="fa-solid fa-user"></i>
                        <span>Customer</span>
                    </label>
                </div>
                <div class="role-option">
                    <input type="radio" id="role-worker" name="user_type" value="worker" <?php echo $user_type === 'worker' ? 'checked' : ''; ?>>
                    <label for="role-worker" class="role-label">
                        <i class="fa-solid fa-screwdriver-wrench"></i>
                        <span>Worker</span>
                    </label>
                </div>
            </div>

            <!-- Full Name -->
            <div class="form-group">
                <label for="full_name" class="form-label">Full Name</label>
                <input type="text" id="full_name" name="full_name" class="form-control" placeholder="John Doe" value="<?php echo e($full_name); ?>" required autocomplete="name">
            </div>

            <!-- Email Address -->
            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="john@example.com" value="<?php echo e($email); ?>" required autocomplete="email">
            </div>

            <!-- Phone Number -->
            <div class="form-group">
                <label for="phone" class="form-label">Phone Number</label>
                <input type="tel" id="phone" name="phone" class="form-control" placeholder="+1 (555) 000-0000" value="<?php echo e($phone); ?>" autocomplete="tel">
            </div>

            <!-- Location -->
            <div class="form-group">
                <label for="location" class="form-label">Location (City/Neighborhood)</label>
                <input type="text" id="location" name="location" class="form-control" placeholder="e.g. Downtown" value="<?php echo e($location); ?>">
            </div>

            <!-- Password -->
            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="At least 6 characters" required autocomplete="new-password">
            </div>

            <!-- Confirm Password -->
            <div class="form-group">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Repeat your password" required autocomplete="new-password">
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                <i class="fa-solid fa-user-plus"></i> Register
            </button>
        </form>

        <div class="auth-footer">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
