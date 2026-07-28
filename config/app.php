<?php
/**
 * GoWorker - Application Configuration and Helpers
 */

// Dynamic Protocol Detection
$protocol = 'http';
if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] === 1)) {
    $protocol = 'https';
} elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $protocol = 'https';
} elseif (isset($_SERVER['HTTP_CF_VISITOR'])) {
    $visitor = json_decode($_SERVER['HTTP_CF_VISITOR'], true);
    if (isset($visitor['scheme']) && $visitor['scheme'] === 'https') {
        $protocol = 'https';
    }
}

// Dynamic Host Detection
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

// Dynamic Subfolder Detection
$app_root_orig = str_replace('\\', '/', dirname(__DIR__));
$doc_root_orig = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? '');

$app_root_lower = strtolower($app_root_orig);
$doc_root_lower = strtolower($doc_root_orig);

$subfolder = '';
if (!empty($doc_root_lower) && strpos($app_root_lower, $doc_root_lower) === 0) {
    $subfolder = substr($app_root_orig, strlen($doc_root_orig));
    $subfolder = '/' . trim(str_replace('\\', '/', $subfolder), '/');
}
if ($subfolder === '/') {
    $subfolder = '';
}

// Override subfolder if accessed via Cloudflare Tunnel or any public proxy/domain
$is_localhost_access = (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false || strpos($host, '::1') !== false);
$is_lan_ip_access = preg_match('/^(?:192\.168\.|10\.|172\.(?:1[6-9]|2[0-9]|3[0-1])\.)/', $host);

if (!$is_localhost_access && !$is_lan_ip_access) {
    $subfolder = '';
}

// Define BASE_URL
$base_url = $protocol . '://' . $host . $subfolder;
if (!defined('BASE_URL')) {
    define('BASE_URL', $base_url);
}

// Define helper functions
if (!function_exists('base_url')) {
    function base_url() {
        return BASE_URL;
    }
}

if (!function_exists('url')) {
    function url($path = '') {
        $base = rtrim(BASE_URL, '/');
        if (preg_match('/^https?:\/\//i', $path)) {
            return $path;
        }
        
        $path = str_replace('\\', '/', $path);
        
        // Extract subfolder from BASE_URL to check if the path already starts with it
        $parsed = parse_url(BASE_URL);
        $subpath = isset($parsed['path']) ? trim($parsed['path'], '/') : '';
        
        $path_clean = ltrim($path, '/');
        
        if ($subpath !== '') {
            $subpath_pattern = '/^' . preg_quote($subpath, '/') . '\//i';
            if (preg_match($subpath_pattern, $path_clean)) {
                $path_clean = substr($path_clean, strlen($subpath));
                $path_clean = ltrim($path_clean, '/');
            }
        }
        
        return $path_clean === '' ? $base : $base . '/' . $path_clean;
    }
}

if (!function_exists('asset')) {
    function asset($path = '') {
        return url($path);
    }
}

if (!function_exists('image_url')) {
    function image_url($path = '') {
        if (empty($path)) {
            return url('images/avatar_placeholder.png');
        }
        if (preg_match('/^https?:\/\//i', $path)) {
            return $path;
        }
        return url($path);
    }
}

if (!function_exists('upload_url')) {
    function upload_url($path = '') {
        if (empty($path)) {
            return '';
        }
        if (preg_match('/^https?:\/\//i', $path)) {
            return $path;
        }
        return url($path);
    }
}

if (!function_exists('redirect')) {
    function redirect($path) {
        $url = url($path);
        session_write_close();
        if (!headers_sent()) {
            header("Location: $url");
        } else {
            echo "<script>window.location.href='" . addslashes($url) . "';</script>";
        }
        exit();
    }
}

// Session cookie protection configuration dynamically matching transport security
if (session_status() === PHP_SESSION_NONE) {
    $is_secure = ($protocol === 'https');
    $params = session_get_cookie_params();
    session_set_cookie_params([
        'lifetime' => $params['lifetime'] ?? 0,
        'path' => $params['path'] ?? '/',
        'domain' => $params['domain'] ?? '',
        'secure' => $is_secure,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}
