<?php view('layouts/header', ['title' => 'Master Services']); ?>

<div class="row mb-4">
    <div class="col-md-6">
        <h1>Master Services</h1>
        <p class="text-muted">Manage the core services offered by the platform.</p>
    </div>
    <div class="col-md-6 text-end">
        <?php if ($_SESSION['role_code'] === 'SUPER_ADMIN'): ?>
            <a href="<?php echo url('service/create'); ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Service
            </a>
            <a href="<?php echo url('dashboard/super_admin'); ?>" class="btn btn-secondary">Back to Dashboard</a>
        <?php else: ?>
            <a href="<?php echo url('dashboard/white_label'); ?>" class="btn btn-secondary">Back to Dashboard</a>
        <?php endif; ?>
    </div>
</div>

<?php flash('svc_success'); ?>
<?php flash('svc_error'); ?>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Type</th>
                        <th>Status</th>
                        <?php if ($_SESSION['role_code'] === 'SUPER_ADMIN'): ?>
                            <th>Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($services)): ?>
                        <?php foreach ($services as $svc): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($svc['image_url'])): ?>
                                        <img src="<?php echo asset($svc['image_url']); ?>"
                                             alt="Icon"
                                             style="width: 40px; height: 40px; object-fit: contain;"
                                             onerror="this.onerror=null; this.src='https://via.placeholder.com/40?text=Icon'; this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="avatar-placeholder bg-light text-primary rounded-circle align-items-center justify-content-center" style="width: 40px; height: 40px; display: none;">
                                            <i class="fas fa-box-open"></i>
                                        </div>
                                    <?php else: ?>
                                        <div class="avatar-placeholder bg-light text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="fas fa-box-open"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-bold"><?php echo $svc['name']; ?></td>
                                <td><span class="badge bg-secondary"><?php echo $svc['category']; ?></span></td>
                                <td>
                                    <?php if ($svc['service_type'] == 'INTERNAL_FORM'): ?>
                                        <span class="badge bg-info text-dark">Internal Form</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">External Link</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($svc['is_active']): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Inactive</span>
                                    <?php endif; ?>
                                </td>

                                <?php if ($_SESSION['role_code'] === 'SUPER_ADMIN'): ?>
                                    <td>
                                        <a href="<?php echo url('service/edit/' . $svc['id']); ?>" class="btn btn-sm btn-info text-white"><i class="fas fa-edit"></i></a>

                                        <?php
                                            // Protect Instant Panel AND Internal Forms from deletion
                                            $is_protected = ($svc['category'] === 'INSTANT_PANEL' || $svc['service_type'] === 'INTERNAL_FORM');
                                        ?>

                                        <?php if (!$is_protected): ?>
                                            <a href="<?php echo url('service/delete/' . $svc['id']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');"><i class="fas fa-trash"></i></a>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-secondary" disabled title="Cannot delete core system service"><i class="fas fa-trash"></i></button>
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="<?php echo ($_SESSION['role_code'] === 'SUPER_ADMIN') ? '6' : '5'; ?>" class="text-center py-4">No services found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php view('layouts/footer'); ?>
