<?php view('layouts/header', ['title' => 'View Inquiry']); ?>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-dark">View Inquiry</h2>
            <a href="<?php echo url('inquiry/index'); ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Back
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-bottom p-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="fw-bold mb-2"><?php echo $inquiry['subject']; ?></h4>
                        <div class="d-flex align-items-center text-muted">
                            <div class="me-3">
                                <i class="fas fa-user me-1"></i> <strong><?php echo $inquiry['name']; ?></strong>
                            </div>
                            <div>
                                <i class="fas fa-envelope me-1"></i> <a href="mailto:<?php echo $inquiry['email']; ?>" class="text-decoration-none text-muted"><?php echo $inquiry['email']; ?></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <div class="mb-2">
                            <?php if ($inquiry['wl_name']): ?>
                                <span class="badge bg-info text-dark border"><i class="fas fa-building me-1"></i> <?php echo $inquiry['wl_name']; ?></span>
                            <?php else: ?>
                                <span class="badge bg-secondary"><i class="fas fa-globe me-1"></i> Main Site</span>
                            <?php endif; ?>
                        </div>
                        <div class="text-muted small">
                            <i class="far fa-clock me-1"></i> <?php echo date('d M Y, h:i A', strtotime($inquiry['created_at'])); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body p-5">
                <h6 class="text-uppercase text-muted small fw-bold mb-3 letter-spacing-1">Message Content</h6>
                <div class="bg-light p-4 rounded-3 border" style="min-height: 200px; font-size: 1.05rem; line-height: 1.6;">
                    <?php echo nl2br($inquiry['message']); ?>
                </div>

                <div class="mt-5 d-flex gap-2 border-top pt-4">
                    <a href="mailto:<?php echo $inquiry['email']; ?>?subject=Re: <?php echo urlencode($inquiry['subject']); ?>" class="btn btn-primary btn-lg px-4">
                        <i class="fas fa-reply me-2"></i> Reply via Email
                    </a>
                    <a href="<?php echo url('inquiry/delete/' . $inquiry['id']); ?>" class="btn btn-outline-danger btn-lg px-4 ms-auto" onclick="return confirm('Delete this inquiry?');">
                        <i class="fas fa-trash me-2"></i> Delete
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .letter-spacing-1 { letter-spacing: 1px; }
</style>

<?php view('layouts/footer'); ?>
