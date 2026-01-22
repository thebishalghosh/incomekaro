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
    <div class="card-header bg-white border-0 py-3">
        <h5 class="fw-bold mb-0">Registered Banks</h5>
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
                <tbody>
                    <?php if (!empty($banks)): ?>
                        <?php foreach ($banks as $bank): ?>
                            <tr>
                                <td class="ps-4 fw-bold"><?php echo $bank['name']; ?></td>
                                <td class="text-muted small"><?php echo $bank['id']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2" class="text-center py-5 text-muted">No banks found. Import a CSV file.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
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
