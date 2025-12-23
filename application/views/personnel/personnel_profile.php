<?php
// Personnel Profile View (for personnel without user accounts)
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
.chart-container {
    position: relative;
    height: 300px;
}

/* Print styles */
@media print {
    /* Hide all navigation and branding elements */
    .no-print, .sidebar, .navbar, .page-header,
    .main-header, .logo-header, .nav-toggle,
    .nav-tabs, .tab-pane:not(.active), 
    .card-stats,
    #recent, #audit,
    header, nav, .topbar, .navbar-header {
        display: none !important;
    }
    
    /* Show only the active trends tab */
    #trends.active {
        display: block !important;
    }
    
    @page {
        size: A4 landscape;
        margin: 0.4cm;
    }
    
    /* Prevent second page */
    html, body {
        height: auto !important;
        overflow: hidden !important;
    }
    
    body {
        margin: 0;
        padding: 0;
        font-size: 9pt;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    
    /* Ensure main content is visible and starts at top */
    .main-panel, .content, .container {
        width: 100% !important;
        margin: 0 !important;
        padding: 5px !important;
        max-width: 100% !important;
    }
    
    /* Remove any top spacing */
    .row {
        margin-top: 0 !important;
    }
    
    /* Add print header with profile image */
    .row:first-of-type::before {
        content: 'Personnel Profile & Analytics Report';
        display: block;
        text-align: center;
        font-size: 14pt;
        font-weight: bold;
        color: #1a73e8;
        margin-bottom: 8px;
        padding-bottom: 5px;
        border-bottom: 2px solid #1a73e8;
    }
    
    /* Show profile image in print */
    .print-profile-image {
        display: block !important;
        width: 80px !important;
        height: 80px !important;
        border-radius: 50% !important;
        object-fit: cover !important;
        margin: 0 auto 8px auto !important;
        border: 2px solid #1a73e8 !important;
    }
    
    /* Keep the 2-column layout */
    .col-md-3 {
        width: 25% !important;
        max-width: 25% !important;
        flex: 0 0 25% !important;
        padding: 5px !important;
    }
    
    .col-md-9 {
        width: 75% !important;
        max-width: 75% !important;
        flex: 0 0 75% !important;
        padding: 5px !important;
    }
    
    /* Compact profile card */
    .card-profile {
        margin-bottom: 0 !important;
        margin-top: 0 !important;
    }
    
    /* Hide the card header with iHRIS logo */
    .card-profile .card-header {
        display: none !important;
    }
    
    .card-profile .avatar,
    .card-profile .profile-picture {
        display: none !important;
    }
    
    /* Show only the print profile image */
    .print-profile-image-container {
        display: block !important;
        text-align: center !important;
        margin-bottom: 5px !important;
    }
    
    .card-profile .card-body {
        padding: 8px !important;
        font-size: 8pt !important;
    }
    
    .card-profile .name {
        font-size: 11pt !important;
        margin-bottom: 2px !important;
    }
    
    .card-profile .job,
    .card-profile .desc {
        font-size: 8pt !important;
        margin-bottom: 2px !important;
    }
    
    .card-profile small {
        font-size: 7pt !important;
    }
    
    .card-profile .btn {
        display: none !important;
    }
    
    /* Style the stat cards for print - make them smaller */
    .row {
        display: flex !important;
        flex-wrap: wrap !important;
        margin: 0 !important;
    }
    
    .stat-card {
        page-break-inside: avoid;
        margin-bottom: 3px !important;
    }
    
    .stat-card .card-body {
        padding: 5px !important;
    }
    
    .stat-card h6 {
        font-size: 8pt !important;
        margin-bottom: 2px !important;
    }
    
    .stat-card h3 {
        font-size: 14pt !important;
        margin: 0 !important;
    }
    
    .stat-card p,
    .stat-card small {
        font-size: 7pt !important;
        margin: 0 !important;
    }
    
    /* Make chart much smaller to fit on one page */
    .chart-container {
        page-break-inside: avoid;
        height: 180px !important;
        margin: 5px 0 !important;
    }
    
    canvas {
        max-width: 100% !important;
        height: 180px !important;
    }
    
    /* Compact card styling */
    .card {
        margin-bottom: 5px !important;
        box-shadow: none !important;
    }
    
    .card-body {
        padding: 8px !important;
    }
    
    /* Smaller headings */
    h4, h5 {
        font-size: 10pt !important;
        margin: 3px 0 !important;
    }
    
    /* Hide performance grade section to save space */
    .performance-badge {
        display: none !important;
    }
    
    /* Hide footer to prevent page overflow */
    footer, .footer {
        display: none !important;
    }
    
    /* Prevent any page breaks */
    * {
        page-break-after: avoid !important;
        page-break-before: avoid !important;
    }
    
    /* Keep everything on one page */
    .main-panel {
        page-break-after: avoid !important;
    }
}
</style>

