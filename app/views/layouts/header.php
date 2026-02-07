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
    <style>
        /* Dynamic Theme Color */
        :root {
            --primary-color: <?php echo get_primary_color(); ?>;
            --secondary-color: <?php echo get_secondary_color(); ?>;
        }

        /* Sidebar Gradient Override */
        .sidebar {
            background: linear-gradient(180deg, var(--primary-color) 0%, var(--secondary-color) 100%) !important;
        }

        /* Navbar Enhancements */
        .navbar {
            padding-top: 1rem;
            padding-bottom: 1rem;
            transition: all 0.3s ease;
        }
        .navbar-brand img {
            height: 60px; /* Increased from 45px */
            transition: transform 0.3s ease;
        }
        .navbar-brand:hover img {
            transform: scale(1.05);
        }
        .nav-link {
            font-weight: 500;
            color: #4a5568 !important;
            margin-left: 1rem;
            margin-right: 1rem;
            position: relative;
            transition: color 0.3s ease;
        }
        .nav-link:hover, .nav-link.active {
            color: var(--primary-color) !important;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 50%;
            background-color: var(--primary-color);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }
        .nav-link:hover::after {
            width: 100%;
        }
        .btn-login {
            border-radius: 50px;
            padding-left: 2rem;
            padding-right: 2rem;
            font-weight: 600;
            box-shadow: 0 4px 6px rgba(50, 50, 93, 0.11), 0 1px 3px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 7px 14px rgba(50, 50, 93, 0.1), 0 3px 6px rgba(0, 0, 0, 0.08);
            background-color: var(--primary-color); /* Darken slightly in real CSS, but keeping simple */
            filter: brightness(90%);
        }
    </style>
</head>
<body>

<?php
// Determine if we are in Dashboard mode
$url = $_GET['url'] ?? 'home';
$is_dashboard = false;
// Added 'bank' to the list of dashboard routes
$dashboard_routes = ['dashboard', 'white_label', 'partner', 'user', 'service', 'application', 'report', 'settings', 'withdrawal', 'subscription', 'rm', 'instant_panel', 'inquiry', 'notification', 'sales', 'profile', 'policy', 'bank'];

foreach ($dashboard_routes as $route) {
    if (strpos($url, $route) === 0) {
        $is_dashboard = true;
        break;
    }
}

if ($is_dashboard && isLoggedIn()):
?>
    <!-- Dashboard Layout -->
    <?php require_once APP_PATH . '/views/layouts/sidebar.php'; ?>

    <div class="main-content" id="main-content">
        <!-- Dashboard Header -->
        <header class="dashboard-header">
            <div class="toggle-sidebar" onclick="document.getElementById('sidebar').classList.toggle('active'); document.getElementById('main-content').classList.toggle('active');">
                <i class="fas fa-bars"></i>
            </div>

            <div class="d-flex align-items-center">
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

                <!-- User Profile -->
                <div class="user-profile dropdown">
                    <div class="d-flex align-items-center" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="user-avatar">
                            <?php echo strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)); ?>
                        </div>
                        <div class="d-none d-md-block">
                            <div class="fw-bold text-dark"><?php echo $_SESSION['user_name'] ?? 'User'; ?></div>
                            <div class="small text-muted" style="font-size: 0.75rem;">
                                <?php
                                    $role_display = $_SESSION['role_code'] ?? 'Role';
                                    if ($role_display === 'WHITE_LABEL') {
                                        echo 'Administrator';
                                    } elseif ($role_display === 'SUPER_ADMIN') {
                                        echo 'Super Admin';
                                    } elseif ($role_display === 'PARTNER_ADMIN') {
                                        echo 'Partner';
                                    } else {
                                        echo ucfirst(strtolower(str_replace('_', ' ', $role_display)));
                                    }
                                ?>
                            </div>
                        </div>
                        <i class="fas fa-chevron-down ms-2 text-muted small"></i>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow">
                        <li><a class="dropdown-item" href="<?php echo url('profile/index'); ?>"><i class="fas fa-user me-2"></i> Profile</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="<?php echo url('auth/logout'); ?>"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Optimized Notification Polling Script -->
        <script>
            (function() {
                let pollTimer = null;
                let baseInterval = 60000; // 60 seconds
                let currentInterval = baseInterval;
                let isRequestPending = false;

                function pollNotifications() {
                    // Stop if tab is hidden
                    if (document.hidden) {
                        return;
                    }

                    // Prevent duplicate requests
                    if (isRequestPending) {
                        return;
                    }

                    isRequestPending = true;

                    // Timeout protection (10 seconds)
                    const controller = new AbortController();
                    const timeoutId = setTimeout(() => controller.abort(), 10000);

                    fetch('<?php echo url('notification/count'); ?>', { signal: controller.signal })
                        .then(response => {
                            clearTimeout(timeoutId);
                            if (response.status === 429) {
                                // Rate limited: Backoff
                                throw new Error('Rate limited');
                            }
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            const badge = document.getElementById('notification-badge');
                            if (data.count > 0) {
                                badge.textContent = data.count;
                                badge.style.display = 'inline-block';
                            } else {
                                badge.style.display = 'none';
                            }
                            // Success: Reset interval
                            currentInterval = baseInterval;
                        })
                        .catch(error => {
                            if (error.name === 'AbortError') {
                                console.warn('Notification poll timed out');
                            } else {
                                console.warn('Notification poll failed:', error);
                            }
                            // Exponential backoff: double interval, max 5 mins
                            currentInterval = Math.min(currentInterval * 2, 300000);
                        })
                        .finally(() => {
                            isRequestPending = false;
                            // Schedule next poll
                            if (!document.hidden) {
                                clearTimeout(pollTimer);
                                pollTimer = setTimeout(pollNotifications, currentInterval);
                            }
                        });
                }

                // Start polling
                pollTimer = setTimeout(pollNotifications, baseInterval);

                // Handle visibility change
                document.addEventListener('visibilitychange', function() {
                    if (!document.hidden) {
                        // Reset and poll immediately when tab becomes active
                        currentInterval = baseInterval;
                        clearTimeout(pollTimer);
                        pollNotifications();
                    } else {
                        // Stop polling when hidden
                        clearTimeout(pollTimer);
                    }
                });
            })();
        </script>

        <div class="container-fluid p-4">

