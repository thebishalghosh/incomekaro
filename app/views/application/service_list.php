<?php view('layouts/partner_header', ['title' => $service['name']]); ?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="fw-bold display-6 text-dark"><?php echo $service['name']; ?></h1>
            <p class="lead text-muted mb-0">Manage your <?php echo strtolower($service['name']); ?> applications.</p>
        </div>
        <a href="<?php echo url('application/new/' . $service['id']); ?>" class="btn btn-primary btn-lg shadow-sm">
            <i class="fas fa-plus-circle me-2"></i> New Application
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3">Date</th>
                            <th class="py-3">App ID</th>
                            <th class="py-3">Customer Name</th>
                            <th class="py-3">Phone</th>
                            <th class="py-3">Status</th>
                            <th class="pe-4 py-3 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($applications)): ?>
                            <?php foreach ($applications as $app): ?>
                                <tr>
                                    <td class="ps-4 text-muted small"><?php echo date('d M Y', strtotime($app['created_at'])); ?></td>
                                    <td class="fw-bold text-primary"><?php echo $app['id']; ?></td>
                                    <td class="fw-bold"><?php echo $app['customer_name']; ?></td>
                                    <td class="text-muted"><?php echo $app['customer_phone']; ?></td>
                                    <td>
                                        <?php
                                            $status_class = 'bg-secondary';
                                            if ($app['status'] == 'approved') $status_class = 'bg-success';
                                            elseif ($app['status'] == 'rejected') $status_class = 'bg-danger';
                                            elseif ($app['status'] == 'under_verification') $status_class = 'bg-warning text-dark';
                                            elseif ($app['status'] == 'submitted') $status_class = 'bg-primary';
                                        ?>
                                        <span class="badge <?php echo $status_class; ?> text-uppercase"><?php echo str_replace('_', ' ', $app['status']); ?></span>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <a href="<?php echo url('application/view/' . $app['id']); ?>" class="btn btn-sm btn-outline-secondary">View Details</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted mb-3">
                                        <i class="fas fa-folder-open fa-3x opacity-25"></i>
                                    </div>
                                    <p class="text-muted mb-0">No applications found for this service.</p>
                                    <a href="<?php echo url('application/new/' . $service['id']); ?>" class="btn btn-link text-decoration-none">Create your first application</a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="text-center mt-5">
        <a href="<?php echo url('dashboard/partner'); ?>" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i> Back to Dashboard</a>
    </div>
</div>

<?php view('layouts/partner_footer'); ?>
