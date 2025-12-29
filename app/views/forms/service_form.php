<?php view('layouts/header', ['title' => isset($service) ? 'Edit Service' : 'Add Service']); ?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark"><?php echo isset($service) ? 'Edit Service' : 'New Master Service'; ?></h2>
                <p class="text-muted">Define a service panel for subscription plans.</p>
            </div>
            <a href="<?php echo url('service/index'); ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Back
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-lg">
            <div class="card-body p-4 p-md-5">
                <form action="<?php echo isset($service) ? url('service/update/' . $service['id']) : url('service/store'); ?>" method="POST" enctype="multipart/form-data">

                    <h5 class="fw-bold text-primary mb-4">Service Details</h5>

                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label fw-bold">Title / Panel Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg" id="name" name="name" value="<?php echo isset($service) ? $service['name'] : ''; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="category" class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                            <select class="form-select form-select-lg" id="category" name="category" required>
                                <option value="LOAN" <?php echo (isset($service) && $service['category'] == 'LOAN') ? 'selected' : ''; ?>>LOAN</option>
                                <option value="CREDIT" <?php echo (isset($service) && $service['category'] == 'CREDIT') ? 'selected' : ''; ?>>CREDIT</option>
                                <option value="TAX" <?php echo (isset($service) && $service['category'] == 'TAX') ? 'selected' : ''; ?>>TAX</option>
                                <option value="INSURANCE" <?php echo (isset($service) && $service['category'] == 'INSURANCE') ? 'selected' : ''; ?>>INSURANCE</option>
                                <option value="OTHER" <?php echo (isset($service) && $service['category'] == 'OTHER') ? 'selected' : ''; ?>>OTHER</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label fw-bold">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?php echo isset($service) ? $service['description'] : ''; ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="url" class="form-label fw-bold">URL</label>
                        <input type="url" class="form-control" id="url" name="url" value="<?php echo isset($service) ? $service['url'] : ''; ?>" placeholder="https://example.com/service-details">
                    </div>

                    <div class="mb-4">
                        <label for="image" class="form-label fw-bold">Image</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        <?php if (isset($service) && $service['image_url']): ?>
                            <div class="mt-2">
                                <img src="<?php echo asset($service['image_url']); ?>" alt="Current Image" style="height: 60px;">
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-check form-switch mb-4">
                        <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" <?php echo (isset($service) && $service['is_active']) ? 'checked' : 'checked'; ?>>
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

<?php view('layouts/footer'); ?>
