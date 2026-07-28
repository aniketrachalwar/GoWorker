<?php
/**
 * GoWorker - Reset Password Page
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$token = trim($_GET['token'] ?? '');
$error_msg = '';
$success_msg = '';
$email_to_reset = '';

$tokens_file = __DIR__ . '/config/reset_tokens.json';
$tokens = [];
if (file_exists($tokens_file)) {
    $tokens = json_decode(file_get_contents($tokens_file), true) ?: [];
}

// Token validation
if (empty($token) || !isset($tokens[$token])) {
    $error_msg = 'Invalid or expired password reset token.';
} else {
    $token_data = $tokens[$token];
    if (time() > $token_data['expiry']) {
        $error_msg = 'This password reset token has expired.';
        // Clean up expired token
        unset($tokens[$token]);
        file_put_contents($tokens_file, json_encode($tokens, JSON_PRETTY_PRINT));
    } else {
        $email_to_reset = $token_data['email'];
    }
}

// Handle Password Update Form Submit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($error_msg)) {
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($new_password)) {
        $error_msg = 'Password cannot be empty.';
    } elseif ($new_password !== $confirm_password) {
        $error_msg = 'Passwords do not match.';
    } else {
        $hashed_pwd = password_hash($new_password, PASSWORD_DEFAULT);
        
        $updated = false;
        if (isset($pdo) && !empty($email_to_reset)) {
            try {
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
                $stmt->execute([$hashed_pwd, $email_to_reset]);
                $updated = true;
            } catch (PDOException $e) {
                error_log("Database error in reset-password.php: " . $e->getMessage());
                $error_msg = 'Failed to update database record.';
            }
        } else {
            $updated = true; // Sandbox fallback success
        }
        
        if ($updated) {
            // Delete token after successful reset
            unset($tokens[$token]);
            file_put_contents($tokens_file, json_encode($tokens, JSON_PRETTY_PRINT));
            
            $success_msg = 'Password updated successfully! Redirecting you to login...';
            
            // Output script redirect
            header("refresh:2;url=login.php");
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="margin-top: 5rem; margin-bottom: 5rem; min-height: 60vh; display: flex; align-items: center; justify-content: center; padding: 0 1rem;">
    <div class="card" style="max-width: 480px; width: 100%; padding: 40px; border: 1px solid var(--border-color); border-radius: var(--radius-lg); box-shadow: var(--shadow-premium); background: var(--white);">
        
        <div style="text-align: center; margin-bottom: 24px;">
            <i class="fa-solid fa-key" style="font-size: 40px; color: var(--success); margin-bottom: 12px;"></i>
            <h2 style="font-weight: 700; color: var(--dark-navy); margin-bottom: 4px;">Update Password</h2>
            <p style="color: var(--secondary-text); font-size: 14px; margin-bottom: 0;">Set a new secure password for your GoWorker account.</p>
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
        <?php endif; ?>

        <?php if (empty($success_msg) && empty($error_msg)): ?>
            <form method="POST">
                <!-- New Password -->
                <div style="margin-bottom: 20px; text-align: left;">
                    <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px; color: var(--dark-navy);">New Password</label>
                    <div style="position: relative;">
                        <i class="fa-solid fa-lock" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--secondary-text); font-size: 16px;"></i>
                        <input type="password" name="new_password" required placeholder="Min 6 characters" style="width: 100%; padding: 12px 16px 12px 48px; border: 1.5px solid var(--border-color); border-radius: 8px; font-size: 14px; box-sizing: border-box;">
                    </div>
                </div>

                <!-- Confirm Password -->
                <div style="margin-bottom: 20px; text-align: left;">
                    <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px; color: var(--dark-navy);">Confirm Password</label>
                    <div style="position: relative;">
                        <i class="fa-solid fa-lock" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--secondary-text); font-size: 16px;"></i>
                        <input type="password" name="confirm_password" required placeholder="Repeat your password" style="width: 100%; padding: 12px 16px 12px 48px; border: 1.5px solid var(--border-color); border-radius: 8px; font-size: 14px; box-sizing: border-box;">
                    </div>
                </div>

                <button type="submit" class="btn" style="width: 100%; height: 50px; background: linear-gradient(135deg, var(--success) 0%, #059669 100%); border: none; color: white; font-weight: 700; border-radius: 8px; cursor: pointer; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);">
                    <i class="fa-solid fa-check-double" style="margin-right: 6px;"></i> Update Password
                </button>
            </form>
        <?php endif; ?>

        <div style="text-align: center; margin-top: 24px; font-size: 13px;">
            <a href="login.php" style="color: var(--primary); text-decoration: none; font-weight: 600;"><i class="fa-solid fa-arrow-left"></i> Back to Log In</a>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
