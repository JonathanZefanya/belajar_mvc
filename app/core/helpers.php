<?php
/**
 * Helper Functions
 * 
 * File ini berisi fungsi-fungsi helper yang dapat digunakan
 * di seluruh aplikasi untuk mempermudah development.
 */

/**
 * Debug helper - Dump variable dan stop execution
 * 
 * @param mixed $data Data yang akan di-dump
 */
function dd($data) {
    echo '<pre style="background: #1e293b; color: #e2e8f0; padding: 20px; border-radius: 8px; font-size: 14px; line-height: 1.5;">';
    var_dump($data);
    echo '</pre>';
    die();
}

/**
 * Print readable array/object
 * 
 * @param mixed $data Data yang akan di-print
 */
function pr($data) {
    echo '<pre style="background: #f1f5f9; padding: 15px; border-radius: 8px; border-left: 4px solid #3b82f6;">';
    print_r($data);
    echo '</pre>';
}

/**
 * Asset URL helper
 * 
 * @param string $path Path relatif dari assets
 * @return string Full URL ke asset
 */
function asset($path) {
    return BASE_URL . '/assets/' . ltrim($path, '/');
}

/**
 * URL helper
 * 
 * @param string $path Path URL
 * @return string Full URL
 */
function url($path = '') {
    return BASE_URL . '/' . ltrim($path, '/');
}

/**
 * Redirect helper
 * 
 * @param string $path Path tujuan
 */
function redirect($path) {
    header('Location: ' . BASE_URL . '/' . ltrim($path, '/'));
    exit;
}

/**
 * Old input helper (untuk form validation)
 * 
 * @param string $key Key dari input
 * @param mixed $default Default value
 * @return mixed
 */
function old($key, $default = '') {
    if (isset($_SESSION['old_input'][$key])) {
        $value = $_SESSION['old_input'][$key];
        return htmlspecialchars($value);
    }
    return $default;
}

/**
 * Flash message helper
 * 
 * @param string $key Key message
 * @param string $value Value message
 */
function flash($key, $value = null) {
    if ($value === null) {
        // Get flash message
        if (isset($_SESSION['flash'][$key])) {
            $msg = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $msg;
        }
        return null;
    } else {
        // Set flash message
        $_SESSION['flash'][$key] = $value;
    }
}

/**
 * Check if user is authenticated
 * 
 * @return bool
 */
function auth() {
    return isset($_SESSION['user_id']);
}

/**
 * Get current user data
 * 
 * @return array|null
 */
function currentUser() {
    if (auth()) {
        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'email' => $_SESSION['email'],
            'role' => $_SESSION['role']
        ];
    }
    return null;
}

/**
 * Check if user is admin
 * 
 * @return bool
 */
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Format date
 * 
 * @param string $date Date string
 * @param string $format Format output
 * @return string
 */
function formatDate($date, $format = 'd M Y') {
    return date($format, strtotime($date));
}

/**
 * Format datetime
 * 
 * @param string $datetime Datetime string
 * @param string $format Format output
 * @return string
 */
function formatDateTime($datetime, $format = 'd M Y H:i') {
    return date($format, strtotime($datetime));
}

/**
 * Time ago helper
 * 
 * @param string $datetime Datetime string
 * @return string
 */
function timeAgo($datetime) {
    $time = strtotime($datetime);
    $diff = time() - $time;
    
    if ($diff < 60) {
        return 'just now';
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . ' minute' . ($mins > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } else {
        return date('M d, Y', $time);
    }
}

/**
 * Truncate text
 * 
 * @param string $text Text to truncate
 * @param int $length Max length
 * @param string $suffix Suffix for truncated text
 * @return string
 */
function truncate($text, $length = 100, $suffix = '...') {
    if (strlen($text) > $length) {
        return substr($text, 0, $length) . $suffix;
    }
    return $text;
}

/**
 * Sanitize HTML
 * 
 * @param string $html HTML string
 * @return string
 */
function sanitizeHtml($html) {
    return htmlspecialchars($html, ENT_QUOTES, 'UTF-8');
}

/**
 * Generate random string
 * 
 * @param int $length Length of string
 * @return string
 */
function randomString($length = 10) {
    return substr(bin2hex(random_bytes($length)), 0, $length);
}

/**
 * Check if request is POST
 * 
 * @return bool
 */
function isPost() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * Check if request is GET
 * 
 * @return bool
 */
function isGet() {
    return $_SERVER['REQUEST_METHOD'] === 'GET';
}

/**
 * Get request input
 * 
 * @param string $key Input key
 * @param mixed $default Default value
 * @return mixed
 */
function input($key, $default = null) {
    if (isset($_POST[$key])) {
        return $_POST[$key];
    }
    if (isset($_GET[$key])) {
        return $_GET[$key];
    }
    return $default;
}

/**
 * Success response JSON
 * 
 * @param array $data Response data
 * @param int $code HTTP status code
 */
function jsonSuccess($data = [], $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'data' => $data
    ]);
    exit;
}

/**
 * Error response JSON
 * 
 * @param string $message Error message
 * @param int $code HTTP status code
 */
function jsonError($message, $code = 400) {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'error' => $message
    ]);
    exit;
}
