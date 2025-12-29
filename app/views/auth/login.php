<?php view('layouts/header', ['title' => 'Login']); ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h3 class="fw-bold mb-0">Login</h3>
                </div>
                <div class="card-body p-5">
                    <?php flash('login_error'); ?>

                    <form action="<?php echo url('auth/login_post'); ?>" method="POST">
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
                            <label for="email">Email address</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                            <label for="password">Password</label>
                        </div>

                        <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                            <a class="small text-decoration-none" href="#">Forgot Password?</a>
                            <button type="submit" class="btn btn-primary px-4">Login</button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center py-3">
                    <div class="small"><a href="<?php echo url('/'); ?>">Back to Home</a></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php view('layouts/footer'); ?>
