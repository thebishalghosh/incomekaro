<?php view('layouts/header', ['title' => isset($user) ? 'Edit User' : 'Add User']); ?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark"><?php echo isset($user) ? 'Edit User' : 'New User'; ?></h2>
                <p class="text-muted">Create internal users like RMs, Sales Executives, etc.</p>
            </div>
            <a href="<?php echo url('user/index'); ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Back
            </a>
        </div>

        <?php flash('usr_error'); ?>

        <div class="card border-0 shadow-sm rounded-lg">
            <div class="card-body p-4 p-md-5">
                <form action="<?php echo isset($user) ? url('user/update/' . $user['id']) : url('user/store'); ?>" method="POST" enctype="multipart/form-data">

                    <h5 class="fw-bold text-primary mb-4">User Details</h5>

                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label fw-bold">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg" id="first_name" name="first_name" value="<?php echo isset($user) ? $user['first_name'] : ''; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label fw-bold">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg" id="last_name" name="last_name" value="<?php echo isset($user) ? $user['last_name'] : ''; ?>" required>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control form-control-lg" id="email" name="email" value="<?php echo isset($user) ? $user['email'] : ''; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label fw-bold">Phone <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control form-control-lg" id="phone" name="phone" value="<?php echo isset($user) ? $user['phone'] : ''; ?>" required>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label for="role_id" class="form-label fw-bold">Role <span class="text-danger">*</span></label>

                            <?php
                                // Check if this is a White Label Admin
                                $is_wl_admin = false;
                                if (isset($user)) {
                                    foreach ($roles as $role) {
                                        if ($role['id'] == $user['role_id'] && $role['code'] == 'WHITE_LABEL') {
                                            $is_wl_admin = true;
                                            break;
                                        }
                                    }
                                }
                            ?>

                            <?php if ($is_wl_admin): ?>
                                <!-- Static Display for WL Admin -->
                                <div class="form-control form-control-lg bg-light">
                                    <i class="fas fa-building me-2 text-primary"></i> White Label Administrator
                                </div>
                                <input type="hidden" name="role_id" value="<?php echo $user['role_id']; ?>">
                                <div class="form-text">Role cannot be changed for White Label Admins.</div>
                            <?php else: ?>
                                <!-- Dropdown for Regular Users -->
                                <select class="form-select form-select-lg" id="role_id" name="role_id" required>
                                    <option value="">Select Role</option>
                                    <?php foreach ($roles as $role): ?>
                                        <?php
                                            // Hide WHITE_LABEL and PARTNER_ADMIN roles from manual creation
                                            if ($role['code'] !== 'WHITE_LABEL' && $role['code'] !== 'PARTNER_ADMIN'):
                                        ?>
                                            <option value="<?php echo $role['id']; ?>" <?php echo (isset($user) && $user['role_id'] == $role['id']) ? 'selected' : ''; ?>>
                                                <?php echo $role['name']; ?>
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label fw-bold">Password <?php echo isset($user) ? '(Leave blank to keep current)' : '<span class="text-danger">*</span>'; ?></label>
                            <input type="password" class="form-control form-control-lg" id="password" name="password" <?php echo isset($user) ? '' : 'required'; ?>>
                            <?php if (!isset($user)): ?>
                                <div class="form-text text-success"><i class="fas fa-envelope me-1"></i> This password will be emailed to the user.</div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="profile_image" class="form-label fw-bold">Profile Image</label>
                        <input type="file" class="form-control" id="profile_image" name="profile_image" accept="image/*">
                        <?php if (isset($user) && $user['profile_image']): ?>
                            <div class="mt-2">
                                <img src="<?php echo asset($user['profile_image']); ?>" alt="Current Image" style="height: 60px;" class="rounded-circle">
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if (isset($user)): ?>
                    <div class="form-check form-switch mb-4">
                        <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" <?php echo ($user['status'] == 'active') ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="is_active">Is Active</label>
                    </div>
                    <?php endif; ?>

                    <hr class="my-5">

                    <!-- Added ID for anchor link -->
                    <h5 class="fw-bold text-primary mb-4" id="bank">Bank Details (Optional)</h5>

                    <?php
                        // Helper to safely get bank details
                        $bank = isset($user['bank_details']) && is_array($user['bank_details']) ? $user['bank_details'] : [];
                    ?>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Account Holder Name</label>
                            <input type="text" class="form-control" name="account_holder_name" value="<?php echo $bank['account_holder_name'] ?? ''; ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Bank Name</label>
                            <input type="text" class="form-control" name="bank_name" value="<?php echo $bank['bank_name'] ?? ''; ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Account Number</label>
                            <input type="text" class="form-control" name="account_number" value="<?php echo $bank['account_number'] ?? ''; ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">IFSC Code</label>
                            <input type="text" class="form-control" name="ifsc_code" value="<?php echo $bank['ifsc_code'] ?? ''; ?>">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Branch</label>
                        <input type="text" class="form-control" name="branch" value="<?php echo $bank['branch'] ?? ''; ?>">
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-5">
                        <a href="<?php echo url('user/index'); ?>" class="btn btn-light btn-lg px-4 me-md-2">Cancel</a>
                        <button type="submit" class="btn btn-primary btn-lg px-5"><?php echo isset($user) ? 'Update User' : 'Create User'; ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php view('layouts/footer'); ?>
