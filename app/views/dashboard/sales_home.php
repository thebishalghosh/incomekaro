<?php view('layouts/header', ['title' => 'Sales Dashboard']); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark">Sales Dashboard</h2>
        <p class="text-muted">Track your partner acquisitions.</p>
    </div>
    <div>
        <a href="<?php echo url('sales/create_partner'); ?>" class="btn btn-primary"><i class="fas fa-plus me-2"></i> Add New Partner</a>
    </div>
</div>

<?php flash('ptr_success'); ?>

<!-- Stats Row -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="stat-card purple">
            <div class="icon-box">
                <i class="fas fa-users"></i>
            </div>
            <h3><?php echo $stats['total_partners'] ?? 0; ?></h3>
            <p>Total Partners Created</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card green">
            <div class="icon-box">
                <i class="fas fa-calendar-check"></i>
            </div>
            <h3><?php echo $stats['this_month'] ?? 0; ?></h3>
            <p>Created This Month</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card blue">
            <div class="icon-box">
                <i class="fas fa-wallet"></i>
            </div>
            <h3>₹<?php echo number_format($user['wallet_balance'] ?? 0, 2); ?></h3>
            <p>Wallet Balance</p>
        </div>
    </div>
</div>

<!-- Recent Partners -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0">Recently Added Partners</h5>
        <a href="<?php echo url('sales/partners'); ?>" class="btn btn-sm btn-outline-secondary">View All</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Name</th>
                        <th>Contact</th>
                        <th>Date Added</th>
                        <th class="pe-4 text-end">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($partners)): ?>
                        <?php foreach ($partners as $partner): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-placeholder bg-light text-primary rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                            <?php echo strtoupper(substr($partner['full_name'] ?? $partner['name'], 0, 1)); ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark"><?php echo $partner['full_name'] ?? $partner['name']; ?></div>
                                            <div class="small text-muted">ID: <?php echo $partner['id']; ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div><i class="fas fa-envelope me-1 text-muted"></i> <?php echo $partner['email']; ?></div>
                                    <div><i class="fas fa-phone me-1 text-muted"></i> <?php echo $partner['mobile'] ?? $partner['phone']; ?></div>
                                </td>
                                <td><?php echo date('d M Y', strtotime($partner['created_at'])); ?></td>
                                <td class="pe-4 text-end">
                                    <?php if ($partner['status'] == 'active'): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Inactive</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">No partners added yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php view('layouts/footer'); ?>
