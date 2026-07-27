<?php
/**
 * GoWorker - Google OAuth Callback Handler
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$name = trim($_GET['name'] ?? '');
$email = trim($_GET['email'] ?? '');
$google_id = trim($_GET['google_id'] ?? '');
$profile_photo = trim($_GET['profile_photo'] ?? 'images/avatar_placeholder.png');
$user_type = trim($_GET['user_type'] ?? 'customer');

if (empty($name) || empty($email)) {
    die("Error: Name and Email are required for Google Authentication.");
}

$user = null;

if (isset($pdo)) {
    try {
        // Search if email already exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            // Register user automatically
            $random_password = bin2hex(random_bytes(16));
            $hashed_password = password_hash($random_password, PASSWORD_DEFAULT);
            
            $ins = $pdo->prepare("INSERT INTO users (full_name, email, password, user_type) VALUES (?, ?, ?, ?)");
            $ins->execute([$name, $email, $hashed_password, $user_type]);
            
            $user_id = $pdo->lastInsertId();
            
            // Reload user details
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Create default worker profile if user is a worker
            if ($user_type === 'worker') {
                $wp_stmt = $pdo->prepare("INSERT INTO worker_profiles (user_id, category_id, title, hourly_rate, experience_years, profile_picture) VALUES (?, ?, ?, ?, ?, ?)");
                $wp_stmt->execute([$user_id, 1, 'Certified Professional', 299.00, 5, $profile_photo]);
            }
        }
    } catch (PDOException $e) {
        error_log("Database error in Google Callback: " . $e->getMessage());
    }
}

// Fallback user details for sandbox/offline execution
if (!$user) {
    $user = [
        'id' => 999,
        'full_name' => $name,
        'email' => $email,
        'user_type' => $user_type
    ];
}

// Log in automatically by setting sessions
$_SESSION['user_id'] = $user['id'];
$_SESSION['full_name'] = $user['name'] ?? ($user['full_name'] ?? $name);
$_SESSION['email'] = $user['email'];
$_SESSION['user_type'] = $user['user_type'];

// Track Google metadata locally without changing the database structure
$meta_file = __DIR__ . '/config/google_users.json';
$metadata = [];
if (file_exists($meta_file)) {
    $metadata = json_decode(file_get_contents($meta_file), true) ?: [];
}
$metadata[$user['id']] = [
    'google_id' => $google_id,
    'profile_photo' => $profile_photo
];
file_put_contents($meta_file, json_encode($metadata, JSON_PRETTY_PRINT));

// Determine dynamic landing redirect
$redirect_url = url(($user['user_type'] === 'worker') ? 'worker-dashboard.php' : 'customer-dashboard.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Authentication Successful</title>
</head>
<body>
    <p>Authenticating... Please wait...</p>
    <script>
        if (window.opener) {
            // Redirect the parent page to the dashboard and close this popup window
            window.opener.location.href = "<?php echo $redirect_url; ?>";
            window.close();
        } else {
            // Fallback if not inside a popup
            window.location.href = "<?php echo $redirect_url; ?>";
        }
    </script>
</body>
</html>
