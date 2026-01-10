<?php view('layouts/header', ['title' => 'Manage Subscription']); ?>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark">Manage Subscription</h2>
                <p class="text-muted">For <?php echo $client['company_name']; ?></p>
            </div>
            <a href="<?php echo url('white_label/index'); ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Back
            </a>
        </div>

        <?php if ($active_sub): ?>
            <div class="alert alert-info border-0 shadow-sm mb-4">
                <div class="d-flex align-items-center">
                    <div class="icon-box bg-white text-info rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="fas fa-check"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">Active Plan: <?php echo $active_sub['plan_name']; ?></h6>
                        <small>Valid until <?php echo date('d M Y', strtotime($active_sub['end_date'])); ?></small>
                        <?php if ($active_sub['due_amount'] > 0): ?>
                            <br><span class="badge bg-danger">Due: ₹<?php echo number_format($active_sub['due_amount'], 2); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-5">
                <form action="<?php echo url('white_label/subscription_store/' . $client['id']); ?>" method="POST">

                    <div class="mb-4">
                        <label class="form-label fw-bold">Select Plan <span class="text-danger">*</span></label>
                        <select class="form-select form-select-lg" name="plan_id" id="planSelect" required onchange="updatePrice()">
                            <option value="">Choose a Plan...</option>
                            <?php foreach ($plans as $plan): ?>
                                <option value="<?php echo $plan['id']; ?>" data-price="<?php echo $plan['price']; ?>"
                                    <?php echo ($active_sub && $active_sub['plan_id'] == $plan['id']) ? 'selected' : ''; ?>>
                                    <?php echo $plan['name']; ?> - ₹<?php echo number_format($plan['price'], 2); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control form-control-lg" name="start_date"
                                   value="<?php echo $active_sub ? $active_sub['start_date'] : date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">End Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control form-control-lg" name="end_date"
                                   value="<?php echo $active_sub ? $active_sub['end_date'] : date('Y-m-d', strtotime('+1 year')); ?>" required>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Total Amount (₹)</label>
                            <input type="number" step="0.01" class="form-control form-control-lg" name="amount" id="amountInput"
                                   value="<?php echo $active_sub ? $active_sub['amount'] : ''; ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Due Amount (₹)</label>
                            <input type="number" step="0.01" class="form-control form-control-lg" name="due_amount" id="dueInput"
                                   value="<?php echo $active_sub ? $active_sub['due_amount'] : '0.00'; ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Payment Status</label>
                            <select class="form-select form-select-lg" name="payment_status">
                                <option value="Paid" <?php echo ($active_sub && $active_sub['payment_status'] == 'Paid') ? 'selected' : ''; ?>>Paid</option>
                                <option value="Pending" <?php echo ($active_sub && $active_sub['payment_status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                <option value="Due" <?php echo ($active_sub && $active_sub['payment_status'] == 'Due') ? 'selected' : ''; ?>>Due</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <?php echo $active_sub ? 'Update Subscription' : 'Assign Subscription'; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function updatePrice() {
    const select = document.getElementById('planSelect');
    const price = select.options[select.selectedIndex].getAttribute('data-price');

    if (price) {
        document.getElementById('amountInput').value = price;
    }
}
</script>

<?php view('layouts/footer'); ?>
