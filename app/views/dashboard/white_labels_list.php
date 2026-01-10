<?php view('layouts/header', ['title' => 'White Label Clients']); ?>

<div class="row mb-4">
    <div class="col-md-6">
        <h1>White Label Clients</h1>
        <p class="text-muted">Manage your B2B partners and their domains.</p>
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
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($clients)): ?>
                        <?php foreach ($clients as $client): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($client['logo_url'])): ?>
                                        <img src="<?php echo asset($client['logo_url']); ?>" alt="Logo" style="height: 40px;">
                                    <?php else: ?>
                                        <div class="avatar-placeholder" style="width: 40px; height: 40px;">
                                            <?php echo strtoupper(substr($client['company_name'], 0, 1)); ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-bold"><?php echo $client['company_name']; ?></td>
                                <td>
                                    <a href="http://<?php echo $client['primary_domain']; ?>" target="_blank">
                                        <?php echo $client['primary_domain']; ?> <i class="fas fa-external-link-alt small"></i>
                                    </a>
                                </td>
                                <td>
                                    <?php if ($client['status'] == 'active'): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?php echo url('white_label/subscription/' . $client['id']); ?>" class="btn btn-sm btn-warning text-dark me-1" title="Manage Subscription"><i class="fas fa-tags"></i></a>
                                    <a href="<?php echo url('white_label/edit/' . $client['id']); ?>" class="btn btn-sm btn-info text-white me-1"><i class="fas fa-edit"></i></a>
                                    <a href="<?php echo url('white_label/delete/' . $client['id']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure? This will delete ALL data associated with this client.');"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-4">No White Label clients found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php view('layouts/footer'); ?>
