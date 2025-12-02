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
            <a href="javascript:void(0)">Settings</a>
        </li>
        <li class="separator">
            <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
            <a href="javascript:void(0)">Departments</a>
        </li>
    </ul>
    <div class="ml-md-auto py-2 py-md-0">
        <button type="button" class="btn btn-primary btn-round btn-sm" data-toggle="modal" data-target="#addDepartmentModal">
            <span class="btn-label">
                <i class="fas fa-plus"></i>
            </span>
            Add Department
        </button>
    </div>
</div>

<!-- Department Statistics Cards -->
<div class="row">
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
                            <p class="card-category">Total Personnel</p>
                            <h4 class="card-title" id="stat-total"><?= isset($statistics['total_personnel']) ? number_format($statistics['total_personnel']) : '0' ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php foreach ($statistics['departments'] as $dept): ?>
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center bubble-shadow-small" style="background-color: <?= $dept->color ?>; color: white;">
                            <i class="<?= $dept->icon ?: 'fas fa-building' ?>"></i>
                        </div>
                    </div>
                    <div class="col col-stats ml-3 ml-sm-0">
                        <div class="numbers">
                            <p class="card-category"><?= htmlspecialchars($dept->name) ?></p>
                            <h4 class="card-title" id="stat-dept-<?= $dept->id ?>"><?= number_format($dept->personnel_count) ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-secondary bubble-shadow-small">
                            <i class="fas fa-user-slash"></i>
                        </div>
                    </div>
                    <div class="col col-stats ml-3 ml-sm-0">
                        <div class="numbers">
                            <p class="card-category">Unassigned</p>
                            <h4 class="card-title" id="stat-unassigned"><?= isset($statistics['unassigned_count']) ? number_format($statistics['unassigned_count']) : '0' ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Search and Filter Bar -->
