<?php view('layouts/partner_header', ['title' => 'Select Service']); ?>

<div class="container my-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold">Select <?php echo $parent_service['name']; ?> Type</h1>
        <p class="lead text-muted">Please choose one of the options below to proceed.</p>
    </div>

    <div class="row g-4 justify-content-center">
        <?php
            $colors = ['purple', 'blue', 'green', 'orange'];
            $i = 0;
        ?>
        <?php foreach ($child_services as $svc): ?>
            <?php
                $link = '#';
                if ($svc['form_type'] !== 'NONE') {
                    $link = url('application/create/' . $svc['id']);
                } else {
                    $link = url('application/select/' . $svc['id']);
                }
                $color_class = $colors[$i % count($colors)];
                $i++;
            ?>
            <div class="col-md-6">
                <a href="<?php echo $link; ?>" class="text-decoration-none">
                    <div class="card h-100 shadow-sm stat-card <?php echo $color_class; ?> service-select-card">
                        <div class="card-body d-flex align-items-center p-4">
                            <div class="icon-box me-4">
                                <i class="fas fa-hand-holding-usd"></i> <!-- Generic icon -->
                            </div>
                            <div>
                                <h5 class="card-title fw-bold mb-1 text-dark"><?php echo $svc['name']; ?></h5>
                                <?php if (!empty($svc['description'])): ?>
                                    <p class="card-text text-muted mb-0"><?php echo $svc['description']; ?></p>
                                <?php endif; ?>
                            </div>
                            <i class="fas fa-chevron-right text-muted ms-auto"></i>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="text-center mt-5">
        <a href="<?php echo url('dashboard/partner'); ?>" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i> Back to Dashboard</a>
    </div>
</div>

<style>
.service-select-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.service-select-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
}
</style>

<?php view('layouts/partner_footer'); ?>
