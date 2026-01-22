<?php view('layouts/header', ['title' => 'Policy Management']); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark">Policy Management</h2>
        <p class="text-muted">Upload and manage policy documents.</p>
    </div>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPolicyModal">
        <i class="fas fa-plus me-2"></i> Add New Policy
    </button>
</div>

<?php flash('policy_success'); ?>
<?php flash('policy_error'); ?>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <select class="form-select" id="typeFilter">
                    <option value="">All Types</option>
                    <option value="Bank">Bank</option>
                    <option value="Credit Card">Credit Card</option>
                    <option value="Account Opening">Account Opening</option>
                    <option value="Insurance">Insurance</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-secondary w-100" onclick="resetFilters()">Reset</button>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Policy Name</th>
                        <th>Type</th>
                        <th>File</th>
                        <th>Date Added</th>
                        <th class="pe-4 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="policiesTableBody">
                    <!-- Data loaded via AJAX -->
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="mt-2 text-muted">Loading policies...</p>
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

<!-- Add Modal -->
<div class="modal fade" id="addPolicyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Policy</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo url('policy/store'); ?>" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Policy Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <select class="form-select" name="type" required>
                            <option value="Bank">Bank</option>
                            <option value="Credit Card">Credit Card</option>
                            <option value="Account Opening">Account Opening</option>
                            <option value="Insurance">Insurance</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Policy Document (PDF)</label>
                        <input type="file" class="form-control" name="policy_file" accept="application/pdf" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Upload Policy</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentPage = 1;

document.addEventListener('DOMContentLoaded', function() {
    loadPolicies();

    document.getElementById('typeFilter').addEventListener('change', function() {
        currentPage = 1;
        loadPolicies();
    });
});

function resetFilters() {
    document.getElementById('typeFilter').value = '';
    currentPage = 1;
    loadPolicies();
}

function loadPolicies() {
    const type = document.getElementById('typeFilter').value;
    const tbody = document.getElementById('policiesTableBody');

    fetch(`<?php echo url('policy/index'); ?>?ajax=1&page=${currentPage}&type=${encodeURIComponent(type)}`)
        .then(response => response.json())
        .then(data => {
            renderTable(data.policies);
            renderPagination(data.pagination);
        })
        .catch(error => {
            console.error('Error:', error);
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger py-4">Error loading data.</td></tr>';
        });
}

function renderTable(policies) {
    const tbody = document.getElementById('policiesTableBody');
    tbody.innerHTML = '';

    if (policies.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center py-5 text-muted">No policies found.</td></tr>';
        return;
    }

    policies.forEach(policy => {
        const date = new Date(policy.created_at).toLocaleString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });

        const row = `
            <tr>
                <td class="ps-4 fw-bold">${policy.name}</td>
                <td><span class="badge bg-secondary-subtle text-secondary border">${policy.type}</span></td>
                <td>
                    <a href="<?php echo asset(''); ?>${policy.file_url}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-file-pdf me-1"></i> View PDF
                    </a>
                </td>
                <td>${date}</td>
                <td class="pe-4 text-end">
                    <a href="<?php echo url('policy/delete/'); ?>${policy.id}" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');">
                        <i class="fas fa-trash"></i>
                    </a>
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
    loadPolicies();
}
</script>

<?php view('layouts/footer'); ?>
