<?php view('layouts/header', ['title' => 'Home']); ?>

<?php
    // $landing is passed from the controller. No need to fetch it from $wl.
    // If $landing is empty (e.g. not set in DB), we use defaults.

    // Hero Section
    $hero_title = !empty($landing['hero']['title']) ? $landing['hero']['title'] : 'Welcome to ' . $wl['company_name'];
    $hero_text = !empty($landing['hero']['text']) ? $landing['hero']['text'] : 'Your trusted partner for financial growth.';
    $hero_image = !empty($landing['hero']['image']) ? asset($landing['hero']['image']) : asset('images/hero.png');

    // About Section
    $about_title = !empty($landing['about']['title']) ? $landing['about']['title'] : 'Who We Are';
    $about_text = !empty($landing['about']['text']) ? $landing['about']['text'] : 'We provide the best financial solutions.';
    $about_image = !empty($landing['about']['image']) ? asset($landing['about']['image']) : asset('images/about-img.png');

    // Contact Info
    $contact_email = $wl['support_email'];
    $contact_phone = !empty($landing['contact_phone']) ? $landing['contact_phone'] : '';
    $contact_address = !empty($landing['contact_address']) ? $landing['contact_address'] : '';

    // Products
    $products = $landing['products'] ?? [];
?>

<!-- Hero Section -->
<section class="hero-section position-relative overflow-hidden" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%); padding: 120px 0 150px;">

    <!-- Animated Background Elements -->
    <div class="position-absolute top-0 start-0 w-100 h-100 overflow-hidden" style="z-index: 1;">
        <div class="floating-shape shape-1"></div>
        <div class="floating-shape shape-2"></div>
        <div class="floating-shape shape-3"></div>
        <div class="floating-shape shape-4"></div>
    </div>

    <div class="container position-relative" style="z-index: 2;">
        <div class="row align-items-center">
            <div class="col-lg-6 text-center text-lg-start mb-5 mb-lg-0">
                <h1 class="display-4 fw-bold text-white mb-4 lh-tight animate-up">
                    <?php echo $hero_title; ?>
                </h1>
                <p class="lead text-white-50 mb-5 pe-lg-5 animate-up delay-100">
                    <?php echo nl2br($hero_text); ?>
                </p>
                <div class="d-flex justify-content-center justify-content-lg-start gap-3 animate-up delay-200">
                    <button type="button" class="btn btn-light btn-lg px-5 shadow-lg rounded-pill fw-bold text-primary" data-bs-toggle="modal" data-bs-target="#loginModal">
                        Get Started <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </div>
            </div>
            <div class="col-lg-6 text-center animate-in-right">
                <div class="position-relative d-inline-block">
                    <!-- Glow Effect behind image -->
                    <div class="position-absolute top-50 start-50 translate-middle bg-white opacity-25 rounded-circle" style="width: 120%; height: 120%; filter: blur(60px);"></div>
                    <!-- Reduced max-height to 450px -->
                    <img src="<?php echo $hero_image; ?>" alt="Hero Image" class="img-fluid rounded-4 shadow-lg position-relative" style="max-height: 450px; object-fit: cover; transform: perspective(1000px) rotateY(-10deg) rotateX(5deg);">
                </div>
            </div>
        </div>
    </div>

    <!-- Wave Divider -->
    <div class="position-absolute bottom-0 start-0 w-100" style="line-height: 0;">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
            <path fill="#ffffff" fill-opacity="1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,112C672,96,768,96,864,112C960,128,1056,160,1152,160C1248,160,1344,128,1392,112L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
        </svg>
    </div>
</section>

