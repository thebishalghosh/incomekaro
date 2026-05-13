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

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" class="form-control border-start-0 ps-0" id="partnerSearch" placeholder="Search by name, email, or phone...">
                </div>
            </div>

            <?php if ($_SESSION['role_code'] === 'SUPER_ADMIN'): ?>
            <div class="col-md-3">
                <select class="form-select" id="typeFilter">
                    <option value="">All Types</option>
                    <option value="PLATFORM">Platform Partners</option>
                    <option value="WHITE_LABEL">White Label Partners</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="wlFilter" disabled>
                    <option value="">All White Labels</option>
                    <?php if (!empty($white_labels)): ?>
                        <?php foreach ($white_labels as $wl): ?>
                            <option value="<?php echo $wl['id']; ?>"><?php echo $wl['company_name']; ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <?php endif; ?>

            <div class="col-md-2">
                <button class="btn btn-outline-secondary w-100" onclick="resetFilters()">Reset</button>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive" style="min-height: 300px;">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Name</th>
                        <th>Contact</th>
                        <th>Type</th>
                        <?php if ($_SESSION['role_code'] === 'SUPER_ADMIN'): ?>
                            <th>White Label</th>
                        <?php endif; ?>
                        <th>Wallet</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody id="partnersTableBody">
                    <!-- Data loaded via AJAX -->
                    <tr>
                        <td colspan="<?php echo ($_SESSION['role_code'] === 'SUPER_ADMIN') ? '7' : '6'; ?>" class="text-center py-5">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="mt-2 text-muted">Loading partners...</p>
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

<!-- Revoke Modal Template -->
<div id="revokeModalTemplate" style="display: none;">
    <div class="modal fade" id="revokeModal__ID__" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Revoke Access</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo url('partner/update_status/'); ?>__ID__" method="POST">
                    <div class="modal-body">
                        <p>Are you sure you want to revoke access for <strong>__NAME__</strong>?</p>
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
</div>

<!-- Wallet Modal Template -->
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
const roleCode = '<?php echo $_SESSION['role_code']; ?>';

document.addEventListener('DOMContentLoaded', function() {
    loadPartners();

    document.getElementById('partnerSearch').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            currentPage = 1;
            loadPartners();
        }, 500);
    });

    if (document.getElementById('typeFilter')) {
        document.getElementById('typeFilter').addEventListener('change', function() {
            const wlFilter = document.getElementById('wlFilter');
            if (this.value === 'WHITE_LABEL') {
                wlFilter.disabled = false;
            } else {
                wlFilter.disabled = true;
                wlFilter.value = ''; // Reset WL filter if type is not WL
            }
            currentPage = 1;
            loadPartners();
        });
    }

    if (document.getElementById('wlFilter')) {
        document.getElementById('wlFilter').addEventListener('change', function() {
            currentPage = 1;
            loadPartners();
        });
    }
});

function resetFilters() {
    document.getElementById('partnerSearch').value = '';
    if (document.getElementById('typeFilter')) {
        document.getElementById('typeFilter').value = '';
        document.getElementById('wlFilter').disabled = true;
        document.getElementById('wlFilter').value = '';
    }
    currentPage = 1;
    loadPartners();
}

function loadPartners() {
    const search = document.getElementById('partnerSearch').value;
    const type = document.getElementById('typeFilter') ? document.getElementById('typeFilter').value : '';
    const wl = document.getElementById('wlFilter') ? document.getElementById('wlFilter').value : '';
    const tbody = document.getElementById('partnersTableBody');

    fetch(`<?php echo url('partner/index'); ?>?ajax=1&page=${currentPage}&search=${encodeURIComponent(search)}&type=${type}&wl=${wl}`)
        .then(response => response.json())
        .then(data => {
            renderTable(data.partners);
            renderPagination(data.pagination);
        })
        .catch(error => {
            console.error('Error:', error);
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger py-4">Error loading data.</td></tr>';
        });
}

