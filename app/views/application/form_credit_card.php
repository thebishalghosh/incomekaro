<?php
$is_edit = isset($application);
$title = $is_edit ? 'Edit Application: ' . $service['name'] : 'New ' . $service['name'];
view('layouts/partner_header', ['title' => $title]);
?>

<div class="container-fluid">
    <div class="text-center mb-5 pt-4">
        <h1 class="fw-bold display-5 text-dark"><?php echo $is_edit ? 'Edit Application' : 'New Application'; ?>: <span class="text-primary"><?php echo $service['name']; ?></span></h1>
        <p class="lead text-muted">Please fill out the client's details for a new credit card.</p>
    </div>

    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-body p-4 p-md-5">
            <form action="<?php echo $is_edit ? url('application/update/' . $application['id']) : url('application/store'); ?>" method="POST" enctype="multipart/form-data">
                <?php if (!$is_edit): ?>
                    <input type="hidden" name="service_id" value="<?php echo $service['id']; ?>">
                <?php endif; ?>

                <!-- Personal Information -->
                <fieldset class="mb-4 p-4 rounded-3" style="background-color: var(--accent-color);">
                    <legend class="h5 fw-bold text-primary mb-4 border-bottom pb-2">Personal Information</legend>
                    <div class="row g-3">
                        <div class="col-md-4">
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
                        <div class="col-md-4">
                            <label for="last_name" class="form-label fw-bold">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="customer[last_name]" value="<?php echo $last_name; ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label for="email" class="form-label fw-bold">Email</label>
                            <input type="email" class="form-control" id="email" name="customer[email]" value="<?php echo $is_edit ? $application['customer_email'] : ''; ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="phone" class="form-label fw-bold">Phone</label>
                            <input type="tel" class="form-control" id="phone" name="customer[phone]" value="<?php echo $is_edit ? $application['customer_phone'] : ''; ?>" required>
                        </div>
                        <div class="col-md-4">
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
                        <div class="col-md-4">
                            <label for="dob" class="form-label fw-bold">Date of Birth</label>
                            <input type="date" class="form-control" id="dob" name="meta[dob]" value="<?php echo $is_edit ? ($application['meta']['dob'] ?? '') : ''; ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="qualification" class="form-label fw-bold">Qualification</label>
                            <input type="text" class="form-control" id="qualification" name="meta[qualification]" value="<?php echo $is_edit ? ($application['meta']['qualification'] ?? '') : ''; ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="permanent_pincode" class="form-label fw-bold">Permanent Pincode</label>
                            <input type="text" class="form-control" id="permanent_pincode" name="meta[permanent_pincode]" value="<?php echo $is_edit ? ($application['meta']['permanent_pincode'] ?? '') : ''; ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="current_pincode" class="form-label fw-bold">Current Pincode</label>
                            <input type="text" class="form-control" id="current_pincode" name="meta[current_pincode]" value="<?php echo $is_edit ? ($application['meta']['current_pincode'] ?? '') : ''; ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="mother_name" class="form-label fw-bold">Mother's Name</label>
                            <input type="text" class="form-control" id="mother_name" name="meta[mother_name]" value="<?php echo $is_edit ? ($application['meta']['mother_name'] ?? '') : ''; ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="pan" class="form-label fw-bold">PAN</label>
                            <input type="text" class="form-control" id="pan" name="meta[pan_number]" value="<?php echo $is_edit ? ($application['meta']['pan_number'] ?? '') : ''; ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label for="has_credit_card" class="form-label fw-bold">Have a Credit Card</label>
                            <select class="form-select" id="has_credit_card" name="meta[has_credit_card]">
                                <option selected>Select Option</option>
                                <?php
                                    $opts = ['Yes', 'No'];
                                    foreach($opts as $opt) {
                                        $selected = ($is_edit && ($application['meta']['has_credit_card'] ?? '') == $opt) ? 'selected' : '';
                                        echo "<option value='$opt' $selected>$opt</option>";
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                </fieldset>

                <!-- Employment Details -->
                <fieldset class="mb-4 p-4 rounded-3" style="background-color: var(--accent-color);">
                    <legend class="h5 fw-bold text-primary mb-4 border-bottom pb-2">Employment Details</legend>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="company_name" class="form-label fw-bold">Company Name</label>
                            <input type="text" class="form-control" id="company_name" name="meta[company_name]" value="<?php echo $is_edit ? ($application['meta']['company_name'] ?? '') : ''; ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="designation" class="form-label fw-bold">Designation</label>
                            <input type="text" class="form-control" id="designation" name="meta[designation]" value="<?php echo $is_edit ? ($application['meta']['designation'] ?? '') : ''; ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="income" class="form-label fw-bold">Income</label>
                            <input type="number" class="form-control" id="income" name="meta[income]" value="<?php echo $is_edit ? ($application['meta']['income'] ?? '') : ''; ?>">
                        </div>
                        <div class="col-md-4">
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
                        <div class="col-md-4">
                            <label for="profession" class="form-label fw-bold">Profession</label>
                            <input type="text" class="form-control" id="profession" name="meta[profession]" value="<?php echo $is_edit ? ($application['meta']['profession'] ?? '') : ''; ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="office_pincode" class="form-label fw-bold">Office Pincode</label>
                            <input type="text" class="form-control" id="office_pincode" name="meta[office_pincode]" value="<?php echo $is_edit ? ($application['meta']['office_pincode'] ?? '') : ''; ?>">
                        </div>
                    </div>
                </fieldset>

                <!-- Document Upload -->
                <fieldset class="p-4 rounded-3" style="background-color: var(--accent-color);">
                    <legend class="h5 fw-bold text-primary mb-4 border-bottom pb-2">Document Upload (All Optional)</legend>
                    <div class="row g-3">
                        <?php
                            $docs = [
                                'aadhaar_front' => 'Aadhar Card Front',
                                'aadhaar_back' => 'Aadhar Card Back',
                                'pan_card' => 'Pan Card',
                                'passport_photo' => 'Passport Photo',
                                'bank_statement_1y' => '1 Year Bank Statement',
                                'salary_slip_1m' => '1 Month Salary Slip',
                                'salary_slip_2m' => '2 Month Salary Slip',
                                'salary_slip_3m' => '3 Month Salary Slip'
                            ];
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
