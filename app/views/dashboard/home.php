<?php view('layouts/header', ['title' => 'Home']); ?>

<!-- Hero Section -->
<section class="hero-section position-relative overflow-hidden" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); padding: 80px 0;">

    <!-- Animated 3D Balls -->
    <div class="floating-ball ball-1"></div>
    <div class="floating-ball ball-2"></div>
    <div class="floating-ball ball-3"></div>

    <div class="container position-relative" style="z-index: 2;">
        <div class="row align-items-center">
            <!-- Left Content -->
            <div class="col-lg-6 text-center text-lg-start mb-5 mb-lg-0">
                <span class="badge bg-primary-subtle text-primary mb-3 px-3 py-2 rounded-pill fw-bold border border-primary-subtle">
                    <i class="fas fa-star me-2"></i>India’s No. 1 Fast Payout Provider
                </span>
                <h1 class="display-4 fw-bold text-dark mb-4 lh-tight">
                    Welcome to <span class="text-primary">IncomeKaro</span><br>
                    Instant Disbursal
                </h1>
                <p class="lead text-muted mb-5 pe-lg-5">
                    Powered by INCOMEKARO and trusted by over <strong class="text-dark">7,500+ businesses</strong> across India. We deliver reliable, high-speed financial solutions tailored to accelerate your growth.
                </p>
                <div class="d-flex justify-content-center justify-content-lg-start gap-3">
                    <button type="button" class="btn btn-primary btn-lg px-5 shadow-lg rounded-pill" data-bs-toggle="modal" data-bs-target="#loginModal">
                        Apply Now <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                    <a href="#contact" class="btn btn-outline-dark btn-lg px-5 rounded-pill">Contact Us</a>
                </div>

                <!-- Trust Indicators -->
                <div class="mt-5 pt-3 border-top d-flex align-items-center justify-content-center justify-content-lg-start gap-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle text-success fa-lg me-2"></i>
                        <span class="small fw-bold text-muted">Instant Approval</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle text-success fa-lg me-2"></i>
                        <span class="small fw-bold text-muted">Secure Process</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle text-success fa-lg me-2"></i>
                        <span class="small fw-bold text-muted">24/7 Support</span>
                    </div>
                </div>
            </div>

            <!-- Right Image -->
            <div class="col-lg-6">
                <div class="position-relative">
                    <!-- Decorative Elements behind image -->
                    <div class="position-absolute top-0 end-0 translate-middle-y bg-warning rounded-circle opacity-10" style="width: 200px; height: 200px; filter: blur(40px); z-index: 0;"></div>
                    <div class="position-absolute bottom-0 start-0 translate-middle-y bg-primary rounded-circle opacity-10" style="width: 200px; height: 200px; filter: blur(40px); z-index: 0;"></div>

                    <img src="<?php echo asset('images/hero.png'); ?>" alt="IncomeKaro Dashboard" class="img-fluid position-relative z-1" style="transform: perspective(1000px) rotateY(-5deg) rotateX(2deg); transition: transform 0.5s ease;">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Trusted By Section -->
<section class="py-5 bg-white border-bottom">
    <div class="container text-center">
        <p class="text-muted fw-bold text-uppercase mb-4 small letter-spacing-2">Trusted by Top Financial Institutions</p>
        <div class="d-flex justify-content-center flex-wrap align-items-center gap-5 opacity-50 grayscale-hover">
            <!-- Placeholder for Logos - Using Text for now but styled -->
            <h4 class="fw-bold text-dark m-0">HDFC Bank</h4>
            <h4 class="fw-bold text-dark m-0">SBI</h4>
            <h4 class="fw-bold text-dark m-0">ICICI Bank</h4>
            <h4 class="fw-bold text-dark m-0">Axis Bank</h4>
            <h4 class="fw-bold text-dark m-0">Kotak</h4>
        </div>
    </div>
</section>

<!-- Steps Section -->
<section class="py-5 bg-light" id="about">
    <div class="container">
        <div class="text-center mb-5">
            <h6 class="text-primary fw-bold text-uppercase letter-spacing-2">Why Choose Us</h6>
            <h2 class="fw-bold display-6">Streamline Your Loan Business</h2>
            <p class="text-muted mx-auto" style="max-width: 600px;">We provide the best DSA CRM software designed to boost productivity and manage leads effortlessly.</p>
        </div>
        <div class="row g-4 text-center">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-hover p-4 bg-white rounded-4">
                    <div class="card-body">
                        <div class="icon-box bg-primary-subtle text-primary rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="fas fa-user-plus fa-2x"></i>
                        </div>
                        <h4 class="card-title fw-bold h5">1. Apply for Registration</h4>
                        <p class="card-text text-muted">Create your account in minutes by providing basic business details.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-hover p-4 bg-white rounded-4">
                    <div class="card-body">
                        <div class="icon-box bg-success-subtle text-success rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                        <h4 class="card-title fw-bold h5">2. Choose Your Plan</h4>
                        <p class="card-text text-muted">Select the subscription plan that perfectly fits your business scale.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-hover p-4 bg-white rounded-4">
                    <div class="card-body">
                        <div class="icon-box bg-warning-subtle text-warning rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                        <h4 class="card-title fw-bold h5">3. Grow Your Business</h4>
                        <p class="card-text text-muted">Leverage our CRM tools to track leads and maximize your earnings.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center mt-5">
            <button type="button" class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm" data-bs-toggle="modal" data-bs-target="#loginModal">Join Now</button>
        </div>
    </div>
