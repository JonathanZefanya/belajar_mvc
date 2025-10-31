<?php
/**
 * UserController
 * 
 * Controller untuk user management (hanya admin).
 * Menangani CRUD users dengan validasi dan CSRF protection.
 */
class UserController extends Controller {
    private $userModel;

    /**
     * Constructor - Initialize User model
     */
    public function __construct() {
        $this->userModel = $this->model('User');
    }

    /**
     * Display list of users
     */
    public function index() {
        // Require admin
        $this->requireAdmin();

        // Get all users
        $users = $this->userModel->getAll();

        parent::view('users/index', [
            'title' => 'User Management',
            'users' => $users
        ]);
    }

    /**
     * Display create user form
     */
    public function create() {
        // Require admin
        $this->requireAdmin();

        // Generate CSRF token
        $csrfToken = $this->generateCSRFToken();

        parent::view('users/create', [
            'title' => 'Add New User',
            'csrf_token' => $csrfToken
        ]);
    }

    /**
     * Process create user form
     */
    public function store() {
        // Require admin
        $this->requireAdmin();

        // Cek request method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('user/create');
        }

        // Validasi CSRF token
        if (!$this->validateCSRFToken($_POST['csrf_token'] ?? '')) {
            $this->setFlash('error', 'Invalid CSRF token');
            $this->redirect('user/create');
        }

        // Sanitize input
        $username = $this->sanitize($_POST['username'] ?? '');
        $email = $this->sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $role = $_POST['role'] ?? 'user';

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

        if (!in_array($role, ['admin', 'user'])) {
            $errors[] = 'Invalid role';
        }

        // Jika ada error, redirect dengan error message
        if (!empty($errors)) {
            $_SESSION['user_errors'] = $errors;
            $_SESSION['user_data'] = $_POST;
            $this->redirect('user/create');
        }

        // Create user
        $userId = $this->userModel->create([
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'role' => $role
        ]);

        if ($userId) {
            $this->setFlash('success', 'User created successfully');
            $this->redirect('user');
        } else {
            $this->setFlash('error', 'Failed to create user');
            $this->redirect('user/create');
        }
    }

    /**
     * Display edit user form
     */
    public function edit($id) {
        // Require admin
        $this->requireAdmin();

        // Get user
        $user = $this->userModel->getById($id);

        if (!$user) {
            $this->setFlash('error', 'User not found');
            $this->redirect('user');
        }

        // Generate CSRF token
        $csrfToken = $this->generateCSRFToken();

        parent::view('users/edit', [
            'title' => 'Edit User',
            'user' => $user,
            'csrf_token' => $csrfToken
        ]);
    }

    /**
     * Process edit user form
     */
    public function update($id) {
        // Require admin
        $this->requireAdmin();

        // Cek request method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('user/edit/' . $id);
        }

        // Validasi CSRF token
        if (!$this->validateCSRFToken($_POST['csrf_token'] ?? '')) {
            $this->setFlash('error', 'Invalid CSRF token');
            $this->redirect('user/edit/' . $id);
        }

        // Get user
        $user = $this->userModel->getById($id);

        if (!$user) {
            $this->setFlash('error', 'User not found');
            $this->redirect('user');
        }

        // Sanitize input
        $username = $this->sanitize($_POST['username'] ?? '');
        $email = $this->sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $role = $_POST['role'] ?? 'user';

        // Validasi input
        $errors = [];

        if (empty($username)) {
            $errors[] = 'Username is required';
        } elseif (strlen($username) < 3) {
            $errors[] = 'Username must be at least 3 characters';
        } elseif ($this->userModel->usernameExists($username, $id)) {
            $errors[] = 'Username already exists';
        }

        if (empty($email)) {
            $errors[] = 'Email is required';
        } elseif (!$this->validateEmail($email)) {
            $errors[] = 'Invalid email format';
        } elseif ($this->userModel->emailExists($email, $id)) {
            $errors[] = 'Email already exists';
        }

        // Password optional untuk update
        if (!empty($password)) {
            if (strlen($password) < 6) {
                $errors[] = 'Password must be at least 6 characters';
            }

            if ($password !== $confirmPassword) {
                $errors[] = 'Passwords do not match';
            }
        }

        if (!in_array($role, ['admin', 'user'])) {
            $errors[] = 'Invalid role';
        }

        // Jika ada error, redirect dengan error message
        if (!empty($errors)) {
            $_SESSION['user_errors'] = $errors;
            $this->redirect('user/edit/' . $id);
        }

        // Prepare update data
        $updateData = [
            'username' => $username,
            'email' => $email,
            'role' => $role
        ];

        // Add password jika diisi
        if (!empty($password)) {
            $updateData['password'] = $password;
        }

        // Update user
        if ($this->userModel->update($id, $updateData)) {
            $this->setFlash('success', 'User updated successfully');
            $this->redirect('user');
        } else {
            $this->setFlash('error', 'Failed to update user');
            $this->redirect('user/edit/' . $id);
        }
    }

    /**
     * Delete user
     */
    public function delete($id) {
        // Require admin
        $this->requireAdmin();

        // Cek CSRF token dari POST request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('user');
        }

        // Validasi CSRF token
        if (!$this->validateCSRFToken($_POST['csrf_token'] ?? '')) {
            $this->setFlash('error', 'Invalid CSRF token');
            $this->redirect('user');
        }

        // Jangan hapus diri sendiri
        $currentUser = $this->getCurrentUser();
        if ($currentUser['id'] == $id) {
            $this->setFlash('error', 'You cannot delete your own account');
            $this->redirect('user');
        }

        // Delete user
        if ($this->userModel->delete($id)) {
            $this->setFlash('success', 'User deleted successfully');
        } else {
            $this->setFlash('error', 'Failed to delete user. Cannot delete the last admin.');
        }

        $this->redirect('user');
    }
}
