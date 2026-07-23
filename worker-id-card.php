<?php
/**
 * GoWorker - Worker Virtual ID Card View Page
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Session Security: Enforce worker login
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'worker') {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$worker = null;

if (isset($pdo)) {
    try {
        $stmt = $pdo->prepare("
            SELECT w.*, u.id as user_id, u.full_name as worker_name, u.email, u.phone, u.created_at as user_created_at, u.location as user_location, c.name as category_name 
            FROM users u
            LEFT JOIN worker_profiles w ON w.user_id = u.id
            LEFT JOIN categories c ON w.category_id = c.id
            WHERE u.id = ? AND u.user_type = 'worker'
        ");
        $stmt->execute([$userId]);
        $worker = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error in worker-id-card.php: " . $e->getMessage());
    }
}

// Fallback to dynamic session data if worker is not yet registered in profiles
if (!$worker) {
    $worker = [
        'id' => $userId,
        'user_id' => $userId,
        'worker_name' => $_SESSION['full_name'] ?? 'Worker Professional',
        'email' => $_SESSION['email'] ?? '',
        'phone' => '+91 98765 43210',
        'category_name' => 'General Trade',
        'title' => 'Service Specialist',
        'bio' => 'Verified GoWorker professional contractor.',
        'hourly_rate' => 299.00,
        'location' => 'Pune',
        'experience_years' => 5,
        'availability' => 'Mon, Tue, Wed, Thu, Fri',
        'skills' => 'Repair, Maintenance',
        'profile_picture' => 'images/avatar_placeholder.png',
        'user_created_at' => date('Y-m-d H:i:s'),
        'user_location' => 'Pune'
    ];
}

// Coalesce locations and picture variables for safe rendering
$worker['location'] = $worker['location'] ?: ($worker['user_location'] ?: 'Pune');
$worker['profile_picture'] = $worker['profile_picture'] ?: 'images/avatar_placeholder.png';

$idCode = 'GW-' . str_pad($worker['user_id'], 5, '0', STR_PAD_LEFT);
$memberSince = date('F Y', strtotime($worker['user_created_at'] ?? 'now'));

require_once __DIR__ . '/includes/header.php';
?>

<div class="container">
    <div class="dashboard-layout">
        <!-- Sidebar Navigation -->
        <aside class="dashboard-sidebar">
            <div class="user-profile-summary">
                <div class="avatar-placeholder" style="background-color: var(--primary-light); color: var(--primary); width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 28px; font-weight: 700; margin: 0 auto 12px auto; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
                    <?php 
                    $name_parts = explode(' ', $_SESSION['full_name']);
                    $initials = (count($name_parts) > 1) ? $name_parts[0][0] . $name_parts[1][0] : $name_parts[0][0];
                    echo e(strtoupper($initials)); 
                    ?>
                </div>
                <h3><?php echo e($_SESSION['full_name']); ?></h3>
                <p style="margin-top: 4px; margin-bottom: 8px;">
                    <span class="virtual-id-badge" style="font-size: 12px; background: var(--primary-light); color: var(--primary); padding: 3px 8px; border-radius: 4px; font-weight: 600;">
                        <?php echo e($idCode); ?>
                    </span>
                </p>
                <p style="color: var(--text-muted); font-size: 0.9rem;"><i class="fa-solid fa-screwdriver-wrench"></i> Worker Profile</p>
            </div>
            
            <nav>
                <ul class="sidebar-nav">
                    <li>
                        <a href="worker-dashboard.php" class="sidebar-link">
                            <i class="fa-solid fa-gauge"></i> Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="worker-jobs.php" class="sidebar-link">
                            <i class="fa-solid fa-briefcase"></i> Jobs
                        </a>
                    </li>
                    <li>
                        <a href="worker-requests.php" class="sidebar-link">
                            <i class="fa-solid fa-list-check"></i> Requests
                        </a>
                    </li>
                    <li>
                        <a href="worker-earnings.php" class="sidebar-link">
                            <i class="fa-solid fa-wallet"></i> Earnings
                        </a>
                    </li>
                    <li>
                        <a href="worker-id-card.php" class="sidebar-link active">
                            <i class="fa-solid fa-id-card"></i> Virtual ID Card
                        </a>
                    </li>
                    <li>
                        <a href="profile.php" class="sidebar-link">
                            <i class="fa-solid fa-user-gear"></i> Manage Profile
                        </a>
                    </li>
                    <li>
                        <a href="chat.php" class="sidebar-link">
                            <i class="fa-solid fa-comments"></i> Messages
                        </a>
                    </li>
                    <li>
                        <a href="notifications.php" class="sidebar-link">
                            <i class="fa-solid fa-bell"></i> Notifications
                        </a>
                    </li>
                    <li>
                        <a href="logout.php" class="sidebar-link" style="color: var(--danger);">
                            <i class="fa-solid fa-right-from-bracket"></i> Logout
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="dashboard-content">
            <div class="dashboard-header">
                <div>
                    <h1 style="margin-bottom: 0.25rem;">Virtual ID Card</h1>
                    <p style="color: var(--text-muted);">View, print, or download your official GoWorker digital identity badge.</p>
                </div>
            </div>

            <!-- Card Showcase Section -->
            <div class="card" style="padding: 3rem 1.5rem; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 2rem; background: var(--white); border: 1px solid var(--border-color); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); margin-top: 1.5rem;">
                
                <!-- Front Card Preview -->
                <div class="id-card-display" style="width: 320px; height: 500px; background: #ffffff; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); overflow: hidden; display: flex; flex-direction: column; border: 1px solid #e2e8f0; position: relative;">
                    <div style="background: linear-gradient(135deg, #0d4dff 0%, #4a7bff 100%); color: white; padding: 24px; text-align: center; border-bottom-left-radius: 24px; border-bottom-right-radius: 24px;">
                        <div style="font-size: 20px; font-weight: 800; letter-spacing: 0.5px; margin-bottom: 4px;"><i class="fa-solid fa-screwdriver-wrench"></i> GoWorker</div>
                        <span style="background: rgba(255,255,255,0.2); padding: 4px 12px; border-radius: 12px; font-size: 10px; text-transform: uppercase; letter-spacing: 1px; display: inline-flex; align-items: center; gap: 4px;"><i class="fa-solid fa-circle-check"></i> Verified</span>
                    </div>
                    <div style="margin-top: -50px; align-self: center; z-index: 10;">
                        <img src="<?php echo e($worker['profile_picture']); ?>" alt="<?php echo e($worker['worker_name']); ?>" style="width: 100px; height: 100px; border-radius: 50%; border: 4px solid white; object-fit: cover; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                    </div>
                    <div style="flex: 1; padding: 24px; text-align: center; display: flex; flex-direction: column; justify-content: space-between;">
                        <div style="margin-bottom: 10px;">
                            <h2 style="font-size: 18px; font-weight: 700; color: #0f172a; margin: 0 0 4px 0;"><?php echo e($worker['worker_name']); ?></h2>
                            <p style="font-size: 13px; color: #0d4dff; font-weight: 600; margin: 0;"><?php echo e($worker['title'] ?: $worker['category_name']); ?></p>
                            <p style="font-size: 11px; color: #64748b; margin: 4px 0 0 0; font-weight: 600;">ID: <?php echo $idCode; ?></p>
                        </div>
                        
                        <div style="text-align: left; margin: 10px 0; border-top: 1.5px solid #f1f5f9; border-bottom: 1.5px solid #f1f5f9; padding: 12px 0; display: flex; flex-direction: column; gap: 8px;">
                            <div style="display: flex; justify-content: space-between; font-size: 12px;">
                                <span style="color: #64748b; font-weight: 500;">Email:</span>
                                <span style="color: #1e293b; font-weight: 600;"><?php echo e($worker['email']); ?></span>
                            </div>
                            <div style="display: flex; justify-content: space-between; font-size: 12px;">
                                <span style="color: #64748b; font-weight: 500;">Phone:</span>
                                <span style="color: #1e293b; font-weight: 600;"><?php echo e($worker['phone']); ?></span>
                            </div>
                            <div style="display: flex; justify-content: space-between; font-size: 12px;">
                                <span style="color: #64748b; font-weight: 500;">Location:</span>
                                <span style="color: #1e293b; font-weight: 600;"><?php echo e($worker['location']); ?></span>
                            </div>
                            <div style="display: flex; justify-content: space-between; font-size: 12px;">
                                <span style="color: #64748b; font-weight: 500;">Member Since:</span>
                                <span style="color: #1e293b; font-weight: 600;"><?php echo $memberSince; ?></span>
                            </div>
                        </div>
                        
                        <div style="display: flex; justify-content: center; align-items: center; gap: 10px; margin-top: 5px;">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=https://goworker-demo.netlify.app/worker-profile.html?id=<?php echo $worker['user_id']; ?>" style="width: 70px; height: 70px; border: 1px solid #e2e8f0; border-radius: 6px; padding: 2px;" alt="QR Code">
                            <div style="text-align: left; font-size: 10px; color: #64748b; max-width: 140px; line-height: 1.3;">
                                Scan QR to verify professional credentials on the GoWorker platform.
                            </div>
                        </div>
                        <div style="font-size: 11px; color: #94a3b8; margin-top: 5px;">
                            <span>GoWorker Professional ID Card</span>
                        </div>
                    </div>
                </div>

                <!-- Print/Download Button -->
                <button class="btn btn-primary" style="height: 50px; padding: 0 32px; background: linear-gradient(135deg, #10B981 0%, #059669 100%); border: none; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2); font-weight: 700; display: inline-flex; align-items: center; gap: 8px; border-radius: 25px; cursor: pointer; color: white;" onclick="downloadIDCard('<?php echo e(addslashes($worker['worker_name'])); ?>', <?php echo $worker['user_id']; ?>, '<?php echo e(addslashes($worker['title'] ?: $worker['category_name'])); ?>', '<?php echo e(addslashes($worker['profile_picture'])); ?>', '<?php echo e(addslashes($worker['location'])); ?>', '<?php echo e($worker['experience_years']); ?>', '<?php echo e(addslashes($worker['email'] ?? '')); ?>', '<?php echo e(addslashes($worker['phone'] ?? '')); ?>', '<?php echo e($memberSince); ?>')">
                    <i class="fa-solid fa-id-card"></i>
                    <span>Print Virtual ID Card</span>
                </button>
            </div>
        </main>
    </div>
</div>

<script src="worker-profile.js"></script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
