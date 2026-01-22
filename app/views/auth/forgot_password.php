<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - <?php echo get_site_name(); ?></title>
    <link rel="icon" type="image/png" href="<?php echo get_favicon_url(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: <?php echo get_primary_color(); ?>;
            --secondary-color: <?php echo get_secondary_color(); ?>;
        }
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .auth-card {
            width: 100%;
            max-width: 400px;
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .auth-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            padding: 30px;
            text-align: center;
            color: white;
        }
        .auth-body {
            padding: 40px 30px;
            background: white;
        }
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
    </style>
</head>
<body>

<div class="auth-card">
    <div class="auth-header">
        <img src="<?php echo get_logo_url(); ?>" alt="Logo" style="height: 50px; background: white; padding: 5px; border-radius: 5px; margin-bottom: 15px;">
        <h4 class="fw-bold mb-0">Forgot Password?</h4>
        <p class="small opacity-75 mb-0">Enter your email to reset it.</p>
    </div>
    <div class="auth-body">
        <?php flash('login_error'); ?>
        <?php flash('login_success'); ?>

        <form action="<?php echo url('auth/send_reset_link'); ?>" method="POST">
            <div class="mb-4">
                <label class="form-label text-muted small fw-bold">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                    <input type="email" class="form-control border-start-0 bg-light" name="email" required placeholder="name@example.com">
                </div>
            </div>

            <div class="d-grid mb-3">
                <button type="submit" class="btn btn-primary py-2 fw-bold">Send Reset Link</button>
            </div>

            <div class="text-center">
                <a href="<?php echo url('/'); ?>" class="text-decoration-none small text-muted">
                    <i class="fas fa-arrow-left me-1"></i> Back to Login
                </a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
