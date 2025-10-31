<!-- Page Header -->
<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/user">Users</a></li>
                <li class="breadcrumb-item active">Edit User</li>
            </ol>
        </nav>
        <h1 class="h3 fw-bold">
            <i class="bi bi-pencil-square me-2 text-warning"></i>Edit User
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

                <form method="POST" action="<?= BASE_URL ?>/user/update/<?= $user['id'] ?>">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                    <!-- Username -->
                    <div class="mb-3">
                        <label for="username" class="form-label fw-semibold">
                            Username <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" class="form-control" id="username" name="username" 
                                   value="<?= htmlspecialchars($user['username']) ?>" 
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
                                   value="<?= htmlspecialchars($user['email']) ?>" 
                                   required>
                        </div>
                    </div>

                    <!-- Role -->
                    <div class="mb-3">
                        <label for="role" class="form-label fw-semibold">
                            User Role <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>
                                User - Regular user with basic permissions
                            </option>
                            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>
                                Admin - Full access to all features
                            </option>
                        </select>
                    </div>

                    <hr class="my-4">

                    <!-- Password (Optional) -->
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Change Password (Optional)</strong>
                        <p class="mb-0 small mt-1">Leave password fields empty to keep the current password</p>
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">
                            New Password
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                <i class="bi bi-eye" id="password-icon"></i>
                            </button>
                        </div>
                        <small class="text-muted">Minimum 6 characters (if changing)</small>
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label fw-semibold">
                            Confirm New Password
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('confirm_password')">
                                <i class="bi bi-eye" id="confirm_password-icon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-check-lg me-2"></i>Update User
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
                    <i class="bi bi-shield-check me-2 text-success"></i>User Information
                </h6>
                <div class="mb-2">
                    <small class="text-muted d-block">User ID</small>
                    <span><?= $user['id'] ?></span>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">Created Date</small>
                    <span><?= date('F d, Y', strtotime($user['created_at'])) ?></span>
                </div>
                <div>
                    <small class="text-muted d-block">Last Updated</small>
                    <span><?= date('F d, Y H:i', strtotime($user['updated_at'])) ?></span>
                </div>
            </div>
        </div>

        <div class="card shadow-sm bg-light mt-3">
            <div class="card-body">
                <h6 class="fw-semibold mb-3">
                    <i class="bi bi-lightbulb me-2 text-warning"></i>Tips
                </h6>
                <ul class="small mb-0">
                    <li class="mb-2">Usernames must be unique</li>
                    <li class="mb-2">Email addresses must be valid and unique</li>
                    <li class="mb-2">Leave password empty to keep current password</li>
                    <li>Cannot delete the last admin user</li>
                </ul>
            </div>
        </div>
    </div>
</div>

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
