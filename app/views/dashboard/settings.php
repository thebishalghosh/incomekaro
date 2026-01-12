<?php view('layouts/header', ['title' => 'Branding & Settings']); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark">Branding & Settings</h2>
        <p class="text-muted">Customize your platform's appearance and landing page.</p>
    </div>
</div>

<?php flash('settings_success'); ?>
<?php flash('settings_error'); ?>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-header bg-white border-bottom p-0">
        <ul class="nav nav-tabs nav-fill card-header-tabs m-0" id="settingsTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active py-3 fw-bold" id="branding-tab" data-bs-toggle="tab" data-bs-target="#branding" type="button" role="tab">
                    <i class="fas fa-paint-brush me-2"></i> Branding
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link py-3 fw-bold" id="landing-tab" data-bs-toggle="tab" data-bs-target="#landing" type="button" role="tab">
                    <i class="fas fa-desktop me-2"></i> Landing Page
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link py-3 fw-bold" id="products-tab" data-bs-toggle="tab" data-bs-target="#products" type="button" role="tab">
                    <i class="fas fa-box-open me-2"></i> Products
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link py-3 fw-bold" id="agreement-tab" data-bs-toggle="tab" data-bs-target="#agreement" type="button" role="tab">
                    <i class="fas fa-file-contract me-2"></i> Agreement
                </button>
            </li>
        </ul>
    </div>

    <div class="card-body p-4">
        <form action="<?php echo url('settings/update'); ?>" method="POST" enctype="multipart/form-data">
            <div class="tab-content" id="settingsTabContent">

                <!-- Branding Tab -->
                <div class="tab-pane fade show active" id="branding" role="tabpanel">
                    <h5 class="fw-bold text-primary mb-4">General Branding</h5>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Company Name</label>
                            <input type="text" class="form-control" name="company_name" value="<?php echo $wl['company_name']; ?>" required>
                            <div class="form-text">This name will appear in the browser title and emails.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Logo</label>
                            <input type="file" class="form-control" name="logo" accept="image/*">
                            <?php if (!empty($wl['logo_url'])): ?>
                                <div class="mt-2 p-2 bg-light border rounded d-inline-block">
                                    <img src="<?php echo asset($wl['logo_url']); ?>" alt="Current Logo" style="height: 40px;">
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Primary Color</label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color" id="primaryColorPicker" value="<?php echo $wl['primary_color']; ?>" title="Choose your color">
                                <input type="text" class="form-control" name="primary_color" id="primaryColorInput" value="<?php echo $wl['primary_color']; ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Secondary Color</label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color" id="secondaryColorPicker" value="<?php echo $wl['secondary_color']; ?>" title="Choose your color">
                                <input type="text" class="form-control" name="secondary_color" id="secondaryColorInput" value="<?php echo $wl['secondary_color']; ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Landing Page Tab -->
                <div class="tab-pane fade" id="landing" role="tabpanel">
                    <h5 class="fw-bold text-primary mb-4">Hero Section</h5>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Hero Title</label>
                            <input type="text" class="form-control" name="hero_title" value="<?php echo $landing['hero']['title'] ?? ''; ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Hero Image</label>
                            <input type="file" class="form-control" name="hero_image" accept="image/*">
                            <?php if (!empty($landing['hero']['image'])): ?>
                                <div class="mt-2">
                                    <img src="<?php echo asset($landing['hero']['image']); ?>" alt="Hero" style="height: 60px;" class="rounded">
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-12 mt-3">
                            <label class="form-label fw-bold">Hero Text</label>
                            <textarea class="form-control" name="hero_text" rows="3"><?php echo $landing['hero']['text'] ?? ''; ?></textarea>
                        </div>
                    </div>

                    <hr>

                    <h5 class="fw-bold text-primary mb-4">About Section</h5>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">About Title</label>
                            <input type="text" class="form-control" name="about_title" value="<?php echo $landing['about']['title'] ?? ''; ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">About Image</label>
                            <input type="file" class="form-control" name="about_image" accept="image/*">
                            <?php if (!empty($landing['about']['image'])): ?>
                                <div class="mt-2">
                                    <img src="<?php echo asset($landing['about']['image']); ?>" alt="About" style="height: 60px;" class="rounded">
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-12 mt-3">
                            <label class="form-label fw-bold">About Text</label>
                            <textarea class="form-control" name="about_text" rows="3"><?php echo $landing['about']['text'] ?? ''; ?></textarea>
                        </div>
                    </div>

                    <hr>

                    <h5 class="fw-bold text-primary mb-4">Contact Info</h5>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Contact Phone</label>
                            <input type="text" class="form-control" name="contact_phone" value="<?php echo $landing['contact_phone'] ?? ''; ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Contact Address</label>
                            <input type="text" class="form-control" name="contact_address" value="<?php echo $landing['contact_address'] ?? ''; ?>">
                        </div>
                    </div>
                </div>

                <!-- Products Tab -->
                <div class="tab-pane fade" id="products" role="tabpanel">
                    <h5 class="fw-bold text-primary mb-4">Product Cards</h5>
                    <p class="text-muted mb-4">Customize the 6 product cards displayed on your landing page.</p>

                    <div class="row g-3">
                        <?php for($i = 0; $i < 6; $i++): ?>
                            <div class="col-md-6">
                                <div class="card bg-light border-0">
                                    <div class="card-body">
                                        <h6 class="fw-bold mb-3">Product <?php echo $i + 1; ?></h6>
                                        <div class="mb-2">
                                            <label class="form-label small fw-bold">Title</label>
                                            <input type="text" class="form-control form-control-sm" name="prod_title[]" value="<?php echo $landing['products'][$i]['title'] ?? ''; ?>">
                                        </div>
                                        <div>
                                            <label class="form-label small fw-bold">Description</label>
                                            <textarea class="form-control form-control-sm" name="prod_desc[]" rows="2"><?php echo $landing['products'][$i]['desc'] ?? ''; ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <!-- Agreement Tab -->
                <div class="tab-pane fade" id="agreement" role="tabpanel">
                    <h5 class="fw-bold text-primary mb-4">Agreement Settings</h5>
                    <p class="text-muted mb-4">Configure the authorized signatory details for the partner agreement.</p>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Signatory Name</label>
                            <input type="text" class="form-control" name="signatory_name" value="<?php echo $wl['signatory_name'] ?? ''; ?>" placeholder="e.g. John Doe">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Designation</label>
                            <input type="text" class="form-control" name="signatory_designation" value="<?php echo $wl['signatory_designation'] ?? ''; ?>" placeholder="e.g. CEO">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Digital Signature</label>
                        <input type="file" class="form-control" name="signature" accept="image/*">
                        <div class="form-text">Upload a PNG/JPG image of the signature (transparent background recommended).</div>

                        <?php if (!empty($wl['signature_url'])): ?>
                            <div class="mt-3 p-3 bg-light border rounded d-inline-block">
                                <img src="<?php echo asset($wl['signature_url']); ?>" alt="Current Signature" style="height: 60px;">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-5 pt-3 border-top">
                <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Sync Color Pickers
    document.getElementById('primaryColorPicker').addEventListener('input', function() {
        document.getElementById('primaryColorInput').value = this.value;
    });
    document.getElementById('primaryColorInput').addEventListener('input', function() {
        document.getElementById('primaryColorPicker').value = this.value;
    });

    document.getElementById('secondaryColorPicker').addEventListener('input', function() {
        document.getElementById('secondaryColorInput').value = this.value;
    });
    document.getElementById('secondaryColorInput').addEventListener('input', function() {
        document.getElementById('secondaryColorPicker').value = this.value;
    });
</script>

<?php view('layouts/footer'); ?>
