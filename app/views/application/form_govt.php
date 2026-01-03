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
                            <label for="loan_amount" class="form-label fw-bold">Loan Amount Requirement</label>
                            <input type="number" class="form-control" id="loan_amount" name="meta[loan_amount]" required>
                        </div>
                        <div class="col-md-3">
                            <label for="applicant_type" class="form-label fw-bold">Type of Applicant</label>
                            <select class="form-select" id="applicant_type" name="meta[applicant_type]">
                                <option selected>Select Applicant Type</option>
                                <option value="Individual">Individual</option>
                                <option value="Business">Business</option>
                                <option value="Others">Others</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="activity_type" class="form-label fw-bold">Type of Activity</label>
                            <select class="form-select" id="activity_type" name="meta[activity_type]">
                                <option selected>Select Activity Type</option>
                                <option value="Manufacturing">Manufacturing</option>
                                <option value="Trading">Trading</option>
                                <option value="Service">Service</option>
                                <option value="Others">Others</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="loan_tenure" class="form-label fw-bold">Duration of Loan</label>
                            <select class="form-select" id="loan_tenure" name="meta[loan_tenure]">
                                <option selected>Select Tenure</option>
                                <option value="12">12 Months</option>
                                <option value="24">24 Months</option>
                                <option value="36">36 Months</option>
                                <option value="48">48 Months</option>
                                <option value="60">60 Months</option>
                                <option value="72">72 Months</option>
                                <option value="84">84 Months</option>
                                <option value="96">96 Months</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="loan_purpose" class="form-label fw-bold">Purpose of Loan</label>
                            <textarea class="form-control" id="loan_purpose" name="meta[loan_purpose]" rows="3" placeholder="What will the client do with the loan amount?"></textarea>
                        </div>
                    </div>
                </fieldset>

                <!-- Personal Information -->
                <fieldset class="mb-4 p-4 rounded-3" style="background-color: var(--accent-color);">
                    <legend class="h5 fw-bold text-primary mb-4 border-bottom pb-2">Personal Information</legend>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="first_name" class="form-label fw-bold">Applicant Name (First)</label>
                            <input type="text" class="form-control" id="first_name" name="customer[first_name]" required>
                        </div>
                        <div class="col-md-3">
                            <label for="last_name" class="form-label fw-bold">Applicant Name (Last)</label>
                            <input type="text" class="form-control" id="last_name" name="customer[last_name]" required>
                        </div>
                        <div class="col-md-3">
                            <label for="email" class="form-label fw-bold">Applicant Email</label>
                            <input type="email" class="form-control" id="email" name="customer[email]">
                        </div>
                        <div class="col-md-3">
                            <label for="dob" class="form-label fw-bold">Applicant DOB (As on PAN)</label>
                            <input type="date" class="form-control" id="dob" name="meta[dob]">
                        </div>
                        <div class="col-md-3">
                            <label for="gender" class="form-label fw-bold">Gender</label>
                            <select class="form-select" id="gender" name="meta[gender]">
                                <option selected>Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="pan" class="form-label fw-bold">Applicant PAN Number</label>
                            <input type="text" class="form-control" id="pan" name="meta[pan]">
                        </div>
                        <div class="col-md-3">
                            <label for="father_name" class="form-label fw-bold">Father Name</label>
                            <input type="text" class="form-control" id="father_name" name="meta[father_name]">
                        </div>
                        <div class="col-md-3">
                            <label for="phone" class="form-label fw-bold">Mobile Number</label>
                            <input type="tel" class="form-control" id="phone" name="customer[phone]" required>
                        </div>
                        <div class="col-md-3">
                            <label for="alt_phone" class="form-label fw-bold">Alternate Number</label>
                            <input type="tel" class="form-control" id="alt_phone" name="meta[alt_phone]">
                        </div>
                        <div class="col-md-3">
                            <label for="marital_status" class="form-label fw-bold">Marital Status</label>
                            <select class="form-select" id="marital_status" name="meta[marital_status]" onchange="toggleSpouseField()">
                                <option selected>Select Marital Status</option>
                                <option value="Single">Single</option>
                                <option value="Married">Married</option>
                                <option value="Divorced">Divorced</option>
                                <option value="Widowed">Widowed</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="spouse_name" class="form-label fw-bold">Spouse Name (If Married)</label>
                            <input type="text" class="form-control" id="spouse_name" name="meta[spouse_name]" disabled>
                        </div>
                        <div class="col-md-3">
                            <label for="children" class="form-label fw-bold">Number of Children (If Any)</label>
                            <input type="number" class="form-control" id="children" name="meta[children]">
                        </div>
                    </div>
                </fieldset>

                <!-- Property Details -->
                <fieldset class="mb-4 p-4 rounded-3" style="background-color: var(--accent-color);">
                    <legend class="h5 fw-bold text-primary mb-4 border-bottom pb-2">Property Details</legend>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="residence_type" class="form-label fw-bold">Residence Property Type</label>
                            <select class="form-select" id="residence_type" name="meta[residence_type]">
                                <option selected>Select Type</option>
                                <option value="Owned">Owned</option>
                                <option value="Rented">Rented</option>
                                <option value="Leased">Leased</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="office_type" class="form-label fw-bold">Official/Shop Property Type</label>
                            <select class="form-select" id="office_type" name="meta[office_type]">
                                <option selected>Select Type</option>
                                <option value="Owned">Owned</option>
                                <option value="Rented">Rented</option>
                                <option value="Leased">Leased</option>
                                <option value="None">None</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="pincode" class="form-label fw-bold">Applicant Resident Pincode</label>
                            <input type="text" class="form-control" id="pincode" name="meta[pincode]">
                        </div>
                        <div class="col-md-3">
                            <label for="address_duration" class="form-label fw-bold">Duration at Current Address</label>
                            <input type="text" class="form-control" id="address_duration" name="meta[address_duration]" placeholder="e.g., 5 years">
                        </div>
                    </div>
                </fieldset>

                <!-- Business/Professional Details -->
                <fieldset class="mb-4 p-4 rounded-3" style="background-color: var(--accent-color);">
                    <legend class="h5 fw-bold text-primary mb-4 border-bottom pb-2">Business/Professional Details</legend>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="profession" class="form-label fw-bold">Applicant Profession</label>
                            <input type="text" class="form-control" id="profession" name="meta[profession]">
                        </div>
                        <div class="col-md-3">
                            <label for="business_since" class="form-label fw-bold">Business Operating Since</label>
                            <input type="date" class="form-control" id="business_since" name="meta[business_since]">
                        </div>
                        <div class="col-md-3">
                            <label for="experience" class="form-label fw-bold">Experience in Current Business</label>
                            <input type="text" class="form-control" id="experience" name="meta[experience]" placeholder="e.g., 10 years">
                        </div>
                        <div class="col-md-3">
                            <label for="monthly_salary" class="form-label fw-bold">Monthly Salary</label>
                            <input type="number" class="form-control" id="monthly_salary" name="meta[monthly_salary]">
                        </div>
                        <div class="col-md-3">
                            <label for="itr_filed" class="form-label fw-bold">Does Applicant File ITR?</label>
                            <select class="form-select" id="itr_filed" name="meta[itr_filed]">
                                <option value="" selected disabled>Select ITR Filing Status</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="gst_registered" class="form-label fw-bold">Is Applicant GST Registered?</label>
                            <select class="form-select" id="gst_registered" name="meta[gst_registered]">
                                <option value="" selected disabled>Select GST Registration Status</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="other_loan" class="form-label fw-bold">Any Other Loan Running?</label>
                            <select class="form-select" id="other_loan" name="meta[other_loan]" onchange="toggleOtherLoanDetailsField()">
                                <option value="" selected disabled>Select Loan Status</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                        <div class="col-md-9">
                            <label for="other_loan_details" class="form-label fw-bold">Other Loan Details (If Any)</label>
                            <input type="text" class="form-control" id="other_loan_details" name="meta[other_loan_details]" disabled>
                        </div>
                    </div>
                </fieldset>

                <!-- Bank Details -->
                <fieldset class="mb-4 p-4 rounded-3" style="background-color: var(--accent-color);">
                    <legend class="h5 fw-bold text-primary mb-4 border-bottom pb-2">Bank Details</legend>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="bank_name" class="form-label fw-bold">Bank Name</label>
                            <input type="text" class="form-control" id="bank_name" name="meta[bank_name]">
                        </div>
                        <div class="col-md-3">
                            <label for="account_number" class="form-label fw-bold">Account Number</label>
                            <input type="text" class="form-control" id="account_number" name="meta[account_number]">
                        </div>
                        <div class="col-md-3">
                            <label for="ifsc_code" class="form-label fw-bold">IFSC Code</label>
                            <input type="text" class="form-control" id="ifsc_code" name="meta[ifsc_code]">
                        </div>
                        <div class="col-md-3">
                            <label for="caste" class="form-label fw-bold">Caste of Client</label>
                            <select class="form-select" id="caste" name="meta[caste]">
                                <option selected>Select Caste</option>
                                <option value="General">General</option>
                                <option value="OBC">OBC</option>
                                <option value="SC">SC</option>
                                <option value="ST">ST</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="other_bank_name" class="form-label fw-bold">Other Bank Name (If Any)</label>
                            <input type="text" class="form-control" id="other_bank_name" name="meta[other_bank_name]">
                        </div>
                        <div class="col-md-3">
                            <label for="other_account_number" class="form-label fw-bold">Other Bank Account Number</label>
                            <input type="text" class="form-control" id="other_account_number" name="meta[other_account_number]">
                        </div>
                        <div class="col-md-3">
                            <label for="other_ifsc_code" class="form-label fw-bold">Other Bank IFSC Code</label>
                            <input type="text" class="form-control" id="other_ifsc_code" name="meta[other_ifsc_code]">
                        </div>
                    </div>
                </fieldset>

                <!-- Document Upload -->
                <fieldset class="p-4 rounded-3" style="background-color: var(--accent-color);">
                    <legend class="h5 fw-bold text-primary mb-4 border-bottom pb-2">Document Upload (Optional)</legend>
                    <div class="row g-3">
                        <div class="col-md-3"><label class="form-label">Aadhar Card Front</label><input type="file" class="form-control" name="docs[aadhaar_front]" onchange="previewDocument(this)">
                            <div class="mt-2 preview-container"></div>
                        </div>
                        <div class="col-md-3"><label class="form-label">Aadhar Card Back</label><input type="file" class="form-control" name="docs[aadhaar_back]" onchange="previewDocument(this)">
                            <div class="mt-2 preview-container"></div>
                        </div>
                        <div class="col-md-3"><label class="form-label">PAN Card</label><input type="file" class="form-control" name="docs[pan_card]" onchange="previewDocument(this)">
                            <div class="mt-2 preview-container"></div>
                        </div>
                        <div class="col-md-3"><label class="form-label">1-Year Trade License</label><input type="file" class="form-control" name="docs[trade_license_1y]" onchange="previewDocument(this)">
                            <div class="mt-2 preview-container"></div>
                        </div>
                        <div class="col-md-3"><label class="form-label">1-Year ITR File</label><input type="file" class="form-control" name="docs[itr_1y]" onchange="previewDocument(this)">
                            <div class="mt-2 preview-container"></div>
                        </div>
                        <div class="col-md-3"><label class="form-label">2-Year ITR File</label><input type="file" class="form-control" name="docs[itr_2y]" onchange="previewDocument(this)">
                            <div class="mt-2 preview-container"></div>
                        </div>
                        <div class="col-md-3"><label class="form-label">MSME Certificate</label><input type="file" class="form-control" name="docs[msme_cert]" onchange="previewDocument(this)">
                            <div class="mt-2 preview-container"></div>
                        </div>
                        <div class="col-md-3"><label class="form-label">Project Report</label><input type="file" class="form-control" name="docs[project_report]" onchange="previewDocument(this)">
                            <div class="mt-2 preview-container"></div>
                        </div>
                        <div class="col-md-3"><label class="form-label">1-Year Bank Statement</label><input type="file" class="form-control" name="docs[bank_statement_1y]" onchange="previewDocument(this)">
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

function toggleSpouseField() {
    const maritalStatus = document.getElementById('marital_status').value;
    const spouseNameInput = document.getElementById('spouse_name');

    if (maritalStatus === 'Married') {
        spouseNameInput.disabled = false;
    } else {
        spouseNameInput.disabled = true;
        spouseNameInput.value = '';
    }
}

function toggleOtherLoanDetailsField() {
    const otherLoanStatus = document.getElementById('other_loan').value;
    const otherLoanDetailsInput = document.getElementById('other_loan_details');

    if (otherLoanStatus === 'Yes') {
        otherLoanDetailsInput.disabled = false;
    } else {
        otherLoanDetailsInput.disabled = true;
        otherLoanDetailsInput.value = '';
    }
}
</script>

<?php view('layouts/partner_footer'); ?>