<!-- About Section -->
<section class="py-5 bg-white" id="about">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-5 mb-lg-0 reveal-left">
                <div class="position-relative">
                    <div class="position-absolute top-0 start-0 translate-middle bg-primary opacity-10 rounded-circle" style="width: 200px; height: 200px; z-index: 0;"></div>
                    <div class="position-absolute bottom-0 end-0 translate-middle bg-secondary opacity-10 rounded-circle" style="width: 150px; height: 150px; z-index: 0;"></div>
                    <!-- Reduced max-height to 450px -->
                    <img src="<?php echo $about_image; ?>" alt="About Us" class="img-fluid rounded-4 shadow-lg position-relative z-1" style="border-radius: 20px; width: 100%; max-height: 450px; object-fit: cover;">
                </div>
            </div>
            <div class="col-lg-6 ps-lg-5 reveal-right">
                <h6 class="text-primary fw-bold text-uppercase mb-2 letter-spacing-2">About Us</h6>
                <h2 class="fw-bold display-6 mb-4"><?php echo $about_title; ?></h2>
                <p class="text-muted lead mb-4" style="line-height: 1.8;">
                    <?php echo nl2br($about_text); ?>
                </p>
                <div class="row g-4 mt-2">
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center p-3 rounded-3 bg-light-custom">
                            <div class="icon-box bg-white text-primary rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 50px; height: 50px;">
                                <i class="fas fa-check"></i>
                            </div>
                            <h6 class="mb-0 fw-bold">Trusted Service</h6>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center p-3 rounded-3 bg-light-custom">
                            <div class="icon-box bg-white text-primary rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 50px; height: 50px;">
                                <i class="fas fa-headset"></i>
                            </div>
                            <h6 class="mb-0 fw-bold">Expert Support</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Products Section -->
<?php if (!empty($products)): ?>
<section class="py-5 position-relative" style="background-color: #eef0f4;">
    <div class="container py-5">
        <div class="text-center mb-5 reveal-up">
            <h6 class="text-primary fw-bold text-uppercase mb-2 letter-spacing-2">Our Offerings</h6>
            <h2 class="fw-bold display-5">Our Financial Products</h2>
            <p class="text-muted mx-auto" style="max-width: 600px;">Explore our range of high-commission financial products specially designed for our partners.</p>
        </div>

        <div class="row g-4">
            <?php foreach ($products as $index => $prod): ?>
                <?php if (!empty($prod['title'])): ?>
                <div class="col-md-6 col-lg-4 reveal-up" style="transition-delay: <?php echo $index * 100; ?>ms;">
                    <div class="card h-100 border-0 shadow-sm hover-lift transition-all overflow-hidden group">
                        <div class="card-body p-4 position-relative z-1">
                            <div class="d-flex align-items-center mb-4">
                                <div class="number-box bg-primary-subtle text-primary rounded-3 d-flex align-items-center justify-content-center me-3 fw-bold fs-4" style="width: 60px; height: 60px;">
                                    <?php echo sprintf("%02d", $index + 1); ?>
                                </div>
                                <h5 class="fw-bold mb-0 lh-sm"><?php echo $prod['title']; ?></h5>
                            </div>
                            <p class="text-muted mb-0"><?php echo $prod['desc']; ?></p>
                        </div>
                        <!-- Hover Gradient -->
                        <div class="position-absolute top-0 start-0 w-100 h-100 bg-gradient-primary opacity-0 group-hover-opacity transition-all" style="z-index: 0;"></div>
                    </div>
                </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Pricing Section (Dynamic) -->
