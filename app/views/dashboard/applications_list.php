<?php view('layouts/header', ['title' => 'Applications']); ?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark">Applications</h2>
                <p class="text-muted">Manage all service applications submitted by partners.</p>
            </div>
            <!-- Optional: Add filters here later -->
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3">Date</th>
                        <th class="py-3">App ID</th>
                        <th class="py-3">Partner</th>
                        <th class="py-3">Service</th>
                        <th class="py-3">Customer</th>
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
                                <td>
                                    <div class="fw-bold"><?php echo $app['partner_full_name'] ?: $app['partner_name']; ?></div>
                                    <div class="small text-muted">ID: <?php echo $app['partner_id']; ?></div>
                                </td>
                                <td><span class="badge bg-info text-dark"><?php echo $app['service_name']; ?></span></td>
                                <td>
                                    <div class="fw-bold"><?php echo $app['customer_name']; ?></div>
                                    <div class="small text-muted"><?php echo $app['customer_phone']; ?></div>
                                </td>
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
                                    <a href="<?php echo url('application/view/' . $app['id']); ?>" class="btn btn-sm btn-outline-primary">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">No applications found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php view('layouts/footer'); ?>
