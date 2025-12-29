<?php
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
        </div> <!-- End Container Fluid -->
    </div> <!-- End Main Content -->

<?php else: ?>
    </div> <!-- End Main Content Wrapper -->

    <!-- Footer -->
    <footer class="bg-dark text-white pt-5 pb-3 mt-auto" id="contact">
        <div class="container">
            <div class="row g-4">
                <!-- About -->
                <div class="col-lg-4 col-md-6">
                    <h5 class="fw-bold mb-3 text-primary"><?php echo SITE_NAME; ?></h5>
                    <p class="small text-white-50">IncomeKaro is a simple finance tool to track income, check CIBIL, and manage loans easily.</p>
                    <p class="small text-white-50">Helping Businesses Across India Earn More with IncomeKaro!</p>
                </div>

                <!-- Links -->
                <div class="col-lg-2 col-md-6">
                    <h6 class="fw-bold mb-3">Links</h6>
                    <ul class="list-unstyled small">
                        <li><a href="#" class="text-white-50 text-decoration-none">About Us</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none" data-bs-toggle="modal" data-bs-target="#loginModal">Login</a></li>
                        <li><a href="#contact" class="text-white-50 text-decoration-none">Contact Us</a></li>
                    </ul>
                </div>

                <!-- Legal -->
                <div class="col-lg-2 col-md-6">
                    <h6 class="fw-bold mb-3">Legal</h6>
                    <ul class="list-unstyled small">
                        <li><a href="#" class="text-white-50 text-decoration-none">Company Doc</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">Privacy Policy</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">Terms & Conditions</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">Refund Policy</a></li>
                    </ul>
                </div>

                <!-- Contact -->
                <div class="col-lg-4 col-md-6">
                    <h6 class="fw-bold mb-3">Get in touch</h6>
                    <ul class="list-unstyled small text-white-50">
                        <li class="mb-2"><i class="fas fa-envelope me-2"></i> support@incomekaro.in</li>
                        <li class="mb-2"><i class="fas fa-map-marker-alt me-2"></i> Astra Tower, New Town, 700181</li>
                        <li class="mb-2"><i class="fas fa-phone me-2"></i> +91 786-4951-543</li>
                        <li class="mb-2"><i class="fas fa-phone me-2"></i> +91 877-7834-218</li>
                    </ul>
                </div>
            </div>

            <hr class="border-secondary my-4">

            <!-- Disclaimer -->
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-secondary bg-transparent border-secondary text-white-50 small" role="alert">
                        <strong>⚠ Attention:</strong> SUNGLORY SOFTWARE PRIVATE LIMITED never asks any details related to debit cards, credit cards and Net Banking like CVV, OTP, SMS. If any such call or mail comes, report it to support@incomekaro.in
                    </div>
                    <div class="alert alert-secondary bg-transparent border-secondary text-white-50 small" role="alert">
                        <strong>⚠ Attention:</strong> SUNGLORY SOFTWARE PRIVATE LIMITED doesn't charge any amount in the name of Loan Approval & Disbursal. If you get any such information, mail it to support@incomekaro.in
                    </div>
                </div>
            </div>

            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <p class="small text-white-50 mb-0">© <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved | CIN: U62013WB2025PTC276552</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <p class="small text-white-50 mb-0">Design & Development by Luma9</p>
                </div>
            </div>
        </div>
    </footer>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
