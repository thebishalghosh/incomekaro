<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete KYC - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">
    <style>
        body {
            background-color: var(--light-bg);
        }
        .kyc-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 40px 0;
            text-align: center;
            margin-bottom: 40px;
        }
        .upload-card {
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s ease;
            background-color: #fff;
            cursor: pointer;
            position: relative;
        }
        .upload-card:hover {
            border-color: var(--primary-color);
            background-color: #f8f9fa;
            transform: translateY(-5px);
        }
        .upload-card i {
            font-size: 3rem;
            color: #adb5bd;
            margin-bottom: 15px;
            transition: color 0.3s;
        }
        .upload-card:hover i {
            color: var(--primary-color);
        }
        .upload-card h6 {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .upload-card p {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 0;
        }
        .file-input {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }
        .status-badge {
            position: absolute;
            top: 10px;
            right: 10px;
        }
    </style>
</head>
<body>

<div class="kyc-header shadow-sm">
    <div class="container">
        <h1 class="fw-bold mb-2"><i class="fas fa-shield-alt me-3"></i>Complete Your KYC</h1>
        <p class="lead mb-0 opacity-75">Upload your documents to unlock your partner dashboard.</p>
    </div>
</div>

<div class="container mb-5">

    <?php flash('kyc_success'); ?>
    <?php flash('kyc_error'); ?>

    <?php if (!empty($partner['documents'])): ?>
        <div class="alert alert-info text-center shadow-sm border-0 mb-5">
            <h4 class="alert-heading fw-bold"><i class="fas fa-clock me-2"></i>Verification in Progress</h4>
            <p class="mb-0">You have submitted your documents. Our team is reviewing them. You will be notified once verified.</p>
        </div>
    <?php endif; ?>

    <form action="<?php echo url('kyc/upload'); ?>" method="POST" enctype="multipart/form-data">
        <div class="row g-4 justify-content-center">

            <!-- Aadhaar Front -->
            <div class="col-md-4">
                <div class="upload-card shadow-sm">
                    <i class="fas fa-id-card"></i>
                    <h6>Aadhaar Card (Front)</h6>
                    <p>Click to upload front side</p>
                    <input type="file" name="aadhaar_front" class="file-input" accept="image/*,application/pdf" onchange="updateCard(this)">
                    <?php if (has_document($partner['documents'], 'AADHAAR_FRONT')): ?>
                        <span class="badge bg-success status-badge"><i class="fas fa-check"></i> Uploaded</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Aadhaar Back -->
            <div class="col-md-4">
                <div class="upload-card shadow-sm">
                    <i class="fas fa-id-card"></i>
                    <h6>Aadhaar Card (Back)</h6>
                    <p>Click to upload back side</p>
                    <input type="file" name="aadhaar_back" class="file-input" accept="image/*,application/pdf" onchange="updateCard(this)">
                    <?php if (has_document($partner['documents'], 'AADHAAR_BACK')): ?>
                        <span class="badge bg-success status-badge"><i class="fas fa-check"></i> Uploaded</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- PAN Card -->
            <div class="col-md-4">
                <div class="upload-card shadow-sm">
                    <i class="fas fa-address-card"></i>
                    <h6>PAN Card</h6>
                    <p>Click to upload PAN card</p>
                    <input type="file" name="pan_card" class="file-input" accept="image/*,application/pdf" onchange="updateCard(this)">
                    <?php if (has_document($partner['documents'], 'PAN_CARD')): ?>
                        <span class="badge bg-success status-badge"><i class="fas fa-check"></i> Uploaded</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Cancelled Cheque -->
            <div class="col-md-4">
                <div class="upload-card shadow-sm">
                    <i class="fas fa-money-check"></i>
                    <h6>Cancelled Cheque</h6>
                    <p>Click to upload cheque leaf</p>
                    <input type="file" name="cheque" class="file-input" accept="image/*,application/pdf" onchange="updateCard(this)">
                    <?php if (has_document($partner['documents'], 'CHEQUE')): ?>
                        <span class="badge bg-success status-badge"><i class="fas fa-check"></i> Uploaded</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Certificate (Optional) -->
            <div class="col-md-4">
                <div class="upload-card shadow-sm">
                    <i class="fas fa-certificate"></i>
                    <h6>Certificate (Optional)</h6>
                    <p>Any other relevant certificate</p>
                    <input type="file" name="certificate" class="file-input" accept="image/*,application/pdf" onchange="updateCard(this)">
                    <?php if (has_document($partner['documents'], 'CERTIFICATE')): ?>
                        <span class="badge bg-success status-badge"><i class="fas fa-check"></i> Uploaded</span>
                    <?php endif; ?>
                </div>
            </div>

        </div>

        <div class="text-center mt-5">
            <button type="submit" class="btn btn-primary btn-lg px-5 shadow-lg fw-bold"><i class="fas fa-upload me-2"></i>Submit Documents</button>
        </div>
    </form>
</div>

<script>
function updateCard(input) {
    if (input.files && input.files[0]) {
        const card = input.parentElement;
        const fileName = input.files[0].name;
        card.style.borderColor = 'var(--primary-color)';
        card.style.backgroundColor = '#f0f4ff';
        card.querySelector('p').textContent = 'Selected: ' + fileName;
        card.querySelector('i').classList.remove('text-muted');
        card.querySelector('i').classList.add('text-primary');
    }
}
</script>

</body>
</html>

<?php
function has_document($documents, $type) {
    if (empty($documents)) return false;
    foreach ($documents as $doc) {
        if ($doc['document_type'] === $type) return true;
    }
    return false;
}
?>
