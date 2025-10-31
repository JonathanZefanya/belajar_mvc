<?php
/**
 * DashboardController
 * 
 * Controller untuk halaman dashboard utama.
 * Menampilkan statistik dan overview untuk admin dan user.
 */
class DashboardController extends Controller {
    private $userModel;
    private $universityModel;

    /**
     * Constructor - Initialize models
     */
    public function __construct() {
        $this->userModel = $this->model('User');
        $this->universityModel = $this->model('University');
    }

    /**
     * Display dashboard page
     */
    public function index() {
        // Require login
        $this->requireLogin();

        // Get current user
        $currentUser = $this->getCurrentUser();

        // Get statistics
        $stats = [
            'total_users' => $this->userModel->getTotalCount(),
            'total_universities' => $this->universityModel->getTotalCount(),
            'admin_count' => $this->userModel->getTotalCount('admin'),
            'user_count' => $this->userModel->getTotalCount('user')
        ];

        // Get recent universities (5 terbaru)
        $recentUniversities = $this->universityModel->getAll(5, 0);

        $this->view('dashboard/index', [
            'title' => 'Dashboard',
            'currentUser' => $currentUser,
            'stats' => $stats,
            'recentUniversities' => $recentUniversities
        ]);
    }
}
