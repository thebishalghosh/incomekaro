<?php view('layouts/header', ['title' => 'Contact Inquiries']); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark">Contact Inquiries</h2>
        <p class="text-muted">Manage leads and messages from contact forms.</p>
    </div>
</div>

<?php flash('inq_success'); ?>
<?php flash('inq_error'); ?>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
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
                <tbody>
                    <?php if (!empty($inquiries)): ?>
                        <?php foreach ($inquiries as $inq): ?>
                            <tr class="<?php echo $inq['status'] == 'new' ? 'fw-bold bg-light-subtle' : ''; ?>">
                                <td class="ps-4 text-muted small"><?php echo date('d M Y, h:i A', strtotime($inq['created_at'])); ?></td>
                                <td>
                                    <?php echo $inq['name']; ?>
                                    <div class="small text-muted"><?php echo $inq['email']; ?></div>
                                </td>
                                <td><?php echo substr($inq['subject'], 0, 50) . (strlen($inq['subject']) > 50 ? '...' : ''); ?></td>
                                <td>
                                    <?php if ($inq['wl_name']): ?>
                                        <span class="badge bg-info text-dark"><?php echo $inq['wl_name']; ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Main Site</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($inq['status'] == 'new'): ?>
                                        <span class="badge bg-primary">New</span>
                                    <?php elseif ($inq['status'] == 'read'): ?>
                                        <span class="badge bg-light text-dark border">Read</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Replied</span>
                                    <?php endif; ?>
                                </td>
                                <td class="pe-4 text-end">
                                    <button class="btn btn-sm btn-outline-secondary me-1" data-bs-toggle="modal" data-bs-target="#statusModal<?php echo $inq['id']; ?>" title="Update Status">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="<?php echo url('inquiry/view/' . $inq['id']); ?>" class="btn btn-sm btn-outline-primary me-1" title="View Details"><i class="fas fa-eye"></i></a>
                                    <a href="<?php echo url('inquiry/delete/' . $inq['id']); ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?');" title="Delete"><i class="fas fa-trash"></i></a>

                                    <!-- Status Modal -->
                                    <div class="modal fade" id="statusModal<?php echo $inq['id']; ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-sm">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title fs-6">Update Status</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="<?php echo url('inquiry/update_status/' . $inq['id']); ?>" method="POST">
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label small fw-bold">Select Status</label>
                                                            <select class="form-select" name="status">
                                                                <option value="new" <?php echo $inq['status'] == 'new' ? 'selected' : ''; ?>>New</option>
                                                                <option value="read" <?php echo $inq['status'] == 'read' ? 'selected' : ''; ?>>Read</option>
                                                                <option value="replied" <?php echo $inq['status'] == 'replied' ? 'selected' : ''; ?>>Replied</option>
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
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
                                <p class="mb-0">No inquiries found.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php view('layouts/footer'); ?>
