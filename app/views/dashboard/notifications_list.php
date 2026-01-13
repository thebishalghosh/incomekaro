<?php
// Conditionally load header
if (isset($_SESSION['role_code']) && $_SESSION['role_code'] === 'PARTNER_ADMIN') {
    view('layouts/partner_header', ['title' => 'Notifications']);
} else {
    view('layouts/header', ['title' => 'Notifications']);
}
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">Notifications</h2>
            <p class="text-muted">Stay updated with the latest activities.</p>
        </div>
        <a href="<?php echo url('notification/mark_all_read'); ?>" class="btn btn-outline-primary">
            <i class="fas fa-check-double me-2"></i> Mark All as Read
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="list-group list-group-flush">
                <?php if (!empty($notifications)): ?>
                    <?php foreach ($notifications as $notif): ?>
                        <a href="<?php echo url('notification/read/' . $notif['id']); ?>" class="list-group-item list-group-item-action p-4 <?php echo $notif['is_read'] ? '' : 'bg-light'; ?>">
                            <div class="d-flex w-100 justify-content-between align-items-center mb-2">
                                <h6 class="mb-0 fw-bold <?php echo $notif['is_read'] ? 'text-dark' : 'text-primary'; ?>">
                                    <?php if (!$notif['is_read']): ?>
                                        <i class="fas fa-circle text-primary me-2" style="font-size: 8px;"></i>
                                    <?php endif; ?>
                                    <?php echo $notif['title']; ?>
                                </h6>
                                <small class="text-muted"><?php echo date('d M Y, h:i A', strtotime($notif['created_at'])); ?></small>
                            </div>
                            <p class="mb-1 text-secondary"><?php echo $notif['message']; ?></p>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-5">
                        <div class="icon-box bg-light text-muted rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="fas fa-bell-slash fa-3x opacity-50"></i>
                        </div>
                        <h5 class="fw-bold text-dark">No Notifications</h5>
                        <p class="text-muted">You're all caught up!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
// Conditionally load footer
if (isset($_SESSION['role_code']) && $_SESSION['role_code'] === 'PARTNER_ADMIN') {
    view('layouts/partner_footer');
} else {
    view('layouts/footer');
}
?>
