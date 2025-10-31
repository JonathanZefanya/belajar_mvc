<!-- Page Header -->
<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/university">Universities</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/university/detail/<?= $university['id'] ?>"><?= htmlspecialchars($university['name']) ?></a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
        <h1 class="h3 fw-bold">
            <i class="bi bi-pencil-square me-2 text-warning"></i>Edit University
        </h1>
    </div>
</div>

<!-- Form -->
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <!-- Errors -->
                <?php if (isset($_SESSION['university_errors'])): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-2">
                        <?php foreach ($_SESSION['university_errors'] as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['university_errors']); endif; ?>

                <form method="POST" action="<?= BASE_URL ?>/university/update/<?= $university['id'] ?>" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                    <!-- Name -->
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">
                            University Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?= htmlspecialchars($university['name']) ?>" 
                               required autofocus>
                    </div>

                    <!-- Address -->
                    <div class="mb-3">
                        <label for="address" class="form-label fw-semibold">
                            Address <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="address" name="address" 
                                  rows="3" required><?= htmlspecialchars($university['address']) ?></textarea>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold">
                            Description
                        </label>
                        <textarea class="form-control" id="description" name="description" 
                                  rows="4"><?= htmlspecialchars($university['description'] ?? '') ?></textarea>
                        <small class="text-muted">Optional: Brief description about the university</small>
                    </div>

                    <!-- Current Image -->
                    <?php if ($university['image']): ?>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Current Image</label>
                        <div>
                            <img src="<?= UPLOAD_URL . $university['image'] ?>" 
                                 alt="Current image" 
                                 class="img-thumbnail" 
                                 style="max-width: 300px;">
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Image Upload -->
                    <div class="mb-3">
                        <label for="image" class="form-label fw-semibold">
                            <?= $university['image'] ? 'Change Image' : 'Upload Image' ?>
                        </label>
                        <input type="file" class="form-control" id="image" name="image" 
                               accept=".jpg,.jpeg,.png" onchange="previewImage(event)">
                        <small class="text-muted">
                            Allowed: JPG, PNG. Max size: 2MB. 
                            <?= $university['image'] ? 'Leave empty to keep current image.' : '' ?>
                        </small>
                        
                        <!-- Image Preview -->
                        <div id="imagePreview" class="mt-3" style="display: none;">
                            <label class="form-label fw-semibold">New Image Preview</label>
                            <img id="preview" src="" alt="Preview" class="img-thumbnail" style="max-width: 300px;">
                        </div>
                    </div>

                    <!-- Website -->
                    <div class="mb-3">
                        <label for="website" class="form-label fw-semibold">
                            Website
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-globe"></i></span>
                            <input type="url" class="form-control" id="website" name="website" 
                                   value="<?= htmlspecialchars($university['website'] ?? '') ?>" 
                                   placeholder="https://example.com">
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label fw-semibold">
                                Phone Number
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                <input type="text" class="form-control" id="phone" name="phone" 
                                       value="<?= htmlspecialchars($university['phone'] ?? '') ?>" 
                                       placeholder="(021) 1234567">
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label fw-semibold">
                                Email Address
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= htmlspecialchars($university['email'] ?? '') ?>" 
                                       placeholder="info@university.ac.id">
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-check-lg me-2"></i>Update University
                        </button>
                        <a href="<?= BASE_URL ?>/university/detail/<?= $university['id'] ?>" class="btn btn-outline-secondary">
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
                    <i class="bi bi-info-circle me-2 text-info"></i>Edit Information
                </h6>
                <ul class="small mb-0">
                    <li class="mb-2">Only modified fields will be updated</li>
                    <li class="mb-2">Leave image field empty to keep current image</li>
                    <li class="mb-2">All changes are saved immediately</li>
                    <li>Make sure all information is accurate</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
// Preview image before upload
function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        // Check file size (2MB)
        if (file.size > 2097152) {
            alert('File size exceeds 2MB. Please choose a smaller file.');
            event.target.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('preview');
            const previewDiv = document.getElementById('imagePreview');
            preview.src = e.target.result;
            previewDiv.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
}
</script>
