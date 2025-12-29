<?php view('layouts/header', ['title' => 'White Label Clients']); ?>

<div class="row mb-4">
    <div class="col-md-6">
        <h1>White Label Clients</h1>
    </div>
    <div class="col-md-6 text-end">
        <a href="<?php echo url('white_label/create'); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Client
        </a>
        <a href="<?php echo url('dashboard/super_admin'); ?>" class="btn btn-secondary">Back to Dashboard</a>
    </div>
</div>

<?php flash('wl_success'); ?>
<?php flash('wl_error'); ?>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Logo</th>
                        <th>Company Name</th>
                        <th>Domain</th>
                        <th>Support Email</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($white_labels)): ?>
                        <?php foreach ($white_labels as $wl): ?>
                            <tr>
                                <td>
                                    <?php if ($wl['logo_url']): ?>
                                        <img src="<?php echo asset($wl['logo_url']); ?>" alt="Logo" style="height: 40px;">
                                    <?php else: ?>
                                        <span class="text-muted">No Logo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-bold"><?php echo $wl['company_name']; ?></td>
                                <td><a href="http://<?php echo $wl['primary_domain']; ?>" target="_blank"><?php echo $wl['primary_domain']; ?></a></td>
                                <td><?php echo $wl['support_email']; ?></td>
                                <td>
                                    <?php if ($wl['status'] == 'active'): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?php echo url('white_label/edit/' . $wl['id']); ?>" class="btn btn-sm btn-info text-white"><i class="fas fa-edit"></i></a>
                                    <a href="<?php echo url('white_label/delete/' . $wl['id']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4">No White Label Clients found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php view('layouts/footer'); ?>
