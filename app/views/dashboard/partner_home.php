<?php view('layouts/partner_header', ['title' => 'Partner Dashboard']); ?>

<div class="container">
    <!-- Partner Hero Section -->
    <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, var(--accent-color) 0%, #ffffff 100%);">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-auto">
                    <?php if (!empty($partner['profile']['profile_image'])): ?>
                        <img src="<?php echo asset($partner['profile']['profile_image']); ?>" alt="Profile" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover; border: 4px solid #fff;">
                    <?php else: ?>
                        <div class="avatar-placeholder mx-auto" style="width: 100px; height: 100px; font-size: 2.5rem; border: 4px solid #fff;">
                            <?php echo strtoupper(substr($partner['profile']['full_name'], 0, 1)); ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md">
                    <h2 class="fw-bold mb-1"><?php echo $partner['profile']['full_name']; ?></h2>
                    <p class="text-muted mb-2">Partner ID: <?php echo $partner['id']; ?></p>
                    <div class="d-flex flex-wrap">
                        <span class="me-4"><i class="fas fa-envelope me-2 text-muted"></i><?php echo $partner['profile']['email']; ?></span>
                        <span><i class="fas fa-phone me-2 text-muted"></i><?php echo $partner['profile']['mobile']; ?></span>
                    </div>
                </div>
                <div class="col-md-auto text-end">
                    <?php
                        $kyc_status = $partner['kyc_status'] ?? 'PENDING';
                        $kyc_badge_class = 'bg-warning text-dark';
                        if ($kyc_status == 'VERIFIED') $kyc_badge_class = 'bg-success';
                        elseif ($kyc_status == 'REJECTED') $kyc_badge_class = 'bg-danger';
                    ?>
                    <div class="mb-2">KYC Status</div>
                    <span class="badge <?php echo $kyc_badge_class; ?> fs-6"><?php echo $kyc_status; ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Info Row -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="d-flex align-items-center p-3 bg-white rounded shadow-sm h-100">
                <div class="icon-box bg-light text-primary rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="fas fa-tags fa-lg"></i>
                </div>
                <div>
                    <p class="text-muted mb-1 small text-uppercase fw-bold">Plan Name</p>
                    <h6 class="fw-bold mb-0 text-dark"><?php echo $partner['subscription']['plan_name'] ?? 'Not Subscribed'; ?></h6>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="d-flex align-items-center p-3 bg-white rounded shadow-sm h-100">
                <div class="icon-box bg-light text-success rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="fas fa-user-tie fa-lg"></i>
                </div>
                <div>
                    <p class="text-muted mb-1 small text-uppercase fw-bold">Relationship Manager</p>
                    <h6 class="fw-bold mb-0 text-dark"><?php echo $partner['rm_first'] ? $partner['rm_first'] . ' ' . $partner['rm_last'] : 'Not Assigned'; ?></h6>
                    <?php if ($partner['rm_phone']): ?>
                        <p class="small text-muted mb-0"><i class="fas fa-phone-alt me-1"></i> <?php echo $partner['rm_phone']; ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="d-flex align-items-center p-3 bg-white rounded shadow-sm h-100">
                <div class="icon-box bg-light text-warning rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="fas fa-calendar-alt fa-lg"></i>
                </div>
                <div>
                    <p class="text-muted mb-1 small text-uppercase fw-bold">Joining Date</p>
                    <h6 class="fw-bold mb-0 text-dark"><?php echo date('d M Y', strtotime($partner['created_at'])); ?></h6>
                    <p class="small text-muted mb-0"><?php echo $partner['address_permanent']['state'] ?? ''; ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card text-center h-100 shadow-sm stat-card purple">
                <div class="card-body d-flex flex-column justify-content-center">
                    <div class="icon-box mx-auto">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <h5 class="card-title fw-bold mt-3">IncomeKaro Certificate</h5>
                    <a href="<?php echo url('certificate/download'); ?>" class="btn btn-primary mt-3" target="_blank"><i class="fas fa-download me-2"></i>Download</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center h-100 shadow-sm stat-card blue">
                <div class="card-body d-flex flex-column justify-content-center">
                    <div class="icon-box mx-auto">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <h5 class="card-title fw-bold mt-3">Authorization</h5>
                    <a href="<?php echo url('authorization/download'); ?>" class="btn btn-primary mt-3" target="_blank"><i class="fas fa-download me-2"></i>Download</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center h-100 shadow-sm stat-card green">
                <div class="card-body d-flex flex-column justify-content-center">
                    <div class="icon-box mx-auto">
                        <i class="fas fa-file-contract"></i>
                    </div>
                    <h5 class="card-title fw-bold mt-3">IncomeKaro Agreement</h5>
                    <a href="<?php echo url('agreement/download'); ?>" class="btn btn-primary mt-3" target="_blank"><i class="fas fa-download me-2"></i>Download</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Due Alert -->
    <?php if (!empty($partner['subscription']['due_amount']) && $partner['subscription']['due_amount'] > 0): ?>
        <div class="alert alert-danger shadow-sm border-0 d-flex align-items-center mb-4" role="alert">
            <i class="fas fa-exclamation-circle fa-2x me-3"></i>
            <div>
                <h5 class="alert-heading fw-bold mb-1">Payment Due Alert</h5>
                <p class="mb-0">You have a pending due amount of <strong>₹<?php echo number_format($partner['subscription']['due_amount'], 2); ?></strong>. Please clear your dues to continue enjoying uninterrupted services.</p>
            </div>
        </div>
    <?php endif; ?>

    <!-- My Services Section -->
    <h4 class="fw-bold text-dark mb-3">My Services</h4>
    <?php if (!empty($partner['subscription']['services'])): ?>
        <div class="row g-4 mb-5">
            <?php
                // Filter for parent services only
                $parent_services = array_filter($partner['subscription']['services'], function($svc) {
                    return empty($svc['parent_id']); // Changed from is_null to empty to handle 0 or empty string
                });
            ?>
            <?php foreach ($parent_services as $svc): ?>
                <?php
                    $link = '#';
                    $target = '';
                    if ($svc['service_type'] === 'EXTERNAL_REDIRECT' && !empty($svc['url'])) {
                        $link = $svc['url'];
                        $target = '_blank';
                    } elseif ($svc['service_type'] === 'INTERNAL_FORM') {
                        // If it's a parent, it should go to the select page
                        $link = url('application/select/' . $svc['id']);
                    }
                ?>
                <div class="col-md-3 col-sm-6">
                    <a href="<?php echo $link; ?>" target="<?php echo $target; ?>" class="text-decoration-none">
                        <div class="card h-100 shadow-sm border-0 service-card">
                            <div class="card-body text-center p-4">
                                <?php if (!empty($svc['image_url'])): ?>
                                    <img src="<?php echo asset($svc['image_url']); ?>"
                                         alt="<?php echo $svc['name']; ?>"
                                         class="mb-3"
                                         style="height: 80px; object-fit: contain;"
                                         onerror="this.onerror=null; this.src='https://via.placeholder.com/80?text=Icon'; this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="icon-box bg-light text-primary rounded-circle mx-auto mb-3 align-items-center justify-content-center" style="width: 80px; height: 80px; display: none;">
                                        <i class="fas fa-box-open fa-2x"></i>
                                    </div>
                                <?php else: ?>
                                    <div class="icon-box bg-light text-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                        <i class="fas fa-box-open fa-2x"></i>
                                    </div>
                                <?php endif; ?>

                                <h6 class="card-title fw-bold text-dark mb-0"><?php echo $svc['name']; ?></h6>
                                <p class="small text-muted mt-2 mb-0"><?php echo $svc['category']; ?></p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-light border text-center py-4 mb-5">
            <p class="text-muted mb-0">No services are currently active for your plan.</p>
        </div>
    <?php endif; ?>

    <!-- Performance Overview -->
    <h4 class="fw-bold text-dark mb-3">Performance Overview</h4>

    <!-- Stat Cards by Category -->
    <div class="row g-4 mb-4 justify-content-center">
        <div class="col-lg-2 col-md-4">
            <div class="card text-white h-100" style="background-color: #6A5ACD;">
                <div class="card-body text-center">
                    <h1 class="fw-bold"><?php echo $stats['by_category']['LOAN'] ?? 0; ?></h1>
                    <p class="mb-0 small">Total Loans</p>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4">
            <div class="card text-white h-100" style="background-color: #20B2AA;">
                <div class="card-body text-center">
                    <h1 class="fw-bold"><?php echo $stats['by_category']['TAX'] ?? 0; ?></h1>
                    <p class="mb-0 small">Total Taxation</p>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4">
            <div class="card text-white h-100" style="background-color: #FF7F50;">
                <div class="card-body text-center">
                    <h1 class="fw-bold"><?php echo $stats['by_category']['CREDIT'] ?? 0; ?></h1>
                    <p class="mb-0 small">Total Credit Cards</p>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4">
            <div class="card text-white h-100" style="background-color: #4682B4;">
                <div class="card-body text-center">
                    <h1 class="fw-bold"><?php echo $stats['by_category']['INSURANCE'] ?? 0; ?></h1>
                    <p class="mb-0 small">Total Insurance</p>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4">
            <div class="card text-white bg-dark h-100">
                <div class="card-body text-center">
                    <h1 class="fw-bold"><?php echo $stats['total_applications'] ?? 0; ?></h1>
                    <p class="mb-0 small">Total Applications</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-4 ps-4">
                    <h5 class="fw-bold mb-0 text-primary">Application Status</h5>
                </div>
                <div class="card-body">
                    <div id="statusChart" style="width: 100%; height: 300px;"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-4 ps-4">
                    <h5 class="fw-bold mb-0 text-primary">Applications by Service</h5>
                </div>
                <div class="card-body">
                    <div id="serviceChart" style="width: 100%; height: 300px;"></div>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
