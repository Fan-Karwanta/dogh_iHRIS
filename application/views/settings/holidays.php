<div class="main-panel">
    <div class="content">
        <div class="panel-header bg-primary-gradient">
            <div class="page-inner py-5">
                <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                    <div>
                        <h2 class="text-white pb-2 fw-bold"><i class="fas fa-calendar-alt mr-2"></i>Holiday Management</h2>
                        <h5 class="text-white op-7 mb-2">Manage holidays and events that are considered no duty days</h5>
                    </div>
                    <div class="ml-md-auto py-2 py-md-0">
                        <button class="btn btn-white btn-round mr-2" data-toggle="modal" data-target="#addHolidayModal">
                            <i class="fas fa-plus mr-1"></i> Add Holiday
                        </button>
                        <button class="btn btn-secondary btn-round" data-toggle="modal" data-target="#duplicateYearModal">
                            <i class="fas fa-copy mr-1"></i> Duplicate Year
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="page-inner mt--5">
            <?php if ($this->session->flashdata('message')) : ?>
                <div class="alert alert-<?= $this->session->flashdata('success') == 'success' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
                    <?= $this->session->flashdata('message') ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                <h4 class="card-title">Holidays for <?= $selected_year ?></h4>
                                <div class="ml-auto">
                                    <form method="get" class="form-inline">
                                        <label class="mr-2">Year:</label>
                                        <select name="year" class="form-control form-control-sm" onchange="this.form.submit()">
                                            <?php 
                                            $current_year = date('Y');
                                            for ($y = $current_year - 2; $y <= $current_year + 2; $y++) : 
                                            ?>
                                                <option value="<?= $y ?>" <?= $y == $selected_year ? 'selected' : '' ?>><?= $y ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <style>
                                #holidaysTable th, #holidaysTable td { vertical-align: middle; }
                                #holidaysTable .date-col { width: 100px; white-space: nowrap; }
                                #holidaysTable .name-col { min-width: 200px; max-width: 280px; }
                                #holidaysTable .type-col { width: 80px; text-align: center; }
                                #holidaysTable .recurring-col { width: 80px; text-align: center; }
                                #holidaysTable .applies-col { width: 130px; text-align: center; }
                                #holidaysTable .status-col { width: 100px; }
                                #holidaysTable .actions-col { width: 100px; white-space: nowrap; }
                                #holidaysTable .description-text { 
                                    display: block; 
                                    max-width: 250px; 
                                    overflow: hidden; 
                                    text-overflow: ellipsis; 
                                    white-space: nowrap;
                                }
                            </style>
                            <div class="table-responsive">
                                <table id="holidaysTable" class="display table table-striped table-hover" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th class="date-col">Date</th>
                                            <th class="name-col">Holiday Name</th>
                                            <th class="type-col">Type</th>
                                            <th class="recurring-col">Recurring</th>
                                            <th class="applies-col">Applies To</th>
                                            <th class="status-col">Status</th>
                                            <th class="actions-col text-center">Actions</th>
                                            <th style="display:none;">Sort Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($holidays)) : ?>
                                            <?php foreach ($holidays as $holiday) : ?>
                                                <tr>
                                                    <td class="date-col">
                                                        <?php if ($holiday->recurring) : ?>
                                                            <strong><?= date('M d', strtotime($holiday->date)) ?></strong>
                                                            <br><small class="text-success"><i class="fas fa-sync-alt"></i> Yearly</small>
                                                        <?php else : ?>
                                                            <strong><?= date('M d, Y', strtotime($holiday->date)) ?></strong>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="name-col">
                                                        <strong><?= htmlspecialchars($holiday->name) ?></strong>
                                                        <?php if (!empty($holiday->description)) : ?>
                                                            <br><small class="text-muted description-text" title="<?= htmlspecialchars($holiday->description) ?>"><?= htmlspecialchars($holiday->description) ?></small>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="type-col text-center">
                                                        <?php if ($holiday->holiday_type == 'fixed') : ?>
                                                            <span class="badge badge-info">Fixed</span>
                                                        <?php else : ?>
                                                            <span class="badge badge-warning">Variable</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="recurring-col text-center">
                                                        <?php if ($holiday->recurring) : ?>
                                                            <span class="badge badge-success">Yes</span>
                                                        <?php else : ?>
                                                            <span class="badge badge-secondary">No</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="applies-col text-center">
                                                        <?php if ($holiday->applies_to_all) : ?>
                                                            <span class="badge badge-primary">All Depts</span>
                                                        <?php else : ?>
                                                            <?php 
                                                            $dept_ids = !empty($holiday->department_ids) ? explode(',', $holiday->department_ids) : array();
                                                            $dept_count = count($dept_ids);
                                                            ?>
                                                            <span class="badge badge-warning"><?= $dept_count ?> Dept<?= $dept_count != 1 ? 's' : '' ?></span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="status-col">
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" class="custom-control-input status-toggle" 
                                                                   id="status_<?= $holiday->id ?>" 
                                                                   data-id="<?= $holiday->id ?>"
                                                                   <?= $holiday->status ? 'checked' : '' ?>>
                                                            <label class="custom-control-label" for="status_<?= $holiday->id ?>">
                                                                <?= $holiday->status ? 'Active' : 'Inactive' ?>
                                                            </label>
                                                        </div>
                                                    </td>
                                                    <td class="actions-col text-center">
                                                        <button class="btn btn-xs btn-primary btn-edit" data-id="<?= $holiday->id ?>" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-xs btn-danger btn-delete" data-id="<?= $holiday->id ?>" data-name="<?= htmlspecialchars($holiday->name) ?>" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                    <td style="display:none;"><?= date('m-d', strtotime($holiday->date)) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else : ?>
                                            <tr>
                                                <td colspan="8" class="text-center text-muted py-4">
                                                    <i class="fas fa-calendar-times fa-3x mb-3 d-block"></i>
                                                    <p>No holidays found for <?= $selected_year ?>.</p>
                                                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addHolidayModal">
                                                        <i class="fas fa-plus mr-1"></i> Add Holiday
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Holiday Statistics -->
            <div class="row">
                <div class="col-md-4">
                    <div class="card card-stats card-round">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-icon">
                                    <div class="icon-big text-center icon-primary bubble-shadow-small">
                                        <i class="fas fa-calendar-check"></i>
                                    </div>
                                </div>
                                <div class="col col-stats ml-3 ml-sm-0">
                                    <div class="numbers">
                                        <p class="card-category">Total Holidays</p>
                                        <h4 class="card-title"><?= count($holidays) ?></h4>
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
                                    <div class="icon-big text-center icon-success bubble-shadow-small">
                                        <i class="fas fa-sync-alt"></i>
                                    </div>
                                </div>
                                <div class="col col-stats ml-3 ml-sm-0">
                                    <div class="numbers">
                                        <p class="card-category">Recurring</p>
                                        <h4 class="card-title"><?= count(array_filter($holidays, function($h) { return $h->recurring == 1; })) ?></h4>
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
                                        <i class="fas fa-building"></i>
                                    </div>
                                </div>
                                <div class="col col-stats ml-3 ml-sm-0">
                                    <div class="numbers">
                                        <p class="card-category">Department-Specific</p>
                                        <h4 class="card-title"><?= count(array_filter($holidays, function($h) { return $h->applies_to_all == 0; })) ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Holiday Modal -->
