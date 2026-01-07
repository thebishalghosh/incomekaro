<?php view('layouts/header', ['title' => 'RM Dashboard']); ?>

<div class="row mb-4">
    <div class="col-12">
        <h1 class="fw-bold text-dark">Welcome, <?php echo $_SESSION['user_name']; ?></h1>
        <p class="text-muted">Here is an overview of your assigned partners and their applications.</p>
    </div>
</div>

<!-- Stats Row -->
<div class="row g-4 mb-5">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100" style="border-left: 5px solid #6A5ACD !important;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted text-uppercase small fw-bold mb-1">My Partners</h6>
                        <h2 class="fw-bold mb-0 text-dark"><?php echo $stats['total_partners']; ?></h2>
                    </div>
                    <div class="icon-box bg-light text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fas fa-users fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100" style="border-left: 5px solid #20B2AA !important;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted text-uppercase small fw-bold mb-1">Total Applications</h6>
                        <h2 class="fw-bold mb-0 text-dark"><?php echo $stats['total_applications']; ?></h2>
                    </div>
                    <div class="icon-box bg-light text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fas fa-file-alt fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100" style="border-left: 5px solid #FF7F50 !important;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted text-uppercase small fw-bold mb-1">Pending Verification</h6>
                        <h2 class="fw-bold mb-0 text-dark"><?php echo $stats['pending_applications']; ?></h2>
                    </div>
                    <div class="icon-box bg-light text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fas fa-clock fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Partners -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3">
        <h5 class="fw-bold mb-0">Recently Assigned Partners</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th class="pe-4 text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recent_partners)): ?>
                        <?php foreach ($recent_partners as $ptr): ?>
                            <tr>
                                <td class="ps-4 fw-bold"><?php echo $ptr['full_name']; ?></td>
                                <td><?php echo $ptr['email']; ?></td>
                                <td><?php echo $ptr['mobile']; ?></td>
                                <td class="pe-4 text-end">
                                    <a href="<?php echo url('partner/profile/' . $ptr['id']); ?>" class="btn btn-sm btn-outline-secondary">View Profile</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">No partners assigned yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php view('layouts/footer'); ?>
