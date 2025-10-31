<?php
/**
 * UniversityController
 * 
 * Controller untuk CRUD universitas dengan upload gambar.
 * Menggunakan CSRF protection dan validasi upload.
 */
class UniversityController extends Controller {
    private $universityModel;

    /**
     * Constructor - Initialize University model
     */
    public function __construct() {
        $this->universityModel = $this->model('University');
    }

    /**
     * Display list of universities dengan pagination dan search
     */
    public function index() {
        // Require login
        $this->requireLogin();

        // Get search keyword dan pagination
        $search = $_GET['search'] ?? '';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10; // Universities per page
        $offset = ($page - 1) * $limit;

        // Get universities
        $universities = $this->universityModel->getAll($limit, $offset, $search);
        
        // Get total untuk pagination
        $total = $this->universityModel->getTotalCount($search);
        $totalPages = ceil($total / $limit);

        parent::view('universities/index', [
            'title' => 'Universities',
            'universities' => $universities,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'search' => $search
        ]);
    }

    /**
     * Display single university detail
     */
    public function detail($id) {
        // Require login
        $this->requireLogin();

        // Get university
        $university = $this->universityModel->getById($id);

        if (!$university) {
            $this->setFlash('error', 'University not found');
            $this->redirect('university');
        }

        parent::view('universities/detail', [
            'title' => $university['name'],
            'university' => $university
        ]);
    }

    /**
     * Display create form
     */
    public function create() {
        // Require login
        $this->requireLogin();

        // Generate CSRF token
        $csrfToken = $this->generateCSRFToken();

        parent::view('universities/create', [
            'title' => 'Add New University',
            'csrf_token' => $csrfToken
        ]);
    }

    /**
     * Process create form submission
     */
    public function store() {
        // Require login
        $this->requireLogin();

        // Cek request method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('university/create');
        }

        // Validasi CSRF token
        if (!$this->validateCSRFToken($_POST['csrf_token'] ?? '')) {
            $this->setFlash('error', 'Invalid CSRF token');
            $this->redirect('university/create');
        }

        // Sanitize input
        $name = $this->sanitize($_POST['name'] ?? '');
        $address = $this->sanitize($_POST['address'] ?? '');
        $description = $this->sanitize($_POST['description'] ?? '');
        $website = $this->sanitize($_POST['website'] ?? '');
        $phone = $this->sanitize($_POST['phone'] ?? '');
        $email = $this->sanitize($_POST['email'] ?? '');

        // Validasi input
        $errors = [];

        if (empty($name)) {
            $errors[] = 'University name is required';
        }

        if (empty($address)) {
            $errors[] = 'Address is required';
        }

        if (!empty($email) && !$this->validateEmail($email)) {
            $errors[] = 'Invalid email format';
        }

        // Handle image upload
        $imageName = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $uploadResult = $this->universityModel->uploadImage($_FILES['image']);
            
            if ($uploadResult['success']) {
                $imageName = $uploadResult['filename'];
            } else {
                $errors[] = $uploadResult['error'];
            }
        }

        // Jika ada error, redirect dengan error message
        if (!empty($errors)) {
            $_SESSION['university_errors'] = $errors;
            $_SESSION['university_data'] = $_POST;
            $this->redirect('university/create');
        }

        // Get current user ID
        $currentUser = $this->getCurrentUser();

        // Create university
        $universityId = $this->universityModel->create([
            'name' => $name,
            'address' => $address,
            'description' => $description,
            'image' => $imageName,
            'website' => $website,
            'phone' => $phone,
            'email' => $email,
            'created_by' => $currentUser['id']
        ]);

        if ($universityId) {
            $this->setFlash('success', 'University added successfully');
            $this->redirect('university/detail/' . $universityId);
        } else {
            $this->setFlash('error', 'Failed to add university');
            $this->redirect('university/create');
        }
    }

    /**
     * Display edit form
     */
    public function edit($id) {
        // Require login
        $this->requireLogin();

        // Get university
        $university = $this->universityModel->getById($id);

        if (!$university) {
            $this->setFlash('error', 'University not found');
            $this->redirect('university');
        }

        // Generate CSRF token
        $csrfToken = $this->generateCSRFToken();

        parent::view('universities/edit', [
            'title' => 'Edit University',
            'university' => $university,
            'csrf_token' => $csrfToken
        ]);
    }

    /**
     * Process edit form submission
     */
    public function update($id) {
        // Require login
        $this->requireLogin();

        // Cek request method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('university/edit/' . $id);
        }

        // Validasi CSRF token
        if (!$this->validateCSRFToken($_POST['csrf_token'] ?? '')) {
            $this->setFlash('error', 'Invalid CSRF token');
            $this->redirect('university/edit/' . $id);
        }

        // Get university
        $university = $this->universityModel->getById($id);

        if (!$university) {
            $this->setFlash('error', 'University not found');
            $this->redirect('university');
        }

        // Sanitize input
        $name = $this->sanitize($_POST['name'] ?? '');
        $address = $this->sanitize($_POST['address'] ?? '');
        $description = $this->sanitize($_POST['description'] ?? '');
        $website = $this->sanitize($_POST['website'] ?? '');
        $phone = $this->sanitize($_POST['phone'] ?? '');
        $email = $this->sanitize($_POST['email'] ?? '');

        // Validasi input
        $errors = [];

        if (empty($name)) {
            $errors[] = 'University name is required';
        }

        if (empty($address)) {
            $errors[] = 'Address is required';
        }

        if (!empty($email) && !$this->validateEmail($email)) {
            $errors[] = 'Invalid email format';
        }

        // Handle image upload
        $imageName = $university['image']; // Keep old image by default
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $uploadResult = $this->universityModel->uploadImage($_FILES['image'], $university['image']);
            
            if ($uploadResult['success']) {
                $imageName = $uploadResult['filename'];
            } else {
                $errors[] = $uploadResult['error'];
            }
        }

        // Jika ada error, redirect dengan error message
        if (!empty($errors)) {
            $_SESSION['university_errors'] = $errors;
            $this->redirect('university/edit/' . $id);
        }

        // Update university
        $success = $this->universityModel->update($id, [
            'name' => $name,
            'address' => $address,
            'description' => $description,
            'image' => $imageName,
            'website' => $website,
            'phone' => $phone,
            'email' => $email
        ]);

        if ($success) {
            $this->setFlash('success', 'University updated successfully');
            $this->redirect('university/detail/' . $id);
        } else {
            $this->setFlash('error', 'Failed to update university');
            $this->redirect('university/edit/' . $id);
        }
    }

    /**
     * Delete university (with confirmation)
     */
    public function delete($id) {
        // Require admin (only admin can delete)
        $this->requireAdmin();

        // Cek CSRF token dari POST request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('university');
        }

        // Validasi CSRF token
        if (!$this->validateCSRFToken($_POST['csrf_token'] ?? '')) {
            $this->setFlash('error', 'Invalid CSRF token');
            $this->redirect('university');
        }

        // Delete university
        if ($this->universityModel->delete($id)) {
            $this->setFlash('success', 'University deleted successfully');
        } else {
            $this->setFlash('error', 'Failed to delete university');
        }

        $this->redirect('university');
    }
}
