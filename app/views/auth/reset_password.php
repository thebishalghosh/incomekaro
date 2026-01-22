<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - <?php echo get_site_name(); ?></title>
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
        <h4 class="fw-bold mb-0">Set New Password</h4>
        <p class="small opacity-75 mb-0">Create a strong password for your account.</p>
    </div>
    <div class="auth-body">
        <?php flash('login_error'); ?>

        <form action="<?php echo url('auth/update_password'); ?>" method="POST">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">

            <div class="mb-3">
                <label class="form-label text-muted small fw-bold">New Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-lock text-muted"></i></span>
                    <input type="password" class="form-control border-start-0 bg-light" name="password" required minlength="6">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label text-muted small fw-bold">Confirm Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-lock text-muted"></i></span>
                    <input type="password" class="form-control border-start-0 bg-light" name="confirm_password" required minlength="6">
                </div>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary py-2 fw-bold">Update Password</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
