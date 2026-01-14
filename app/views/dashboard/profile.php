<?php view('layouts/header', ['title' => 'My Profile']); ?>

<div class="row justify-content-center">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-dark">My Profile</h2>
        </div>

        <?php flash('profile_success'); ?>
        <?php flash('profile_error'); ?>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-5">
                <form action="<?php echo url('profile/update'); ?>" method="POST" enctype="multipart/form-data">

                    <div class="text-center mb-5">
                        <div class="position-relative d-inline-block">
                            <?php if (!empty($user['profile_image'])): ?>
                                <img src="<?php echo asset($user['profile_image']); ?>" class="rounded-circle shadow-sm" style="width: 120px; height: 120px; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-light text-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm mx-auto" style="width: 120px; height: 120px; font-size: 3rem;">
                                    <?php echo strtoupper(substr($user['first_name'], 0, 1)); ?>
                                </div>
                            <?php endif; ?>
                            <label for="profile_image" class="position-absolute bottom-0 end-0 bg-white rounded-circle shadow-sm p-2 cursor-pointer border" style="cursor: pointer;">
                                <i class="fas fa-camera text-primary"></i>
                                <input type="file" id="profile_image" name="profile_image" class="d-none" accept="image/*">
                            </label>
                        </div>
                        <h4 class="fw-bold mt-3 mb-1"><?php echo $user['first_name'] . ' ' . $user['last_name']; ?></h4>
                        <p class="text-muted mb-0"><?php echo $user['email']; ?></p>
                    </div>

                    <h5 class="fw-bold text-primary mb-4">Personal Details</h5>
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">First Name</label>
                            <input type="text" class="form-control form-control-lg" name="first_name" value="<?php echo $user['first_name']; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Last Name</label>
                            <input type="text" class="form-control form-control-lg" name="last_name" value="<?php echo $user['last_name']; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Phone</label>
                            <input type="tel" class="form-control form-control-lg" name="phone" value="<?php echo $user['phone']; ?>" required>
                        </div>
                    </div>

                    <hr class="my-5">

                    <h5 class="fw-bold text-primary mb-4">Bank Details</h5>
                    <div class="alert alert-light border mb-4">
                        <i class="fas fa-info-circle me-2 text-primary"></i> These details are required for withdrawals.
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Account Holder Name</label>
                            <input type="text" class="form-control form-control-lg" name="account_holder_name" value="<?php echo $user['bank_details']['account_holder_name'] ?? ''; ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Bank Name</label>
                            <input type="text" class="form-control form-control-lg" name="bank_name" value="<?php echo $user['bank_details']['bank_name'] ?? ''; ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Account Number</label>
                            <input type="text" class="form-control form-control-lg" name="account_number" value="<?php echo $user['bank_details']['account_number'] ?? ''; ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">IFSC Code</label>
                            <input type="text" class="form-control form-control-lg" name="ifsc_code" value="<?php echo $user['bank_details']['ifsc_code'] ?? ''; ?>">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">Branch</label>
                        <input type="text" class="form-control form-control-lg" name="branch" value="<?php echo $user['bank_details']['branch'] ?? ''; ?>">
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-5">
                        <button type="submit" class="btn btn-primary btn-lg px-5">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php view('layouts/footer'); ?>
