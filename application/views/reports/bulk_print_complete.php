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
            <a href="<?= site_url('reports/schedule_compliance') ?>">Schedule Compliance</a>
        </li>
        <li class="separator">
            <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
            <a href="javascript:void(0)">Bulk Print Complete</a>
        </li>
    </ul>
</div>

<!-- Back Button and Info -->
<div class="row mb-3">
    <div class="col-md-12">
        <a href="<?= site_url('reports/schedule_compliance?start_date=' . $start_date . '&end_date=' . $end_date . ($department_id ? '&department_id=' . $department_id : '')) ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i>Back to Schedule Compliance
        </a>
    </div>
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
                <form method="get" action="<?= site_url('reports/schedule_compliance/bulk_print_complete') ?>" id="filterForm">
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
                    <a href="<?= site_url('reports/schedule_compliance/bulk_print_complete?start_date=' . date('Y-m-01') . '&end_date=' . date('Y-m-t') . ($department_id ? '&department_id=' . $department_id : '')) ?>" class="btn btn-sm btn-outline-primary ml-2">
                        <i class="fas fa-calendar"></i> Current Month
                    </a>
                    <a href="<?= site_url('reports/schedule_compliance/bulk_print_complete?start_date=' . date('Y-m-01', strtotime('-1 month')) . '&end_date=' . date('Y-m-t', strtotime('-1 month')) . ($department_id ? '&department_id=' . $department_id : '')) ?>" class="btn btn-sm btn-outline-secondary ml-2">
                        <i class="fas fa-calendar-minus"></i> Last Month
                    </a>
                    <a href="<?= site_url('reports/schedule_compliance/bulk_print_complete?start_date=' . date('Y-01-01') . '&end_date=' . date('Y-12-31') . ($department_id ? '&department_id=' . $department_id : '')) ?>" class="btn btn-sm btn-outline-info ml-2">
                        <i class="fas fa-calendar-alt"></i> Current Year
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row">
    <div class="col-sm-6 col-md-4">
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
                            <p class="card-category">Complete Schedule Personnel</p>
                            <h4 class="card-title"><?= $total_complete ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-4">
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
                            <h4 class="card-title"><?= $total_employees ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-4">
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
                            <p class="card-category">Completion Rate</p>
                            <h4 class="card-title"><?= $total_employees > 0 ? round(($total_complete / $total_employees) * 100, 1) : 0 ?>%</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Department Summary -->
