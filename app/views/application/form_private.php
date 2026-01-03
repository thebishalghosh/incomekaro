<?php view('layouts/partner_header', ['title' => 'New ' . $service['name']]); ?>

<div class="container-fluid">
    <div class="text-center mb-5 pt-4">
        <h1 class="fw-bold display-5 text-dark">New Application: <span class="text-primary"><?php echo $service['name']; ?></span></h1>
        <p class="lead text-muted">Please fill out all the required details for the client.</p>
    </div>

    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-body p-4 p-md-5">
            <form action="<?php echo url('application/store'); ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="service_id" value="<?php echo $service['id']; ?>">

                <!-- Loan Details -->
                <fieldset class="mb-4 p-4 rounded-3" style="background-color: var(--accent-color);">
                    <legend class="h5 fw-bold text-primary mb-4 border-bottom pb-2">Loan Details</legend>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="loan_mode" class="form-label fw-bold">Loan Mode</label>
                            <select class="form-select" id="loan_mode" name="meta[loan_mode]">
                                <option selected>Select Loan Mode</option>
                                <option value="Personal">Personal</option>
                                <option value="Business">Business</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="loan_amount" class="form-label fw-bold">Amount</label>
                            <input type="number" class="form-control" id="loan_amount" name="meta[loan_amount]" required>
                        </div>
                        <div class="col-md-3">
                            <label for="loan_tenure" class="form-label fw-bold">Tenure</label>
                            <select class="form-select" id="loan_tenure" name="meta[loan_tenure]">
                                <option selected>Select Tenure</option>
                                <option value="12">12 Months</option>
                                <option value="24">24 Months</option>
                                <option value="36">36 Months</option>
                                <option value="48">48 Months</option>
                                <option value="60">60 Months</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="dob" class="form-label fw-bold">Date of Birth</label>
                            <input type="date" class="form-control" id="dob" name="meta[dob]">
                        </div>
                    </div>
                </fieldset>

                <!-- Personal Information -->
                <fieldset class="mb-4 p-4 rounded-3" style="background-color: var(--accent-color);">
                    <legend class="h5 fw-bold text-primary mb-4 border-bottom pb-2">Personal Information</legend>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="first_name" class="form-label fw-bold">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="customer[first_name]" required>
                        </div>
                        <div class="col-md-3">
                            <label for="middle_name" class="form-label fw-bold">Middle Name</label>
                            <input type="text" class="form-control" id="middle_name" name="meta[middle_name]">
                        </div>
                        <div class="col-md-3">
                            <label for="last_name" class="form-label fw-bold">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="customer[last_name]" required>
                        </div>
                        <div class="col-md-3">
                            <label for="email" class="form-label fw-bold">Email</label>
                            <input type="email" class="form-control" id="email" name="customer[email]">
                        </div>
                        <div class="col-md-3">
                            <label for="phone" class="form-label fw-bold">Phone</label>
                            <input type="tel" class="form-control" id="phone" name="customer[phone]" required>
                        </div>
                        <div class="col-md-3">
                            <label for="pan" class="form-label fw-bold">PAN</label>
                            <input type="text" class="form-control" id="pan" name="meta[pan]">
                        </div>
                        <div class="col-md-3">
                            <label for="mother_name" class="form-label fw-bold">Mother's Name</label>
                            <input type="text" class="form-control" id="mother_name" name="meta[mother_name]">
                        </div>
                    </div>
                </fieldset>

                <!-- Employment Details -->
                <fieldset class="mb-4 p-4 rounded-3" style="background-color: var(--accent-color);">
                    <legend class="h5 fw-bold text-primary mb-4 border-bottom pb-2">Employment Details</legend>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="employment_type" class="form-label fw-bold">Employment Type</label>
                            <select class="form-select" id="employment_type" name="meta[employment_type]">
                                <option selected>Select Employment Type</option>
                                <option value="Salaried">Salaried</option>
                                <option value="Self-Employed">Self-Employed</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="company_name" class="form-label fw-bold">Company Name</label>
                            <input type="text" class="form-control" id="company_name" name="meta[company_name]">
                        </div>
                        <div class="col-md-3">
                            <label for="company_type" class="form-label fw-bold">Company Type</label>
                            <select class="form-select" id="company_type" name="meta[company_type]">
                                <option selected>Select Company Type</option>
                                <option value="Public Ltd">Public Ltd</option>
                                <option value="Private Ltd">Private Ltd</option>
                                <option value="LLP">LLP</option>
                                <option value="Partnership">Partnership</option>
                                <option value="Proprietorship">Proprietorship</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="monthly_income" class="form-label fw-bold">Monthly Income</label>
                            <input type="number" class="form-control" id="monthly_income" name="meta[monthly_income]">
                        </div>
                    </div>
                </fieldset>

                <!-- Address & References -->
                <fieldset class="mb-4 p-4 rounded-3" style="background-color: var(--accent-color);">
                    <legend class="h5 fw-bold text-primary mb-4 border-bottom pb-2">Address & References</legend>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="residence_address" class="form-label fw-bold">Residence Address</label>
                            <textarea class="form-control" id="residence_address" name="meta[residence_address]" rows="2"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="residence_pincode" class="form-label fw-bold">Residence Pincode</label>
                            <input type="text" class="form-control" id="residence_pincode" name="meta[residence_pincode]">
                        </div>
                        <div class="col-md-3">
                            <label for="ref_name_1" class="form-label fw-bold">Reference Name 1</label>
                            <input type="text" class="form-control" id="ref_name_1" name="meta[ref_name_1]">
                        </div>
                        <div class="col-md-3">
                            <label for="ref_phone_1" class="form-label fw-bold">Reference Phone 1</label>
                            <input type="tel" class="form-control" id="ref_phone_1" name="meta[ref_phone_1]">
                        </div>
                        <div class="col-md-3">
                            <label for="ref_name_2" class="form-label fw-bold">Reference Name 2</label>
                            <input type="text" class="form-control" id="ref_name_2" name="meta[ref_name_2]">
                        </div>
                        <div class="col-md-3">
                            <label for="ref_phone_2" class="form-label fw-bold">Reference Phone 2</label>
                            <input type="tel" class="form-control" id="ref_phone_2" name="meta[ref_phone_2]">
                        </div>
                    </div>
                </fieldset>

                <!-- Document Upload -->
                <fieldset class="p-4 rounded-3" style="background-color: var(--accent-color);">
                    <legend class="h5 fw-bold text-primary mb-4 border-bottom pb-2">Document Upload (All Optional)</legend>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Aadhar Card Front</label>
                            <input type="file" class="form-control" name="docs[aadhaar_front]" onchange="previewDocument(this)">
                            <div class="mt-2 preview-container"></div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Aadhar Card Back</label>
                            <input type="file" class="form-control" name="docs[aadhaar_back]" onchange="previewDocument(this)">
                            <div class="mt-2 preview-container"></div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Pan Card</label>
                            <input type="file" class="form-control" name="docs[pan_card]" onchange="previewDocument(this)">
                            <div class="mt-2 preview-container"></div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Passport Photo</label>
                            <input type="file" class="form-control" name="docs[passport_photo]" onchange="previewDocument(this)">
                            <div class="mt-2 preview-container"></div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">1 Month Salary Slip</label>
                            <input type="file" class="form-control" name="docs[salary_slip_1m]" onchange="previewDocument(this)">
                            <div class="mt-2 preview-container"></div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">2 Month Salary Slip</label>
                            <input type="file" class="form-control" name="docs[salary_slip_2m]" onchange="previewDocument(this)">
                            <div class="mt-2 preview-container"></div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">3 Month Salary Slip</label>
                            <input type="file" class="form-control" name="docs[salary_slip_3m]" onchange="previewDocument(this)">
                            <div class="mt-2 preview-container"></div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Bank Statement</label>
                            <input type="file" class="form-control" name="docs[bank_statement]" onchange="previewDocument(this)">
                            <div class="mt-2 preview-container"></div>
                        </div>
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
