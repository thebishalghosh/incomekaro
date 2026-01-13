<?php view('layouts/header', ['title' => 'Partners']); ?>

<div class="row mb-4">
    <div class="col-md-6">
        <h1>Partners</h1>
        <p class="text-muted">Manage your partner network.</p>
    </div>
    <div class="col-md-6 text-end">
        <?php if ($_SESSION['role_code'] === 'SUPER_ADMIN' || $_SESSION['role_code'] === 'WHITE_LABEL'): ?>
            <a href="<?php echo url('partner/create'); ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Partner
            </a>
        <?php endif; ?>

        <?php if ($_SESSION['role_code'] === 'SUPER_ADMIN'): ?>
            <a href="<?php echo url('dashboard/super_admin'); ?>" class="btn btn-secondary">Back to Dashboard</a>
        <?php elseif ($_SESSION['role_code'] === 'WHITE_LABEL'): ?>
            <a href="<?php echo url('dashboard/white_label'); ?>" class="btn btn-secondary">Back to Dashboard</a>
        <?php elseif ($_SESSION['role_code'] === 'RM'): ?>
            <a href="<?php echo url('rm/index'); ?>" class="btn btn-secondary">Back to Dashboard</a>
        <?php endif; ?>
    </div>
</div>

<?php flash('ptr_success'); ?>
<?php flash('ptr_error'); ?>
<?php flash('user_success'); ?>
<?php flash('user_error'); ?>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive" style="min-height: 300px;">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Contact</th>
                        <th>Type</th>
                        <?php if ($_SESSION['role_code'] === 'SUPER_ADMIN'): ?>
                            <th>White Label</th>
                        <?php endif; ?>
                        <th>Wallet</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($partners)): ?>
                        <?php foreach ($partners as $partner): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if (!empty($partner['profile_image'])): ?>
                                            <img src="<?php echo asset($partner['profile_image']); ?>" alt="Avatar" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="avatar-placeholder bg-light text-primary rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <?php echo strtoupper(substr($partner['full_name'] ?? $partner['name'], 0, 1)); ?>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <div class="fw-bold"><?php echo $partner['full_name'] ?? $partner['name']; ?></div>
                                            <div class="small text-muted">ID: <?php echo $partner['id']; ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div><i class="fas fa-envelope me-1 text-muted"></i> <?php echo $partner['email']; ?></div>
                                    <div><i class="fas fa-phone me-1 text-muted"></i> <?php echo $partner['mobile'] ?? $partner['phone']; ?></div>
                                </td>
                                <td>
                                    <?php if ($partner['partner_type'] == 'PLATFORM'): ?>
                                        <span class="badge bg-primary">Platform</span>
                                    <?php else: ?>
                                        <span class="badge bg-info text-dark">White Label</span>
                                    <?php endif; ?>
                                </td>

                                <?php if ($_SESSION['role_code'] === 'SUPER_ADMIN'): ?>
                                    <td>
                                        <?php echo $partner['white_label_name'] ?? '-'; ?>
                                    </td>
                                <?php endif; ?>

                                <td>
                                    <span class="fw-bold text-success">₹<?php echo number_format($partner['wallet_balance'] ?? 0, 2); ?></span>
                                </td>

                                <td>
                                    <?php if ($_SESSION['role_code'] === 'SUPER_ADMIN' || $_SESSION['role_code'] === 'WHITE_LABEL'): ?>
                                        <div class="dropdown">
                                            <button class="btn btn-sm <?php echo $partner['status'] == 'active' ? 'btn-success' : 'btn-danger'; ?> dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-boundary="window">
                                                <?php if ($partner['status'] == 'active'): ?>
                                                    <i class="fas fa-check-circle me-1"></i> Active
                                                <?php else: ?>
                                                    <i class="fas fa-ban me-1"></i> Inactive
                                                <?php endif; ?>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <form action="<?php echo url('partner/update_status/' . $partner['id']); ?>" method="POST">
                                                        <input type="hidden" name="status" value="active">
                                                        <button type="submit" class="dropdown-item text-success"><i class="fas fa-check-circle me-2"></i>Active</button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <button type="button" class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#revokeModal<?php echo $partner['id']; ?>">
                                                        <i class="fas fa-ban me-2"></i>Revoke Access
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    <?php else: ?>
                                        <?php if ($partner['status'] == 'active'): ?>
                                            <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger"><i class="fas fa-ban me-1"></i> Inactive</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <!-- Wallet Button Logic -->
                                    <?php
                                        $show_wallet = false;
                                        if ($_SESSION['role_code'] === 'WHITE_LABEL') {
                                            $show_wallet = true; // WL Admin can manage own partners
                                        } elseif ($_SESSION['role_code'] === 'SUPER_ADMIN') {
                                            // Super Admin can manage Platform Partners, but NOT White Label Partners
                                            if ($partner['partner_type'] === 'PLATFORM') {
                                                $show_wallet = true;
                                            }
                                        }
                                    ?>

                                    <?php if ($show_wallet): ?>
                                        <button class="btn btn-sm btn-warning text-dark me-1" data-bs-toggle="modal" data-bs-target="#walletModal<?php echo $partner['user_id']; ?>" title="Manage Wallet">
                                            <i class="fas fa-wallet"></i>
                                        </button>
                                    <?php endif; ?>

                                    <a href="<?php echo url('partner/profile/' . $partner['id']); ?>" class="btn btn-sm btn-light me-1" title="View Profile"><i class="fas fa-eye"></i></a>

                                    <?php if ($_SESSION['role_code'] !== 'RM'): ?>
                                        <a href="<?php echo url('partner/edit/' . $partner['id']); ?>" class="btn btn-sm btn-info text-white me-1" title="Edit"><i class="fas fa-edit"></i></a>
                                    <?php endif; ?>

                                    <?php if ($_SESSION['role_code'] === 'SUPER_ADMIN' || $_SESSION['role_code'] === 'WHITE_LABEL'): ?>
                                        <a href="<?php echo url('partner/delete/' . $partner['id']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure? This will delete all partner data.');" title="Delete"><i class="fas fa-trash"></i></a>
                                    <?php endif; ?>

                                    <!-- Revoke Modal -->
                                    <div class="modal fade" id="revokeModal<?php echo $partner['id']; ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title">Revoke Access</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="<?php echo url('partner/update_status/' . $partner['id']); ?>" method="POST">
                                                    <div class="modal-body">
                                                        <p>Are you sure you want to revoke access for <strong><?php echo $partner['full_name'] ?? $partner['name']; ?></strong>?</p>
                                                        <p class="text-muted small">This will prevent the partner from logging in and accessing their dashboard.</p>
                                                        <input type="hidden" name="status" value="inactive">
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger">Revoke Access</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Wallet Modal -->
                                    <?php if (isset($partner['user_id']) && $show_wallet): ?>
                                    <div class="modal fade" id="walletModal<?php echo $partner['user_id']; ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg overflow-hidden">
                                                <!-- Header with Theme Color -->
                                                <div class="modal-header text-white border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                                    <div class="d-flex align-items-center">
                                                        <div class="icon-box bg-white bg-opacity-25 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                            <i class="fas fa-wallet"></i>
                                                        </div>
                                                        <div>
                                                            <h5 class="modal-title fw-bold mb-0">Manage Wallet</h5>
                                                            <small class="opacity-75"><?php echo $partner['full_name'] ?? $partner['name']; ?></small>
                                                        </div>
                                                    </div>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>

                                                <?php if (($partner['has_bank_details'] ?? 0) > 0): ?>
                                                    <form action="<?php echo url('user/wallet_update/' . $partner['user_id']); ?>" method="POST">
                                                        <div class="modal-body p-4">
                                                            <div class="row g-4">
                                                                <!-- Left: Bank Details -->
                                                                <div class="col-md-6 border-end">
                                                                    <div class="card bg-success-subtle border-0 mb-4">
                                                                        <div class="card-body text-center p-3">
                                                                            <small class="text-uppercase fw-bold text-success opacity-75">Current Balance</small>
                                                                            <h2 class="fw-bold text-success mb-0">₹<?php echo number_format($partner['wallet_balance'] ?? 0, 2); ?></h2>
                                                                        </div>
                                                                    </div>

                                                                    <h6 class="fw-bold text-dark mb-3 d-flex align-items-center">
                                                                        <i class="fas fa-university me-2" style="color: #667eea;"></i> Bank Details
                                                                    </h6>
                                                                    <div class="bg-light p-3 rounded-3 border border-start-4" style="border-left-color: #667eea !important;">
                                                                        <div class="mb-2 pb-2 border-bottom">
                                                                            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Account Holder</small>
                                                                            <div class="fw-bold text-dark"><?php echo $partner['account_holder_name']; ?></div>
                                                                        </div>
                                                                        <div class="mb-2 pb-2 border-bottom">
                                                                            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Bank Name</small>
                                                                            <div class="text-dark"><?php echo $partner['bank_name']; ?></div>
                                                                        </div>
                                                                        <div class="mb-2 pb-2 border-bottom">
                                                                            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Account Number</small>
                                                                            <div class="font-monospace text-dark fs-5"><?php echo $partner['account_number']; ?></div>
                                                                        </div>
                                                                        <div>
                                                                            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">IFSC Code</small>
                                                                            <div class="font-monospace text-dark"><?php echo $partner['ifsc_code']; ?></div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- Right: Form -->
                                                                <div class="col-md-6 ps-md-4">
                                                                    <h6 class="fw-bold text-dark mb-3">Update Balance</h6>
                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-bold small text-uppercase">Action</label>
                                                                        <select class="form-select form-select-lg" name="type">
                                                                            <option value="credit">Credit (Add Money)</option>
                                                                            <option value="debit">Debit (Deduct Money)</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-bold small text-uppercase">Amount (₹)</label>
                                                                        <div class="input-group input-group-lg">
                                                                            <span class="input-group-text bg-light border-end-0">₹</span>
                                                                            <input type="number" step="0.01" class="form-control border-start-0 ps-0" name="amount" required placeholder="0.00">
                                                                        </div>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-bold small text-uppercase">Description</label>
                                                                        <textarea class="form-control" name="description" rows="3" placeholder="Reason for adjustment..."></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer bg-light border-top-0 p-3">
                                                            <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-primary px-4 shadow-sm">Update Wallet</button>
                                                        </div>
                                                    </form>
                                                <?php else: ?>
                                                    <div class="modal-body text-center py-5">
                                                        <div class="icon-box bg-warning-subtle text-warning rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                                            <i class="fas fa-university fa-3x"></i>
                                                        </div>
                                                        <h4 class="fw-bold text-dark">Bank Details Missing</h4>
                                                        <p class="text-muted mb-4 px-4">You cannot add balance to this partner because their bank details are not updated. Please ask them to update their profile.</p>
                                                        <a href="<?php echo url('partner/edit/' . $partner['id']); ?>" class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm">
                                                            <i class="fas fa-edit me-2"></i> Update Profile
                                                        </a>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="<?php echo ($_SESSION['role_code'] === 'SUPER_ADMIN') ? '7' : '6'; ?>" class="text-center py-4">No partners found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php view('layouts/footer'); ?>
