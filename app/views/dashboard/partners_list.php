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
                                            <img src="<?php echo asset($ptr['profile_image']); ?>" alt="Avatar" style="width: 45px; height: 45px; object-fit: cover;" class="rounded-circle me-3">
                                        <?php else: ?>
                                            <div class="avatar-placeholder me-3" style="width: 45px; height: 45px; font-size: 1.2rem;">
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
                                    <a href="<?php echo url('partner/profile/' . $ptr['id']); ?>" class="btn btn-sm btn-outline-secondary" title="View Profile"><i class="fas fa-eye"></i></a>
                                    <a href="<?php echo url('partner/edit/' . $ptr['id']); ?>" class="btn btn-sm btn-info text-white" title="Edit"><i class="fas fa-edit"></i></a>
                                    <button type="button" class="btn btn-sm btn-warning text-dark" data-bs-toggle="modal" data-bs-target="#statusModal<?php echo $ptr['id']; ?>" title="Change Status">
                                        <i class="fas fa-user-lock"></i>
                                    </button>
                                    <a href="<?php echo url('partner/delete/' . $ptr['id']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');" title="Delete"><i class="fas fa-trash"></i></a>

                                    <!-- Status Modal -->
                                    <div class="modal fade" id="statusModal<?php echo $ptr['id']; ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Change Status for <?php echo $ptr['full_name']; ?></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="<?php echo url('partner/update_status/' . $ptr['id']); ?>" method="POST">
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="status" class="form-label">Select Status</label>
                                                            <select class="form-select" name="status">
                                                                <option value="active" <?php echo $ptr['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                                                                <option value="inactive" <?php echo $ptr['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive (Revoke Access)</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        <button type="submit" class="btn btn-primary">Update Status</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
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
