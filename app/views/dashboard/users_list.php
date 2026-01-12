<?php view('layouts/header', ['title' => 'Users']); ?>

<div class="row mb-4">
    <div class="col-md-6">
        <h1>Users</h1>
        <p class="text-muted">Manage system users, admins, and partners.</p>
    </div>
    <div class="col-md-6 text-end">
        <a href="<?php echo url('user/create'); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New User
        </a>
        <a href="<?php echo url('dashboard/super_admin'); ?>" class="btn btn-secondary">Back to Dashboard</a>
    </div>
</div>

<?php flash('user_success'); ?>
<?php flash('user_error'); ?>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Email</th>
                        <th>Wallet</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td class="fw-bold">
                                    <?php echo $user['first_name'] . ' ' . $user['last_name']; ?>
                                    <?php if ($user['wl_name']): ?>
                                        <div class="small text-muted"><i class="fas fa-building"></i> <?php echo $user['wl_name']; ?></div>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge bg-info text-dark"><?php echo $user['role_name']; ?></span></td>
                                <td><?php echo $user['email']; ?></td>
                                <td>
                                    <span class="fw-bold text-success">₹<?php echo number_format($user['wallet_balance'] ?? 0, 2); ?></span>
                                </td>
                                <td>
                                    <?php if ($user['status'] == 'active'): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                        // Wallet Button Logic for Super Admin
                                        $show_wallet = true;

                                        // If user is a Partner belonging to a White Label, hide wallet
                                        // We check if role is 'Partner Admin' (usually ID 3 or 4, but let's rely on name or code if available)
                                        // The query returns role_name. Let's assume 'Partner Admin' is the name.
                                        // Better: The query doesn't return role_code.
                                        // However, we know WL Partners have a white_label_id AND are not the WL Admin themselves.
                                        // WL Admin also has white_label_id.

                                        // Logic:
                                        // If user has white_label_id:
                                        //    If role is 'White Label' -> Show (It's the client)
                                        //    If role is 'Partner Admin' -> Hide (It's a sub-partner)

                                        if (!empty($user['white_label_id'])) {
                                            // We need to distinguish between WL Admin and WL Partner
                                            // WL Admin role name is usually 'White Label'
                                            // Partner role name is usually 'Partner Admin'

                                            if (stripos($user['role_name'], 'Partner') !== false) {
                                                $show_wallet = false;
                                            }
                                        }
                                    ?>

                                    <?php if ($show_wallet): ?>
                                        <button class="btn btn-sm btn-warning text-dark me-1" data-bs-toggle="modal" data-bs-target="#walletModal<?php echo $user['id']; ?>" title="Manage Wallet">
                                            <i class="fas fa-wallet"></i>
                                        </button>
                                    <?php endif; ?>

                                    <a href="<?php echo url('user/edit/' . $user['id']); ?>" class="btn btn-sm btn-info text-white me-1"><i class="fas fa-edit"></i></a>
                                    <a href="<?php echo url('user/delete/' . $user['id']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');"><i class="fas fa-trash"></i></a>

                                    <!-- Wallet Modal -->
                                    <?php if ($show_wallet): ?>
                                    <div class="modal fade" id="walletModal<?php echo $user['id']; ?>" tabindex="-1" aria-hidden="true">
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
                                                            <small class="opacity-75"><?php echo $user['first_name'] . ' ' . $user['last_name']; ?></small>
                                                        </div>
                                                    </div>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>

                                                <?php if ($user['has_bank_details'] > 0): ?>
                                                    <form action="<?php echo url('user/wallet_update/' . $user['id']); ?>" method="POST">
                                                        <div class="modal-body p-4">
                                                            <div class="row g-4">
                                                                <!-- Left: Bank Details -->
                                                                <div class="col-md-6 border-end">
                                                                    <div class="card bg-success-subtle border-0 mb-4">
                                                                        <div class="card-body text-center p-3">
                                                                            <small class="text-uppercase fw-bold text-success opacity-75">Current Balance</small>
                                                                            <h2 class="fw-bold text-success mb-0">₹<?php echo number_format($user['wallet_balance'] ?? 0, 2); ?></h2>
                                                                        </div>
                                                                    </div>

                                                                    <h6 class="fw-bold text-dark mb-3 d-flex align-items-center">
                                                                        <i class="fas fa-university me-2" style="color: #667eea;"></i> Bank Details
                                                                    </h6>
                                                                    <div class="bg-light p-3 rounded-3 border border-start-4" style="border-left-color: #667eea !important;">
                                                                        <div class="mb-2 pb-2 border-bottom">
                                                                            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Account Holder</small>
                                                                            <div class="fw-bold text-dark"><?php echo $user['account_holder_name']; ?></div>
                                                                        </div>
                                                                        <div class="mb-2 pb-2 border-bottom">
                                                                            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Bank Name</small>
                                                                            <div class="text-dark"><?php echo $user['bank_name']; ?></div>
                                                                        </div>
                                                                        <div class="mb-2 pb-2 border-bottom">
                                                                            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Account Number</small>
                                                                            <div class="font-monospace text-dark fs-5"><?php echo $user['account_number']; ?></div>
                                                                        </div>
                                                                        <div>
                                                                            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">IFSC Code</small>
                                                                            <div class="font-monospace text-dark"><?php echo $user['ifsc_code']; ?></div>
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
                                                        <p class="text-muted mb-4 px-4">You cannot add balance to this user because their bank details are not updated. Please update the profile first.</p>
                                                        <a href="<?php echo url('user/edit/' . $user['id']); ?>#bank" class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm">
                                                            <i class="fas fa-plus me-2"></i> Add Bank Details
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
                            <td colspan="6" class="text-center py-4">No users found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php view('layouts/footer'); ?>
