<!-- Page Header -->
<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/university">Universities</a></li>
                <li class="breadcrumb-item active"><?= htmlspecialchars($university['name']) ?></li>
            </ol>
        </nav>
    </div>
</div>

<!-- University Detail -->
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <!-- Image -->
            <?php if ($university['image']): ?>
            <div class="university-detail-image">
                <img src="<?= UPLOAD_URL . $university['image'] ?>" 
                     alt="<?= htmlspecialchars($university['name']) ?>" 
                     class="w-100">
            </div>
            <?php endif; ?>

            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h1 class="h3 fw-bold mb-0"><?= htmlspecialchars($university['name']) ?></h1>
                    <div class="btn-group">
                        <a href="<?= BASE_URL ?>/university/edit/<?= $university['id'] ?>" 
                           class="btn btn-warning">
                            <i class="bi bi-pencil me-2"></i>Edit
                        </a>
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <button type="button" class="btn btn-danger" 
                                onclick="deleteUniversity(<?= $university['id'] ?>, '<?= htmlspecialchars($university['name']) ?>')">
                            <i class="bi bi-trash me-2"></i>Delete
                        </button>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($university['description']): ?>
                <div class="mb-4">
                    <h5 class="fw-semibold mb-3">
                        <i class="bi bi-file-text me-2 text-primary"></i>Description
                    </h5>
                    <p class="text-muted"><?= nl2br(htmlspecialchars($university['description'])) ?></p>
                </div>
                <?php endif; ?>

                <div class="row g-3">
                    <div class="col-md-12">
                        <div class="detail-item">
                            <i class="bi bi-geo-alt-fill text-danger me-2"></i>
                            <div>
                                <small class="text-muted d-block">Address</small>
                                <span><?= htmlspecialchars($university['address']) ?></span>
                            </div>
                        </div>
                    </div>

                    <?php if ($university['phone']): ?>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <i class="bi bi-telephone-fill text-success me-2"></i>
                            <div>
                                <small class="text-muted d-block">Phone</small>
                                <a href="tel:<?= htmlspecialchars($university['phone']) ?>">
                                    <?= htmlspecialchars($university['phone']) ?>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($university['email']): ?>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <i class="bi bi-envelope-fill text-primary me-2"></i>
                            <div>
                                <small class="text-muted d-block">Email</small>
                                <a href="mailto:<?= htmlspecialchars($university['email']) ?>">
                                    <?= htmlspecialchars($university['email']) ?>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($university['website']): ?>
                    <div class="col-md-12">
                        <div class="detail-item">
                            <i class="bi bi-globe text-info me-2"></i>
                            <div>
                                <small class="text-muted d-block">Website</small>
                                <a href="<?= htmlspecialchars($university['website']) ?>" target="_blank" rel="noopener">
                                    <?= htmlspecialchars($university['website']) ?>
                                    <i class="bi bi-box-arrow-up-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Meta Information -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h6 class="mb-0 fw-semibold">
                    <i class="bi bi-info-circle me-2"></i>Information
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block mb-1">Created By</small>
                    <span class="badge bg-primary-subtle text-primary">
                        <i class="bi bi-person-circle me-1"></i>
                        <?= htmlspecialchars($university['creator_name']) ?>
                    </span>
                </div>
                
                <div class="mb-3">
                    <small class="text-muted d-block mb-1">Created Date</small>
                    <span>
                        <i class="bi bi-calendar-check me-1"></i>
                        <?= date('F d, Y', strtotime($university['created_at'])) ?>
                    </span>
                </div>

                <div>
                    <small class="text-muted d-block mb-1">Last Updated</small>
                    <span>
                        <i class="bi bi-clock-history me-1"></i>
                        <?= date('F d, Y H:i', strtotime($university['updated_at'])) ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0 fw-semibold">
                    <i class="bi bi-lightning me-2"></i>Quick Actions
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= BASE_URL ?>/university" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left me-2"></i>Back to List
                    </a>
                    <a href="<?= BASE_URL ?>/university/edit/<?= $university['id'] ?>" class="btn btn-outline-warning">
                        <i class="bi bi-pencil me-2"></i>Edit University
                    </a>
                    <a href="<?= BASE_URL ?>/university/create" class="btn btn-outline-success">
                        <i class="bi bi-plus-lg me-2"></i>Add New University
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Form (Hidden) -->
<form id="deleteForm" method="POST" style="display: none;">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
</form>

<script>
function deleteUniversity(id, name) {
    if (confirm(`Are you sure you want to delete "${name}"?\n\nThis action cannot be undone.`)) {
        const form = document.getElementById('deleteForm');
        form.action = '<?= BASE_URL ?>/university/delete/' + id;
        form.submit();
    }
}
</script>
