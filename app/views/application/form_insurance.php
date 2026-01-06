<?php
$is_edit = isset($application);
$title = $is_edit ? 'Edit Application: ' . $service['name'] : 'New ' . $service['name'];
view('layouts/partner_header', ['title' => $title]);
?>

<div class="container-fluid">
    <div class="text-center mb-5 pt-4">
        <h1 class="fw-bold display-5 text-dark"><?php echo $is_edit ? 'Edit Application' : 'New Application'; ?>: <span class="text-primary"><?php echo $service['name']; ?></span></h1>
        <p class="lead text-muted">Please fill out the client's details for insurance.</p>
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
                            <label for="middle_name" class="form-label fw-bold">Middle Name</label>
                            <input type="text" class="form-control" id="middle_name" name="meta[middle_name]" value="<?php echo $is_edit ? ($application['meta']['middle_name'] ?? '') : ''; ?>">
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
                            <label for="phone" class="form-label fw-bold">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="customer[phone]" value="<?php echo $is_edit ? $application['customer_phone'] : ''; ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label for="pan" class="form-label fw-bold">PAN Number</label>
                            <input type="text" class="form-control" id="pan" name="meta[pan_number]" value="<?php echo $is_edit ? ($application['meta']['pan_number'] ?? '') : ''; ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label for="aadhaar" class="form-label fw-bold">Aadhar Number</label>
                            <input type="text" class="form-control" id="aadhaar" name="meta[aadhaar_number]" value="<?php echo $is_edit ? ($application['meta']['aadhaar_number'] ?? '') : ''; ?>">
                        </div>
                    </div>
                </fieldset>

                <!-- Address Information -->
                <fieldset class="mb-4 p-4 rounded-3" style="background-color: var(--accent-color);">
                    <legend class="h5 fw-bold text-primary mb-4 border-bottom pb-2">Address Information</legend>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="residence_address" class="form-label fw-bold">Residence Address</label>
                            <textarea class="form-control" id="residence_address" name="meta[residence_address]" rows="2"><?php echo $is_edit ? ($application['meta']['residence_address'] ?? '') : ''; ?></textarea>
                        </div>
                        <div class="col-md-4">
                            <label for="residence_city" class="form-label fw-bold">Residence City</label>
                            <input type="text" class="form-control" id="residence_city" name="meta[residence_city]" value="<?php echo $is_edit ? ($application['meta']['residence_city'] ?? '') : ''; ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="residence_pincode" class="form-label fw-bold">Residence Pincode</label>
                            <input type="text" class="form-control" id="residence_pincode" name="meta[residence_pincode]" value="<?php echo $is_edit ? ($application['meta']['residence_pincode'] ?? '') : ''; ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="residence_state" class="form-label fw-bold">Residence State</label>
                            <input type="text" class="form-control" id="residence_state" name="meta[residence_state]" value="<?php echo $is_edit ? ($application['meta']['residence_state'] ?? '') : ''; ?>">
                        </div>
                    </div>
                </fieldset>

                <!-- Insurance Information -->
                <fieldset class="mb-4 p-4 rounded-3" style="background-color: var(--accent-color);">
                    <legend class="h5 fw-bold text-primary mb-4 border-bottom pb-2">Insurance Information</legend>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="insurance_type" class="form-label fw-bold">Insurance Type</label>
                            <select class="form-select" id="insurance_type" name="meta[insurance_type]">
                                <option selected>Select Insurance Type</option>
                                <?php
                                    $opts = ['Life Insurance', 'Health Insurance', 'Vehicle Insurance'];
                                    foreach($opts as $opt) {
                                        $selected = ($is_edit && ($application['meta']['insurance_type'] ?? '') == $opt) ? 'selected' : '';
                                        echo "<option value='$opt' $selected>$opt</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="vehicle_number" class="form-label fw-bold">Bike/Car Number</label>
                            <input type="text" class="form-control" id="vehicle_number" name="meta[vehicle_number]" placeholder="If applicable" value="<?php echo $is_edit ? ($application['meta']['vehicle_number'] ?? '') : ''; ?>">
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
                                'rc' => 'RC'
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
