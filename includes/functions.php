<?php
/**
 * Security and Utility Functions
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load global multi-language translation dictionary
require_once __DIR__ . '/translations.php';

/**
 * Escapes text for safe output in HTML (prevents XSS)
 * 
 * @param string|null $text
 * @return string
 */
function e($text) {
    return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Generates and stores a CSRF token in the session if not present
 * 
 * @return string
 */
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verifies if the provided token matches the session CSRF token
 * 
 * @param string|null $token
 * @return bool
 */
function verify_csrf_token($token) {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Outputs a hidden CSRF token input field for forms
 * 
 * @return string
 */
function csrf_field() {
    $token = generate_csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . e($token) . '">';
}

function redirect($url) {
    session_write_close();
    if (!headers_sent()) {
        header("Location: $url");
    } else {
        echo "<script>window.location.href='" . addslashes($url) . "';</script>";
    }
    exit();
}

/**
 * Translates a dynamic category name using language key mapping
 * 
 * @param string $name
 * @return string
 */
function translate_category_name($name) {
    $mapping = [
        'Electrician' => 'cat_electrician',
        'Plumber' => 'cat_plumber',
        'Carpenter' => 'cat_carpenter',
        'Painter' => 'cat_painter',
        'Cleaner' => 'cat_cleaner',
        'Appliance Repair' => 'cat_appliance',
        'Mechanic' => 'cat_mechanic'
    ];
    if (isset($mapping[$name])) {
        return __($mapping[$name]);
    }
    $key = 'cat_' . strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '_', $name), '_'));
    $translated = __($key);
    return ($translated !== $key) ? $translated : $name;
}

/**
 * Returns the nearest main city fallback for location searches with no exact matches
 * 
 * @param string $searched_location
 * @return string
 */
function get_nearest_location_fallback($searched_location) {
    $searched = strtolower(trim($searched_location));
    if (empty($searched)) {
        return 'Pune';
    }
    
    // Map of suburbs/areas to main cities where workers are located
    $map = [
        'kothrud' => 'Pune',
        'hadapsar' => 'Pune',
        'hinjewadi' => 'Pune',
        'baner' => 'Pune',
        'vimannagar' => 'Pune',
        'wakad' => 'Pune',
        'pimpri' => 'Pune',
        'chinchwad' => 'Pune',
        'camp' => 'Pune',
        
        'thane' => 'Mumbai',
        'navi mumbai' => 'Mumbai',
        'andheri' => 'Mumbai',
        'bandra' => 'Mumbai',
        'borivali' => 'Mumbai',
        
        'whitefield' => 'Bangalore',
        'indiranagar' => 'Bangalore',
        'koramangala' => 'Bangalore',
        'jayanagar' => 'Bangalore',
        
        'noida' => 'Delhi',
        'gurugram' => 'Delhi',
        'gurgaon' => 'Delhi',
        'ghaziabad' => 'Delhi',
        
        'secunderabad' => 'Hyderabad',
        'gachibowli' => 'Hyderabad',
        
        'adyar' => 'Chennai',
        'tambaram' => 'Chennai',
        
        'howrah' => 'Kolkata',
        'salt lake' => 'Kolkata'
    ];
    
    foreach ($map as $suburb => $city) {
        if (strpos($searched, $suburb) !== false || strpos($suburb, $searched) !== false) {
            return $city;
        }
    }
    
    // If no match, return Pune as default main city where database seeds have workers
    return 'Pune';
}

/**
 * Set or get a flash message
 * 
 * @param string|null $type 'success' or 'error'
 * @param string|null $message The text message to display
 * @return string|array|null
 */
function flash($type = null, $message = null) {
    if ($type !== null && $message !== null) {
        $_SESSION['flash'][$type] = $message;
        return null;
    }
    
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    
    return null;
}

/**
 * Returns a unique formatted Virtual ID for a worker based on their profile ID
 * 
 * @param int $worker_profile_id
 * @return string
 */
function get_worker_virtual_id($worker_profile_id) {
    if (empty($worker_profile_id)) {
        return 'GW-W-0000';
    }
    return 'GW-W-' . str_pad($worker_profile_id, 4, '0', STR_PAD_LEFT);
}

/**
 * Queries and retrieves the worker profile ID for a given user ID
 * 
 * @param int $user_id
 * @return int|null
 */
function get_worker_profile_id_by_user_id($user_id) {
    global $pdo;
    if (isset($pdo) && $user_id > 0) {
        try {
            $stmt = $pdo->prepare("SELECT id FROM worker_profiles WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                return intval($row['id']);
            }
        } catch (PDOException $e) {
            error_log("Database error in get_worker_profile_id_by_user_id: " . $e->getMessage());
        }
    }
    return null;
}

