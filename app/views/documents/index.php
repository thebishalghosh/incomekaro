<?php view('layouts/header', ['title' => 'Company Documents']); ?>

<!-- Company Documents Section -->
<div class="company-documents-section py-5" style="background-color: #f8f9fa; min-height: 100vh;">
    <div class="container">
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <!-- Header -->
                <div class="mb-5 text-center">
                    <h1 class="display-5 fw-bold mb-3" style="color: #0f172a;">Company Documents</h1>
                    <p class="lead text-muted">Important company documents and certifications</p>
                </div>

                <!-- Content -->
                <div class="company-documents-content">

                    <!-- Documents Grid -->
                    <div class="row g-4 mb-5">

                        <!-- Document 1 -->
                        <div class="col-lg-6 col-md-12">
                            <div class="card h-100 border-0 shadow-sm" style="transition: all 0.3s ease;">
                                <div class="card-body p-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; position: relative; overflow: hidden;">
                                    <!-- Background Icon -->
                                    <div style="position: absolute; top: -50px; right: -50px; font-size: 200px; opacity: 0.1;">
                                        <i class="fas fa-file-pdf"></i>
                                    </div>
                                    
                                    <div style="position: relative; z-index: 1;">
                                        <div class="mb-3">
                                            <i class="fas fa-file-pdf" style="font-size: 40px;"></i>
                                        </div>
                                        <h5 class="card-title fw-bold mb-2">Company PAN Card</h5>
                                        <p class="card-text text-white-50 small mb-4">Official company Permanent Account Number (PAN) certificate issued by Income Tax Department</p>
                                        <a href="/documents/pan.pdf" class="btn btn-light btn-sm" target="_blank" download>
                                            <i class="fas fa-download me-2"></i> Download PDF
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Document 2 -->
                        <div class="col-lg-6 col-md-12">
                            <div class="card h-100 border-0 shadow-sm" style="transition: all 0.3s ease;">
                                <div class="card-body p-4" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; position: relative; overflow: hidden;">
                                    <!-- Background Icon -->
                                    <div style="position: absolute; top: -50px; right: -50px; font-size: 200px; opacity: 0.1;">
                                        <i class="fas fa-file-pdf"></i>
                                    </div>
                                    
                                    <div style="position: relative; z-index: 1;">
                                        <div class="mb-3">
                                            <i class="fas fa-file-pdf" style="font-size: 40px;"></i>
                                        </div>
                                        <h5 class="card-title fw-bold mb-2">Company Certificate</h5>
                                        <p class="card-text text-white-50 small mb-4">Official company registration certificate and business authorization document</p>
                                        <a href="/documents/CertificateofIncorporation.089b160ab358152467bd.pdf" class="btn btn-light btn-sm" target="_blank" download>
                                            <i class="fas fa-download me-2"></i> Download PDF
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Information Box -->
                    <div class="mb-5 p-4 rounded" style="background-color: #e8f4f8; border-left: 4px solid #0f172a;">
                        <h5 class="fw-bold mb-3" style="color: #0f172a;">About These Documents</h5>
                        <p class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> All documents are official and verified</p>
                        <p class="mb-2"><i class="fas fa-lock text-primary me-2"></i> Your information is protected and secure</p>
                        <p class="mb-0"><i class="fas fa-download text-info me-2"></i> Documents are available for download in PDF format</p>
                    </div>

                    <!-- Document Table/List -->
                    <div class="table-responsive">
                        <table class="table table-hover" style="background-color: white;">
                            <thead style="background-color: #f8f9fa; border-top: 2px solid #0f172a;">
                                <tr>
                                    <th style="color: #0f172a;"><i class="fas fa-file-alt me-2"></i> Document Name</th>
                                    <th style="color: #0f172a;">Type</th>
                                    <th style="color: #0f172a;" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Company PAN Card</strong></td>
                                    <td><span class="badge bg-primary">PAN Certificate</span></td>
                                    <td class="text-center">
                                        <a href="/documents/pan.pdf" class="btn btn-sm btn-outline-primary" target="_blank" download>
                                            <i class="fas fa-download me-1"></i> Download
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Company Certificate</strong></td>
                                    <td><span class="badge bg-success">Certificate</span></td>
                                    <td class="text-center">
                                        <a href="/documents/CertificateofIncorporation.089b160ab358152467bd.pdf" class="btn btn-sm btn-outline-primary" target="_blank" download>
                                            <i class="fas fa-download me-1"></i> Download
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Footer Note -->
                    <div class="alert alert-info mt-5">
                        <p class="mb-0"><strong>Note:</strong> For any questions regarding these documents or verification, please contact us at <a href="mailto:support@incomekaro.in">support@incomekaro.in</a></p>
                    </div>

                </div>

                <!-- Back Button -->
                <div class="mt-5 text-center">
                    <a href="<?php echo url('/'); ?>" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i> Back to Home
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>

<?php view('layouts/footer'); ?>
