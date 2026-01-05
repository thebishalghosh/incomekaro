<?php view('layouts/partner_header', ['title' => 'New ' . $service['name']]); ?>

<div class="container-fluid">
    <div class="text-center mb-5 pt-4">
        <h1 class="fw-bold display-5 text-dark">New Application: <span class="text-primary"><?php echo $service['name']; ?></span></h1>
        <p class="lead text-muted">Please fill out the client's details for tax services.</p>
    </div>

    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-body p-4 p-md-5">
            <form action="<?php echo url('application/store'); ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="service_id" value="<?php echo $service['id']; ?>">

                <!-- Personal Information -->
                <fieldset class="mb-4 p-4 rounded-3" style="background-color: var(--accent-color);">
                    <legend class="h5 fw-bold text-primary mb-4 border-bottom pb-2">Personal Information</legend>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="first_name" class="form-label fw-bold">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="customer[first_name]" required>
                        </div>
                        <div class="col-md-4">
                            <label for="middle_name" class="form-label fw-bold">Middle Name</label>
                            <input type="text" class="form-control" id="middle_name" name="meta[middle_name]">
                        </div>
                        <div class="col-md-4">
                            <label for="last_name" class="form-label fw-bold">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="customer[last_name]" required>
                        </div>
                        <div class="col-md-4">
                            <label for="email" class="form-label fw-bold">Email</label>
                            <input type="email" class="form-control" id="email" name="customer[email]">
                        </div>
                        <div class="col-md-4">
                            <label for="phone" class="form-label fw-bold">Phone</label>
                            <input type="tel" class="form-control" id="phone" name="customer[phone]" required>
                        </div>
                        <div class="col-md-4">
                            <label for="pan" class="form-label fw-bold">PAN</label>
                            <input type="text" class="form-control" id="pan" name="meta[pan_number]" required>
                        </div>
                        <div class="col-md-4">
                            <label for="aadhaar" class="form-label fw-bold">Aadhar</label>
                            <input type="text" class="form-control" id="aadhaar" name="meta[aadhaar_number]">
                        </div>
                        <div class="col-md-4">
                            <label for="dob" class="form-label fw-bold">Date of Birth</label>
                            <input type="date" class="form-control" id="dob" name="meta[dob]">
                        </div>
                    </div>
                </fieldset>

                <!-- Residence Address -->
                <fieldset class="mb-4 p-4 rounded-3" style="background-color: var(--accent-color);">
                    <legend class="h5 fw-bold text-primary mb-4 border-bottom pb-2">Residence Address</legend>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="residence_address" class="form-label fw-bold">Residence Address</label>
                            <textarea class="form-control" id="residence_address" name="meta[residence_address]" rows="2"></textarea>
                        </div>
                        <div class="col-md-4">
                            <label for="residence_city" class="form-label fw-bold">Residence City</label>
                            <input type="text" class="form-control" id="residence_city" name="meta[residence_city]">
                        </div>
                        <div class="col-md-4">
                            <label for="residence_pincode" class="form-label fw-bold">Residence Pincode</label>
                            <input type="text" class="form-control" id="residence_pincode" name="meta[residence_pincode]">
                        </div>
                        <div class="col-md-4">
                            <label for="residence_state" class="form-label fw-bold">Residence State</label>
                            <input type="text" class="form-control" id="residence_state" name="meta[residence_state]">
                        </div>
                    </div>
                </fieldset>

                <!-- Office Address -->
                <fieldset class="mb-4 p-4 rounded-3" style="background-color: var(--accent-color);">
                    <legend class="h5 fw-bold text-primary mb-4 border-bottom pb-2">Office Address</legend>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="office_address" class="form-label fw-bold">Office Address</label>
                            <textarea class="form-control" id="office_address" name="meta[office_address]" rows="2"></textarea>
                        </div>
                        <div class="col-md-4">
                            <label for="office_city" class="form-label fw-bold">Office City</label>
                            <input type="text" class="form-control" id="office_city" name="meta[office_city]">
                        </div>
                        <div class="col-md-4">
                            <label for="office_pincode" class="form-label fw-bold">Office Pincode</label>
                            <input type="text" class="form-control" id="office_pincode" name="meta[office_pincode]">
                        </div>
                        <div class="col-md-4">
                            <label for="office_state" class="form-label fw-bold">Office State</label>
                            <input type="text" class="form-control" id="office_state" name="meta[office_state]">
                        </div>
                    </div>
                </fieldset>

                <!-- Select Service -->
                <fieldset class="mb-4 p-4 rounded-3" style="background-color: var(--accent-color);">
                    <legend class="h5 fw-bold text-primary mb-4 border-bottom pb-2">Select Service</legend>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="tax_service" class="form-label fw-bold">Select Service</label>
                            <select class="form-select" id="tax_service" name="meta[tax_service]">
                                <option selected>Select Service</option>
                                <option value="ITR Filing">ITR Filing</option>
                                <option value="GST Registration">GST Registration</option>
                                <option value="GST Filing">GST Filing</option>
                                <option value="TDS Filing">TDS Filing</option>
                                <option value="Company Registration">Company Registration</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                </fieldset>

                <!-- Document Upload -->
                <fieldset class="p-4 rounded-3" style="background-color: var(--accent-color);">
                    <legend class="h5 fw-bold text-primary mb-4 border-bottom pb-2">Document Upload (All Optional)</legend>
                    <div class="row g-3">
                        <div class="col-md-3"><label class="form-label">Aadhar Card Front</label><input type="file" class="form-control" name="docs[aadhaar_front]" onchange="previewDocument(this)"><div class="mt-2 preview-container"></div></div>
                        <div class="col-md-3"><label class="form-label">Aadhar Card Back</label><input type="file" class="form-control" name="docs[aadhaar_back]" onchange="previewDocument(this)"><div class="mt-2 preview-container"></div></div>
                        <div class="col-md-3"><label class="form-label">Pan Card</label><input type="file" class="form-control" name="docs[pan_card]" onchange="previewDocument(this)"><div class="mt-2 preview-container"></div></div>
                        <div class="col-md-3"><label class="form-label">Passport Photo</label><input type="file" class="form-control" name="docs[passport_photo]" onchange="previewDocument(this)"><div class="mt-2 preview-container"></div></div>
                    </div>
                </fieldset>

                <div class="text-center mt-5">
                    <button type="submit" class="btn btn-primary btn-lg px-5 shadow-lg"><i class="fas fa-paper-plane me-2"></i>Submit Application</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function previewDocument(input) {
    const container = input.nextElementSibling;
    container.innerHTML = ''; // Clear previous preview

    if (input.files && input.files[0]) {
        const file = input.files[0];

        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.maxHeight = '100px';
                img.style.maxWidth = '100%';
                img.classList.add('img-thumbnail', 'mt-2');
                container.appendChild(img);
            }
            reader.readAsDataURL(file);
        } else {
            const p = document.createElement('p');
            p.textContent = 'Selected: ' + file.name;
            p.classList.add('text-success', 'small', 'mt-1', 'fw-bold');
            container.appendChild(p);
        }
    }
}
</script>

<?php view('layouts/partner_footer'); ?>
