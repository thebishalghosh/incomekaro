<?php view('layouts/header', ['title' => 'All Applications']); ?>

<div class="row mb-4">
    <div class="col-md-6">
        <h1>All Applications</h1>
    </div>
    <div class="col-md-6 text-end">
        <a href="<?php echo url('dashboard/super_admin'); ?>" class="btn btn-secondary">Back to Dashboard</a>
    </div>
</div>

<?php flash('app_success'); ?>
<?php flash('app_error'); ?>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Service</th>
                        <th>Created By</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($applications)): ?>
                        <?php foreach ($applications as $app): ?>
                            <tr>
                                <td>#<?php echo substr($app['id'], 0, 8); ?></td>
                                <td>
                                    <div class="fw-bold"><?php echo $app['customer_name']; ?></div>
                                    <div class="small text-muted"><?php echo $app['customer_phone']; ?></div>
                                </td>
                                <td><?php echo $app['service_name']; ?></td>
                                <td><?php echo $app['first_name'] . ' ' . $app['last_name']; ?></td>
                                <td>
                                    <?php
                                        $badge_class = 'bg-secondary';
                                        if ($app['status'] == 'approved') $badge_class = 'bg-success';
                                        elseif ($app['status'] == 'rejected') $badge_class = 'bg-danger';
                                        elseif ($app['status'] == 'under_verification') $badge_class = 'bg-warning text-dark';
                                    ?>
                                    <span class="badge <?php echo $badge_class; ?>"><?php echo ucfirst(str_replace('_', ' ', $app['status'])); ?></span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($app['created_at'])); ?></td>
                                <td>
                                    <a href="<?php echo url('application/view/' . $app['id']); ?>" class="btn btn-sm btn-primary">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4">No applications found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php view('layouts/footer'); ?>
