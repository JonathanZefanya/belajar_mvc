<?php
/**
 * Class Controller
 * 
 * Base controller yang diextend oleh semua controller lainnya.
 * Menyediakan method helper untuk view rendering, redirect, dan CSRF protection.
 */
class Controller {
    
    /**
     * Load view file dengan data
     * 
     * @param string $view Nama view file (tanpa .php)
     * @param array $data Data yang akan dikirim ke view
     * @param string|null $layout Layout yang digunakan (null untuk tanpa layout)
     */
    protected function view($view, $data = [], $layout = 'default') {
        // Extract data array menjadi variable
        extract($data);
        
        // Path ke view file
        $viewPath = ROOT_PATH . '/app/views/' . $view . '.php';
        
        if (file_exists($viewPath)) {
            if ($layout) {
                // Gunakan layout
                $layoutPath = ROOT_PATH . '/app/views/layouts/' . $layout . '.php';
                if (file_exists($layoutPath)) {
                    // Capture view content
                    ob_start();
                    require $viewPath;
                    $content = ob_get_clean();
                    
                    // Load layout dengan content
                    require $layoutPath;
                } else {
                    die("Layout file not found: " . $layoutPath);
                }
            } else {
                // Load view tanpa layout
                require $viewPath;
            }
        } else {
            die("View file not found: " . $viewPath);
        }
    }

    /**
     * Load model
     * 
     * @param string $model Nama model
     * @return object Instance dari model
     */
    protected function model($model) {
        $modelPath = ROOT_PATH . '/app/models/' . $model . '.php';
        
        if (file_exists($modelPath)) {
            require_once $modelPath;
            return new $model();
        } else {
            die("Model file not found: " . $modelPath);
        }
    }

    /**
     * Redirect ke URL tertentu
     * 
     * @param string $url URL tujuan (relatif dari BASE_URL)
     */
    protected function redirect($url) {
        header('Location: ' . BASE_URL . '/' . ltrim($url, '/'));
        exit;
    }

    /**
     * Generate CSRF token dan simpan di session
     * 
     * @return string CSRF token
     */
    protected function generateCSRFToken() {
        if (empty($_SESSION['csrf_token'])) {
            // Generate random token menggunakan random_bytes (lebih aman)
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Validasi CSRF token dari request
     * 
     * @param string $token Token yang dikirim dari form
     * @return bool True jika valid, false jika tidak
     */
    protected function validateCSRFToken($token) {
        // Cek apakah token ada di session dan cocok dengan yang dikirim
        if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            return false;
        }
        return true;
    }

    /**
     * Set flash message di session
     * 
     * @param string $type Tipe message (success, error, warning, info)
     * @param string $message Pesan yang akan ditampilkan
     */
    protected function setFlash($type, $message) {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message
        ];
    }

    /**
     * Get flash message dan hapus dari session
     * 
     * @return array|null Flash data atau null jika tidak ada
     */
    protected function getFlash() {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }

    /**
     * Cek apakah user sudah login
     * 
     * @return bool True jika sudah login
     */
    protected function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    /**
     * Cek apakah user adalah admin
     * 
     * @return bool True jika user adalah admin
     */
    protected function isAdmin() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }

    /**
     * Require login - redirect ke login jika belum login
     */
    protected function requireLogin() {
        if (!$this->isLoggedIn()) {
            $this->setFlash('error', 'Please login first');
            $this->redirect('auth/login');
        }
    }

    /**
     * Require admin - redirect jika bukan admin
     */
    protected function requireAdmin() {
        $this->requireLogin();
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied. Admin only.');
            $this->redirect('dashboard');
        }
    }

    /**
     * Get current user data dari session
     * 
     * @return array|null User data atau null
     */
    protected function getCurrentUser() {
        if ($this->isLoggedIn()) {
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
     * Sanitize input untuk mencegah XSS
     * 
     * @param string $data Input data
     * @return string Sanitized data
     */
    protected function sanitize($data) {
        return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Validasi email format
     * 
     * @param string $email Email address
     * @return bool True jika valid
     */
    protected function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Return JSON response
     * 
     * @param array $data Data untuk di-encode ke JSON
     * @param int $statusCode HTTP status code
     */
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
