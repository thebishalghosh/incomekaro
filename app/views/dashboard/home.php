<?php view('layouts/header', ['title' => 'Home']); ?>

<!-- Hero Section -->
<section class="hero-section text-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <span class="badge bg-success mb-3">India’s No. 1 Fast Payout Provider</span>
                <h1 class="display-4 fw-bold mb-4">Welcome Incomekaro Instant Disbursed</h1>
                <p class="lead text-muted mb-5">Powered by INCOMEKARO and trusted by over 7,500 businesses across India, we deliver reliable solutions tailored to your business needs.</p>
                <div class="d-flex justify-content-center gap-3">
                    <button type="button" class="btn btn-primary btn-lg px-5" data-bs-toggle="modal" data-bs-target="#loginModal">Apply Now</button>
                    <a href="#contact" class="btn btn-outline-dark btn-lg px-5">Contact Now</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Trusted By Section -->
<section class="py-5 bg-white border-bottom">
    <div class="container text-center">
        <p class="text-muted fw-bold text-uppercase mb-4">Trusted by Top Companies</p>
        <div class="d-flex justify-content-center flex-wrap align-items-center gap-5">
            <!-- Placeholder for Logos -->
            <h3 class="text-muted">HDFC Bank</h3>
            <h3 class="text-muted">SBI</h3>
            <h3 class="text-muted">ICICI Bank</h3>
            <h3 class="text-muted">Axis Bank</h3>
            <h3 class="text-muted">Kotak</h3>
        </div>
    </div>
</section>

<!-- Steps Section -->
<section class="py-5 bg-light" id="about">
    <div class="container">
        <div class="text-center mb-5">
            <h6 class="text-primary fw-bold">WE PROVIDE YOU THE BEST DSA CRM SOFTWARE</h6>
            <h2 class="fw-bold">Streamline Your Loan Business</h2>
            <p class="text-muted">We provide the best DSA CRM software designed to boost productivity and manage leads effortlessly.</p>
        </div>
        <div class="row g-4 text-center">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm p-4">
                    <div class="card-body">
                        <div class="feature-icon"><i class="fas fa-user-plus"></i></div>
                        <h4 class="card-title">1. APPLY FOR REGISTRATION ✔</h4>
                        <p class="card-text text-muted">Register your account by providing basic details.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm p-4">
                    <div class="card-body">
                        <div class="feature-icon"><i class="fas fa-check-circle"></i></div>
                        <h4 class="card-title">2. CHOOSE YOUR PLAN (ACTIVED) ✔</h4>
                        <p class="card-text text-muted">Choose the best plan that suits your business needs.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm p-4">
                    <div class="card-body">
                        <div class="feature-icon"><i class="fas fa-chart-line"></i></div>
                        <h4 class="card-title">3. GROW YOUR BUSINESS ✔</h4>
                        <p class="card-text text-muted">Start growing your business with our CRM tools.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center mt-5">
            <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#loginModal">Join Now</button>
        </div>
    </div>
</section>