<?php if (!empty($plans)): ?>
<section class="py-5 bg-white" id="pricing">
    <div class="container py-5">
        <div class="text-center mb-5 reveal-up">
            <h6 class="text-primary fw-bold text-uppercase mb-2 letter-spacing-2">Pricing Plans</h6>
            <h2 class="fw-bold display-5">Choose Your Plan</h2>
            <p class="text-muted mx-auto" style="max-width: 600px;">Select the best plan to kickstart your journey with us.</p>
        </div>

        <div class="row g-4 justify-content-center">
            <?php foreach ($plans as $plan): ?>
                <div class="col-lg-3 col-md-6 reveal-up">
                    <div class="card h-100 border-0 shadow-lg rounded-4 overflow-hidden hover-scale transition-all d-flex flex-column">
                        <div class="card-header bg-white border-0 pt-4 pb-0 text-center">
                            <h5 class="fw-bold text-uppercase text-muted small letter-spacing-1 mb-2"><?php echo $plan['name']; ?></h5>
                            <h2 class="display-5 fw-bold text-dark mb-0">₹<?php echo number_format($plan['price']); ?></h2>
                            <small class="text-muted">+ <?php echo $plan['gst_rate']; ?>% GST</small>
                        </div>
                        <div class="card-body p-4 d-flex flex-column">
                            <ul class="list-unstyled mb-4 flex-grow-1">
                                <?php
                                    // Parse Description
                                    $desc_points = explode('|', $plan['description']);
                                    foreach ($desc_points as $point):
                                        $point = trim($point);
                                        if (!empty($point)):
                                ?>
                                <li class="mb-3 d-flex align-items-start text-muted">
                                    <i class="fas fa-check-circle text-success me-2 mt-1"></i> <span><?php echo $point; ?></span>
                                </li>
                                <?php
                                        endif;
                                    endforeach;
                                ?>
                            </ul>
                            <div class="mt-auto w-100">
                                <a href="#contact" data-subject="Interest in <?php echo htmlspecialchars($plan['name']); ?>" class="btn btn-outline-primary rounded-pill fw-bold py-2 w-100 choose-plan-btn">Choose Plan</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Contact Section -->
