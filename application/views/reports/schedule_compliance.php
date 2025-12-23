<div class="page-header">
    <h4 class="page-title"><?= $title ?></h4>
    <ul class="breadcrumbs">
        <li class="nav-home">
            <a href="<?= site_url('admin/dashboard') ?>">
                <i class="fas fa-home"></i>
            </a>
        </li>
        <li class="separator">
            <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
            <a href="javascript:void(0)">Reports</a>
        </li>
        <li class="separator">
            <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
            <a href="javascript:void(0)">Schedule Compliance</a>
        </li>
    </ul>
</div>

<!-- Filter Section -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-head-row">
                    <div class="card-title">
                        <i class="fas fa-filter mr-2"></i>Filter Options
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form method="get" action="<?= site_url('reports/schedule_compliance') ?>" id="filterForm">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="start_date"><i class="fas fa-calendar mr-1"></i>Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" value="<?= $start_date ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="end_date"><i class="fas fa-calendar mr-1"></i>End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" value="<?= $end_date ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="department_id"><i class="fas fa-building mr-1"></i>Department</label>
                                <select class="form-control" id="department_id" name="department_id">
                                    <option value="">All Departments</option>
                                    <?php foreach ($departments as $dept): ?>
                                        <option value="<?= $dept->id ?>" <?= $department_id == $dept->id ? 'selected' : '' ?>>
                                            <?= $dept->name ?> (<?= $dept->personnel_count ?> personnel)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-search mr-1"></i>Apply Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
                
                <!-- Quick Date Filters -->
                <div class="mt-2">
                    <strong><i class="fas fa-clock mr-1"></i>Quick Filters:</strong>
                    <a href="<?= site_url('reports/schedule_compliance?start_date=' . date('Y-m-01') . '&end_date=' . date('Y-m-t') . ($department_id ? '&department_id=' . $department_id : '')) ?>" class="btn btn-sm btn-outline-primary ml-2">
                        <i class="fas fa-calendar"></i> Current Month
                    </a>
                    <a href="<?= site_url('reports/schedule_compliance?start_date=' . date('Y-m-01', strtotime('-1 month')) . '&end_date=' . date('Y-m-t', strtotime('-1 month')) . ($department_id ? '&department_id=' . $department_id : '')) ?>" class="btn btn-sm btn-outline-secondary ml-2">
                        <i class="fas fa-calendar-minus"></i> Last Month
                    </a>
                    <a href="<?= site_url('reports/schedule_compliance?start_date=' . date('Y-01-01') . '&end_date=' . date('Y-12-31') . ($department_id ? '&department_id=' . $department_id : '')) ?>" class="btn btn-sm btn-outline-info ml-2">
                        <i class="fas fa-calendar-alt"></i> Current Year
                    </a>
                    <a href="<?= site_url('reports/schedule_compliance?start_date=' . date('Y-m-d', strtotime('-7 days')) . '&end_date=' . date('Y-m-d') . ($department_id ? '&department_id=' . $department_id : '')) ?>" class="btn btn-sm btn-outline-warning ml-2">
                        <i class="fas fa-calendar-week"></i> Last 7 Days
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row">
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-success bubble-shadow-small">
                            <i class="fas fa-trophy"></i>
                        </div>
                    </div>
                    <div class="col col-stats ml-3 ml-sm-0">
                        <div class="numbers">
                            <p class="card-category">Perfect Attendance</p>
                            <h4 class="card-title"><?= $overall_stats['perfect_employees'] ?></h4>
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
                        <div class="icon-big text-center icon-primary bubble-shadow-small">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    <div class="col col-stats ml-3 ml-sm-0">
                        <div class="numbers">
                            <p class="card-category">Total Employees</p>
                            <h4 class="card-title"><?= $overall_stats['total_employees'] ?></h4>
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
                            <i class="fas fa-percentage"></i>
                        </div>
                    </div>
                    <div class="col col-stats ml-3 ml-sm-0">
                        <div class="numbers">
                            <p class="card-category">Avg. Compliance</p>
                            <h4 class="card-title"><?= $overall_stats['average_compliance'] ?>%</h4>
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
                        <div class="icon-big text-center icon-danger bubble-shadow-small">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                    <div class="col col-stats ml-3 ml-sm-0">
                        <div class="numbers">
                            <p class="card-category">Missing Entries</p>
                            <h4 class="card-title"><?= number_format($overall_stats['total_missing_entries']) ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row">
    <!-- Department Compliance Chart -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-chart-bar mr-2"></i>Department Compliance Overview</div>
            </div>
            <div class="card-body">
                <div class="chart-container" style="min-height: 300px;">
                    <canvas id="departmentChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Top Performers -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <div class="card-head-row">
                    <div class="card-title"><i class="fas fa-medal mr-2 text-warning"></i>Top Performers (100% Compliance)</div>
                    <span class="badge badge-success"><?= count($top_performers) ?> employees</span>
                </div>
            </div>
            <div class="card-body" style="max-height: 350px; overflow-y: auto;">
                <?php if (empty($top_performers)): ?>
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle fa-2x mb-2"></i>
                        <p class="mb-0">No employees with 100% compliance in this period.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th width="10%">#</th>
                                    <th>Employee</th>
                                    <th>Department</th>
                                    <th class="text-center">Days</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $rank = 1; foreach ($top_performers as $emp): ?>
                                    <tr>
                                        <td>
                                            <?php if ($rank <= 3): ?>
                                                <span class="badge badge-<?= $rank == 1 ? 'warning' : ($rank == 2 ? 'secondary' : 'info') ?>">
                                                    <i class="fas fa-trophy"></i> <?= $rank ?>
                                                </span>
                                            <?php else: ?>
                                                <?= $rank ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?= $emp['name'] ?></strong>
                                            <br><small class="text-muted"><?= $emp['position'] ?></small>
                                        </td>
                                        <td>
                                            <span class="badge" style="background-color: <?= $emp['department_color'] ?>; color: white;">
                                                <?= $emp['department_name'] ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-success"><?= $emp['complete_days'] ?>/<?= $emp['working_days'] ?></span>
                                        </td>
                                    </tr>
                                <?php $rank++; endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Main Compliance Table -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-head-row">
                    <div class="card-title"><i class="fas fa-table mr-2"></i>Complete Schedule Compliance Report</div>
                    <div class="card-tools">
                        <a href="<?= site_url('reports/schedule_compliance/bulk_print_complete?start_date=' . $start_date . '&end_date=' . $end_date . ($department_id ? '&department_id=' . $department_id : '')) ?>" class="btn btn-warning btn-sm mr-2" title="Preview and Bulk Print Complete Schedule Personnel">
                            <i class="fas fa-users mr-1"></i>Bulk Print Complete
                        </a>
                        <a href="<?= site_url('reports/schedule_compliance/export_csv?start_date=' . $start_date . '&end_date=' . $end_date . ($department_id ? '&department_id=' . $department_id : '')) ?>" class="btn btn-success btn-sm mr-2">
                            <i class="fas fa-file-excel mr-1"></i>Export CSV
                        </a>
                        <a href="<?= site_url('reports/schedule_compliance/print_report?start_date=' . $start_date . '&end_date=' . $end_date . ($department_id ? '&department_id=' . $department_id : '')) ?>" class="btn btn-info btn-sm" target="_blank">
                            <i class="fas fa-print mr-1"></i>Print
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Info Alert -->
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Schedule:</strong> 8:00 AM - 5:00 PM (12:00 PM - 1:00 PM lunch break). 
                    A <strong>complete schedule</strong> requires all 4 clock entries: AM IN, AM OUT, PM IN, PM OUT.
                    Click on any employee row to view their detailed failure report.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <!-- Working Days Calculation Info -->
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-calculator mr-2"></i>
                    <strong>Working Days Calculation:</strong> Working days are now calculated <strong>dynamically</strong> based on actual attendance.
                    A day is counted as a "working day" if the employee has at least <strong>one clock-in entry (AM IN or PM IN)</strong>.
                    This provides more accurate compliance rates per employee.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- Employee Dropdown Filter -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="employeeSelect"><i class="fas fa-user mr-1"></i>Quick Employee Lookup</label>
                            <select class="form-control select2" id="employeeSelect">
                                <option value="">-- Select Employee to View Details --</option>
                                <?php foreach ($compliance_data as $emp): ?>
                                    <option value="<?= $emp['bio_id'] ?>" 
                                            data-name="<?= htmlspecialchars($emp['name']) ?>"
                                            data-department="<?= htmlspecialchars($emp['department_name']) ?>"
                                            data-compliance="<?= $emp['compliance_rate'] ?>"
                                            data-failures="<?= $emp['total_missing_entries'] ?>">
                                        <?= $emp['name'] ?> - <?= $emp['department_name'] ?> (<?= $emp['compliance_rate'] ?>%)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><i class="fas fa-filter mr-1"></i>Filter by Compliance</label>
                            <select class="form-control" id="complianceFilter">
                                <option value="">All Employees</option>
                                <option value="perfect">Perfect (100%)</option>
                                <option value="good">Good (80-99%)</option>
                                <option value="needs_improvement">Needs Improvement (50-79%)</option>
                                <option value="critical">Critical (&lt;50%)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div class="d-flex">
                                <button type="button" class="btn btn-outline-secondary btn-block" id="resetFilters">
                                    <i class="fas fa-undo mr-1"></i>Reset Filters
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Data Table -->
                <div class="table-responsive">
                    <table class="table table-hover table-bordered" id="complianceTable">
                        <thead class="thead-light">
                            <tr>
                                <th width="5%" class="text-center">Rank</th>
                                <th width="20%">Employee Name</th>
                                <th width="12%">Department</th>
                                <th width="8%" class="text-center" title="Days with at least one clock-in entry (AM IN or PM IN)">Working<br>Days <i class="fas fa-info-circle text-info" style="font-size: 10px;"></i></th>
                                <th width="8%" class="text-center">Complete<br>Days</th>
                                <th width="10%" class="text-center">Compliance<br>Rate</th>
                                <th width="8%" class="text-center">Missing<br>AM IN</th>
                                <th width="8%" class="text-center">Missing<br>AM OUT</th>
                                <th width="8%" class="text-center">Missing<br>PM IN</th>
                                <th width="8%" class="text-center">Missing<br>PM OUT</th>
                                <th width="5%" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $rank = 1; foreach ($compliance_data as $emp): ?>
                                <?php
                                    // Determine compliance class
                                    $compliance_class = 'danger';
                                    $compliance_category = 'critical';
                                    if ($emp['compliance_rate'] == 100) {
                                        $compliance_class = 'success';
                                        $compliance_category = 'perfect';
                                    } elseif ($emp['compliance_rate'] >= 80) {
                                        $compliance_class = 'info';
                                        $compliance_category = 'good';
                                    } elseif ($emp['compliance_rate'] >= 50) {
                                        $compliance_class = 'warning';
                                        $compliance_category = 'needs_improvement';
                                    }
                                ?>
                                <tr class="employee-row" 
                                    data-bio-id="<?= $emp['bio_id'] ?>" 
                                    data-compliance="<?= $compliance_category ?>"
                                    style="cursor: pointer;">
                                    <td class="text-center">
                                        <?php if ($rank <= 3 && $emp['compliance_rate'] == 100): ?>
                                            <span class="badge badge-<?= $rank == 1 ? 'warning' : ($rank == 2 ? 'secondary' : 'info') ?>">
                                                <i class="fas fa-trophy"></i> <?= $rank ?>
                                            </span>
                                        <?php else: ?>
                                            <?= $rank ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?= $emp['name'] ?></strong>
                                        <br><small class="text-muted"><?= $emp['position'] ?: 'No position' ?></small>
                                    </td>
                                    <td>
                                        <span class="badge" style="background-color: <?= $emp['department_color'] ?>; color: white;">
                                            <?= $emp['department_name'] ?>
                                        </span>
                                    </td>
                                    <td class="text-center" title="Days with at least one clock-in (based on actual attendance)">
                                        <span class="badge badge-primary"><?= $emp['working_days'] ?></span>
                                        <?php if (isset($emp['calendar_working_days']) && $emp['calendar_working_days'] != $emp['working_days']): ?>
                                        <br><small class="text-muted" title="Calendar working days (excluding weekends/holidays)">(<?= $emp['calendar_working_days'] ?> cal)</small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-<?= $emp['complete_days'] == $emp['working_days'] ? 'success' : 'secondary' ?>">
                                            <?= $emp['complete_days'] ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-<?= $compliance_class ?>" role="progressbar" 
                                                 style="width: <?= $emp['compliance_rate'] ?>%;" 
                                                 aria-valuenow="<?= $emp['compliance_rate'] ?>" aria-valuemin="0" aria-valuemax="100">
                                                <?= $emp['compliance_rate'] ?>%
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center <?= $emp['missing_am_in'] > 0 ? 'bg-danger text-white' : '' ?>">
                                        <?= $emp['missing_am_in'] ?>
                                    </td>
                                    <td class="text-center <?= $emp['missing_am_out'] > 0 ? 'bg-danger text-white' : '' ?>">
                                        <?= $emp['missing_am_out'] ?>
                                    </td>
                                    <td class="text-center <?= $emp['missing_pm_in'] > 0 ? 'bg-danger text-white' : '' ?>">
                                        <?= $emp['missing_pm_in'] ?>
                                    </td>
                                    <td class="text-center <?= $emp['missing_pm_out'] > 0 ? 'bg-danger text-white' : '' ?>">
                                        <?= $emp['missing_pm_out'] ?>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-primary view-details-btn" 
                                                data-bio-id="<?= $emp['bio_id'] ?>"
                                                data-name="<?= htmlspecialchars($emp['name']) ?>"
                                                title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php $rank++; endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Employee Details Modal -->