<div class="modal fade" id="addHolidayModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white"><i class="fas fa-plus-circle mr-2"></i>Add New Holiday</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addHolidayForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="add_name">Holiday Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="add_name" name="name" required placeholder="e.g., New Year's Day">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="add_date">Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="add_date" name="date" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="add_holiday_type">Holiday Type</label>
                                <select class="form-control" id="add_holiday_type" name="holiday_type">
                                    <option value="fixed">Fixed (Same date every year)</option>
                                    <option value="variable">Variable (Changes yearly)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Options</label>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="add_recurring" name="recurring" value="1" checked>
                                    <label class="custom-control-label" for="add_recurring">Recurring (Repeats every year)</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="add_description">Description</label>
                        <textarea class="form-control" id="add_description" name="description" rows="2" placeholder="Optional description"></textarea>
                    </div>
                    <hr>
                    <div class="form-group">
                        <label>Department Application</label>
                        <div class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" id="add_applies_all" name="applies_to_all" value="1" checked>
                            <label class="custom-control-label" for="add_applies_all">
                                <i class="fas fa-users mr-1"></i> Apply to All Departments
                            </label>
                        </div>
                        <div class="custom-control custom-radio mt-2">
                            <input type="radio" class="custom-control-input" id="add_applies_specific" name="applies_to_all" value="0">
                            <label class="custom-control-label" for="add_applies_specific">
                                <i class="fas fa-building mr-1"></i> Apply to Specific Departments Only
                            </label>
                        </div>
                    </div>
                    <div class="form-group department-select-group" style="display: none;">
                        <label>Select Departments</label>
                        <div class="row">
                            <?php foreach ($departments as $dept) : ?>
                                <div class="col-md-4">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input dept-checkbox" 
                                               id="add_dept_<?= $dept->id ?>" name="department_ids[]" value="<?= $dept->id ?>">
                                        <label class="custom-control-label" for="add_dept_<?= $dept->id ?>">
                                            <span class="badge" style="background-color: <?= $dept->color ?>; color: white;"><?= $dept->code ?></span>
                                            <?= $dept->name ?>
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Save Holiday</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Holiday Modal -->
<div class="modal fade" id="editHolidayModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white"><i class="fas fa-edit mr-2"></i>Edit Holiday</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editHolidayForm">
                <input type="hidden" id="edit_id" name="id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="edit_name">Holiday Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_name" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_date">Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="edit_date" name="date" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_holiday_type">Holiday Type</label>
                                <select class="form-control" id="edit_holiday_type" name="holiday_type">
                                    <option value="fixed">Fixed (Same date every year)</option>
                                    <option value="variable">Variable (Changes yearly)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Options</label>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="edit_recurring" name="recurring" value="1">
                                    <label class="custom-control-label" for="edit_recurring">Recurring (Repeats every year)</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="edit_description">Description</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="2"></textarea>
                    </div>
                    <hr>
                    <div class="form-group">
                        <label>Department Application</label>
                        <div class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" id="edit_applies_all" name="applies_to_all" value="1">
                            <label class="custom-control-label" for="edit_applies_all">
                                <i class="fas fa-users mr-1"></i> Apply to All Departments
                            </label>
                        </div>
                        <div class="custom-control custom-radio mt-2">
                            <input type="radio" class="custom-control-input" id="edit_applies_specific" name="applies_to_all" value="0">
                            <label class="custom-control-label" for="edit_applies_specific">
                                <i class="fas fa-building mr-1"></i> Apply to Specific Departments Only
                            </label>
                        </div>
                    </div>
                    <div class="form-group edit-department-select-group" style="display: none;">
                        <label>Select Departments</label>
                        <div class="row">
                            <?php foreach ($departments as $dept) : ?>
                                <div class="col-md-4">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input edit-dept-checkbox" 
                                               id="edit_dept_<?= $dept->id ?>" name="department_ids[]" value="<?= $dept->id ?>">
                                        <label class="custom-control-label" for="edit_dept_<?= $dept->id ?>">
                                            <span class="badge" style="background-color: <?= $dept->color ?>; color: white;"><?= $dept->code ?></span>
                                            <?= $dept->name ?>
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Update Holiday</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Duplicate Year Modal -->
<div class="modal fade" id="duplicateYearModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-secondary">
                <h5 class="modal-title text-white"><i class="fas fa-copy mr-2"></i>Duplicate Holidays for New Year</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="duplicateYearForm">
                <div class="modal-body">
                    <p class="text-muted">This will copy all recurring holidays from the source year to the target year.</p>
                    <div class="form-group">
                        <label for="source_year">Source Year</label>
                        <select class="form-control" id="source_year" name="source_year">
                            <?php for ($y = date('Y') - 2; $y <= date('Y') + 1; $y++) : ?>
                                <option value="<?= $y ?>" <?= $y == $selected_year ? 'selected' : '' ?>><?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="target_year">Target Year</label>
                        <select class="form-control" id="target_year" name="target_year">
                            <?php for ($y = date('Y'); $y <= date('Y') + 3; $y++) : ?>
                                <option value="<?= $y ?>" <?= $y == (date('Y') + 1) ? 'selected' : '' ?>><?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-copy mr-1"></i> Duplicate</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteHolidayModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white"><i class="fas fa-exclamation-triangle mr-2"></i>Confirm Delete</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the holiday "<strong id="delete_holiday_name"></strong>"?</p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="delete_holiday_id">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn"><i class="fas fa-trash mr-1"></i> Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
