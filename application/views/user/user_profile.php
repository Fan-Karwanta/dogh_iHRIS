<?php
$user = $this->ion_auth->user()->row();
$gro = $this->ion_auth->get_users_groups()->row();
?>
<style>
.stat-card {
    transition: transform 0.2s;
}
.stat-card:hover {
    transform: translateY(-5px);
}
.performance-badge {
    font-size: 2rem;
    font-weight: bold;
}
.trend-up {
    color: #1abc9c;
}
.trend-down {
    color: #e74c3c;
}
.chart-container {
    position: relative;
    height: 300px;
}

/* Simple print styles - hide screen elements only */
@media print {
    .no-print {
        display: none !important;
    }
    
    @page {
        size: A4 portrait;
        margin: 1cm;
    }
    
    body {
        margin: 0;
        padding: 0;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
}
</style>

<div class="page-header">
    <h4 class="page-title"><?= $title ?></h4>
    <div class="ml-auto">
        <button onclick="printDiv('printThis')" class="btn btn-primary btn-sm mr-2 no-print" style="padding: 8px 20px; font-weight: 500;">
            <i class="fas fa-print"></i> Print Analytics Report
        </button>
        <form method="GET" class="form-inline d-inline">
            <select name="month" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                <?php for ($m = 1; $m <= 12; $m++): ?>
                    <option value="<?= str_pad($m, 2, '0', STR_PAD_LEFT) ?>" <?= $selected_month == str_pad($m, 2, '0', STR_PAD_LEFT) ? 'selected' : '' ?>>
                        <?= date('F', mktime(0, 0, 0, $m, 1)) ?>
                    </option>
                <?php endfor; ?>
            </select>
            <select name="year" class="form-control form-control-sm" onchange="this.form.submit()">
                <?php for ($y = date('Y'); $y >= date('Y') - 3; $y--): ?>
                    <option value="<?= $y ?>" <?= $selected_year == $y ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
        </form>
    </div>
</div>

<!-- Hidden Print Container (will be shown via printDiv function) -->
<div id="printThis" style="display: none;">
    <?php if ($monthly_stats): ?>
    <div style="width: 100%; font-family: 'Times New Roman', Arial, sans-serif; padding: 20px; box-sizing: border-box;">
        <!-- Header -->
        <div style="text-align: center; margin-bottom: 12px; padding-bottom: 8px; border-bottom: 3px solid #000;">
            <h2 style="font-size: 18px; margin: 0 0 5px 0; color: #000; font-weight: bold;">PERSONNEL DTR ANALYTICS REPORT</h2>
            <p style="font-size: 10px; margin: 0; color: #000;"><strong>Generated:</strong> <?= date('F d, Y g:i A') ?> | <strong>Period:</strong> <?= date('F Y', mktime(0, 0, 0, $selected_month, 1, $selected_year)) ?></p>
        </div>
        
        <!-- Profile Information -->
        <table style="width: 100%; margin-bottom: 10px; border: 2px solid #000; border-collapse: collapse;">
            <tr>
                <td style="padding: 8px; border-right: 1px solid #000; width: 50%; vertical-align: top;">
                    <div style="margin-bottom: 4px; font-size: 11px;"><strong>Name:</strong> <?= $profile_data->first_name . ' ' . $profile_data->last_name ?></div>
                    <div style="margin-bottom: 4px; font-size: 11px;"><strong>Email:</strong> <?= $profile_data->email ?></div>
                    <?php if ($profile_data->position): ?>
                    <div style="margin-bottom: 4px; font-size: 11px;"><strong>Position:</strong> <?= $profile_data->position ?></div>
                    <?php endif; ?>
                </td>
                <td style="padding: 8px; width: 50%; vertical-align: top;">
                    <?php if ($profile_data->department_name): ?>
                    <div style="margin-bottom: 4px; font-size: 11px;"><strong>Department:</strong> <?= $profile_data->department_name ?></div>
                    <?php endif; ?>
                    <?php if ($profile_data->employment_type): ?>
                    <div style="margin-bottom: 4px; font-size: 11px;"><strong>Employment Type:</strong> <?= $profile_data->employment_type ?></div>
                    <?php endif; ?>
                    <?php if ($performance): ?>
                    <div style="margin-bottom: 4px; font-size: 11px;"><strong>Performance:</strong> <?= $performance['grade'] ?> (<?= $performance['attendance_rate'] ?>%)</div>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
        
        <!-- Key Attendance Metrics -->
        <div style="font-size: 12px; font-weight: bold; margin: 10px 0 6px 0; padding: 4px 8px; background-color: #e0e0e0; color: #000; border-left: 4px solid #000;">KEY ATTENDANCE METRICS - <?= date('F Y', mktime(0, 0, 0, $selected_month, 1, $selected_year)) ?></div>
        <table style="width: 100%; border-collapse: collapse; margin: 8px 0;">
            <tr>
                <td style="border: 2px solid #000; padding: 8px; text-align: center; width: 25%;">
                    <strong style="font-size: 16px; display: block; margin-bottom: 3px; color: #000;"><?= $monthly_stats['present_days'] ?></strong>
                    <small style="font-size: 9px; color: #000;">Present Days</small>
                </td>
                <td style="border: 2px solid #000; padding: 8px; text-align: center; width: 25%;">
                    <strong style="font-size: 16px; display: block; margin-bottom: 3px; color: #000;"><?= $monthly_stats['absent_days'] ?></strong>
                    <small style="font-size: 9px; color: #000;">Absent Days</small>
                </td>
                <td style="border: 2px solid #000; padding: 8px; text-align: center; width: 25%;">
                    <strong style="font-size: 16px; display: block; margin-bottom: 3px; color: #000;"><?= $monthly_stats['late_days'] ?></strong>
                    <small style="font-size: 9px; color: #000;">Late Arrivals</small>
                </td>
                <td style="border: 2px solid #000; padding: 8px; text-align: center; width: 25%;">
                    <strong style="font-size: 16px; display: block; margin-bottom: 3px; color: #000;"><?= $monthly_stats['complete_days'] ?></strong>
                    <small style="font-size: 9px; color: #000;">Complete DTR</small>
                </td>
            </tr>
        </table>
        
        <!-- Time & Hours Summary -->
        <div style="font-size: 12px; font-weight: bold; margin: 10px 0 6px 0; padding: 4px 8px; background-color: #e0e0e0; color: #000; border-left: 4px solid #000;">TIME & HOURS SUMMARY</div>
        <table style="width: 100%; border-collapse: collapse; margin: 8px 0;">
            <tr>
                <td style="border: 1px solid #000; padding: 6px; text-align: center; width: 25%;">
                    <strong style="font-size: 13px; display: block; margin-bottom: 2px; color: #000;"><?= number_format($monthly_stats['total_hours_worked'], 1) ?></strong>
                    <small style="font-size: 8px; color: #000;">Total Hours</small>
                </td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center; width: 25%;">
                    <strong style="font-size: 13px; display: block; margin-bottom: 2px; color: #000;"><?= $monthly_stats['mode_arrival_time'] ? date('g:i A', strtotime($monthly_stats['mode_arrival_time'])) : 'N/A' ?></strong>
                    <small style="font-size: 8px; color: #000;">Mode Arrival</small>
                </td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center; width: 25%;">
                    <strong style="font-size: 13px; display: block; margin-bottom: 2px; color: #000;"><?= $monthly_stats['mode_departure_time'] ? date('g:i A', strtotime($monthly_stats['mode_departure_time'])) : 'N/A' ?></strong>
                    <small style="font-size: 8px; color: #000;">Mode Departure</small>
                </td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center; width: 25%;">
                    <strong style="font-size: 13px; display: block; margin-bottom: 2px; color: #000;"><?= $monthly_stats['total_days'] ?></strong>
                    <small style="font-size: 8px; color: #000;">Working Days</small>
                </td>
            </tr>
        </table>
        
        <!-- Attendance Trend Summary (Text-based instead of chart) -->
        <?php if ($attendance_trends && count($attendance_trends) > 0): ?>
        <div style="font-size: 12px; font-weight: bold; margin: 10px 0 6px 0; padding: 4px 8px; background-color: #e0e0e0; color: #000; border-left: 4px solid #000;">6-MONTH ATTENDANCE TREND</div>
        <table style="width: 100%; border-collapse: collapse; margin: 8px 0; font-size: 9px;">
            <tr style="background-color: #f0f0f0;">
                <th style="border: 1px solid #000; padding: 4px; text-align: center;">Month</th>
                <th style="border: 1px solid #000; padding: 4px; text-align: center;">Present</th>
                <th style="border: 1px solid #000; padding: 4px; text-align: center;">Early Departures</th>
                <th style="border: 1px solid #000; padding: 4px; text-align: center;">Absent</th>
            </tr>
            <?php foreach (array_slice($attendance_trends, -6) as $trend): ?>
            <tr>
                <td style="border: 1px solid #000; padding: 4px; text-align: center;"><?= $trend['month_short'] ?></td>
                <td style="border: 1px solid #000; padding: 4px; text-align: center;"><?= $trend['present_days'] ?></td>
                <td style="border: 1px solid #000; padding: 4px; text-align: center;"><?= $trend['early_departures'] ?></td>
                <td style="border: 1px solid #000; padding: 4px; text-align: center;"><?= $trend['absent_days'] ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
        
        <!-- Footer -->
        <div style="margin-top: 12px; padding-top: 6px; border-top: 2px solid #000; text-align: center; font-size: 8px; color: #000;">
            <p style="margin: 2px 0;"><strong>Department of Health - Daily Time Record System</strong></p>
            <p style="margin: 2px 0;">This is a system-generated report. For inquiries, contact your HR department.</p>
            <p style="margin: 2px 0;">Printed: <?= date('F d, Y g:i A') ?></p>
        </div>
    </div>
    <?php endif; ?>
</div>

<div class="row">
    <!-- Profile Card -->
    <div class="col-md-3">
        <div class="card card-profile">
            <div class="card-header" style="background-image: url('<?= site_url() ?>/assets/img/blogpost.jpg')">
                <div class="profile-picture">
                    <div class="avatar avatar-xl">
                        <?php if (empty($user->avatar)) : ?>
                            <img class="avatar-img rounded-circle" alt="preview" src="<?= site_url() ?>assets/img/person.png" />
                        <?php else : ?>
                            <img class="avatar-img rounded-circle" alt="preview" src="<?= preg_match('/data:image/i', $user->avatar) ? $user->avatar : site_url() . 'assets/uploads/avatar/' . $user->avatar ?>" />
                        <?php endif ?>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="user-profile text-center">
                    <div class="name"><?= $user->first_name . ' ' . $user->last_name ?></div>
                    <div class="job"><?= $gro->name ?></div>
                    <div class="desc"><?= $user->email ?></div>
                    <?php if ($profile_data && $profile_data->position): ?>
                        <div class="mt-2"><small class="text-muted"><i class="fas fa-briefcase"></i> <?= $profile_data->position ?></small></div>
                    <?php endif; ?>
                    <?php if ($profile_data && $profile_data->department_name): ?>
                        <div><small class="text-muted"><i class="fas fa-building"></i> <?= $profile_data->department_name ?></small></div>
                    <?php endif; ?>
                    <?php if ($profile_data && $profile_data->employment_type): ?>
                        <div><small class="text-muted"><i class="fas fa-id-card"></i> <?= $profile_data->employment_type ?></small></div>
                    <?php endif; ?>
                </div>
                <hr>
                <div class="text-center">
                    <button class="btn btn-primary btn-sm btn-block" data-toggle="modal" data-target="#editProfileModal">
                        <i class="fas fa-edit"></i> Edit Profile
                    </button>
                </div>
            </div>
        </div>

        <?php if ($performance): ?>
        <div class="card">
            <div class="card-body text-center">
                <h5 class="card-title">Performance Grade</h5>
                <div class="performance-badge" style="color: <?= $performance['attendance_rate'] >= 90 ? '#1abc9c' : ($performance['attendance_rate'] >= 80 ? '#f39c12' : '#e74c3c') ?>">
                    <?= $performance['grade'] ?>
                </div>
                <p class="text-muted"><?= $performance['status'] ?></p>
                <div class="progress" style="height: 10px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: <?= $performance['attendance_rate'] ?>%" aria-valuenow="<?= $performance['attendance_rate'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <small class="text-muted"><?= $performance['attendance_rate'] ?>% Attendance Rate</small>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Main Content -->
    <div class="col-md-9">
        <?php if ($monthly_stats): ?>
        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-md-3">
                <a href="<?= site_url('auth/user_attendance_history/' . $profile_data->id . '/present_days?month=' . $selected_month . '&year=' . $selected_year) ?>" style="text-decoration: none; color: inherit;">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="text-muted mb-1">Present Days</h6>
                                    <h3 class="mb-0"><?= $monthly_stats['present_days'] ?></h3>
                                    <small class="text-muted">of <?= $monthly_stats['total_days'] ?> days</small>
                                </div>
                                <div class="text-success">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="<?= site_url('auth/user_attendance_history/' . $profile_data->id . '/absent_days?month=' . $selected_month . '&year=' . $selected_year) ?>" style="text-decoration: none; color: inherit;">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="text-muted mb-1">Absent Days</h6>
                                    <h3 class="mb-0"><?= $monthly_stats['absent_days'] ?></h3>
                                    <small class="text-muted">days missed</small>
                                </div>
                                <div class="text-danger">
                                    <i class="fas fa-times-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="<?= site_url('auth/user_attendance_history/' . $profile_data->id . '/late_arrivals?month=' . $selected_month . '&year=' . $selected_year) ?>" style="text-decoration: none; color: inherit;">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="text-muted mb-1">Late Arrivals</h6>
                                    <h3 class="mb-0"><?= $monthly_stats['late_days'] ?></h3>
                                    <small class="text-muted">times late</small>
                                </div>
                                <div class="text-warning">
                                    <i class="fas fa-clock fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="<?= site_url('auth/user_attendance_history/' . $profile_data->id . '/early_departures?month=' . $selected_month . '&year=' . $selected_year) ?>" style="text-decoration: none; color: inherit;">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="text-muted mb-1">Early Departures</h6>
                                    <h3 class="mb-0"><?= $monthly_stats['early_departures'] ?></h3>
                                    <small class="text-muted">times early</small>
                                </div>
                                <div class="text-warning">
                                    <i class="fas fa-door-open fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Additional Stats -->
        <div class="row">
            <div class="col-md-3">
                <a href="<?= site_url('auth/user_attendance_history/' . $profile_data->id . '/complete_dtr?month=' . $selected_month . '&year=' . $selected_year) ?>" style="text-decoration: none; color: inherit;">
                    <div class="card stat-card">
                        <div class="card-body text-center">
                            <h6 class="text-muted">Complete DTR</h6>
                            <h2 class="text-info"><?= $monthly_stats['complete_days'] ?></h2>
                            <small class="text-muted">full records</small>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="<?= site_url('auth/user_analytics_justification/' . $profile_data->id . '/total_hours?month=' . $selected_month . '&year=' . $selected_year) ?>" style="text-decoration: none; color: inherit;">
                    <div class="card stat-card">
                        <div class="card-body text-center">
                            <h6 class="text-muted">Total Hours Worked</h6>
                            <h2 class="text-primary"><?= number_format($monthly_stats['total_hours_worked'], 1) ?></h2>
                            <small class="text-muted">hours this month</small>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="<?= site_url('auth/user_analytics_justification/' . $profile_data->id . '/mode_arrival?month=' . $selected_month . '&year=' . $selected_year) ?>" style="text-decoration: none; color: inherit;">
                    <div class="card stat-card">
                        <div class="card-body text-center">
                            <h6 class="text-muted">Mode Arrival</h6>
                            <h2 class="text-info"><?= $monthly_stats['mode_arrival_time'] ? date('g:i A', strtotime($monthly_stats['mode_arrival_time'])) : 'N/A' ?></h2>
                            <small class="text-muted">most common check-in</small>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="<?= site_url('auth/user_analytics_justification/' . $profile_data->id . '/mode_departure?month=' . $selected_month . '&year=' . $selected_year) ?>" style="text-decoration: none; color: inherit;">
                    <div class="card stat-card">
                        <div class="card-body text-center">
                            <h6 class="text-muted">Mode Departure</h6>
                            <h2 class="text-info"><?= $monthly_stats['mode_departure_time'] ? date('g:i A', strtotime($monthly_stats['mode_departure_time'])) : 'N/A' ?></h2>
                            <small class="text-muted">most common check-out</small>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <?php endif; ?>

        <!-- Tabs for different sections -->
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#trends" role="tab">Trends</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#recent" role="tab">Recent Activity</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#comparison" role="tab">Department Comparison</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#audit" role="tab">Audit Trail</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <!-- Trends Tab -->
                    <div class="tab-pane fade show active" id="trends" role="tabpanel">
                        <div class="print-only print-section-title">Attendance Trends (Last 6 Months)</div>
                        <?php if ($attendance_trends): ?>
                        <h5 class="no-print">Attendance Trends (Last 6 Months)</h5>
                        <div class="chart-container">
                            <canvas id="trendsChart"></canvas>
                        </div>
                        <?php else: ?>
                        <p class="text-center text-muted">No attendance data available</p>
                        <?php endif; ?>
                    </div>

                    <!-- Recent Activity Tab -->
                    <div class="tab-pane fade" id="recent" role="tabpanel">
                        <div class="print-only print-section-title">Recent Attendance Records</div>
                        <?php if ($recent_attendance): ?>
                        <h5 class="no-print">Recent Attendance Records</h5>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>AM In</th>
                                        <th>AM Out</th>
                                        <th>PM In</th>
                                        <th>PM Out</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_attendance as $record): ?>
                                    <tr>
                                        <td><?= date('M d, Y', strtotime($record->date)) ?></td>
                                        <td><?= $record->am_in ? date('g:i A', strtotime($record->am_in)) : '<span class="text-muted">-</span>' ?></td>
                                        <td><?= $record->am_out ? date('g:i A', strtotime($record->am_out)) : '<span class="text-muted">-</span>' ?></td>
                                        <td><?= $record->pm_in ? date('g:i A', strtotime($record->pm_in)) : '<span class="text-muted">-</span>' ?></td>
                                        <td><?= $record->pm_out ? date('g:i A', strtotime($record->pm_out)) : '<span class="text-muted">-</span>' ?></td>
                                        <td>
                                            <?php if ($record->am_in && $record->am_out && $record->pm_in && $record->pm_out): ?>
                                                <span class="badge badge-success">Complete</span>
                                            <?php else: ?>
                                                <span class="badge badge-warning">Incomplete</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <p class="text-center text-muted">No recent attendance records</p>
                        <?php endif; ?>
                    </div>

                    <!-- Department Comparison Tab -->
                    <div class="tab-pane fade" id="comparison" role="tabpanel">
                        <div class="print-only print-section-title">Department Performance Comparison</div>
                        <?php if ($dept_comparison): ?>
                        <h5 class="no-print">Department Performance Comparison</h5>
                        <div class="row mt-4">
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h6 class="text-muted">Your Attendance Rate</h6>
                                        <h2 class="text-primary"><?= $performance ? $performance['attendance_rate'] : 0 ?>%</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h6 class="text-muted">Department Average</h6>
                                        <h2 class="text-info"><?= $dept_comparison['avg_attendance_rate'] ?>%</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h6 class="text-muted">Difference</h6>
                                        <h2 class="<?= ($performance['attendance_rate'] - $dept_comparison['avg_attendance_rate']) >= 0 ? 'text-success' : 'text-danger' ?>">
                                            <?= ($performance['attendance_rate'] - $dept_comparison['avg_attendance_rate']) >= 0 ? '+' : '' ?><?= number_format($performance['attendance_rate'] - $dept_comparison['avg_attendance_rate'], 1) ?>%
                                        </h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <p class="text-muted">Department: <?= $profile_data->department_name ?> (<?= $dept_comparison['total_personnel'] ?> personnel)</p>
                            <p class="text-muted">Avg Late Days: <?= $dept_comparison['avg_late_days'] ?> | Avg Complete Days: <?= $dept_comparison['avg_complete_days'] ?></p>
                        </div>
                        <?php else: ?>
                        <p class="text-center text-muted">No department comparison available</p>
                        <?php endif; ?>
                    </div>

                    <!-- Audit Trail Tab -->
                    <div class="tab-pane fade" id="audit" role="tabpanel">
                        <div class="print-only print-section-title">Audit Trail - Recent Changes to DTR</div>
                        <?php if ($audit_trail && count($audit_trail) > 0): ?>
                        <h5 class="no-print">Recent Changes to Your DTR</h5>
                        <div class="timeline timeline-simple">
                            <?php foreach ($audit_trail as $audit): ?>
                            <div class="timeline-item">
                                <div class="timeline-badge <?= $audit->action_type == 'UPDATE' ? 'warning' : 'info' ?>">
                                    <i class="fas fa-<?= $audit->action_type == 'UPDATE' ? 'edit' : 'plus' ?>"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6 class="mb-1"><?= $audit->action_type ?> - <?= $audit->table_name ?></h6>
                                    <p class="mb-1">
                                        <small class="text-muted">
                                            <?= date('M d, Y g:i A', strtotime($audit->created_at)) ?>
                                            <?php if ($audit->first_name): ?>
                                                by <?= $audit->first_name . ' ' . $audit->last_name ?>
                                            <?php endif; ?>
                                        </small>
                                    </p>
                                    <?php if ($audit->reason): ?>
                                    <p class="mb-0"><small><strong>Reason:</strong> <?= $audit->reason ?></small></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <p class="text-center text-muted">No audit trail records</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Profile</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form method="POST" action="<?= site_url(uri_string()) ?>" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="size" value="1000000">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="text-center mb-3">
                                <?php if (empty($user->avatar)) : ?>
                                    <img class="img img-fluid" width="200" alt="preview" src="<?= site_url() ?>assets/img/person.png" />
                                <?php else : ?>
                                    <img class="img img-fluid" width="200" alt="preview" src="<?= preg_match('/data:image/i', $user->avatar) ? $user->avatar : site_url() . 'assets/uploads/avatar/' . $user->avatar ?>" />
                                <?php endif ?>
                            </div>
                            <div class="form-group">
                                <label>Upload New Avatar</label>
                                <input type="file" class="form-control" name="avatar" accept="image/*">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>First Name</label>
                                <input type="text" class="form-control" name="first_name" value="<?= $user->first_name ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Last Name</label>
                                <input type="text" class="form-control" name="last_name" value="<?= $user->last_name ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Password (leave blank to keep current)</label>
                                <input type="password" class="form-control" name="password" value="">
                            </div>
                            <div class="form-group">
                                <label>Confirm Password</label>
                                <input type="password" class="form-control" name="password_confirm" value="">
                            </div>
                            <?php if ($this->ion_auth->is_admin()) : ?>
                                <div class="form-group">
                                    <label>User Role</label>
                                    <?php foreach ($groups as $group) : ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="groups[]" value="<?php echo $group['id']; ?>" <?php echo (in_array($group, $currentGroups)) ? 'checked="checked"' : null; ?>>
                                            <label class="form-check-label">
                                                <?php echo htmlspecialchars($group['name'], ENT_QUOTES, 'UTF-8'); ?>
                                            </label>
                                        </div>
                                    <?php endforeach ?>
                                </div>
                            <?php endif ?>
                        </div>
                    </div>
                    <?php echo form_hidden('id', $user->id); ?>
                    <?php echo form_hidden($csrf); ?>
                    <input type="hidden" name="profileimg">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
