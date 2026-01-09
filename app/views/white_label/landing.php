<?php view('layouts/header', ['title' => 'Home']); ?>

<?php
    $landing = $wl['landing_data'] ?? [];
    $hero_title = $landing['hero_title'] ?? 'Welcome to ' . $wl['company_name'];
    $hero_text = $landing['hero_text'] ?? 'Your trusted partner for financial growth.';
    $about_text = $landing['about_text'] ?? 'We provide the best financial solutions.';
    $contact_email = $landing['contact_email'] ?? $wl['support_email'];
    $contact_phone = $landing['contact_phone'] ?? '';
?>

<!-- Hero Section -->
<section class="hero-section position-relative overflow-hidden" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); padding: 100px 0;">
    <div class="container position-relative" style="z-index: 2;">
        <div class="row align-items-center">
            <div class="col-lg-6 text-center text-lg-start mb-5 mb-lg-0">
                <h1 class="display-4 fw-bold text-dark mb-4 lh-tight">
                    <?php echo $hero_title; ?>
                </h1>
                <p class="lead text-muted mb-5 pe-lg-5">
                    <?php echo $hero_text; ?>
                </p>
                <div class="d-flex justify-content-center justify-content-lg-start gap-3">
                    <button type="button" class="btn btn-primary btn-lg px-5 shadow-lg rounded-pill btn-login" data-bs-toggle="modal" data-bs-target="#loginModal">
                        Get Started <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <!-- Placeholder for WL Hero Image if they have one, or default -->
                <img src="<?php echo asset('images/hero.png'); ?>" alt="Hero Image" class="img-fluid rounded-4 shadow-lg" style="max-height: 400px;">
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <img src="<?php echo asset('images/about-img.png'); ?>" alt="About Us" class="img-fluid rounded-4 shadow" style="border-radius: 20px;">
            </div>
            <div class="col-lg-6">
                <h6 class="text-primary fw-bold text-uppercase mb-2">About Us</h6>
                <h2 class="fw-bold mb-4">Who We Are</h2>
                <p class="text-muted lead mb-4">
                    <?php echo $about_text; ?>
                </p>
                <div class="row g-4 mt-2">
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                <i class="fas fa-check"></i>
                            </div>
                            <h6 class="mb-0 fw-bold">Trusted Service</h6>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
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

<!-- Contact Section -->
<section class="py-5 bg-light" id="contact">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Contact Us</h2>
            <p class="text-muted">We are here to help you.</p>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-5 text-center">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <i class="fas fa-envelope fa-2x text-primary mb-3"></i>
                                    <h5 class="fw-bold">Email</h5>
                                    <p class="text-muted mb-0"><?php echo $contact_email; ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <i class="fas fa-phone-alt fa-2x text-primary mb-3"></i>
                                    <h5 class="fw-bold">Phone</h5>
                                    <p class="text-muted mb-0"><?php echo $contact_phone ?: 'N/A'; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php view('layouts/footer'); ?>