<?php else: ?>
    <!-- Public Layout -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
        <div class="container">
            <a class="navbar-brand" href="<?php echo url('/'); ?>">
                <img src="<?php echo get_logo_url(); ?>" alt="<?php echo get_site_name(); ?>">
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="<?php echo url('/'); ?>">Home</a></li>

                    <?php if (!defined('IS_WHITE_LABEL') || !IS_WHITE_LABEL): ?>
                        <li class="nav-item"><a class="nav-link" href="<?php echo url('about/index'); ?>">About</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo url('contact/index'); ?>">Contact Us</a></li>
                    <?php endif; ?>

                    <li class="nav-item ms-3">
                        <?php if (isLoggedIn()): ?>
                            <a href="<?php echo url('dashboard/super_admin'); ?>" class="btn btn-primary btn-login">Dashboard</a>
                        <?php else: ?>
                            <button type="button" class="btn btn-primary btn-login" data-bs-toggle="modal" data-bs-target="#loginModal">
                                Login
                            </button>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Enhanced Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 overflow-hidden rounded-4 shadow-lg">
          <div class="row g-0">
            <!-- Left Side: Image/Welcome -->
            <div class="col-md-6 d-none d-md-flex bg-primary text-white align-items-center justify-content-center p-5" style="background: linear-gradient(135deg, var(--primary-color) 0%, #764ba2 100%);">
                <div class="text-center">
                    <div class="mb-4">
                        <i class="fas fa-chart-pie fa-4x opacity-75"></i>
                    </div>
                    <h2 class="fw-bold mb-3">Welcome Back!</h2>
                    <p class="opacity-75">Login to access your dashboard, track your earnings, and manage your business efficiently.</p>
                </div>
            </div>

            <!-- Right Side: Form -->
            <div class="col-md-6 bg-white p-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="fw-bold text-dark mb-0">Login</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="<?php echo url('auth/login_post'); ?>" method="POST">
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
                        <label for="email">Email address</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                        <label for="password">Password</label>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="rememberMe">
                            <label class="form-check-label small text-muted" for="rememberMe">Remember me</label>
                        </div>
                        <a href="<?php echo url('auth/forgot_password'); ?>" class="small text-decoration-none">Forgot Password?</a>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg btn-login">Login</button>
                    </div>
                </form>

            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content Wrapper -->
    <div style="margin-top: 80px;">
        <div class="container mt-3">
            <?php flash('login_error'); ?>
        </div>

        <!-- Auto-open Login Modal Script -->
        <?php if (isset($_GET['login']) && $_GET['login'] == 'true'): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
                loginModal.show();
            });
        </script>
        <?php endif; ?>
<?php endif; ?>