<div class="modal fade" id="employeeDetailsModal" tabindex="-1" role="dialog" aria-labelledby="employeeDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="employeeDetailsModalLabel">
                    <i class="fas fa-user-clock mr-2"></i>Employee Failure Details
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="employeeDetailsContent">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2">Loading employee details...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize Select2 for employee dropdown
    if ($.fn.select2) {
        $('#employeeSelect').select2({
            placeholder: '-- Select Employee to View Details --',
            allowClear: true,
            width: '100%'
        });
    }

    // Initialize DataTable
    var table = $('#complianceTable').DataTable({
        "pageLength": 25,
        "order": [[5, "desc"]], // Sort by compliance rate
        "language": {
            "emptyTable": "No compliance data found for the selected period"
        },
        "columnDefs": [
            { "orderable": false, "targets": [10] } // Disable sorting on actions column
        ]
    });

    // Department Chart
    var deptData = <?= json_encode($department_summary) ?>;
    var ctx = document.getElementById('departmentChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: deptData.map(d => d.department_name),
            datasets: [{
                label: 'Average Compliance Rate (%)',
                data: deptData.map(d => d.average_compliance),
                backgroundColor: deptData.map(d => d.department_color + 'CC'),
                borderColor: deptData.map(d => d.department_color),
                borderWidth: 2
            }, {
                label: 'Perfect Employees',
                data: deptData.map(d => d.perfect_employees),
                backgroundColor: 'rgba(40, 167, 69, 0.7)',
                borderColor: 'rgba(40, 167, 69, 1)',
                borderWidth: 2,
                type: 'line',
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    title: {
                        display: true,
                        text: 'Compliance Rate (%)'
                    }
                },
                y1: {
                    beginAtZero: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Perfect Employees'
                    },
                    grid: {
                        drawOnChartArea: false
                    }
                }
            }
        }
    });

    // Compliance Filter
    $('#complianceFilter').on('change', function() {
        var filter = $(this).val();
        
        if (filter === '') {
            table.search('').columns().search('').draw();
            $('.employee-row').show();
        } else {
            $('.employee-row').each(function() {
                var rowCompliance = $(this).data('compliance');
                if (filter === rowCompliance) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
            table.draw();
        }
    });

    // Reset Filters
    $('#resetFilters').on('click', function() {
        $('#complianceFilter').val('');
        $('#employeeSelect').val('').trigger('change');
        table.search('').columns().search('').draw();
        $('.employee-row').show();
    });

    // Employee Select Change
    $('#employeeSelect').on('change', function() {
        var bioId = $(this).val();
        if (bioId) {
            showEmployeeDetails(bioId);
        }
    });

    // View Details Button Click
    $(document).on('click', '.view-details-btn', function(e) {
        e.stopPropagation();
        var bioId = $(this).data('bio-id');
        showEmployeeDetails(bioId);
    });

    // Row Click
    $(document).on('click', '.employee-row', function() {
        var bioId = $(this).data('bio-id');
        showEmployeeDetails(bioId);
    });

    // Show Employee Details Modal
    function showEmployeeDetails(bioId) {
        $('#employeeDetailsModal').modal('show');
        $('#employeeDetailsContent').html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <p class="mt-2">Loading employee details...</p>
            </div>
        `);

        $.ajax({
            url: '<?= site_url('reports/schedule_compliance/get_employee_details') ?>',
            type: 'POST',
            data: {
                bio_id: bioId,
                start_date: '<?= $start_date ?>',
                end_date: '<?= $end_date ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data) {
                    renderEmployeeDetails(response.data);
                } else {
                    $('#employeeDetailsContent').html(`
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            Failed to load employee details.
                        </div>
                    `);
                }
            },
            error: function() {
                $('#employeeDetailsContent').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        An error occurred while loading employee details.
                    </div>
                `);
            }
        });
    }

    // Render Employee Details
    function renderEmployeeDetails(data) {
        var metrics = data.metrics;
        var failureDetails = metrics.failure_details || [];
        
        var complianceClass = 'danger';
        if (data.compliance_rate == 100) complianceClass = 'success';
        else if (data.compliance_rate >= 80) complianceClass = 'info';
        else if (data.compliance_rate >= 50) complianceClass = 'warning';

        var html = `
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5><i class="fas fa-user mr-2"></i>${data.name}</h5>
                    <p class="mb-1">
                        <span class="badge" style="background-color: ${data.department_color}; color: white;">
                            ${data.department_name}
                        </span>
                    </p>
                    <p class="text-muted mb-0">${data.personnel.position || 'No position'}</p>
                </div>
                <div class="col-md-6 text-right">
                    <h2 class="text-${complianceClass}">${data.compliance_rate}%</h2>
                    <p class="mb-0">Compliance Rate</p>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-light">
                        <div class="card-body text-center py-3">
                            <h4 class="mb-0">${data.working_days}</h4>
                            <small class="text-muted">Working Days</small>
                            ${data.calendar_working_days && data.calendar_working_days != data.working_days ? 
                                '<br><small class="text-info" title="Calendar working days (excluding weekends/holidays)">(' + data.calendar_working_days + ' calendar)</small>' : ''}
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center py-3">
                            <h4 class="mb-0">${metrics.complete_days}</h4>
                            <small>Complete Days</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning">
                        <div class="card-body text-center py-3">
                            <h4 class="mb-0">${metrics.incomplete_days}</h4>
                            <small>Incomplete Days</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body text-center py-3">
                            <h4 class="mb-0">${metrics.total_missing_entries}</h4>
                            <small>Missing Entries</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-12">
                    <h6><i class="fas fa-chart-pie mr-2"></i>Missing Entries Breakdown</h6>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="d-flex justify-content-between align-items-center p-2 border rounded ${metrics.missing_am_in > 0 ? 'border-danger' : ''}">
                                <span>AM IN</span>
                                <span class="badge badge-${metrics.missing_am_in > 0 ? 'danger' : 'success'}">${metrics.missing_am_in}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex justify-content-between align-items-center p-2 border rounded ${metrics.missing_am_out > 0 ? 'border-danger' : ''}">
                                <span>AM OUT</span>
                                <span class="badge badge-${metrics.missing_am_out > 0 ? 'danger' : 'success'}">${metrics.missing_am_out}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex justify-content-between align-items-center p-2 border rounded ${metrics.missing_pm_in > 0 ? 'border-danger' : ''}">
                                <span>PM IN</span>
                                <span class="badge badge-${metrics.missing_pm_in > 0 ? 'danger' : 'success'}">${metrics.missing_pm_in}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex justify-content-between align-items-center p-2 border rounded ${metrics.missing_pm_out > 0 ? 'border-danger' : ''}">
                                <span>PM OUT</span>
                                <span class="badge badge-${metrics.missing_pm_out > 0 ? 'danger' : 'success'}">${metrics.missing_pm_out}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        if (failureDetails.length > 0) {
            html += `
                <h6><i class="fas fa-list mr-2"></i>Detailed Failure Report</h6>
                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-sm table-bordered table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Date</th>
                                <th class="text-center">AM IN</th>
                                <th class="text-center">AM OUT</th>
                                <th class="text-center">PM IN</th>
                                <th class="text-center">PM OUT</th>
                                <th>Missing</th>
                            </tr>
                        </thead>
                        <tbody>
            `;
            
            failureDetails.forEach(function(detail) {
                html += `
                    <tr>
                        <td><strong>${detail.date_formatted}</strong></td>
                        <td class="text-center ${!detail.am_in ? 'bg-danger text-white' : ''}">
                            ${detail.am_in ? formatTime(detail.am_in) : '<i class="fas fa-times"></i>'}
                        </td>
                        <td class="text-center ${!detail.am_out ? 'bg-danger text-white' : ''}">
                            ${detail.am_out ? formatTime(detail.am_out) : '<i class="fas fa-times"></i>'}
                        </td>
                        <td class="text-center ${!detail.pm_in ? 'bg-danger text-white' : ''}">
                            ${detail.pm_in ? formatTime(detail.pm_in) : '<i class="fas fa-times"></i>'}
                        </td>
                        <td class="text-center ${!detail.pm_out ? 'bg-danger text-white' : ''}">
                            ${detail.pm_out ? formatTime(detail.pm_out) : '<i class="fas fa-times"></i>'}
                        </td>
                        <td>
                            ${detail.failures.map(f => '<span class="badge badge-danger mr-1">' + f + '</span>').join('')}
                        </td>
                    </tr>
                `;
            });
            
            html += `
                        </tbody>
                    </table>
                </div>
            `;
        } else {
            html += `
                <div class="alert alert-success text-center">
                    <i class="fas fa-check-circle fa-2x mb-2"></i>
                    <h5>Perfect Attendance!</h5>
                    <p class="mb-0">This employee has no missing clock entries in the selected period.</p>
                </div>
            `;
        }

        $('#employeeDetailsContent').html(html);
    }

    // Format time helper
    function formatTime(timeStr) {
        if (!timeStr) return '';
        var parts = timeStr.split(':');
        var hours = parseInt(parts[0]);
        var minutes = parts[1];
        var ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12;
        return hours + ':' + minutes + ' ' + ampm;
    }
});
</script>

<style>
.employee-row:hover {
    background-color: #f8f9fa !important;
}
.progress {
    border-radius: 10px;
}
.progress-bar {
    font-size: 11px;
    font-weight: bold;
}
.card-stats .icon-big {
    font-size: 2rem;
}
.select2-container {
    width: 100% !important;
}
#complianceTable tbody tr {
    transition: background-color 0.2s ease;
}
</style>
