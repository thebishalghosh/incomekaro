<?php view('layouts/header', ['title' => 'Bank Management']); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark">Bank Management</h2>
        <p class="text-muted">Manage serviceable banks and pincodes.</p>
    </div>
    <div>
        <a href="<?php echo url('bank/clear'); ?>" class="btn btn-outline-danger me-2" onclick="return confirm('Are you sure? This will delete ALL bank data.');">
            <i class="fas fa-trash me-2"></i> Clear Data
        </a>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="fas fa-file-upload me-2"></i> Import CSV
        </button>
    </div>
</div>

<?php flash('bank_success'); ?>
<?php flash('bank_error'); ?>

<div class="alert alert-warning border-0 shadow-sm rounded-3 mb-4">
    <div class="d-flex">
        <div class="me-3">
            <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
        </div>
        <div>
            <h6 class="fw-bold mb-1">Important Note</h6>
            <p class="mb-0 small">Please ensure you <strong>Clear Data</strong> before importing a new CSV file to avoid duplicate entries and data inconsistencies. The system will attempt to merge data, but a fresh import is recommended for accuracy.</p>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">Test Pincode Search</h5>
                <div class="input-group">
                    <input type="text" class="form-control" id="testPincode" placeholder="Enter Pincode">
                    <button class="btn btn-primary" type="button" onclick="searchBanks()">Search</button>
                </div>
                <div id="searchResults" class="mt-3"></div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-light">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">CSV Format Guide</h5>
                <p class="small text-muted">Upload a CSV file with the following columns (headers are optional but recommended):</p>
                <div class="bg-white p-3 rounded border font-monospace small">
                    Pincode, Bank Name<br>
                    700001, HDFC Bank<br>
                    700001, SBI<br>
                    110001, ICICI Bank
                </div>
                <p class="small text-muted mt-2 mb-0"><i class="fas fa-info-circle me-1"></i> Column 1: Pincode, Column 2: Bank Name</p>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0">Registered Banks</h5>
        <div class="input-group w-auto">
            <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
            <input type="text" class="form-control border-start-0 ps-0" id="bankSearch" placeholder="Search banks...">
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive" style="max-height: 500px;">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light sticky-top">
                    <tr>
                        <th class="ps-4">Bank Name</th>
                        <th>ID</th>
                    </tr>
                </thead>
                <tbody id="banksTableBody">
                    <!-- Data loaded via AJAX -->
                    <tr>
                        <td colspan="2" class="text-center py-5">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="mt-2 text-muted">Loading banks...</p>
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

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Banks & Pincodes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo url('bank/import'); ?>" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select CSV File</label>
                        <input type="file" class="form-control" name="csv_file" accept=".csv" required>
                    </div>
                    <div class="alert alert-info small">
                        <i class="fas fa-info-circle me-1"></i> Large files may take some time to process.
                    </div>
                    <div class="alert alert-secondary small">
                        <strong>Format:</strong> Pincode (Col 1), Bank Name (Col 2)
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentPage = 1;
let searchTimeout;

document.addEventListener('DOMContentLoaded', function() {
    loadBanks();

    document.getElementById('bankSearch').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            currentPage = 1;
            loadBanks();
        }, 500);
    });
});

function loadBanks() {
    const search = document.getElementById('bankSearch').value;
    const tbody = document.getElementById('banksTableBody');

    fetch(`<?php echo url('bank/index'); ?>?ajax=1&page=${currentPage}&search=${encodeURIComponent(search)}`)
        .then(response => response.json())
        .then(data => {
            renderTable(data.banks);
            renderPagination(data.pagination);
        })
        .catch(error => {
            console.error('Error:', error);
            tbody.innerHTML = '<tr><td colspan="2" class="text-center text-danger py-4">Error loading data.</td></tr>';
        });
}

function renderTable(banks) {
    const tbody = document.getElementById('banksTableBody');
    tbody.innerHTML = '';

    if (banks.length === 0) {
        tbody.innerHTML = '<tr><td colspan="2" class="text-center py-5 text-muted">No banks found. Import a CSV file.</td></tr>';
        return;
    }

    banks.forEach(bank => {
        const row = `
            <tr>
                <td class="ps-4 fw-bold">${bank.name}</td>
                <td class="text-muted small">${bank.id}</td>
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
    loadBanks();
}

function searchBanks() {
    const pincode = document.getElementById('testPincode').value;
    const resultsDiv = document.getElementById('searchResults');

    if (!pincode) return;

    resultsDiv.innerHTML = '<div class="spinner-border spinner-border-sm text-primary" role="status"></div> Searching...';

    fetch('<?php echo url('bank/search'); ?>?pincode=' + pincode)
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                let html = '<ul class="list-group list-group-flush">';
                data.forEach(bank => {
                    html += `<li class="list-group-item"><i class="fas fa-check-circle text-success me-2"></i> ${bank}</li>`;
                });
                html += '</ul>';
                resultsDiv.innerHTML = html;
            } else {
                resultsDiv.innerHTML = '<div class="text-danger"><i class="fas fa-times-circle me-1"></i> No banks found for this pincode.</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            resultsDiv.innerHTML = '<div class="text-danger">Error searching banks.</div>';
        });
}
</script>

<?php view('layouts/footer'); ?>
