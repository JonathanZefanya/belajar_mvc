<?php
/**
 * File Konfigurasi Utama Aplikasi
 * 
 * File ini berisi konstanta dan konfigurasi yang digunakan di seluruh aplikasi
 * termasuk pengaturan database, URL, dan path direktori.
 */

// Konstanta Database - Sesuaikan dengan konfigurasi XAMPP Anda
define('DB_HOST', 'localhost');      // Host database
define('DB_NAME', 'praktikum_mvc');  // Nama database
define('DB_USER', 'root');           // Username database
define('DB_PASS', 'root');               // Password database (kosong untuk XAMPP default)

// Konstanta URL - Base URL aplikasi untuk routing
define('BASE_URL', 'http://localhost/praktikum/mvc/public');

// Konstanta Path - Path direktori untuk kebutuhan file upload dan include
define('ROOT_PATH', dirname(__DIR__));
define('UPLOAD_PATH', ROOT_PATH . '/public/assets/uploads/');
define('UPLOAD_URL', BASE_URL . '/assets/uploads/');

// Konstanta Upload - Validasi file upload
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png']);
define('MAX_FILE_SIZE', 2097152); // 2MB dalam bytes (2 * 1024 * 1024)

// Konstanta Session
define('SESSION_LIFETIME', 3600); // 1 jam dalam detik

// Timezone
date_default_timezone_set('Asia/Jakarta');

// Error reporting (set ke 0 di production)
error_reporting(E_ALL);
ini_set('display_errors', value: 1);
