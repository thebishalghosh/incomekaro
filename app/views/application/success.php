<?php view('layouts/partner_header', ['title' => 'Application Submitted']); ?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="card border-0 shadow-sm p-5">
                <div class="mb-4 text-success">
                    <i class="fas fa-check-circle fa-5x"></i>
                </div>
                <h2 class="fw-bold mb-3">Application Submitted Successfully!</h2>
                <p class="lead text-muted mb-4">Your application has been received and is currently under verification. You can track its status from your dashboard.</p>

                <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                    <a href="<?php echo url('dashboard/partner'); ?>" class="btn btn-primary btn-lg px-4 gap-3">Go to Dashboard</a>
                    <a href="<?php echo url('application/index'); ?>" class="btn btn-outline-secondary btn-lg px-4">View All Applications</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php view('layouts/partner_footer'); ?>
