<?php view('layouts/header', ['title' => isset($service) ? 'Edit Service' : 'Add Service']); ?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark"><?php echo isset($service) ? 'Edit Service' : 'New Service'; ?></h2>
                <p class="text-muted">Manage service details and configuration.</p>
            </div>
            <a href="<?php echo url('service/index'); ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Back
            </a>
        </div>

        <?php flash('svc_error'); ?>

        <div class="card border-0 shadow-sm rounded-lg">
            <div class="card-body p-4 p-md-5">
                <form action="<?php echo isset($service) ? url('service/update/' . $service['id']) : url('service/store'); ?>" method="POST" enctype="multipart/form-data">

                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label fw-bold">Service Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg" id="name" name="name" value="<?php echo isset($service) ? $service['name'] : ''; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="category" class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                            <select class="form-select form-select-lg" id="category" name="category" required onchange="toggleFields()">
                                <option value="">Select Category</option>
                                <?php
                                    $categories = ['LOAN', 'CREDIT', 'TAX', 'INSURANCE', 'INSTANT_PANEL', 'OTHER'];
                                    foreach ($categories as $cat) {
                                        $selected = (isset($service) && $service['category'] == $cat) ? 'selected' : '';
                                        echo "<option value='$cat' $selected>$cat</option>";
                                    }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label fw-bold">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?php echo isset($service) ? $service['description'] : ''; ?></textarea>
                    </div>

                    <div id="extra-fields">
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label for="service_type" class="form-label fw-bold">Service Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="service_type" name="service_type" onchange="toggleUrlField()">
                                    <option value="INTERNAL_FORM" <?php echo (isset($service) && $service['service_type'] == 'INTERNAL_FORM') ? 'selected' : ''; ?>>Internal Form</option>
                                    <option value="EXTERNAL_REDIRECT" <?php echo (isset($service) && $service['service_type'] == 'EXTERNAL_REDIRECT') ? 'selected' : ''; ?>>External Redirect</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3" id="url-field-container" style="display: none;">
                                <label for="url" class="form-label fw-bold">External URL (If Redirect)</label>
                                <input type="url" class="form-control" id="url" name="url" value="<?php echo isset($service) ? $service['url'] : ''; ?>">
                            </div>
                        </div>

                        <!-- Hidden Fields (Parent ID & Form Type) -->
                        <input type="hidden" name="parent_id" value="">
                        <input type="hidden" name="form_type" value="<?php echo isset($service) ? $service['form_type'] : 'NONE'; ?>">
                    </div>

                    <div class="mb-4">
                        <label for="image" class="form-label fw-bold">Service Icon/Image</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        <?php if (isset($service) && $service['image_url']): ?>
                            <div class="mt-2">
                                <img src="<?php echo asset($service['image_url']); ?>" alt="Current Image" style="height: 60px; object-fit: contain;">
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-check form-switch mb-4">
                        <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" <?php echo (!isset($service) || $service['is_active']) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="is_active">Is Active</label>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-5">
                        <a href="<?php echo url('service/index'); ?>" class="btn btn-light btn-lg px-4 me-md-2">Cancel</a>
                        <button type="submit" class="btn btn-primary btn-lg px-5"><?php echo isset($service) ? 'Update Service' : 'Create Service'; ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function toggleFields() {
    const category = document.getElementById('category').value;
    const extraFields = document.getElementById('extra-fields');

    if (category === 'INSTANT_PANEL') {
        extraFields.style.display = 'none';
    } else {
        extraFields.style.display = 'block';
        toggleUrlField(); // Re-check URL field visibility
    }
}

function toggleUrlField() {
    const serviceType = document.getElementById('service_type').value;
    const urlContainer = document.getElementById('url-field-container');

    if (serviceType === 'EXTERNAL_REDIRECT') {
        urlContainer.style.display = 'block';
    } else {
        urlContainer.style.display = 'none';
    }
}

// Run on load
document.addEventListener('DOMContentLoaded', function() {
    toggleFields();
    toggleUrlField();
});
</script>

<?php view('layouts/footer'); ?>