<!-- Plans Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">We Help You to Incomekaro</h2>
            <p class="text-muted">Get lifetime access with our Incomekaro subscription plan. Enjoy zero monthly fees and full premium features — forever.</p>
        </div>
        <div class="row g-4">
            <!-- Silver Plan -->
            <div class="col-lg-3 col-md-6">
                <div class="card plan-card h-100 text-center p-3">
                    <div class="card-header bg-transparent border-0">
                        <h4 class="fw-bold">Silver Plan</h4>
                        <p class="text-muted small">Starter</p>
                    </div>
                    <div class="card-body">
                        <h2 class="fw-bold">₹2499.00</h2>
                        <p class="text-muted small">(Incl. GST: 18%)</p>
                        <hr>
                        <ul class="list-unstyled text-start small">
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Private Bank Loan Panel</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Government Bank Loan Panel</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Small businesses / Startups</li>
                        </ul>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <a href="#" class="btn btn-outline-primary w-100">CHOOSE PLAN</a>
                    </div>
                </div>
            </div>

            <!-- Gold Plan -->
            <div class="col-lg-3 col-md-6">
                <div class="card plan-card h-100 text-center p-3 border-primary">
                    <div class="card-header bg-transparent border-0">
                        <h4 class="fw-bold text-primary">Gold Plan</h4>
                        <p class="text-muted small">Growth</p>
                    </div>
                    <div class="card-body">
                        <h2 class="fw-bold text-primary">₹9999.00</h2>
                        <p class="text-muted small">(Incl. GST: 18%)</p>
                        <hr>
                        <ul class="list-unstyled text-start small">
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Private Bank Loan Panel</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Government Bank Loan Panel</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Expanding businesses</li>
                        </ul>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <a href="#" class="btn btn-primary w-100">CHOOSE PLAN</a>
                    </div>
                </div>
            </div>

            <!-- Platinum Plan -->
            <div class="col-lg-3 col-md-6">
                <div class="card plan-card h-100 text-center p-3">
                    <div class="card-header bg-transparent border-0">
                        <h4 class="fw-bold">Platinum Plan</h4>
                        <p class="text-muted small">Pro</p>
                    </div>
                    <div class="card-body">
                        <h2 class="fw-bold">₹14999.00</h2>
                        <p class="text-muted small">(Incl. GST: 18%)</p>
                        <hr>
                        <ul class="list-unstyled text-start small">
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Private Bank Loan Panel</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Government Bank Loan Panel</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Large businesses / Professionals</li>
                        </ul>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <a href="#" class="btn btn-outline-primary w-100">CHOOSE PLAN</a>
                    </div>
                </div>
            </div>

            <!-- Franchise -->
            <div class="col-lg-3 col-md-6">
                <div class="card plan-card h-100 text-center p-3 bg-dark text-white">
                    <div class="card-header bg-transparent border-0">
                        <h4 class="fw-bold">FRANCHISE</h4>
                        <p class="text-white-50 small">Enterprise</p>
                    </div>
                    <div class="card-body">
                        <h2 class="fw-bold">₹70000.00</h2>
                        <p class="text-white-50 small">(Incl. GST: 18%)</p>
                        <hr class="border-light">
                        <ul class="list-unstyled text-start small">
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Private Bank Loan Panel</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Government Bank Loan Panel</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Full Customization</li>
                        </ul>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <a href="#" class="btn btn-light w-100">CHOOSE PLAN</a>
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
            <h2 class="fw-bold">Our Financial Products</h2>
            <p class="text-muted">Explore our range of high-commission financial products specially designed for Loan DSA partners.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="d-flex align-items-start">
                    <div class="flex-shrink-0 text-primary h2 me-3">01</div>
                    <div>
                        <h5 class="fw-bold">Credit Card DSA</h5>
                        <p class="text-muted small">Sell credit cards from leading banks like SBI, ICICI, HDFC, Citi, RBL, etc.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex align-items-start">
                    <div class="flex-shrink-0 text-primary h2 me-3">02</div>
                    <div>
                        <h5 class="fw-bold">Personal & Business Loan DSA</h5>
                        <p class="text-muted small">Sell instant personal loans, business loans, home loans, LAP from top banks.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex align-items-start">
                    <div class="flex-shrink-0 text-primary h2 me-3">03</div>
                    <div>
                        <h5 class="fw-bold">General & Life Insurance</h5>
                        <p class="text-muted small">Sell insurance plans from top companies like HDFC Ergo, ICICI Lombard, etc.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex align-items-start">
                    <div class="flex-shrink-0 text-primary h2 me-3">04</div>
                    <div>
                        <h5 class="fw-bold">Demat, Mutual Funds & Forex</h5>
                        <p class="text-muted small">Open FREE DEMAT Account and sell top mutual funds and stock market products.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex align-items-start">
                    <div class="flex-shrink-0 text-primary h2 me-3">05</div>
                    <div>
                        <h5 class="fw-bold">FD, RD & Gold Bonds</h5>
                        <p class="text-muted small">Open deposits accounts for your clients with top fixed deposit plans.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex align-items-start">
                    <div class="flex-shrink-0 text-primary h2 me-3">06</div>
                    <div>
                        <h5 class="fw-bold">Banking Services</h5>
                        <p class="text-muted small">Offer a wide range of paperless, fully online banking services.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 bg-primary text-white text-center">
    <div class="container">
        <h2 class="fw-bold mb-3">Ready to Take to your Business the Next Level</h2>
        <p class="lead mb-4">Incomekaro empowers businesses with powerful SEO solutions to boost visibility and accelerate growth.</p>
        <a href="#contact" class="btn btn-light btn-lg">Contact Us</a>
    </div>
</section>

<?php view('layouts/footer'); ?>
