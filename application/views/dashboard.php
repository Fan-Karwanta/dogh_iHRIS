<!-- Dashboard Styles -->
<style>
@media print {
    .no-print, .sidebar, .main-header, .page-header, .breadcrumbs, .filter-section, button, .btn { display: none !important; }
    .main-panel { width: 100% !important; margin: 0 !important; padding: 0 !important; }
    .container { max-width: 100% !important; }
    .card { break-inside: avoid; margin-bottom: 15px !important; box-shadow: none !important; border: 1px solid #ddd !important; }
    body { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
}
.print-header { display: none; }
@media print { .print-header { display: block; text-align: center; margin-bottom: 20px; } }
.filter-section { background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
.stat-card-mini { text-align: center; padding: 8px; }
.stat-card-mini .stat-value { font-size: 22px; font-weight: bold; }
.stat-card-mini .stat-label { font-size: 10px; color: #6c757d; text-transform: uppercase; }
.chart-card { height: 100%; }
.chart-card .card-body { display: flex; flex-direction: column; }
.chart-container-fixed { height: 280px; flex: 1; position: relative; }
.chart-container-fixed canvas { max-height: 280px !important; }
.attendee-item { padding: 10px 0; border-bottom: 1px solid #f0f0f0; }
.attendee-item:last-child { border-bottom: none; }
.attendee-avatar { width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
.attendee-avatar-placeholder { width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #fff; }
.perfect-badge { background: linear-gradient(135deg, #ffd700 0%, #ffb347 100%); color: #333; padding: 2px 8px; border-radius: 12px; font-size: 10px; font-weight: bold; }
.dept-badge { padding: 2px 8px; border-radius: 10px; font-size: 10px; color: #fff; }
</style>

<div class="print-header">
    <h2>DOH DTR Analytics Dashboard</h2>
    <p>Generated on: <?= date('F j, Y g:i A') ?></p>
</div>

<div class="page-header no-print">
    <h4 class="page-title">Analytics Dashboard</h4>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="#"><i class="fas fa-home"></i></a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a href="javascript:void(0)">Dashboard</a></li>
    </ul>
</div>

<!-- Global Filter Section -->
<div class="filter-section no-print">
    <div class="row align-items-end">
        <div class="col-md-3">
            <label class="form-label"><i class="fas fa-calendar-alt mr-1"></i> Date Range</label>
            <select id="dateRangeFilter" class="form-control form-control-sm">
                <option value="this_month" selected>This Month</option>
                <option value="last_month">Last Month</option>
                <option value="last_3_months">Last 3 Months</option>
                <option value="last_6_months">Last 6 Months</option>
                <option value="this_year">This Year</option>
                <option value="custom">Custom Range</option>
            </select>
        </div>
        <div class="col-md-2" id="customStartDate" style="display:none;">
            <label class="form-label">Start Date</label>
            <input type="date" id="startDate" class="form-control form-control-sm" value="<?= date('Y-m-01') ?>">
        </div>
        <div class="col-md-2" id="customEndDate" style="display:none;">
            <label class="form-label">End Date</label>
            <input type="date" id="endDate" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>">
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary btn-sm" onclick="applyFilters()">
                <i class="fas fa-filter mr-1"></i> Apply Filters
            </button>
        </div>
        <div class="col-md-3 text-right">
            <button class="btn btn-success btn-sm mr-2" onclick="printDashboard()">
                <i class="fas fa-print mr-1"></i> Print Dashboard
            </button>
            <button class="btn btn-danger btn-sm" onclick="exportToPDF()">
                <i class="fas fa-file-pdf mr-1"></i> Export PDF
            </button>
        </div>
    </div>
</div>

<!-- Key Metrics Row - 2 cards only -->
<div class="row">
    <div class="col-sm-6 col-md-6">
        <div class="card card-stats card-primary card-round">
            <div class="card-body">
                <div class="row">
                    <div class="col-5">
                        <div class="icon-big text-center"><i class="flaticon-users"></i></div>
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
    <div class="col-sm-6 col-md-6">
        <div class="card card-stats card-warning card-round">
            <div class="card-body">
                <div class="row">
                    <div class="col-5">
                        <div class="icon-big text-center"><i class="flaticon-success"></i></div>
                    </div>
                    <div class="col-7 col-stats">
                        <div class="numbers">
                            <p class="card-category">Schedule Compliance</p>
                            <h4 class="card-title" id="complianceRate"><?= $compliance_stats['compliance_rate'] ?>%</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Row 1: 3 Equal Charts -->
<div class="row">
    <!-- Department Personnel Distribution -->
    <div class="col-md-4">
        <div class="card chart-card">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-building mr-2"></i>Personnel by Department</div>
            </div>
            <div class="card-body">
                <div class="chart-container-fixed">
                    <canvas id="departmentDistChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Schedule Compliance Overview -->
    <div class="col-md-4">
        <div class="card chart-card">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-clipboard-check mr-2"></i>Schedule Compliance</div>
            </div>
            <div class="card-body">
                <div class="row text-center mb-2">
                    <div class="col-6">
                        <div class="stat-card-mini">
                            <div class="stat-value text-success" id="completeDays"><?= $compliance_stats['complete_days'] ?></div>
                            <div class="stat-label">Complete</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-card-mini">
                            <div class="stat-value text-danger" id="incompleteDays"><?= $compliance_stats['incomplete_days'] ?></div>
                            <div class="stat-label">Incomplete</div>
                        </div>
                    </div>
                </div>
                <div style="height: 200px;">
                    <canvas id="complianceDonutChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Missing Entries Breakdown -->
    <div class="col-md-4">
        <div class="card chart-card">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-exclamation-triangle mr-2"></i>Missing Entries</div>
            </div>
            <div class="card-body">
                <div class="chart-container-fixed">
                    <canvas id="missingEntriesChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Row 2: 2 Equal Charts -->
<div class="row">
    <!-- Department Compliance Comparison -->
    <div class="col-md-6">
        <div class="card chart-card">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-chart-bar mr-2"></i>Department Compliance Comparison</div>
            </div>
            <div class="card-body">
                <div class="chart-container-fixed">
                    <canvas id="deptComplianceChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Late Arrivals & Early Departures Trend -->
    <div class="col-md-6">
        <div class="card chart-card">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-clock mr-2"></i>Late Arrivals & Early Departures</div>
            </div>
            <div class="card-body">
                <div class="chart-container-fixed">
                    <canvas id="lateEarlyChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Row 3: Attendance by Day of Week & Edits vs Missing -->
<div class="row">
    <!-- Attendance by Day of Week (replacing 7-day trend) -->
    <div class="col-md-6">
        <div class="card chart-card">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-calendar-week mr-2"></i>Attendance by Day of Week</div>
            </div>
            <div class="card-body">
                <div class="chart-container-fixed">
                    <canvas id="dayOfWeekChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Edits vs Missing Logs -->
    <div class="col-md-6">
        <div class="card chart-card">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-edit mr-2"></i>Edits vs Missing Logs</div>
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-6">
                        <label class="small text-muted mb-1">Edits Period</label>
                        <select id="editsMonthFilter" class="form-control form-control-sm">
                            <?php for ($i = 0; $i < 12; $i++): ?>
                            <option value="<?= date('Y-m', strtotime("-$i months")) ?>" <?= $i == 0 ? 'selected' : '' ?>>
                                <?= date('M Y', strtotime("-$i months")) ?>
                            </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="small text-muted mb-1">DTR Period</label>
                        <select id="dtrMonthFilter" class="form-control form-control-sm">
                            <?php for ($i = 0; $i < 12; $i++): ?>
                            <option value="<?= date('Y-m', strtotime("-$i months")) ?>" <?= $i == 1 ? 'selected' : '' ?>>
                                <?= date('M Y', strtotime("-$i months")) ?>
                            </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                <div style="height: 180px;">
                    <canvas id="breakdownChart"></canvas>
                </div>
                <div class="row mt-2 pt-2" style="border-top: 1px solid #eee;">
                    <div class="col-6 text-center">
                        <small class="text-muted">Total Edits</small>
                        <div style="font-size: 18px; font-weight: bold; color: #36A2EB;" id="totalEditsCount"><?= $chart_data['total_edits'] ?></div>
                    </div>
                    <div class="col-6 text-center">
                        <small class="text-muted">Missing Logs</small>
                        <div style="font-size: 18px; font-weight: bold; color: #FF6384;" id="missingLogsCount"><?= $chart_data['missing_logs'] ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Row 4: Missing Entries Progression & Undertime -->
<div class="row">
    <!-- Missing Entries Progression -->
    <div class="col-md-8">
        <div class="card chart-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="card-title mb-0"><i class="fas fa-exclamation-triangle mr-2"></i>Missing Entries Progression</div>
                <div class="d-flex align-items-center">
                    <label class="small text-muted mb-0 mr-2">From:</label>
                    <select id="missingProgressionFrom" class="form-control form-control-sm mr-2" style="width: 120px;">
                        <?php for ($i = 11; $i >= 0; $i--): ?>
                        <option value="<?= date('Y-m', strtotime("-$i months")) ?>" <?= $i == 5 ? 'selected' : '' ?>>
                            <?= date('M Y', strtotime("-$i months")) ?>
                        </option>
                        <?php endfor; ?>
                    </select>
                    <label class="small text-muted mb-0 mr-2">To:</label>
                    <select id="missingProgressionTo" class="form-control form-control-sm mr-2" style="width: 120px;">
                        <?php for ($i = 11; $i >= 0; $i--): ?>
                        <option value="<?= date('Y-m', strtotime("-$i months")) ?>" <?= $i == 0 ? 'selected' : '' ?>>
                            <?= date('M Y', strtotime("-$i months")) ?>
                        </option>
                        <?php endfor; ?>
                    </select>
                    <button class="btn btn-sm btn-primary" onclick="updateMissingProgression()">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-container-fixed">
                    <canvas id="missingProgressionChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Undertime by Department -->
    <div class="col-md-4">
        <div class="card chart-card">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-hourglass-half mr-2"></i>Undertime by Department</div>
            </div>
            <div class="card-body">
                <div class="chart-container-fixed">
                    <canvas id="undertimeChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Row 5: Personnel Status, Peak Hours, Top Attendees -->
<div class="row">
    <!-- Personnel Status Distribution -->
    <div class="col-md-4">
        <div class="card chart-card">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-user-check mr-2"></i>Personnel Status</div>
            </div>
            <div class="card-body">
                <div class="row text-center mb-2">
                    <div class="col-6">
                        <div class="stat-card-mini">
                            <div class="stat-value text-primary"><?= $personnel_status['regular'] ?></div>
                            <div class="stat-label">Regular</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-card-mini">
                            <div class="stat-value text-info"><?= $personnel_status['contract'] ?></div>
                            <div class="stat-label">Contract/COS</div>
                        </div>
                    </div>
                </div>
                <div style="height: 200px;">
                    <canvas id="personnelStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Peak Attendance Hours -->
    <div class="col-md-4">
        <div class="card chart-card">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-history mr-2"></i>Peak Attendance Hours</div>
            </div>
            <div class="card-body">
                <div class="chart-container-fixed">
                    <canvas id="peakHoursChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Top Attendees (Filter-based) -->
    <div class="col-md-4">
        <div class="card chart-card">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-trophy mr-2"></i>Top Attendees <small class="text-muted" id="topAttendeesLabel">(This Month)</small></div>
            </div>
            <div class="card-body" id="topAttendeesContainer">
                <?php if (!empty($top_attendees_filtered)): ?>
                    <?php foreach ($top_attendees_filtered as $index => $attendee): ?>
                        <div class="attendee-item d-flex align-items-center">
                            <?php if (!empty($attendee->profile_image)): ?>
                                <img src="<?= base_url('uploads/profiles/' . $attendee->profile_image) ?>" class="attendee-avatar mr-3" alt="">
                            <?php else: ?>
                                <div class="attendee-avatar-placeholder mr-3" style="background: <?= $attendee->department_color ?: '#6c757d' ?>;">
                                    <?= strtoupper(substr($attendee->firstname, 0, 1) . substr($attendee->lastname, 0, 1)) ?>
                                </div>
                            <?php endif; ?>
                            <div class="flex-1">
                                <h6 class="mb-0" style="font-size: 13px;"><?= $attendee->firstname . ' ' . $attendee->lastname ?></h6>
                                <span class="dept-badge" style="background: <?= $attendee->department_color ?: '#6c757d' ?>;">
                                    <?= $attendee->department_name ?: 'N/A' ?>
                                </span>
                            </div>
                            <div class="text-right">
                                <div class="badge badge-success"><?= $attendee->complete_days ?></div>
                                <div style="font-size: 10px; color: #888;">days</div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted text-center py-4">No attendance data for this period</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Row 6: Perfect Attendance -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-star mr-2 text-warning"></i>Perfect Attendance <small class="text-muted" id="perfectAttendanceLabel">(This Month)</small></div>
            </div>
            <div class="card-body" id="perfectAttendanceContainer">
                <?php if (!empty($perfect_attendance)): ?>
                    <div class="row">
                        <?php foreach ($perfect_attendance as $person): ?>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="d-flex align-items-center p-2" style="background: #f8f9fa; border-radius: 8px;">
                                    <?php if (!empty($person->profile_image)): ?>
                                        <img src="<?= base_url('uploads/profiles/' . $person->profile_image) ?>" class="attendee-avatar mr-3" alt="">
                                    <?php else: ?>
                                        <div class="attendee-avatar-placeholder mr-3" style="background: <?= $person->department_color ?: '#6c757d' ?>;">
                                            <?= strtoupper(substr($person->firstname, 0, 1) . substr($person->lastname, 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="flex-1">
                                        <h6 class="mb-0" style="font-size: 13px;"><?= $person->firstname . ' ' . $person->lastname ?></h6>
                                        <span class="dept-badge" style="background: <?= $person->department_color ?: '#6c757d' ?>;">
                                            <?= $person->department_name ?: 'N/A' ?>
                                        </span>
                                    </div>
                                    <span class="perfect-badge"><i class="fas fa-star mr-1"></i>Perfect</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-award fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No personnel with perfect attendance for this period yet.</p>
                        <small class="text-muted">Perfect attendance requires complete AM IN, AM OUT, PM IN, and PM OUT for all working days.</small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js and html2pdf Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
// Color palette for charts
const chartColors = {
    primary: 'rgba(54, 162, 235, 0.85)',
    danger: 'rgba(255, 99, 132, 0.85)',
    success: 'rgba(75, 192, 192, 0.85)',
    warning: 'rgba(255, 206, 86, 0.85)',
    info: 'rgba(153, 102, 255, 0.85)',
    secondary: 'rgba(201, 203, 207, 0.85)'
};

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    resizeDelay: 0,
    animation: {
        duration: 0
    }
};

// 1. Department Distribution Chart (Doughnut)
const departmentDistChart = new Chart(document.getElementById('departmentDistChart'), {
    type: 'doughnut',
    data: {
        labels: [<?php echo "'" . implode("', '", array_map(function($d) { return addslashes($d->name); }, $department_distribution)) . "'"; ?>],
        datasets: [{
            data: [<?php echo implode(', ', array_column($department_distribution, 'personnel_count')); ?>],
            backgroundColor: [<?php foreach ($department_distribution as $dept) { echo "'" . ($dept->color ?: '#' . substr(md5($dept->name), 0, 6)) . "', "; } ?>],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: { 
        ...chartOptions, 
        plugins: { legend: { position: 'right', labels: { boxWidth: 10, font: { size: 10 } } } },
        resizeDelay: 0,
        layout: { padding: 0 }
    }
});

// 2. Compliance Donut Chart
const complianceDonutChart = new Chart(document.getElementById('complianceDonutChart'), {
    type: 'doughnut',
    data: {
        labels: ['Complete', 'Incomplete'],
        datasets: [{ data: [<?= $compliance_stats['complete_days'] ?>, <?= $compliance_stats['incomplete_days'] ?>], backgroundColor: [chartColors.success, chartColors.danger], borderWidth: 0 }]
    },
    options: { ...chartOptions, cutout: '70%', plugins: { legend: { display: false } } }
});

// 3. Missing Entries Chart
const missingEntriesChart = new Chart(document.getElementById('missingEntriesChart'), {
    type: 'bar',
    data: {
        labels: ['AM IN', 'AM OUT', 'PM IN', 'PM OUT'],
        datasets: [{ data: [<?= $missing_entries['am_in'] ?>, <?= $missing_entries['am_out'] ?>, <?= $missing_entries['pm_in'] ?>, <?= $missing_entries['pm_out'] ?>], backgroundColor: [chartColors.danger, chartColors.warning, chartColors.info, chartColors.primary], borderRadius: 4 }]
    },
    options: { ...chartOptions, indexAxis: 'y', plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true } } }
});

// 4. Department Compliance Chart
const deptComplianceChart = new Chart(document.getElementById('deptComplianceChart'), {
    type: 'bar',
    data: {
        labels: [<?php echo "'" . implode("', '", array_map(function($d) { return addslashes($d->name); }, $department_compliance)) . "'"; ?>],
        datasets: [{ label: 'Compliance %', data: [<?php echo implode(', ', array_map(function($d) { return $d->compliance_rate; }, $department_compliance)); ?>], backgroundColor: [<?php foreach ($department_compliance as $dept) { echo "'" . ($dept->color ?: 'rgba(54, 162, 235, 0.85)') . "', "; } ?>], borderRadius: 4 }]
    },
    options: { ...chartOptions, indexAxis: 'y', plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true, max: 100, ticks: { callback: v => v + '%' } } } }
});

// 5. Late/Early Trend Chart
const lateEarlyChart = new Chart(document.getElementById('lateEarlyChart'), {
    type: 'line',
    data: {
        labels: [<?php echo "'" . implode("', '", array_map(function($d) { return date('M j', strtotime($d->record_date)); }, $late_early_trend)) . "'"; ?>],
        datasets: [
            { label: 'Late Arrivals', data: [<?php echo implode(', ', array_map(function($d) { return $d->late_arrivals; }, $late_early_trend)); ?>], borderColor: 'rgb(255, 99, 132)', backgroundColor: 'rgba(255, 99, 132, 0.1)', fill: true, tension: 0.4 },
            { label: 'Early Departures', data: [<?php echo implode(', ', array_map(function($d) { return $d->early_departures; }, $late_early_trend)); ?>], borderColor: 'rgb(255, 206, 86)', backgroundColor: 'rgba(255, 206, 86, 0.1)', fill: true, tension: 0.4 }
        ]
    },
    options: { ...chartOptions, plugins: { legend: { position: 'top', labels: { boxWidth: 12 } } }, scales: { y: { beginAtZero: true } } }
});

// 6. Attendance by Day of Week Chart (NEW - replacing 7-day trend)
const dayOfWeekChart = new Chart(document.getElementById('dayOfWeekChart'), {
    type: 'bar',
    data: {
        labels: [<?php echo "'" . implode("', '", array_map(function($d) { return $d->day_name; }, $attendance_by_day)) . "'"; ?>],
        datasets: [
            { label: 'Total Records', data: [<?php echo implode(', ', array_map(function($d) { return $d->total_records; }, $attendance_by_day)); ?>], backgroundColor: chartColors.primary, borderRadius: 4 },
            { label: 'Complete', data: [<?php echo implode(', ', array_map(function($d) { return $d->complete_records; }, $attendance_by_day)); ?>], backgroundColor: chartColors.success, borderRadius: 4 }
        ]
    },
    options: { ...chartOptions, plugins: { legend: { position: 'top', labels: { boxWidth: 12 } } }, scales: { y: { beginAtZero: true } } }
});

// 7. Edits vs Missing Chart
let breakdownChart = new Chart(document.getElementById('breakdownChart'), {
    type: 'bar',
    data: {
        labels: ['Edits', 'Missing'],
        datasets: [{ data: [<?= $chart_data['total_edits'] ?>, <?= $chart_data['missing_logs'] ?>], backgroundColor: [chartColors.primary, chartColors.danger], borderRadius: 6 }]
    },
    options: { ...chartOptions, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});

// 8. Missing Entries Progression Chart
let missingProgressionChart = new Chart(document.getElementById('missingProgressionChart'), {
    type: 'bar',
    data: {
        labels: [],
        datasets: [
            { label: 'AM IN', data: [], backgroundColor: chartColors.danger, borderRadius: 4 },
            { label: 'AM OUT', data: [], backgroundColor: chartColors.warning, borderRadius: 4 },
            { label: 'PM IN', data: [], backgroundColor: chartColors.info, borderRadius: 4 },
            { label: 'PM OUT', data: [], backgroundColor: chartColors.primary, borderRadius: 4 }
        ]
    },
    options: { 
        ...chartOptions, 
        plugins: { legend: { position: 'top', labels: { boxWidth: 12 } } }, 
        scales: { y: { beginAtZero: true, title: { display: true, text: 'Missing Entries Count' } } }
    }
});

// Load initial missing entries progression data
updateMissingProgression();

function updateMissingProgression() {
    const fromMonth = document.getElementById('missingProgressionFrom').value;
    const toMonth = document.getElementById('missingProgressionTo').value;
    
    fetch('<?= site_url('dashboard/get_missing_entries_progression') ?>?from_month=' + fromMonth + '&to_month=' + toMonth)
        .then(r => r.json())
        .then(response => {
            if (response.success && response.data) {
                const data = response.data;
                missingProgressionChart.data.labels = data.map(d => d.month_label);
                missingProgressionChart.data.datasets[0].data = data.map(d => parseInt(d.am_in_missing) || 0);
                missingProgressionChart.data.datasets[1].data = data.map(d => parseInt(d.am_out_missing) || 0);
                missingProgressionChart.data.datasets[2].data = data.map(d => parseInt(d.pm_in_missing) || 0);
                missingProgressionChart.data.datasets[3].data = data.map(d => parseInt(d.pm_out_missing) || 0);
                missingProgressionChart.update();
            }
        })
        .catch(err => console.error('Missing progression error:', err));
}

// 9. Undertime Chart
const undertimeChart = new Chart(document.getElementById('undertimeChart'), {
    type: 'bar',
    data: {
        labels: [<?php echo "'" . implode("', '", array_map(function($d) { return addslashes($d->name); }, $undertime_by_dept)) . "'"; ?>],
        datasets: [{ data: [<?php echo implode(', ', array_map(function($d) { return $d->total_undertime; }, $undertime_by_dept)); ?>], backgroundColor: chartColors.warning, borderRadius: 4 }]
    },
    options: { ...chartOptions, indexAxis: 'y', plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true, ticks: { callback: v => v + 'h' } } } }
});

// 10. Personnel Status Chart
const personnelStatusChart = new Chart(document.getElementById('personnelStatusChart'), {
    type: 'pie',
    data: {
        labels: ['Active', 'Inactive'],
        datasets: [{ data: [<?= $personnel_status['active'] ?>, <?= $personnel_status['inactive'] ?>], backgroundColor: [chartColors.success, chartColors.secondary], borderWidth: 2, borderColor: '#fff' }]
    },
    options: { ...chartOptions, plugins: { legend: { position: 'bottom', labels: { boxWidth: 12 } } } }
});

// 11. Peak Hours Chart
<?php
$am_in_hours = array_fill(5, 6, 0);
$pm_out_hours = array_fill(15, 6, 0);
foreach ($peak_hours as $ph) {
    if ($ph->type == 'AM IN' && $ph->hour >= 5 && $ph->hour <= 10) $am_in_hours[$ph->hour] = $ph->count;
    elseif ($ph->type == 'PM OUT' && $ph->hour >= 15 && $ph->hour <= 20) $pm_out_hours[$ph->hour] = $ph->count;
}
?>
const peakHoursChart = new Chart(document.getElementById('peakHoursChart'), {
    type: 'bar',
    data: {
        labels: ['5AM', '6AM', '7AM', '8AM', '9AM', '10AM', '3PM', '4PM', '5PM', '6PM', '7PM', '8PM'],
        datasets: [
            { label: 'AM IN', data: [<?= implode(',', array_values($am_in_hours)) ?>, 0, 0, 0, 0, 0, 0], backgroundColor: chartColors.success },
            { label: 'PM OUT', data: [0, 0, 0, 0, 0, 0, <?= implode(',', array_values($pm_out_hours)) ?>], backgroundColor: chartColors.info }
        ]
    },
    options: { ...chartOptions, plugins: { legend: { position: 'top', labels: { boxWidth: 12 } } }, scales: { y: { beginAtZero: true, stacked: true }, x: { stacked: true } } }
});

// Edits filter handlers
document.getElementById('editsMonthFilter').addEventListener('change', updateEditsChart);
document.getElementById('dtrMonthFilter').addEventListener('change', updateEditsChart);

function updateEditsChart() {
    fetch('<?= site_url('biometrics/get_dashboard_chart_data') ?>?edits_month=' + document.getElementById('editsMonthFilter').value + '&dtr_month=' + document.getElementById('dtrMonthFilter').value)
        .then(r => r.json())
        .then(data => {
            breakdownChart.data.datasets[0].data = [data.total_edits, data.missing_logs];
            breakdownChart.update();
            document.getElementById('totalEditsCount').textContent = data.total_edits;
            document.getElementById('missingLogsCount').textContent = data.missing_logs;
        });
}

// Date Range Filter
document.getElementById('dateRangeFilter').addEventListener('change', function() {
    const show = this.value === 'custom';
    document.getElementById('customStartDate').style.display = show ? 'block' : 'none';
    document.getElementById('customEndDate').style.display = show ? 'block' : 'none';
});

function getDateRange() {
    const filter = document.getElementById('dateRangeFilter').value;
    const today = new Date();
    let start, end;
    switch(filter) {
        case 'this_month': start = new Date(today.getFullYear(), today.getMonth(), 1); end = new Date(today.getFullYear(), today.getMonth() + 1, 0); break;
        case 'last_month': start = new Date(today.getFullYear(), today.getMonth() - 1, 1); end = new Date(today.getFullYear(), today.getMonth(), 0); break;
        case 'last_3_months': start = new Date(today.getFullYear(), today.getMonth() - 2, 1); end = new Date(today.getFullYear(), today.getMonth() + 1, 0); break;
        case 'last_6_months': start = new Date(today.getFullYear(), today.getMonth() - 5, 1); end = new Date(today.getFullYear(), today.getMonth() + 1, 0); break;
        case 'this_year': start = new Date(today.getFullYear(), 0, 1); end = new Date(today.getFullYear(), today.getMonth() + 1, 0); break;
        case 'custom': start = new Date(document.getElementById('startDate').value); end = new Date(document.getElementById('endDate').value); break;
    }
    return { start: start.toISOString().split('T')[0], end: end.toISOString().split('T')[0] };
}

function getFilterLabel() {
    const filter = document.getElementById('dateRangeFilter').value;
    const labels = { 'this_month': 'This Month', 'last_month': 'Last Month', 'last_3_months': 'Last 3 Months', 'last_6_months': 'Last 6 Months', 'this_year': 'This Year', 'custom': 'Custom' };
    return labels[filter] || 'This Month';
}

function applyFilters() {
    const range = getDateRange();
    const label = getFilterLabel();
    
    // Update labels
    document.getElementById('topAttendeesLabel').textContent = '(' + label + ')';
    document.getElementById('perfectAttendanceLabel').textContent = '(' + label + ')';
    
    fetch('<?= site_url('dashboard/get_filtered_data') ?>?start_date=' + range.start + '&end_date=' + range.end)
        .then(r => r.json())
        .then(data => {
            // Update compliance
            document.getElementById('complianceRate').textContent = data.compliance_stats.compliance_rate + '%';
            document.getElementById('completeDays').textContent = data.compliance_stats.complete_days;
            document.getElementById('incompleteDays').textContent = data.compliance_stats.incomplete_days;
            complianceDonutChart.data.datasets[0].data = [data.compliance_stats.complete_days, data.compliance_stats.incomplete_days];
            complianceDonutChart.update();
            
            // Update missing entries
            missingEntriesChart.data.datasets[0].data = [data.missing_entries.am_in, data.missing_entries.am_out, data.missing_entries.pm_in, data.missing_entries.pm_out];
            missingEntriesChart.update();
            
            // Update department compliance
            if (data.department_compliance && data.department_compliance.length > 0) {
                deptComplianceChart.data.labels = data.department_compliance.map(d => d.name);
                deptComplianceChart.data.datasets[0].data = data.department_compliance.map(d => d.compliance_rate);
                deptComplianceChart.update();
            }
            
            // Update undertime
            if (data.undertime_by_dept && data.undertime_by_dept.length > 0) {
                undertimeChart.data.labels = data.undertime_by_dept.map(d => d.name);
                undertimeChart.data.datasets[0].data = data.undertime_by_dept.map(d => d.total_undertime);
                undertimeChart.update();
            }
            
            // Update attendance by day
            if (data.attendance_by_day && data.attendance_by_day.length > 0) {
                dayOfWeekChart.data.labels = data.attendance_by_day.map(d => d.day_name);
                dayOfWeekChart.data.datasets[0].data = data.attendance_by_day.map(d => d.total_records);
                dayOfWeekChart.data.datasets[1].data = data.attendance_by_day.map(d => d.complete_records);
                dayOfWeekChart.update();
            }
            
            // Update Top Attendees
            updateTopAttendees(data.top_attendees || []);
            
            // Update Perfect Attendance
            updatePerfectAttendance(data.perfect_attendance || []);
            
            if (typeof $.notify !== 'undefined') $.notify({ message: 'Dashboard updated' }, { type: 'success' });
        })
        .catch(err => {
            console.error('Filter error:', err);
            if (typeof $.notify !== 'undefined') $.notify({ message: 'Error updating dashboard' }, { type: 'danger' });
        });
}

function updateTopAttendees(attendees) {
    const container = document.getElementById('topAttendeesContainer');
    if (attendees.length === 0) {
        container.innerHTML = '<p class="text-muted text-center py-4">No attendance data for this period</p>';
        return;
    }
    let html = '';
    attendees.forEach(a => {
        const initials = (a.firstname?.charAt(0) || '') + (a.lastname?.charAt(0) || '');
        const avatar = a.profile_image 
            ? `<img src="<?= base_url('uploads/profiles/') ?>${a.profile_image}" class="attendee-avatar mr-3" alt="">`
            : `<div class="attendee-avatar-placeholder mr-3" style="background: ${a.department_color || '#6c757d'};">${initials.toUpperCase()}</div>`;
        html += `<div class="attendee-item d-flex align-items-center">
            ${avatar}
            <div class="flex-1">
                <h6 class="mb-0" style="font-size: 13px;">${a.firstname} ${a.lastname}</h6>
                <span class="dept-badge" style="background: ${a.department_color || '#6c757d'};">${a.department_name || 'N/A'}</span>
            </div>
            <div class="text-right">
                <div class="badge badge-success">${a.complete_days}</div>
                <div style="font-size: 10px; color: #888;">days</div>
            </div>
        </div>`;
    });
    container.innerHTML = html;
}

function updatePerfectAttendance(people) {
    const container = document.getElementById('perfectAttendanceContainer');
    if (people.length === 0) {
        container.innerHTML = `<div class="text-center py-4">
            <i class="fas fa-award fa-3x text-muted mb-3"></i>
            <p class="text-muted">No personnel with perfect attendance for this period yet.</p>
            <small class="text-muted">Perfect attendance requires complete AM IN, AM OUT, PM IN, and PM OUT for all working days.</small>
        </div>`;
        return;
    }
    let html = '<div class="row">';
    people.forEach(p => {
        const initials = (p.firstname?.charAt(0) || '') + (p.lastname?.charAt(0) || '');
        const avatar = p.profile_image 
            ? `<img src="<?= base_url('uploads/profiles/') ?>${p.profile_image}" class="attendee-avatar mr-3" alt="">`
            : `<div class="attendee-avatar-placeholder mr-3" style="background: ${p.department_color || '#6c757d'};">${initials.toUpperCase()}</div>`;
        html += `<div class="col-md-3 col-sm-6 mb-3">
            <div class="d-flex align-items-center p-2" style="background: #f8f9fa; border-radius: 8px;">
                ${avatar}
                <div class="flex-1">
                    <h6 class="mb-0" style="font-size: 13px;">${p.firstname} ${p.lastname}</h6>
                    <span class="dept-badge" style="background: ${p.department_color || '#6c757d'};">${p.department_name || 'N/A'}</span>
                </div>
                <span class="perfect-badge"><i class="fas fa-star mr-1"></i>Perfect</span>
            </div>
        </div>`;
    });
    html += '</div>';
    container.innerHTML = html;
}

function printDashboard() { window.print(); }

function exportToPDF() {
    const btn = event.target.closest('button');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Generating...';
    btn.disabled = true;
    
    html2pdf().set({
        margin: 10,
        filename: 'DTR_Dashboard_' + new Date().toISOString().split('T')[0] + '.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, useCORS: true },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' }
    }).from(document.querySelector('.container')).save().then(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    }).catch(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}

// Fix chart height growth issue
document.addEventListener('DOMContentLoaded', function() {
    // Prevent charts from resizing continuously
    const chartContainers = document.querySelectorAll('.chart-container-fixed');
    chartContainers.forEach(container => {
        const canvas = container.querySelector('canvas');
        if (canvas) {
            // Set explicit dimensions
            canvas.style.height = '280px';
            canvas.style.maxHeight = '280px';
            canvas.style.width = '100%';
        }
    });
    
    // Disable Chart.js automatic resize
    Chart.defaults.responsive = true;
    Chart.defaults.maintainAspectRatio = false;
    Chart.defaults.resizeDelay = 0;
    Chart.defaults.animation.duration = 0;
});
</script>