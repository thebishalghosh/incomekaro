<?php view('layouts/header', ['title' => isset($user) ? 'Edit User' : 'Add User']); ?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark"><?php echo isset($user) ? 'Edit User' : 'New User'; ?></h2>
                <p class="text-muted">Create and manage system users (RMs, Sales Executives, etc.).</p>
            </div>
            <a href="<?php echo url('user/index'); ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Back
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-lg">
            <div class="card-body p-4 p-md-5">
                <form action="<?php echo isset($user) ? url('user/update/' . $user['id']) : url('user/store'); ?>" method="POST" enctype="multipart/form-data">

                    <h5 class="fw-bold text-primary mb-4">Personal Information</h5>

                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label for="profile_image" class="form-label fw-bold">Profile Image</label>
                            <input type="file" class="form-control" id="profile_image" name="profile_image" accept="image/*" onchange="previewImage(this)">
                            <div class="mt-3" id="image_preview_container" style="<?php echo (isset($user) && !empty($user['profile_image'])) ? '' : 'display:none;'; ?>">
                                <img id="image_preview" src="<?php echo (isset($user) && !empty($user['profile_image'])) ? asset($user['profile_image']) : '#'; ?>" alt="Profile Preview" style="height: 100px; width: 100px; object-fit: cover; border-radius: 50%; border: 2px solid #dee2e6;">
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="first_name" class="form-label fw-bold">First Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo isset($user) ? $user['first_name'] : ''; ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="last_name" class="form-label fw-bold">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo isset($user) ? $user['last_name'] : ''; ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label fw-bold">Phone Number <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo isset($user) ? $user['phone'] : ''; ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label fw-bold">E-mail Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($user) ? $user['email'] : ''; ?>" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4 text-muted">

                    <h5 class="fw-bold text-primary mb-4">Bank Details</h5>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="account_holder_name" class="form-label fw-bold">Account Holder Name</label>
                            <input type="text" class="form-control" id="account_holder_name" name="account_holder_name" value="<?php echo !empty($user['bank_details']) ? $user['bank_details']['account_holder_name'] : ''; ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="bank_name" class="form-label fw-bold">Bank Name</label>
                            <input type="text" class="form-control" id="bank_name" name="bank_name" value="<?php echo !empty($user['bank_details']) ? $user['bank_details']['bank_name'] : ''; ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="account_number" class="form-label fw-bold">Account Number</label>
                            <input type="text" class="form-control" id="account_number" name="account_number" value="<?php echo !empty($user['bank_details']) ? $user['bank_details']['account_number'] : ''; ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="ifsc_code" class="form-label fw-bold">IFSC Code</label>
                            <input type="text" class="form-control" id="ifsc_code" name="ifsc_code" value="<?php echo !empty($user['bank_details']) ? $user['bank_details']['ifsc_code'] : ''; ?>">
                        </div>
                    </div>

                    <hr class="my-4 text-muted">

                    <h5 class="fw-bold text-primary mb-4">Role & Status</h5>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="role_id" class="form-label fw-bold">Select Role <span class="text-danger">*</span></label>
                            <select class="form-select" id="role_id" name="role_id" required>
                                <option value="">-- Select Role --</option>
                                <?php foreach ($roles as $role): ?>
                                    <?php if ($role['code'] !== 'PARTNER_ADMIN'): // Cannot manually create partner admins ?>
                                        <option value="<?php echo $role['id']; ?>" <?php echo (isset($user) && $user['role_id'] == $role['id']) ? 'selected' : ''; ?>>
                                            <?php echo $role['name']; ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php if (isset($user)): ?>
                        <div class="col-md-6">
                            <label for="status" class="form-label fw-bold">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="active" <?php echo ($user['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo ($user['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>
                        <?php endif; ?>
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

<script>
function previewImage(input) {
    const preview = document.getElementById('image_preview');
    const container = document.getElementById('image_preview_container');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            container.style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php view('layouts/footer'); ?>
