<?php
$url = $_GET['url'] ?? 'home';
$is_dashboard = false;
// Added 'inquiry' to the list of dashboard routes
$dashboard_routes = ['dashboard', 'white_label', 'partner', 'user', 'service', 'application', 'report', 'settings', 'withdrawal', 'subscription', 'rm', 'instant_panel', 'inquiry'];

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

    <?php
        // Dynamic Footer Data
        global $WL_CONFIG;

        // Ensure functions exist before calling (safety check)
        $site_name = function_exists('get_site_name') ? get_site_name() : 'IncomeKaro';
        $logo_url = function_exists('get_logo_url') ? get_logo_url() : 'images/logo.png';

        // If get_logo_url returned a relative path (unlikely but possible if changed), wrap in asset
        if (strpos($logo_url, 'http') === false) {
            $logo_url = asset($logo_url);
        }

        // Default Contact Info
        $contact_email = 'support@incomekaro.in';
        $contact_phone = '+91 786-4951-543';
        $contact_address = 'Astra Tower, New Town, Kolkata, 700181';

        // Override if White Label
        if (defined('IS_WHITE_LABEL') && IS_WHITE_LABEL && $WL_CONFIG) {
            $contact_email = $WL_CONFIG['support_email'];
            // Check if landing data has phone/address overrides
            $landing = !empty($WL_CONFIG['landing_page_data']) ? json_decode($WL_CONFIG['landing_page_data'], true) : [];
            if (!empty($landing['contact_phone'])) $contact_phone = $landing['contact_phone'];
            if (!empty($landing['contact_address'])) $contact_address = $landing['contact_address'];
        }
    ?>

    <!-- Footer -->
    <footer class="text-white pt-5 pb-3 mt-auto" style="background-color: #0f172a;" id="contact">
        <div class="container">
            <div class="row g-5">
                <!-- Brand & About -->
                <div class="col-lg-4 col-md-6">
                    <div class="mb-4">
                        <!-- Removed filter to debug, ensure logo is visible -->
                        <img src="<?php echo $logo_url; ?>" alt="<?php echo $site_name; ?>" style="max-height: 45px;">
                    </div>
                    <p class="text-white-50 small mb-4" style="line-height: 1.8;">
                        <?php echo $site_name; ?> is India's leading financial distribution network. We empower partners to earn by distributing financial products like Loans, Credit Cards, and Insurance.
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

                        <?php if (!defined('IS_WHITE_LABEL') || !IS_WHITE_LABEL): ?>
                            <li><a href="<?php echo url('about/index'); ?>">About Us</a></li>
                        <?php endif; ?>

                        <li><a href="#" data-bs-toggle="modal" data-bs-target="#loginModal">Partner Login</a></li>

                        <?php if (!defined('IS_WHITE_LABEL') || !IS_WHITE_LABEL): ?>
                            <li><a href="<?php echo url('contact/index'); ?>">Contact Support</a></li>
                        <?php endif; ?>
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

                <!-- Contact Info -->
                <div class="col-lg-4 col-md-6">
                    <h6 class="fw-bold text-white mb-4 text-uppercase small letter-spacing-1">Get in Touch</h6>
                    <div class="text-white-50 small">
                        <p class="mb-3"><i class="fas fa-map-marker-alt me-2 text-primary"></i> <?php echo $contact_address; ?></p>
                        <p class="mb-3"><i class="fas fa-envelope me-2 text-primary"></i> <?php echo $contact_email; ?></p>
                        <p class="mb-3"><i class="fas fa-phone-alt me-2 text-primary"></i> <?php echo $contact_phone; ?></p>
                    </div>
                </div>
            </div>

            <hr class="border-secondary my-5 opacity-25">

            <!-- Bottom Footer -->
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    <p class="small text-white-50 mb-0">
                        &copy; <?php echo date('Y'); ?> <strong class="text-white"><?php echo $site_name; ?></strong>. All Rights Reserved.
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