<section class="py-5 bg-white" id="contact">
    <div class="container py-5">
        <div class="card border-0 shadow-lg rounded-5 overflow-hidden reveal-up">
            <div class="row g-0">
                <!-- Left Side: Info -->
                <div class="col-lg-5 text-white p-5 d-flex flex-column justify-content-center position-relative" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);">
                    <!-- Subtle Decorative Elements -->
                    <div class="position-absolute top-0 end-0 translate-middle-y bg-white opacity-05 rounded-circle" style="width: 300px; height: 300px; margin-right: -100px; filter: blur(40px);"></div>
                    <div class="position-absolute bottom-0 start-0 translate-middle-y bg-white opacity-05 rounded-circle" style="width: 200px; height: 200px; margin-left: -50px; filter: blur(30px);"></div>

                    <div class="position-relative z-1">
                        <h2 class="fw-bold mb-4 display-6">Ready to Start Your Journey?</h2>
                        <p class="lead mb-5 opacity-75">Contact our support team for any assistance or inquiries.</p>

                        <div class="d-flex align-items-center mb-4 p-3 rounded-3 bg-white bg-opacity-10 border border-white border-opacity-10 backdrop-blur">
                            <div class="icon-box bg-white text-primary rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 45px; height: 45px;">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div>
                                <small class="text-white-50 text-uppercase fw-bold letter-spacing-1" style="font-size: 0.7rem;">Email Us</small>
                                <div class="fs-6 fw-bold"><?php echo $contact_email; ?></div>
                            </div>
                        </div>

                        <?php if (!empty($contact_phone)): ?>
                        <div class="d-flex align-items-center p-3 rounded-3 bg-white bg-opacity-10 border border-white border-opacity-10 backdrop-blur mb-4">
                            <div class="icon-box bg-white text-primary rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 45px; height: 45px;">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div>
                                <small class="text-white-50 text-uppercase fw-bold letter-spacing-1" style="font-size: 0.7rem;">Call Us</small>
                                <div class="fs-6 fw-bold"><?php echo $contact_phone; ?></div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($contact_address)): ?>
                        <div class="d-flex align-items-center p-3 rounded-3 bg-white bg-opacity-10 border border-white border-opacity-10 backdrop-blur">
                            <div class="icon-box bg-white text-primary rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 45px; height: 45px;">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <small class="text-white-50 text-uppercase fw-bold letter-spacing-1" style="font-size: 0.7rem;">Visit Us</small>
                                <div class="fs-6 fw-bold"><?php echo $contact_address; ?></div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Right Side: Form -->
                <div class="col-lg-7 bg-white p-5">
                    <h3 class="fw-bold text-dark mb-4">Send us a Message</h3>

                    <?php flash('contact_success'); ?>
                    <?php flash('contact_error'); ?>

                    <form action="<?php echo url('contact/store'); ?>" method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control bg-light border" id="name" name="name" placeholder="Your Name" required>
                                    <label for="name">Your Name</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="email" class="form-control bg-light border" id="email" name="email" placeholder="Your Email" required>
                                    <label for="email">Your Email</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control bg-light border" id="subject" name="subject" placeholder="Subject" required>
                                    <label for="subject">Subject</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control bg-light border" id="message" name="message" placeholder="Message" style="height: 150px" required></textarea>
                                    <label for="message">Message</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-lg px-5 w-100 rounded-pill shadow-sm">Send Message</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    /* Animations */
    @keyframes float {
        0% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(5deg); }
        100% { transform: translateY(0px) rotate(0deg); }
    }

    .floating-shape {
        position: absolute;
        border-radius: 50%;
        opacity: 0.1;
        background: white;
        animation: float 8s ease-in-out infinite;
    }

    .shape-1 { width: 300px; height: 300px; top: -50px; left: -50px; animation-delay: 0s; }
    .shape-2 { width: 150px; height: 150px; bottom: 100px; right: 10%; animation-delay: 2s; }
    .shape-3 { width: 80px; height: 80px; top: 20%; right: 20%; animation-delay: 4s; }
    .shape-4 { width: 200px; height: 200px; bottom: -50px; left: 30%; animation-delay: 1s; }

    .hover-lift:hover {
        transform: translateY(-10px);
        box-shadow: 0 1rem 3rem rgba(0,0,0,.1)!important;
    }

    .transition-all { transition: all 0.4s ease; }
    .letter-spacing-2 { letter-spacing: 2px; }
    .backdrop-blur { backdrop-filter: blur(10px); }
    .opacity-05 { opacity: 0.05; }

    /* Custom Backgrounds */
    .bg-light-custom { background-color: #f3f4f6; }

    /* Text Animations */
    .animate-up { animation: fadeInUp 0.8s ease-out forwards; opacity: 0; transform: translateY(20px); }
    .delay-100 { animation-delay: 0.1s; }
    .delay-200 { animation-delay: 0.2s; }

    @keyframes fadeInUp {
        to { opacity: 1; transform: translateY(0); }
    }

    /* Scroll Reveal Classes */
    .reveal-up, .reveal-left, .reveal-right {
        opacity: 0;
        transition: all 0.8s ease-out;
    }
    .reveal-up { transform: translateY(30px); }
    .reveal-left { transform: translateX(-30px); }
    .reveal-right { transform: translateX(30px); }

    .reveal-active {
        opacity: 1;
        transform: translate(0, 0);
    }
</style>

<script>
    // Scroll Reveal Script
    document.addEventListener('DOMContentLoaded', function() {
        const reveals = document.querySelectorAll('.reveal-up, .reveal-left, .reveal-right');

        const revealOnScroll = function() {
            const windowHeight = window.innerHeight;
            const elementVisible = 100;

            reveals.forEach((reveal) => {
                const elementTop = reveal.getBoundingClientRect().top;
                if (elementTop < windowHeight - elementVisible) {
                    reveal.classList.add('reveal-active');
                }
            });
        };

        window.addEventListener('scroll', revealOnScroll);
        // Trigger once on load
        revealOnScroll();

        // Handle Choose Plan Click
        document.querySelectorAll('.choose-plan-btn').forEach(button => {
            button.addEventListener('click', function() {
                const subject = this.getAttribute('data-subject');
                const subjectInput = document.getElementById('subject');
                if (subjectInput) {
                    subjectInput.value = subject;
                }
            });
        });
    });
</script>

<?php view('layouts/footer'); ?>
