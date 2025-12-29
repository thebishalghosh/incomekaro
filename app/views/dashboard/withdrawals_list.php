<?php view('layouts/header', ['title' => 'Withdrawals']); ?>

<div class="row mb-4">
    <div class="col-md-6">
        <h1>Withdrawal Requests</h1>
    </div>
    <div class="col-md-6 text-end">
        <a href="<?php echo url('dashboard/super_admin'); ?>" class="btn btn-secondary">Back to Dashboard</a>
    </div>
</div>

<?php flash('wd_success'); ?>
<?php flash('wd_error'); ?>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Amount</th>
                        <th>Bank Details</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($withdrawals)): ?>
                        <?php foreach ($withdrawals as $wd): ?>
                            <tr>
                                <td>#<?php echo substr($wd['id'], 0, 8); ?></td>
                                <td>
                                    <div class="fw-bold"><?php echo $wd['first_name'] . ' ' . $wd['last_name']; ?></div>
                                    <div class="small text-muted"><?php echo $wd['email']; ?></div>
                                </td>
                                <td>
                                    <div class="fw-bold">₹<?php echo number_format($wd['net_amount'], 2); ?></div>
                                    <div class="small text-muted">Gross: ₹<?php echo number_format($wd['gross_amount'], 2); ?></div>
                                </td>
                                <td>
                                    <div class="small">
                                        <strong><?php echo $wd['bank_name']; ?></strong><br>
                                        <?php echo $wd['bank_account_number']; ?><br>
                                        <?php echo $wd['ifsc_code']; ?>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                        $badge_class = 'bg-secondary';
                                        if ($wd['status'] == 'paid') $badge_class = 'bg-success';
                                        elseif ($wd['status'] == 'approved') $badge_class = 'bg-info text-dark';
                                        elseif ($wd['status'] == 'requested') $badge_class = 'bg-warning text-dark';
                                    ?>
                                    <span class="badge <?php echo $badge_class; ?>"><?php echo ucfirst($wd['status']); ?></span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($wd['created_at'])); ?></td>
                                <td>
                                    <?php if ($wd['status'] == 'requested'): ?>
                                        <form action="<?php echo url('withdrawal/update_status/' . $wd['id']); ?>" method="POST" class="d-inline">
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Approve this withdrawal?');">Approve</button>
                                        </form>
                                    <?php elseif ($wd['status'] == 'approved'): ?>
                                        <form action="<?php echo url('withdrawal/update_status/' . $wd['id']); ?>" method="POST" class="d-inline">
                                            <input type="hidden" name="status" value="paid">
                                            <button type="submit" class="btn btn-sm btn-primary" onclick="return confirm('Mark as Paid?');">Mark Paid</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4">No withdrawal requests found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php view('layouts/footer'); ?>
