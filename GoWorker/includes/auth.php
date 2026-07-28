<?php
/**
 * Authentication helpers
 */
require_once __DIR__ . '/functions.php';

/**
 * Checks if a user is logged in
 * 
 * @return bool
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Gets the current logged in user type
 * 
 * @return string|null
 */
function current_user_type() {
    return $_SESSION['user_type'] ?? null;
}

/**
 * Restricts access to logged-in users only. Redirects to login if guest.
 */
function requireLogin() {
    if (!is_logged_in()) {
        flash('error', 'Please log in to access this page.');
        redirect('login.php');
    }
}

/**
 * Restricts access to customers only. Redirects workers to their dashboard.
 */
function requireCustomer() {
    requireLogin();
    if (current_user_type() !== 'customer') {
        flash('error', 'This page is restricted to customers.');
        redirect('worker-dashboard.php');
    }
}

/**
 * Restricts access to workers only. Redirects customers to their dashboard.
 */
function requireWorker() {
    requireLogin();
    if (current_user_type() !== 'worker') {
        flash('error', 'This page is restricted to workers.');
        redirect('customer-dashboard.php');
    }
}
