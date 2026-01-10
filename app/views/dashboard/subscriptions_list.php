<?php view('layouts/header', ['title' => 'Subscription Plans']); ?>

<div class="row mb-4">
    <div class="col-md-6">
        <h1>Subscription Plans</h1>
        <p class="text-muted">Manage pricing and service bundles.</p>
    </div>
    <div class="col-md-6 text-end">
        <a href="<?php echo url('subscription/create'); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create New Plan
        </a>
        <a href="<?php echo url('dashboard/super_admin'); ?>" class="btn btn-secondary">Back to Dashboard</a>
    </div>
</div>

<?php flash('sub_success'); ?>
<?php flash('sub_error'); ?>

<?php if ($_SESSION['role_code'] === 'SUPER_ADMIN'): ?>
    <ul class="nav nav-tabs mb-4" id="planTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold" id="partner-tab" data-bs-toggle="tab" data-bs-target="#partner-plans" type="button" role="tab">
                <i class="fas fa-handshake me-2"></i> Partner Plans
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold" id="wl-tab" data-bs-toggle="tab" data-bs-target="#wl-plans" type="button" role="tab">
                <i class="fas fa-building me-2"></i> White Label Plans
            </button>
        </li>
    </ul>
<?php endif; ?>

<div class="tab-content" id="planTabsContent">
    <!-- Partner Plans Tab -->
    <div class="tab-pane fade show active" id="partner-plans" role="tabpanel">

        <?php if ($_SESSION['role_code'] === 'SUPER_ADMIN'): ?>
            <!-- Filter Dropdown -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <select class="form-select" id="creatorFilter" onchange="filterPlans()">
                        <option value="all">All Creators</option>
                        <option value="global">Super Admin (Global)</option>
                        <?php if (!empty($white_labels)): ?>
                            <?php foreach ($white_labels as $wl): ?>
                                <option value="<?php echo $wl['id']; ?>"><?php echo $wl['company_name']; ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Plan Name</th>
                                <th>Price</th>
                                <th>GST</th>
                                <th>Status</th>
                                <th class="pe-4 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="partnerPlansBody">
                            <?php if (!empty($partner_plans)): ?>
                                <?php foreach ($partner_plans as $plan): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold"><?php echo $plan['name']; ?></td>
                                        <td>₹<?php echo number_format($plan['price'], 2); ?></td>
                                        <td><?php echo $plan['gst_rate']; ?>%</td>
                                        <td>
                                            <?php if ($plan['status'] == 'active'): ?>
                                                <span class="badge bg-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="pe-4 text-end">
                                            <a href="<?php echo url('subscription/edit/' . $plan['id']); ?>" class="btn btn-sm btn-info text-white"><i class="fas fa-edit"></i></a>
                                            <a href="<?php echo url('subscription/delete/' . $plan['id']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No Partner Plans found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- White Label Plans Tab (Only for Super Admin) -->
    <?php if ($_SESSION['role_code'] === 'SUPER_ADMIN'): ?>
    <div class="tab-pane fade" id="wl-plans" role="tabpanel">
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Plan Name</th>
                                <th>Price</th>
                                <th>GST</th>
                                <th>Status</th>
                                <th class="pe-4 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($wl_plans)): ?>
                                <?php foreach ($wl_plans as $plan): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold text-primary"><?php echo $plan['name']; ?></td>
                                        <td>₹<?php echo number_format($plan['price'], 2); ?></td>
                                        <td><?php echo $plan['gst_rate']; ?>%</td>
                                        <td>
                                            <?php if ($plan['status'] == 'active'): ?>
                                                <span class="badge bg-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="pe-4 text-end">
                                            <a href="<?php echo url('subscription/edit/' . $plan['id']); ?>" class="btn btn-sm btn-info text-white"><i class="fas fa-edit"></i></a>
                                            <a href="<?php echo url('subscription/delete/' . $plan['id']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No White Label Plans found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
function filterPlans() {
    const creatorId = document.getElementById('creatorFilter').value;
    const tbody = document.getElementById('partnerPlansBody');

    // Show loading state
    tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></td></tr>';

    fetch('<?php echo url('subscription/filter'); ?>?creator_id=' + creatorId)
        .then(response => response.text())
        .then(html => {
            tbody.innerHTML = html;
        })
        .catch(error => {
            console.error('Error:', error);
            tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-danger">Error loading plans.</td></tr>';
        });
}
</script>

<?php view('layouts/footer'); ?>