</section>

<!-- Plans Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold display-6">Simple, Transparent Pricing</h2>
            <p class="text-muted mx-auto" style="max-width: 600px;">Get lifetime access with our Incomekaro subscription plan. Enjoy zero monthly fees and full premium features — forever.</p>
        </div>
        <div class="row g-4 justify-content-center">
            <?php if (!empty($plans)): ?>
                <?php foreach ($plans as $plan): ?>
                    <div class="col-lg-3 col-md-6">
                        <div class="card plan-card h-100 text-center p-4 border rounded-4 shadow-sm position-relative overflow-hidden d-flex flex-column">
                            <?php if (stripos($plan['name'], 'Gold') !== false): ?>
                                <div class="position-absolute top-0 end-0 bg-primary text-white px-3 py-1 small fw-bold rounded-bottom-start">Popular</div>
                            <?php endif; ?>

                            <div class="card-body d-flex flex-column">
                                <h5 class="fw-bold <?php echo (stripos($plan['name'], 'Gold') !== false) ? 'text-primary' : 'text-muted'; ?> mb-3"><?php echo $plan['name']; ?></h5>
                                <h2 class="fw-bold display-5 mb-0 <?php echo (stripos($plan['name'], 'Gold') !== false) ? 'text-primary' : ''; ?>">₹<?php echo number_format($plan['price']); ?></h2>
                                <p class="text-muted small mb-4">+ <?php echo $plan['gst_rate']; ?>% GST</p>

                                <?php if ($plan['type'] === 'WHITE_LABEL'): ?>
                                    <div class="badge bg-info-subtle text-info mb-3 px-3 py-2 rounded-pill border border-info-subtle align-self-center">
                                        <i class="fas fa-paint-brush me-1"></i> White Label Branding
                                    </div>
                                <?php endif; ?>

                                <ul class="list-unstyled text-start small mb-4 flex-grow-1">
                                    <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i>Private Bank Loan Panel</li>
                                    <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i>Government Bank Loan Panel</li>
                                    <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i><?php echo $plan['description'] ?? 'Standard Features'; ?></li>
                                    <li class="mb-3 text-muted fst-italic"><i class="fas fa-plus-circle text-primary me-2"></i>And many more...</li>
                                </ul>

                                <div class="mt-auto w-100">
                                    <a href="<?php echo url('contact/index?subject=Interest in ' . urlencode($plan['name'])); ?>" class="btn <?php echo (stripos($plan['name'], 'Gold') !== false) ? 'btn-primary' : 'btn-outline-primary'; ?> w-100 rounded-pill">Choose Plan</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p class="text-muted">No plans available at the moment.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Products Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold display-6">Our Financial Products</h2>
            <p class="text-muted mx-auto" style="max-width: 600px;">Explore our range of high-commission financial products specially designed for Loan DSA partners.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="d-flex align-items-start p-3 bg-white rounded shadow-sm h-100">
                    <div class="flex-shrink-0 text-primary h2 me-3 fw-bold opacity-25">01</div>
                    <div>
                        <h5 class="fw-bold">Credit Card DSA</h5>
                        <p class="text-muted small mb-0">Sell credit cards from leading banks like SBI, ICICI, HDFC, Citi, RBL, etc.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex align-items-start p-3 bg-white rounded shadow-sm h-100">
                    <div class="flex-shrink-0 text-primary h2 me-3 fw-bold opacity-25">02</div>
                    <div>
                        <h5 class="fw-bold">Personal & Business Loan</h5>
                        <p class="text-muted small mb-0">Sell instant personal loans, business loans, home loans, LAP from top banks.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex align-items-start p-3 bg-white rounded shadow-sm h-100">
                    <div class="flex-shrink-0 text-primary h2 me-3 fw-bold opacity-25">03</div>
                    <div>
                        <h5 class="fw-bold">General & Life Insurance</h5>
                        <p class="text-muted small mb-0">Sell insurance plans from top companies like HDFC Ergo, ICICI Lombard, etc.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex align-items-start p-3 bg-white rounded shadow-sm h-100">
                    <div class="flex-shrink-0 text-primary h2 me-3 fw-bold opacity-25">04</div>
                    <div>
                        <h5 class="fw-bold">Demat, Mutual Funds & Forex</h5>
                        <p class="text-muted small mb-0">Open FREE DEMAT Account and sell top mutual funds and stock market products.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex align-items-start p-3 bg-white rounded shadow-sm h-100">
                    <div class="flex-shrink-0 text-primary h2 me-3 fw-bold opacity-25">05</div>
                    <div>
                        <h5 class="fw-bold">FD, RD & Gold Bonds</h5>
                        <p class="text-muted small mb-0">Open deposits accounts for your clients with top fixed deposit plans.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex align-items-start p-3 bg-white rounded shadow-sm h-100">
                    <div class="flex-shrink-0 text-primary h2 me-3 fw-bold opacity-25">06</div>
                    <div>
                        <h5 class="fw-bold">Banking Services</h5>
                        <p class="text-muted small mb-0">Offer a wide range of paperless, fully online banking services.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 text-white text-center" style="background-color: #0f172a;">
    <div class="container">
        <h2 class="fw-bold mb-3 display-6">Ready to Take Your Business to the Next Level?</h2>
        <p class="lead mb-5 opacity-75 mx-auto" style="max-width: 700px;">Incomekaro empowers businesses with powerful solutions to boost visibility and accelerate growth.</p>
        <a href="#contact" class="btn btn-outline-light btn-lg rounded-pill px-5 fw-bold hover-lift">Contact Us</a>
    </div>
