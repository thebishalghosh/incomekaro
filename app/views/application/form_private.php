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
                            <label for="loan_mode" class="form-label fw-bold">Loan Mode</label>
                            <select class="form-select" id="loan_mode" name="meta[loan_mode]">
                                <option selected>Select Loan Mode</option>
                                <?php
                                    $opts = ['New', 'BT', 'Top Up', 'Card to Card'];
                                    foreach($opts as $opt) {
                                        $selected = ($is_edit && ($application['meta']['loan_mode'] ?? '') == $opt) ? 'selected' : '';
                                        echo "<option value='$opt' $selected>$opt</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="loan_amount" class="form-label fw-bold">Amount</label>
                            <input type="number" class="form-control" id="loan_amount" name="meta[loan_amount]" value="<?php echo $is_edit ? ($application['meta']['loan_amount'] ?? '') : ''; ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label for="loan_tenure" class="form-label fw-bold">Tenure</label>
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
                        <div class="col-md-3">
                            <label for="dob" class="form-label fw-bold">Date of Birth</label>
                            <input type="date" class="form-control" id="dob" name="meta[dob]" value="<?php echo $is_edit ? ($application['meta']['dob'] ?? '') : ''; ?>">
                        </div>
                    </div>
                </fieldset>

                <!-- Personal Information -->
                <fieldset class="mb-4 p-4 rounded-3" style="background-color: var(--accent-color);">
                    <legend class="h5 fw-bold text-primary mb-4 border-bottom pb-2">Personal Information</legend>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="first_name" class="form-label fw-bold">First Name</label>
                            <?php
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
                            <label for="middle_name" class="form-label fw-bold">Middle Name</label>
                            <input type="text" class="form-control" id="middle_name" name="meta[middle_name]" value="<?php echo $is_edit ? ($application['meta']['middle_name'] ?? '') : ''; ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="last_name" class="form-label fw-bold">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="customer[last_name]" value="<?php echo $last_name; ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label for="email" class="form-label fw-bold">Email</label>
                            <input type="email" class="form-control" id="email" name="customer[email]" value="<?php echo $is_edit ? $application['customer_email'] : ''; ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="phone" class="form-label fw-bold">Phone</label>
                            <input type="tel" class="form-control" id="phone" name="customer[phone]" value="<?php echo $is_edit ? $application['customer_phone'] : ''; ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label for="pan" class="form-label fw-bold">PAN</label>
                            <input type="text" class="form-control" id="pan" name="meta[pan]" value="<?php echo $is_edit ? ($application['meta']['pan'] ?? '') : ''; ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="mother_name" class="form-label fw-bold">Mother's Name</label>
                            <input type="text" class="form-control" id="mother_name" name="meta[mother_name]" value="<?php echo $is_edit ? ($application['meta']['mother_name'] ?? '') : ''; ?>">
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
                                <?php
                                    $opts = ['Salaried', 'Self-Employed', 'Business'];
                                    foreach($opts as $opt) {
                                        $selected = ($is_edit && ($application['meta']['employment_type'] ?? '') == $opt) ? 'selected' : '';
                                        echo "<option value='$opt' $selected>$opt</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="company_name" class="form-label fw-bold">Company Name</label>
                            <input type="text" class="form-control" id="company_name" name="meta[company_name]" value="<?php echo $is_edit ? ($application['meta']['company_name'] ?? '') : ''; ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="company_type" class="form-label fw-bold">Company Type</label>
                            <select class="form-select" id="company_type" name="meta[company_type]">
                                <option selected>Select Company Type</option>
                                <?php
                                    $opts = ['Private', 'Public', 'Government', 'NGO', 'PSU', 'Partnership', 'Proprietorship', 'LLP'];
                                    foreach($opts as $opt) {
                                        $selected = ($is_edit && ($application['meta']['company_type'] ?? '') == $opt) ? 'selected' : '';
                                        echo "<option value='$opt' $selected>$opt</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="monthly_income" class="form-label fw-bold">Monthly Income</label>
                            <input type="number" class="form-control" id="monthly_income" name="meta[monthly_income]" value="<?php echo $is_edit ? ($application['meta']['monthly_income'] ?? '') : ''; ?>">
                        </div>
                    </div>
                </fieldset>

                <!-- Address & References -->
                <fieldset class="mb-4 p-4 rounded-3" style="background-color: var(--accent-color);">
                    <legend class="h5 fw-bold text-primary mb-4 border-bottom pb-2">Address & References</legend>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="residence_address" class="form-label fw-bold">Residence Address</label>
                            <textarea class="form-control" id="residence_address" name="meta[residence_address]" rows="2"><?php echo $is_edit ? ($application['meta']['residence_address'] ?? '') : ''; ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="residence_pincode" class="form-label fw-bold">Residence Pincode</label>
                            <input type="text" class="form-control" id="residence_pincode" name="meta[residence_pincode]" value="<?php echo $is_edit ? ($application['meta']['residence_pincode'] ?? '') : ''; ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="ref_name_1" class="form-label fw-bold">Reference Name 1</label>
                            <input type="text" class="form-control" id="ref_name_1" name="meta[ref_name_1]" value="<?php echo $is_edit ? ($application['meta']['ref_name_1'] ?? '') : ''; ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="ref_phone_1" class="form-label fw-bold">Reference Phone 1</label>
                            <input type="tel" class="form-control" id="ref_phone_1" name="meta[ref_phone_1]" value="<?php echo $is_edit ? ($application['meta']['ref_phone_1'] ?? '') : ''; ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="ref_name_2" class="form-label fw-bold">Reference Name 2</label>
                            <input type="text" class="form-control" id="ref_name_2" name="meta[ref_name_2]" value="<?php echo $is_edit ? ($application['meta']['ref_name_2'] ?? '') : ''; ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="ref_phone_2" class="form-label fw-bold">Reference Phone 2</label>
                            <input type="tel" class="form-control" id="ref_phone_2" name="meta[ref_phone_2]" value="<?php echo $is_edit ? ($application['meta']['ref_phone_2'] ?? '') : ''; ?>">
                        </div>
                    </div>
                </fieldset>

                <!-- Document Upload -->
                <fieldset class="p-4 rounded-3" style="background-color: var(--accent-color);">
                    <legend class="h5 fw-bold text-primary mb-4 border-bottom pb-2">Document Upload (All Optional)</legend>
                    <div class="row g-3">
                        <?php
                            // Default Documents
                            $docs = [
                                'aadhaar_front' => 'Aadhar Card Front',
                                'aadhaar_back' => 'Aadhar Card Back',
                                'pan_card' => 'Pan Card',
                                'passport_photo' => 'Passport Photo'
                            ];

                            // Determine Service Type
                            $svc_name = strtoupper($service['name']);

                            if (strpos($svc_name, 'PERSONAL LOAN') !== false) {
                                $docs['salary_slip_1m'] = '1 Month Salary Slip';
                                $docs['salary_slip_2m'] = '2 Month Salary Slip';
                                $docs['salary_slip_3m'] = '3 Month Salary Slip';
                                $docs['bank_statement'] = 'Bank Statement (1 Year)';
                                $docs['electric_bill'] = 'Electric Bill';
                            }
                            elseif (strpos($svc_name, 'BUSINESS LOAN') !== false) {
                                $docs['itr_file'] = 'ITR File (2 Years)';
                                $docs['business_proof'] = 'Business Proof (1 Year)';
                                $docs['electric_bill'] = 'Electric Bill';
                            }
                            elseif (strpos($svc_name, 'HOME LOAN') !== false || strpos($svc_name, 'LOAN AGAINST PROPERTY') !== false) {
                                $docs['salary_slip_1m'] = '1 Month Salary Slip';
                                $docs['salary_slip_2m'] = '2 Month Salary Slip';
                                $docs['salary_slip_3m'] = '3 Month Salary Slip';
                                $docs['bank_statement'] = 'Bank Statement (1 Year)';
                                $docs['itr_file'] = 'ITR File (3 Years)';
                                $docs['business_proof'] = 'Business Proof (3 Years)';
                                $docs['chain_deed'] = 'Complete Chain Deed';
                                $docs['registration_paper'] = 'Registration Paper';
                                $docs['side_plan'] = 'Side Plan';
                                $docs['building_plan'] = 'Building Plan';
                            }
                            elseif (strpos($svc_name, 'CAR LOAN') !== false || strpos($svc_name, 'OLD CAR LOAN') !== false) {
                                // Only basic docs + RC
                                $docs['rc'] = 'RC';
                            }
                            else {
                                // Fallback for generic private loans
                                $docs['salary_slip_1m'] = '1 Month Salary Slip';
                                $docs['bank_statement'] = 'Bank Statement';
                            }

                            foreach ($docs as $key => $label):
                        ?>
                        <div class="col-md-3">
                            <label class="form-label"><?php echo $label; ?></label>
                            <input type="file" class="form-control" name="docs[<?php echo $key; ?>]" onchange="previewDocument(this)">
                            <div class="mt-2 preview-container">
                                <?php
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
</script>

<?php view('layouts/partner_footer'); ?>
