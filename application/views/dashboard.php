<div class="page-header">
    <h4 class="page-title">Dashboard</h4>
    <ul class="breadcrumbs">
        <li class="nav-home">
            <a href="#">
                <i class="fas fa-home"></i>
            </a>
        </li>
        <li class="separator">
            <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
            <a href="javascript:void(0)">Dashboard</a>
        </li>
    </ul>
</div>

<!-- Key Metrics Row -->
<div class="row">
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-primary card-round">
            <div class="card-body">
                <div class="row">
                    <div class="col-5">
                        <div class="icon-big text-center">
                            <i class="flaticon-users"></i>
                        </div>
                    </div>
                    <div class="col-7 col-stats">
                        <div class="numbers">
                            <p class="card-category">Total Personnel</p>
                            <h4 class="card-title"><?= number_format($person) ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-info card-round">
            <div class="card-body">
                <div class="row">
                    <div class="col-5">
                        <div class="icon-big text-center">
                            <i class="flaticon-calendar"></i>
                        </div>
                    </div>
                    <div class="col-7 col-stats">
                        <div class="numbers">
                            <p class="card-category">Today's Attendance</p>
                            <h4 class="card-title"><?= number_format($today_attendance + $today_biometrics) ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-success card-round">
            <div class="card-body">
                <div class="row">
                    <div class="col-5">
                        <div class="icon-big text-center">
                            <i class="flaticon-analytics"></i>
                        </div>
                    </div>
                    <div class="col-7 col-stats">
                        <div class="numbers">
                            <p class="card-category">Monthly Records</p>
                            <h4 class="card-title"><?= number_format($monthly_attendance + $monthly_biometrics) ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-warning card-round">
            <div class="card-body">
                <div class="row">
                    <div class="col-5">
                        <div class="icon-big text-center">
                            <i class="flaticon-success"></i>
                        </div>
                    </div>
                    <div class="col-7 col-stats">
                        <div class="numbers">
                            <p class="card-category">Attendance Rate</p>
                            <h4 class="card-title"><?= $attendance_rate ?>%</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts and Analytics Row -->
<div class="row">
    <!-- Daily Attendance Trend -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <div class="card-title">7-Day Attendance Trend</div>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="dailyChart" style="width: 100%; height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Attendance Distribution -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Today's Breakdown</div>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="pieChart" style="width: 100%; height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Monthly Trends and Top Performers -->
<div class="row">
    <!-- Monthly Trends -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <div class="card-title">12-Month Attendance Overview</div>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="monthlyChart" style="width: 100%; height: 350px;"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Top Attendees -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Top Attendees This Month</div>
            </div>
            <div class="card-body">
                <?php if (!empty($top_attendees)): ?>
                    <?php foreach ($top_attendees as $index => $attendee): ?>
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar avatar-sm mr-3">
                                <span class="avatar-title rounded-circle bg-primary"><?= $index + 1 ?></span>
                            </div>
                            <div class="flex-1">
                                <h6 class="mb-0"><?= $attendee->firstname . ' ' . $attendee->lastname ?></h6>
                                <small class="text-muted"><?= $attendee->total_days ?> days</small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted text-center">No attendance data available</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Recent Attendance Activity</div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Employee</th>
                                <th>Time In</th>
                                <th>Time Out</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recent_activity)): ?>
                                <?php foreach ($recent_activity as $activity): ?>
                                    <tr>
                                        <td><?= date('M j, Y', strtotime($activity->date)) ?></td>
                                        <td><?= $activity->firstname . ' ' . $activity->lastname ?></td>
                                        <td>
                                            <?php if ($activity->morning_in): ?>
                                                <span class="badge badge-success"><?= date('h:i A', strtotime($activity->morning_in)) ?></span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary">--</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($activity->afternoon_out): ?>
                                                <span class="badge badge-info"><?= date('h:i A', strtotime($activity->afternoon_out)) ?></span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary">--</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($activity->morning_in && $activity->afternoon_out): ?>
                                                <span class="badge badge-success">Complete</span>
                                            <?php elseif ($activity->morning_in): ?>
                                                <span class="badge badge-warning">Partial</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger">Absent</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No recent activity found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Daily Attendance Chart
const dailyCtx = document.getElementById('dailyChart').getContext('2d');
const dailyChart = new Chart(dailyCtx, {
    type: 'line',
    data: {
        labels: [<?php echo "'" . implode("', '", array_column($daily_stats, 'date')) . "'"; ?>],
        datasets: [{
            label: 'Manual Attendance',
            data: [<?php echo implode(', ', array_column($daily_stats, 'attendance')); ?>],
            borderColor: 'rgb(54, 162, 235)',
            backgroundColor: 'rgba(54, 162, 235, 0.1)',
            tension: 0.4
        }, {
            label: 'Biometric Records',
            data: [<?php echo implode(', ', array_column($daily_stats, 'biometrics')); ?>],
            borderColor: 'rgb(255, 99, 132)',
            backgroundColor: 'rgba(255, 99, 132, 0.1)',
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Pie Chart for Today's Distribution
const pieCtx = document.getElementById('pieChart').getContext('2d');
const pieChart = new Chart(pieCtx, {
    type: 'doughnut',
    data: {
        labels: ['Manual Attendance', 'Biometric Records'],
        datasets: [{
            data: [<?= $today_attendance ?>, <?= $today_biometrics ?>],
            backgroundColor: ['#36A2EB', '#FF6384'],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Monthly Trends Chart
const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
const monthlyChart = new Chart(monthlyCtx, {
    type: 'bar',
    data: {
        labels: [<?php echo "'" . implode("', '", array_column($monthly_stats, 'month')) . "'"; ?>],
        datasets: [{
            label: 'Manual Attendance',
            data: [<?php echo implode(', ', array_column($monthly_stats, 'attendance')); ?>],
            backgroundColor: 'rgba(54, 162, 235, 0.8)',
            borderColor: 'rgb(54, 162, 235)',
            borderWidth: 1
        }, {
            label: 'Biometric Records',
            data: [<?php echo implode(', ', array_column($monthly_stats, 'biometrics')); ?>],
            backgroundColor: 'rgba(255, 99, 132, 0.8)',
            borderColor: 'rgb(255, 99, 132)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            x: {
                stacked: true
            },
            y: {
                stacked: true,
                beginAtZero: true
            }
        }
    }
});
</script>