function renderTable(partners) {
    const tbody = document.getElementById('partnersTableBody');
    tbody.innerHTML = '';

    if (partners.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center py-5 text-muted">No partners found.</td></tr>';
        return;
    }

    partners.forEach(partner => {
        const fullName = partner.full_name || partner.name;
        const initial = fullName.charAt(0).toUpperCase();
        const avatar = partner.profile_image
            ? `<img src="<?php echo asset(''); ?>${partner.profile_image}" alt="Avatar" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">`
            : `<div class="avatar-placeholder bg-light text-primary rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">${initial}</div>`;

        const typeBadge = partner.partner_type === 'PLATFORM'
            ? '<span class="badge bg-primary">Platform</span>'
            : '<span class="badge bg-info text-dark">White Label</span>';

        const wlColumn = roleCode === 'SUPER_ADMIN'
            ? `<td>${partner.white_label_name || '-'}</td>`
            : '';

        const walletBalance = parseFloat(partner.wallet_balance || 0).toLocaleString('en-IN', { minimumFractionDigits: 2 });

        // Status Logic
        let statusHtml = '';
        if (roleCode === 'SUPER_ADMIN' || roleCode === 'WHITE_LABEL') {
            const btnClass = partner.status === 'active' ? 'btn-success' : 'btn-danger';
            const icon = partner.status === 'active' ? '<i class="fas fa-check-circle me-1"></i> Active' : '<i class="fas fa-ban me-1"></i> Inactive';

            statusHtml = `
                <div class="dropdown">
                    <button class="btn btn-sm ${btnClass} dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        ${icon}
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <form action="<?php echo url('partner/update_status/'); ?>${partner.id}" method="POST">
                                <input type="hidden" name="status" value="active">
                                <button type="submit" class="dropdown-item text-success"><i class="fas fa-check-circle me-2"></i>Active</button>
                            </form>
                        </li>
                        <li>
                            <button type="button" class="dropdown-item text-danger" onclick="openRevokeModal('${partner.id}', '${fullName}')">
                                <i class="fas fa-ban me-2"></i>Revoke Access
                            </button>
                        </li>
                    </ul>
                </div>
            `;
        } else {
            statusHtml = partner.status === 'active'
                ? '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Active</span>'
                : '<span class="badge bg-danger"><i class="fas fa-ban me-1"></i> Inactive</span>';
        }

        // Action Buttons
        let showWallet = false;
        if (roleCode === 'WHITE_LABEL') showWallet = true;
        if (roleCode === 'SUPER_ADMIN' && partner.partner_type === 'PLATFORM') showWallet = true;

        let walletBtn = '';
        if (showWallet && partner.user_id) {
            walletBtn = `<button class="btn btn-sm btn-warning text-dark me-1" onclick="openWalletModal('${partner.user_id}', '${fullName}', ${partner.wallet_balance || 0}, ${partner.has_bank_details}, '${partner.account_holder_name || ''}', '${partner.bank_name || ''}', '${partner.account_number || ''}', '${partner.ifsc_code || ''}', '${partner.id}')" title="Manage Wallet"><i class="fas fa-wallet"></i></button>`;
        }

        let editBtn = roleCode !== 'RM' ? `<a href="<?php echo url('partner/edit/'); ?>${partner.id}" class="btn btn-sm btn-info text-white me-1"><i class="fas fa-edit"></i></a>` : '';
        let deleteBtn = (roleCode === 'SUPER_ADMIN' || roleCode === 'WHITE_LABEL') ? `<a href="<?php echo url('partner/delete/'); ?>${partner.id}" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');"><i class="fas fa-trash"></i></a>` : '';

        const row = `
            <tr>
                <td class="ps-4">
                    <div class="d-flex align-items-center">
                        ${avatar}
                        <div>
                            <div class="fw-bold text-dark">${fullName}</div>
                            <div class="small text-muted">ID: ${partner.id}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <div><i class="fas fa-envelope me-1 text-muted"></i> ${partner.email}</div>
                    <div><i class="fas fa-phone me-1 text-muted"></i> ${partner.mobile || partner.phone}</div>
                </td>
                <td>${typeBadge}</td>
                ${wlColumn}
                <td><span class="fw-bold text-success">₹${walletBalance}</span></td>
                <td>${statusHtml}</td>
                <td class="text-end pe-4">
                    ${walletBtn}
                    <a href="<?php echo url('partner/profile/'); ?>${partner.id}" class="btn btn-sm btn-light me-1"><i class="fas fa-eye"></i></a>
                    ${editBtn}
                    ${deleteBtn}
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
    loadPartners();
}

function openRevokeModal(id, name) {
    const existingModal = document.getElementById('dynamicRevokeModal');
    if (existingModal) existingModal.remove();

    let html = document.getElementById('revokeModalTemplate').innerHTML;

    // Fix: Replace ID first, then the rest
    html = html.replace('revokeModal__ID__', 'dynamicRevokeModal');
    html = html.replace(/__ID__/g, id);
    html = html.replace(/__NAME__/g, name);

    const div = document.createElement('div');
    div.innerHTML = html;
    document.body.appendChild(div);

    const modal = new bootstrap.Modal(document.getElementById('dynamicRevokeModal'));
    modal.show();
}

function openWalletModal(userId, userName, balance, hasBank, holder, bank, acc, ifsc, partnerId) {
    const existingModal = document.getElementById('dynamicWalletModal');
    if (existingModal) existingModal.remove();

    let modalHtml = document.getElementById('walletModalTemplate').innerHTML;
    modalHtml = modalHtml.replace('walletModal__ID__', 'dynamicWalletModal');
    modalHtml = modalHtml.replace('__NAME__', userName);

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
                <p class="text-muted mb-4 px-4">You cannot add balance to this partner because their bank details are not updated. Please ask them to update their profile.</p>
                <a href="<?php echo url('partner/edit/'); ?>${partnerId}" class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm">
                    <i class="fas fa-edit me-2"></i> Update Profile
                </a>
            </div>
        `;
    }

    const modal = new bootstrap.Modal(document.getElementById('dynamicWalletModal'));
    modal.show();
}
</script>

<?php view('layouts/footer'); ?>
