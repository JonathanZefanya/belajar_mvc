-- ============================================
-- Schema Database untuk Aplikasi MVC Praktikum
-- ============================================

-- Buat database jika belum ada
CREATE DATABASE IF NOT EXISTS praktikum_mvc CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE praktikum_mvc;

-- ============================================
-- Tabel Users
-- Menyimpan data pengguna dengan role admin/user
-- ============================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,  -- Password di-hash dengan password_hash()
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabel Universities
-- Menyimpan data universitas dengan gambar
-- ============================================
CREATE TABLE IF NOT EXISTS universities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    address TEXT NOT NULL,
    description TEXT,
    image VARCHAR(255),  -- Nama file gambar
    website VARCHAR(255),
    phone VARCHAR(20),
    email VARCHAR(100),
    created_by INT NOT NULL,  -- ID user yang membuat
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_name (name),
    INDEX idx_created_by (created_by)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabel Sessions
-- Menyimpan session aktif untuk keamanan tambahan
-- ============================================
CREATE TABLE IF NOT EXISTS sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id INT NOT NULL,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_last_activity (last_activity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Data Awal (Seeding)
-- ============================================

-- Insert admin default
-- Username: admin
-- Password: password (akan di-hash)
INSERT INTO users (username, email, password, role) VALUES 
('admin', 'admin@praktikum.com', '$2y$10$GY7pt/B1RN5liTvW6l1rxu.nZJiDI8Nb1xul8.Vo8YzEp4JNCzVmS', 'admin');
-- Password di atas adalah hash dari 'password'

-- Insert user default
-- Username: user
-- Password: password (akan di-hash)
INSERT INTO users (username, email, password, role) VALUES 
('user', 'user@praktikum.com', '$2y$10$GY7pt/B1RN5liTvW6l1rxu.nZJiDI8Nb1xul8.Vo8YzEp4JNCzVmS', 'user');
-- Password di atas adalah hash dari 'password'

-- Insert sample universities (dibuat oleh admin)
INSERT INTO universities (name, address, description, website, phone, email, created_by) VALUES 
(
    'Universitas Indonesia',
    'Jl. Margonda Raya, Pondok Cina, Beji, Kota Depok, Jawa Barat',
    'Universitas Indonesia adalah perguruan tinggi negeri di Indonesia yang terletak di Depok, Jawa Barat, dan Salemba, Jakarta. UI adalah salah satu universitas tertua dan terkemuka di Indonesia.',
    'https://www.ui.ac.id',
    '(021) 7867222',
    'humas-ui@ui.ac.id',
    1
),
(
    'Institut Teknologi Bandung',
    'Jl. Ganesha No.10, Lb. Siliwangi, Kecamatan Coblong, Kota Bandung, Jawa Barat',
    'Institut Teknologi Bandung adalah sekolah tinggi teknik pertama di Indonesia yang didirikan pada tanggal 2 Maret 1959 di Bandung.',
    'https://www.itb.ac.id',
    '(022) 2500935',
    'humas@itb.ac.id',
    1
),
(
    'Universitas Gadjah Mada',
    'Bulaksumur, Caturtunggal, Kec. Depok, Kabupaten Sleman, Daerah Istimewa Yogyakarta',
    'Universitas Gadjah Mada adalah universitas negeri yang terletak di Yogyakarta, Indonesia. UGM merupakan universitas pertama yang didirikan oleh Pemerintah Indonesia setelah Indonesia merdeka.',
    'https://www.ugm.ac.id',
    '(0274) 588688',
    'info@ugm.ac.id',
    1
);

-- ============================================
-- Catatan Penting
-- ============================================
-- 1. Password default untuk admin dan user adalah 'password'
-- 2. Untuk keamanan, ganti password setelah login pertama kali
-- 3. Pastikan folder public/assets/uploads/ memiliki permission write
-- 4. Sesuaikan konfigurasi database di config/config.php
