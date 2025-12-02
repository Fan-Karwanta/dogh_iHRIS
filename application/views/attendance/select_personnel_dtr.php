<style>
.personnel-checkbox-card {
    border: 1px solid #e3e6f0;
    border-radius: 8px;
    padding: 10px 15px;
    margin-bottom: 8px;
    background: #fff;
    transition: all 0.2s ease;
    cursor: pointer;
}
.personnel-checkbox-card:hover {
    background: #f8f9fc;
    border-color: #4e73df;
}
.personnel-checkbox-card.selected {
    background: #e8f4fd;
    border-color: #4e73df;
}
.personnel-checkbox-card .custom-checkbox {
    margin-right: 10px;
}
.department-badge {
    font-size: 11px;
    padding: 3px 8px;
    border-radius: 12px;
}
.personnel-list-container {
    max-height: 500px;
    overflow-y: auto;
    border: 1px solid #e3e6f0;
    border-radius: 8px;
    padding: 15px;
    background: #f8f9fc;
}
.filter-section {
    background: #fff;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58,59,69,.15);
}
.stats-badge {
    font-size: 14px;
    padding: 8px 15px;
    border-radius: 20px;
    margin-right: 10px;
}
</style>

<div class="page-header">
    <h4 class="page-title"><?= $title ?></h4>
    <ul class="breadcrumbs">
        <li class="nav-home">
            <a href="#">
                <i class="fas fa-users"></i>
            </a>
        </li>
        <li class="separator">
            <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
            <a href="javascript:void(0)">DTR Reports</a>
        </li>
        <li class="separator">
            <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
            <a href="javascript:void(0)">Generate DTR</a>
        </li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-head-row">
                    <div class="card-title"><i class="fas fa-file-alt mr-2"></i>Generate DTR - Select Personnel</div>
                    <div class="card-tools">
                        <span class="badge badge-primary stats-badge" id="selectedCount">0 Selected</span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Filter Section -->
                <div class="filter-section">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label><strong><i class="fas fa-building mr-1"></i> Filter by Department</strong></label>
                                <select class="form-control" id="department_filter">
                                    <option value="all">All Departments</option>
                                    <?php if (isset($departments) && !empty($departments)) : ?>
                                        <?php foreach ($departments as $dept) : ?>
                                            <option value="<?= $dept->id ?>" data-color="<?= $dept->color ?>">
                                                <?= $dept->name ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    <option value="0">Unassigned</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label><strong><i class="fas fa-calendar mr-1"></i> Select Month</strong></label>
                                <input type="month" class="form-control" id="month_filter" value="<?= date('Y-m', strtotime('first day of last month')) ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label><strong><i class="fas fa-search mr-1"></i> Search Personnel</strong></label>
                                <input type="text" class="form-control" id="search_personnel" placeholder="Type name to search...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="selectAll">
                                        <i class="fas fa-check-double"></i> Select All
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="deselectAll">
                                        <i class="fas fa-times"></i> Deselect All
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Personnel List -->
                <form id="bulkDtrForm" method="POST" action="<?= site_url('attendance/bulk_generate_dtr') ?>">
                    <input type="hidden" name="date" id="selected_date" value="<?= date('Y-m', strtotime('first day of last month')) ?>">
                    
                    <div class="personnel-list-container" id="personnelListContainer">
                        <div class="row" id="personnelList">
                            <?php foreach ($all_personnel as $person) : 
                                $dept_name = isset($person->department_name) ? $person->department_name : 'Unassigned';
                                $dept_id = isset($person->department_id) ? $person->department_id : 0;
                            ?>
                                <div class="col-md-4 col-lg-3 personnel-item" 
                                     data-department="<?= $dept_id ?>" 
                                     data-name="<?= strtolower($person->lastname . ' ' . $person->firstname . ' ' . $person->middlename) ?>">
                                    <label class="personnel-checkbox-card d-flex align-items-center">
                                        <input type="checkbox" name="personnel_ids[]" value="<?= $person->id ?>" class="personnel-checkbox mr-2">
                                        <div class="flex-grow-1">
                                            <div class="font-weight-bold" style="font-size: 13px;">
                                                <?= $person->lastname ?>, <?= $person->firstname ?>
                                            </div>
                                            <small class="text-muted"><?= $person->position ?></small>
                                            <br>
                                            <span class="badge department-badge" style="background-color: <?= isset($person->department_color) ? $person->department_color : '#6c757d' ?>; color: white;">
                                                <?= $dept_name ?>
                                            </span>
                                        </div>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row mt-4">
                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn btn-primary btn-lg" id="generateBulkBtn" disabled>
                                <i class="fas fa-print mr-2"></i> Generate DTR for Selected Personnel
                            </button>
                            <a href="<?= site_url('attendance/generate_bulk_dtr') ?>" class="btn btn-secondary btn-lg ml-2">
                                <i class="fas fa-users mr-2"></i> Generate All Personnel DTR
                            </a>
                        </div>
                    </div>
                </form>

                <!-- Instructions -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <h5><i class="fas fa-info-circle"></i> Instructions:</h5>
                            <ul class="mb-0">
                                <li><strong>Filter by Department:</strong> Select a department to show only personnel from that department</li>
                                <li><strong>Select Personnel:</strong> Check the boxes next to the personnel you want to include</li>
                                <li><strong>Select Month:</strong> Choose the month for the DTR</li>
                                <li><strong>Generate DTR:</strong> Click the button to generate DTRs for selected personnel</li>
                                <li><strong>Tip:</strong> Use "Select All" to quickly select all visible personnel</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var checkJQuery = setInterval(function() {
        if (typeof jQuery !== 'undefined') {
            clearInterval(checkJQuery);
            initPage();
        }
    }, 50);
});

