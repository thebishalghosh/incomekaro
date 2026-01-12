<?php view('layouts/header', ['title' => isset($plan) ? 'Edit Plan' : 'Create Plan']); ?>

<div class="row justify-content-center">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-dark"><?php echo isset($plan) ? 'Edit Plan' : 'Create New Plan'; ?></h2>
            <a href="<?php echo url('subscription/index'); ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Back
            </a>
        </div>

        <?php flash('sub_error'); ?>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-5">
                <form action="<?php echo isset($plan) ? url('subscription/update/' . $plan['id']) : url('subscription/store'); ?>" method="POST">

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Plan Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg" name="name" value="<?php echo isset($plan) ? $plan['name'] : ''; ?>" required>
                        </div>

                        <?php if ($_SESSION['role_code'] === 'SUPER_ADMIN'): ?>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Plan Type <span class="text-danger">*</span></label>
                                <select class="form-select form-select-lg" name="type" required style="position: relative; z-index: 10;">
                                    <option value="PARTNER" <?php echo (isset($plan) && $plan['type'] == 'PARTNER') ? 'selected' : ''; ?>>Partner Plan</option>
                                    <option value="WHITE_LABEL" <?php echo (isset($plan) && $plan['type'] == 'WHITE_LABEL') ? 'selected' : ''; ?>>White Label Plan</option>
                                </select>
                                <div class="form-text">Who is this plan for?</div>
                            </div>
                        <?php else: ?>
                            <!-- Hidden for WL Admin, defaults to PARTNER in controller -->
                            <input type="hidden" name="type" value="PARTNER">
                        <?php endif; ?>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Price (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control form-control-lg" name="price" value="<?php echo isset($plan) ? $plan['price'] : ''; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">GST Rate (%)</label>
                            <input type="number" step="0.01" class="form-control form-control-lg" name="gst_rate" value="<?php echo isset($plan) ? $plan['gst_rate'] : '18.00'; ?>">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Description</label>
                        <textarea class="form-control" name="description" rows="3"><?php echo isset($plan) ? $plan['description'] : ''; ?></textarea>
                        <div class="form-text">Features list (one per line recommended).</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Footer Note</label>
                        <input type="text" class="form-control" name="footer_description" value="<?php echo isset($plan) ? $plan['footer_description'] : ''; ?>">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold d-block mb-3">Included Services</label>
                        <div class="row g-3">
                            <?php foreach ($services as $svc): ?>
                                <div class="col-md-4 col-sm-6">
                                    <!-- Added position-relative to contain stretched-link -->
                                    <div class="form-check p-3 border rounded bg-light position-relative h-100">
                                        <input class="form-check-input" type="checkbox" name="services[]" value="<?php echo $svc['id']; ?>" id="svc_<?php echo $svc['id']; ?>"
                                            <?php echo (isset($plan) && in_array($svc['id'], $plan['services'])) ? 'checked' : ''; ?>>
                                        <label class="form-check-label w-100 stretched-link fw-bold" for="svc_<?php echo $svc['id']; ?>">
                                            <?php echo $svc['name']; ?>
                                            <div class="mt-1"><span class="badge bg-secondary"><?php echo $svc['category']; ?></span></div>
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Status</label>
                        <select class="form-select" name="status">
                            <option value="active" <?php echo (isset($plan) && $plan['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo (isset($plan) && $plan['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-5">
                        <a href="<?php echo url('subscription/index'); ?>" class="btn btn-light btn-lg px-4 me-md-2">Cancel</a>
                        <button type="submit" class="btn btn-primary btn-lg px-5"><?php echo isset($plan) ? 'Update Plan' : 'Create Plan'; ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php view('layouts/footer'); ?>
