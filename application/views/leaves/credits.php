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
            <a href="<?= site_url('leaves') ?>">Leave Management</a>
        </li>
        <li class="separator">
            <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
            <a href="#">Leave Credits</a>
        </li>
    </ul>
    <div class="ml-md-auto py-2 py-md-0">
        <a href="<?= site_url('leaves/bulk_init_credits') ?>" class="btn btn-success btn-round btn-sm" onclick="return confirm('Initialize leave credits for all personnel? This will add default credits for those who don\'t have any.')">
            <i class="fa fa-magic"></i> Initialize All Credits
        </a>
        <a href="<?= site_url('leaves/add_monthly_accrual') ?>" class="btn btn-info btn-round btn-sm" onclick="return confirm('Add monthly accrual (+1.25 VL/SL) for all personnel?')">
            <i class="fa fa-plus-circle"></i> Add Monthly Accrual
        </a>
    </div>
</div>

<!-- Credit Rules Info -->
<div class="row mb-3">
    <div class="col-md-12">
        <div class="alert alert-info">
            <h6><i class="fa fa-info-circle"></i> Leave Credit Rules:</h6>
            <ul class="mb-0" style="font-size: 12px;">
                <li><strong>Vacation Leave (VL):</strong> +1.25 days/month (cumulative, not reset yearly)</li>
                <li><strong>Sick Leave (SL):</strong> +1.25 days/month (cumulative, not reset yearly)</li>
                <li><strong>Special Privilege Leave (SPL):</strong> 3 days/year (reset yearly)</li>
                <li><strong>Mandatory/Forced Leave:</strong> Max 5 days, uses VL balance (forfeited if not taken)</li>
            </ul>
        </div>
    </div>
</div>

<?php if ($this->session->flashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= $this->session->flashdata('success') ?>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
<?php endif; ?>

<?php if ($this->session->flashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= $this->session->flashdata('error') ?>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-head-row">
                    <div class="card-title">Leave Credits Management (<?= date('Y') ?>)</div>
                    <div class="card-tools">
                        <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Search personnel..." style="width: 200px;">
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="creditsTable" class="table table-striped table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Department</th>
                                <th class="text-center">Vacation Leave</th>
                                <th class="text-center">Sick Leave</th>
                                <th class="text-center">Special Privilege</th>
                                <th class="text-center">Solo Parent</th>
                                <th class="text-center">VAWC</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($personnel as $person): ?>
                            <tr>
                                <td>
                                    <strong><?= $person->lastname . ', ' . $person->firstname ?></strong><br>
                                    <small class="text-muted"><?= $person->position ?? '' ?></small>
                                </td>
                                <td><?= $person->department_name ?? 'N/A' ?></td>
                                <?php 
                                $credits_by_type = array();
                                foreach ($person->credits as $c) {
                                    $credits_by_type[$c->leave_type] = $c;
                                }
                                
                                $types = array('vacation', 'sick', 'special_privilege', 'solo_parent', 'vawc');
                                foreach ($types as $type):
                                    $credit = isset($credits_by_type[$type]) ? $credits_by_type[$type] : null;
                                ?>
                                <td class="text-center">
                                    <?php if ($credit): ?>
                                        <span class="badge badge-<?= $credit->balance > 0 ? 'success' : 'danger' ?>" 
                                              title="Earned: <?= number_format($credit->earned, 3) ?> | Used: <?= number_format($credit->used, 3) ?>">
                                            <?= number_format($credit->balance, 1) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <?php endforeach; ?>
                                <td>
                                    <?php if (empty($person->credits)): ?>
                                    <a href="<?= site_url('leaves/init_credits/' . $person->id) ?>" class="btn btn-xs btn-success" title="Initialize Credits">
                                        <i class="fa fa-plus"></i>
                                    </a>
                                    <?php endif; ?>
                                    <button type="button" class="btn btn-xs btn-info" onclick="editCredits(<?= $person->id ?>, '<?= addslashes($person->lastname . ', ' . $person->firstname) ?>')" title="Edit Credits">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Credits Modal -->
<div class="modal fade" id="editCreditsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Leave Credits</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <h6 id="personnelName" class="mb-3"></h6>
                <input type="hidden" id="editPersonnelId">
                
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Leave Type</th>
                            <th>Earned</th>
                            <th>Used</th>
                            <th>Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Vacation Leave</td>
                            <td><input type="number" class="form-control form-control-sm credit-input" id="vacation_earned" step="0.001" min="0"></td>
                            <td><input type="number" class="form-control form-control-sm credit-input" id="vacation_used" step="0.001" min="0"></td>
                            <td><span id="vacation_balance" class="font-weight-bold">0.000</span></td>
                        </tr>
                        <tr>
                            <td>Sick Leave</td>
                            <td><input type="number" class="form-control form-control-sm credit-input" id="sick_earned" step="0.001" min="0"></td>
                            <td><input type="number" class="form-control form-control-sm credit-input" id="sick_used" step="0.001" min="0"></td>
                            <td><span id="sick_balance" class="font-weight-bold">0.000</span></td>
                        </tr>
                        <tr>
                            <td>Special Privilege</td>
                            <td><input type="number" class="form-control form-control-sm credit-input" id="special_privilege_earned" step="0.001" min="0"></td>
                            <td><input type="number" class="form-control form-control-sm credit-input" id="special_privilege_used" step="0.001" min="0"></td>
                            <td><span id="special_privilege_balance" class="font-weight-bold">0.000</span></td>
                        </tr>
                        <tr>
                            <td>Solo Parent</td>
                            <td><input type="number" class="form-control form-control-sm credit-input" id="solo_parent_earned" step="0.001" min="0"></td>
                            <td><input type="number" class="form-control form-control-sm credit-input" id="solo_parent_used" step="0.001" min="0"></td>
                            <td><span id="solo_parent_balance" class="font-weight-bold">0.000</span></td>
                        </tr>
                        <tr>
                            <td>VAWC</td>
                            <td><input type="number" class="form-control form-control-sm credit-input" id="vawc_earned" step="0.001" min="0"></td>
                            <td><input type="number" class="form-control form-control-sm credit-input" id="vawc_used" step="0.001" min="0"></td>
                            <td><span id="vawc_balance" class="font-weight-bold">0.000</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveCredits()">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#creditsTable').DataTable({
        "pageLength": 25,
        "order": [[0, "asc"]]
    });
    
    // Search functionality
    $('#searchInput').on('keyup', function() {
        table.search(this.value).draw();
    });
    
    // Calculate balance on input change
    $('.credit-input').on('input', function() {
        var type = $(this).attr('id').replace('_earned', '').replace('_used', '');
        var earned = parseFloat($('#' + type + '_earned').val()) || 0;
        var used = parseFloat($('#' + type + '_used').val()) || 0;
        var balance = earned - used;
        $('#' + type + '_balance').text(balance.toFixed(3));
    });
});

