<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - University Management</title>
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="<?= asset('css/style.css') ?>" rel="stylesheet">
</head>
<body class="auth-page">
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="text-center mb-4">
                <div class="auth-logo">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>
                <h2 class="fw-bold mb-2">Welcome Back</h2>
                <p class="text-muted">Sign in to your account</p>
            </div>

            <!-- Flash Messages -->
            <?php 
            $flash = null;
            if (isset($_SESSION['flash'])) {
                $flash = $_SESSION['flash'];
                unset($_SESSION['flash']);
            }
            ?>
            
            <?php if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : $flash['type'] ?> alert-dismissible fade show">
                <i class="bi bi-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-triangle' ?>-fill me-2"></i>
                <?= $flash['message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <form method="POST" action="<?= BASE_URL ?>/auth/processLogin">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                
                <div class="mb-3">
                    <label for="identifier" class="form-label">
                        <i class="bi bi-person me-1"></i>Username or Email
                    </label>
                    <input type="text" class="form-control form-control-lg" id="identifier" 
                           name="identifier" required autofocus>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">
                        <i class="bi bi-lock me-1"></i>Password
                    </label>
                    <input type="password" class="form-control form-control-lg" id="password" 
                           name="password" required>
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                </button>

                <div class="text-center">
                    <p class="text-muted mb-0">
                        Don't have an account? 
                        <a href="<?= BASE_URL ?>/auth/register" class="text-primary fw-semibold">Sign Up</a>
                    </p>
                </div>
            </form>

            <!-- Demo Credentials Info -->
            <div class="mt-4 p-3 bg-light rounded">
                <small class="d-block fw-semibold mb-2"><i class="bi bi-info-circle me-1"></i>Demo Credentials:</small>
                <small class="d-block"><strong>Admin:</strong> admin / password</small>
                <small class="d-block"><strong>User:</strong> user / password</small>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
