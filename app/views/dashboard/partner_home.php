<?php
    view('layouts/partner_header', ['title' => 'Partner Dashboard']);

    // Fetch dynamic company details
    require_once APP_PATH . '/core/helpers.php'; // Ensure helper is loaded
    $company = get_company_details();
?>

<style>
    :root {
        --primary-color: <?php echo get_primary_color(); ?>;
        --secondary-color: <?php echo get_secondary_color(); ?>;
        --bg-color: #f8f9fa;
        --card-bg: #ffffff;
        --text-dark: #1e293b;
        --text-muted: #64748b;
        --border-radius: 16px;
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    body {
        background-color: #f1f5f9;
        font-family: 'Inter', sans-serif;
        color: var(--text-dark);
    }

    .dashboard-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 30px 20px;
    }

    /* Animations */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .animate-in {
        animation: fadeInUp 0.6s ease-out forwards;
        opacity: 0;
    }

    .delay-1 { animation-delay: 0.1s; }
    .delay-2 { animation-delay: 0.2s; }
    .delay-3 { animation-delay: 0.3s; }
    .delay-4 { animation-delay: 0.4s; }

    /* Hero Section */
    .hero-card {
        background: white;
        border-radius: var(--border-radius);
        padding: 30px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        border: 1px solid rgba(0,0,0,0.05);
        margin-bottom: 30px;
    }

    .hero-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 300px;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(var(--primary-rgb), 0.05));
        clip-path: polygon(20% 0%, 100% 0, 100% 100%, 0% 100%);
    }

    .profile-avatar {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid white;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transition: var(--transition);
    }

    .hero-card:hover .profile-avatar {
        transform: scale(1.05);
    }

    /* Stat Cards */
    .stat-card {
        background: white;
        border-radius: var(--border-radius);
        padding: 24px;
        height: 100%;
        border: 1px solid rgba(0,0,0,0.05);
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        transition: var(--transition);
        position: relative;
        overflow: hidden;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.08);
        border-color: rgba(var(--primary-rgb), 0.2);
    }

    .stat-card::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 3px;
        background: var(--primary-color);
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.3s ease;
    }

    .stat-card:hover::after {
        transform: scaleX(1);
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        margin-bottom: 16px;
        transition: var(--transition);
    }

    .stat-card:hover .stat-icon {
        transform: rotate(10deg) scale(1.1);
    }

    /* Action Cards */
    .action-card {
        background: white;
        border-radius: var(--border-radius);
        padding: 24px;
        text-align: center;
        border: 1px solid rgba(0,0,0,0.05);
        transition: var(--transition);
        height: 100%;
        cursor: pointer;
    }

    .action-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    }

    .action-icon-circle {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: #f8fafc;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
        font-size: 1.5rem;
        transition: var(--transition);
    }

    .action-card:hover .action-icon-circle {
        background: var(--primary-color);
        color: white;
        box-shadow: 0 4px 12px rgba(var(--primary-rgb), 0.3);
    }

    /* Service Cards */
    .service-card {
        background: white;
        border-radius: var(--border-radius);
        padding: 20px;
        border: 1px solid rgba(0,0,0,0.05);
        transition: var(--transition);
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    .service-card:hover {
        border-color: var(--primary-color);
        box-shadow: 0 8px 20px rgba(0,0,0,0.06);
        transform: translateY(-3px);
    }

    .service-img-wrapper {
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 15px;
        transition: var(--transition);
    }

    .service-card:hover .service-img-wrapper {
        transform: scale(1.1);
    }

    /* Charts */
    .chart-card {
        background: white;
        border-radius: var(--border-radius);
        padding: 24px;
        border: 1px solid rgba(0,0,0,0.05);
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        height: 100%;
    }

    /* Utility */
    .text-primary-soft { color: var(--primary-color); opacity: 0.8; }
    .bg-primary-soft { background-color: rgba(var(--primary-rgb), 0.1); }
</style>

<div class="dashboard-container">

    <!-- Hero Section -->
    <div class="hero-card animate-in">
        <div class="d-flex align-items-center flex-wrap">
            <div class="me-4 mb-3 mb-md-0">
                <?php if (!empty($partner['profile']['profile_image'])): ?>
                    <img src="<?php echo asset($partner['profile']['profile_image']); ?>" alt="Profile" class="profile-avatar">
                <?php else: ?>
                    <div class="profile-avatar bg-light d-flex align-items-center justify-content-center text-primary fw-bold fs-2">
                        <?php echo strtoupper(substr($partner['profile']['full_name'], 0, 1)); ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="flex-grow-1">
                <h6 class="text-muted text-uppercase small fw-bold mb-1">Welcome Back</h6>
                <h2 class="fw-bold mb-1 text-dark"><?php echo $partner['profile']['full_name']; ?></h2>
                <div class="d-flex align-items-center gap-3 text-muted small">
                    <span><i class="fas fa-id-badge me-1"></i> <?php echo $partner['id']; ?></span>
                    <span class="d-none d-sm-inline">|</span>
                    <span><i class="fas fa-envelope me-1"></i> <?php echo $partner['profile']['email']; ?></span>
                </div>
            </div>
            <div class="text-end mt-3 mt-md-0">
                <?php
                    $kyc_status = $partner['kyc_status'] ?? 'PENDING';
                    $kyc_class = 'bg-warning-subtle text-warning';
                    $kyc_icon = 'fa-clock';
                    if ($kyc_status == 'VERIFIED') { $kyc_class = 'bg-success-subtle text-success'; $kyc_icon = 'fa-check-circle'; }
                    elseif ($kyc_status == 'REJECTED') { $kyc_class = 'bg-danger-subtle text-danger'; $kyc_icon = 'fa-times-circle'; }
                ?>
                <div class="badge <?php echo $kyc_class; ?> px-3 py-2 rounded-pill border border-0 d-inline-flex align-items-center">
                    <i class="fas <?php echo $kyc_icon; ?> me-2"></i> KYC <?php echo $kyc_status; ?>
                </div>
                <div class="mt-2 text-muted small">Joined: <?php echo date('d M Y', strtotime($partner['created_at'])); ?></div>
            </div>
        </div>
    </div>

    <!-- Payment Alert -->
    <?php if (!empty($partner['subscription']['due_amount']) && $partner['subscription']['due_amount'] > 0): ?>
        <div class="alert alert-danger border-0 shadow-sm rounded-3 d-flex align-items-center mb-4 animate-in delay-1" role="alert">
            <div class="me-3 text-danger"><i class="fas fa-exclamation-circle fa-2x"></i></div>
            <div>
                <h6 class="alert-heading fw-bold mb-0">Payment Due</h6>
                <p class="mb-0 small">Pending Amount: <strong>₹<?php echo number_format($partner['subscription']['due_amount'], 2); ?></strong>. Please clear your dues.</p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Stats Grid -->
    <div class="row g-4 mb-5">
        <div class="col-xl-3 col-md-6 animate-in delay-1">
            <div class="stat-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted small fw-bold text-uppercase mb-1">Wallet Balance</p>
                        <h3 class="fw-bold text-dark mb-0">₹<?php echo number_format($user['wallet_balance'] ?? 0, 2); ?></h3>
                    </div>
                    <div class="stat-icon bg-success-subtle text-success">
                        <i class="fas fa-wallet"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 animate-in delay-2">
            <div class="stat-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted small fw-bold text-uppercase mb-1">Current Plan</p>
                        <h4 class="fw-bold text-dark mb-0"><?php echo $partner['subscription']['plan_name'] ?? 'None'; ?></h4>
                    </div>
                    <div class="stat-icon bg-primary-subtle text-primary">
                        <i class="fas fa-crown"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 animate-in delay-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted small fw-bold text-uppercase mb-1">Total Applications</p>
                        <h3 class="fw-bold text-dark mb-0"><?php echo $stats['total_applications'] ?? 0; ?></h3>
                    </div>
                    <div class="stat-icon bg-info-subtle text-info">
                        <i class="fas fa-file-alt"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 animate-in delay-4">
            <div class="stat-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted small fw-bold text-uppercase mb-1">Approved</p>
                        <h3 class="fw-bold text-dark mb-0"><?php echo $stats['total_approved'] ?? 0; ?></h3>
                    </div>
                    <div class="stat-icon bg-warning-subtle text-warning">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-4 mb-5">
        <div class="col-md-4 animate-in delay-2">
            <div class="action-card" onclick="window.open('<?php echo url('certificate/download'); ?>', '_blank')">
                <div class="action-icon-circle">
                    <i class="fas fa-certificate"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1">Certificate</h6>
                <p class="text-muted small mb-0">Download Partnership Certificate</p>
            </div>
        </div>
        <div class="col-md-4 animate-in delay-3">
            <div class="action-card" onclick="window.open('<?php echo url('authorization/download'); ?>', '_blank')">
                <div class="action-icon-circle">
                    <i class="fas fa-user-check"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1">Authorization</h6>
                <p class="text-muted small mb-0">Download Authorization Letter</p>
            </div>
        </div>
        <div class="col-md-4 animate-in delay-4">
            <div class="action-card" onclick="window.open('<?php echo url('agreement/download'); ?>', '_blank')">
                <div class="action-icon-circle">
                    <i class="fas fa-file-contract"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1">Agreement</h6>
                <p class="text-muted small mb-0">View Signed Agreement</p>
            </div>
        </div>
    </div>

    <!-- Services -->
    <div class="d-flex align-items-center justify-content-between mb-4 animate-in">
        <h5 class="fw-bold text-dark mb-0">My Services</h5>
        <span class="badge bg-light text-dark border"><?php echo count($partner['subscription']['services'] ?? []); ?> Active</span>
    </div>

    <?php if (!empty($partner['subscription']['services'])): ?>
        <div class="row g-4 mb-5">
            <?php
                $parent_services = array_filter($partner['subscription']['services'], function($svc) {
                    return empty($svc['parent_id']);
                });
            ?>
            <?php foreach ($parent_services as $index => $svc): ?>
                <?php
                    $link = '#';
                    $target = '';
                    if ($svc['service_type'] === 'EXTERNAL_REDIRECT' && !empty($svc['url'])) {
                        $link = $svc['url'];
                        $target = '_blank';
                    } elseif ($svc['service_type'] === 'INTERNAL_FORM') {
                        $link = url('application/select/' . $svc['id']);
                    }
                    $delay = ($index % 5) + 1;
                ?>
                <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 animate-in delay-<?php echo $delay; ?>">
                    <a href="<?php echo $link; ?>" target="<?php echo $target; ?>" class="text-decoration-none">
                        <div class="service-card">
                            <div class="service-img-wrapper">
                                <?php if (!empty($svc['image_url'])): ?>
                                    <img src="<?php echo asset($svc['image_url']); ?>" alt="<?php echo $svc['name']; ?>" style="max-height: 100%; max-width: 100%;" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                    <i class="fas fa-box-open fa-2x text-primary" style="display: none;"></i>
                                <?php else: ?>
                                    <i class="fas fa-box-open fa-2x text-primary"></i>
                                <?php endif; ?>
                            </div>
                            <h6 class="fw-bold text-dark mb-1 small"><?php echo $svc['name']; ?></h6>
                            <span class="text-muted" style="font-size: 0.7rem;"><?php echo $svc['category']; ?></span>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-5 animate-in">
            <i class="fas fa-box-open fa-3x text-muted opacity-25 mb-3"></i>
            <p class="text-muted">No services active.</p>
        </div>
    <?php endif; ?>

    <!-- Charts -->
    <div class="row g-4 animate-in delay-2">
        <div class="col-lg-8">
            <div class="chart-card">
                <h6 class="fw-bold text-dark mb-4">Applications Overview</h6>
                <div id="serviceChart" style="width: 100%; height: 300px;"></div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="chart-card">
                <h6 class="fw-bold text-dark mb-4">Status Distribution</h6>
                <div id="statusChart" style="width: 100%; height: 300px;"></div>
            </div>
        </div>
    </div>

</div>

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
                    echo "['No Data', 1]";
                }
            ?>
        ]);

        var statusOptions = {
            pieHole: 0.6,
            colors: ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#3b82f6'],
            chartArea: {width: '90%', height: '80%'},
            legend: {position: 'bottom'},
            pieSliceText: 'none',
            backgroundColor: 'transparent'
        };

        var statusChart = new google.visualization.PieChart(document.getElementById('statusChart'));
        statusChart.draw(statusData, statusOptions);

        // 2. Service Chart
        var serviceData = google.visualization.arrayToDataTable([
            ['Service', 'Applications', { role: 'style' }],
            <?php
                if (!empty($stats['by_root_service'])) {
                    $colors = ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#3b82f6'];
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
            chartArea: {width: '85%', height: '70%'},
            vAxis: { minValue: 0, format: '0', gridlines: { color: '#f1f5f9' } },
            hAxis: { textStyle: { color: '#64748b' } },
            backgroundColor: 'transparent',
            bar: { groupWidth: '40%' }
        };

        var serviceChart = new google.visualization.ColumnChart(document.getElementById('serviceChart'));
        serviceChart.draw(serviceData, serviceOptions);
    }

    window.addEventListener('resize', drawCharts);
</script>

<?php view('layouts/partner_footer'); ?>
