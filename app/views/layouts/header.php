<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    <link rel="icon" type="image/png" href="<?php echo asset('images/fav.png'); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">
</head>
<body>

<?php
// Determine if we are in Dashboard mode
$url = $_GET['url'] ?? 'home';
$is_dashboard = false;
$dashboard_routes = ['dashboard', 'white_label', 'partner', 'user', 'service', 'application', 'report', 'settings', 'withdrawal', 'subscription'];

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

            <div class="user-profile dropdown">
                <div class="d-flex align-items-center" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)); ?>
                    </div>
                    <div class="d-none d-md-block">
                        <div class="fw-bold text-dark"><?php echo $_SESSION['user_name'] ?? 'User'; ?></div>
                        <div class="small text-muted" style="font-size: 0.75rem;"><?php echo $_SESSION['role_code'] ?? 'Role'; ?></div>
                    </div>
                    <i class="fas fa-chevron-down ms-2 text-muted small"></i>
                </div>
                <ul class="dropdown-menu dropdown-menu-end border-0 shadow">
                    <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i> Profile</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> Settings</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="<?php echo url('auth/logout'); ?>"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                </ul>
            </div>
        </header>

        <div class="container-fluid p-4">

<?php else: ?>
    <!-- Public Layout -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
        <div class="container">
            <a class="navbar-brand" href="<?php echo url('/'); ?>">
                <img src="<?php echo asset('images/logo.png'); ?>" alt="<?php echo SITE_NAME; ?>" style="max-height: 40px;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="<?php echo url('/'); ?>">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Contact Us</a></li>
                    <li class="nav-item ms-2">
                        <?php if (isLoggedIn()): ?>
                            <a href="<?php echo url('dashboard/super_admin'); ?>" class="btn btn-primary px-4">Dashboard</a>
                        <?php else: ?>
                            <button type="button" class="btn btn-primary px-4" data-bs-toggle="modal" data-bs-target="#loginModal">
                                Login
                            </button>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="loginModalLabel">Login to IncomeKaro</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form action="<?php echo url('auth/login_post'); ?>" method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Login</button>
                </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content Wrapper -->
    <div style="margin-top: 70px;">
        <div class="container mt-3">
            <?php flash('login_error'); ?>
        </div>
<?php endif; ?>
