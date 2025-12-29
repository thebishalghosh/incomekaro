<?php view('layouts/header', ['title' => 'Master Services']); ?>

<div class="row mb-4">
    <div class="col-md-6">
        <h1>Master Services</h1>
    </div>
    <div class="col-md-6 text-end">
        <a href="<?php echo url('service/create'); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Service
        </a>
        <a href="<?php echo url('dashboard/super_admin'); ?>" class="btn btn-secondary">Back to Dashboard</a>
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
                        <th>URL</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($services)): ?>
                        <?php foreach ($services as $svc): ?>
                            <tr>
                                <td>
                                    <img src="<?php echo asset($svc['image_url'] ?: 'images/default-avatar.png'); ?>" alt="Service" style="width: 50px; height: 50px; object-fit: cover;" class="rounded">
                                </td>
                                <td class="fw-bold"><?php echo $svc['name']; ?></td>
                                <td><?php echo $svc['category']; ?></td>
                                <td><a href="<?php echo $svc['url']; ?>" target="_blank"><?php echo $svc['url']; ?></a></td>
                                <td>
                                    <?php if ($svc['is_active']): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?php echo url('service/edit/' . $svc['id']); ?>" class="btn btn-sm btn-info text-white"><i class="fas fa-edit"></i></a>
                                    <a href="<?php echo url('service/delete/' . $svc['id']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4">No services found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php view('layouts/footer'); ?>
