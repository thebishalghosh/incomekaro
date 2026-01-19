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
                <tbody>
                    <?php if (!empty($policies)): ?>
                        <?php foreach ($policies as $policy): ?>
                            <tr>
                                <td class="ps-4 fw-bold"><?php echo $policy['name']; ?></td>
                                <td><span class="badge bg-secondary-subtle text-secondary border"><?php echo $policy['type']; ?></span></td>
                                <td>
                                    <a href="<?php echo asset($policy['file_url']); ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-file-pdf me-1"></i> View PDF
                                    </a>
                                </td>
                                <td><?php echo date('d M Y', strtotime($policy['created_at'])); ?></td>
                                <td class="pe-4 text-end">
                                    <a href="<?php echo url('policy/delete/' . $policy['id']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">No policies found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
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

<?php view('layouts/footer'); ?>
