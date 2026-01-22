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

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" class="form-control border-start-0 ps-0" id="userSearch" placeholder="Search by name, email, or phone...">
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="roleFilter">
                    <option value="">All Roles</option>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?php echo $role['id']; ?>"><?php echo $role['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-secondary w-100" onclick="resetFilters()">Reset</button>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Name</th>
                        <th>Role</th>
                        <th>Email</th>
                        <th>Wallet</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody id="usersTableBody">
                    <!-- Data will be loaded via AJAX -->
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="mt-2 text-muted">Loading users...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white border-top-0 py-3">
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-end mb-0" id="pagination">
                <!-- Pagination links will be generated here -->
            </ul>
        </nav>
    </div>
</div>

<!-- Wallet Modal Template (Hidden) -->
<div id="walletModalTemplate" style="display: none;">
    <div class="modal fade" id="walletModal__ID__" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg overflow-hidden">
                <div class="modal-header text-white border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-white bg-opacity-25 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-0">Manage Wallet</h5>
                            <small class="opacity-75">__NAME__</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body-content">
                    <!-- Content injected via JS -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentPage = 1;
let searchTimeout;

document.addEventListener('DOMContentLoaded', function() {
    loadUsers();

    document.getElementById('userSearch').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            currentPage = 1;
            loadUsers();
        }, 500);
    });

    document.getElementById('roleFilter').addEventListener('change', function() {
        currentPage = 1;
        loadUsers();
    });
});

function resetFilters() {
    document.getElementById('userSearch').value = '';
    document.getElementById('roleFilter').value = '';
    currentPage = 1;
    loadUsers();
}

function loadUsers() {
    const search = document.getElementById('userSearch').value;
    const role = document.getElementById('roleFilter').value;
    const tbody = document.getElementById('usersTableBody');
    const pagination = document.getElementById('pagination');

    fetch(`<?php echo url('user/index'); ?>?ajax=1&page=${currentPage}&search=${encodeURIComponent(search)}&role=${role}`)
        .then(response => response.json())
        .then(data => {
            renderTable(data.users);
            renderPagination(data.pagination);
        })
        .catch(error => {
            console.error('Error:', error);
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger py-4">Error loading data.</td></tr>';
        });
}

function renderTable(users) {
    const tbody = document.getElementById('usersTableBody');
    tbody.innerHTML = '';

    if (users.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-5 text-muted">No users found.</td></tr>';
        return;
    }

    users.forEach(user => {
        const wlName = user.wl_name ? `<div class="small text-muted"><i class="fas fa-building me-1"></i> ${user.wl_name}</div>` : '';
        const statusBadge = user.status === 'active'
            ? '<span class="badge bg-success">Active</span>'
            : '<span class="badge bg-danger">Inactive</span>';

        const walletBalance = parseFloat(user.wallet_balance || 0).toLocaleString('en-IN', { minimumFractionDigits: 2 });

        // Wallet Logic (Replicated from PHP)
        let showWallet = true;
        if (user.white_label_id && user.role_name.toLowerCase().includes('partner')) {
            showWallet = false;
        }

        let walletBtn = '';
        if (showWallet) {
            walletBtn = `<button class="btn btn-sm btn-warning text-dark me-1" onclick="openWalletModal('${user.id}', '${user.first_name} ${user.last_name}', ${user.wallet_balance || 0}, ${user.has_bank_details}, '${user.account_holder_name || ''}', '${user.bank_name || ''}', '${user.account_number || ''}', '${user.ifsc_code || ''}')" title="Manage Wallet"><i class="fas fa-wallet"></i></button>`;
        }

        const row = `
            <tr>
                <td class="fw-bold">
                    ${user.first_name} ${user.last_name}
                    ${wlName}
                </td>
                <td><span class="badge bg-info text-dark">${user.role_name}</span></td>
                <td>${user.email}</td>
                <td><span class="fw-bold text-success">₹${walletBalance}</span></td>
                <td>${statusBadge}</td>
                <td class="text-end pe-4">
                    ${walletBtn}
                    <a href="<?php echo url('user/edit/'); ?>${user.id}" class="btn btn-sm btn-info text-white me-1"><i class="fas fa-edit"></i></a>
                    <a href="<?php echo url('user/delete/'); ?>${user.id}" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');"><i class="fas fa-trash"></i></a>
                </td>
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
    loadUsers();
}

// Dynamic Wallet Modal Handling
function openWalletModal(userId, userName, balance, hasBank, holder, bank, acc, ifsc) {
    // Remove existing modal if any
    const existingModal = document.getElementById('dynamicWalletModal');
    if (existingModal) existingModal.remove();

    let modalHtml = document.getElementById('walletModalTemplate').innerHTML;
    modalHtml = modalHtml.replace('walletModal__ID__', 'dynamicWalletModal'); // Set ID
    modalHtml = modalHtml.replace('__NAME__', userName);

    // Create container
    const div = document.createElement('div');
    div.innerHTML = modalHtml;
    document.body.appendChild(div);

    const modalBody = div.querySelector('.modal-body-content');

    if (hasBank == 1) {
        modalBody.innerHTML = `
            <form action="<?php echo url('user/wallet_update/'); ?>${userId}" method="POST">
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6 border-end">
                            <div class="card bg-success-subtle border-0 mb-4">
                                <div class="card-body text-center p-3">
                                    <small class="text-uppercase fw-bold text-success opacity-75">Current Balance</small>
                                    <h2 class="fw-bold text-success mb-0">₹${balance.toLocaleString('en-IN', { minimumFractionDigits: 2 })}</h2>
                                </div>
                            </div>
                            <h6 class="fw-bold text-dark mb-3 d-flex align-items-center">
                                <i class="fas fa-university me-2" style="color: #667eea;"></i> Bank Details
                            </h6>
                            <div class="bg-light p-3 rounded-3 border border-start-4" style="border-left-color: #667eea !important;">
                                <div class="mb-2 pb-2 border-bottom">
                                    <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Account Holder</small>
                                    <div class="fw-bold text-dark">${holder}</div>
                                </div>
                                <div class="mb-2 pb-2 border-bottom">
                                    <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Bank Name</small>
                                    <div class="text-dark">${bank}</div>
                                </div>
                                <div class="mb-2 pb-2 border-bottom">
                                    <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Account Number</small>
                                    <div class="font-monospace text-dark fs-5">${acc}</div>
                                </div>
                                <div>
                                    <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">IFSC Code</small>
                                    <div class="font-monospace text-dark">${ifsc}</div>
                                </div>
                            </div>
                        </div>
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
        `;
    } else {
        modalBody.innerHTML = `
            <div class="modal-body text-center py-5">
                <div class="icon-box bg-warning-subtle text-warning rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                    <i class="fas fa-university fa-3x"></i>
                </div>
                <h4 class="fw-bold text-dark">Bank Details Missing</h4>
                <p class="text-muted mb-4 px-4">You cannot add balance to this user because their bank details are not updated. Please update the profile first.</p>
                <a href="<?php echo url('user/edit/'); ?>${userId}#bank" class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm">
                    <i class="fas fa-plus me-2"></i> Add Bank Details
                </a>
            </div>
        `;
    }

    const modal = new bootstrap.Modal(document.getElementById('dynamicWalletModal'));
    modal.show();
}
</script>

<?php view('layouts/footer'); ?>
