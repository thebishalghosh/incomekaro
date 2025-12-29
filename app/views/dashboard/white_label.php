<?php view('layouts/header', ['title' => 'Dashboard']); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark">My Dashboard</h2>
        <p class="text-muted">Welcome back, <?php echo $_SESSION['user_name'] ?? 'Client'; ?>!</p>
    </div>
    <div>
        <button class="btn btn-primary"><i class="fas fa-plus me-2"></i> Add Partner</button>
    </div>
</div>

<!-- Stats Row -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card purple">
            <div class="icon-box">
                <i class="fas fa-users"></i>
            </div>
            <h3>0</h3>
            <p>My Partners</p>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card blue">
            <div class="icon-box">
                <i class="fas fa-file-alt"></i>
            </div>
            <h3>0</h3>
            <p>Total Applications</p>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card green">
            <div class="icon-box">
                <i class="fas fa-wallet"></i>
            </div>
            <h3>₹0.00</h3>
            <p>Wallet Balance</p>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card orange">
            <div class="icon-box">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <h3>0</h3>
            <p>Action Required</p>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3">
        <h5 class="fw-bold mb-0">Recent Applications</h5>
    </div>
    <div class="card-body p-0">
        <div class="text-center py-5 text-muted">
            <i class="fas fa-inbox fa-3x mb-3"></i>
            <p>No applications found yet.</p>
        </div>
    </div>
</div>

<?php view('layouts/footer'); ?>
