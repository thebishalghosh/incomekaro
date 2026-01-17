<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title . ' - ' . get_site_name() : get_site_name(); ?></title>
    <link rel="icon" type="image/png" href="<?php echo get_favicon_url(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        /* Dynamic Theme Color */
        :root {
            --primary-color: <?php echo get_primary_color(); ?>;
            --secondary-color: <?php echo get_secondary_color(); ?>;
        }

        /* User Profile Dropdown Styling */
        .user-profile {
            position: relative;
        }

        .user-profile > div[data-bs-toggle="dropdown"] {
            cursor: pointer;
            padding: 6px 12px;
            border-radius: 8px;
            transition: all 0.3s ease;
            user-select: none;
        }

        .user-profile > div[data-bs-toggle="dropdown"]:hover {
            background-color: rgba(0, 0, 0, 0.05);
            transform: translateY(-1px);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
            margin-right: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .user-profile > div[data-bs-toggle="dropdown"]:hover .user-avatar {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .user-profile .fas.fa-chevron-down {
            font-size: 0.75rem;
            transition: transform 0.3s ease;
            color: #9ca3af;
        }

        .user-profile > div[data-bs-toggle="dropdown"][aria-expanded="true"] .fas.fa-chevron-down {
            transform: rotate(180deg);
            color: var(--primary-color);
        }

        .user-profile .dropdown-menu {
            margin-top: 8px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(0, 0, 0, 0.05);
            animation: slideDown 0.2s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-8px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .user-profile .dropdown-item {
            padding: 10px 16px;
            color: #0f172a;
            font-weight: 500;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }

        .user-profile .dropdown-item:hover {
            background-color: rgba(0, 0, 0, 0.04);
            border-left-color: var(--primary-color);
            padding-left: 19px;
        }

        .user-profile .dropdown-item i {
            color: var(--primary-color);
            transition: all 0.2s ease;
        }

        .user-profile .dropdown-item.text-danger {
            color: #dc2626;
        }

        .user-profile .dropdown-item.text-danger:hover {
            background-color: rgba(220, 38, 38, 0.1);
            border-left-color: #dc2626;
        }

        .user-profile .dropdown-item.text-danger i {
            color: #dc2626;
        }

        .user-profile .dropdown-divider {
            margin: 6px 0;
            border-color: rgba(0, 0, 0, 0.08);
        }
        

    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo url('dashboard/partner'); ?>">
            <img src="<?php echo get_logo_url(); ?>" alt="<?php echo get_site_name(); ?>" style="max-height: 60px;">
        </a>

        <div class="d-flex align-items-center">
            <a href="<?php echo url('application/index'); ?>" class="btn btn-outline-primary me-2">
                <i class="fas fa-file-alt me-1"></i> My Applications
            </a>
            <a href="<?php echo url('withdrawal/index'); ?>" class="btn btn-outline-success me-2">
                <i class="fas fa-wallet me-1"></i> Withdrawals
            </a>
            <button class="btn btn-outline-secondary me-2" data-bs-toggle="modal" data-bs-target="#policyModal">Policy</button>
            <a href="<?php echo url('invoice/download'); ?>" class="btn btn-outline-secondary me-3" target="_blank">
                <i class="fas fa-file-invoice me-1"></i> Invoice
            </a>

            <!-- Notification Bell -->
            <div class="dropdown me-3">
                <a href="#" class="text-muted position-relative" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-bell fa-lg"></i>
                    <?php $unread_count = get_my_unread_count(); ?>
                    <span id="notification-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem; <?php echo $unread_count > 0 ? '' : 'display: none;'; ?>">
                        <?php echo $unread_count; ?>
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-end shadow border-0 p-0" style="width: 350px;">
                    <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                        <h6 class="mb-0 fw-bold">Notifications</h6>
                        <a href="<?php echo url('notification/mark_all_read'); ?>" class="small text-decoration-none">Mark all as read</a>
                    </div>
                    <div class="list-group list-group-flush" style="max-height: 400px; overflow-y: auto;">
                        <?php $notifications = get_my_notifications(); ?>
                        <?php if (empty($notifications)): ?>
                            <div class="text-center p-4 text-muted">
                                <i class="fas fa-check-circle fa-2x mb-2"></i>
                                <p>No new notifications.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($notifications as $notif): ?>
                                <a href="<?php echo url('notification/read/' . $notif['id']); ?>" class="list-group-item list-group-item-action <?php echo $notif['is_read'] ? '' : 'bg-light'; ?>">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1 fw-bold small"><?php echo $notif['title']; ?></h6>
                                        <small class="text-muted"><?php echo date('d M', strtotime($notif['created_at'])); ?></small>
                                    </div>
                                    <p class="mb-1 small text-muted"><?php echo $notif['message']; ?></p>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <a href="<?php echo url('notification/index'); ?>" class="dropdown-item text-center small py-2">View All Notifications</a>
                </div>
            </div>

            <div class="user-profile dropdown">
                <div class="d-flex align-items-center" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)); ?>
                    </div>
                    <div class="d-none d-md-block">
                        <div class="fw-bold text-dark"><?php echo $_SESSION['user_name'] ?? 'User'; ?></div>
                    </div>
                    <i class="bi bi-chevron-down ms-2 text-muted small"></i>
                </div>
                <ul class="dropdown-menu dropdown-menu-end border-0 shadow">
                    <li><a class="dropdown-item" href="<?php echo url('profile/index'); ?>"><i class="fas fa-user me-2"></i> My Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="<?php echo url('auth/logout'); ?>"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<!-- Policy Modal -->
<div class="modal fade" id="policyModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Partner Policy</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <h6>Loan Approval and Rejection Policy</h6>
        <p class="small text-muted">We adhere to strict guidelines set by our banking partners. Approval is subject to credit checks and document verification.</p>

        <h6 class="mt-4">Code of Conduct</h6>
        <p class="small text-muted">Partners must not charge any extra fees from customers. Any fraudulent activity will lead to immediate termination.</p>

        <h6 class="mt-4">Data Privacy</h6>
        <p class="small text-muted">Customer data must be handled with confidentiality and used only for the purpose of loan processing.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Notification Polling Script -->
<script>
    setInterval(function() {
        fetch('<?php echo url('notification/count'); ?>')
            .then(response => response.json())
            .then(data => {
                const badge = document.getElementById('notification-badge');
                if (data.count > 0) {
                    badge.textContent = data.count;
                    badge.style.display = 'inline-block';
                } else {
                    badge.style.display = 'none';
                }
            })
            .catch(error => console.error('Error polling notifications:', error));
    }, 30000); // Poll every 30 seconds
</script>

<div class="container-fluid p-4">
