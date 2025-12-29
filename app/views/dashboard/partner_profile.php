<?php view('layouts/header', ['title' => 'Partner Profile']); ?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark">Partner Profile</h2>
                <p class="text-muted">Detailed information for <?php echo $partner['profile']['full_name']; ?>.</p>
            </div>
            <a href="<?php echo url('partner/index'); ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Back to List
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Left Column: Profile Card -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body text-center p-4">
                <img src="<?php echo asset($partner['profile']['profile_image'] ?: 'images/default-avatar.png'); ?>" alt="Profile" class="rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover; border: 4px solid var(--accent-color);">
                <h4 class="card-title mb-1"><?php echo $partner['profile']['full_name']; ?></h4>
                <p class="text-muted mb-3">
                    <?php if ($partner['partner_type'] == 'PLATFORM'): ?>
                        <span class="badge bg-primary">Platform Partner</span>
                    <?php else: ?>
                        <span class="badge bg-info text-dark">White Label Partner</span>
                    <?php endif; ?>
                </p>
                <a href="<?php echo url('partner/edit/' . $partner['id']); ?>" class="btn btn-primary"><i class="fas fa-edit me-2"></i>Edit Profile</a>
            </div>
            <div class="card-footer bg-white border-0 p-4">
                <h6 class="fw-bold text-muted text-uppercase small mb-3">Contact Info</h6>
                <ul class="list-unstyled text-start">
                    <li class="d-flex align-items-center mb-2">
                        <i class="fas fa-envelope fa-fw me-3 text-muted"></i>
                        <span><?php echo $partner['profile']['email']; ?></span>
                    </li>
                    <li class="d-flex align-items-center mb-2">
                        <i class="fas fa-phone fa-fw me-3 text-muted"></i>
                        <span><?php echo $partner['profile']['mobile']; ?></span>
                    </li>
                    <li class="d-flex align-items-center">
                        <i class="fab fa-whatsapp fa-fw me-3 text-muted"></i>
                        <span><?php echo $partner['profile']['whatsapp'] ?: 'N/A'; ?></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Right Column: Details -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button" role="tab"><i class="fas fa-info-circle me-2"></i>Details</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="team-tab" data-bs-toggle="tab" data-bs-target="#team" type="button" role="tab"><i class="fas fa-users me-2"></i>Team</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="bank-tab" data-bs-toggle="tab" data-bs-target="#bank" type="button" role="tab"><i class="fas fa-university me-2"></i>Bank</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="subscription-tab" data-bs-toggle="tab" data-bs-target="#subscription" type="button" role="tab"><i class="fas fa-tags me-2"></i>Subscription</button>
                    </li>
                </ul>
                <div class="tab-content p-3" id="myTabContent">
                    <!-- Details Tab -->
                    <div class="tab-pane fade show active" id="details" role="tabpanel">
                        <h5 class="fw-bold text-primary mb-4">Permanent Address</h5>
                        <address class="mb-4">
                            <?php echo $partner['address_permanent']['address']; ?><br>
                            <?php echo $partner['address_permanent']['city'] . ', ' . $partner['address_permanent']['state'] . ' - ' . $partner['address_permanent']['pincode']; ?>
                        </address>

                        <h5 class="fw-bold text-primary mt-4 mb-4">Office Address</h5>
                        <address class="mb-4">
                            <?php echo $partner['address_office']['address'] ? ($partner['address_office']['address'] . '<br>' . $partner['address_office']['city'] . ', ' . $partner['address_office']['state'] . ' - ' . $partner['address_office']['pincode']) : 'Same as permanent address'; ?>
                        </address>

                        <h5 class="fw-bold text-primary mt-4 mb-4">Identity</h5>
                        <dl class="row">
                            <dt class="col-sm-3 text-muted">PAN</dt>
                            <dd class="col-sm-9 fw-bold"><?php echo $partner['identity']['pan'] ?: 'N/A'; ?></dd>
                            <dt class="col-sm-3 text-muted">Aadhar</dt>
                            <dd class="col-sm-9 fw-bold"><?php echo $partner['identity']['aadhaar'] ?: 'N/A'; ?></dd>
                            <dt class="col-sm-3 text-muted">GST</dt>
                            <dd class="col-sm-9 fw-bold"><?php echo $partner['identity']['gst'] ?: 'N/A'; ?></dd>
                        </dl>
                    </div>

                    <!-- Team Tab -->
                    <div class="tab-pane fade" id="team" role="tabpanel">
                        <h5 class="fw-bold text-primary mb-4">Sales Executive</h5>
                        <?php if ($partner['creator_first']): ?>
                            <?php if ($partner['creator_role'] === 'SUPER_ADMIN'): ?>
                                <p class="fw-bold">ADMIN</p>
                            <?php else: ?>
                                <p class="fw-bold"><?php echo $partner['creator_first'] . ' ' . $partner['creator_last']; ?></p>
                                <p class="text-muted mb-0"><?php echo $partner['creator_email']; ?></p>
                                <p class="text-muted"><?php echo $partner['creator_phone']; ?></p>
                            <?php endif; ?>
                        <?php else: ?>
                            <p class="text-muted">Unknown (Legacy Data)</p>
                        <?php endif; ?>

                        <hr class="my-4">

                        <h5 class="fw-bold text-primary mb-4">Relationship Manager (RM)</h5>
                        <?php if ($partner['rm_first']): ?>
                            <p class="fw-bold"><?php echo $partner['rm_first'] . ' ' . $partner['rm_last']; ?></p>
                            <p class="text-muted mb-0"><?php echo $partner['rm_email']; ?></p>
                            <p class="text-muted"><?php echo $partner['rm_phone']; ?></p>
                        <?php else: ?>
                            <p class="text-muted">Not assigned yet.</p>
                            <?php if ($_SESSION['role_code'] === 'SUPER_ADMIN'): ?>
                                <button class="btn btn-sm btn-outline-primary mt-2">Assign RM</button>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Bank Details Tab -->
                    <div class="tab-pane fade" id="bank" role="tabpanel">
                        <h5 class="fw-bold text-primary mb-4">Bank Account Details</h5>
                        <?php if ($partner['bank_details']): ?>
                            <dl class="row">
                                <dt class="col-sm-4 text-muted">Account Holder</dt>
                                <dd class="col-sm-8 fw-bold"><?php echo $partner['bank_details']['account_holder_name']; ?></dd>
                                <dt class="col-sm-4 text-muted">Bank Name</dt>
                                <dd class="col-sm-8 fw-bold"><?php echo $partner['bank_details']['bank_name']; ?></dd>
                                <dt class="col-sm-4 text-muted">Account Number</dt>
                                <dd class="col-sm-8 fw-bold"><?php echo $partner['bank_details']['account_number']; ?></dd>
                                <dt class="col-sm-4 text-muted">IFSC Code</dt>
                                <dd class="col-sm-8 fw-bold"><?php echo $partner['bank_details']['ifsc_code']; ?></dd>
                                <dt class="col-sm-4 text-muted">Branch</dt>
                                <dd class="col-sm-8 fw-bold"><?php echo $partner['bank_details']['branch']; ?></dd>
                            </dl>
                        <?php else: ?>
                            <p class="text-muted">No bank details found.</p>
                        <?php endif; ?>
                    </div>

                    <!-- Subscription Tab -->
                    <div class="tab-pane fade" id="subscription" role="tabpanel">
                        <h5 class="fw-bold text-primary mb-4">Subscription Details</h5>
                        <?php if ($partner['subscription']): ?>
                            <dl class="row">
                                <dt class="col-sm-4 text-muted">Plan Title</dt>
                                <dd class="col-sm-8"><span class="badge bg-success fs-6"><?php echo $partner['subscription']['plan_name']; ?></span></dd>

                                <dt class="col-sm-4 text-muted">Plan Price (Incl. GST)</dt>
                                <dd class="col-sm-8 fw-bold">₹<?php echo number_format($partner['subscription']['base_price'] * (1 + $partner['subscription']['gst_rate'] / 100), 2); ?></dd>

                                <dt class="col-sm-4 text-muted">GST Rate</dt>
                                <dd class="col-sm-8 fw-bold"><?php echo $partner['subscription']['gst_rate']; ?>%</dd>

                                <dt class="col-sm-4 text-muted">Transaction ID</dt>
                                <dd class="col-sm-8 fw-bold"><?php echo $partner['subscription']['transaction_id'] ?: 'N/A'; ?></dd>

                                <dt class="col-sm-4 text-muted">Amount Paid</dt>
                                <dd class="col-sm-8 fw-bold">₹<?php echo number_format($partner['subscription']['payment_amount'], 2); ?></dd>

                                <dt class="col-sm-4 text-muted">Due Amount</dt>
                                <dd class="col-sm-8 fw-bold">₹<?php echo number_format($partner['subscription']['due_amount'], 2); ?></dd>
                            </dl>

                            <h5 class="fw-bold text-primary mt-4 mb-4">Included Services</h5>
                            <?php if (!empty($partner['subscription']['services'])): ?>
                                <ul class="list-group">
                                    <?php foreach($partner['subscription']['services'] as $service_name): ?>
                                        <li class="list-group-item border-0 d-flex align-items-center">
                                            <i class="fas fa-check-circle text-success me-2"></i> <?php echo $service_name; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p class="text-muted">No services included in this plan.</p>
                            <?php endif; ?>
                        <?php else: ?>
                            <p class="text-muted">No subscription data found.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php view('layouts/footer'); ?>