function editCredits(personnelId, name) {
    $('#editPersonnelId').val(personnelId);
    $('#personnelName').text(name);
    
    // Reset all fields
    $('.credit-input').val('');
    $('[id$="_balance"]').text('0.000');
    
    // Load current credits via AJAX
    $.get('<?= site_url('leave_application/get_credits/') ?>' + personnelId, function(response) {
        var data = JSON.parse(response);
        if (data.success && data.data) {
            data.data.forEach(function(credit) {
                $('#' + credit.leave_type + '_earned').val(credit.earned);
                $('#' + credit.leave_type + '_used').val(credit.used);
                $('#' + credit.leave_type + '_balance').text(parseFloat(credit.balance).toFixed(3));
            });
        }
    });
    
    $('#editCreditsModal').modal('show');
}

function saveCredits() {
    var personnelId = $('#editPersonnelId').val();
    var types = ['vacation', 'sick', 'special_privilege', 'solo_parent', 'vawc'];
    var promises = [];
    
    types.forEach(function(type) {
        var earned = $('#' + type + '_earned').val();
        var used = $('#' + type + '_used').val();
        
        if (earned !== '' || used !== '') {
            promises.push($.post('<?= site_url('leaves/update_credits') ?>', {
                personnel_id: personnelId,
                leave_type: type,
                earned: earned || 0,
                used: used || 0
            }));
        }
    });
    
    if (promises.length > 0) {
        $.when.apply($, promises).done(function() {
            $('#editCreditsModal').modal('hide');
            location.reload();
        }).fail(function() {
            alert('Error saving credits. Please try again.');
        });
    } else {
        $('#editCreditsModal').modal('hide');
    }
}
</script>

<style>
.btn-xs {
    padding: 2px 6px;
    font-size: 11px;
}
</style>
