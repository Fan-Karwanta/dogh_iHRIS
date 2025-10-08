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
            <a href="javascript:void(0)">Failure to Clock In/Out</a>
        </li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-head-row">
                    <div class="card-title">Summary of Failure to Clock In/Out</div>
                </div>
            </div>
            <div class="card-body">
                <!-- Date Range Filter -->
                <form method="get" action="<?= site_url('biometrics/failure_summary') ?>" class="mb-4">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="start_date">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" value="<?= $start_date ?>" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="end_date">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" value="<?= $end_date ?>" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Quick Date Filters -->
                <div class="mb-3">
                    <strong>Quick Filters:</strong>
                    <a href="<?= site_url('biometrics/failure_summary?start_date=' . date('Y-m-01') . '&end_date=' . date('Y-m-t')) ?>" class="btn btn-sm btn-outline-primary ml-2">
                        <i class="fas fa-calendar"></i> Current Month
                    </a>
                    <a href="<?= site_url('biometrics/failure_summary?start_date=' . date('Y-m-01', strtotime('-1 month')) . '&end_date=' . date('Y-m-t', strtotime('-1 month'))) ?>" class="btn btn-sm btn-outline-secondary ml-2">
                        <i class="fas fa-calendar-minus"></i> Last Month
                    </a>
                    <a href="<?= site_url('biometrics/failure_summary?start_date=' . date('Y-01-01') . '&end_date=' . date('Y-12-31')) ?>" class="btn btn-sm btn-outline-info ml-2">
                        <i class="fas fa-calendar-alt"></i> Current Year
                    </a>
                </div>

                <hr>

                <!-- Summary Information -->
                <div class="alert alert-info">
                    <strong><i class="fas fa-info-circle"></i> Note:</strong> This report shows employees who failed to clock in or out on workdays where they have at least one time entry. 
                    Full-day absences (no entries), weekends, and holidays are excluded.
                </div>

                <?php if (empty($failure_data)) : ?>
                    <div class="alert alert-success text-center">
                        <i class="fas fa-check-circle fa-3x mb-3"></i>
                        <h5>No Failures Found!</h5>
                        <p>All employees with attendance records have complete clock in/out entries for the selected period.</p>
                    </div>
                <?php else : ?>
                    <!-- Statistics Summary -->
                    <?php 
                    $total_incidents = 0;
                    $total_employees_affected = array();
                    foreach ($failure_data as $date => $employees) {
                        foreach ($employees as $emp) {
                            $total_incidents += count($emp['failures']);
                            $total_employees_affected[$emp['bio_id']] = true;
                        }
                    }
                    ?>
                    <div class="row mb-4">
                        <div class="col-md-4">
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
                                                <p class="card-category">Total Incidents</p>
                                                <h4 class="card-title"><?= $total_incidents ?></h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card card-stats card-round">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-warning bubble-shadow-small">
                                                <i class="fas fa-users"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ml-3 ml-sm-0">
                                            <div class="numbers">
                                                <p class="card-category">Employees Affected</p>
                                                <h4 class="card-title"><?= count($total_employees_affected) ?></h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card card-stats card-round">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-info bubble-shadow-small">
                                                <i class="fas fa-calendar-day"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ml-3 ml-sm-0">
                                            <div class="numbers">
                                                <p class="card-category">Days with Failures</p>
                                                <h4 class="card-title"><?= count($failure_data) ?></h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Failure List Grouped by Date -->
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="failureTable">
                            <thead class="thead-light">
                                <tr>
                                    <th width="12%">Date</th>
                                    <th width="25%">Employee Name</th>
                                    <th width="10%" class="text-center">AM IN</th>
                                    <th width="10%" class="text-center">AM OUT</th>
                                    <th width="10%" class="text-center">PM IN</th>
                                    <th width="10%" class="text-center">PM OUT</th>
                                    <th width="23%">Missing Entries</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($failure_data as $date => $employees) : ?>
                                    <?php 
                                    $date_display = date('M d, Y (D)', strtotime($date));
                                    $employee_count = count($employees);
                                    ?>
                                    <?php foreach ($employees as $index => $emp) : ?>
                                        <tr>
                                            <?php if ($index === 0) : ?>
                                                <td rowspan="<?= $employee_count ?>" class="align-middle bg-light">
                                                    <strong><?= $date_display ?></strong>
                                                    <br>
                                                    <small class="text-muted"><?= $employee_count ?> employee(s)</small>
                                                </td>
                                            <?php endif; ?>
                                            <td><?= $emp['employee_name'] ?></td>
                                            <td class="text-center <?= empty($emp['am_in']) ? 'bg-danger text-white' : '' ?>">
                                                <?= !empty($emp['am_in']) ? date('h:i A', strtotime($emp['am_in'])) : '<i class="fas fa-times"></i>' ?>
                                            </td>
                                            <td class="text-center <?= empty($emp['am_out']) ? 'bg-danger text-white' : '' ?>">
                                                <?= !empty($emp['am_out']) ? date('h:i A', strtotime($emp['am_out'])) : '<i class="fas fa-times"></i>' ?>
                                            </td>
                                            <td class="text-center <?= empty($emp['pm_in']) ? 'bg-danger text-white' : '' ?>">
                                                <?= !empty($emp['pm_in']) ? date('h:i A', strtotime($emp['pm_in'])) : '<i class="fas fa-times"></i>' ?>
                                            </td>
                                            <td class="text-center <?= empty($emp['pm_out']) ? 'bg-danger text-white' : '' ?>">
                                                <?= !empty($emp['pm_out']) ? date('h:i A', strtotime($emp['pm_out'])) : '<i class="fas fa-times"></i>' ?>
                                            </td>
                                            <td>
                                                <?php foreach ($emp['failures'] as $failure) : ?>
                                                    <span class="badge badge-danger mr-1"><?= $failure ?></span>
                                                <?php endforeach; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Export Button -->
                    <div class="mt-3">
                        <button class="btn btn-success" onclick="exportTableToCSV('failure_summary_<?= $start_date ?>_to_<?= $end_date ?>.csv')">
                            <i class="fas fa-file-excel"></i> Export to CSV
                        </button>
                        <button class="btn btn-info" onclick="window.print()">
                            <i class="fas fa-print"></i> Print Report
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for CSV Export -->
<script>
function exportTableToCSV(filename) {
    var csv = [];
    var rows = document.querySelectorAll("#failureTable tr");
    
    for (var i = 0; i < rows.length; i++) {
        var row = [], cols = rows[i].querySelectorAll("td, th");
        
        for (var j = 0; j < cols.length; j++) {
            // Get text content and clean it
            var text = cols[j].innerText.replace(/\n/g, ' ').replace(/,/g, ';');
            row.push('"' + text + '"');
        }
        
        csv.push(row.join(","));
    }

    // Download CSV file
    var csvFile = new Blob([csv.join("\n")], { type: "text/csv" });
    var downloadLink = document.createElement("a");
    downloadLink.download = filename;
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = "none";
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}

// Add DataTable functionality if available
$(document).ready(function() {
    if ($.fn.DataTable) {
        $('#failureTable').DataTable({
            "pageLength": 25,
            "order": [[0, "desc"]],
            "language": {
                "emptyTable": "No failures found for the selected period"
            }
        });
    }
});
</script>

<!-- Print Styles -->
<style>
@media print {
    .sidebar, .navbar, .page-header .breadcrumbs, .card-header, .btn, .alert-info {
        display: none !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    .table {
        font-size: 10px;
    }
}
</style>