.service-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.service-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
}
</style>

<!-- Google Charts -->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawCharts);

    function drawCharts() {
        // 1. Status Chart
        var statusData = google.visualization.arrayToDataTable([
            ['Status', 'Count'],
            <?php
                if (!empty($stats['by_status'])) {
                    foreach ($stats['by_status'] as $status => $count) {
                        echo "['" . str_replace('_', ' ', $status) . "', " . $count . "],";
                    }
                } else {
                    echo "['No Data', 1]"; // Placeholder
                }
            ?>
        ]);

        var statusOptions = {
            pieHole: 0.4,
            colors: ['#6A5ACD', '#20B2AA', '#FF7F50', '#FF6347', '#4682B4'],
            chartArea: {width: '90%', height: '80%'},
            legend: {position: 'bottom'},
            pieSliceText: 'value'
        };

        var statusChart = new google.visualization.PieChart(document.getElementById('statusChart'));
        statusChart.draw(statusData, statusOptions);

        // 2. Service Chart
        var serviceData = google.visualization.arrayToDataTable([
            ['Service', 'Applications', { role: 'style' }],
            <?php
                if (!empty($stats['by_root_service'])) {
                    $colors = ['#6A5ACD', '#20B2AA', '#FF7F50', '#FF6347', '#4682B4'];
                    $i = 0;
                    foreach ($stats['by_root_service'] as $name => $count) {
                        $color = $colors[$i % count($colors)];
                        echo "['" . $name . "', " . $count . ", '$color'],";
                        $i++;
                    }
                } else {
                    echo "['No Data', 0, '#ccc']";
                }
            ?>
        ]);

        var serviceOptions = {
            legend: { position: "none" },
            chartArea: {width: '80%', height: '70%'},
            vAxis: { minValue: 0, format: '0' }
        };

        var serviceChart = new google.visualization.ColumnChart(document.getElementById('serviceChart'));
        serviceChart.draw(serviceData, serviceOptions);
    }

    // Make charts responsive
    window.addEventListener('resize', drawCharts);
</script>

<?php view('layouts/partner_footer'); ?>
