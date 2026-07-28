<?php
/**
 * GoWorker - Become a Worker (Placeholder)
 */
require_once __DIR__ . '/includes/functions.php';

require_once __DIR__ . '/includes/header.php';
?>

<div class="container section">
    <div class="card coming-soon-container">
        <div class="coming-soon-icon">
            <i class="fa-solid fa-user-tie"></i>
        </div>
        <h1>Become a GoWorker Partner</h1>
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <?php if ($_SESSION['user_type'] === 'worker'): ?>
                <p style="color: var(--text-muted); max-width: 500px; margin: 0 auto 1.5rem auto;">
                    You are already registered as a worker partner! You can manage your jobs and settings directly from your dashboard.
                </p>
                <a href="worker-dashboard.php" class="btn btn-primary"><i class="fa-solid fa-gauge"></i> Go to Worker Dashboard</a>
            <?php else: ?>
                <p style="color: var(--text-muted); max-width: 500px; margin: 0 auto 1.5rem auto;">
                    Hi <strong><?php echo e($_SESSION['full_name']); ?></strong>, you are currently logged in as a customer. 
                    You can register your secondary worker profile (setting your service category, skills, hourly rate, and location) using our onboarding module.
                </p>
                <div style="display: flex; gap: 1rem; justify-content: center;">
                    <a href="worker-registration.php" class="btn btn-primary"><i class="fa-solid fa-user-tie"></i> Register Worker Profile</a>
                    <a href="customer-dashboard.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Return to Dashboard</a>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <p style="color: var(--text-muted); max-width: 500px; margin: 0 auto 1.5rem auto;">
                Want to offer your professional skills on GoWorker? You can fill out worker profiles and start receiving booking requests in your locality.
            </p>
            <div style="display: flex; gap: 1rem; justify-content: center;">
                <a href="worker-registration.php" class="btn btn-primary"><i class="fa-solid fa-user-plus"></i> Join as Worker Partner</a>
                <a href="index.php" class="btn btn-secondary">Learn More</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
