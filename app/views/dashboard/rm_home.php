<?php view('layouts/header', ['title' => 'RM Dashboard']); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark">My Dashboard</h2>
        <p class="text-muted">Overview of your assigned partners and tasks.</p>
    </div>
</div>

<!-- Target & Achievement Row -->
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100 bg-primary text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Monthly Target</h5>
                    <i class="fas fa-bullseye fa-2x opacity-50"></i>
                </div>
                <h2 class="fw-bold mb-0">₹<?php echo number_format($stats['monthly_target'], 2); ?></h2>
                <small class="opacity-75">Revenue Goal for <?php echo date('F Y'); ?></small>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0 text-success">Achieved</h5>
                    <i class="fas fa-chart-line fa-2x text-success opacity-50"></i>
                </div>
                <h2 class="fw-bold text-success mb-0">₹<?php echo number_format($stats['achieved_amount'], 2); ?></h2>
                <div class="progress mt-3" style="height: 10px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo min($stats['achieved_percentage'], 100); ?>%;" aria-valuenow="<?php echo $stats['achieved_percentage']; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <small class="text-muted mt-2 d-block"><?php echo number_format($stats['achieved_percentage'], 1); ?>% of target reached</small>
            </div>
        </div>
    </div>
</div>

<!-- Stats Row -->
<div class="row g-4 mb-4">
    <div class="col-xl-4 col-md-6">
        <div class="stat-card purple">
            <div class="icon-box">
                <i class="fas fa-users"></i>
            </div>
            <h3><?php echo $stats['total_partners'] ?? 0; ?></h3>
            <p>Assigned Partners</p>
        </div>
    </div>
    <div class="col-xl-4 col-md-6">
        <div class="stat-card orange">
            <div class="icon-box">
                <i class="fas fa-id-card"></i>
            </div>
            <h3><?php echo $stats['pending_kyc'] ?? 0; ?></h3>
            <p>Pending KYC</p>
        </div>
    </div>
    <div class="col-xl-4 col-md-6">
        <div class="stat-card blue">
            <div class="icon-box">
                <i class="fas fa-file-alt"></i>
            </div>
            <h3><?php echo $stats['pending_applications'] ?? 0; ?></h3>
            <p>Pending Applications</p>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Pending KYC List -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-warning"><i class="fas fa-exclamation-circle me-2"></i> KYC Verification</h5>
                <a href="<?php echo url('rm/partners'); ?>" class="btn btn-sm btn-outline-secondary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php if (!empty($stats['kyc_list'])): ?>
                        <?php foreach ($stats['kyc_list'] as $partner): ?>
                            <a href="<?php echo url('partner/profile/' . $partner['id']); ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
                                <div>
                                    <div class="fw-bold text-dark"><?php echo $partner['name']; ?></div>
                                    <small class="text-muted">ID: <?php echo $partner['id']; ?></small>
                                </div>
                                <span class="badge bg-warning text-dark">Pending</span>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-check-circle fa-3x mb-3 opacity-25 text-success"></i>
                            <p class="mb-0">No pending KYC requests.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Applications List -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-primary"><i class="fas fa-file-contract me-2"></i> Application Review</h5>
                <a href="<?php echo url('rm/applications'); ?>" class="btn btn-sm btn-outline-secondary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php if (!empty($stats['application_list'])): ?>
                        <?php foreach ($stats['application_list'] as $app): ?>
                            <a href="<?php echo url('application/view/' . $app['id']); ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
                                <div>
                                    <div class="fw-bold text-dark"><?php echo $app['customer_name']; ?></div>
                                    <small class="text-muted"><?php echo $app['service_name']; ?> • <?php echo $app['partner_name']; ?></small>
                                </div>
                                <span class="badge bg-info text-dark"><?php echo ucfirst(str_replace('_', ' ', $app['status'])); ?></span>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-check-circle fa-3x mb-3 opacity-25 text-success"></i>
                            <p class="mb-0">No pending applications.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php view('layouts/footer'); ?>
