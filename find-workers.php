<?php
/**
 * GoWorker - Find Workers Marketplace Page
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$category_name = '';
$location = trim($_GET['location'] ?? '');
$category_id = intval($_GET['category'] ?? 0);
$search_query = trim($_GET['q'] ?? '');

$workers = [];
$categories_list = [];
if (isset($pdo)) {
    try {
        // Retrieve categories for filters list
        try {
            $cat_stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
            $categories_list = $cat_stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $categories_list = [];
        }
        if (empty($categories_list)) {
            $categories_list = [
                ['id' => 1, 'name' => 'Electrician'],
                ['id' => 2, 'name' => 'Plumber'],
                ['id' => 3, 'name' => 'Carpenter'],
                ['id' => 4, 'name' => 'Painter'],
                ['id' => 5, 'name' => 'Cleaner'],
                ['id' => 6, 'name' => 'Appliance Repair'],
                ['id' => 7, 'name' => 'Mechanic']
            ];
        }

        if ($category_id > 0) {
            $category_name = '';
            foreach ($categories_list as $cat) {
                if ($cat['id'] == $category_id) {
                    $category_name = $cat['name'];
                    break;
                }
            }
        }

        // Search Query
        $sql = "SELECT w.*, u.name as worker_name, u.email, u.phone, c.name as category_name 
                FROM workers w 
                JOIN users u ON w.user_id = u.id 
                JOIN categories c ON w.category_id = c.id
                WHERE 1=1";
        $params = [];

        if ($category_id > 0) {
            $sql .= " AND w.category_id = ?";
            $params[] = $category_id;
        }
        if (!empty($location)) {
            $sql .= " AND w.service_area LIKE ?";
            $params[] = "%$location%";
        }
        if (!empty($search_query)) {
            $sql .= " AND (u.name LIKE ? OR w.skills LIKE ?)";
            $params[] = "%$search_query%";
            $params[] = "%$search_query%";
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $workers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fallback nearest location check if no workers found
        $location_info_message = '';
        if (empty($workers) && !empty($location)) {
            $fallback_city = get_nearest_location_fallback($location);
            
            // Re-run search with fallback city
            $sql_fallback = "SELECT w.*, u.name as worker_name, u.email, u.phone, c.name as category_name 
                             FROM workers w 
                             JOIN users u ON w.user_id = u.id 
                             JOIN categories c ON w.category_id = c.id
                             WHERE w.service_area LIKE ?";
            $params_fallback = ["%$fallback_city%"];
            
            if ($category_id > 0) {
                $sql_fallback .= " AND w.category_id = ?";
                $params_fallback[] = $category_id;
            }
            if (!empty($search_query)) {
                $sql_fallback .= " AND (u.name LIKE ? OR w.skills LIKE ?)";
                $params_fallback[] = "%$search_query%";
                $params_fallback[] = "%$search_query%";
            }
            
            $stmt_fallback = $pdo->prepare($sql_fallback);
            $stmt_fallback->execute($params_fallback);
            $workers = $stmt_fallback->fetchAll(PDO::FETCH_ASSOC);
            
            if (!empty($workers)) {
                $location_info_message = "No workers found in '" . e($location) . "'. Showing nearest workers in '" . e($fallback_city) . "'.";
            }
        }

    } catch (PDOException $e) {
        error_log("Database error on find-workers.php: " . $e->getMessage());
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<!-- Specific Marketplace Style Embed -->
<style>
    .marketplace-layout {
      display: grid;
      grid-template-columns: 280px 1fr;
      gap: 32px;
      margin: 40px auto;
      align-items: start;
    }
    .filter-sidebar {
      background: var(--white);
      border: 1px solid var(--border-color);
      border-radius: var(--radius-lg);
      padding: 24px;
      box-shadow: var(--shadow-sm);
    }
    .filter-section {
      margin-bottom: 24px;
      padding-bottom: 24px;
      border-bottom: 1px solid var(--border-color);
    }
    .filter-section:last-child {
      margin-bottom: 0;
      padding-bottom: 0;
      border-bottom: none;
    }
    .filter-title {
      font-size: 15px;
      font-weight: 600;
      color: var(--dark-navy);
      margin-bottom: 16px;
    }
    .filter-group {
      display: flex;
      flex-direction: column;
      gap: 12px;
    }
    .checkbox-label {
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 14px;
      color: var(--text-dark);
      cursor: pointer;
    }
    .checkbox-label input {
      accent-color: var(--primary);
      width: 18px;
      height: 18px;
    }
    .search-hero {
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-hover) 100%);
      border-radius: var(--radius-lg);
      padding: 40px;
      color: var(--white);
      margin-bottom: 32px;
    }
    .search-hero h1 {
      color: var(--white);
      font-size: 32px;
      margin-bottom: 12px;
    }
    .search-hero p {
      color: rgba(255,255,255,0.85);
      font-size: 15px;
      margin-bottom: 24px;
    }
    .marketplace-search-bar {
      display: flex;
      background: var(--white);
      padding: 8px;
      border-radius: var(--radius-sm);
      gap: 12px;
    }
    .marketplace-search-bar input {
      flex: 1;
      border: none;
      outline: none;
      padding: 12px 16px;
      font-size: 15px;
    }
    .marketplace-search-bar button {
      background: var(--primary);
      color: var(--white);
      border: none;
      padding: 12px 28px;
      border-radius: 12px;
      font-weight: 600;
      cursor: pointer;
    }
    .workers-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
      gap: 24px;
    }
    .worker-card {
      background: var(--white);
      border: 1px solid var(--border-color);
      border-radius: var(--radius-lg);
      padding: 24px;
      display: flex;
      flex-direction: column;
      transition: var(--transition);
      box-shadow: var(--shadow-sm);
    }
    .worker-card:hover {
      transform: translateY(-6px);
      box-shadow: var(--shadow-md);
      border-color: var(--primary);
    }
    .worker-card-header {
      display: flex;
      gap: 16px;
      margin-bottom: 16px;
    }
    .worker-avatar-container {
      position: relative;
      width: 64px;
      height: 64px;
    }
    .worker-avatar {
      width: 100%;
      height: 100%;
      border-radius: 50%;
      object-fit: cover;
    }
    .verified-badge {
      position: absolute;
      bottom: 0;
      right: 0;
      background: var(--primary);
      color: var(--white);
      width: 20px;
      height: 20px;
      border-radius: 50%;
      display: flex;
      justify-content: center;
      align-items: center;
      font-size: 10px;
      border: 2px solid var(--white);
    }
    .worker-meta h4 {
      font-size: 16px;
      font-weight: 700;
      margin-bottom: 4px;
    }
    .worker-meta p {
      font-size: 13px;
      color: var(--text-muted);
    }
    .worker-card-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: auto;
      padding-top: 16px;
      border-top: 1px solid var(--border-color);
    }
    .price-tag strong {
      display: block;
      font-size: 18px;
      color: var(--primary);
    }
</style>

<main class="container">
  <div class="marketplace-layout">
    <!-- Filters Sidebar -->
    <aside class="filter-sidebar">
      <div class="filter-section">
        <h3 class="filter-title">Filter by Category</h3>
        <div class="filter-group">
          <label class="checkbox-label">
            <input type="checkbox" checked onclick="location.href='find-workers.php'"> All Categories
          </label>
          <?php if (!empty($categories_list)): ?>
            <?php foreach ($categories_list as $cat): ?>
              <label class="checkbox-label">
                <input type="checkbox" <?php echo ($category_id == $cat['id']) ? 'checked' : ''; ?> onclick="location.href='find-workers.php?category=<?php echo $cat['id']; ?>'">
                <?php echo e(translate_category_name($cat['name'])); ?>
              </label>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </aside>

    <!-- Main Listing Area -->
    <section class="main-content">
      <div class="search-hero">
        <h1>Find Trusted Workers</h1>
        <p>Connect with verified experts for your home service requirements.</p>
        <form method="GET" action="find-workers.php" class="marketplace-search-bar">
          <input type="text" name="q" value="<?php echo e($search_query); ?>" placeholder="Search by name or skills...">
          <input type="text" name="location" value="<?php echo e($location); ?>" placeholder="Enter location...">
          <button type="submit">Search</button>
        </form>
      </div>

      <?php if (!empty($location_info_message)): ?>
        <div style="background: rgba(18, 69, 197, 0.06); border: 1.5px solid rgba(18, 69, 197, 0.15); color: var(--primary); border-radius: var(--radius-md); padding: 14px 20px; margin-bottom: 24px; font-weight: 500; display: flex; align-items: center; gap: 12px; font-size: 14px;">
            <i class="fa-solid fa-circle-info" style="font-size: 16px;"></i>
            <span><?php echo $location_info_message; ?></span>
        </div>
      <?php endif; ?>

      <h3 style="margin-bottom: 24px; font-weight: 600;"><?php echo count($workers); ?> Workers Found</h3>

      <div class="workers-grid">
        <?php if (!empty($workers)): ?>
          <?php foreach ($workers as $worker): ?>
            <article class="worker-card">
              <div class="worker-card-header">
                <div class="worker-avatar-container">
                  <img class="worker-avatar" src="<?php echo e($worker['profile_picture'] ?: 'images/avatar_placeholder.png'); ?>" alt="Worker">
                  <div class="verified-badge"><i class="fa-solid fa-check"></i></div>
                </div>
                <div class="worker-meta">
                  <h4><?php echo e($worker['worker_name']); ?></h4>
                  <p><?php echo e(translate_category_name($worker['category_name'])); ?></p>
                </div>
              </div>
              <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 16px;">
                <i class="fa-solid fa-location-dot"></i> Service Area: <?php echo e($worker['service_area']); ?>
              </div>
              <div class="worker-card-footer">
                <div class="price-tag">
                  Starting Price
                  <strong>₹<?php echo e($worker['hourly_rate']); ?>/hr</strong>
                </div>
                <button class="btn btn-primary" onclick="location.href='booking.php?worker=<?php echo $worker['id']; ?>'">Book Now</button>
              </div>
            </article>
          <?php endforeach; ?>
        <?php else: ?>
          <!-- Simulated Fallback sample workers if DB is empty -->
          <article class="worker-card">
            <div class="worker-card-header">
              <div class="worker-avatar-container">
                <img class="worker-avatar" src="https://images.unsplash.com/photo-1540569014015-19a7be504e3a?w=150&fit=crop" alt="Worker">
                <div class="verified-badge"><i class="fa-solid fa-check"></i></div>
              </div>
              <div class="worker-meta">
                <h4>Ramesh Kumar</h4>
                <p>Electrician</p>
              </div>
            </div>
            <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 16px;">
              <i class="fa-solid fa-location-dot"></i> Service Area: Pune, Maharashtra
            </div>
            <div class="worker-card-footer">
              <div class="price-tag">
                Starting Price
                <strong>₹299/hr</strong>
              </div>
              <button class="btn btn-primary" onclick="location.href='booking.php'">Book Now</button>
            </div>
          </article>
        <?php endif; ?>
      </div>
    </section>
  </div>
</main>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
