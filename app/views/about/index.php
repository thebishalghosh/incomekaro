<?php view('layouts/header', ['title' => 'About Us']); ?>

<!-- Hero Section -->
<section class="bg-primary text-white py-5 position-relative overflow-hidden" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container position-relative z-1 text-center">
        <h1 class="display-4 fw-bold mb-3">About Us</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center mb-0">
                <li class="breadcrumb-item"><a href="<?php echo url('/'); ?>" class="text-white-50 text-decoration-none">Home</a></li>
                <li class="breadcrumb-item active text-white" aria-current="page">About Us</li>
            </ol>
        </nav>
    </div>
    <!-- Decorative Circles -->
    <div class="position-absolute top-0 start-0 translate-middle bg-white opacity-10 rounded-circle" style="width: 300px; height: 300px;"></div>
    <div class="position-absolute bottom-0 end-0 translate-middle bg-white opacity-10 rounded-circle" style="width: 200px; height: 200px;"></div>
</section>

<!-- Main About Section -->
<section class="py-5">
    <div class="container my-5">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 text-center">
                <div class="position-relative d-inline-block w-100">
                    <!-- Oval Image -->
                    <img src="<?php echo asset('images/about-img.png'); ?>" alt="About IncomeKaro" class="img-fluid shadow-lg position-relative z-1" style="border-radius: 50%; width: 100%; max-width: 350px; height: auto; object-fit: cover;">

                    <!-- Decorative Elements -->
                    <div class="position-absolute top-0 start-0 translate-middle bg-warning rounded-circle opacity-25" style="width: 120px; height: 120px; z-index: 0;"></div>
                    <div class="position-absolute bottom-0 end-0 translate-middle bg-primary rounded-circle opacity-25" style="width: 180px; height: 180px; z-index: 0;"></div>
                </div>
            </div>
            <div class="col-lg-6">
                <h6 class="text-primary fw-bold text-uppercase letter-spacing-2 mb-2">Who We Are</h6>
                <h2 class="fw-bold display-6 mb-4">India's No. 1 Instant Payout Provider</h2>
                <p class="lead text-muted mb-4">
                    Incomekaro, under the inspired leadership of <strong>Pratap Mondal</strong>, was launched in the year 2024 as a Direct Sales Associate for Piramal.
                </p>
                <p class="text-muted mb-4">
                    Now it is India's No. 1 instant disbursed, instant payout provider with services in <strong>60+ cities</strong>, <strong>30+ franchises</strong>, and an excellent distribution network of over <strong>75,000+ partners</strong>. Join our family to change your life.
                </p>
                <button type="button" class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm" data-bs-toggle="modal" data-bs-target="#loginModal">Register Now</button>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="py-5 position-relative" style="background-color: #f3f4f6;">
    <div class="container my-4">
        <div class="text-center mb-5">
            <h6 class="text-primary fw-bold text-uppercase letter-spacing-2">Why Choose Us</h6>
            <h2 class="fw-bold display-6">Experience the Advantage</h2>
            <p class="text-muted mx-auto" style="max-width: 700px;">Why We're the Right Choice for your financial journey.</p>
        </div>

        <div class="row g-4">
            <!-- Transparency Card -->
            <div class="col-lg-6">
                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                    <div class="card-body p-5 d-flex flex-column justify-content-center">
                        <div class="d-flex align-items-center mb-4">
                            <div class="icon-box bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;">
                                <i class="fas fa-hand-holding-usd fa-lg"></i>
                            </div>
                            <h3 class="fw-bold mb-0">Transparency & Trust</h3>
                        </div>
                        <p class="text-muted lead mb-0">
                            IncomeKaro doesn't charge any amount in the name of Loan Approval, Loan Disbursal, or Loan Processing Fees in cash or in kind. We believe in complete transparency with our partners and customers.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Warning/Disclaimer Card -->
            <div class="col-lg-6">
                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden bg-danger text-white position-relative">
                    <!-- Background Pattern -->
                    <div class="position-absolute top-0 end-0 p-3 opacity-25">
                        <i class="fas fa-exclamation-triangle fa-8x"></i>
                    </div>

                    <div class="card-body p-5 position-relative z-1">
                        <div class="d-flex align-items-center mb-4">
                            <div class="icon-box bg-white text-danger rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;">
                                <i class="fas fa-shield-alt fa-lg"></i>
                            </div>
                            <h3 class="fw-bold mb-0">Important Note</h3>
                        </div>
                        <p class="mb-4 opacity-90">
                            IncomeKaro <strong>doesn't deal</strong> in documents like Death Certificates, Birth Certificates, Driving License, Passport, or Voter Card.
                        </p>
                        <p class="small opacity-75 mb-0 border-top border-white pt-3" style="border-top-style: dashed !important;">
                            If found or came to your notice then please complain on the given number at the website or email us at <a href="mailto:support@incomekaro.in" class="text-white fw-bold text-decoration-underline">support@incomekaro.in</a>.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mission, Vision, Values -->
