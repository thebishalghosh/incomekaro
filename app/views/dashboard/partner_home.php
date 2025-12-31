<?php view('layouts/partner_header', ['title' => 'Partner Dashboard']); ?>

<div class="container">
    <!-- Partner Hero Section -->
    <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, var(--accent-color) 0%, #ffffff 100%);">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-auto">
                    <?php if (!empty($partner['profile']['profile_image'])): ?>
                        <img src="<?php echo asset($partner['profile']['profile_image']); ?>" alt="Profile" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover; border: 4px solid #fff;">
                    <?php else: ?>
                        <div class="avatar-placeholder mx-auto" style="width: 100px; height: 100px; font-size: 2.5rem; border: 4px solid #fff;">
                            <?php echo strtoupper(substr($partner['profile']['full_name'], 0, 1)); ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md">
                    <h2 class="fw-bold mb-1"><?php echo $partner['profile']['full_name']; ?></h2>
                    <p class="text-muted mb-2">Partner ID: <?php echo $partner['id']; ?></p>
                    <div class="d-flex flex-wrap">
                        <span class="me-4"><i class="fas fa-envelope me-2 text-muted"></i><?php echo $partner['profile']['email']; ?></span>
                        <span><i class="fas fa-phone me-2 text-muted"></i><?php echo $partner['profile']['mobile']; ?></span>
                    </div>
                </div>
                <div class="col-md-auto text-end">
                    <?php
                        $kyc_status = $partner['kyc_status'] ?? 'PENDING';
                        $kyc_badge_class = 'bg-warning text-dark';
                        if ($kyc_status == 'VERIFIED') $kyc_badge_class = 'bg-success';
                        elseif ($kyc_status == 'REJECTED') $kyc_badge_class = 'bg-danger';
                    ?>
                    <div class="mb-2">KYC Status</div>
                    <span class="badge <?php echo $kyc_badge_class; ?> fs-6"><?php echo $kyc_status; ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Info Row -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="d-flex align-items-center p-3 bg-white rounded shadow-sm h-100">
                <div class="icon-box bg-light text-primary rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="fas fa-tags fa-lg"></i>
                </div>
                <div>
                    <p class="text-muted mb-1 small text-uppercase fw-bold">Plan Name</p>
                    <h6 class="fw-bold mb-0 text-dark"><?php echo $partner['subscription']['plan_name'] ?? 'Not Subscribed'; ?></h6>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="d-flex align-items-center p-3 bg-white rounded shadow-sm h-100">
                <div class="icon-box bg-light text-success rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="fas fa-user-tie fa-lg"></i>
                </div>
                <div>
                    <p class="text-muted mb-1 small text-uppercase fw-bold">Relationship Manager</p>
                    <h6 class="fw-bold mb-0 text-dark"><?php echo $partner['rm_first'] ? $partner['rm_first'] . ' ' . $partner['rm_last'] : 'Not Assigned'; ?></h6>
                    <?php if ($partner['rm_phone']): ?>
                        <p class="small text-muted mb-0"><i class="fas fa-phone-alt me-1"></i> <?php echo $partner['rm_phone']; ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="d-flex align-items-center p-3 bg-white rounded shadow-sm h-100">
                <div class="icon-box bg-light text-warning rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="fas fa-calendar-alt fa-lg"></i>
                </div>
                <div>
                    <p class="text-muted mb-1 small text-uppercase fw-bold">Joining Date</p>
                    <h6 class="fw-bold mb-0 text-dark"><?php echo date('d M Y', strtotime($partner['created_at'])); ?></h6>
                    <p class="small text-muted mb-0"><?php echo $partner['address_permanent']['state'] ?? ''; ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card text-center h-100 shadow-sm stat-card purple">
                <div class="card-body d-flex flex-column justify-content-center">
                    <div class="icon-box mx-auto">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <h5 class="card-title fw-bold mt-3">IncomeKaro Certificate</h5>
                    <a href="<?php echo url('certificate/download'); ?>" class="btn btn-primary mt-3" target="_blank"><i class="fas fa-download me-2"></i>Download</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center h-100 shadow-sm stat-card blue">
                <div class="card-body d-flex flex-column justify-content-center">
                    <div class="icon-box mx-auto">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <h5 class="card-title fw-bold mt-3">Authorization</h5>
                    <a href="<?php echo url('authorization/download'); ?>" class="btn btn-primary mt-3" target="_blank"><i class="fas fa-download me-2"></i>Download</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center h-100 shadow-sm stat-card green">
                <div class="card-body d-flex flex-column justify-content-center">
                    <div class="icon-box mx-auto">
                        <i class="fas fa-file-contract"></i>
                    </div>
                    <h5 class="card-title fw-bold mt-3">IncomeKaro Agreement</h5>
                    <a href="<?php echo url('agreement/download'); ?>" class="btn btn-primary mt-3" target="_blank"><i class="fas fa-download me-2"></i>Download</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Due Alert -->
    <?php if (!empty($partner['subscription']['due_amount']) && $partner['subscription']['due_amount'] > 0): ?>
        <div class="alert alert-danger shadow-sm border-0 d-flex align-items-center mb-4" role="alert">
            <i class="fas fa-exclamation-circle fa-2x me-3"></i>
            <div>
                <h5 class="alert-heading fw-bold mb-1">Payment Due Alert</h5>
                <p class="mb-0">You have a pending due amount of <strong>₹<?php echo number_format($partner['subscription']['due_amount'], 2); ?></strong>. Please clear your dues to continue enjoying uninterrupted services.</p>
            </div>
        </div>
    <?php endif; ?>

    <!-- My Services Section -->
    <h4 class="fw-bold text-dark mb-3">My Services</h4>
    <?php if (!empty($partner['subscription']['services'])): ?>
        <div class="row g-4">
            <?php foreach ($partner['subscription']['services'] as $svc): ?>
                <div class="col-md-3 col-sm-6">
                    <a href="<?php echo $svc['url'] ?: '#'; ?>" target="_blank" class="text-decoration-none">
                        <div class="card h-100 shadow-sm border-0 service-card">
                            <div class="card-body text-center p-4">
                                <img src="<?php echo asset($svc['image_url'] ?: 'images/default-avatar.png'); ?>" alt="<?php echo $svc['name']; ?>" class="mb-3" style="height: 80px; object-fit: contain;">
                                <h6 class="card-title fw-bold text-dark mb-0"><?php echo $svc['name']; ?></h6>
                                <p class="small text-muted mt-2 mb-0"><?php echo $svc['category']; ?></p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-light border text-center py-4">
            <p class="text-muted mb-0">No services are currently active for your plan.</p>
        </div>
    <?php endif; ?>

</div>

<style>
.service-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.service-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
}
</style>

<?php view('layouts/partner_footer'); ?>
