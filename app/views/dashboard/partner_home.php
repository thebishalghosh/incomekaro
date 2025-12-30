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

    <!-- Action Cards -->
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card text-center h-100 shadow-sm stat-card purple">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <div class="icon-box mx-auto">
                            <i class="fas fa-certificate"></i>
                        </div>
                        <h5 class="card-title fw-bold mt-3">IncomeKaro Certificate</h5>
                        <a href="#" class="btn btn-primary mt-3"><i class="fas fa-download me-2"></i>Download</a>
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
                        <a href="#" class="btn btn-primary mt-3"><i class="fas fa-download me-2"></i>Download</a>
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
    </div>
</div>

<?php view('layouts/partner_footer'); ?>
