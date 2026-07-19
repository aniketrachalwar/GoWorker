<?php
/**
 * Security and Utility Functions
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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

/**
 * Safe redirection helper
 * 
 * @param string $url
 * @return void
 */
function redirect($url) {
    header("Location: $url");
    exit();
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
