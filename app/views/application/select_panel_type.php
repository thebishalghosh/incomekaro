<?php view('layouts/partner_header', ['title' => 'Select Panel Type']); ?>

<div class="container my-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold display-5 text-dark">Select Panel Type</h1>
        <p class="lead text-muted">Choose the type of service you are looking for.</p>
    </div>

    <div class="row g-4 justify-content-center">
        <?php if (!empty($panel_types)): ?>
            <?php foreach ($panel_types as $type): ?>
                <div class="col-md-4 col-sm-6">
                    <!-- Use base64_encode to avoid URL issues with spaces -->
                    <a href="<?php echo url('instant_panel/list_by_type/' . base64_encode($type)); ?>" class="text-decoration-none">
                        <div class="card h-100 shadow-sm border-0 service-card text-center p-4">
                            <div class="card-body">
                                <div class="icon-box bg-light text-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                                    <i class="fas fa-layer-group fa-2x"></i>
                                </div>
                                <h5 class="card-title fw-bold text-dark mb-0"><?php echo $type; ?></h5>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center">
                <div class="alert alert-light border">
                    <p class="text-muted mb-0">No panel types available at the moment.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="text-center mt-5">
        <a href="<?php echo url('dashboard/partner'); ?>" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i> Back to Dashboard</a>
    </div>
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