function initPage() {
    var $ = jQuery;
    
    // Update selected count
    function updateSelectedCount() {
        var count = $('.personnel-checkbox:checked').length;
        $('#selectedCount').text(count + ' Selected');
        $('#generateBulkBtn').prop('disabled', count === 0);
        
        // Update card styling
        $('.personnel-checkbox').each(function() {
            var card = $(this).closest('.personnel-checkbox-card');
            if ($(this).is(':checked')) {
                card.addClass('selected');
            } else {
                card.removeClass('selected');
            }
        });
    }

    // Department filter
    $('#department_filter').on('change', function() {
        var deptId = $(this).val();
        
        $('.personnel-item').each(function() {
            var itemDept = $(this).data('department');
            
            if (deptId === 'all') {
                $(this).show();
            } else if (deptId === '0') {
                // Show unassigned (null or 0)
                if (itemDept === 0 || itemDept === '' || itemDept === null) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            } else {
                if (itemDept == deptId) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            }
        });
        
        updateSelectedCount();
    });

    // Search filter
    $('#search_personnel').on('keyup', function() {
        var searchTerm = $(this).val().toLowerCase();
        
        $('.personnel-item').each(function() {
            var name = $(this).data('name');
            if (name.indexOf(searchTerm) > -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Select all visible
    $('#selectAll').on('click', function() {
        $('.personnel-item:visible .personnel-checkbox').prop('checked', true);
        updateSelectedCount();
    });

    // Deselect all
    $('#deselectAll').on('click', function() {
        $('.personnel-checkbox').prop('checked', false);
        updateSelectedCount();
    });

    // Update count on checkbox change
    $(document).on('change', '.personnel-checkbox', function() {
        updateSelectedCount();
    });

    // Update date field when month changes
    $('#month_filter').on('change', function() {
        $('#selected_date').val($(this).val());
    });

    // Form submission - add date to URL
    $('#bulkDtrForm').on('submit', function(e) {
        var selectedCount = $('.personnel-checkbox:checked').length;
        if (selectedCount === 0) {
            e.preventDefault();
            alert('Please select at least one personnel');
            return false;
        }
        
        // Append date to form action
        var date = $('#month_filter').val();
        $(this).attr('action', $(this).attr('action') + '?date=' + date);
    });

    // Click on card to toggle checkbox
    $('.personnel-checkbox-card').on('click', function(e) {
        if (e.target.type !== 'checkbox') {
            var checkbox = $(this).find('.personnel-checkbox');
            checkbox.prop('checked', !checkbox.prop('checked'));
            updateSelectedCount();
        }
    });

    // Initial count
    updateSelectedCount();
}
</script>
