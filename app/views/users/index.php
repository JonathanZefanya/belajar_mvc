<!-- Page Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1 fw-bold">
                    <i class="bi bi-people me-2 text-primary"></i>User Management
                </h1>
                <p class="text-muted">Manage system users and their roles</p>
            </div>
            <a href="<?= BASE_URL ?>/user/create" class="btn btn-primary">
                <i class="bi bi-person-plus me-2"></i>Add User
            </a>
        </div>
    </div>
</div>

<!-- Users Table -->
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <?php if (empty($users)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-person-x display-1 text-muted"></i>
                    <h3 class="mt-3">No Users Found</h3>
                    <p class="text-muted">Start by adding your first user</p>
                    <a href="<?= BASE_URL ?>/user/create" class="btn btn-primary mt-2">
                        <i class="bi bi-person-plus me-2"></i>Add User
                    </a>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th style="width: 120px;">Role</th>
                                <th class="d-none d-lg-table-cell">Created Date</th>
                                <th style="width: 150px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $index => $user): ?>
                            <tr>
                                <td class="text-muted"><?= $index + 1 ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle avatar-sm me-2">
                                            <?= strtoupper(substr($user['username'], 0, 1)) ?>
                                        </div>
                                        <span class="fw-semibold"><?= htmlspecialchars($user['username']) ?></span>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td>
                                    <?php if ($user['role'] === 'admin'): ?>
                                        <span class="badge bg-danger">
                                            <i class="bi bi-shield-fill-check me-1"></i>Admin
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">
                                            <i class="bi bi-person me-1"></i>User
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="d-none d-lg-table-cell text-muted">
                                    <small><?= date('M d, Y', strtotime($user['created_at'])) ?></small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?= BASE_URL ?>/user/edit/<?= $user['id'] ?>" 
                                           class="btn btn-sm btn-warning" 
                                           title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                        <button type="button" 
                                                class="btn btn-sm btn-danger" 
                                                onclick="deleteUser(<?= $user['id'] ?>, '<?= htmlspecialchars($user['username']) ?>')" 
                                                title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Delete Form (Hidden) -->
<form id="deleteForm" method="POST" style="display: none;">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
</form>

<script>
function deleteUser(id, username) {
    if (confirm(`Are you sure you want to delete user "${username}"?\n\nThis action cannot be undone.`)) {
        const form = document.getElementById('deleteForm');
        form.action = '<?= BASE_URL ?>/user/delete/' + id;
        form.submit();
    }
}
</script>
