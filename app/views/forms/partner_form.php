<?php view('layouts/header', ['title' => isset($partner) ? 'Edit Partner' : 'Add Partner']); ?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark"><?php echo isset($partner) ? 'Edit Partner' : 'New Partner'; ?></h2>
                <p class="text-muted">Manage DSA partners.</p>
            </div>
            <a href="<?php echo url('partner/index'); ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Back
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-lg">
            <div class="card-body p-4 p-md-5">
                <form action="<?php echo isset($partner) ? url('partner/update/' . $partner['id']) : url('partner/store'); ?>" method="POST" enctype="multipart/form-data">

                    <!-- Super Admin: Partner Type Selection -->
                    <?php if ($_SESSION['role_code'] === 'SUPER_ADMIN' && !isset($partner)): ?>
                        <h5 class="fw-bold text-primary mb-4">Partner Assignment</h5>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="partner_type" class="form-label fw-bold">Partner Type</label>
                                <select class="form-select" id="partner_type" name="partner_type" onchange="toggleWhiteLabelSelect()">
                                    <option value="PLATFORM">Platform Partner (Direct)</option>
                                    <option value="WHITE_LABEL">White Label Partner</option>
                                </select>
                            </div>
                            <div class="col-md-6" id="white_label_select_container" style="display: none;">
                                <label for="white_label_id" class="form-label fw-bold">Select White Label Client</label>
                                <select class="form-select" id="white_label_id" name="white_label_id">
                                    <option value="">-- Select Client --</option>
                                    <?php if (!empty($white_labels)): ?>
                                        <?php foreach ($white_labels as $wl): ?>
                                            <option value="<?php echo $wl['id']; ?>"><?php echo $wl['company_name']; ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <hr class="my-4 text-muted">
                    <?php endif; ?>

                    <!-- Profile Information -->
                    <h5 class="fw-bold text-primary mb-4">Profile Information</h5>
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <label for="profile_image" class="form-label fw-bold">Profile Image <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="profile_image" name="profile_image" accept="image/*" onchange="previewImage(this)">
                            <div class="mt-3" id="image_preview_container" style="<?php echo (isset($partner) && !empty($partner['profile']['profile_image'])) ? '' : 'display:none;'; ?>">
                                <img id="image_preview" src="<?php echo (isset($partner) && !empty($partner['profile']['profile_image'])) ? asset($partner['profile']['profile_image']) : '#'; ?>" alt="Profile Preview" style="height: 100px; width: 100px; object-fit: cover; border-radius: 50%; border: 2px solid #dee2e6;">
                            </div>
                        </div>
                    </div>

                    <hr class="my-4 text-muted">

                    <!-- Personal Information -->
                    <h5 class="fw-bold text-primary mb-4">Personal Information</h5>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="full_name" class="form-label fw-bold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo !empty($partner['profile']) ? $partner['profile']['full_name'] : ''; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="mobile" class="form-label fw-bold">Mobile Number <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="mobile" name="mobile" value="<?php echo !empty($partner['profile']) ? $partner['profile']['mobile'] : ''; ?>" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="email" class="form-label fw-bold">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo !empty($partner['profile']) ? $partner['profile']['email'] : ''; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="whatsapp" class="form-label fw-bold">WhatsApp Number</label>
                            <input type="tel" class="form-control" id="whatsapp" name="whatsapp" value="<?php echo !empty($partner['profile']) ? $partner['profile']['whatsapp'] : ''; ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="dob" class="form-label fw-bold">Date of Birth</label>
                            <input type="date" class="form-control" id="dob" name="dob" value="<?php echo !empty($partner['profile']) ? $partner['profile']['dob'] : ''; ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold d-block">Gender</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="gender" id="gender_male" value="male" <?php echo (!empty($partner['profile']) && $partner['profile']['gender'] == 'male') ? 'checked' : 'checked'; ?>>
                                <label class="form-check-label" for="gender_male">Male</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="gender" id="gender_female" value="female" <?php echo (!empty($partner['profile']) && $partner['profile']['gender'] == 'female') ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="gender_female">Female</label>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4 text-muted">

                    <!-- Bank Details -->
                    <h5 class="fw-bold text-primary mb-4">Bank Details</h5>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="account_holder_name" class="form-label fw-bold">Account Holder Name</label>
                            <input type="text" class="form-control" id="account_holder_name" name="account_holder_name" value="<?php echo !empty($partner['bank_details']) ? $partner['bank_details']['account_holder_name'] : ''; ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="bank_name" class="form-label fw-bold">Bank Name</label>
                            <input type="text" class="form-control" id="bank_name" name="bank_name" value="<?php echo !empty($partner['bank_details']) ? $partner['bank_details']['bank_name'] : ''; ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="account_number" class="form-label fw-bold">Account Number</label>
                            <input type="text" class="form-control" id="account_number" name="account_number" value="<?php echo !empty($partner['bank_details']) ? $partner['bank_details']['account_number'] : ''; ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="ifsc_code" class="form-label fw-bold">IFSC Code</label>
                            <input type="text" class="form-control" id="ifsc_code" name="ifsc_code" value="<?php echo !empty($partner['bank_details']) ? $partner['bank_details']['ifsc_code'] : ''; ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="branch" class="form-label fw-bold">Branch</label>
                            <input type="text" class="form-control" id="branch" name="branch" value="<?php echo !empty($partner['bank_details']) ? $partner['bank_details']['branch'] : ''; ?>">
                        </div>
                    </div>

                    <hr class="my-4 text-muted">

                    <!-- Subscription Plan -->
                    <h5 class="fw-bold text-primary mb-4">Subscription Plan</h5>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="plan_id" class="form-label fw-bold">Select Plan</label>
                            <select class="form-select" id="plan_id" name="plan_id" onchange="updatePlanPrice()">
                                <option value="">-- Select Plan --</option>
                                <?php if (!empty($plans)): ?>
                                    <?php foreach ($plans as $p): ?>
                                        <?php
                                            $total_price = $p['price'] * (1 + $p['gst_rate'] / 100);
                                            $is_selected = (isset($partner['subscription']) && $partner['subscription']['plan_name'] == $p['name']) ? 'selected' : '';
                                        ?>
                                        <option value="<?php echo $p['id']; ?>" data-price="<?php echo number_format($total_price, 2, '.', ''); ?>" <?php echo $is_selected; ?>>
                                            <?php echo $p['name']; ?> (₹<?php echo number_format($total_price, 2); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="payment_amount" class="form-label fw-bold">Payment Amount</label>
                            <input type="number" step="0.01" class="form-control" id="payment_amount" name="payment_amount" placeholder="0.00" value="<?php echo !empty($partner['subscription']) ? $partner['subscription']['payment_amount'] : ''; ?>" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="due_amount" class="form-label fw-bold">Due Amount</label>
                            <input type="number" step="0.01" class="form-control" id="due_amount" name="due_amount" placeholder="0.00" value="<?php echo !empty($partner['subscription']) ? $partner['subscription']['due_amount'] : ''; ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="payment_mode" class="form-label fw-bold">Payment Mode</label>
                            <select class="form-select" id="payment_mode" name="payment_mode">
                                <option value="Online" <?php echo (!empty($partner['subscription']) && $partner['subscription']['payment_mode'] == 'Online') ? 'selected' : ''; ?>>Online</option>
                                <option value="Cash" <?php echo (!empty($partner['subscription']) && $partner['subscription']['payment_mode'] == 'Cash') ? 'selected' : ''; ?>>Cash</option>
                                <option value="Cheque" <?php echo (!empty($partner['subscription']) && $partner['subscription']['payment_mode'] == 'Cheque') ? 'selected' : ''; ?>>Cheque</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="transaction_id" class="form-label fw-bold">Transaction ID</label>
                            <input type="text" class="form-control" id="transaction_id" name="transaction_id" value="<?php echo !empty($partner['subscription']) ? $partner['subscription']['transaction_id'] : ''; ?>">
                        </div>
                    </div>

                    <hr class="my-4 text-muted">

                    <!-- Permanent Address -->
                    <h5 class="fw-bold text-primary mb-4">Permanent Address</h5>
                    <div class="mb-3">
                        <label for="perm_address" class="form-label fw-bold">Address <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="perm_address" name="perm_address" rows="2" required><?php echo !empty($partner['address_permanent']) ? $partner['address_permanent']['address'] : ''; ?></textarea>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="perm_state" class="form-label fw-bold">State <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="perm_state" name="perm_state" value="<?php echo !empty($partner['address_permanent']) ? $partner['address_permanent']['state'] : ''; ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label for="perm_city" class="form-label fw-bold">City <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="perm_city" name="perm_city" value="<?php echo !empty($partner['address_permanent']) ? $partner['address_permanent']['city'] : ''; ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label for="perm_pincode" class="form-label fw-bold">Pincode <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="perm_pincode" name="perm_pincode" value="<?php echo !empty($partner['address_permanent']) ? $partner['address_permanent']['pincode'] : ''; ?>" required>
                        </div>
                    </div>

                    <hr class="my-4 text-muted">

                    <!-- Office Address -->
                    <h5 class="fw-bold text-primary mb-4">Office Address</h5>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="same_as_perm" name="same_as_perm" onchange="toggleOfficeAddress()">
                        <label class="form-check-label" for="same_as_perm">Same as permanent address</label>
                    </div>
                    <div id="office_address_section">
                        <div class="mb-3">
                            <label for="office_address" class="form-label fw-bold">Address</label>
                            <textarea class="form-control" id="office_address" name="office_address" rows="2"><?php echo !empty($partner['address_office']) ? $partner['address_office']['address'] : ''; ?></textarea>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="office_state" class="form-label fw-bold">State</label>
                                <input type="text" class="form-control" id="office_state" name="office_state" value="<?php echo !empty($partner['address_office']) ? $partner['address_office']['state'] : ''; ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="office_city" class="form-label fw-bold">City</label>
                                <input type="text" class="form-control" id="office_city" name="office_city" value="<?php echo !empty($partner['address_office']) ? $partner['address_office']['city'] : ''; ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="office_pincode" class="form-label fw-bold">Pincode</label>
                                <input type="text" class="form-control" id="office_pincode" name="office_pincode" value="<?php echo !empty($partner['address_office']) ? $partner['address_office']['pincode'] : ''; ?>">
                            </div>
                        </div>
                    </div>

                    <hr class="my-4 text-muted">

                    <!-- Identity Details -->
                    <h5 class="fw-bold text-primary mb-4">Identity Details</h5>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="gst" class="form-label fw-bold">GST Number</label>
                            <input type="text" class="form-control" id="gst" name="gst" value="<?php echo !empty($partner['identity']) ? $partner['identity']['gst'] : ''; ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="aadhaar" class="form-label fw-bold">Aadhar Number</label>
                            <input type="text" class="form-control" id="aadhaar" name="aadhaar" value="<?php echo !empty($partner['identity']) ? $partner['identity']['aadhaar'] : ''; ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="pan" class="form-label fw-bold">PAN Number</label>
                            <input type="text" class="form-control" id="pan" name="pan" value="<?php echo !empty($partner['identity']) ? $partner['identity']['pan'] : ''; ?>">
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-5">
                        <a href="<?php echo url('partner/index'); ?>" class="btn btn-light btn-lg px-4 me-md-2">Cancel</a>
                        <button type="submit" class="btn btn-primary btn-lg px-5"><?php echo isset($partner) ? 'Update Partner' : 'Create Partner'; ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function toggleOfficeAddress() {
    const checkbox = document.getElementById('same_as_perm');
    const section = document.getElementById('office_address_section');
    if (checkbox.checked) {
        section.style.display = 'none';
    } else {
        section.style.display = 'block';
    }
}

function toggleWhiteLabelSelect() {
    const type = document.getElementById('partner_type').value;
    const container = document.getElementById('white_label_select_container');
    if (type === 'WHITE_LABEL') {
        container.style.display = 'block';
    } else {
        container.style.display = 'none';
    }
}

function previewImage(input) {
    const preview = document.getElementById('image_preview');
    const container = document.getElementById('image_preview_container');

    if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
            preview.src = e.target.result;
            container.style.display = 'block';
        }

        reader.readAsDataURL(input.files[0]);
    } else {
        preview.src = '#';
        container.style.display = 'none';
    }
}

function updatePlanPrice() {
    const select = document.getElementById('plan_id');
    const priceInput = document.getElementById('payment_amount');
    const selectedOption = select.options[select.selectedIndex];

    if (selectedOption.value) {
        priceInput.value = selectedOption.getAttribute('data-price');
    } else {
        priceInput.value = '';
    }
}
</script>

<?php view('layouts/footer'); ?>
