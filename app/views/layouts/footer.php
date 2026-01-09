<?php
$url = $_GET['url'] ?? 'home';
$is_dashboard = false;
// Added 'instant_panel' to the list of dashboard routes
$dashboard_routes = ['dashboard', 'white_label', 'partner', 'user', 'service', 'application', 'report', 'settings', 'withdrawal', 'subscription', 'rm', 'instant_panel'];

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
    <footer class="text-white pt-5 pb-3 mt-auto" style="background-color: #0f172a;" id="contact">
        <div class="container">
            <div class="row g-5">
                <!-- Brand & About -->
                <div class="col-lg-4 col-md-6">
                    <div class="mb-4">
                        <img src="<?php echo asset('images/logo.png'); ?>" alt="<?php echo SITE_NAME; ?>" style="max-height: 45px; filter: brightness(0) invert(1);">
                    </div>
                    <p class="text-white-50 small mb-4" style="line-height: 1.8;">
                        IncomeKaro is India's leading financial distribution network. We empower partners to earn by distributing financial products like Loans, Credit Cards, and Insurance.
                    </p>
                    <div class="d-flex gap-3">
                        <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="col-lg-2 col-md-6">
                    <h6 class="fw-bold text-white mb-4 text-uppercase small letter-spacing-1">Quick Links</h6>
                    <ul class="list-unstyled footer-links">
                        <li><a href="<?php echo url('/'); ?>">Home</a></li>
                        <li><a href="<?php echo url('about/index'); ?>">About Us</a></li>
                        <li><a href="#" data-bs-toggle="modal" data-bs-target="#loginModal">Partner Login</a></li>
                        <li><a href="<?php echo url('contact/index'); ?>">Contact Support</a></li>
                    </ul>
                </div>

                <!-- Legal -->
                <div class="col-lg-2 col-md-6">
                    <h6 class="fw-bold text-white mb-4 text-uppercase small letter-spacing-1">Legal</h6>
                    <ul class="list-unstyled footer-links">
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms of Service</a></li>
                        <li><a href="#">Refund Policy</a></li>
                        <li><a href="#">Compliance</a></li>
                    </ul>
                </div>

                <!-- Contact Info (Replaces Newsletter) -->
                <div class="col-lg-4 col-md-6">
                    <h6 class="fw-bold text-white mb-4 text-uppercase small letter-spacing-1">Get in Touch</h6>
                    <div class="text-white-50 small">
                        <p class="mb-3"><i class="fas fa-map-marker-alt me-2 text-primary"></i> Astra Tower, New Town, Kolkata, 700181</p>
                        <p class="mb-3"><i class="fas fa-envelope me-2 text-primary"></i> support@incomekaro.in</p>
                        <p class="mb-3"><i class="fas fa-phone-alt me-2 text-primary"></i> +91 786-4951-543</p>
                        <p class="mb-0"><i class="fas fa-phone-alt me-2 text-primary"></i> +91 877-7834-218</p>
                    </div>
                </div>
            </div>

            <hr class="border-secondary my-5 opacity-25">

            <!-- Bottom Footer -->
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    <p class="small text-white-50 mb-0">
                        &copy; <?php echo date('Y'); ?> <strong class="text-white"><?php echo SITE_NAME; ?></strong>. All Rights Reserved.
                        <br><span class="opacity-50" style="font-size: 0.75rem;">CIN: U62013WB2025PTC276552</span>
                    </p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <!-- Removed Luma9 credit -->
                </div>
            </div>
        </div>
    </footer>

    <style>
        .social-icon {
            width: 36px;
            height: 36px;
            background-color: rgba(255,255,255,0.1);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .social-icon:hover {
            background-color: var(--primary-color);
            color: #fff;
            transform: translateY(-3px);
        }
        .footer-links li {
            margin-bottom: 12px;
        }
        .footer-links a {
            color: rgba(255,255,255,0.6);
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            display: inline-block;
        }
        .footer-links a:hover {
            color: #fff;
            transform: translateX(5px);
        }
        .letter-spacing-1 {
            letter-spacing: 1px;
        }
    </style>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