// Wait for jQuery to be available
(function checkJQuery() {
    if (typeof jQuery === 'undefined') {
        setTimeout(checkJQuery, 50);
        return;
    }
    
    jQuery(document).ready(function($) {
        // Initialize DataTable
        if ($.fn.DataTable && $.fn.DataTable.isDataTable('#holidaysTable')) {
            $('#holidaysTable').DataTable().destroy();
        }
        
        if ($.fn.DataTable) {
            $('#holidaysTable').DataTable({
                "pageLength": 25,
                "order": [[7, "asc"]], // Order by hidden sort date column (index 7)
                "columnDefs": [
                    { "orderable": false, "targets": [6] }, // Actions column not sortable
                    { "visible": false, "targets": [7] } // Hide sort date column
                ]
            });
        }

    // Toggle department selection visibility for Add form
    $('input[name="applies_to_all"]').change(function() {
        if ($(this).val() == '0') {
            $('.department-select-group').slideDown();
        } else {
            $('.department-select-group').slideUp();
        }
    });

    // Toggle department selection visibility for Edit form
    $('#editHolidayModal input[name="applies_to_all"]').change(function() {
        if ($(this).val() == '0') {
            $('.edit-department-select-group').slideDown();
        } else {
            $('.edit-department-select-group').slideUp();
        }
    });

    // Add Holiday Form Submit
    $('#addHolidayForm').submit(function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        
        $.ajax({
            url: '<?= site_url('settings/create_holiday') ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#addHolidayModal').modal('hide');
                    showNotification('success', response.message);
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    showNotification('danger', response.message);
                }
            },
            error: function() {
                showNotification('danger', 'An error occurred. Please try again.');
            }
        });
    });

    // Edit button click
    $('.btn-edit').click(function() {
        var id = $(this).data('id');
        
        $.ajax({
            url: '<?= site_url('settings/get_holiday') ?>',
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var holiday = response.data;
                    $('#edit_id').val(holiday.id);
                    $('#edit_name').val(holiday.name);
                    $('#edit_date').val(holiday.date);
                    $('#edit_holiday_type').val(holiday.holiday_type);
                    $('#edit_recurring').prop('checked', holiday.recurring == 1);
                    $('#edit_description').val(holiday.description);
                    
                    if (holiday.applies_to_all == 1) {
                        $('#edit_applies_all').prop('checked', true);
                        $('.edit-department-select-group').hide();
                    } else {
                        $('#edit_applies_specific').prop('checked', true);
                        $('.edit-department-select-group').show();
                    }
                    
                    // Reset and set department checkboxes
                    $('.edit-dept-checkbox').prop('checked', false);
                    if (holiday.department_ids) {
                        var deptIds = holiday.department_ids.split(',');
                        deptIds.forEach(function(deptId) {
                            $('#edit_dept_' + deptId).prop('checked', true);
                        });
                    }
                    
                    $('#editHolidayModal').modal('show');
                } else {
                    showNotification('danger', response.message);
                }
            }
        });
    });

    // Edit Holiday Form Submit
    $('#editHolidayForm').submit(function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        
        $.ajax({
            url: '<?= site_url('settings/update_holiday') ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#editHolidayModal').modal('hide');
                    showNotification('success', response.message);
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    showNotification('danger', response.message);
                }
            },
            error: function() {
                showNotification('danger', 'An error occurred. Please try again.');
            }
        });
    });

    // Delete button click
    $('.btn-delete').click(function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        
        $('#delete_holiday_id').val(id);
        $('#delete_holiday_name').text(name);
        $('#deleteHolidayModal').modal('show');
    });

    // Confirm delete
    $('#confirmDeleteBtn').click(function() {
        var id = $('#delete_holiday_id').val();
        
        $.ajax({
            url: '<?= site_url('settings/delete_holiday') ?>',
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#deleteHolidayModal').modal('hide');
                    showNotification('success', response.message);
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    showNotification('danger', response.message);
                }
            },
            error: function() {
                showNotification('danger', 'An error occurred. Please try again.');
            }
        });
    });

    // Status toggle
    $('.status-toggle').change(function() {
        var id = $(this).data('id');
        var status = $(this).is(':checked') ? 1 : 0;
        var label = $(this).next('label');
        
        $.ajax({
            url: '<?= site_url('settings/toggle_holiday_status') ?>',
            type: 'POST',
            data: { id: id, status: status },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    label.text(status ? 'Active' : 'Inactive');
                    showNotification('success', response.message);
                } else {
                    showNotification('danger', response.message);
                }
            }
        });
    });

    // Duplicate Year Form Submit
    $('#duplicateYearForm').submit(function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        
        $.ajax({
            url: '<?= site_url('settings/duplicate_holidays_for_year') ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#duplicateYearModal').modal('hide');
                    showNotification('success', response.message);
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    showNotification('danger', response.message);
                }
            },
            error: function() {
                showNotification('danger', 'An error occurred. Please try again.');
            }
        });
    });

    // Notification helper function
    function showNotification(type, message) {
        if ($.notify) {
            $.notify({
                icon: type == 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle',
                message: message
            }, {
                type: type,
                placement: {
                    from: 'top',
                    align: 'right'
                },
                delay: 3000
            });
        } else {
            alert(message);
        }
    }
    }); // End jQuery document ready
})(); // End checkJQuery
</script>
