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
                    <tbody id="withdrawalsTableBody">
                        <!-- Data loaded via AJAX -->
                        <tr>
                            <td colspan="<?php echo ($_SESSION['role_code'] === 'SUPER_ADMIN' || $_SESSION['role_code'] === 'WHITE_LABEL') ? '6' : '5'; ?>" class="text-center py-5">
                                <div class="spinner-border text-primary" role="status"></div>
                                <p class="mt-2 text-muted">Loading withdrawals...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-top-0 py-3">
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-end mb-0" id="pagination">
                    <!-- Pagination links -->
                </ul>
            </nav>
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

<script>
let currentPage = 1;
const roleCode = '<?php echo $_SESSION['role_code']; ?>';

document.addEventListener('DOMContentLoaded', function() {
    loadWithdrawals();
});

function loadWithdrawals() {
    const tbody = document.getElementById('withdrawalsTableBody');

    fetch(`<?php echo url('withdrawal/index'); ?>?ajax=1&page=${currentPage}`)
        .then(response => response.json())
        .then(data => {
            renderTable(data.withdrawals);
            renderPagination(data.pagination);
        })
        .catch(error => {
            console.error('Error:', error);
            tbody.innerHTML = '<tr><td colspan="' + (roleCode === 'SUPER_ADMIN' || roleCode === 'WHITE_LABEL' ? 6 : 5) + '" class="text-center text-danger py-4">Error loading data.</td></tr>';
        });
}