<?php if (!empty($department_summary)): ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-building mr-2"></i>Department Summary</div>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($department_summary as $dept): ?>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="d-flex align-items-center p-3 border rounded">
                                <div class="mr-3">
                                    <span class="badge badge-lg" style="background-color: <?= $dept['color'] ?>; color: white; font-size: 14px; padding: 8px 12px;">
                                        <?= $dept['count'] ?>
                                    </span>
                                </div>
                                <div>
                                    <strong><?= $dept['name'] ?></strong>
                                    <br><small class="text-muted">Complete Personnel</small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Complete Personnel Table -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-head-row">
                    <div class="card-title">
                        <i class="fas fa-check-circle mr-2 text-success"></i>Complete Schedule Personnel
                        <span class="badge badge-success ml-2"><?= $total_complete ?> personnel</span>
                    </div>
                    <div class="card-tools">
                        <a href="<?= site_url('reports/schedule_compliance/bulk_print_complete_print?start_date=' . $start_date . '&end_date=' . $end_date . ($department_id ? '&department_id=' . $department_id : '')) ?>" 
                           class="btn btn-success mr-2" target="_blank">
                            <i class="fas fa-list mr-1"></i>Print List
                        </a>
                        <a href="<?= site_url('reports/schedule_compliance/bulk_print_dtr?start_date=' . $start_date . '&end_date=' . $end_date . ($department_id ? '&department_id=' . $department_id : '')) ?>" 
                           class="btn btn-primary btn-lg" target="_blank" id="bulkPrintDtrBtn">
                            <i class="fas fa-print mr-1"></i>Bulk Print DTRs (<?= $total_complete ?>)
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Info Alert -->
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Complete Schedule Personnel:</strong> This list shows all employees with <strong>100% compliance rate</strong> - 
                    meaning they have all 4 clock entries (AM IN, AM OUT, PM IN, PM OUT) for every working day in the selected period.
                    <br><strong>Period:</strong> <?= date('F d, Y', strtotime($start_date)) ?> - <?= date('F d, Y', strtotime($end_date)) ?>
                    <?php if ($department_id): ?>
                        | <strong>Department:</strong> <?= $department_name ?>
                    <?php endif; ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <?php if (empty($complete_personnel)): ?>
                    <div class="alert alert-warning text-center">
                        <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                        <h5>No Complete Schedule Personnel Found</h5>
                        <p class="mb-0">No employees have 100% complete schedules for the selected period and department filter.</p>
                    </div>
                <?php else: ?>
                    <!-- Select All Checkbox -->
                    <div class="mb-3">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="selectAll" checked>
                            <label class="custom-control-label" for="selectAll">
                                <strong>Select All Personnel for Printing</strong>
                            </label>
                        </div>
                    </div>

                    <!-- Main Data Table -->
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="completePersonnelTable">
                            <thead class="thead-light">
                                <tr>
                                    <th width="5%" class="text-center">
                                        <i class="fas fa-check"></i>
                                    </th>
                                    <th width="5%" class="text-center">#</th>
                                    <th width="25%">Employee Name</th>
                                    <th width="15%">Department</th>
                                    <th width="15%">Position</th>
                                    <th width="10%" class="text-center">Working Days</th>
                                    <th width="10%" class="text-center">Complete Days</th>
                                    <th width="15%" class="text-center">Compliance Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $num = 1; foreach ($complete_personnel as $emp): ?>
                                    <tr class="personnel-row" data-bio-id="<?= $emp['bio_id'] ?>">
                                        <td class="text-center">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input personnel-checkbox" 
                                                       id="personnel_<?= $emp['bio_id'] ?>" 
                                                       value="<?= $emp['bio_id'] ?>" checked>
                                                <label class="custom-control-label" for="personnel_<?= $emp['bio_id'] ?>"></label>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($num <= 3): ?>
                                                <span class="badge badge-<?= $num == 1 ? 'warning' : ($num == 2 ? 'secondary' : 'info') ?>">
                                                    <i class="fas fa-trophy"></i> <?= $num ?>
                                                </span>
                                            <?php else: ?>
                                                <?= $num ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?= $emp['name'] ?></strong>
                                            <br><small class="text-muted">Bio ID: <?= $emp['bio_id'] ?></small>
                                        </td>
                                        <td>
                                            <span class="badge" style="background-color: <?= $emp['department_color'] ?>; color: white;">
                                                <?= $emp['department_name'] ?>
                                            </span>
                                        </td>
                                        <td><?= $emp['position'] ?: '<span class="text-muted">No position</span>' ?></td>
                                        <td class="text-center">
                                            <span class="badge badge-primary"><?= $emp['working_days'] ?></span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-success"><?= $emp['complete_days'] ?></span>
                                        </td>
                                        <td class="text-center">
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar bg-success" role="progressbar" 
                                                     style="width: 100%;" 
                                                     aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                                                    100%
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php $num++; endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Print Actions -->
                    <div class="mt-4 text-center">
                        <div class="btn-group" role="group">
                            <a href="<?= site_url('reports/schedule_compliance/bulk_print_complete_print?start_date=' . $start_date . '&end_date=' . $end_date . ($department_id ? '&department_id=' . $department_id : '')) ?>" 
                               class="btn btn-success btn-lg" target="_blank">
                                <i class="fas fa-list mr-2"></i>Print Personnel List
                            </a>
                            <a href="<?= site_url('reports/schedule_compliance/bulk_print_dtr?start_date=' . $start_date . '&end_date=' . $end_date . ($department_id ? '&department_id=' . $department_id : '')) ?>" 
                               class="btn btn-primary btn-lg" target="_blank">
                                <i class="fas fa-print mr-2"></i>Bulk Print DTRs (<?= $total_complete ?>)
                            </a>
                        </div>
                        <p class="text-muted mt-2">
                            <i class="fas fa-info-circle mr-1"></i>
                            <strong>Print Personnel List:</strong> Opens a summary list of all <?= $total_complete ?> complete schedule personnel.<br>
                            <strong>Bulk Print DTRs:</strong> Opens the actual DTR forms for all <?= $total_complete ?> personnel ready for printing.
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#completePersonnelTable').DataTable({
        "pageLength": 50,
        "order": [[1, "asc"]],
        "language": {
            "emptyTable": "No complete schedule personnel found for the selected period"
        },
        "columnDefs": [
            { "orderable": false, "targets": [0] }
        ]
    });

    // Select All Checkbox
    $('#selectAll').on('change', function() {
        var isChecked = $(this).is(':checked');
        $('.personnel-checkbox').prop('checked', isChecked);
        updateSelectedCount();
    });

    // Individual Checkbox Change
    $(document).on('change', '.personnel-checkbox', function() {
        var allChecked = $('.personnel-checkbox:checked').length === $('.personnel-checkbox').length;
        $('#selectAll').prop('checked', allChecked);
        updateSelectedCount();
    });

    // Update Selected Count
    function updateSelectedCount() {
        var selectedCount = $('.personnel-checkbox:checked').length;
        var totalCount = $('.personnel-checkbox').length;
        $('#bulkPrintBtn').html('<i class="fas fa-print mr-1"></i>Bulk Print All (' + selectedCount + ')');
    }
});
</script>

<style>
.personnel-row:hover {
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
#completePersonnelTable tbody tr {
    transition: background-color 0.2s ease;
}
.badge-lg {
    font-size: 14px;
    padding: 8px 12px;
}
</style>
