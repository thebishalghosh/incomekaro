<?php view('layouts/header', ['title' => 'Super Admin Dashboard']); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark">Dashboard Overview</h2>
        <p class="text-muted">Welcome back, <?php echo $_SESSION['user_name'] ?? 'Admin'; ?>!</p>
    </div>
    <div>
        <button class="btn btn-primary"><i class="fas fa-download me-2"></i> Download Report</button>
    </div>
</div>

<!-- Stats Row -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card purple">
            <div class="icon-box">
                <i class="fas fa-building"></i>
            </div>
            <?php
                $db = get_db_connection();
                $wl_count = $db->query("SELECT COUNT(*) FROM white_label_clients WHERE status='active'")->fetchColumn();
            ?>
            <h3><?php echo $wl_count; ?></h3>
            <p>Active White Labels</p>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card blue">
            <div class="icon-box">
                <i class="fas fa-handshake"></i>
            </div>
            <?php
                $partner_count = $db->query("SELECT COUNT(*) FROM partners WHERE status='active'")->fetchColumn();
            ?>
            <h3><?php echo $partner_count; ?></h3>
            <p>Total Partners</p>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card green">
            <div class="icon-box">
                <i class="fas fa-rupee-sign"></i>
            </div>
            <h3>₹4.5L</h3>
            <p>Total Revenue (This Month)</p>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card orange">
            <div class="icon-box">
                <i class="fas fa-file-invoice"></i>
            </div>
            <h3>45</h3>
            <p>Pending Applications</p>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="fw-bold mb-0">Revenue Analytics</h5>
            </div>
            <div class="card-body">
                <div id="revenueChart" style="width: 100%; height: 350px;"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="fw-bold mb-0">Partner Distribution</h5>
            </div>
            <div class="card-body">
                <div id="partnerChart" style="width: 100%; height: 350px;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity & Top Services -->
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="fw-bold mb-0">Recent Applications</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">ID</th>
                                <th>Customer</th>
                                <th>Service</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="ps-4">#APP-001</td>
                                <td>Rahul Kumar</td>
                                <td>Personal Loan</td>
                                <td><span class="badge bg-warning text-dark">Pending</span></td>
                                <td>Oct 24, 2023</td>
                            </tr>
                            <tr>
                                <td class="ps-4">#APP-002</td>
                                <td>Sneha Gupta</td>
                                <td>GST Registration</td>
                                <td><span class="badge bg-success">Approved</span></td>
                                <td>Oct 23, 2023</td>
                            </tr>
                            <tr>
                                <td class="ps-4">#APP-003</td>
                                <td>Amit Singh</td>
                                <td>Credit Card</td>
                                <td><span class="badge bg-danger">Rejected</span></td>
                                <td>Oct 22, 2023</td>
                            </tr>
                            <tr>
                                <td class="ps-4">#APP-004</td>
                                <td>Priya Sharma</td>
                                <td>ITR Filing</td>
                                <td><span class="badge bg-info text-dark">Processing</span></td>
                                <td>Oct 21, 2023</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white border-0 text-center py-3">
                <a href="<?php echo url('application/index'); ?>" class="text-decoration-none fw-bold">View All Applications</a>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="fw-bold mb-0">Top Performing Services</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-4">
                    <div class="flex-shrink-0">
                        <div class="icon-box bg-light text-primary rounded p-3">
                            <i class="fas fa-credit-card fa-lg"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1 fw-bold">Credit Cards</h6>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 75%"></div>
                        </div>
                    </div>
                    <div class="ms-3 fw-bold">75%</div>
                </div>

                <div class="d-flex align-items-center mb-4">
                    <div class="flex-shrink-0">
                        <div class="icon-box bg-light text-success rounded p-3">
                            <i class="fas fa-money-bill-wave fa-lg"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1 fw-bold">Personal Loans</h6>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 60%"></div>
                        </div>
                    </div>
                    <div class="ms-3 fw-bold">60%</div>
                </div>

                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="icon-box bg-light text-warning rounded p-3">
                            <i class="fas fa-file-contract fa-lg"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1 fw-bold">GST Filing</h6>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: 45%"></div>
                        </div>
                    </div>
                    <div class="ms-3 fw-bold">45%</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Google Charts Script -->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawCharts);

    function drawCharts() {
        // Revenue Chart
        var revenueData = google.visualization.arrayToDataTable([
            ['Month', 'Revenue'],
            ['Jan',  10000],
            ['Feb',  15000],
            ['Mar',  20000],
            ['Apr',  25000],
            ['May',  30000],
            ['Jun',  45000]
        ]);

        var revenueOptions = {
            title: 'Monthly Revenue',
            curveType: 'function',
            legend: { position: 'bottom' },
            colors: ['#6A5ACD'],
            vAxis: { format: 'currency' },
            chartArea: { width: '85%', height: '70%' }
        };

        var revenueChart = new google.visualization.LineChart(document.getElementById('revenueChart'));
        revenueChart.draw(revenueData, revenueOptions);

        // Partner Distribution Chart
        var partnerData = google.visualization.arrayToDataTable([
            ['Type', 'Count'],
            ['Platform Partners',     11],
            ['White Label Partners',      2]
        ]);

        var partnerOptions = {
            title: 'Partner Distribution',
            pieHole: 0.4,
            colors: ['#6A5ACD', '#9370DB'],
            legend: { position: 'bottom' },
            chartArea: { width: '90%', height: '80%' }
        };

        var partnerChart = new google.visualization.PieChart(document.getElementById('partnerChart'));
        partnerChart.draw(partnerData, partnerOptions);
    }

    // Resize charts on window resize
    window.onresize = function() {
        drawCharts();
    };
</script>

<?php view('layouts/footer'); ?>
