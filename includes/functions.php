<?php
/**
 * Helper Functions for Lugomax Logistics
 */

// Session Management
function init_session() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

// Security Functions
function sanitize_input($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

function escape($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// CSRF Protection
function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Flash Messages
function set_flash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function display_flash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        $class = $flash['type'] === 'success' ? 'alert-success' : 'alert-error';
        echo '<div class="alert ' . $class . '">' . escape($flash['message']) . '</div>';
        unset($_SESSION['flash']);
    }
}

// Redirect
function redirect($url) {
    header("Location: " . $url);
    exit;
}

// Date Formatting
function format_date($date, $format = 'F j, Y') {
    return date($format, strtotime($date));
}

function time_ago($datetime) {
    $time = strtotime($datetime);
    $diff = time() - $time;
    
    if ($diff < 60) return $diff . ' seconds ago';
    if ($diff < 3600) return floor($diff / 60) . ' minutes ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
    if ($diff < 604800) return floor($diff / 86400) . ' days ago';
    return date('M j, Y', $time);
}

// Settings
function get_setting($key, $default = null) {
    $db = getDB();
    $stmt = $db->prepare("SELECT setting_value FROM site_settings WHERE setting_key = ?");
    $stmt->execute([$key]);
    $result = $stmt->fetch();
    return $result ? $result['setting_value'] : $default;
}

// Email Function
function send_email($to, $subject, $body) {
    $headers = "From: " . SITE_EMAIL . "\r\n";
    $headers .= "Reply-To: " . SITE_EMAIL . "\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    return mail($to, $subject, $body, $headers);
}

// Generate Tracking Number
function generate_tracking_number() {
    $prefix = get_setting('tracking_prefix', 'LGX');
    return $prefix . '-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));
}

// Check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function require_login() {
    if (!is_logged_in()) {
        redirect('/admin/login.php');
    }
}

// Get logged in user (RENAMED from get_current_user to avoid PHP conflict)
function get_logged_in_user() {
    if (!is_logged_in()) return null;
    
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}