<?php view('layouts/partner_header', ['title' => $type]); ?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="fw-bold display-6 text-dark"><?php echo $type; ?></h1>
            <p class="lead text-muted mb-0">Select a panel to proceed.</p>
        </div>
        <a href="<?php echo url('application/select/' . $panels[0]['parent_id']); ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i> Back to Types
        </a>
    </div>

    <div class="row g-4">
        <?php if (!empty($panels)): ?>
            <?php foreach ($panels as $panel): ?>
                <div class="col-md-4 col-sm-6">
                    <a href="<?php echo $panel['url']; ?>" target="_blank" class="text-decoration-none">
                        <div class="card h-100 shadow-sm border-0 service-card text-center p-4">
                            <div class="card-body">
                                <?php if (!empty($panel['image_url'])): ?>
                                    <img src="<?php echo asset($panel['image_url']); ?>"
                                         alt="<?php echo $panel['name']; ?>"
                                         class="mb-3 rounded-circle shadow-sm"
                                         style="width: 80px; height: 80px; object-fit: cover;"
                                         onerror="this.onerror=null; this.src='https://via.placeholder.com/80?text=Icon'; this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="icon-box bg-light text-primary rounded-circle mx-auto mb-3 align-items-center justify-content-center" style="width: 80px; height: 80px; display: none;">
                                        <i class="fas fa-external-link-alt fa-2x"></i>
                                    </div>
                                <?php else: ?>
                                    <div class="icon-box bg-light text-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                        <i class="fas fa-external-link-alt fa-2x"></i>
                                    </div>
                                <?php endif; ?>

                                <h5 class="card-title fw-bold text-dark mb-2"><?php echo $panel['name']; ?></h5>

                                <!-- Subtitles -->
                                <div class="d-flex flex-column gap-1">
                                    <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2 fw-normal">
                                        <i class="fas fa-bolt me-1"></i> Instant Access Available
                                    </span>
                                    <span class="small text-muted">
                                        <i class="fas fa-lock me-1 text-success"></i> Secure Connection
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center">
                <div class="alert alert-light border py-5">
                    <i class="fas fa-folder-open fa-3x text-muted mb-3 opacity-25"></i>
                    <p class="text-muted mb-0">No panels found for this type.</p>
                </div>
            </div>
        <?php endif; ?>
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
