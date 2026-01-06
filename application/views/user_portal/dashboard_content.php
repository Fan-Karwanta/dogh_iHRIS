<!-- Welcome Card -->
<div class="welcome-card">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h3 class="mb-2">Welcome back, <?= isset($current_user) ? htmlspecialchars($current_user->firstname) : 'User' ?>!</h3>
            <p class="mb-0 opacity-90">Here's your DTR summary for <?= date('F Y') ?>. Keep up the good work!</p>
        </div>
        <div class="col-md-4 text-md-right mt-3 mt-md-0">
            <p class="mb-0 small opacity-75">Today is</p>
            <h5 class="mb-0"><?= date('l, F d, Y') ?></h5>
        </div>
    </div>
</div>

<!-- Month/Year Filter -->
<div class="card-custom mb-4">
    <div class="card-body py-3">
        <form method="GET" class="form-inline flex-wrap">
            <label class="mr-2 mb-2">Select Period:</label>
            <select name="month" class="form-control form-control-sm mr-2 mb-2" style="width: auto;">
                <?php for ($m = 1; $m <= 12; $m++): ?>
                    <option value="<?= str_pad($m, 2, '0', STR_PAD_LEFT) ?>" <?= $selected_month == $m ? 'selected' : '' ?>>
                        <?= date('F', mktime(0, 0, 0, $m, 1)) ?>
                    </option>
                <?php endfor; ?>
            </select>
            <select name="year" class="form-control form-control-sm mr-2 mb-2" style="width: auto;">
                <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                    <option value="<?= $y ?>" <?= $selected_year == $y ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
            <button type="submit" class="btn btn-sm btn-primary mb-2">
                <i class="fas fa-filter mr-1"></i> Filter
            </button>
        </form>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row">
    <div class="col-lg-3 col-md-6">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="icon success">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="ml-3">
                    <div class="stat-value"><?= isset($monthly_stats->present_days) ? $monthly_stats->present_days : 0 ?></div>
                    <div class="stat-label">Present Days</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="icon danger">
                    <i class="fas fa-calendar-times"></i>
                </div>
                <div class="ml-3">
                    <div class="stat-value"><?= isset($monthly_stats->absent_days) ? $monthly_stats->absent_days : 0 ?></div>
                    <div class="stat-label">Absent Days</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="icon warning">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="ml-3">
                    <div class="stat-value"><?= isset($monthly_stats->late_count) ? $monthly_stats->late_count : 0 ?></div>
                    <div class="stat-label">Late Arrivals</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="icon primary">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <div class="ml-3">
                    <div class="stat-value"><?= isset($monthly_stats->total_hours) ? number_format($monthly_stats->total_hours, 1) : 0 ?></div>
                    <div class="stat-label">Total Hours</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Missing Clock-ins -->
    <div class="col-lg-6">
        <div class="card-custom">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-exclamation-triangle text-warning mr-2"></i>Missing Clock-ins</h5>
                <span class="badge badge-warning"><?= isset($missing_clockins) ? count($missing_clockins) : 0 ?> records</span>
            </div>
            <div class="card-body" style="max-height: 380px; overflow-y: auto;">
                <?php if (isset($missing_clockins) && !empty($missing_clockins)): ?>
                    <?php foreach (array_slice($missing_clockins, 0, 10) as $missing): ?>
                        <div class="missing-clock-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><?= date('l, M d, Y', strtotime($missing['date'])) ?></strong>
                                    <div class="text-muted small">
                                        Missing: <?= implode(', ', $missing['missing']) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php if (count($missing_clockins) > 10): ?>
                        <p class="text-center text-muted mt-3 mb-0">
                            <a href="<?= site_url('user/dtr') ?>">View all <?= count($missing_clockins) ?> records</a>
                        </p>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 48px;"></i>
                        <p class="mt-3 text-muted mb-0">No missing clock-ins for this period!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Recent Attendance -->
    <div class="col-lg-6">
        <div class="card-custom">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-history text-primary mr-2"></i>Recent Attendance</h5>
                <a href="<?= site_url('user/dtr') ?>" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0" style="max-height: 380px; overflow-y: auto;">
                <?php if (isset($recent_attendance) && !empty($recent_attendance)): ?>
                    <table class="table table-sm mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0">Date</th>
                                <th class="border-0">AM In</th>
                                <th class="border-0">AM Out</th>
                                <th class="border-0">PM In</th>
                                <th class="border-0">PM Out</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($recent_attendance, 0, 10) as $record): ?>
                                <tr>
                                    <td class="font-weight-medium"><?= date('M d', strtotime($record->date)) ?></td>
                                    <td><?= $record->am_in ? '<span class="text-success">' . date('h:i A', strtotime($record->am_in)) . '</span>' : '<span class="text-muted">-</span>' ?></td>
                                    <td><?= $record->am_out ? date('h:i A', strtotime($record->am_out)) : '<span class="text-muted">-</span>' ?></td>
                                    <td><?= $record->pm_in ? date('h:i A', strtotime($record->pm_in)) : '<span class="text-muted">-</span>' ?></td>
                                    <td><?= $record->pm_out ? '<span class="text-info">' . date('h:i A', strtotime($record->pm_out)) . '</span>' : '<span class="text-muted">-</span>' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-alt text-muted" style="font-size: 48px;"></i>
                        <p class="mt-3 text-muted mb-0">No attendance records found.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Performance Summary -->
