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
                        <div class="form-floating mb-3 position-relative">
                            <input type="password" class="form-control pe-5" id="password" name="password" placeholder="Password" required>
                            <label for="password">Password</label>
                            <button type="button" class="password-toggle btn btn-link p-0" data-target="password" aria-label="Toggle password visibility">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>

                        <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                            <a class="small text-decoration-none" href="<?php echo url('auth/forgot_password'); ?>">Forgot Password?</a>
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var toggle = document.querySelector('.password-toggle');
        if (!toggle) return;
        toggle.addEventListener('click', function () {
            var input = document.getElementById(this.dataset.target);
            if (!input) return;
            var icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
</script>

<?php view('layouts/footer'); ?>
