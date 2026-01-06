<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Revoked - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            max-width: 450px;
            width: 100%;
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
        }
    </style>
</head>
<body>

<div class="container d-flex justify-content-center">
    <div class="card p-5 text-center">
        <div class="icon-box">
            <i class="fas fa-user-lock fa-3x"></i>
        </div>
        <h2 class="fw-bold text-dark mb-3">Access Revoked</h2>
        <p class="text-muted mb-4">
            Your account has been temporarily deactivated by the administrator.
            You cannot access the dashboard at this time.
        </p>
        <p class="text-muted small mb-4">
            If you believe this is an error, please contact support.
        </p>

        <a href="<?php echo url('auth/logout'); ?>" class="btn btn-outline-danger px-4">
            <i class="fas fa-sign-out-alt me-2"></i> Logout
        </a>
    </div>
</div>

</body>
</html>
