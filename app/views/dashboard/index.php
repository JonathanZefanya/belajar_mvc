<!-- Dashboard Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1 fw-bold">Dashboard</h1>
                <p class="text-muted">Welcome back, <?= $currentUser['username'] ?>!</p>
            </div>
            <div>
                <span class="badge bg-primary-subtle text-primary fs-6">
                    <i class="bi bi-shield-check me-1"></i><?= ucfirst($currentUser['role']) ?>
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <!-- Total Universities -->
    <div class="col-md-6 col-xl-3">
        <div class="stats-card stats-card-primary">
            <div class="stats-icon">
                <i class="bi bi-building"></i>
            </div>
            <div class="stats-content">
                <h3 class="stats-number"><?= $stats['total_universities'] ?></h3>
                <p class="stats-label">Total Universities</p>
            </div>
        </div>
    </div>

    <!-- Total Users -->
    <div class="col-md-6 col-xl-3">
        <div class="stats-card stats-card-success">
            <div class="stats-icon">
                <i class="bi bi-people"></i>
            </div>
            <div class="stats-content">
                <h3 class="stats-number"><?= $stats['total_users'] ?></h3>
                <p class="stats-label">Total Users</p>
            </div>
        </div>
    </div>

    <!-- Admin Count -->
    <div class="col-md-6 col-xl-3">
        <div class="stats-card stats-card-warning">
            <div class="stats-icon">
                <i class="bi bi-shield-check"></i>
            </div>
            <div class="stats-content">
                <h3 class="stats-number"><?= $stats['admin_count'] ?></h3>
                <p class="stats-label">Administrators</p>
            </div>
        </div>
    </div>

    <!-- Regular Users -->
    <div class="col-md-6 col-xl-3">
        <div class="stats-card stats-card-info">
            <div class="stats-icon">
                <i class="bi bi-person"></i>
            </div>
            <div class="stats-content">
                <h3 class="stats-number"><?= $stats['user_count'] ?></h3>
                <p class="stats-label">Regular Users</p>
            </div>
        </div>
    </div>
</div>

<!-- Recent Universities -->
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-clock-history me-2 text-primary"></i>Recent Universities
                    </h5>
                    <a href="<?= BASE_URL ?>/university" class="btn btn-sm btn-outline-primary">
                        View All <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentUniversities)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-inbox display-1 text-muted"></i>
                    <p class="text-muted mt-3">No universities yet</p>
                    <a href="<?= BASE_URL ?>/university/create" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-2"></i>Add First University
                    </a>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 80px;">Image</th>
                                <th>Name</th>
                                <th class="d-none d-md-table-cell">Location</th>
                                <th class="d-none d-lg-table-cell">Created By</th>
                                <th class="d-none d-lg-table-cell">Date</th>
                                <th style="width: 100px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentUniversities as $univ): ?>
                            <tr>
                                <td>
                                    <?php if ($univ['image']): ?>
                                        <img src="<?= UPLOAD_URL . $univ['image'] ?>" 
                                             alt="<?= htmlspecialchars($univ['name']) ?>" 
                                             class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                             style="width: 60px; height: 60px;">
                                            <i class="bi bi-building text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="fw-semibold"><?= htmlspecialchars($univ['name']) ?></div>
                                    <?php if ($univ['website']): ?>
                                    <small class="text-muted">
                                        <i class="bi bi-globe me-1"></i><?= htmlspecialchars($univ['website']) ?>
                                    </small>
                                    <?php endif; ?>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <small><?= htmlspecialchars(substr($univ['address'], 0, 50)) ?>...</small>
                                </td>
                                <td class="d-none d-lg-table-cell">
                                    <span class="badge bg-light text-dark">
                                        <?= htmlspecialchars($univ['creator_name']) ?>
                                    </span>
                                </td>
                                <td class="d-none d-lg-table-cell">
                                    <small class="text-muted">
                                        <?= date('M d, Y', strtotime($univ['created_at'])) ?>
                                    </small>
                                </td>
                                <td>
                                    <a href="<?= BASE_URL ?>/university/detail/<?= $univ['id'] ?>" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
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