<div class="page-header">
    <h4 class="page-title"><?= $title ?></h4>
    <div class="ml-auto">
        <button onclick="printPersonnelReport()" class="btn btn-primary btn-sm mr-2 no-print">
            <i class="fas fa-print"></i> Print Report
        </button>
        <a href="<?= site_url('personnel') ?>" class="btn btn-secondary btn-sm mr-2 no-print">
            <i class="fas fa-arrow-left"></i> Back to Personnel List
        </a>
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


<div class="row">
    <!-- Profile Card -->
    <div class="col-md-3">
        <div class="card card-profile">
            <div class="card-header" style="background-image: url('<?= site_url() ?>/assets/img/blogpost.jpg')">
                <div class="profile-picture">
                    <div class="avatar avatar-xl">
                        <?php 
                        $profile_image_url = $personnel->profile_image 
                            ? site_url('assets/uploads/profile_images/' . $personnel->profile_image) 
                            : site_url('assets/img/person.png');
                        ?>
                        <img class="avatar-img rounded-circle" alt="<?= $personnel->firstname ?>" src="<?= $profile_image_url ?>" id="profileImagePreview" />
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Profile image for print -->
                <div class="print-profile-image-container" style="display: none;">
                    <?php 
                    $print_profile_image_url = $personnel->profile_image 
                        ? site_url('assets/uploads/profile_images/' . $personnel->profile_image) 
                        : site_url('assets/img/person.png');
                    ?>
                    <img src="<?= $print_profile_image_url ?>" alt="<?= $personnel->firstname ?>" class="print-profile-image" />
                </div>
                <div class="user-profile text-center">
                    <div class="name"><?= $personnel->firstname . ' ' . $personnel->lastname ?></div>
                    <div class="job"><?= $personnel->position ?></div>
                    <div class="desc"><?= $personnel->email ?></div>
                    <?php if ($personnel->employment_type): ?>
                        <div class="mt-2"><small class="text-muted"><i class="fas fa-id-card"></i> <?= $personnel->employment_type ?></small></div>
                    <?php endif; ?>
                    <?php if ($personnel->salary_grade): ?>
                        <div><small class="text-muted"><i class="fas fa-money-bill-wave"></i> SG <?= $personnel->salary_grade ?></small></div>
                    <?php endif; ?>
                    <?php if ($personnel->schedule_type): ?>
                        <div><small class="text-muted"><i class="fas fa-clock"></i> <?= $personnel->schedule_type ?></small></div>
                    <?php endif; ?>
                    <?php if ($personnel->bio_id): ?>
                        <div><small class="text-muted"><i class="fas fa-fingerprint"></i> Bio ID: <?= $personnel->bio_id ?></small></div>
                    <?php endif; ?>
                </div>
                <hr>
                <div class="text-center">
                    <button type="button" class="btn btn-info btn-sm btn-block mb-2" data-toggle="modal" data-target="#uploadImageModal">
                        <i class="fas fa-camera"></i> <?= $personnel->profile_image ? 'Change' : 'Upload' ?> Profile Image
                    </button>
                    <?php if ($personnel->profile_image): ?>
                        <a href="<?= site_url('personnel/delete_profile_image/' . $personnel->id) ?>" class="btn btn-danger btn-sm btn-block mb-2" onclick="return confirm('Are you sure you want to delete this profile image?');">
                            <i class="fas fa-trash"></i> Remove Profile Image
                        </a>
                    <?php endif; ?>
                    <?php if ($personnel->fb): ?>
                        <a href="<?= $personnel->fb ?>" target="_blank" class="btn btn-primary btn-sm btn-block mb-2">
                            <i class="fab fa-facebook"></i> Facebook Profile
                        </a>
                    <?php endif; ?>
                    <a href="<?= site_url('admin/generate_dtr/') . $personnel->id ?>" class="btn btn-secondary btn-sm btn-block">
                        <i class="fas fa-file"></i> Generate DTR
                    </a>
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
    <div class="col-md-9 print-full-width">
        <?php if ($monthly_stats): ?>
        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-md-3">
                <a href="<?= site_url('personnel/attendance_history/' . $personnel->id . '/present_days?month=' . $selected_month . '&year=' . $selected_year) ?>" style="text-decoration: none; color: inherit;">
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
                <a href="<?= site_url('personnel/attendance_history/' . $personnel->id . '/absent_days?month=' . $selected_month . '&year=' . $selected_year) ?>" style="text-decoration: none; color: inherit;">
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
                <a href="<?= site_url('personnel/attendance_history/' . $personnel->id . '/late_arrivals?month=' . $selected_month . '&year=' . $selected_year) ?>" style="text-decoration: none; color: inherit;">
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
                <a href="<?= site_url('personnel/attendance_history/' . $personnel->id . '/early_departures?month=' . $selected_month . '&year=' . $selected_year) ?>" style="text-decoration: none; color: inherit;">
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
                <a href="<?= site_url('personnel/attendance_history/' . $personnel->id . '/complete_dtr?month=' . $selected_month . '&year=' . $selected_year) ?>" style="text-decoration: none; color: inherit;">
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
                <a href="<?= site_url('personnel/analytics_justification/' . $personnel->id . '/total_hours?month=' . $selected_month . '&year=' . $selected_year) ?>" style="text-decoration: none; color: inherit;">
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
                <a href="<?= site_url('personnel/analytics_justification/' . $personnel->id . '/avg_late_arrival?month=' . $selected_month . '&year=' . $selected_year) ?>" style="text-decoration: none; color: inherit;">
                    <div class="card stat-card">
                        <div class="card-body text-center">
                            <h6 class="text-muted">Avg Late Arrival</h6>
                            <h2 class="text-warning">
                                <?php if ($monthly_stats['avg_late_arrival_time']): ?>
                                    <?php 
                                    $minutes = $monthly_stats['avg_late_arrival_time'];
                                    if ($minutes < 60) {
                                        echo $minutes . ' min';
                                    } else {
                                        $hours = floor($minutes / 60);
                                        $mins = $minutes % 60;
                                        echo $hours . 'h ' . ($mins > 0 ? $mins . 'm' : '');
                                    }
                                    ?>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </h2>
                            <small class="text-muted">avg time late (<?= $monthly_stats['late_days'] ?> days)</small>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="<?= site_url('personnel/analytics_justification/' . $personnel->id . '/avg_early_departure?month=' . $selected_month . '&year=' . $selected_year) ?>" style="text-decoration: none; color: inherit;">
                    <div class="card stat-card">
                        <div class="card-body text-center">
                            <h6 class="text-muted">Avg Early Departure</h6>
                            <h2 class="text-warning">
                                <?php if ($monthly_stats['avg_early_departure_time']): ?>
                                    <?php 
                                    $minutes = $monthly_stats['avg_early_departure_time'];
                                    if ($minutes < 60) {
                                        echo $minutes . ' min';
                                    } else {
                                        $hours = floor($minutes / 60);
                                        $mins = $minutes % 60;
                                        echo $hours . 'h ' . ($mins > 0 ? $mins . 'm' : '');
                                    }
                                    ?>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </h2>
                            <small class="text-muted">avg time early (<?= $monthly_stats['early_departures'] ?> days)</small>
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

                    <!-- Audit Trail Tab -->
                    <div class="tab-pane fade" id="audit" role="tabpanel">
                        <div class="print-only print-section-title">Audit Trail - Recent Changes to DTR</div>
                        <?php if ($audit_trail && count($audit_trail) > 0): ?>
                       
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

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
<?php if ($attendance_trends): ?>
// Attendance Trends Chart
var ctx = document.getElementById('trendsChart').getContext('2d');
var trendsChart = new Chart(ctx, {
    type: 'line',
    data: {
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
    },
    options: {
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
    }
});
<?php endif; ?>

