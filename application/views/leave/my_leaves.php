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
            <a href="#">My Leave Applications</a>
        </li>
    </ul>
    <div class="ml-md-auto py-2 py-md-0">
        <a href="<?= site_url('leave_application/create') ?>" class="btn btn-primary btn-round btn-sm">
            <span class="btn-label">
                <i class="fa fa-plus"></i>
            </span>
            New Leave Application
        </a>
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
    <!-- Leave Credits Summary -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-title">My Leave Credits (<?= date('Y') ?>)</div>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php 
                    $credit_labels = array(
                        'vacation' => array(
                            'label' => 'Vacation Leave', 
                            'icon' => 'fa-umbrella-beach', 
                            'color' => 'primary',
                            'note' => '+1.25/month (cumulative)'
                        ),
                        'sick' => array(
                            'label' => 'Sick Leave', 
                            'icon' => 'fa-medkit', 
                            'color' => 'danger',
                            'note' => '+1.25/month (cumulative)'
                        ),
                        'special_privilege' => array(
                            'label' => 'Special Privilege', 
                            'icon' => 'fa-star', 
                            'color' => 'warning',
                            'note' => '3 days/year (resets yearly)'
                        ),
                        'solo_parent' => array(
                            'label' => 'Solo Parent', 
                            'icon' => 'fa-user', 
                            'color' => 'info',
                            'note' => '7 days/year'
                        ),
                        'vawc' => array(
                            'label' => 'VAWC Leave', 
                            'icon' => 'fa-shield-alt', 
                            'color' => 'secondary',
                            'note' => '10 days/year'
                        )
                    );
                    
                    foreach ($leave_credits as $credit): 
                        $info = isset($credit_labels[$credit->leave_type]) ? $credit_labels[$credit->leave_type] : array('label' => ucfirst($credit->leave_type), 'icon' => 'fa-calendar', 'color' => 'secondary', 'note' => '');
                    ?>
                    <div class="col-md-4 col-sm-6 mb-3">
                        <div class="card card-stats card-round">
                            <div class="card-body py-3">
                                <div class="row align-items-center">
                                    <div class="col-icon">
                                        <div class="icon-big text-center icon-<?= $info['color'] ?> bubble-shadow-small">
                                            <i class="fas <?= $info['icon'] ?>"></i>
                                        </div>
                                    </div>
                                    <div class="col col-stats ml-2">
                                        <div class="numbers">
                                            <p class="card-category mb-0" style="font-size: 11px;"><?= $info['label'] ?></p>
                                            <h4 class="card-title mb-0"><?= number_format($credit->balance, 3) ?></h4>
                                            <small class="text-muted" style="font-size: 9px;">
                                                <?= $info['note'] ?>
                                                <?php if (isset($credit->carried_over) && $credit->carried_over > 0): ?>
                                                    <br>Carried over: <?= number_format($credit->carried_over, 3) ?>
                                                <?php endif; ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <?php if (empty($leave_credits)): ?>
                    <div class="col-12">
                        <div class="alert alert-warning">
                            <i class="fa fa-exclamation-triangle"></i> No leave credits found. Please contact HR to initialize your leave credits.
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Mandatory/Forced Leave Note -->
                <div class="alert alert-info mt-2" style="font-size: 12px;">
                    <i class="fa fa-info-circle"></i> <strong>Note:</strong> Mandatory/Forced Leave (max 5 days) uses your Vacation Leave balance. 
                    Annual 5-day mandatory leave will be forfeited if not taken during the year.
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-head-row">
                    <div class="card-title">My Leave Applications</div>
                    <div class="card-tools">
                        <select id="statusFilter" class="form-control form-control-sm" style="width: 150px;">
                            <option value="">All Status</option>
                            <option value="draft">Draft</option>
                            <option value="pending">Pending</option>
                            <option value="certified">Certified</option>
                            <option value="recommended">Recommended</option>
                            <option value="approved">Approved</option>
                            <option value="disapproved">Disapproved</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="leavesTable" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Application #</th>
                                <th>Date Filed</th>
                                <th>Leave Type</th>
                                <th>Inclusive Dates</th>
                                <th>Days</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($leaves as $leave): ?>
                            <tr data-status="<?= $leave->status ?>">
                                <td><strong><?= $leave->application_number ?></strong></td>
                                <td><?= date('M d, Y', strtotime($leave->date_of_filing)) ?></td>
                                <td><?= $this->leaveModel->get_leave_type_label($leave->leave_type) ?></td>
                                <td>
                                    <?= date('M d', strtotime($leave->inclusive_date_from)) ?> - 
                                    <?= date('M d, Y', strtotime($leave->inclusive_date_to)) ?>
                                </td>
                                <td class="text-center"><?= number_format($leave->working_days_applied, 1) ?></td>
                                <td>
                                    <span class="badge <?= $this->leaveModel->get_status_badge($leave->status) ?>">
                                        <?= ucfirst($leave->status) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="<?= site_url('leave_application/view/' . $leave->id) ?>" class="btn btn-sm btn-info" title="View">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <?php if ($leave->status == 'draft'): ?>
                                        <a href="<?= site_url('leave_application/edit/' . $leave->id) ?>" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <a href="<?= site_url('leave_application/submit/' . $leave->id) ?>" class="btn btn-sm btn-success" title="Submit" onclick="return confirm('Submit this leave application?')">
                                            <i class="fa fa-paper-plane"></i>
                                        </a>
                                        <?php endif; ?>
                                        <?php if (in_array($leave->status, array('draft', 'pending'))): ?>
                                        <a href="<?= site_url('leave_application/cancel/' . $leave->id) ?>" class="btn btn-sm btn-danger" title="Cancel" onclick="return confirm('Are you sure you want to cancel this application?')">
                                            <i class="fa fa-times"></i>
                                        </a>
                                        <?php endif; ?>
                                        <?php if ($leave->status == 'approved'): ?>
                                        <a href="<?= site_url('leave_application/print_form/' . $leave->id) ?>" class="btn btn-sm btn-secondary" title="Print" target="_blank">
                                            <i class="fa fa-print"></i>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if (empty($leaves)): ?>
                <div class="text-center py-4">
                    <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No leave applications found.</p>
                    <a href="<?= site_url('leave_application/create') ?>" class="btn btn-primary">
                        <i class="fa fa-plus"></i> Create New Application
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#leavesTable').DataTable({
        "order": [[1, "desc"]],
        "pageLength": 10
    });
    
    // Status filter
    $('#statusFilter').change(function() {
        var status = $(this).val();
        if (status) {
            table.column(5).search(status).draw();
        } else {
            table.column(5).search('').draw();
        }
    });
});
</script>
