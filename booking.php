<?php
/**
 * GoWorker - Booking Management (Placeholder)
 */
require_once __DIR__ . '/includes/auth.php';

// Enforce customer login (bookings are managed by customers)
requireCustomer();

require_once __DIR__ . '/includes/header.php';
?>

<div class="container section">
    <div class="card coming-soon-container">
        <div class="coming-soon-icon">
            <i class="fa-solid fa-calendar-check"></i>
        </div>
        <h1>Booking Management Module</h1>
        <p style="color: var(--text-muted); max-width: 500px; margin: 0 auto 1.5rem auto;">
            The service booking scheduler and work-history tracking system will be fully functional in Phase 2.
            You will be able to book service workers, view task schedules, track progress, and write ratings/reviews.
        </p>
        <a href="customer-dashboard.php" class="btn btn-primary"><i class="fa-solid fa-arrow-left"></i> Return to Dashboard</a>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
