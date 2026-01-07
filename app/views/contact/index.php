<?php view('layouts/header', ['title' => 'Contact Us']); ?>

<!-- Hero Section -->
<section class="bg-primary text-white py-5 position-relative overflow-hidden" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container position-relative z-1 text-center">
        <h1 class="display-4 fw-bold mb-3">Get in Touch</h1>
        <p class="lead opacity-75 mb-0">We'd love to hear from you. Our team is always here to help.</p>
    </div>
    <!-- Decorative Circles -->
    <div class="position-absolute top-0 start-0 translate-middle bg-white opacity-10 rounded-circle" style="width: 300px; height: 300px;"></div>
    <div class="position-absolute bottom-0 end-0 translate-middle bg-white opacity-10 rounded-circle" style="width: 200px; height: 200px;"></div>
</section>

<div class="container my-5">
    <div class="row g-5 align-items-center">
        <!-- Left Column: Contact Info -->
        <div class="col-lg-5">
            <div class="pe-lg-4">
                <h2 class="fw-bold mb-4 text-dark">Contact Information</h2>
                <p class="text-muted mb-5">Have questions about our services or need support? Reach out to us through any of the following channels.</p>

                <div class="d-flex mb-4">
                    <div class="flex-shrink-0">
                        <div class="icon-box bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="fas fa-map-marker-alt fa-lg"></i>
                        </div>
                    </div>
                    <div class="ms-3">
                        <h5 class="fw-bold mb-1">Our Office</h5>
                        <p class="text-muted mb-0">Astra Tower, New Town,<br>Kolkata, West Bengal 700181</p>
                    </div>
                </div>

                <div class="d-flex mb-4">
                    <div class="flex-shrink-0">
                        <div class="icon-box bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="fas fa-envelope fa-lg"></i>
                        </div>
                    </div>
                    <div class="ms-3">
                        <h5 class="fw-bold mb-1">Email Us</h5>
                        <p class="text-muted mb-0">support@incomekaro.in</p>
                        <p class="text-muted mb-0">info@incomekaro.in</p>
                    </div>
                </div>

                <div class="d-flex mb-4">
                    <div class="flex-shrink-0">
                        <div class="icon-box bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="fas fa-phone-alt fa-lg"></i>
                        </div>
                    </div>
                    <div class="ms-3">
                        <h5 class="fw-bold mb-1">Call Us</h5>
                        <p class="text-muted mb-0">+91 786-4951-543</p>
                        <p class="text-muted mb-0">+91 877-7834-218</p>
                    </div>
                </div>

                <div class="mt-5">
                    <h6 class="fw-bold text-uppercase text-muted small mb-3">Follow Us</h6>
                    <div class="d-flex gap-3">
                        <a href="#" class="btn btn-outline-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="btn btn-outline-info rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="btn btn-outline-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="btn btn-outline-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Contact Form -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden h-100">
                <div class="card-body p-5">
                    <h3 class="fw-bold mb-4">Send us a Message</h3>

                    <?php flash('contact_success'); ?>
                    <?php flash('contact_error'); ?>

                    <form action="<?php echo url('contact/store'); ?>" method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Your Name" required>
                                    <label for="name">Your Name</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Your Email" required>
                                    <label for="email">Your Email</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="subject" name="subject" placeholder="Subject" required>
                                    <label for="subject">Subject</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control" id="message" name="message" placeholder="Leave a message here" style="height: 150px" required></textarea>
                                    <label for="message">Message</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-lg w-100 py-3 rounded-pill fw-bold shadow-sm">Send Message <i class="fas fa-paper-plane ms-2"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php view('layouts/footer'); ?>
