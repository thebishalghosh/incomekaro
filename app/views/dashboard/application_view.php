<?php view('layouts/header', ['title' => 'Application Details']); ?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark">Application Details</h2>
                <p class="text-muted">Review application <span class="text-primary fw-bold"><?php echo $application['id']; ?></span></p>
            </div>
            <a href="<?php echo url('application/index'); ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Back to List
            </a>
        </div>
        <?php flash('app_success'); ?>
        <?php flash('app_error'); ?>
    </div>
</div>

<div class="row">
    <!-- Left Column: Application Data -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light border-0 p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-0"><?php echo $application['service_name']; ?></h5>
                        <p class="text-muted mb-0">Submitted on <?php echo date('d M Y, h:i A', strtotime($application['created_at'])); ?></p>
                    </div>
                    <div>
                        <?php
                            $status_class = 'bg-secondary';
                            if ($application['status'] == 'approved') $status_class = 'bg-success';
                            elseif ($application['status'] == 'rejected') $status_class = 'bg-danger';
                            elseif ($application['status'] == 'under_verification') $status_class = 'bg-warning text-dark';
                            elseif ($application['status'] == 'submitted') $status_class = 'bg-primary';
                        ?>
                        <span class="badge <?php echo $status_class; ?> fs-6 text-uppercase"><?php echo str_replace('_', ' ', $application['status']); ?></span>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <!-- Customer & Partner Info -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted text-uppercase small fw-bold">Customer Details</h6>
                        <p class="mb-1"><strong class="me-2">Name:</strong> <?php echo $application['customer_name']; ?></p>
                        <p class="mb-1"><strong class="me-2">Phone:</strong> <?php echo $application['customer_phone']; ?></p>
                        <p class="mb-0"><strong class="me-2">Email:</strong> <?php echo $application['customer_email']; ?></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted text-uppercase small fw-bold">Partner Details</h6>
                        <p class="mb-1"><strong class="me-2">Name:</strong> <?php echo $application['partner_full_name'] ?: $application['partner_name']; ?></p>
                        <p class="mb-0"><strong class="me-2">Phone:</strong> <?php echo $application['partner_phone']; ?></p>
                    </div>
                </div>
                <hr>
                <!-- All Meta Fields -->
                <h5 class="fw-bold text-primary my-4">Submitted Information</h5>
                <div class="row">
                    <?php if (!empty($application['meta'])): ?>
                        <?php foreach($application['meta'] as $key => $value): ?>
                            <div class="col-md-6 col-lg-4 mb-3">
                                <p class="text-muted mb-0 small text-uppercase"><?php echo str_replace('_', ' ', $key); ?></p>
                                <p class="fw-bold mb-0"><?php echo htmlspecialchars($value) ?: '-'; ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted">No additional information was submitted.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Documents & Actions -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light border-0 p-3">
                <h5 class="fw-bold mb-0">Documents</h5>
            </div>
            <div class="card-body p-3">
                <?php if (!empty($application['documents'])): ?>
                    <div class="list-group">
                        <?php foreach($application['documents'] as $doc): ?>
                            <a href="<?php echo asset($doc['file_url']); ?>" target="_blank" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-file-alt me-2 text-primary"></i>
                                    <?php echo str_replace('_', ' ', $doc['document_type']); ?>
                                </div>
                                <i class="fas fa-external-link-alt text-muted small"></i>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center p-3">No documents were uploaded.</p>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($_SESSION['role_code'] === 'SUPER_ADMIN'): ?>
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light border-0 p-3">
                <h5 class="fw-bold mb-0">Actions</h5>
            </div>
            <div class="card-body p-3">
                <p class="text-muted small">Update the status of this application.</p>
                <form action="<?php echo url('application/update_status/' . $application['id']); ?>" method="POST">
                    <div class="input-group mb-3">
                        <select class="form-select" name="status">
                            <option value="submitted" <?php echo $application['status'] == 'submitted' ? 'selected' : ''; ?>>Submitted</option>
                            <option value="under_verification" <?php echo $application['status'] == 'under_verification' ? 'selected' : ''; ?>>Under Verification</option>
                            <option value="approved" <?php echo $application['status'] == 'approved' ? 'selected' : ''; ?>>Approved</option>
                            <option value="rejected" <?php echo $application['status'] == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                            <option value="completed" <?php echo $application['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                        </select>
                        <button class="btn btn-primary" type="submit">Update</button>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php view('layouts/footer'); ?>
