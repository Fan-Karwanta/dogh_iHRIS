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
    
    <!-- Edits vs Missing Logs Comparison -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <div class="card-head-row">
                    <div class="card-title">
                        <i class="fas fa-chart-bar mr-2"></i>Edits vs Missing Logs
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Filter Section -->
                <div class="mb-3">
                    <div class="row">
                        <div class="col-12 mb-2">
                            <label class="form-label mb-1" style="font-size: 12px; font-weight: 600; color: #36A2EB;">
                                <i class="fas fa-edit"></i> Edits Period
                            </label>
                            <select id="editsMonthFilter" class="form-control form-control-sm">
                                <?php 
                                // Generate last 12 months
                                for ($i = 0; $i < 12; $i++) {
                                    $month_date = date('Y-m', strtotime("-$i months"));
                                    $month_label = date('F Y', strtotime("-$i months"));
                                    $selected = ($i == 0) ? 'selected' : '';
                                    echo "<option value='$month_date' $selected>$month_label</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label mb-1" style="font-size: 12px; font-weight: 600; color: #FF6384;">
                                <i class="fas fa-calendar-times"></i> DTR Records Period
                            </label>
                            <select id="dtrMonthFilter" class="form-control form-control-sm">
                                <?php 
                                // Generate last 12 months
                                for ($i = 0; $i < 12; $i++) {
                                    $month_date = date('Y-m', strtotime("-$i months"));
                                    $month_label = date('F Y', strtotime("-$i months"));
                                    $selected = ($i == 1) ? 'selected' : ''; // Default to previous month
                                    echo "<option value='$month_date' $selected>$month_label</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Chart Container -->
                <div class="chart-container" style="position: relative; height: 220px;">
                    <canvas id="breakdownChart"></canvas>
                </div>
                
                <!-- Summary Stats -->
                <div class="row mt-3 pt-3" style="border-top: 1px solid #eee;">
                    <div class="col-6 text-center">
                        <div style="font-size: 11px; color: #888; margin-bottom: 4px;">Total Edits</div>
                        <div style="font-size: 20px; font-weight: bold; color: #36A2EB;" id="totalEditsCount"><?= $chart_data['total_edits'] ?></div>
                    </div>
                    <div class="col-6 text-center">
                        <div style="font-size: 11px; color: #888; margin-bottom: 4px;">Missing Logs</div>
                        <div style="font-size: 20px; font-weight: bold; color: #FF6384;" id="missingLogsCount"><?= $chart_data['missing_logs'] ?></div>
                    </div>
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

// Clustered Column Chart for Edits vs Missing Logs
const breakdownCtx = document.getElementById('breakdownChart').getContext('2d');
let breakdownChart = new Chart(breakdownCtx, {
    type: 'bar',
    data: {
        labels: ['Comparison'],
        datasets: [
            {
                label: 'Total Edits',
                data: [<?= $chart_data['total_edits'] ?>],
                backgroundColor: 'rgba(54, 162, 235, 0.85)',
                borderColor: 'rgb(54, 162, 235)',
                borderWidth: 2,
                borderRadius: 6,
                barThickness: 40
            },
            {
                label: 'Missing Logs',
                data: [<?= $chart_data['missing_logs'] ?>],
                backgroundColor: 'rgba(255, 99, 132, 0.85)',
                borderColor: 'rgb(255, 99, 132)',
                borderWidth: 2,
                borderRadius: 6,
                barThickness: 40
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 10,
                    font: {
                        size: 11
                    }
                },
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        },
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                titleFont: {
                    size: 13
                },
                bodyFont: {
                    size: 12
                },
                callbacks: {
                    title: function(context) {
                        return context[0].dataset.label;
                    },
                    label: function(context) {
                        return 'Count: ' + context.parsed.y;
                    }
                }
            }
        }
    }
});

// Function to update chart data
function updateChartData() {
    const editsMonth = document.getElementById('editsMonthFilter').value;
    const dtrMonth = document.getElementById('dtrMonthFilter').value;
    
    // Fetch new data via AJAX
    fetch('<?= site_url('biometrics/get_dashboard_chart_data') ?>?edits_month=' + editsMonth + '&dtr_month=' + dtrMonth)
        .then(response => response.json())
        .then(data => {
            // Update chart data
            breakdownChart.data.datasets[0].data = [data.total_edits];
            breakdownChart.data.datasets[1].data = [data.missing_logs];
            breakdownChart.update('active');
            
            // Update summary counts
            document.getElementById('totalEditsCount').textContent = data.total_edits;
            document.getElementById('missingLogsCount').textContent = data.missing_logs;
        })
        .catch(error => {
            console.error('Error fetching chart data:', error);
        });
}

// Month filter change handlers
document.getElementById('editsMonthFilter').addEventListener('change', updateChartData);
document.getElementById('dtrMonthFilter').addEventListener('change', updateChartData);

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