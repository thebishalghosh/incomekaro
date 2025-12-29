<?php view('layouts/header', ['title' => 'Users']); ?>

<div class="row mb-4">
    <div class="col-md-6">
        <h1>All Users</h1>
    </div>
    <div class="col-md-6 text-end">
        <a href="<?php echo url('user/create'); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New User
        </a>
        <a href="<?php echo url('dashboard/super_admin'); ?>" class="btn btn-secondary">Back to Dashboard</a>
    </div>
</div>

<?php flash('user_success'); ?>
<?php flash('user_error'); ?>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>User</th>
                        <th>Role</th>
                        <th>Contact</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if (!empty($u['final_profile_image'])): ?>
                                            <img src="<?php echo asset($u['final_profile_image']); ?>" alt="Avatar" style="width: 45px; height: 45px; object-fit: cover;" class="rounded-circle me-3">
                                        <?php else: ?>
                                            <div class="avatar-placeholder me-3" style="width: 45px; height: 45px;">
                                                <?php echo strtoupper(substr($u['first_name'], 0, 1)); ?>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <div class="fw-bold"><?php echo $u['first_name'] . ' ' . $u['last_name']; ?></div>
                                            <div class="small text-muted"><?php echo $u['email']; ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge bg-info text-dark"><?php echo $u['role_name']; ?></span></td>
                                <td><?php echo $u['phone']; ?></td>
                                <td>
                                    <?php if ($u['status'] == 'active'): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?php echo url('user/edit/' . $u['id']); ?>" class="btn btn-sm btn-info text-white"><i class="fas fa-edit"></i></a>
                                    <a href="<?php echo url('user/delete/' . $u['id']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-4">No users found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php view('layouts/footer'); ?>
