<?php
/**
 * GoWorker - Global Configuration
 * 
 * Central configuration file containing database credentials, network setup,
 * and environment settings for localhost and collaborative local network (LAN) setups.
 */

// Load the central configuration
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/app.php';

// Toggle between 'production' and 'development' modes
if (!defined('ENV_MODE')) {
    define('ENV_MODE', 'development');
}

// Default fallback credentials for XAMPP localhost
$default_host = 'localhost';
$default_port = '3306';
$default_db   = 'goworker';
$default_user = 'root';
$default_pass = '';

// Auto-detect LAN environment: If accessing via local IP address instead of localhost,
// adjust host and credentials automatically to target the central host
if (isset($_SERVER['HTTP_HOST'])) {
    $http_host = parse_url('http://' . $_SERVER['HTTP_HOST'], PHP_URL_HOST);
    if ($http_host && $http_host !== 'localhost' && $http_host !== '127.0.0.1' && $http_host !== '::1') {
        // Only override if accessed via a local private network IP address (LAN IP)
        $is_lan_ip = preg_match('/^(?:192\.168\.|10\.|172\.(?:1[6-9]|2[0-9]|3[0-1])\.)/', $http_host);
        
        // Exclude Cloudflare Tunnel domains and general Cloudflare proxy requests
        $is_cloudflare = isset($_SERVER['HTTP_CF_RAY']) || isset($_SERVER['HTTP_CF_CONNECTING_IP']) || (strpos($http_host, 'trycloudflare.com') !== false);
        
        if ($is_lan_ip && !$is_cloudflare) {
            $default_host = $http_host;
            $default_user = 'goworker_dev';
            $default_pass = 'GoWorkerLAN2026!';
        }
    }
}

// Load local developer credential overrides if present (not tracked by Git)
$local_config_path = __DIR__ . '/config_local.json';
$local_config = [];
if (file_exists($local_config_path)) {
    $local_config = json_decode(file_get_contents($local_config_path), true) ?: [];
}

// Final Database Constant definitions
if (!defined('DB_HOST')) {
    define('DB_HOST', getenv('DB_HOST') ?: ($local_config['db_host'] ?? $default_host));
}
if (!defined('DB_PORT')) {
    define('DB_PORT', getenv('DB_PORT') ?: ($local_config['db_port'] ?? $default_port));
}
if (!defined('DB_NAME')) {
    define('DB_NAME', getenv('DB_NAME') ?: ($local_config['db_name'] ?? $default_db));
}
if (!defined('DB_USER')) {
    define('DB_USER', getenv('DB_USER') ?: ($local_config['db_user'] ?? $default_user));
}
if (!defined('DB_PASS')) {
    define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : ($local_config['db_pass'] ?? $default_pass));
}
if (!defined('DB_CHARSET')) {
    define('DB_CHARSET', 'utf8mb4');
}

// Connection timeout and retry options
define('DB_CONNECT_TIMEOUT', 3);
define('DB_MAX_RETRIES', 2);
define('DB_RETRY_DELAY', 1);

// Backup and Error logging directories
define('BACKUP_DIR', __DIR__ . '/backups');
define('LOG_FILE', __DIR__ . '/logs/db_error.log');

// Ensure system directories exist safely (suppressing warnings if permissions are restricted on shared hosts)
if (!is_dir(BACKUP_DIR)) {
    @mkdir(BACKUP_DIR, 0777, true);
}
if (!is_dir(__DIR__ . '/logs')) {
    @mkdir(__DIR__ . '/logs', 0777, true);
}