// Print function for personnel report
function printPersonnelReport() {
    // Make sure we're on the trends tab to show the chart
    var trendsTab = document.querySelector('a[href="#trends"]');
    if (trendsTab && !trendsTab.parentElement.classList.contains('active')) {
        trendsTab.click();
        setTimeout(function() {
            window.print();
        }, 300);
    } else {
        window.print();
    }
}

// Profile image preview
function previewProfileImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imagePreview').src = e.target.result;
            document.getElementById('imagePreviewContainer').style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<!-- Upload Profile Image Modal -->
<div class="modal fade" id="uploadImageModal" tabindex="-1" role="dialog" aria-labelledby="uploadImageModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadImageModalLabel">
                    <i class="fas fa-camera"></i> Upload Profile Image
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= site_url('personnel/upload_profile_image') ?>" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="personnel_id" value="<?= $personnel->id ?>">
                    
                    <div class="form-group">
                        <label for="profile_image">Select Image</label>
                        <input type="file" class="form-control-file" id="profile_image" name="profile_image" accept="image/jpeg,image/jpg,image/png,image/gif" onchange="previewProfileImage(this)" required>
                        <small class="form-text text-muted">
                            Allowed formats: JPG, JPEG, PNG, GIF. Max size: 2MB. Max dimensions: 2000x2000px.
                        </small>
                    </div>
                    
                    <div id="imagePreviewContainer" style="display: none; text-align: center; margin-top: 15px;">
                        <p><strong>Preview:</strong></p>
                        <img id="imagePreview" src="" alt="Preview" style="max-width: 100%; max-height: 300px; border-radius: 10px; border: 2px solid #ddd;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Upload Image
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
