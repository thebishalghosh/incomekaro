<?php view('layouts/header', ['title' => isset($plan) ? 'Edit Plan' : 'Add Plan']); ?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark"><?php echo isset($plan) ? 'Edit Plan' : 'New Subscription Plan'; ?></h2>
                <p class="text-muted">Define pricing and features for partners.</p>
            </div>
            <a href="<?php echo url('subscription/index'); ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Back
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-lg">
            <div class="card-body p-4 p-md-5">
                <form action="<?php echo isset($plan) ? url('subscription/update/' . $plan['id']) : url('subscription/store'); ?>" method="POST">

                    <h5 class="fw-bold text-primary mb-4">Plan Details</h5>

                    <div class="row mb-4">
                        <div class="col-md-12 mb-3">
                            <label for="name" class="form-label fw-bold">Plan Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg" id="name" name="name" value="<?php echo isset($plan) ? $plan['name'] : ''; ?>" placeholder="e.g. Gold Plan" required>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label fw-bold">Base Price (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control form-control-lg" id="price" name="price" value="<?php echo isset($plan) ? $plan['price'] : ''; ?>" placeholder="0.00" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="gst_rate" class="form-label fw-bold">GST Rate (%)</label>
                            <input type="number" step="0.01" class="form-control form-control-lg" id="gst_rate" name="gst_rate" value="<?php echo isset($plan) ? $plan['gst_rate'] : '18.00'; ?>">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label fw-bold">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Plan features..."><?php echo isset($plan) ? $plan['description'] : ''; ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="footer_description" class="form-label fw-bold">Footer Description</label>
                        <textarea class="form-control" id="footer_description" name="footer_description" rows="2" placeholder="Small text at the bottom..."><?php echo isset($plan) ? $plan['footer_description'] : ''; ?></textarea>
                    </div>

                    <hr class="my-5 text-muted">

                    <h5 class="fw-bold text-primary mb-4">Included Services</h5>

                    <div class="row">
                        <?php if (!empty($services)): ?>
                            <?php foreach ($services as $svc): ?>
                                <div class="col-md-4 mb-3">
                                    <div class="form-check p-3 border rounded bg-light">
                                        <input class="form-check-input" type="checkbox" name="services[]" value="<?php echo $svc['id']; ?>" id="svc_<?php echo $svc['id']; ?>"
                                            <?php echo (isset($plan) && in_array($svc['id'], $plan['services'])) ? 'checked' : ''; ?>>
                                        <label class="form-check-label fw-bold" for="svc_<?php echo $svc['id']; ?>">
                                            <?php echo $svc['name']; ?>
                                        </label>
                                        <div class="small text-muted"><?php echo $svc['category']; ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">No services found. Please add services first.</p>
                        <?php endif; ?>
                    </div>

                    <hr class="my-5 text-muted">

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="status" class="form-label fw-bold">Status</label>
                            <select class="form-select form-select-lg" id="status" name="status">
                                <option value="active" <?php echo (isset($plan) && $plan['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo (isset($plan) && $plan['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>
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
