<?php
/**
 * GoWorker - Forgot Password Request Page
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error_msg = '';
$success_msg = '';
$reset_link = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    
    if (empty($email)) {
        $error_msg = 'Please enter your email address.';
    } else {
        $user = null;
        if (isset($pdo)) {
            try {
                $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                error_log("Database error in forgot-password.php: " . $e->getMessage());
            }
        }
        
        // Simulates lookup check
        if (!$user && $email !== 'aniket@example.com' && $email !== 'ramesh@example.com') {
            $error_msg = 'Account not found.';
        } else {
            // Generate a secure reset token
            $token = bin2hex(random_bytes(32));
            $expiry = time() + 3600; // 1 hour validity
            
            // Store token mapping locally in JSON
            $tokens_file = __DIR__ . '/config/reset_tokens.json';
            $tokens = [];
            if (file_exists($tokens_file)) {
                $tokens = json_decode(file_get_contents($tokens_file), true) ?: [];
            }
            $tokens[$token] = [
                'email' => $email,
                'expiry' => $expiry
            ];
            file_put_contents($tokens_file, json_encode($tokens, JSON_PRETTY_PRINT));
            
            $reset_link = 'reset-password.php?token=' . $token;
            $success_msg = 'A secure password reset link has been successfully generated!';
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="margin-top: 5rem; margin-bottom: 5rem; min-height: 60vh; display: flex; align-items: center; justify-content: center; padding: 0 1rem;">
    <div class="card" style="max-width: 480px; width: 100%; padding: 40px; border: 1px solid var(--border-color); border-radius: var(--radius-lg); box-shadow: var(--shadow-premium); background: var(--white);">
        
        <div style="text-align: center; margin-bottom: 24px;">
            <i class="fa-solid fa-lock" style="font-size: 40px; color: var(--primary); margin-bottom: 12px;"></i>
            <h2 style="font-weight: 700; color: var(--dark-navy); margin-bottom: 4px;">Forgot Password?</h2>
            <p style="color: var(--secondary-text); font-size: 14px; margin-bottom: 0;">Enter your registered email address and we'll generate a secure reset link.</p>
        </div>

        <?php if (!empty($error_msg)): ?>
            <div style="background: rgba(239, 68, 68, 0.08); border-left: 4px solid var(--danger); color: var(--danger); padding: 12px 16px; border-radius: 8px; font-weight: 600; font-size: 14px; margin-bottom: 20px;">
                <i class="fa-solid fa-circle-exclamation"></i> <?php echo e($error_msg); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success_msg)): ?>
            <div style="background: rgba(34, 197, 94, 0.08); border-left: 4px solid var(--success); color: var(--success); padding: 12px 16px; border-radius: 8px; font-weight: 600; font-size: 14px; margin-bottom: 20px;">
                <i class="fa-solid fa-circle-check"></i> <?php echo e($success_msg); ?>
            </div>
            
            <div style="background: var(--light-bg); border: 1px solid var(--border-color); padding: 16px; border-radius: 8px; text-align: center; margin-bottom: 20px;">
                <p style="font-size: 13px; font-weight: 600; margin-bottom: 12px; color: var(--dark-navy);">Click the button below to update password:</p>
                <a href="<?php echo $reset_link; ?>" class="btn" style="background: var(--primary); color: white; display: inline-block; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 700; font-size: 14px; box-shadow: 0 4px 10px rgba(18,69,197,0.2);">
                    <i class="fa-solid fa-key"></i> Reset Password Now
                </a>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div style="margin-bottom: 20px; text-align: left;">
                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px; color: var(--dark-navy);">Email Address</label>
                <div style="position: relative;">
                    <i class="fa-regular fa-envelope" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--secondary-text); font-size: 16px;"></i>
                    <input type="email" name="email" required placeholder="you@example.com" style="width: 100%; padding: 12px 16px 12px 48px; border: 1.5px solid var(--border-color); border-radius: 8px; font-size: 14px; box-sizing: border-box;">
                </div>
            </div>

            <button type="submit" class="btn" style="width: 100%; height: 50px; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-hover) 100%); border: none; color: white; font-weight: 700; border-radius: 8px; cursor: pointer; box-shadow: 0 4px 12px rgba(18,69,197,0.2);">
                <i class="fa-solid fa-paper-plane" style="margin-right: 6px;"></i> Send Reset Link
            </button>
        </form>

        <div style="text-align: center; margin-top: 24px; font-size: 13px;">
            <a href="login.php" style="color: var(--primary); text-decoration: none; font-weight: 600;"><i class="fa-solid fa-arrow-left"></i> Back to Log In</a>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
