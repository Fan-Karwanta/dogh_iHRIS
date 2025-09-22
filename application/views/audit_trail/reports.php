<div class="page-header">
    <h4 class="page-title"><?= $title ?></h4>
    <ul class="breadcrumbs">
        <li class="nav-home">
            <a href="<?= site_url('admin/audit_trail') ?>">
                <i class="fas fa-history"></i>
            </a>
        </li>
        <li class="separator">
            <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
            <a href="<?= site_url('admin/audit_trail') ?>">Audit Trail</a>
        </li>
        <li class="separator">
            <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
            <a href="javascript:void(0)">Reports</a>
        </li>
    </ul>
    <div class="ml-md-auto py-2 py-md-0">
        <a href="<?= site_url('audit_report/generate_pdf?date_from=' . $date_from . '&date_to=' . $date_to) ?>" class="btn btn-danger btn-border btn-round btn-sm" target="_blank">
            <span class="btn-label">
                <i class="fas fa-file-pdf"></i>
            </span>
            Generate PDF Report
        </a>
    </div>
</div>

<!-- Date Range Filter -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" class="row align-items-end">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Date From</label>
                            <input type="date" class="form-control" name="date_from" value="<?= $date_from ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Date To</label>
                            <input type="date" class="form-control" name="date_to" value="<?= $date_to ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Generate Report
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Summary Statistics -->
<div class="row">
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-primary bubble-shadow-small">
                            <i class="fas fa-edit"></i>
                        </div>
                    </div>
                    <div class="col col-stats ml-3 ml-sm-0">
                        <div class="numbers">
                            <p class="card-category">Total Edits</p>
                            <h4 class="card-title">
                                <?php 
                                $total = 0;
                                foreach($statistics['action_stats'] as $stat) {
                                    $total += $stat->count;
                                }
                                echo number_format($total);
                                ?>
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-info bubble-shadow-small">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    <div class="col col-stats ml-3 ml-sm-0">
                        <div class="numbers">
                            <p class="card-category">Personnel Affected</p>
                            <h4 class="card-title"><?= count($statistics['personnel_stats']) ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-success bubble-shadow-small">
                            <i class="fas fa-user-shield"></i>
                        </div>
                    </div>
                    <div class="col col-stats ml-3 ml-sm-0">
                        <div class="numbers">
                            <p class="card-category">Active Admins</p>
                            <h4 class="card-title"><?= count($statistics['admin_stats']) ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-warning bubble-shadow-small">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </div>
                    <div class="col col-stats ml-3 ml-sm-0">
                        <div class="numbers">
                            <p class="card-category">Active Days</p>
                            <h4 class="card-title"><?= count($statistics['daily_stats']) ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Daily Edit Activity</div>
            </div>
            <div class="card-body">
                <canvas id="dailyChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Action Type Distribution</div>
            </div>
            <div class="card-body" style="height: 455px; width: 455px;">
                <canvas id="actionChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Analytics -->
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Most Edited Fields</div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Field Name</th>
                                <th>Edit Count</th>
                                <th>Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if (!empty($analytics['field_stats'])) {
                                $total_field_edits = array_sum(array_column($analytics['field_stats'], 'count'));
                                foreach($analytics['field_stats'] as $field): 
                                    $percentage = $total_field_edits > 0 ? ($field->count / $total_field_edits) * 100 : 0;
                            ?>
                            <tr>
                                <td><?= ucfirst(str_replace('_', ' ', $field->field_name)) ?></td>
                                <td><span class="badge badge-primary"><?= $field->count ?></span></td>
                                <td>
                                    <div class="progress progress-sm">
                                        <div class="progress-bar bg-primary" style="width: <?= $percentage ?>%"></div>
                                    </div>
                                    <small><?= number_format($percentage, 1) ?>%</small>
                                </td>
                            </tr>
                            <?php 
                                endforeach; 
                            } else {
                            ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted">No field edits found</td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Personnel with Most Edit Requests</div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Personnel</th>
                                <th>Requests</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if (!empty($analytics['request_stats'])) {
                                foreach($analytics['request_stats'] as $request): 
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($request->personnel_name) ?></td>
                                <td><span class="badge badge-warning"><?= $request->request_count ?></span></td>
                                <td>
                                    <a href="<?= site_url('admin/audit_trail/personnel_by_bio_id/' . $request->bio_id) ?>" 
                                       class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php 
                                endforeach; 
                            } else {
                            ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted">No personnel requests found</td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Common Reasons -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Most Common Edit Reasons</div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Reason</th>
                                <th>Frequency</th>
                                <th>Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if (!empty($analytics['reason_stats'])) {
                                $total_reasons = array_sum(array_column($analytics['reason_stats'], 'count'));
                                foreach($analytics['reason_stats'] as $reason): 
                                    $percentage = $total_reasons > 0 ? ($reason->count / $total_reasons) * 100 : 0;
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($reason->reason) ?></td>
                                <td><span class="badge badge-info"><?= $reason->count ?></span></td>
                                <td>
                                    <div class="progress progress-sm">
                                        <div class="progress-bar bg-info" style="width: <?= $percentage ?>%"></div>
                                    </div>
                                    <small><?= number_format($percentage, 1) ?>%</small>
                                </td>
                            </tr>
                            <?php 
                                endforeach; 
                            } else {
                            ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted">No edit reasons found</td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hourly Distribution -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Hourly Edit Distribution</div>
            </div>
            <div class="card-body">
                <canvas id="hourlyChart" width="400" height="100"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Daily Chart
    var dailyCtx = document.getElementById('dailyChart').getContext('2d');
    var dailyChart = new Chart(dailyCtx, {
        type: 'line',
        data: {
            labels: [<?php 
                if (!empty($statistics['daily_stats'])) {
                    echo '"' . implode('","', array_map(function($d) { return date('M j', strtotime($d->date)); }, $statistics['daily_stats'])) . '"';
                } else {
                    echo '"No Data"';
                }
            ?>],
            datasets: [{
                label: 'Daily Edits',
                data: [<?php 
                    if (!empty($statistics['daily_stats'])) {
                        echo implode(',', array_column($statistics['daily_stats'], 'count'));
                    } else {
                        echo '0';
                    }
                ?>],
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Action Distribution Chart
    var actionCtx = document.getElementById('actionChart').getContext('2d');
    var actionChart = new Chart(actionCtx, {
        type: 'doughnut',
        data: {
            labels: [<?php 
                if (!empty($statistics['action_stats'])) {
                    echo '"' . implode('","', array_column($statistics['action_stats'], 'action_type')) . '"';
                } else {
                    echo '"No Data"';
                }
            ?>],
            datasets: [{
                data: [<?php 
                    if (!empty($statistics['action_stats'])) {
                        echo implode(',', array_column($statistics['action_stats'], 'count'));
                    } else {
                        echo '0';
                    }
                ?>],
                backgroundColor: [
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Hourly Distribution Chart
    var hourlyCtx = document.getElementById('hourlyChart').getContext('2d');
    
    // Prepare hourly data
    var hourlyData = new Array(24).fill(0);
    <?php if (!empty($analytics['hourly_stats'])) {
        foreach($analytics['hourly_stats'] as $hour): ?>
        hourlyData[<?= $hour->hour ?>] = <?= $hour->count ?>;
    <?php endforeach; } ?>
    
    var hourlyChart = new Chart(hourlyCtx, {
        type: 'bar',
        data: {
            labels: ['00:00', '01:00', '02:00', '03:00', '04:00', '05:00', '06:00', '07:00', '08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00', '23:00'],
            datasets: [{
                label: 'Edits by Hour',
                data: hourlyData,
                backgroundColor: 'rgba(54, 162, 235, 0.8)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>
