<?php view('layouts/header', ['title' => 'Super Admin Dashboard']); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark">Dashboard Overview</h2>
        <p class="text-muted">Welcome back, Super Admin!</p>
    </div>
    <div>
        <a href="<?php echo url('white_label/create'); ?>" class="btn btn-primary"><i class="fas fa-plus me-2"></i> Add White Label</a>
    </div>
</div>

<!-- Stats Row -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card purple">
            <div class="icon-box">
                <i class="fas fa-users"></i>
            </div>
            <h3><?php echo $stats['total_partners'] ?? 0; ?></h3>
            <p>Total Partners</p>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card blue">
            <div class="icon-box">
                <i class="fas fa-file-alt"></i>
            </div>
            <h3><?php echo $stats['total_applications'] ?? 0; ?></h3>
            <p>Total Applications</p>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card green">
            <div class="icon-box">
                <i class="fas fa-building"></i>
            </div>
            <h3><?php echo $stats['total_white_labels'] ?? 0; ?></h3>
            <p>White Labels</p>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card orange">
            <div class="icon-box">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <h3><?php echo $stats['pending_applications'] ?? 0; ?></h3>
            <p>Action Required</p>
        </div>
    </div>
</div>

<!-- Charts Row 1 -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 pt-4 ps-4">
                <h5 class="fw-bold mb-0 text-primary">Partner Growth (Last 6 Months)</h5>
            </div>
            <div class="card-body">
                <div id="growthChart" style="width: 100%; height: 300px;"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 pt-4 ps-4">
                <h5 class="fw-bold mb-0 text-primary">Application Status</h5>
            </div>
            <div class="card-body">
                <div id="statusChart" style="width: 100%; height: 300px;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row 2 & Recent Apps -->
<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 pt-4 ps-4">
                <h5 class="fw-bold mb-0 text-primary">Applications by Category</h5>
            </div>
            <div class="card-body">
                <div id="categoryChart" style="width: 100%; height: 300px;"></div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">Recent Applications</h5>
                <a href="<?php echo url('application/index'); ?>" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">ID</th>
                                <th>Partner</th>
                                <th>Service</th>
                                <th>Status</th>
                                <th class="pe-4 text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($stats['recent_applications'])): ?>
                                <?php foreach ($stats['recent_applications'] as $app): ?>
                                    <tr>
                                        <td class="ps-4"><span class="text-muted small">#<?php echo substr($app['id'], -6); ?></span></td>
                                        <td class="fw-bold"><?php echo $app['partner_name']; ?></td>
                                        <td><span class="badge bg-light text-dark border"><?php echo $app['service_name']; ?></span></td>
                                        <td>
                                            <?php
                                                $status_class = 'bg-secondary';
                                                if ($app['status'] == 'approved') $status_class = 'bg-success';
                                                elseif ($app['status'] == 'rejected') $status_class = 'bg-danger';
                                                elseif ($app['status'] == 'submitted') $status_class = 'bg-primary';
                                                elseif ($app['status'] == 'under_verification') $status_class = 'bg-warning text-dark';
                                            ?>
                                            <span class="badge <?php echo $status_class; ?>"><?php echo ucfirst(str_replace('_', ' ', $app['status'])); ?></span>
                                        </td>
                                        <td class="pe-4 text-end">
                                            <a href="<?php echo url('application/view/' . $app['id']); ?>" class="btn btn-sm btn-light"><i class="fas fa-eye"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
                                        <p class="mb-0">No applications found yet.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
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
        // 1. Status Chart (Pie)
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
            pieHole: 0.4,
            colors: ['#6A5ACD', '#20B2AA', '#FF7F50', '#FF6347', '#4682B4'],
            chartArea: {width: '90%', height: '80%'},
            legend: {position: 'bottom'},
            pieSliceText: 'value'
        };

        var statusChart = new google.visualization.PieChart(document.getElementById('statusChart'));
        statusChart.draw(statusData, statusOptions);

        // 2. Category Chart (Column)
        var categoryData = google.visualization.arrayToDataTable([
            ['Category', 'Applications', { role: 'style' }],
            <?php
                if (!empty($stats['by_category'])) {
                    $colors = ['#6A5ACD', '#20B2AA', '#FF7F50', '#FF6347', '#4682B4'];
                    $i = 0;
                    foreach ($stats['by_category'] as $cat => $count) {
                        $color = $colors[$i % count($colors)];
                        echo "['" . $cat . "', " . $count . ", '$color'],";
                        $i++;
                    }
                } else {
                    echo "['No Data', 0, '#ccc']";
                }
            ?>
        ]);

        var categoryOptions = {
            legend: { position: "none" },
            chartArea: {width: '80%', height: '70%'},
            vAxis: { minValue: 0, format: '0' }
        };

        var categoryChart = new google.visualization.ColumnChart(document.getElementById('categoryChart'));
        categoryChart.draw(categoryData, categoryOptions);

        // 3. Growth Chart (Line)
        var growthData = google.visualization.arrayToDataTable([
            ['Month', 'New Partners'],
            <?php
                if (!empty($stats['partner_growth'])) {
                    foreach ($stats['partner_growth'] as $month => $count) {
                        echo "['" . date('M Y', strtotime($month . '-01')) . "', " . $count . "],";
                    }
                } else {
                    echo "['" . date('M Y') . "', 0]";
                }
            ?>
        ]);

        var growthOptions = {
            curveType: 'function',
            legend: { position: 'bottom' },
            colors: ['#6A5ACD'],
            chartArea: {width: '85%', height: '70%'},
            vAxis: { minValue: 0, format: '0' },
            pointSize: 5
        };

        var growthChart = new google.visualization.LineChart(document.getElementById('growthChart'));
        growthChart.draw(growthData, growthOptions);
    }

    // Make charts responsive
    window.addEventListener('resize', drawCharts);
</script>

<?php view('layouts/footer'); ?>
