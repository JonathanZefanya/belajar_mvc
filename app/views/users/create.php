<!-- Page Header -->
<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/user">Users</a></li>
                <li class="breadcrumb-item active">Add New User</li>
            </ol>
        </nav>
        <h1 class="h3 fw-bold">
            <i class="bi bi-person-plus me-2 text-primary"></i>Add New User
        </h1>
    </div>
</div>

<!-- Form -->
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <!-- Errors -->
                <?php if (isset($_SESSION['user_errors'])): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-2">
                        <?php foreach ($_SESSION['user_errors'] as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['user_errors']); endif; ?>

                <form method="POST" action="<?= BASE_URL ?>/user/store">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                    <!-- Username -->
                    <div class="mb-3">
                        <label for="username" class="form-label fw-semibold">
                            Username <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" class="form-control" id="username" name="username" 
                                   value="<?= $_SESSION['user_data']['username'] ?? '' ?>" 
                                   required autofocus>
                        </div>
                        <small class="text-muted">Minimum 3 characters, unique</small>
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">
                            Email Address <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?= $_SESSION['user_data']['email'] ?? '' ?>" 
                                   required>
                        </div>
                    </div>

                    <!-- Role -->
                    <div class="mb-3">
                        <label for="role" class="form-label fw-semibold">
                            User Role <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="user" <?= (isset($_SESSION['user_data']['role']) && $_SESSION['user_data']['role'] === 'user') ? 'selected' : '' ?>>
                                User - Regular user with basic permissions
                            </option>
                            <option value="admin" <?= (isset($_SESSION['user_data']['role']) && $_SESSION['user_data']['role'] === 'admin') ? 'selected' : '' ?>>
                                Admin - Full access to all features
                            </option>
                        </select>
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">
                            Password <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                <i class="bi bi-eye" id="password-icon"></i>
                            </button>
                        </div>
                        <small class="text-muted">Minimum 6 characters</small>
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label fw-semibold">
                            Confirm Password <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('confirm_password')">
                                <i class="bi bi-eye" id="confirm_password-icon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-2"></i>Create User
                        </button>
                        <a href="<?= BASE_URL ?>/user" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg me-2"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Info Sidebar -->
    <div class="col-lg-4">
        <div class="card shadow-sm bg-light">
            <div class="card-body">
                <h6 class="fw-semibold mb-3">
                    <i class="bi bi-info-circle me-2 text-info"></i>User Roles
                </h6>
                <div class="mb-3">
                    <strong>Admin:</strong>
                    <ul class="small mb-0 mt-1">
                        <li>Full access to all features</li>
                        <li>Can manage users</li>
                        <li>Can delete universities</li>
                    </ul>
                </div>
                <div>
                    <strong>User:</strong>
                    <ul class="small mb-0 mt-1">
                        <li>Can view universities</li>
                        <li>Can create universities</li>
                        <li>Can edit universities</li>
                        <li>Cannot manage users</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php unset($_SESSION['user_data']); ?>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '-icon');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}
</script>
