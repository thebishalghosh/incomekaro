<?php view('layouts/header', ['title' => isset($wl) ? 'Edit White Label' : 'Add White Label']); ?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark"><?php echo isset($wl) ? 'Edit White Label' : 'New White Label'; ?></h2>
                <p class="text-muted">Configure branding and settings for the client.</p>
            </div>
            <a href="<?php echo url('white_label/index'); ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Back
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-lg">
            <div class="card-body p-4 p-md-5">
                <form action="<?php echo isset($wl) ? url('white_label/update/' . $wl['id']) : url('white_label/store'); ?>" method="POST" enctype="multipart/form-data">

                    <h5 class="fw-bold text-primary mb-4">Basic Information</h5>

                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label for="company_name" class="form-label fw-bold">Company Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg" id="company_name" name="company_name" value="<?php echo isset($wl) ? $wl['company_name'] : ''; ?>" placeholder="e.g. FinTech Solutions Pvt Ltd" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="support_email" class="form-label fw-bold">Support Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control form-control-lg" id="support_email" name="support_email" value="<?php echo isset($wl) ? $wl['support_email'] : ''; ?>" placeholder="support@example.com" required>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <label for="primary_domain" class="form-label fw-bold">Primary Domain <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">https://</span>
                                <input type="text" class="form-control form-control-lg border-start-0 ps-0" id="primary_domain" name="primary_domain" placeholder="example.com" value="<?php echo isset($wl) ? $wl['primary_domain'] : ''; ?>" required>
                            </div>
                            <div class="form-text">The domain where this white label will be hosted.</div>
                        </div>
                    </div>

                    <hr class="my-5 text-muted">

                    <h5 class="fw-bold text-primary mb-4">Branding & Appearance</h5>

                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label for="primary_color" class="form-label fw-bold">Primary Color</label>
                            <div class="d-flex align-items-center p-2 border rounded bg-light">
                                <input type="color" class="form-control form-control-color me-3 border-0 bg-transparent" id="primary_color" name="primary_color" value="<?php echo isset($wl) ? $wl['primary_color'] : '#0d6efd'; ?>" title="Choose your color">
                                <div class="flex-grow-1">
                                    <div class="small fw-bold">Main Theme Color</div>
                                    <div class="small text-muted">Used for buttons, headers, and accents.</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="secondary_color" class="form-label fw-bold">Secondary Color</label>
                            <div class="d-flex align-items-center p-2 border rounded bg-light">
                                <input type="color" class="form-control form-control-color me-3 border-0 bg-transparent" id="secondary_color" name="secondary_color" value="<?php echo isset($wl) ? $wl['secondary_color'] : '#6c757d'; ?>" title="Choose your color">
                                <div class="flex-grow-1">
                                    <div class="small fw-bold">Accent Color</div>
                                    <div class="small text-muted">Used for secondary actions and borders.</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <label for="logo" class="form-label fw-bold">Company Logo</label>
                            <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                            <div class="form-text">Recommended size: 200x50px. Max size: 2MB.</div>
                            <?php if (isset($wl) && $wl['logo_url']): ?>
                                <div class="mt-3 p-3 bg-light rounded border d-inline-block">
                                    <img src="<?php echo asset($wl['logo_url']); ?>" alt="Current Logo" style="height: 40px;">
                                    <div class="small text-muted mt-1">Current Logo</div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <hr class="my-5 text-muted">

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="status" class="form-label fw-bold">Account Status</label>
                            <select class="form-select form-select-lg" id="status" name="status">
                                <option value="active" <?php echo (isset($wl) && $wl['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo (isset($wl) && $wl['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                            <div class="form-text">Inactive clients cannot log in or access their dashboard.</div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-5">
                        <a href="<?php echo url('white_label/index'); ?>" class="btn btn-light btn-lg px-4 me-md-2">Cancel</a>
                        <button type="submit" class="btn btn-primary btn-lg px-5"><?php echo isset($wl) ? 'Update Client' : 'Create Client'; ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php view('layouts/footer'); ?>
