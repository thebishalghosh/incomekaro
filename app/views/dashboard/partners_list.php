<?php view('layouts/header', ['title' => 'Partners']); ?>

<div class="row mb-4">
    <div class="col-md-6">
        <h1>All Partners</h1>
    </div>
    <div class="col-md-6 text-end">
        <a href="<?php echo url('partner/create'); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Partner
        </a>
        <a href="<?php echo url('dashboard/super_admin'); ?>" class="btn btn-secondary">Back to Dashboard</a>
    </div>
</div>

<?php flash('ptr_success'); ?>
<?php flash('ptr_error'); ?>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Contact</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($partners)): ?>
                        <?php foreach ($partners as $ptr): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if (!empty($ptr['profile_image'])): ?>
                                            <img src="<?php echo asset($ptr['profile_image']); ?>" alt="Avatar" style="width: 45px; height: 45px" class="rounded-circle me-3">
                                        <?php else: ?>
                                            <div class="avatar-placeholder me-3" style="width: 45px; height: 45px;">
                                                <?php echo strtoupper(substr($ptr['full_name'], 0, 1)); ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="fw-bold"><?php echo $ptr['full_name']; ?></div>
                                    </div>
                                </td>
                                <td>
                                    <div><?php echo $ptr['email']; ?></div>
                                    <div class="small text-muted"><?php echo $ptr['mobile']; ?></div>
                                </td>
                                <td>
                                    <?php if ($ptr['partner_type'] == 'PLATFORM'): ?>
                                        <span class="badge bg-primary">Platform</span>
                                    <?php else: ?>
                                        <span class="badge bg-info text-dark"><?php echo $ptr['white_label_name']; ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($ptr['status'] == 'active'): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?php echo url('partner/profile/' . $ptr['id']); ?>" class="btn btn-sm btn-outline-secondary">Profile</a>
                                    <a href="<?php echo url('partner/edit/' . $ptr['id']); ?>" class="btn btn-sm btn-info text-white"><i class="fas fa-edit"></i></a>
                                    <a href="<?php echo url('partner/delete/' . $ptr['id']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-4">No Partners found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php view('layouts/footer'); ?>
