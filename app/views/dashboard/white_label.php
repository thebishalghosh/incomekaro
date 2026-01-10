<?php view('layouts/header', ['title' => 'Dashboard']); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark">My Dashboard</h2>
        <p class="text-muted">Welcome back, <?php echo $_SESSION['user_name'] ?? 'Client'; ?>!</p>
    </div>
    <div>
        <a href="<?php echo url('partner/create'); ?>" class="btn btn-primary"><i class="fas fa-plus me-2"></i> Add Partner</a>
    </div>
</div>

<!-- Stats Row -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card purple">
            <div class="icon-box">
                <i class="fas fa-users"></i>
            </div>
            <h3><?php echo $stats['total_partners'] ?? 0; ?></h3>
            <p>My Partners</p>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card blue">
            <div class="icon-box">
                <i class="fas fa-file-alt"></i>
            </div>
            <h3><?php echo $stats['total_applications'] ?? 0; ?></h3>
            <p>Total Applications</p>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card green">
            <div class="icon-box">
                <i class="fas fa-wallet"></i>
            </div>
            <h3>₹0.00</h3>
            <p>Wallet Balance</p>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card orange">
            <div class="icon-box">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <h3><?php echo $stats['pending_applications'] ?? 0; ?></h3>
            <p>Action Required</p>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0">Recent Applications</h5>
        <a href="<?php echo url('application/index'); ?>" class="btn btn-sm btn-outline-primary">View All</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Partner</th>
                        <th>Customer</th>
                        <th>Service</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th class="pe-4 text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($stats['recent_applications'])): ?>
                        <?php foreach ($stats['recent_applications'] as $app): ?>
                            <tr>
                                <td class="ps-4"><span class="text-muted small">#<?php echo substr($app['id'], -6); ?></span></td>
                                <td class="fw-bold"><?php echo $app['partner_name']; ?></td>
                                <td><?php echo $app['customer_name']; ?></td>
                                <td><span class="badge bg-light text-dark border"><?php echo $app['service_name']; ?></span></td>
                                <td>
                                    <?php
                                        $status_class = 'bg-secondary';
                                        if ($app['status'] == 'approved') $status_class = 'bg-success';
                                        elseif ($app['status'] == 'rejected') $status_class = 'bg-danger';
                                        elseif ($app['status'] == 'submitted') $status_class = 'bg-primary';
                                        elseif ($app['status'] == 'under_verification') $status_class = 'bg-warning text-dark';
                                    ?>
                                    <span class="badge <?php echo $status_class; ?>"><?php echo ucfirst(str_replace('_', ' ', $app['status'])); ?></span>
                                </td>
                                <td class="text-muted small"><?php echo date('d M, Y', strtotime($app['created_at'])); ?></td>
                                <td class="pe-4 text-end">
                                    <a href="<?php echo url('application/view/' . $app['id']); ?>" class="btn btn-sm btn-light"><i class="fas fa-eye"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
                                <p class="mb-0">No applications found yet.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php view('layouts/footer'); ?>
