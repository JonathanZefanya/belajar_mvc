<?php
/**
 * AuthController
 * 
 * Controller untuk menangani autentikasi user (login, logout, register).
 * Menggunakan password_hash, session_regenerate_id, dan CSRF token.
 */
class AuthController extends Controller {
    private $userModel;

    /**
     * Constructor - Initialize User model
     */
    public function __construct() {
        $this->userModel = $this->model('User');
    }

    /**
     * Display login form
     */
    public function login() {
        // Redirect jika sudah login
        if ($this->isLoggedIn()) {
            $this->redirect('dashboard');
        }

        // Generate CSRF token
        $csrfToken = $this->generateCSRFToken();

        $this->view('auth/login', [
            'csrf_token' => $csrfToken
        ], null); // null = tanpa layout
    }

    /**
     * Process login form submission
     */
    public function processLogin() {
        // Cek request method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('auth/login');
        }

        // Validasi CSRF token
        if (!$this->validateCSRFToken($_POST['csrf_token'] ?? '')) {
            $this->setFlash('error', 'Invalid CSRF token');
            $this->redirect('auth/login');
        }

        // Sanitize input
        $identifier = $this->sanitize($_POST['identifier'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validasi input
        if (empty($identifier) || empty($password)) {
            $this->setFlash('error', 'Please fill all fields');
            $this->redirect('auth/login');
        }

        // Authenticate user
        $user = $this->userModel->authenticate($identifier, $password);

        if ($user) {
            // Regenerate session ID untuk keamanan (prevent session fixation)
            session_regenerate_id(true);

            // Set session data
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['login_time'] = time();

            // Log IP dan User Agent untuk tracking (optional)
            $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
            $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];

            $this->setFlash('success', 'Welcome back, ' . $user['username'] . '!');
            $this->redirect('dashboard');
        } else {
            $this->setFlash('error', 'Invalid username/email or password');
            $this->redirect('auth/login');
        }
    }

    /**
     * Logout user
     */
    public function logout() {
        // Destroy session
        session_unset();
        session_destroy();

        // Start new session untuk flash message
        session_start();
        $this->setFlash('success', 'You have been logged out successfully');
        
        $this->redirect('auth/login');
    }

    /**
     * Display register form (hanya untuk demo, dalam production mungkin restricted)
     */
    public function register() {
        // Redirect jika sudah login
        if ($this->isLoggedIn()) {
            $this->redirect('dashboard');
        }

        // Generate CSRF token
        $csrfToken = $this->generateCSRFToken();

        $this->view('auth/register', [
            'csrf_token' => $csrfToken
        ], null);
    }

    /**
     * Process register form submission
     */
    public function processRegister() {
        // Cek request method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('auth/register');
        }

        // Validasi CSRF token
        if (!$this->validateCSRFToken($_POST['csrf_token'] ?? '')) {
            $this->setFlash('error', 'Invalid CSRF token');
            $this->redirect('auth/register');
        }

        // Sanitize input
        $username = $this->sanitize($_POST['username'] ?? '');
        $email = $this->sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Validasi input
        $errors = [];

        if (empty($username)) {
            $errors[] = 'Username is required';
        } elseif (strlen($username) < 3) {
            $errors[] = 'Username must be at least 3 characters';
        } elseif ($this->userModel->usernameExists($username)) {
            $errors[] = 'Username already exists';
        }

        if (empty($email)) {
            $errors[] = 'Email is required';
        } elseif (!$this->validateEmail($email)) {
            $errors[] = 'Invalid email format';
        } elseif ($this->userModel->emailExists($email)) {
            $errors[] = 'Email already exists';
        }

        if (empty($password)) {
            $errors[] = 'Password is required';
        } elseif (strlen($password) < 6) {
            $errors[] = 'Password must be at least 6 characters';
        }

        if ($password !== $confirmPassword) {
            $errors[] = 'Passwords do not match';
        }

        // Jika ada error, redirect dengan error message
        if (!empty($errors)) {
            $_SESSION['register_errors'] = $errors;
            $_SESSION['register_data'] = ['username' => $username, 'email' => $email];
            $this->redirect('auth/register');
        }

        // Create user (default role: user)
        $userId = $this->userModel->create([
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'role' => 'user'
        ]);

        if ($userId) {
            $this->setFlash('success', 'Registration successful! Please login.');
            $this->redirect('auth/login');
        } else {
            $this->setFlash('error', 'Registration failed. Please try again.');
            $this->redirect('auth/register');
        }
    }
}
