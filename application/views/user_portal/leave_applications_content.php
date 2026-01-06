<?php
// Get leave credits info
$credit_labels = array(
    'vacation' => array('label' => 'Vacation Leave', 'icon' => 'fa-umbrella-beach', 'color' => 'primary', 'note' => '+1.25/month'),
    'sick' => array('label' => 'Sick Leave', 'icon' => 'fa-medkit', 'color' => 'danger', 'note' => '+1.25/month'),
    'special_privilege' => array('label' => 'Special Privilege', 'icon' => 'fa-star', 'color' => 'warning', 'note' => '3 days/year')
);
?>

<!-- Leave Credits Summary -->
<div class="row mb-4">
    <?php foreach ($leave_credits as $credit): 
        $info = isset($credit_labels[$credit->leave_type]) ? $credit_labels[$credit->leave_type] : null;
        if (!$info) continue;
    ?>
    <div class="col-md-4 mb-3">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="icon <?= $info['color'] ?>">
                    <i class="fas <?= $info['icon'] ?>"></i>
                </div>
                <div class="ml-3">
                    <div class="stat-label"><?= $info['label'] ?></div>
                    <div class="stat-value"><?= number_format($credit->balance, 3) ?></div>
                    <small class="text-muted"><?= $info['note'] ?></small>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    
    <?php if (empty($leave_credits)): ?>
    <div class="col-12">
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            No leave credits found. Please contact HR to initialize your leave credits.
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Info Note -->
<div class="alert alert-info mb-4" style="font-size: 13px;">
    <i class="fas fa-info-circle mr-2"></i>
    <strong>Note:</strong> Mandatory/Forced Leave (max 5 days) uses your Vacation Leave balance.
</div>

<!-- Leave Applications List -->
<div class="card-custom">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">My Leave Applications</h5>
        <a href="<?= site_url('user/create_leave') ?>" class="btn btn-success btn-sm">
            <i class="fas fa-plus mr-1"></i> New Application
        </a>
    </div>
    <div class="card-body">
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

        <?php if (!empty($leaves)): ?>
        <div class="table-responsive">
            <table class="table table-hover">
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
                    <tr>
                        <td><strong><?= $leave->application_number ?></strong></td>
                        <td><?= date('M d, Y', strtotime($leave->date_of_filing)) ?></td>
                        <td><?= $this->leaveModel->get_leave_type_label($leave->leave_type) ?></td>
                        <td>
                            <?= date('M d', strtotime($leave->inclusive_date_from)) ?> - 
                            <?= date('M d, Y', strtotime($leave->inclusive_date_to)) ?>
                        </td>
                        <td class="text-center"><?= number_format($leave->working_days_applied, 1) ?></td>
                        <td>
                            <?php
                            $badge_class = 'secondary';
                            switch($leave->status) {
                                case 'draft': $badge_class = 'secondary'; break;
                                case 'pending': $badge_class = 'warning'; break;
                                case 'certified': $badge_class = 'info'; break;
                                case 'recommended': $badge_class = 'primary'; break;
                                case 'approved': $badge_class = 'success'; break;
                                case 'disapproved': $badge_class = 'danger'; break;
                                case 'cancelled': $badge_class = 'dark'; break;
                            }
                            ?>
                            <span class="badge badge-<?= $badge_class ?>"><?= ucfirst($leave->status) ?></span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?= site_url('user/view_leave/' . $leave->id) ?>" class="btn btn-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if ($leave->status == 'draft'): ?>
                                <a href="<?= site_url('user/edit_leave/' . $leave->id) ?>" class="btn btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="<?= site_url('user/submit_leave/' . $leave->id) ?>" class="btn btn-success" title="Submit" onclick="return confirm('Submit this leave application?')">
                                    <i class="fas fa-paper-plane"></i>
                                </a>
                                <?php endif; ?>
                                <?php if (in_array($leave->status, array('draft', 'pending'))): ?>
                                <a href="<?= site_url('user/cancel_leave/' . $leave->id) ?>" class="btn btn-danger" title="Cancel" onclick="return confirm('Cancel this application?')">
                                    <i class="fas fa-times"></i>
                                </a>
                                <?php endif; ?>
                                <?php if ($leave->status == 'approved'): ?>
                                <a href="<?= site_url('user/print_leave/' . $leave->id) ?>" class="btn btn-secondary" title="Print" target="_blank">
                                    <i class="fas fa-print"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-file-alt fa-4x text-muted mb-3"></i>
            <h5 class="text-muted">No Leave Applications Yet</h5>
            <p class="text-muted">Start by creating a new leave application.</p>
            <a href="<?= site_url('user/create_leave') ?>" class="btn btn-success">
                <i class="fas fa-plus mr-1"></i> Create New Application
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>
