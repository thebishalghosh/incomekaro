<?php view('layouts/header', ['title' => 'Subscription Plans']); ?>

<div class="row mb-4">
    <div class="col-md-6">
        <h1>Subscription Plans</h1>
    </div>
    <div class="col-md-6 text-end">
        <a href="<?php echo url('subscription/create'); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Plan
        </a>
        <a href="<?php echo url('dashboard/super_admin'); ?>" class="btn btn-secondary">Back to Dashboard</a>
    </div>
</div>

<?php flash('sub_success'); ?>
<?php flash('sub_error'); ?>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Price</th>
                        <th>GST</th>
                        <th>Total Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($plans)): ?>
                        <?php foreach ($plans as $plan): ?>
                            <tr>
                                <td class="fw-bold"><?php echo $plan['name']; ?></td>
                                <td>₹<?php echo number_format($plan['price'], 2); ?></td>
                                <td><?php echo $plan['gst_rate']; ?>%</td>
                                <td>₹<?php echo number_format($plan['price'] * (1 + $plan['gst_rate'] / 100), 2); ?></td>
                                <td>
                                    <?php if ($plan['status'] == 'active'): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?php echo url('subscription/edit/' . $plan['id']); ?>" class="btn btn-sm btn-info text-white"><i class="fas fa-edit"></i></a>
                                    <a href="<?php echo url('subscription/delete/' . $plan['id']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4">No Subscription Plans found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php view('layouts/footer'); ?>
