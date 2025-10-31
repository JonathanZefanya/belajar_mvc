<?php
/**
 * Entry Point Aplikasi
 * 
 * File ini adalah entry point utama aplikasi yang menangani semua request.
 * Menggunakan session untuk authentication dan memuat semua core files.
 */

// Start session dengan konfigurasi aman
session_start([
    'cookie_httponly' => true,  // Prevent XSS attacks
    'cookie_samesite' => 'Lax', // CSRF protection
    'use_strict_mode' => true   // Prevent session fixation
]);

// Load configuration
require_once __DIR__ . '/../config/config.php';

// Load helper functions
require_once ROOT_PATH . '/app/core/helpers.php';

// Load core classes
require_once ROOT_PATH . '/app/core/Database.php';
require_once ROOT_PATH . '/app/core/Controller.php';
require_once ROOT_PATH . '/app/core/App.php';

// Initialize application
$app = new App();