<?php if (isset($performance) && $performance): ?>
<div class="row">
    <div class="col-12">
        <div class="card-custom">
            <div class="card-header">
                <h5><i class="fas fa-chart-line text-success mr-2"></i>Performance Summary</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3 col-6 mb-3 mb-md-0">
                        <div class="performance-metric">
                            <span class="metric-value text-success"><?= isset($performance->attendance_rate) ? number_format($performance->attendance_rate, 1) : 0 ?>%</span>
                            <div class="metric-label">Attendance Rate</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3 mb-md-0">
                        <div class="performance-metric">
                            <span class="metric-value text-primary"><?= isset($performance->punctuality_rate) ? number_format($performance->punctuality_rate, 1) : 0 ?>%</span>
                            <div class="metric-label">Punctuality Rate</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="performance-metric">
                            <span class="metric-value text-warning"><?= isset($performance->complete_dtr_rate) ? number_format($performance->complete_dtr_rate, 1) : 0 ?>%</span>
                            <div class="metric-label">Complete DTR Rate</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="performance-metric">
                            <span class="metric-value text-dark"><?= isset($yearly_stats->total_days_present) ? $yearly_stats->total_days_present : 0 ?></span>
                            <div class="metric-label">Days Present (Year)</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Personnel Info Card -->
<?php if (isset($personnel) && $personnel): ?>
<div class="row">
    <div class="col-12">
        <div class="card-custom">
            <div class="card-header">
                <h5><i class="fas fa-id-card text-info mr-2"></i>My Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 col-6 mb-3">
                        <label class="text-muted small mb-1">Full Name</label>
                        <p class="font-weight-bold mb-0"><?= htmlspecialchars($personnel->firstname . ' ' . $personnel->middlename . ' ' . $personnel->lastname) ?></p>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <label class="text-muted small mb-1">Position</label>
                        <p class="font-weight-bold mb-0"><?= htmlspecialchars($personnel->position ?: '-') ?></p>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <label class="text-muted small mb-1">Employment Type</label>
                        <p class="font-weight-bold mb-0"><?= htmlspecialchars($personnel->employment_type ?: '-') ?></p>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <label class="text-muted small mb-1">Work Schedule</label>
                        <p class="font-weight-bold mb-0"><?= htmlspecialchars($personnel->schedule_type ?: '8:00 AM - 5:00 PM') ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<style>
    .welcome-card {
        background: linear-gradient(135deg, #31ce36 0%, #1b8e20 100%);
        color: white;
        border-radius: 16px;
        padding: 28px;
        margin-bottom: 24px;
        box-shadow: 0 8px 25px rgba(49, 206, 54, 0.25);
    }
    .missing-clock-item {
        padding: 14px 16px;
        border-left: 4px solid #f25961;
        background: linear-gradient(90deg, #fff5f5 0%, #fff 100%);
        margin-bottom: 10px;
        border-radius: 0 10px 10px 0;
        transition: transform 0.2s ease;
    }
    .missing-clock-item:hover {
        transform: translateX(3px);
    }
    .performance-metric .metric-value {
        font-size: 32px;
        font-weight: 700;
        display: block;
        line-height: 1.2;
    }
    .performance-metric .metric-label {
        color: #8898aa;
        font-size: 13px;
        margin-top: 4px;
    }
    .table th {
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        color: #8898aa;
        padding: 12px 16px;
    }
    .table td {
        padding: 10px 16px;
        vertical-align: middle;
        font-size: 13px;
    }
    .font-weight-medium {
        font-weight: 500;
    }
</style>
