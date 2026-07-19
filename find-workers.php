<?php
/**
 * GoWorker - Find Workers (Placeholder)
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$category_name = '';
$location = trim($_GET['location'] ?? '');
$category_id = intval($_GET['category'] ?? 0);

if ($category_id > 0 && isset($pdo)) {
    try {
        $stmt = $pdo->prepare("SELECT name FROM categories WHERE id = ?");
        $stmt->execute([$category_id]);
        $category_name = $stmt->fetchColumn() ?: '';
    } catch (PDOException $e) {
        error_log("Database error on find-workers.php: " . $e->getMessage());
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container section">
    <div class="card coming-soon-container">
        <div class="coming-soon-icon">
            <i class="fa-solid fa-person-digging"></i>
        </div>
        <h1>Worker Search Module</h1>
        <p style="color: var(--text-muted); max-width: 500px; margin: 0 auto 1.5rem auto;">
            <?php if (!empty($category_name) || !empty($location)): ?>
                We are searching for 
                <strong><?php echo e($category_name ?: 'workers'); ?></strong> 
                <?php echo !empty($location) ? 'in <strong>' . e($location) . '</strong>' : ''; ?>.
                This directory query module is coming in Phase 2!
            <?php else: ?>
                The search and directory listing module is coming in Phase 2. You will be able to search, filter by rating, and browse worker profile cards.
            <?php endif; ?>
        </p>
        <a href="index.php" class="btn btn-primary"><i class="fa-solid fa-arrow-left"></i> Return to Homepage</a>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
