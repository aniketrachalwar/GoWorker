<?php
/**
 * GoWorker - User Profile (Placeholder)
 */
require_once __DIR__ . '/includes/auth.php';

// Enforce login
requireLogin();

require_once __DIR__ . '/includes/header.php';
?>

<div class="container section">
    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <div class="text-center" style="margin-bottom: 2rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1.5rem;">
            <div class="avatar-placeholder">
                <?php 
                $name_parts = explode(' ', $_SESSION['full_name']);
                $initials = (count($name_parts) > 1) ? $name_parts[0][0] . $name_parts[1][0] : $name_parts[0][0];
                echo e(strtoupper($initials)); 
                ?>
            </div>
            <h2>Profile Settings</h2>
            <p style="color: var(--text-muted);">Manage your account details and settings</p>
        </div>

        <div style="margin-bottom: 2rem;">
            <h3 style="font-size: 1.1rem; margin-bottom: 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.25rem;">Account Details</h3>
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 1.5rem;">
                <tr style="border-bottom: 1px solid var(--border-color);">
                    <td style="padding: 0.75rem 0; font-weight: 600; color: var(--text-muted); width: 35%;">Full Name:</td>
                    <td style="padding: 0.75rem 0;"><?php echo e($_SESSION['full_name']); ?></td>
                </tr>
                <tr style="border-bottom: 1px solid var(--border-color);">
                    <td style="padding: 0.75rem 0; font-weight: 600; color: var(--text-muted);">Email Address:</td>
                    <td style="padding: 0.75rem 0;"><?php echo e($_SESSION['email']); ?></td>
                </tr>
                <tr style="border-bottom: 1px solid var(--border-color);">
                    <td style="padding: 0.75rem 0; font-weight: 600; color: var(--text-muted);">Account Type:</td>
                    <td style="padding: 0.75rem 0; text-transform: capitalize;"><?php echo e($_SESSION['user_type']); ?></td>
                </tr>
            </table>
        </div>

        <div class="coming-soon-container" style="min-height: auto; border: 1px dashed var(--border-color); border-radius: var(--radius-md); padding: 2rem; background-color: var(--light-bg);">
            <div class="coming-soon-icon" style="font-size: 2.5rem; margin-bottom: 1rem;">
                <i class="fa-solid fa-user-pen"></i>
            </div>
            <h4 style="margin-bottom: 0.5rem;">Profile Editing Coming Soon</h4>
            <p style="color: var(--text-muted); font-size: 0.9rem; max-width: 400px; margin: 0 auto;">In Phase 2, you will be able to update your phone number, password, location, and upload profile pictures here.</p>
        </div>

        <div style="margin-top: 2rem; text-align: center;">
            <?php if ($_SESSION['user_type'] === 'customer'): ?>
                <a href="customer-dashboard.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Return to Dashboard</a>
            <?php else: ?>
                <a href="worker-dashboard.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Return to Dashboard</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