</section>

<!-- Policy & Disclaimer Section (Main Site Only) -->
<?php if (!defined('IS_WHITE_LABEL') || !IS_WHITE_LABEL): ?>
<section class="py-5 bg-light border-top">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-12 mb-4">
                <h5 class="text-dark fw-bold mb-3 border-bottom pb-2 d-inline-block">Policy</h5>
                <p class="text-muted small fw-bold mb-1">Loan Approval and Rejection Bank or NBFC Policy</p>
                <p class="text-muted small" style="line-height: 1.6;">SUNGLORY SOFTWARE PRIVATE LIMITED is present in more than 27+ states of India- ANDHRA PRADESH | ASSAM | BIHAR | CHANDIGARH | CHHATTISGARH | DELHI | GUJARAT | HIMACHAL PRADESH | HARYANA | JHARKHAND | JAMMU AND KASHMIR | KARNATAKA | MAHARASHTRA | MADHYA PRADESH | MONIPUR | MEGHALAYA | ORISSA | PUNJAB | RAJASTHAN | SIKKIM | TAMIL NADU | TRIPURA | TELANGANA | UTTARAKHAND | UTTAR PRADESH | WEST BENGAL. With lots of <span class="text-danger">❤</span> from SUNGLORY SOFTWARE PRIVATE LIMITED</p>
            </div>

            <div class="col-md-6">
                <div class="alert alert-warning border-0 shadow-sm d-flex align-items-start h-100">
                    <i class="fas fa-exclamation-triangle fa-lg me-3 mt-1"></i>
                    <div>
                        <h6 class="fw-bold mb-1">Attention</h6>
                        <p class="small mb-0">SUNGLORY SOFTWARE PRIVATE LIMITED never asks any details related to debit cards, credit cards and Net Banking like CVV, OTP, SMS. If any such call or mail comes, report it to <a href="mailto:support@incomekaro.in" class="fw-bold text-dark">support@incomekaro.in</a></p>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="alert alert-warning border-0 shadow-sm d-flex align-items-start h-100">
                    <i class="fas fa-exclamation-triangle fa-lg me-3 mt-1"></i>
                    <div>
                        <h6 class="fw-bold mb-1">Attention</h6>
                        <p class="small mb-0">SUNGLORY SOFTWARE PRIVATE LIMITED doesn't charge any amount in the name of Loan Approval & Disbursal. If you get any such information, mail it to <a href="mailto:support@incomekaro.in" class="fw-bold text-dark">support@incomekaro.in</a></p>
                    </div>
                </div>
            </div>

            <div class="col-12 mt-4 text-center">
                <p class="small text-muted mb-0">CIN: <span class="fw-bold text-dark">U62013WB2025PTC276552</span></p>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<style>
    .shadow-hover { transition: all 0.3s ease; }
    .shadow-hover:hover { transform: translateY(-5px); box-shadow: 0 1rem 3rem rgba(0,0,0,.175)!important; }
    .letter-spacing-2 { letter-spacing: 2px; }
    .grayscale-hover { filter: grayscale(100%); transition: filter 0.3s; }
    .grayscale-hover:hover { filter: grayscale(0%); }
    .hover-lift:hover { transform: translateY(-3px); }

    /* Floating Balls Animation */
    .floating-ball {
        position: absolute;
        border-radius: 50%;
        z-index: 1;
        animation: float 6s ease-in-out infinite;
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }

    .ball-1 {
        width: 80px;
        height: 80px;
        background: radial-gradient(circle at 30% 30%, #6A5ACD, #483D8B);
        top: 10%;
        left: 5%;
        animation-delay: 0s;
    }

    .ball-2 {
        width: 120px;
        height: 120px;
        background: radial-gradient(circle at 30% 30%, #FF7F50, #FF4500);
        bottom: 15%;
        right: 10%;
        animation-delay: 2s;
    }

    .ball-3 {
        width: 50px;
        height: 50px;
        background: radial-gradient(circle at 30% 30%, #20B2AA, #008B8B);
        top: 20%;
        right: 40%;
        animation-delay: 4s;
    }

    @keyframes float {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-20px); }
        100% { transform: translateY(0px); }
    }
</style>

<?php view('layouts/footer'); ?>
