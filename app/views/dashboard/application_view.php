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
        <div class="card border-0 shadow-sm mb-4 h-100">
            <div class="card-header bg-light border-0 p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-0"><?php echo $application['service_name']; ?></h5>
                        <p class="text-muted mb-0">Submitted on <?php echo date('d M Y, h:i A', strtotime($application['created_at'])); ?></p>
                    </div>
                    <div>
                        <?php
                            $status_class = 'bg-secondary';
                            $status = $application['status'];

                            if ($status == 'APPROVED' || $status == 'COMPLETED') $status_class = 'bg-success';
                            elseif ($status == 'REJECT') $status_class = 'bg-danger';
                            elseif ($status == 'HOLD' || $status == 'DOCUMENTS_PENDING' || $status == 'BANKAR_PENDENCY') $status_class = 'bg-warning text-dark';
                            elseif ($status == 'LOGIN' || $status == 'DOCUMENTS_UPLOAD') $status_class = 'bg-info text-dark';
                            elseif ($status == 'FRESH') $status_class = 'bg-primary';
                        ?>
                        <span class="badge <?php echo $status_class; ?> fs-6 text-uppercase"><?php echo str_replace('_', ' ', $status); ?></span>
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
        <!-- Documents Card -->
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

        <!-- Actions Card (Admin Only) -->
        <?php if ($_SESSION['role_code'] === 'SUPER_ADMIN'): ?>
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light border-0 p-3">
                <h5 class="fw-bold mb-0">Actions</h5>
            </div>
            <div class="card-body p-3">
                <p class="text-muted small">Update the status of this application.</p>
                <form action="<?php echo url('application/update_status/' . $application['id']); ?>" method="POST">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">New Status</label>
                        <select class="form-select" name="status">
                            <option value="FRESH" <?php echo $application['status'] == 'FRESH' ? 'selected' : ''; ?>>FRESH</option>
                            <option value="DOCUMENTS_UPLOAD" <?php echo $application['status'] == 'DOCUMENTS_UPLOAD' ? 'selected' : ''; ?>>DOCUMENTS UPLOAD</option>
                            <option value="HOLD" <?php echo $application['status'] == 'HOLD' ? 'selected' : ''; ?>>HOLD</option>
                            <option value="LOGIN" <?php echo $application['status'] == 'LOGIN' ? 'selected' : ''; ?>>LOGIN</option>
                            <option value="DOCUMENTS_PENDING" <?php echo $application['status'] == 'DOCUMENTS_PENDING' ? 'selected' : ''; ?>>DOCUMENTS PENDING</option>
                            <option value="BANKAR_PENDENCY" <?php echo $application['status'] == 'BANKAR_PENDENCY' ? 'selected' : ''; ?>>BANKAR PENDENCY</option>
                            <option value="REJECT" <?php echo $application['status'] == 'REJECT' ? 'selected' : ''; ?>>REJECT</option>
                            <option value="APPROVED" <?php echo $application['status'] == 'APPROVED' ? 'selected' : ''; ?>>APPROVED</option>
                            <option value="COMPLETED" <?php echo $application['status'] == 'COMPLETED' ? 'selected' : ''; ?>>COMPLETED</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Comment (Optional)</label>
                        <textarea class="form-control" name="comment" rows="2" placeholder="Reason for update..."></textarea>
                    </div>
                    <div class="d-grid">
                        <button class="btn btn-primary" type="submit">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Full Width Activity Log -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light border-0 p-4">
                <h5 class="fw-bold mb-0">Activity Log</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4 py-3" style="width: 20%;">User</th>
                                <th class="py-3" style="width: 10%;">Role</th>
                                <th class="py-3" style="width: 50%;">Action / Comment</th>
                                <th class="pe-4 py-3 text-end" style="width: 20%;">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($application['comments'])): ?>
                                <?php foreach ($application['comments'] as $log): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-placeholder rounded-circle bg-light text-primary d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px; font-size: 14px;">
                                                    <?php echo strtoupper(substr($log['first_name'], 0, 1)); ?>
                                                </div>
                                                <div class="fw-bold"><?php echo $log['first_name'] . ' ' . $log['last_name']; ?></div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary text-uppercase" style="font-size: 0.7rem;">
                                                <?php echo $log['role_name']; ?>
                                            </span>
                                        </td>
                                        <td class="text-wrap">
                                            <?php echo nl2br(htmlspecialchars($log['comment'])); ?>
                                        </td>
                                        <td class="pe-4 text-end text-muted">
                                            <?php echo date('M d, Y', strtotime($log['created_at'])); ?> at <?php echo date('h:i A', strtotime($log['created_at'])); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">No activity recorded yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php view('layouts/footer'); ?>
