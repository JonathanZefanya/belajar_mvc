<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - University Management</title>
    
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
                    <i class="bi bi-person-plus-fill"></i>
                </div>
                <h2 class="fw-bold mb-2">Create Account</h2>
                <p class="text-muted">Join us today</p>
            </div>

            <!-- Errors -->
            <?php if (isset($_SESSION['register_errors'])): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($_SESSION['register_errors'] as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php unset($_SESSION['register_errors']); endif; ?>

            <form method="POST" action="<?= BASE_URL ?>/auth/processRegister">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                
                <div class="mb-3">
                    <label for="username" class="form-label">
                        <i class="bi bi-person me-1"></i>Username
                    </label>
                    <input type="text" class="form-control" id="username" name="username" 
                           value="<?= $_SESSION['register_data']['username'] ?? '' ?>" required autofocus>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">
                        <i class="bi bi-envelope me-1"></i>Email Address
                    </label>
                    <input type="email" class="form-control" id="email" name="email" 
                           value="<?= $_SESSION['register_data']['email'] ?? '' ?>" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">
                        <i class="bi bi-lock me-1"></i>Password
                    </label>
                    <input type="password" class="form-control" id="password" name="password" required>
                    <small class="text-muted">Minimum 6 characters</small>
                </div>

                <div class="mb-3">
                    <label for="confirm_password" class="form-label">
                        <i class="bi bi-lock-fill me-1"></i>Confirm Password
                    </label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                    <i class="bi bi-person-check me-2"></i>Create Account
                </button>

                <div class="text-center">
                    <p class="text-muted mb-0">
                        Already have an account? 
                        <a href="<?= BASE_URL ?>/auth/login" class="text-primary fw-semibold">Sign In</a>
                    </p>
                </div>
            </form>
        </div>
    </div>

    <?php unset($_SESSION['register_data']); ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