<?php if ($attendance_trends): ?>
// Attendance Trends Chart Configuration
var chartData = {
    labels: <?= json_encode(array_column($attendance_trends, 'month_short')) ?>,
    datasets: [
        {
            label: 'Present Days',
            data: <?= json_encode(array_column($attendance_trends, 'present_days')) ?>,
            borderColor: '#1abc9c',
            backgroundColor: 'rgba(26, 188, 156, 0.1)',
            tension: 0.4
        },
        {
            label: 'Absent Days',
            data: <?= json_encode(array_column($attendance_trends, 'absent_days')) ?>,
            borderColor: '#e74c3c',
            backgroundColor: 'rgba(231, 76, 60, 0.1)',
            tension: 0.4
        }
    ]
};

var chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'top',
        },
        title: {
            display: false
        }
    },
    scales: {
        y: {
            beginAtZero: true
        }
    }
};

// Screen Chart
var ctx = document.getElementById('trendsChart');
if (ctx) {
    var trendsChart = new Chart(ctx.getContext('2d'), {
        type: 'line',
        data: chartData,
        options: chartOptions
    });
}

// Print Chart
var printCtx = document.getElementById('printTrendsChart');
if (printCtx) {
    var printTrendsChart = new Chart(printCtx.getContext('2d'), {
        type: 'line',
        data: chartData,
        options: chartOptions
    });
}
<?php endif; ?>

// Print function using window.open
function printDiv(divId) {
    var printContents = document.getElementById(divId).innerHTML;
    var printWindow = window.open('', '', 'height=800,width=800');
    printWindow.document.write('<html><head><title>Print Report</title>');
    printWindow.document.write('<style>');
    printWindow.document.write('@page { size: A4 portrait; margin: 1cm; }');
    printWindow.document.write('body { margin: 0; padding: 0; font-family: Arial, sans-serif; -webkit-print-color-adjust: exact; print-color-adjust: exact; }');
    printWindow.document.write('</style>');
    printWindow.document.write('</head><body>');
    printWindow.document.write(printContents);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.focus();
    setTimeout(function() {
        printWindow.print();
        printWindow.close();
    }, 250);
}
</script>