<div class="row mb-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body py-3">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                            </div>
                            <input type="text" class="form-control" id="searchPersonnel" placeholder="Search personnel by name, email, or position...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-control" id="filterDepartment">
                            <option value="">All Departments</option>
                            <?php foreach ($all_departments as $dept): ?>
                            <option value="<?= $dept->id ?>"><?= htmlspecialchars($dept->name) ?></option>
                            <?php endforeach; ?>
                            <option value="unassigned">Unassigned</option>
                        </select>
                    </div>
                    <div class="col-md-5 text-right">
                        <button type="button" class="btn btn-info btn-sm" id="btnSelectAll">
                            <i class="fas fa-check-square"></i> Select All Visible
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm" id="btnDeselectAll">
                            <i class="fas fa-square"></i> Deselect All
                        </button>
                        <div class="btn-group">
                            <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="btnBulkAssign" disabled>
                                <i class="fas fa-user-plus"></i> Assign Selected (<span id="selectedCount">0</span>)
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <?php foreach ($all_departments as $dept): ?>
                                <a class="dropdown-item bulk-assign-dept" href="javascript:void(0)" data-dept-id="<?= $dept->id ?>">
                                    <i class="<?= $dept->icon ?: 'fas fa-building' ?>" style="color: <?= $dept->color ?>"></i> <?= htmlspecialchars($dept->name) ?>
                                </a>
                                <?php endforeach; ?>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item bulk-assign-dept" href="javascript:void(0)" data-dept-id="unassigned">
                                    <i class="fas fa-user-slash text-secondary"></i> Remove from Department
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Department Columns - Eagle's Eye View -->
<div class="row" id="departmentColumns">
    <?php foreach ($departments_data as $key => $data): ?>
    <div class="col-md-4 col-lg-3 department-column" data-department-id="<?= $key === 'unassigned' ? 'unassigned' : $data['department']->id ?>">
        <div class="card card-round">
            <div class="card-header" style="background-color: <?= $data['department']->color ?>; color: white;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <i class="<?= $data['department']->icon ?>"></i>
                        <span class="ml-2 font-weight-bold"><?= htmlspecialchars($data['department']->name) ?></span>
                        <span class="badge badge-light ml-2"><?= $data['count'] ?></span>
                    </div>
                    <?php if ($key !== 'unassigned'): ?>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-link text-white" type="button" data-toggle="dropdown">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item edit-department" href="javascript:void(0)" 
                               data-id="<?= $data['department']->id ?>"
                               data-name="<?= htmlspecialchars($data['department']->name) ?>"
                               data-description="<?= htmlspecialchars($data['department']->description ?? '') ?>"
                               data-color="<?= $data['department']->color ?>">
                                <i class="fas fa-edit"></i> Edit Department
                            </a>
                            <a class="dropdown-item text-danger delete-department" href="javascript:void(0)" data-id="<?= $data['department']->id ?>">
                                <i class="fas fa-trash"></i> Delete Department
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body personnel-list" style="max-height: 500px; overflow-y: auto;" 
                 data-department-id="<?= $key === 'unassigned' ? 'unassigned' : $data['department']->id ?>">
                <?php if (empty($data['personnel'])): ?>
                <div class="text-center text-muted py-4">
                    <i class="fas fa-user-slash fa-2x mb-2"></i>
                    <p class="mb-0">No personnel assigned</p>
                </div>
                <?php else: ?>
                <?php foreach ($data['personnel'] as $person): ?>
                <div class="personnel-card mb-2 p-2 border rounded" 
                     data-personnel-id="<?= $person->id ?>"
                     draggable="true">
                    <div class="d-flex align-items-center">
                        <div class="custom-control custom-checkbox mr-2">
                            <input type="checkbox" class="custom-control-input personnel-checkbox" 
                                   id="personnel-<?= $person->id ?>" value="<?= $person->id ?>">
                            <label class="custom-control-label" for="personnel-<?= $person->id ?>"></label>
                        </div>
                        <div class="flex-grow-1">
                            <div class="font-weight-bold text-dark" style="font-size: 0.9rem;">
                                <?= htmlspecialchars($person->lastname . ', ' . $person->firstname . ' ' . substr($person->middlename, 0, 1) . '.') ?>
                            </div>
                            <small class="text-muted"><?= htmlspecialchars($person->position) ?></small>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-link text-muted p-0" type="button" data-toggle="dropdown">
                                <i class="fas fa-exchange-alt"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <h6 class="dropdown-header">Move to Department</h6>
                                <?php foreach ($all_departments as $dept): ?>
                                <a class="dropdown-item move-personnel" href="javascript:void(0)" 
                                   data-personnel-id="<?= $person->id ?>" 
                                   data-dept-id="<?= $dept->id ?>">
                                    <i class="<?= $dept->icon ?: 'fas fa-building' ?>" style="color: <?= $dept->color ?>"></i> 
                                    <?= htmlspecialchars($dept->name) ?>
                                </a>
                                <?php endforeach; ?>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item move-personnel" href="javascript:void(0)" 
                                   data-personnel-id="<?= $person->id ?>" 
                                   data-dept-id="unassigned">
                                    <i class="fas fa-user-slash text-secondary"></i> Remove from Department
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Add Department Modal -->
<div class="modal fade" id="addDepartmentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus-circle"></i> Add New Department</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addDepartmentForm">
                    <div class="form-group">
                        <label>Department Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" required placeholder="e.g., Emergency">
                    </div>
                    <div class="form-group">
                        <label>Department Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="code" required placeholder="e.g., EMERG" maxlength="20">
                        <small class="form-text text-muted">Short unique identifier (will be converted to uppercase)</small>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" name="description" rows="2" placeholder="Brief description of the department"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Color</label>
                        <input type="color" class="form-control" name="color" value="#3498db" style="height: 40px;">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btnSaveDepartment">
                    <i class="fas fa-save"></i> Save Department
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Department Modal -->
<div class="modal fade" id="editDepartmentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Department</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editDepartmentForm">
                    <input type="hidden" name="id" id="editDeptId">
                    <div class="form-group">
                        <label>Department Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" id="editDeptName" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" name="description" id="editDeptDescription" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Color</label>
                        <input type="color" class="form-control" name="color" id="editDeptColor" style="height: 40px;">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btnUpdateDepartment">
                    <i class="fas fa-save"></i> Update Department
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Search Results Modal -->
<div class="modal fade" id="searchResultsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-search"></i> Search Results</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="searchResultsContainer">
                    <!-- Results will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.personnel-card {
    background-color: #fff;
    transition: all 0.2s ease;
    cursor: grab;
}

.personnel-card:hover {
    background-color: #f8f9fa;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.personnel-card.dragging {
    opacity: 0.5;
    cursor: grabbing;
}

.personnel-list {
    min-height: 100px;
}

.personnel-list.drag-over {
    background-color: #e3f2fd;
    border: 2px dashed #2196f3;
    min-height: 150px;
}

.department-column .card-header {
    border-radius: 0.5rem 0.5rem 0 0;
}

