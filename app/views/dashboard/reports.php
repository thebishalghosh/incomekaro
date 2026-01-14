<?php view('layouts/header', ['title' => 'Reports & Analytics']); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark">Reports & Analytics</h2>
        <p class="text-muted">Detailed insights into your business performance.</p>
    </div>
    <div>
        <button class="btn btn-outline-secondary me-2" onclick="window.print()"><i class="fas fa-print me-2"></i> Print</button>
        <button class="btn btn-primary"><i class="fas fa-download me-2"></i> Export CSV</button>
    </div>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <form action="" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-bold small text-uppercase text-muted">Start Date</label>
                <input type="date" class="form-control" name="start_date" value="<?php echo $filters['start_date']; ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold small text-uppercase text-muted">End Date</label>
                <input type="date" class="form-control" name="end_date" value="<?php echo $filters['end_date']; ?>">
            </div>

            <?php if ($_SESSION['role_code'] === 'SUPER_ADMIN'): ?>
            <div class="col-md-3">
                <label class="form-label fw-bold small text-uppercase text-muted">White Label</label>
                <select class="form-select" name="white_label_id">
                    <option value="">All White Labels</option>
                    <?php foreach ($white_labels as $wl): ?>
                        <option value="<?php echo $wl['id']; ?>" <?php echo ($filters['white_label_id'] == $wl['id']) ? 'selected' : ''; ?>>
                            <?php echo $wl['company_name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>

            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter me-2"></i> Apply Filters</button>
            </div>
        </form>
    </div>
</div>

<!-- Financial Overview -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-primary text-white" style="background: linear-gradient(135deg, #4e54c8 0%, #8f94fb 100%);">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-uppercase fw-bold opacity-75 mb-0">Total Revenue</h6>
                    <i class="fas fa-rupee-sign fa-2x opacity-50"></i>
                </div>
                <h2 class="fw-bold mb-0">₹<?php echo number_format($revenue['total_revenue'], 2); ?></h2>
                <small class="opacity-75">From <?php echo $revenue['subscription_count']; ?> subscriptions</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 text-white" style="background: linear-gradient(135deg, #f09819 0%, #edde5d 100%);">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-uppercase fw-bold opacity-75 mb-0">Total Payouts</h6>
                    <i class="fas fa-hand-holding-usd fa-2x opacity-50"></i>
                </div>
                <h2 class="fw-bold mb-0">₹<?php echo number_format($payouts['total_payout'] ?? 0, 2); ?></h2>
                <small class="opacity-75"><?php echo $payouts['count'] ?? 0; ?> withdrawals processed</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-success text-white" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-uppercase fw-bold opacity-75 mb-0">Net Profit</h6>
                    <i class="fas fa-chart-line fa-2x opacity-50"></i>
                </div>
                <h2 class="fw-bold mb-0">₹<?php echo number_format($revenue['total_revenue'] - ($payouts['total_payout'] ?? 0), 2); ?></h2>
                <small class="opacity-75">Revenue - Payouts</small>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="fw-bold mb-0">Application Trends (Daily)</h5>
            </div>
            <div class="card-body">
                <div id="trendChart" style="width: 100%; height: 300px;"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="fw-bold mb-0">Application Status</h5>
            </div>
            <div class="card-body">
                <div id="statusChart" style="width: 100%; height: 300px;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Top Partners Table -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 py-3">
        <h5 class="fw-bold mb-0">Top Performing Partners</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Partner Name</th>
                        <th>Approved Apps</th>
                        <th>Revenue Generated</th>
                        <th class="pe-4 text-end">Performance</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($top_partners)): ?>
                        <?php foreach ($top_partners as $partner): ?>
                            <tr>
                                <td class="ps-4 fw-bold"><?php echo $partner['name']; ?></td>
                                <td><?php echo $partner['app_count']; ?></td>
                                <td>₹<?php echo number_format($partner['revenue'] ?? 0, 2); ?></td>
                                <td class="pe-4 text-end">
                                    <div class="progress" style="height: 6px; width: 100px; display: inline-flex;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo min(($partner['app_count'] * 10), 100); ?>%"></div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">No data available.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Google Charts Script -->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawCharts);

    function drawCharts() {
        // 1. Status Chart
        var statusData = google.visualization.arrayToDataTable([
            ['Status', 'Count'],
            <?php
                if (!empty($app_stats)) {
                    foreach ($app_stats as $status => $count) {
                        echo "['" . str_replace('_', ' ', $status) . "', " . $count . "],";
                    }
                } else {
                    echo "['No Data', 1]";
                }
            ?>
        ]);

        var statusOptions = {
            pieHole: 0.4,
            colors: ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#3b82f6'],
            chartArea: {width: '90%', height: '80%'},
            legend: {position: 'bottom'}
        };

        var statusChart = new google.visualization.PieChart(document.getElementById('statusChart'));
        statusChart.draw(statusData, statusOptions);

        // 2. Trend Chart
        var trendData = google.visualization.arrayToDataTable([
            ['Date', 'Applications'],
            <?php
                if (!empty($daily_trends)) {
                    foreach ($daily_trends as $date => $count) {
                        echo "['" . date('d M', strtotime($date)) . "', " . $count . "],";
                    }
                } else {
                    echo "['" . date('d M') . "', 0]";
                }
            ?>
        ]);

        var trendOptions = {
            legend: { position: "none" },
            chartArea: {width: '85%', height: '70%'},
            hAxis: { textStyle: { color: '#6b7280' } },
            vAxis: { minValue: 0, gridlines: { color: '#f3f4f6' } },
            colors: ['#6366f1'],
            areaOpacity: 0.1
        };

        var trendChart = new google.visualization.AreaChart(document.getElementById('trendChart'));
        trendChart.draw(trendData, trendOptions);
    }

    window.addEventListener('resize', drawCharts);
</script>

<?php view('layouts/footer'); ?>