function renderTable(withdrawals) {
    const tbody = document.getElementById('withdrawalsTableBody');
    tbody.innerHTML = '';

    if (withdrawals.length === 0) {
        const colSpan = (roleCode === 'SUPER_ADMIN' || roleCode === 'WHITE_LABEL') ? 6 : 5;
        tbody.innerHTML = `
            <tr>
                <td colspan="${colSpan}" class="text-center py-5">
                    <div class="py-5">
                        <div class="icon-box bg-light text-muted rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="fas fa-wallet fa-3x opacity-50"></i>
                        </div>
                        <h5 class="fw-bold text-dark">No Withdrawal History</h5>
                        <p class="text-muted mb-4">No withdrawal requests found.</p>
                        ${(roleCode !== 'SUPER_ADMIN' && roleCode !== 'WHITE_LABEL') ? '<button type="button" class="btn btn-primary px-4 rounded-pill" data-bs-toggle="modal" data-bs-target="#requestModal">Request Now</button>' : ''}
                    </div>
                </td>
            </tr>
        `;
        return;
    }

    withdrawals.forEach(wd => {
        const date = new Date(wd.created_at).toLocaleString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
        const time = new Date(wd.created_at).toLocaleString('en-GB', { hour: 'numeric', minute: 'numeric', hour12: true });

        let userColumn = '';
        if (roleCode === 'SUPER_ADMIN' || roleCode === 'WHITE_LABEL') {
            const initial = wd.first_name.charAt(0).toUpperCase();
            const wlBadge = (wd.white_label_id && roleCode === 'SUPER_ADMIN') ? '<span class="badge bg-light text-secondary border mt-1" style="font-size: 0.65rem;">WL User</span>' : '';

            userColumn = `
                <td>
                    <div class="d-flex align-items-center">
                        <div class="avatar-placeholder bg-light text-primary rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px; font-size: 0.8rem;">
                            ${initial}
                        </div>
                        <div>
                            <div class="fw-bold text-dark">${wd.first_name} ${wd.last_name}</div>
                            <div class="small text-muted">${wd.email}</div>
                            ${wlBadge}
                        </div>
                    </div>
                </td>
            `;
        }

        let statusClass = 'bg-secondary-subtle text-secondary';
        let icon = 'fa-circle';
        if (wd.status == 'approved') { statusClass = 'bg-info-subtle text-info fw-bold'; icon = 'fa-check-circle'; }
        else if (wd.status == 'paid') { statusClass = 'bg-success-subtle text-success fw-bold'; icon = 'fa-check-double'; }
        else if (wd.status == 'rejected') { statusClass = 'bg-danger-subtle text-danger fw-bold'; icon = 'fa-times-circle'; }
        else if (wd.status == 'requested') { statusClass = 'bg-warning-subtle text-warning fw-bold'; icon = 'fa-clock'; }

        let actionsColumn = '';
        if (roleCode === 'SUPER_ADMIN' || roleCode === 'WHITE_LABEL') {
            let showActions = true;
            if (roleCode === 'SUPER_ADMIN' && wd.white_label_id && wd.role_code === 'PARTNER_ADMIN') {
                showActions = false;
            }

            let buttons = '';
            if (showActions) {
                if (wd.status == 'requested') {
                    buttons = `
                        <a href="<?php echo url('withdrawal/approve/'); ?>${wd.id}" class="btn btn-sm btn-success me-1 shadow-sm" onclick="return confirm('Approve this request?');" title="Approve"><i class="fas fa-check"></i></a>
                        <a href="<?php echo url('withdrawal/reject/'); ?>${wd.id}" class="btn btn-sm btn-danger shadow-sm" onclick="return confirm('Reject and refund?');" title="Reject"><i class="fas fa-times"></i></a>
                    `;
                } else if (wd.status == 'approved') {
                    buttons = `<a href="<?php echo url('withdrawal/mark_paid/'); ?>${wd.id}" class="btn btn-sm btn-primary shadow-sm" onclick="return confirm('Mark as Paid?');">Mark Paid</a>`;
                }
            } else {
                buttons = '<span class="text-muted small fst-italic">Managed by WL</span>';
            }
            actionsColumn = `<td class="text-end pe-4">${buttons}</td>`;
        }

        const row = `
            <tr>
                <td class="ps-4">
                    <div class="fw-bold text-dark">${date}</div>
                    <div class="small text-muted">${time}</div>
                </td>
                ${userColumn}
                <td>
                    <div class="d-flex flex-column">
                        <span class="fw-bold text-success fs-6">Net: ₹${parseFloat(wd.net_amount).toLocaleString('en-IN', { minimumFractionDigits: 2 })}</span>
                        <span class="small text-muted">Gross: ₹${parseFloat(wd.gross_amount).toLocaleString('en-IN', { minimumFractionDigits: 2 })}</span>
                        <span class="small text-danger" style="font-size: 0.7rem;">TDS (2%): -₹${parseFloat(wd.tds_amount).toLocaleString('en-IN', { minimumFractionDigits: 2 })}</span>
                    </div>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-light text-secondary rounded me-2 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                            <i class="fas fa-university"></i>
                        </div>
                        <div class="small">
                            <div class="fw-bold text-dark">${wd.bank_name}</div>
                            <div class="text-muted">AC: ${wd.bank_account_number}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="badge ${statusClass} px-3 py-2 rounded-pill border border-0">
                        <i class="fas ${icon} me-1"></i> ${wd.status.charAt(0).toUpperCase() + wd.status.slice(1)}
                    </span>
                </td>
                ${actionsColumn}
            </tr>
        `;
        tbody.innerHTML += row;
    });
}

function renderPagination(pagination) {
    const ul = document.getElementById('pagination');
    ul.innerHTML = '';

    if (pagination.total_pages <= 1) return;

    // Previous
    ul.innerHTML += `
        <li class="page-item ${pagination.current_page === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="changePage(${pagination.current_page - 1}); return false;">Previous</a>
        </li>
    `;

    // Pages
    for (let i = 1; i <= pagination.total_pages; i++) {
        if (i === 1 || i === pagination.total_pages || (i >= pagination.current_page - 2 && i <= pagination.current_page + 2)) {
             ul.innerHTML += `
                <li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="changePage(${i}); return false;">${i}</a>
                </li>
            `;
        } else if (i === pagination.current_page - 3 || i === pagination.current_page + 3) {
            ul.innerHTML += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
    }

    // Next
    ul.innerHTML += `
        <li class="page-item ${pagination.current_page === pagination.total_pages ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="changePage(${pagination.current_page + 1}); return false;">Next</a>
        </li>
    `;
}

function changePage(page) {
    if (page < 1) return;
    currentPage = page;
    loadWithdrawals();
}
</script>

<?php
if ($_SESSION['role_code'] === 'PARTNER_ADMIN') {
    view('layouts/partner_footer');
} else {
    view('layouts/footer');
}
?>