.icon-big {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.personnel-checkbox:checked + .custom-control-label::before {
    background-color: #2196f3;
    border-color: #2196f3;
}

.card-stats .card-body {
    padding: 15px;
}

.personnel-list::-webkit-scrollbar {
    width: 6px;
}

.personnel-list::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.personnel-list::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.personnel-list::-webkit-scrollbar-thumb:hover {
    background: #a1a1a1;
}
</style>

<script>
// Wait for jQuery to be available
document.addEventListener('DOMContentLoaded', function() {
    // Check if jQuery is loaded, if not wait for it
    var checkJQuery = setInterval(function() {
        if (typeof jQuery !== 'undefined') {
            clearInterval(checkJQuery);
            initDepartmentPage();
        }
    }, 50);
});

function initDepartmentPage() {
    var $ = jQuery;
    let selectedPersonnel = [];

    // Update selected count
    function updateSelectedCount() {
        selectedPersonnel = [];
        $('.personnel-checkbox:checked').each(function() {
            selectedPersonnel.push($(this).val());
        });
        $('#selectedCount').text(selectedPersonnel.length);
        $('#btnBulkAssign').prop('disabled', selectedPersonnel.length === 0);
    }

    // Checkbox change handler
    $(document).on('change', '.personnel-checkbox', function() {
        updateSelectedCount();
    });

    // Select all visible
    $('#btnSelectAll').click(function() {
        $('.personnel-card:visible .personnel-checkbox').prop('checked', true);
        updateSelectedCount();
    });

    // Deselect all
    $('#btnDeselectAll').click(function() {
        $('.personnel-checkbox').prop('checked', false);
        updateSelectedCount();
    });

    // Move single personnel
    $(document).on('click', '.move-personnel', function(e) {
        e.preventDefault();
        let personnelId = $(this).attr('data-personnel-id');
        let deptId = $(this).attr('data-dept-id');
        
        console.log('Move Personnel - ID:', personnelId, 'to Dept:', deptId);
        assignPersonnel(personnelId, deptId);
    });

    // Bulk assign
    $(document).on('click', '.bulk-assign-dept', function(e) {
        e.preventDefault();
        let deptId = $(this).attr('data-dept-id');
        
        console.log('Bulk Assign to Dept:', deptId, 'Personnel:', selectedPersonnel);
        
        if (selectedPersonnel.length === 0) {
            showNotification('Please select personnel first', 'warning');
            return;
        }

        bulkAssignPersonnel(selectedPersonnel, deptId);
    });

    // Assign single personnel
    function assignPersonnel(personnelId, deptId) {
        // Show loading indicator
        showNotification('Assigning personnel...', 'info');
        
        $.ajax({
            url: '<?= site_url("settings/assign_personnel") ?>',
            type: 'POST',
            data: {
                personnel_id: personnelId,
                department_id: deptId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showNotification(response.message, 'success');
                    setTimeout(function() {
                        location.reload();
                    }, 500);
                } else {
                    showNotification(response.message || 'Failed to assign personnel', 'danger');
                }
            },
            error: function(xhr, status, error) {
                console.log('Assign error:', xhr.responseText);
                showNotification('An error occurred: ' + error, 'danger');
            }
        });
    }

    // Bulk assign personnel
    function bulkAssignPersonnel(personnelIds, deptId) {
        // Show loading indicator
        showNotification('Assigning ' + personnelIds.length + ' personnel...', 'info');
        
        $.ajax({
            url: '<?= site_url("settings/bulk_assign_personnel") ?>',
            type: 'POST',
            data: {
                personnel_ids: personnelIds,
                department_id: deptId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showNotification(response.message, 'success');
                    setTimeout(function() {
                        location.reload();
                    }, 500);
                } else {
                    showNotification(response.message || 'Failed to assign personnel', 'danger');
                }
            },
            error: function(xhr, status, error) {
                console.log('Bulk assign error:', xhr.responseText);
                showNotification('An error occurred: ' + error, 'danger');
            }
        });
    }

    // Search personnel
    let searchTimeout;
    $('#searchPersonnel').on('keyup', function() {
        let searchTerm = $(this).val().toLowerCase();
        
        clearTimeout(searchTimeout);
        
        if (searchTerm.length < 2) {
            $('.personnel-card').show();
            return;
        }

        searchTimeout = setTimeout(function() {
            $('.personnel-card').each(function() {
                let text = $(this).text().toLowerCase();
                if (text.indexOf(searchTerm) > -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }, 300);
    });

    // Filter by department
    $('#filterDepartment').on('change', function() {
        let deptId = $(this).val();
        
        if (deptId === '') {
            $('.department-column').show();
        } else {
            $('.department-column').hide();
            $('.department-column[data-department-id="' + deptId + '"]').show();
        }
    });

    // Add department
    $('#btnSaveDepartment').click(function() {
        let form = $('#addDepartmentForm');
        let name = form.find('[name="name"]').val();
        let code = form.find('[name="code"]').val();
        
        if (!name || !code) {
            showNotification('Name and code are required', 'warning');
            return;
        }

        $.ajax({
            url: '<?= site_url("settings/create_department") ?>',
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showNotification(response.message, 'success');
                    $('#addDepartmentModal').modal('hide');
                    location.reload();
                } else {
                    showNotification(response.message, 'danger');
                }
            },
            error: function() {
                showNotification('An error occurred', 'danger');
            }
        });
    });

    // Edit department - open modal
    $(document).on('click', '.edit-department', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var $this = $(this);
        var id = $this.attr('data-id');
        var name = $this.attr('data-name');
        var description = $this.attr('data-description') || '';
        var color = $this.attr('data-color') || '#3498db';
        
        console.log('Edit Department - ID:', id, 'Name:', name, 'Color:', color);
        
        $('#editDeptId').val(id);
        $('#editDeptName').val(name);
        $('#editDeptDescription').val(description);
        $('#editDeptColor').val(color);
        $('#editDepartmentModal').modal('show');
    });

    // Update department
    $('#btnUpdateDepartment').click(function() {
        let form = $('#editDepartmentForm');
        
        $.ajax({
            url: '<?= site_url("settings/update_department") ?>',
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showNotification(response.message, 'success');
                    $('#editDepartmentModal').modal('hide');
                    location.reload();
                } else {
                    showNotification(response.message, 'danger');
                }
            },
            error: function() {
                showNotification('An error occurred', 'danger');
            }
        });
    });

    // Delete department
    $(document).on('click', '.delete-department', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        let deptId = $(this).attr('data-id');
        
        if (!confirm('Are you sure you want to delete this department? All personnel will be unassigned.')) {
            return;
        }

        $.ajax({
            url: '<?= site_url("settings/delete_department") ?>',
            type: 'POST',
            data: { id: deptId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showNotification(response.message, 'success');
                    location.reload();
                } else {
                    showNotification(response.message, 'danger');
                }
            },
            error: function(xhr, status, error) {
                console.log('Delete error:', xhr.responseText);
                showNotification('An error occurred: ' + error, 'danger');
            }
        });
    });

    // Drag and drop functionality - using native event listeners
    function initDragAndDrop() {
        // Add drag events to personnel cards
        document.querySelectorAll('.personnel-card').forEach(function(card) {
            card.addEventListener('dragstart', function(e) {
                this.classList.add('dragging');
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/plain', this.getAttribute('data-personnel-id'));
            });
            
            card.addEventListener('dragend', function(e) {
                this.classList.remove('dragging');
                document.querySelectorAll('.personnel-list').forEach(function(list) {
                    list.classList.remove('drag-over');
                });
            });
        });
        
        // Add drop zone events to personnel lists
        document.querySelectorAll('.personnel-list').forEach(function(list) {
            list.addEventListener('dragover', function(e) {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';
                this.classList.add('drag-over');
            });
            
            list.addEventListener('dragleave', function(e) {
                // Only remove if we're leaving the element entirely
                if (!this.contains(e.relatedTarget)) {
                    this.classList.remove('drag-over');
                }
            });
            
            list.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('drag-over');
                
                let personnelId = e.dataTransfer.getData('text/plain');
                let deptId = this.getAttribute('data-department-id');
                
                console.log('Drop - Personnel ID:', personnelId, 'Dept ID:', deptId);
                
                if (personnelId && deptId !== null) {
                    assignPersonnel(personnelId, deptId);
                }
            });
        });
    }
    
    // Initialize drag and drop
    initDragAndDrop();

    // Show notification
    function showNotification(message, type) {
        let alertHtml = '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
            message +
            '<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>' +
            '</div>';
        
        $('.page-inner').prepend(alertHtml);
        
        setTimeout(function() {
            $('.alert').alert('close');
        }, 3000);
    }
}
</script>
