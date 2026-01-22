<?php view('layouts/header', ['title' => 'Contact Inquiries']); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark">Contact Inquiries</h2>
        <p class="text-muted">Manage leads and messages from contact forms.</p>
    </div>
</div>

<?php flash('inq_success'); ?>
<?php flash('inq_error'); ?>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <select class="form-select" id="statusFilter">
                    <option value="">All Statuses</option>
                    <option value="new">New</option>
                    <option value="read">Read</option>
                    <option value="replied">Replied</option>
                </select>
            </div>

            <?php if ($_SESSION['role_code'] === 'SUPER_ADMIN'): ?>
            <div class="col-md-3">
                <select class="form-select" id="sourceFilter">
                    <option value="">All Sources</option>
                    <option value="MAIN_SITE">Main Site</option>
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

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive" style="min-height: 300px;">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Date</th>
                        <th>Name</th>
                        <th>Subject</th>
                        <th>Source</th>
                        <th>Status</th>
                        <th class="pe-4 text-end">Action</th>
                    </tr>
                </thead>
                <tbody id="inquiriesTableBody">
                    <!-- Data loaded via AJAX -->
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="mt-2 text-muted">Loading inquiries...</p>
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

<!-- Status Modal Template -->
<div id="statusModalTemplate" style="display: none;">
    <div class="modal fade" id="statusModal__ID__" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fs-6">Update Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo url('inquiry/update_status/'); ?>__ID__" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Select Status</label>
                            <select class="form-select" name="status">
                                <option value="new" __NEW_SELECTED__>New</option>
                                <option value="read" __READ_SELECTED__>Read</option>
                                <option value="replied" __REPLIED_SELECTED__>Replied</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer p-2">
                        <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let currentPage = 1;

document.addEventListener('DOMContentLoaded', function() {
    loadInquiries();

    document.getElementById('statusFilter').addEventListener('change', function() {
        currentPage = 1;
        loadInquiries();
    });

    if (document.getElementById('sourceFilter')) {
        document.getElementById('sourceFilter').addEventListener('change', function() {
            currentPage = 1;
            loadInquiries();
        });
    }
});

function resetFilters() {
    document.getElementById('statusFilter').value = '';
    if (document.getElementById('sourceFilter')) document.getElementById('sourceFilter').value = '';
    currentPage = 1;
    loadInquiries();
}

function loadInquiries() {
    const status = document.getElementById('statusFilter').value;
    const source = document.getElementById('sourceFilter') ? document.getElementById('sourceFilter').value : '';
    const tbody = document.getElementById('inquiriesTableBody');

    fetch(`<?php echo url('inquiry/index'); ?>?ajax=1&page=${currentPage}&status=${status}&source=${source}`)
        .then(response => response.json())
        .then(data => {
            renderTable(data.inquiries);
            renderPagination(data.pagination);
        })
        .catch(error => {
            console.error('Error:', error);
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger py-4">Error loading data.</td></tr>';
        });
}

function renderTable(inquiries) {
    const tbody = document.getElementById('inquiriesTableBody');
    tbody.innerHTML = '';

    if (inquiries.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-5 text-muted"><i class="fas fa-inbox fa-3x mb-3 opacity-25"></i><p class="mb-0">No inquiries found.</p></td></tr>';
        return;
    }

    inquiries.forEach(inq => {
        const date = new Date(inq.created_at).toLocaleString('en-GB', { day: 'numeric', month: 'short', year: 'numeric', hour: 'numeric', minute: 'numeric', hour12: true });
        const subject = inq.subject.length > 50 ? inq.subject.substring(0, 50) + '...' : inq.subject;
        const sourceBadge = inq.wl_name
            ? `<span class="badge bg-info text-dark">${inq.wl_name}</span>`
            : '<span class="badge bg-secondary">Main Site</span>';

        let statusBadge = '';
        if (inq.status === 'new') statusBadge = '<span class="badge bg-primary">New</span>';
        else if (inq.status === 'read') statusBadge = '<span class="badge bg-light text-dark border">Read</span>';
        else statusBadge = '<span class="badge bg-success">Replied</span>';

        const rowClass = inq.status === 'new' ? 'fw-bold bg-light-subtle' : '';

        const row = `
            <tr class="${rowClass}">
                <td class="ps-4 text-muted small">${date}</td>
                <td>
                    ${inq.name}
                    <div class="small text-muted">${inq.email}</div>
                </td>
                <td>${subject}</td>
                <td>${sourceBadge}</td>
                <td>${statusBadge}</td>
                <td class="pe-4 text-end">
                    <button class="btn btn-sm btn-outline-secondary me-1" onclick="openStatusModal('${inq.id}', '${inq.status}')" title="Update Status">
                        <i class="fas fa-edit"></i>
                    </button>
                    <a href="<?php echo url('inquiry/view/'); ?>${inq.id}" class="btn btn-sm btn-outline-primary me-1" title="View Details"><i class="fas fa-eye"></i></a>
                    <a href="<?php echo url('inquiry/delete/'); ?>${inq.id}" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?');" title="Delete"><i class="fas fa-trash"></i></a>
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
    loadInquiries();
}

function openStatusModal(id, currentStatus) {
    const existingModal = document.getElementById('dynamicStatusModal');
    if (existingModal) existingModal.remove();

    let html = document.getElementById('statusModalTemplate').innerHTML;
    html = html.replace(/__ID__/g, id).replace('statusModal__ID__', 'dynamicStatusModal');

    html = html.replace('__NEW_SELECTED__', currentStatus === 'new' ? 'selected' : '');
    html = html.replace('__READ_SELECTED__', currentStatus === 'read' ? 'selected' : '');
    html = html.replace('__REPLIED_SELECTED__', currentStatus === 'replied' ? 'selected' : '');

    const div = document.createElement('div');
    div.innerHTML = html;
    document.body.appendChild(div);

    const modal = new bootstrap.Modal(document.getElementById('dynamicStatusModal'));
    modal.show();
}
</script>

<?php view('layouts/footer'); ?>
