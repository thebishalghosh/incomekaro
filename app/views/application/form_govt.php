<?php
$is_edit = isset($application);
$title = $is_edit ? 'Edit Application: ' . $service['name'] : 'New ' . $service['name'];
view('layouts/partner_header', ['title' => $title]);
?>

<div class="container-fluid">
    <div class="text-center mb-5 pt-4">
        <h1 class="fw-bold display-5 text-dark"><?php echo $is_edit ? 'Edit Application' : 'New Application'; ?>: <span class="text-primary"><?php echo $service['name']; ?></span></h1>
        <p class="lead text-muted">Please fill out all the required details for the client.</p>
    </div>

    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-body p-4 p-md-5">
            <form action="<?php echo $is_edit ? url('application/update/' . $application['id']) : url('application/store'); ?>" method="POST" enctype="multipart/form-data">
                <?php if (!$is_edit): ?>
                    <input type="hidden" name="service_id" value="<?php echo $service['id']; ?>">
                <?php endif; ?>

                <!-- Loan Details -->
                <fieldset class="mb-4 p-4 rounded-3" style="background-color: var(--accent-color);">
                    <legend class="h5 fw-bold text-primary mb-4 border-bottom pb-2">Loan Details</legend>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="loan_amount" class="form-label fw-bold">Loan Amount Requirement</label>
                            <input type="number" class="form-control" id="loan_amount" name="meta[loan_amount]" value="<?php echo $is_edit ? ($application['meta']['loan_amount'] ?? '') : ''; ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label for="applicant_type" class="form-label fw-bold">Type of Applicant</label>
                            <select class="form-select" id="applicant_type" name="meta[applicant_type]">
                                <option selected>Select Applicant Type</option>
                                <?php
                                    $opts = ['Individual', 'Business', 'Others'];
                                    foreach($opts as $opt) {
                                        $selected = ($is_edit && ($application['meta']['applicant_type'] ?? '') == $opt) ? 'selected' : '';
                                        echo "<option value='$opt' $selected>$opt</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="activity_type" class="form-label fw-bold">Type of Activity</label>
                            <select class="form-select" id="activity_type" name="meta[activity_type]">
                                <option selected>Select Activity Type</option>
                                <?php
                                    $opts = ['Manufacturing', 'Trading', 'Service', 'Others'];
                                    foreach($opts as $opt) {
                                        $selected = ($is_edit && ($application['meta']['activity_type'] ?? '') == $opt) ? 'selected' : '';
                                        echo "<option value='$opt' $selected>$opt</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="loan_tenure" class="form-label fw-bold">Duration of Loan</label>
                            <select class="form-select" id="loan_tenure" name="meta[loan_tenure]">
                                <option selected>Select Tenure</option>
                                <?php
                                    $opts = [12, 24, 36, 48, 60, 72, 84, 96];
                                    foreach($opts as $opt) {
                                        $val = $opt . ' Months';
                                        $selected = ($is_edit && ($application['meta']['loan_tenure'] ?? '') == $opt) ? 'selected' : '';
                                        echo "<option value='$opt' $selected>$val</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="loan_purpose" class="form-label fw-bold">Purpose of Loan</label>
                            <textarea class="form-control" id="loan_purpose" name="meta[loan_purpose]" rows="3" placeholder="What will the client do with the loan amount?"><?php echo $is_edit ? ($application['meta']['loan_purpose'] ?? '') : ''; ?></textarea>
                        </div>
                    </div>
                </fieldset>

                <!-- Personal Information -->
                <fieldset class="mb-4 p-4 rounded-3" style="background-color: var(--accent-color);">
                    <legend class="h5 fw-bold text-primary mb-4 border-bottom pb-2">Personal Information</legend>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="first_name" class="form-label fw-bold">Applicant Name (First)</label>
                            <?php
                                // Parse name if editing
                                $first_name = ''; $last_name = '';
                                if ($is_edit) {
                                    $parts = explode(' ', $application['customer_name'], 2);
                                    $first_name = $parts[0];
                                    $last_name = $parts[1] ?? '';
                                }
                            ?>
                            <input type="text" class="form-control" id="first_name" name="customer[first_name]" value="<?php echo $first_name; ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label for="last_name" class="form-label fw-bold">Applicant Name (Last)</label>
                            <input type="text" class="form-control" id="last_name" name="customer[last_name]" value="<?php echo $last_name; ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label for="email" class="form-label fw-bold">Applicant Email</label>
                            <input type="email" class="form-control" id="email" name="customer[email]" value="<?php echo $is_edit ? $application['customer_email'] : ''; ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="dob" class="form-label fw-bold">Applicant DOB (As on PAN)</label>
                            <input type="date" class="form-control" id="dob" name="meta[dob]" value="<?php echo $is_edit ? ($application['meta']['dob'] ?? '') : ''; ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="gender" class="form-label fw-bold">Gender</label>
                            <select class="form-select" id="gender" name="meta[gender]">
                                <option selected>Select Gender</option>
                                <?php
                                    $opts = ['Male', 'Female', 'Other'];
                                    foreach($opts as $opt) {
                                        $selected = ($is_edit && ($application['meta']['gender'] ?? '') == $opt) ? 'selected' : '';
                                        echo "<option value='$opt' $selected>$opt</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="pan" class="form-label fw-bold">Applicant PAN Number</label>
                            <input type="text" class="form-control" id="pan" name="meta[pan]" value="<?php echo $is_edit ? ($application['meta']['pan'] ?? '') : ''; ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="father_name" class="form-label fw-bold">Father Name</label>
                            <input type="text" class="form-control" id="father_name" name="meta[father_name]" value="<?php echo $is_edit ? ($application['meta']['father_name'] ?? '') : ''; ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="phone" class="form-label fw-bold">Mobile Number</label>
                            <input type="tel" class="form-control" id="phone" name="customer[phone]" value="<?php echo $is_edit ? $application['customer_phone'] : ''; ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label for="alt_phone" class="form-label fw-bold">Alternate Number</label>
                            <input type="tel" class="form-control" id="alt_phone" name="meta[alt_phone]" value="<?php echo $is_edit ? ($application['meta']['alt_phone'] ?? '') : ''; ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="marital_status" class="form-label fw-bold">Marital Status</label>
                            <select class="form-select" id="marital_status" name="meta[marital_status]" onchange="toggleSpouseField()">
                                <option selected>Select Marital Status</option>
                                <?php
                                    $opts = ['Single', 'Married', 'Divorced', 'Widowed'];
                                    foreach($opts as $opt) {
                                        $selected = ($is_edit && ($application['meta']['marital_status'] ?? '') == $opt) ? 'selected' : '';
                                        echo "<option value='$opt' $selected>$opt</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="spouse_name" class="form-label fw-bold">Spouse Name (If Married)</label>
                            <input type="text" class="form-control" id="spouse_name" name="meta[spouse_name]" value="<?php echo $is_edit ? ($application['meta']['spouse_name'] ?? '') : ''; ?>" <?php echo ($is_edit && ($application['meta']['marital_status'] ?? '') == 'Married') ? '' : 'disabled'; ?>>
                        </div>
                        <div class="col-md-3">
                            <label for="children" class="form-label fw-bold">Number of Children (If Any)</label>
                            <input type="number" class="form-control" id="children" name="meta[children]" value="<?php echo $is_edit ? ($application['meta']['children'] ?? '') : ''; ?>">
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
                                <?php
                                    $opts = ['Owned', 'Rented', 'Leased', 'Other'];
                                    foreach($opts as $opt) {
                                        $selected = ($is_edit && ($application['meta']['residence_type'] ?? '') == $opt) ? 'selected' : '';
                                        echo "<option value='$opt' $selected>$opt</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="office_type" class="form-label fw-bold">Official/Shop Property Type</label>
                            <select class="form-select" id="office_type" name="meta[office_type]">
                                <option selected>Select Type</option>
                                <?php
                                    $opts = ['Owned', 'Rented', 'Leased', 'None'];
                                    foreach($opts as $opt) {
                                        $selected = ($is_edit && ($application['meta']['office_type'] ?? '') == $opt) ? 'selected' : '';
                                        echo "<option value='$opt' $selected>$opt</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="pincode" class="form-label fw-bold">Applicant Resident Pincode</label>
                            <input type="text" class="form-control" id="pincode" name="meta[pincode]" value="<?php echo $is_edit ? ($application['meta']['pincode'] ?? '') : ''; ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="address_duration" class="form-label fw-bold">Duration at Current Address</label>
                            <input type="text" class="form-control" id="address_duration" name="meta[address_duration]" placeholder="e.g., 5 years" value="<?php echo $is_edit ? ($application['meta']['address_duration'] ?? '') : ''; ?>">
                        </div>
                    </div>
                </fieldset>

                <!-- Business/Professional Details -->
                <fieldset class="mb-4 p-4 rounded-3" style="background-color: var(--accent-color);">
                    <legend class="h5 fw-bold text-primary mb-4 border-bottom pb-2">Business/Professional Details</legend>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="profession" class="form-label fw-bold">Applicant Profession</label>
                            <input type="text" class="form-control" id="profession" name="meta[profession]" value="<?php echo $is_edit ? ($application['meta']['profession'] ?? '') : ''; ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="business_since" class="form-label fw-bold">Business Operating Since</label>
                            <input type="date" class="form-control" id="business_since" name="meta[business_since]" value="<?php echo $is_edit ? ($application['meta']['business_since'] ?? '') : ''; ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="experience" class="form-label fw-bold">Experience in Current Business</label>
                            <input type="text" class="form-control" id="experience" name="meta[experience]" placeholder="e.g., 10 years" value="<?php echo $is_edit ? ($application['meta']['experience'] ?? '') : ''; ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="monthly_salary" class="form-label fw-bold">Monthly Salary</label>
                            <input type="number" class="form-control" id="monthly_salary" name="meta[monthly_salary]" value="<?php echo $is_edit ? ($application['meta']['monthly_salary'] ?? '') : ''; ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="itr_filed" class="form-label fw-bold">Does Applicant File ITR?</label>
                            <select class="form-select" id="itr_filed" name="meta[itr_filed]">
                                <option value="" disabled <?php echo !$is_edit ? 'selected' : ''; ?>>Select ITR Filing Status</option>
                                <option value="Yes" <?php echo ($is_edit && ($application['meta']['itr_filed'] ?? '') == 'Yes') ? 'selected' : ''; ?>>Yes</option>
                                <option value="No" <?php echo ($is_edit && ($application['meta']['itr_filed'] ?? '') == 'No') ? 'selected' : ''; ?>>No</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="gst_registered" class="form-label fw-bold">Is Applicant GST Registered?</label>
                            <select class="form-select" id="gst_registered" name="meta[gst_registered]">
                                <option value="" disabled <?php echo !$is_edit ? 'selected' : ''; ?>>Select GST Registration Status</option>
                                <option value="Yes" <?php echo ($is_edit && ($application['meta']['gst_registered'] ?? '') == 'Yes') ? 'selected' : ''; ?>>Yes</option>
                                <option value="No" <?php echo ($is_edit && ($application['meta']['gst_registered'] ?? '') == 'No') ? 'selected' : ''; ?>>No</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="other_loan" class="form-label fw-bold">Any Other Loan Running?</label>
                            <select class="form-select" id="other_loan" name="meta[other_loan]" onchange="toggleOtherLoanDetailsField()">
                                <option value="" disabled <?php echo !$is_edit ? 'selected' : ''; ?>>Select Loan Status</option>
                                <option value="Yes" <?php echo ($is_edit && ($application['meta']['other_loan'] ?? '') == 'Yes') ? 'selected' : ''; ?>>Yes</option>
                                <option value="No" <?php echo ($is_edit && ($application['meta']['other_loan'] ?? '') == 'No') ? 'selected' : ''; ?>>No</option>
                            </select>
                        </div>
                        <div class="col-md-9">
                            <label for="other_loan_details" class="form-label fw-bold">Other Loan Details (If Any)</label>
                            <input type="text" class="form-control" id="other_loan_details" name="meta[other_loan_details]" value="<?php echo $is_edit ? ($application['meta']['other_loan_details'] ?? '') : ''; ?>" <?php echo ($is_edit && ($application['meta']['other_loan'] ?? '') == 'Yes') ? '' : 'disabled'; ?>>
                        </div>
                    </div>
                </fieldset>

                <!-- Bank Details -->
                <fieldset class="mb-4 p-4 rounded-3" style="background-color: var(--accent-color);">
                    <legend class="h5 fw-bold text-primary mb-4 border-bottom pb-2">Bank Details</legend>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="bank_name" class="form-label fw-bold">Bank Name</label>
                            <input type="text" class="form-control" id="bank_name" name="meta[bank_name]" value="<?php echo $is_edit ? ($application['meta']['bank_name'] ?? '') : ''; ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="account_number" class="form-label fw-bold">Account Number</label>
                            <input type="text" class="form-control" id="account_number" name="meta[account_number]" value="<?php echo $is_edit ? ($application['meta']['account_number'] ?? '') : ''; ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="ifsc_code" class="form-label fw-bold">IFSC Code</label>
                            <input type="text" class="form-control" id="ifsc_code" name="meta[ifsc_code]" value="<?php echo $is_edit ? ($application['meta']['ifsc_code'] ?? '') : ''; ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="caste" class="form-label fw-bold">Caste of Client</label>
                            <select class="form-select" id="caste" name="meta[caste]">
                                <option selected>Select Caste</option>
                                <?php
                                    $opts = ['General', 'OBC', 'SC', 'ST', 'Other'];
                                    foreach($opts as $opt) {
                                        $selected = ($is_edit && ($application['meta']['caste'] ?? '') == $opt) ? 'selected' : '';
                                        echo "<option value='$opt' $selected>$opt</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="other_bank_name" class="form-label fw-bold">Other Bank Name (If Any)</label>
                            <input type="text" class="form-control" id="other_bank_name" name="meta[other_bank_name]" value="<?php echo $is_edit ? ($application['meta']['other_bank_name'] ?? '') : ''; ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="other_account_number" class="form-label fw-bold">Other Bank Account Number</label>
                            <input type="text" class="form-control" id="other_account_number" name="meta[other_account_number]" value="<?php echo $is_edit ? ($application['meta']['other_account_number'] ?? '') : ''; ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="other_ifsc_code" class="form-label fw-bold">Other Bank IFSC Code</label>
                            <input type="text" class="form-control" id="other_ifsc_code" name="meta[other_ifsc_code]" value="<?php echo $is_edit ? ($application['meta']['other_ifsc_code'] ?? '') : ''; ?>">
                        </div>
                    </div>
                </fieldset>

                <!-- Document Upload -->
                <fieldset class="p-4 rounded-3" style="background-color: var(--accent-color);">
                    <legend class="h5 fw-bold text-primary mb-4 border-bottom pb-2">Document Upload (Optional)</legend>
                    <div class="row g-3">
                        <?php
                            $docs = [
                                'aadhaar_front' => 'Aadhar Card Front',
                                'aadhaar_back' => 'Aadhar Card Back',
                                'pan_card' => 'PAN Card',
                                'trade_license_1y' => '1-Year Trade License',
                                'itr_1y' => '1-Year ITR File',
                                'itr_2y' => '2-Year ITR File',
                                'msme_cert' => 'MSME Certificate',
                                'project_report' => 'Project Report',
                                'bank_statement_1y' => '1-Year Bank Statement'
                            ];
                            foreach ($docs as $key => $label):
                        ?>
                        <div class="col-md-3">
                            <label class="form-label"><?php echo $label; ?></label>
                            <input type="file" class="form-control" name="docs[<?php echo $key; ?>]" onchange="previewDocument(this)">
                            <div class="mt-2 preview-container">
                                <?php
                                    // Show existing doc if available
                                    if ($is_edit && !empty($application['documents'])) {
                                        foreach ($application['documents'] as $doc) {
                                            if ($doc['document_type'] == strtoupper($key)) {
                                                echo '<p class="text-success small mt-1 fw-bold"><i class="fas fa-check-circle me-1"></i> File Uploaded</p>';
                                                echo '<a href="'.asset($doc['file_url']).'" target="_blank" class="small text-decoration-none">View Current</a>';
                                            }
                                        }
                                    }
                                ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </fieldset>

                <div class="text-center mt-5">
                    <button type="submit" class="btn btn-primary btn-lg px-5 shadow-lg"><i class="fas fa-paper-plane me-2"></i><?php echo $is_edit ? 'Update Application' : 'Submit Application'; ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function previewDocument(input) {
    const container = input.nextElementSibling;
    // Keep existing links if any, just append preview or replace if it's a new preview
    // Actually, simpler to just clear and show new preview
    // But we want to keep the "View Current" link visible until they select a new file?
    // For simplicity, let's clear.
    container.innerHTML = '';

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
