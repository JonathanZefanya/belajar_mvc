<!-- Page Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1 fw-bold">
                    <i class="bi bi-building me-2 text-primary"></i>Universities
                </h1>
                <p class="text-muted">Manage university data</p>
            </div>
            <a href="<?= BASE_URL ?>/university/create" class="btn btn-primary">
                <i class="bi bi-plus-lg me-2"></i>Add University
            </a>
        </div>
    </div>
</div>

<!-- Search and Filter -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <form method="GET" action="<?= BASE_URL ?>/university" class="row g-3">
                    <div class="col-md-10">
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" class="form-control" name="search" 
                                   placeholder="Search universities by name, address, or description..." 
                                   value="<?= htmlspecialchars($search) ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Search</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Universities Grid -->
<?php if (empty($universities)): ?>
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox display-1 text-muted"></i>
                <h3 class="mt-3">No Universities Found</h3>
                <p class="text-muted">Start by adding your first university</p>
                <a href="<?= BASE_URL ?>/university/create" class="btn btn-primary mt-2">
                    <i class="bi bi-plus-lg me-2"></i>Add University
                </a>
            </div>
        </div>
    </div>
</div>
<?php else: ?>
<div class="row g-4 mb-4">
    <?php foreach ($universities as $univ): ?>
    <div class="col-md-6 col-xl-4">
        <div class="university-card">
            <!-- Image -->
            <div class="university-image">
                <?php if ($univ['image']): ?>
                    <img src="<?= UPLOAD_URL . $univ['image'] ?>" alt="<?= htmlspecialchars($univ['name']) ?>">
                <?php else: ?>
                    <div class="no-image">
                        <i class="bi bi-building"></i>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Content -->
            <div class="university-content">
                <h5 class="university-title"><?= htmlspecialchars($univ['name']) ?></h5>
                
                <div class="university-info">
                    <div class="info-item">
                        <i class="bi bi-geo-alt-fill text-primary"></i>
                        <small><?= htmlspecialchars(substr($univ['address'], 0, 60)) ?>...</small>
                    </div>
                    
                    <?php if ($univ['phone']): ?>
                    <div class="info-item">
                        <i class="bi bi-telephone-fill text-success"></i>
                        <small><?= htmlspecialchars($univ['phone']) ?></small>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($univ['website']): ?>
                    <div class="info-item">
                        <i class="bi bi-globe text-info"></i>
                        <small class="text-truncate"><?= htmlspecialchars($univ['website']) ?></small>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                    <small class="text-muted">
                        <i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($univ['creator_name']) ?>
                    </small>
                    <div class="btn-group" role="group">
                        <a href="<?= BASE_URL ?>/university/detail/<?= $univ['id'] ?>" 
                           class="btn btn-sm btn-outline-primary" title="View">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="<?= BASE_URL ?>/university/edit/<?= $univ['id'] ?>" 
                           class="btn btn-sm btn-outline-warning" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                onclick="deleteUniversity(<?= $univ['id'] ?>, '<?= htmlspecialchars($univ['name']) ?>')" 
                                title="Delete">
                            <i class="bi bi-trash"></i>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Pagination -->
<?php if ($totalPages > 1): ?>
<div class="row">
    <div class="col-12">
        <nav aria-label="University pagination">
            <ul class="pagination justify-content-center">
                <!-- Previous -->
                <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= BASE_URL ?>/university?page=<?= $currentPage - 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>

                <!-- Page Numbers -->
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php if ($i == 1 || $i == $totalPages || abs($i - $currentPage) <= 2): ?>
                        <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                            <a class="page-link" href="<?= BASE_URL ?>/university?page=<?= $i ?><?= $search ? '&search=' . urlencode($search) : '' ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php elseif (abs($i - $currentPage) == 3): ?>
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    <?php endif; ?>
                <?php endfor; ?>

                <!-- Next -->
                <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= BASE_URL ?>/university?page=<?= $currentPage + 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</div>
<?php endif; ?>
<?php endif; ?>

<!-- Delete Form (Hidden) -->
<form id="deleteForm" method="POST" style="display: none;">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
</form>

<script>
// Delete confirmation function
function deleteUniversity(id, name) {
    if (confirm(`Are you sure you want to delete "${name}"?\n\nThis action cannot be undone.`)) {
        const form = document.getElementById('deleteForm');
        form.action = '<?= BASE_URL ?>/university/delete/' + id;
        form.submit();
    }
}
</script>
