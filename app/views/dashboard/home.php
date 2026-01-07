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
        <div class="row g-4">
            <!-- Silver Plan -->
            <div class="col-lg-3 col-md-6">
                <div class="card plan-card h-100 text-center p-4 border rounded-4 shadow-sm position-relative overflow-hidden">
                    <div class="card-body">
                        <h5 class="fw-bold text-muted mb-3">Silver Plan</h5>
                        <h2 class="fw-bold display-5 mb-0">₹2499</h2>
                        <p class="text-muted small mb-4">+ 18% GST</p>
                        <ul class="list-unstyled text-start small mb-4">
                            <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i>Private Bank Loan Panel</li>
                            <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i>Government Bank Loan Panel</li>
                            <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i>Small businesses / Startups</li>
                        </ul>
                        <a href="#" class="btn btn-outline-primary w-100 rounded-pill">Choose Plan</a>
                    </div>
                </div>
            </div>

            <!-- Gold Plan -->
            <div class="col-lg-3 col-md-6">
                <div class="card plan-card h-100 text-center p-4 border-primary border-2 rounded-4 shadow position-relative overflow-hidden">
                    <div class="position-absolute top-0 end-0 bg-primary text-white px-3 py-1 small fw-bold rounded-bottom-start">Popular</div>
                    <div class="card-body">
                        <h5 class="fw-bold text-primary mb-3">Gold Plan</h5>
                        <h2 class="fw-bold display-5 mb-0 text-primary">₹9999</h2>
                        <p class="text-muted small mb-4">+ 18% GST</p>
                        <ul class="list-unstyled text-start small mb-4">
                            <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i>Private Bank Loan Panel</li>
                            <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i>Government Bank Loan Panel</li>
                            <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i>Expanding businesses</li>
                        </ul>
                        <a href="#" class="btn btn-primary w-100 rounded-pill">Choose Plan</a>
                    </div>
                </div>
            </div>

            <!-- Platinum Plan -->
            <div class="col-lg-3 col-md-6">
                <div class="card plan-card h-100 text-center p-4 border rounded-4 shadow-sm position-relative overflow-hidden">
                    <div class="card-body">
                        <h5 class="fw-bold text-muted mb-3">Platinum Plan</h5>
                        <h2 class="fw-bold display-5 mb-0">₹14999</h2>
                        <p class="text-muted small mb-4">+ 18% GST</p>
                        <ul class="list-unstyled text-start small mb-4">
                            <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i>Private Bank Loan Panel</li>
                            <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i>Government Bank Loan Panel</li>
                            <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i>Large businesses / Professionals</li>
                        </ul>
                        <a href="#" class="btn btn-outline-primary w-100 rounded-pill">Choose Plan</a>
                    </div>
                </div>
            </div>

            <!-- Franchise -->
            <div class="col-lg-3 col-md-6">
                <div class="card plan-card h-100 text-center p-4 bg-dark text-white rounded-4 shadow-lg position-relative overflow-hidden">
                    <div class="card-body">
                        <h5 class="fw-bold text-white-50 mb-3">FRANCHISE</h5>
                        <h2 class="fw-bold display-5 mb-0">₹70000</h2>
                        <p class="text-white-50 small mb-4">+ 18% GST</p>
                        <ul class="list-unstyled text-start small mb-4">
                            <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i>Private Bank Loan Panel</li>
                            <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i>Government Bank Loan Panel</li>
                            <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i>Full Customization</li>
                        </ul>
                        <a href="#" class="btn btn-light w-100 rounded-pill">Choose Plan</a>
                    </div>
                </div>
            </div>
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
<section class="py-5 bg-primary text-white text-center">
    <div class="container">
        <h2 class="fw-bold mb-3">Ready to Take Your Business to the Next Level?</h2>
        <p class="lead mb-4 opacity-75">Incomekaro empowers businesses with powerful solutions to boost visibility and accelerate growth.</p>
        <a href="#contact" class="btn btn-light btn-lg rounded-pill px-5 fw-bold text-primary">Contact Us</a>
    </div>
</section>

<style>
    .shadow-hover { transition: all 0.3s ease; }
    .shadow-hover:hover { transform: translateY(-5px); box-shadow: 0 1rem 3rem rgba(0,0,0,.175)!important; }
    .letter-spacing-2 { letter-spacing: 2px; }
    .grayscale-hover { filter: grayscale(100%); transition: filter 0.3s; }
    .grayscale-hover:hover { filter: grayscale(0%); }

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
