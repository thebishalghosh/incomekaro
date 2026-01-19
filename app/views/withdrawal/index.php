<?php
if ($_SESSION['role_code'] === 'PARTNER_ADMIN') {
    view('layouts/partner_header', ['title' => 'Withdrawals']);
} else {
    view('layouts/header', ['title' => 'Withdrawals']);
}
?>

<div class="container <?php echo ($_SESSION['role_code'] === 'PARTNER_ADMIN') ? '' : 'px-0'; ?>">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">Withdrawals</h2>
            <p class="text-muted mb-0">Track your payout requests and status.</p>
        </div>
        <?php if ($_SESSION['role_code'] !== 'SUPER_ADMIN' && $_SESSION['role_code'] !== 'WHITE_LABEL'): ?>
            <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#requestModal">
                <i class="fas fa-plus me-2"></i> Request Withdrawal
            </button>
        <?php endif; ?>
    </div>

    <?php flash('withdraw_success'); ?>
    <?php flash('withdraw_error'); ?>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-uppercase small fw-bold text-muted">Date</th>
                            <?php if ($_SESSION['role_code'] === 'SUPER_ADMIN' || $_SESSION['role_code'] === 'WHITE_LABEL'): ?>
                                <th class="py-3 text-uppercase small fw-bold text-muted">User</th>
                            <?php endif; ?>
                            <th class="py-3 text-uppercase small fw-bold text-muted">Amount Breakdown</th>
                            <th class="py-3 text-uppercase small fw-bold text-muted">Bank Details</th>
                            <th class="py-3 text-uppercase small fw-bold text-muted">Status</th>
                            <?php if ($_SESSION['role_code'] === 'SUPER_ADMIN' || $_SESSION['role_code'] === 'WHITE_LABEL'): ?>
                                <th class="text-end pe-4 py-3 text-uppercase small fw-bold text-muted">Actions</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($withdrawals)): ?>
                            <?php foreach ($withdrawals as $wd): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark"><?php echo date('d M, Y', strtotime($wd['created_at'])); ?></div>
                                        <div class="small text-muted"><?php echo date('h:i A', strtotime($wd['created_at'])); ?></div>
                                    </td>

                                    <?php if ($_SESSION['role_code'] === 'SUPER_ADMIN' || $_SESSION['role_code'] === 'WHITE_LABEL'): ?>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-placeholder bg-light text-primary rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px; font-size: 0.8rem;">
                                                    <?php echo strtoupper(substr($wd['first_name'], 0, 1)); ?>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark"><?php echo $wd['first_name'] . ' ' . $wd['last_name']; ?></div>
                                                    <div class="small text-muted"><?php echo $wd['email']; ?></div>
                                                    <?php if (!empty($wd['white_label_id']) && $_SESSION['role_code'] === 'SUPER_ADMIN'): ?>
                                                        <span class="badge bg-light text-secondary border mt-1" style="font-size: 0.65rem;">WL User</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                    <?php endif; ?>

                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold text-success fs-6">Net: ₹<?php echo number_format($wd['net_amount'] ?? 0, 2); ?></span>
                                            <span class="small text-muted">Gross: ₹<?php echo number_format($wd['gross_amount'] ?? 0, 2); ?></span>
                                            <span class="small text-danger" style="font-size: 0.7rem;">TDS (2%): -₹<?php echo number_format($wd['tds_amount'] ?? 0, 2); ?></span>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="icon-box bg-light text-secondary rounded me-2 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                                <i class="fas fa-university"></i>
                                            </div>
                                            <div class="small">
                                                <div class="fw-bold text-dark"><?php echo $wd['bank_name']; ?></div>
                                                <div class="text-muted">AC: <?php echo $wd['bank_account_number']; ?></div>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <?php
                                            $status_class = 'bg-secondary-subtle text-secondary';
                                            $icon = 'fa-circle';

                                            if ($wd['status'] == 'approved') {
                                                $status_class = 'bg-info-subtle text-info fw-bold';
                                                $icon = 'fa-check-circle';
                                            } elseif ($wd['status'] == 'paid') {
                                                $status_class = 'bg-success-subtle text-success fw-bold';
                                                $icon = 'fa-check-double';
                                            } elseif ($wd['status'] == 'rejected') {
                                                $status_class = 'bg-danger-subtle text-danger fw-bold';
                                                $icon = 'fa-times-circle';
                                            } elseif ($wd['status'] == 'requested') {
                                                $status_class = 'bg-warning-subtle text-warning fw-bold';
                                                $icon = 'fa-clock';
                                            }
                                        ?>
                                        <span class="badge <?php echo $status_class; ?> px-3 py-2 rounded-pill border border-0">
                                            <i class="fas <?php echo $icon; ?> me-1"></i> <?php echo ucfirst($wd['status']); ?>
                                        </span>
                                    </td>

                                    <?php if ($_SESSION['role_code'] === 'SUPER_ADMIN' || $_SESSION['role_code'] === 'WHITE_LABEL'): ?>
                                        <td class="text-end pe-4">
                                            <?php
                                                // Logic to hide actions for Super Admin if it's a WL Partner
                                                $show_actions = true;
                                                if ($_SESSION['role_code'] === 'SUPER_ADMIN' && !empty($wd['white_label_id'])) {
                                                    if (isset($wd['role_code']) && $wd['role_code'] === 'PARTNER_ADMIN') {
                                                        $show_actions = false;
                                                    }
                                                }
                                            ?>

                                            <?php if ($show_actions): ?>
                                                <?php if ($wd['status'] == 'requested'): ?>
                                                    <a href="<?php echo url('withdrawal/approve/' . $wd['id']); ?>" class="btn btn-sm btn-success me-1 shadow-sm" onclick="return confirm('Approve this request?');" title="Approve"><i class="fas fa-check"></i></a>
                                                    <a href="<?php echo url('withdrawal/reject/' . $wd['id']); ?>" class="btn btn-sm btn-danger shadow-sm" onclick="return confirm('Reject and refund?');" title="Reject"><i class="fas fa-times"></i></a>
                                                <?php elseif ($wd['status'] == 'approved'): ?>
                                                    <a href="<?php echo url('withdrawal/mark_paid/' . $wd['id']); ?>" class="btn btn-sm btn-primary shadow-sm" onclick="return confirm('Mark as Paid?');">Mark Paid</a>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-muted small fst-italic">Managed by WL</span>
                                            <?php endif; ?>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="<?php echo ($_SESSION['role_code'] === 'SUPER_ADMIN' || $_SESSION['role_code'] === 'WHITE_LABEL') ? '6' : '5'; ?>" class="text-center py-5">
                                    <div class="py-5">
                                        <div class="icon-box bg-light text-muted rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                            <i class="fas fa-wallet fa-3x opacity-50"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark">No Withdrawal History</h5>
                                        <p class="text-muted mb-4">No withdrawal requests found.</p>
                                        <?php if ($_SESSION['role_code'] !== 'SUPER_ADMIN' && $_SESSION['role_code'] !== 'WHITE_LABEL'): ?>
                                            <button type="button" class="btn btn-primary px-4 rounded-pill" data-bs-toggle="modal" data-bs-target="#requestModal">
                                                Request Now
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Request Modal -->
<?php if ($_SESSION['role_code'] !== 'SUPER_ADMIN' && $_SESSION['role_code'] !== 'WHITE_LABEL' && isset($user)): ?>
<div class="modal fade" id="requestModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 bg-primary text-white" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);">
                <h5 class="modal-title fw-bold">Request Withdrawal</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <p class="text-muted mb-1 text-uppercase small fw-bold">Available Balance</p>
                    <h2 class="fw-bold text-success">₹<?php echo number_format($user['wallet_balance'] ?? 0, 2); ?></h2>
                </div>

                <form action="<?php echo url('withdrawal/store'); ?>" method="POST">
                    <div class="mb-2">
                        <label class="form-label fw-bold">Amount (₹)</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-light border-end-0">₹</span>
                            <input type="number" step="0.01" class="form-control border-start-0 ps-0" name="amount" id="withdrawAmount" max="<?php echo $user['wallet_balance']; ?>" required placeholder="0.00" oninput="calculateTDS()">
                        </div>
                        <div class="form-text">Max: ₹<?php echo $user['wallet_balance']; ?></div>
                    </div>

                    <!-- TDS Info -->
                    <div class="alert alert-light border mb-4 py-2">
                        <div class="d-flex justify-content-between small">
                            <span class="text-muted">TDS (2%):</span>
                            <span class="text-danger fw-bold" id="tdsDisplay">-₹0.00</span>
                        </div>
                        <div class="d-flex justify-content-between small mt-1 border-top pt-1">
                            <span class="text-dark fw-bold">Net Payout:</span>
                            <span class="text-success fw-bold" id="netDisplay">₹0.00</span>
                        </div>
                    </div>

                    <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">Payout To</h6>

                    <?php if (!empty($user['bank_details']['account_number'])): ?>
                        <div class="bg-light p-3 rounded border border-start-4 border-primary">
                            <div class="mb-2">
                                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Account Holder</small>
                                <div class="fw-bold text-dark"><?php echo $user['bank_details']['account_holder_name']; ?></div>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Bank Name</small>
                                <div class="text-dark"><?php echo $user['bank_details']['bank_name']; ?></div>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Account Number</small>
                                <div class="font-monospace text-dark fs-5"><?php echo $user['bank_details']['account_number']; ?></div>
                            </div>
                            <div>
                                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">IFSC Code</small>
                                <div class="font-monospace text-dark"><?php echo $user['bank_details']['ifsc_code']; ?></div>
                            </div>
                        </div>

                        <!-- Hidden inputs to pass data to controller -->
                        <input type="hidden" name="account_holder_name" value="<?php echo $user['bank_details']['account_holder_name']; ?>">
                        <input type="hidden" name="bank_name" value="<?php echo $user['bank_details']['bank_name']; ?>">
                        <input type="hidden" name="account_number" value="<?php echo $user['bank_details']['account_number']; ?>">
                        <input type="hidden" name="ifsc_code" value="<?php echo $user['bank_details']['ifsc_code']; ?>">

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary btn-lg shadow-sm" <?php echo ($user['wallet_balance'] <= 0) ? 'disabled' : ''; ?>>
                                Submit Request
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-3">
                            <i class="fas fa-university fa-2x text-warning mb-2"></i>
                            <p class="text-muted small">No bank details found.</p>
                            <?php if ($_SESSION['role_code'] === 'PARTNER_ADMIN'): ?>
                                <a href="<?php echo url('profile/index'); ?>" class="btn btn-sm btn-outline-primary">Update Profile</a>
                            <?php else: ?>
                                <a href="<?php echo url('profile/index'); ?>" class="btn btn-sm btn-outline-primary">Update Profile</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function calculateTDS() {
    const amount = parseFloat(document.getElementById('withdrawAmount').value) || 0;
    const tds = amount * 0.02;
    const net = amount - tds;

    document.getElementById('tdsDisplay').textContent = '-₹' + tds.toFixed(2);
    document.getElementById('netDisplay').textContent = '₹' + net.toFixed(2);
}
</script>
<?php endif; ?>

<?php
if ($_SESSION['role_code'] === 'PARTNER_ADMIN') {
    view('layouts/partner_footer');
} else {
    view('layouts/footer');
}
?>
