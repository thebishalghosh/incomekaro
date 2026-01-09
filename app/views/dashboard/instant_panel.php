<?php view('layouts/header', ['title' => 'Instant Panels']); ?>

<div class="row mb-4">
    <div class="col-md-6">
        <h1>Instant Panels</h1>
        <p class="text-muted">Manage external service panels grouped by type.</p>
    </div>
    <div class="col-md-6 text-end">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPanelModal">
            <i class="fas fa-plus"></i> Add New Panel
        </button>
        <a href="<?php echo url('dashboard/super_admin'); ?>" class="btn btn-secondary">Back to Dashboard</a>
    </div>
</div>

<?php flash('panel_success'); ?>
<?php flash('panel_error'); ?>

<!-- Debug Output -->
<?php // var_dump($panels_by_type); ?>

<?php if (!empty($panels_by_type)): ?>
    <div class="accordion" id="panelsAccordion">
        <?php $i = 0; foreach ($panels_by_type as $type => $panels): $i++; ?>
            <div class="accordion-item border-0 shadow-sm mb-3">
                <h2 class="accordion-header" id="heading<?php echo $i; ?>">
                    <button class="accordion-button <?php echo $i > 1 ? 'collapsed' : ''; ?> fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $i; ?>" aria-expanded="<?php echo $i === 1 ? 'true' : 'false'; ?>" aria-controls="collapse<?php echo $i; ?>">
                        <?php echo $type; ?> <span class="badge bg-secondary ms-2"><?php echo count($panels); ?></span>
                    </button>
                </h2>
                <div id="collapse<?php echo $i; ?>" class="accordion-collapse collapse <?php echo $i === 1 ? 'show' : ''; ?>" aria-labelledby="heading<?php echo $i; ?>" data-bs-parent="#panelsAccordion">
                    <div class="accordion-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">Image</th>
                                        <th>Name</th>
                                        <th>URL</th>
                                        <th class="pe-4 text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($panels as $panel): ?>
                                        <tr>
                                            <td class="ps-4">
                                                <?php if (!empty($panel['image_url'])): ?>
                                                    <img src="<?php echo asset($panel['image_url']); ?>"
                                                         alt="Icon"
                                                         style="width: 40px; height: 40px; object-fit: contain;"
                                                         onerror="this.onerror=null; this.src='https://via.placeholder.com/40?text=Icon'; this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                    <div class="avatar-placeholder bg-light text-primary rounded-circle align-items-center justify-content-center" style="width: 40px; height: 40px; display: none;">
                                                        <i class="fas fa-link"></i>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="avatar-placeholder bg-light text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <i class="fas fa-link"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="fw-bold"><?php echo $panel['name']; ?></td>
                                            <td><a href="<?php echo $panel['url']; ?>" target="_blank" class="text-truncate d-inline-block" style="max-width: 300px;"><?php echo $panel['url']; ?></a></td>
                                            <td class="pe-4 text-end">
                                                <button class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#editPanelModal<?php echo $panel['id']; ?>"><i class="fas fa-edit"></i></button>
                                                <a href="<?php echo url('instant_panel/delete/' . $panel['id']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');"><i class="fas fa-trash"></i></a>

                                                <!-- Edit Modal -->
                                                <div class="modal fade" id="editPanelModal<?php echo $panel['id']; ?>" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Edit Panel</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <form action="<?php echo url('instant_panel/update/' . $panel['id']); ?>" method="POST" enctype="multipart/form-data">
                                                                <div class="modal-body">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Panel Name</label>
                                                                        <input type="text" class="form-control" name="name" value="<?php echo $panel['name']; ?>" required>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Type</label>
                                                                        <select class="form-select" name="type" required>
                                                                            <?php
                                                                                $types = ['Bank Management', 'Credit Card Management', 'Account open', 'Insurance', 'Policy Management', 'Credit Score', 'Bank Pincode'];
                                                                                foreach($types as $t) {
                                                                                    $sel = ($panel['panel_type'] == $t) ? 'selected' : '';
                                                                                    echo "<option value='$t' $sel>$t</option>";
                                                                                }
                                                                            ?>
                                                                        </select>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">URL</label>
                                                                        <input type="url" class="form-control" name="url" value="<?php echo $panel['url']; ?>" required>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Image (Optional)</label>
                                                                        <input type="file" class="form-control" name="image">
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                    <button type="submit" class="btn btn-primary">Update Panel</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="card shadow-sm border-0">
        <div class="card-body text-center py-5">
            <div class="text-muted mb-3">
                <i class="fas fa-layer-group fa-3x opacity-25"></i>
            </div>
            <h5 class="text-muted">No Instant Panels Found</h5>
            <p class="text-muted small">Click "Add New Panel" to get started.</p>
        </div>
    </div>
<?php endif; ?>

<!-- Add Modal -->
<div class="modal fade" id="addPanelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Instant Panel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo url('instant_panel/store'); ?>" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Panel Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <select class="form-select" name="type" required>
                            <option value="">Select Type</option>
                            <option value="Bank Management">Bank Management</option>
                            <option value="Credit Card Management">Credit Card Management</option>
                            <option value="Account open">Account open</option>
                            <option value="Insurance">Insurance</option>
                            <option value="Policy Management">Policy Management</option>
                            <option value="Credit Score">Credit Score</option>
                            <option value="Bank Pincode">Bank Pincode</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">URL</label>
                        <input type="url" class="form-control" name="url" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Image</label>
                        <input type="file" class="form-control" name="image">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Panel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php view('layouts/footer'); ?>
