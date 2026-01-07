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
    <style>
        /* Navbar Enhancements */
        .navbar {
            padding-top: 1rem;
            padding-bottom: 1rem;
            transition: all 0.3s ease;
        }
        .navbar-brand img {
            height: 45px;
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
        }
        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 7px 14px rgba(50, 50, 93, 0.1), 0 3px 6px rgba(0, 0, 0, 0.08);
        }
    </style>
</head>
<body>

<?php
// Determine if we are in Dashboard mode
$url = $_GET['url'] ?? 'home';
$is_dashboard = false;
// Added 'rm' to the list of dashboard routes
$dashboard_routes = ['dashboard', 'white_label', 'partner', 'user', 'service', 'application', 'report', 'settings', 'withdrawal', 'subscription', 'rm'];

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
                <img src="<?php echo asset('images/logo.png'); ?>" alt="<?php echo SITE_NAME; ?>">
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="<?php echo url('/'); ?>">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo url('about/index'); ?>">About Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo url('contact/index'); ?>">Contact Us</a></li>
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
            <div class="col-md-6 d-none d-md-flex bg-primary text-white align-items-center justify-content-center p-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
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
                        <a href="#" class="small text-decoration-none">Forgot Password?</a>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">Login</button>
                    </div>
                </form>

                <div class="text-center mt-4">
                    <p class="small text-muted">Don't have an account? <a href="<?php echo url('contact/index'); ?>" class="text-decoration-none fw-bold">Contact Us</a></p>
                </div>
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
<?php endif; ?>
