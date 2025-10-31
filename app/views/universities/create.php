<!-- Page Header -->
<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/university">Universities</a></li>
                <li class="breadcrumb-item active">Add New University</li>
            </ol>
        </nav>
        <h1 class="h3 fw-bold">
            <i class="bi bi-plus-circle me-2 text-primary"></i>Add New University
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

                <form method="POST" action="<?= BASE_URL ?>/university/store" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                    <!-- Name -->
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">
                            University Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?= $_SESSION['university_data']['name'] ?? '' ?>" 
                               required autofocus>
                    </div>

                    <!-- Address -->
                    <div class="mb-3">
                        <label for="address" class="form-label fw-semibold">
                            Address <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="address" name="address" 
                                  rows="3" required><?= $_SESSION['university_data']['address'] ?? '' ?></textarea>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold">
                            Description
                        </label>
                        <textarea class="form-control" id="description" name="description" 
                                  rows="4"><?= $_SESSION['university_data']['description'] ?? '' ?></textarea>
                        <small class="text-muted">Optional: Brief description about the university</small>
                    </div>

                    <!-- Image Upload -->
                    <div class="mb-3">
                        <label for="image" class="form-label fw-semibold">
                            University Image
                        </label>
                        <input type="file" class="form-control" id="image" name="image" 
                               accept=".jpg,.jpeg,.png" onchange="previewImage(event)">
                        <small class="text-muted">Allowed: JPG, PNG. Max size: 2MB</small>
                        
                        <!-- Image Preview -->
                        <div id="imagePreview" class="mt-3" style="display: none;">
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
                                   value="<?= $_SESSION['university_data']['website'] ?? '' ?>" 
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
                                       value="<?= $_SESSION['university_data']['phone'] ?? '' ?>" 
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
                                       value="<?= $_SESSION['university_data']['email'] ?? '' ?>" 
                                       placeholder="info@university.ac.id">
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-2"></i>Save University
                        </button>
                        <a href="<?= BASE_URL ?>/university" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg me-2"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Help Sidebar -->
    <div class="col-lg-4">
        <div class="card shadow-sm bg-light">
            <div class="card-body">
                <h6 class="fw-semibold mb-3">
                    <i class="bi bi-lightbulb me-2 text-warning"></i>Tips
                </h6>
                <ul class="small mb-0">
                    <li class="mb-2">Fields marked with <span class="text-danger">*</span> are required</li>
                    <li class="mb-2">Image should be clear and representative of the university</li>
                    <li class="mb-2">Use high-quality images for better presentation</li>
                    <li class="mb-2">Provide accurate contact information</li>
                    <li>Double-check all information before saving</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php unset($_SESSION['university_data']); ?>

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
