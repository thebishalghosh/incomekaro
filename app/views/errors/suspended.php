<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Suspended - <?php echo get_site_name(); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-card {
            max-width: 500px;
            width: 100%;
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            text-align: center;
            padding: 40px;
            background: white;
        }
        .icon-box {
            width: 80px;
            height: 80px;
            background-color: #ffebee;
            color: #dc3545;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2.5rem;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="error-card mx-auto">
        <div class="icon-box">
            <i class="fas fa-ban"></i>
        </div>
        <h2 class="fw-bold text-dark mb-3">Account Suspended</h2>
        <p class="text-muted mb-4">
            Your account has been deactivated or suspended by the administrator.
            You no longer have access to the dashboard or services.
        </p>

        <div class="alert alert-light border mb-4 text-start">
            <small class="fw-bold text-uppercase text-muted">What can I do?</small>
            <p class="mb-0 small mt-1">Please contact your Relationship Manager or the Support Team for more information regarding this action.</p>
        </div>

        <a href="<?php echo url('auth/logout'); ?>" class="btn btn-outline-danger px-4 rounded-pill">
            <i class="fas fa-sign-out-alt me-2"></i> Logout
        </a>
        <a href="<?php echo url('contact/index'); ?>" class="btn btn-primary px-4 rounded-pill ms-2">
            Contact Support
        </a>
    </div>
</div>

</body>
</html>