<section class="py-5">
    <div class="container my-5">
        <div class="row g-4">
            <!-- Value -->
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-hover p-4 text-center bg-white rounded-4">
                    <div class="card-body">
                        <div class="icon-box bg-primary-subtle text-primary rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                            <i class="fas fa-gem fa-2x"></i>
                        </div>
                        <h6 class="text-uppercase text-muted small fw-bold mb-2">Our Value</h6>
                        <h4 class="fw-bold mb-3">Solutions, Success & Service</h4>
                        <p class="text-muted small">We are dedicated to delivering customized solutions, driving meaningful success, and providing exceptional service that exceeds expectations.</p>
                    </div>
                </div>
            </div>

            <!-- Mission -->
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-hover p-4 text-center bg-white rounded-4">
                    <div class="card-body">
                        <div class="icon-box bg-success-subtle text-success rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                            <i class="fas fa-bullseye fa-2x"></i>
                        </div>
                        <h6 class="text-uppercase text-muted small fw-bold mb-2">Our Mission</h6>
                        <h4 class="fw-bold mb-3">Driving Innovation</h4>
                        <p class="text-muted small">Fostering Innovation and Empowering Change — we work passionately to bring transformative ideas to life, nurturing talent, and contributing to societal betterment through cutting-edge solutions.</p>
                    </div>
                </div>
            </div>

            <!-- Vision -->
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-hover p-4 text-center bg-white rounded-4">
                    <div class="card-body">
                        <div class="icon-box bg-info-subtle text-info rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                            <i class="fas fa-eye fa-2x"></i>
                        </div>
                        <h6 class="text-uppercase text-muted small fw-bold mb-2">Our Vision</h6>
                        <h4 class="fw-bold mb-3">Empowering Financial Futures</h4>
                        <p class="text-muted small">Revolutionizing the financial landscape with advanced tools — we empower individuals and businesses to make smarter decisions, drive growth, and shape a more inclusive economic future.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 text-white text-center position-relative overflow-hidden" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">

    <!-- Animated 3D Balls -->
    <div class="floating-ball ball-1"></div>
    <div class="floating-ball ball-2"></div>
    <div class="floating-ball ball-3"></div>

    <div class="container position-relative z-1">
        <h2 class="fw-bold mb-3 display-5">Ready to Take Your Business to the Next Level?</h2>
        <p class="lead mb-5 opacity-90 mx-auto" style="max-width: 700px;">Incomekaro empowers businesses with powerful SEO solutions to boost visibility, attract targeted traffic, and accelerate growth.</p>
        <a href="<?php echo url('contact/index'); ?>" class="btn btn-light btn-lg rounded-pill px-5 fw-bold shadow-lg text-primary">Contact Us</a>
    </div>
</section>

<style>
    .shadow-hover { transition: all 0.3s ease; }
    .shadow-hover:hover { transform: translateY(-5px); box-shadow: 0 1rem 3rem rgba(0,0,0,.175)!important; }
    .letter-spacing-2 { letter-spacing: 2px; }

    /* Floating Balls Animation */
    .floating-ball {
        position: absolute;
        border-radius: 50%;
        z-index: 0;
        animation: float 6s ease-in-out infinite;
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        opacity: 0.6;
    }

    .ball-1 {
        width: 80px;
        height: 80px;
        background: radial-gradient(circle at 30% 30%, rgba(255,255,255,0.8), rgba(255,255,255,0.1));
        top: 10%;
        left: 5%;
        animation-delay: 0s;
    }

    .ball-2 {
        width: 120px;
        height: 120px;
        background: radial-gradient(circle at 30% 30%, rgba(255,255,255,0.8), rgba(255,255,255,0.1));
        bottom: 15%;
        right: 10%;
        animation-delay: 2s;
    }

    .ball-3 {
        width: 50px;
        height: 50px;
        background: radial-gradient(circle at 30% 30%, rgba(255,255,255,0.8), rgba(255,255,255,0.1));
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
