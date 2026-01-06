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
            <a href="#">View Application</a>
        </li>
    </ul>
    <div class="ml-md-auto py-2 py-md-0">
        <a href="<?= site_url('leaves') ?>" class="btn btn-secondary btn-round btn-sm">
            <i class="fa fa-arrow-left"></i> Back
        </a>
        <a href="<?= site_url('leaves/print_form/' . $leave->id) ?>" class="btn btn-info btn-round btn-sm" target="_blank">
            <i class="fa fa-print"></i> Print
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
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <div class="card-head-row">
                    <div class="card-title">
                        Leave Application #<?= $leave->application_number ?>
                        <span class="badge <?= $this->leaveModel->get_status_badge($leave->status) ?> ml-2">
                            <?= ucfirst($leave->status) ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Basic Information -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">APPLICANT INFORMATION</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="40%"><strong>Name:</strong></td>
                                <td><?= strtoupper($leave->lastname . ', ' . $leave->firstname . ' ' . ($leave->middlename ?? '')) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Position:</strong></td>
                                <td><?= $leave->position ?? 'N/A' ?></td>
                            </tr>
                            <tr>
                                <td><strong>Office/Department:</strong></td>
                                <td><?= $leave->office_department ?></td>
                            </tr>
                            <tr>
                                <td><strong>Salary Grade:</strong></td>
                                <td>SG <?= $leave->salary_grade ?></td>
                            </tr>
                            <tr>
                                <td><strong>Date of Filing:</strong></td>
                                <td><?= date('F d, Y', strtotime($leave->date_of_filing)) ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">LEAVE DETAILS</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="40%"><strong>Type of Leave:</strong></td>
                                <td><?= $this->leaveModel->get_leave_type_label($leave->leave_type) ?></td>
                            </tr>
                            <?php if ($leave->leave_type == 'others' && $leave->leave_type_others): ?>
                            <tr>
                                <td><strong>Specify:</strong></td>
                                <td><?= $leave->leave_type_others ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <td><strong>Working Days:</strong></td>
                                <td><?= number_format($leave->working_days_applied, 1) ?> day(s)</td>
                            </tr>
                            <tr>
                                <td><strong>Inclusive Dates:</strong></td>
                                <td>
                                    <?= date('M d, Y', strtotime($leave->inclusive_date_from)) ?> - 
                                    <?= date('M d, Y', strtotime($leave->inclusive_date_to)) ?>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Commutation:</strong></td>
                                <td><?= $leave->commutation_requested ? 'Requested' : 'Not Requested' ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Leave Details Based on Type -->
                <?php if (in_array($leave->leave_type, ['vacation_leave', 'special_privilege_leave', 'mandatory_forced_leave'])): ?>
                <div class="alert alert-light">
                    <h6><strong>Vacation/Special Privilege Leave Details:</strong></h6>
                    <?php if ($leave->vacation_special_within_ph): ?>
                        <p class="mb-1"><i class="fa fa-map-marker-alt text-primary"></i> Within the Philippines: <?= $leave->vacation_special_within_ph ?></p>
                    <?php endif; ?>
                    <?php if ($leave->vacation_special_abroad): ?>
                        <p class="mb-0"><i class="fa fa-plane text-info"></i> Abroad: <?= $leave->vacation_special_abroad ?></p>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <?php if ($leave->leave_type == 'sick_leave'): ?>
                <div class="alert alert-light">
                    <h6><strong>Sick Leave Details:</strong></h6>
                    <?php if ($leave->sick_in_hospital): ?>
                        <p class="mb-1"><i class="fa fa-hospital text-danger"></i> In Hospital: <?= $leave->sick_in_hospital ?></p>
                    <?php endif; ?>
                    <?php if ($leave->sick_out_patient): ?>
                        <p class="mb-0"><i class="fa fa-user-md text-warning"></i> Out Patient: <?= $leave->sick_out_patient ?></p>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <?php if ($leave->leave_type == 'special_leave_benefits_women' && $leave->special_women_illness): ?>
                <div class="alert alert-light">
                    <h6><strong>Special Leave Benefits for Women:</strong></h6>
                    <p class="mb-0">Illness: <?= $leave->special_women_illness ?></p>
                </div>
                <?php endif; ?>

                <?php if ($leave->leave_type == 'study_leave'): ?>
                <div class="alert alert-light">
                    <h6><strong>Study Leave Details:</strong></h6>
                    <?php if ($leave->study_completion_masters): ?>
                        <p class="mb-1"><i class="fa fa-graduation-cap text-success"></i> Completion of Master's Degree</p>
                    <?php endif; ?>
                    <?php if ($leave->study_bar_review): ?>
                        <p class="mb-0"><i class="fa fa-book text-primary"></i> BAR/Board Examination Review</p>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <!-- Current Leave Credits -->
                <hr>
                <h6 class="text-muted mb-3">CURRENT LEAVE CREDITS</h6>
                <div class="row">
                    <?php foreach ($leave_credits as $credit): ?>
                    <div class="col-md-4 mb-2">
                        <div class="card bg-light">
                            <div class="card-body py-2">
                                <h6 class="mb-1"><?= ucfirst(str_replace('_', ' ', $credit->leave_type)) ?></h6>
                                <p class="mb-0">
                                    <small>Earned: <?= number_format($credit->earned, 3) ?></small><br>
                                    <small>Used: <?= number_format($credit->used, 3) ?></small><br>
                                    <strong>Balance: <?= number_format($credit->balance, 3) ?></strong>
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Action Details (if processed) -->
                <?php if (in_array($leave->status, ['certified', 'recommended', 'approved', 'disapproved'])): ?>
                <hr>
                <h6 class="text-muted mb-3">ACTION DETAILS</h6>

                <?php if ($leave->certified_date): ?>
                <div class="card bg-light mb-3">
                    <div class="card-body py-2">
                        <h6><i class="fa fa-certificate text-info"></i> Leave Credits Certification</h6>
                        <p class="mb-1"><small>As of: <?= date('M d, Y', strtotime($leave->certification_as_of)) ?></small></p>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Vacation Leave:</strong><br>
                                <small>Earned: <?= number_format($leave->vacation_leave_total_earned, 3) ?> |
                                Less: <?= number_format($leave->vacation_leave_less_application, 3) ?> |
                                Balance: <?= number_format($leave->vacation_leave_balance, 3) ?></small>
                            </div>
                            <div class="col-md-6">
                                <strong>Sick Leave:</strong><br>
                                <small>Earned: <?= number_format($leave->sick_leave_total_earned, 3) ?> |
                                Less: <?= number_format($leave->sick_leave_less_application, 3) ?> |
                                Balance: <?= number_format($leave->sick_leave_balance, 3) ?></small>
                            </div>
                        </div>
                        <p class="mb-0 mt-2"><small class="text-muted">Certified by: <?= $leave->certifier_fname . ' ' . $leave->certifier_lname ?> on <?= date('M d, Y h:i A', strtotime($leave->certified_date)) ?></small></p>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($leave->recommended_date): ?>
                <div class="card <?= $leave->recommendation == 'for_approval' ? 'bg-success' : 'bg-warning' ?> text-white mb-3">
                    <div class="card-body py-2">
                        <h6><i class="fa fa-thumbs-<?= $leave->recommendation == 'for_approval' ? 'up' : 'down' ?>"></i> Recommendation</h6>
                        <p class="mb-1"><strong><?= $leave->recommendation == 'for_approval' ? 'FOR APPROVAL' : 'FOR DISAPPROVAL' ?></strong></p>
                        <?php if ($leave->recommendation_disapproval_reason): ?>
                        <p class="mb-1">Reason: <?= $leave->recommendation_disapproval_reason ?></p>
                        <?php endif; ?>
                        <p class="mb-0"><small>Recommended by: <?= $leave->recommender_fname . ' ' . $leave->recommender_lname ?> on <?= date('M d, Y h:i A', strtotime($leave->recommended_date)) ?></small></p>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($leave->approved_date): ?>
                <div class="card <?= $leave->status == 'approved' ? 'bg-success' : 'bg-danger' ?> text-white mb-3">
                    <div class="card-body py-2">
                        <h6><i class="fa fa-<?= $leave->status == 'approved' ? 'check-circle' : 'times-circle' ?>"></i> Final Decision</h6>
                        <?php if ($leave->status == 'approved'): ?>
                            <p class="mb-1"><strong>APPROVED</strong></p>
                            <?php if ($leave->approved_days_with_pay): ?>
                            <p class="mb-1"><?= number_format($leave->approved_days_with_pay, 1) ?> days with pay</p>
                            <?php endif; ?>
                            <?php if ($leave->approved_days_without_pay): ?>
                            <p class="mb-1"><?= number_format($leave->approved_days_without_pay, 1) ?> days without pay</p>
                            <?php endif; ?>
                            <?php if ($leave->approved_others): ?>
                            <p class="mb-1">Others: <?= $leave->approved_others ?></p>
                            <?php endif; ?>
                        <?php else: ?>
                            <p class="mb-1"><strong>DISAPPROVED</strong></p>
                            <?php if ($leave->disapproval_reason): ?>
                            <p class="mb-1">Reason: <?= $leave->disapproval_reason ?></p>
                            <?php endif; ?>
                        <?php endif; ?>
                        <p class="mb-0"><small>By: <?= $leave->approver_fname . ' ' . $leave->approver_lname ?> on <?= date('M d, Y h:i A', strtotime($leave->approved_date)) ?></small></p>
                    </div>
                </div>
                <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Right Sidebar - Actions & Logs -->
    <div class="col-md-4">
        <!-- Action Panel -->
        <?php if (in_array($leave->status, ['pending', 'certified', 'recommended'])): ?>
        <div class="card">
            <div class="card-header bg-primary text-white">
                <div class="card-title mb-0">Process Application</div>
            </div>
            <div class="card-body">
                <?php if ($leave->status == 'pending'): ?>
                <!-- Certify Action -->
                <h6><i class="fa fa-certificate"></i> Step 1: Certify Leave Credits</h6>
                <p class="text-muted small">Verify and certify the applicant's leave credits.</p>
                <form action="<?= site_url('leaves/certify/' . $leave->id) ?>" method="POST">
                    <button type="submit" class="btn btn-info btn-block" onclick="return confirm('Certify leave credits for this application?')">
                        <i class="fa fa-certificate"></i> Certify Leave Credits
                    </button>
                </form>
                <?php endif; ?>

                <?php if ($leave->status == 'certified'): ?>
                <!-- Recommend Action -->
                <h6><i class="fa fa-thumbs-up"></i> Step 2: Recommendation</h6>
                <p class="text-muted small">Provide your recommendation for this application.</p>
                <form action="<?= site_url('leaves/recommend/' . $leave->id) ?>" method="POST">
                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="recommendation" id="for_approval" value="for_approval" checked>
                            <label class="form-check-label" for="for_approval">For Approval</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="recommendation" id="for_disapproval" value="for_disapproval">
                            <label class="form-check-label" for="for_disapproval">For Disapproval</label>
                        </div>
                    </div>
                    <div class="form-group" id="disapproval_reason_group" style="display: none;">
                        <label>Reason for Disapproval:</label>
                        <textarea name="disapproval_reason" class="form-control" rows="2"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fa fa-paper-plane"></i> Submit Recommendation
                    </button>
                </form>
                
                <hr>
                
                <form action="<?= site_url('leaves/disapprove/' . $leave->id) ?>" method="POST">
                    <div class="form-group">
                        <label>Or Disapprove Directly:</label>
                        <textarea name="disapproval_reason" class="form-control" rows="2" placeholder="Reason for disapproval..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger btn-block" onclick="return confirm('Disapprove this application?')">
                        <i class="fa fa-times"></i> Disapprove
                    </button>
                </form>
                <?php endif; ?>

                <?php if ($leave->status == 'recommended'): ?>
                <!-- Final Approval Action -->
                <h6><i class="fa fa-check-circle"></i> Step 3: Final Approval</h6>
                <p class="text-muted small">Make the final decision on this application.</p>
                
                <?php if ($leave->recommendation == 'for_approval'): ?>
                <form action="<?= site_url('leaves/approve/' . $leave->id) ?>" method="POST">
                    <div class="form-group">
                        <label>Days with Pay:</label>
                        <input type="number" name="days_with_pay" class="form-control" step="0.5" value="<?= $leave->working_days_applied ?>">
                    </div>
                    <div class="form-group">
                        <label>Days without Pay:</label>
                        <input type="number" name="days_without_pay" class="form-control" step="0.5" value="0">
                    </div>
                    <div class="form-group">
                        <label>Others (Specify):</label>
                        <input type="text" name="others" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-success btn-block" onclick="return confirm('Approve this application?')">
                        <i class="fa fa-check"></i> Approve
                    </button>
                </form>
                <?php endif; ?>
                
                <hr>
                
                <form action="<?= site_url('leaves/disapprove/' . $leave->id) ?>" method="POST">
                    <div class="form-group">
                        <label>Disapprove Due To:</label>
                        <textarea name="disapproval_reason" class="form-control" rows="2" placeholder="Reason for disapproval..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger btn-block" onclick="return confirm('Disapprove this application?')">
                        <i class="fa fa-times"></i> Disapprove
                    </button>
                </form>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Activity Log -->
        <div class="card">
            <div class="card-header">
                <div class="card-title">Activity Log</div>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <?php foreach ($logs as $log): ?>
                    <div class="timeline-item">
                        <div class="timeline-badge 
                            <?php 
                            switch($log->action) {
                                case 'created': echo 'bg-secondary'; break;
                                case 'submitted': echo 'bg-primary'; break;
                                case 'certified': echo 'bg-info'; break;
                                case 'recommended': echo 'bg-warning'; break;
                                case 'approved': echo 'bg-success'; break;
                                case 'disapproved': echo 'bg-danger'; break;
                                case 'cancelled': echo 'bg-dark'; break;
                                default: echo 'bg-secondary';
                            }
                            ?>">
                            <i class="fa fa-circle"></i>
                        </div>
                        <div class="timeline-content">
                            <p class="mb-0"><strong><?= ucfirst($log->action) ?></strong></p>
                            <p class="mb-0"><small class="text-muted"><?= $log->first_name . ' ' . $log->last_name ?></small></p>
                            <p class="mb-0"><small class="text-muted"><?= date('M d, Y h:i A', strtotime($log->action_date)) ?></small></p>
                            <?php if ($log->remarks): ?>
                            <p class="mb-0 mt-1"><small><?= $log->remarks ?></small></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <?php if (empty($logs)): ?>
                <p class="text-muted text-center">No activity recorded yet.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Cancel Action -->
        <?php if (in_array($leave->status, ['pending', 'certified', 'recommended'])): ?>
        <div class="card">
            <div class="card-body">
                <form action="<?= site_url('leaves/cancel/' . $leave->id) ?>" method="POST">
                    <input type="hidden" name="cancel_reason" value="Cancelled by admin">
                    <button type="submit" class="btn btn-outline-danger btn-block" onclick="return confirm('Are you sure you want to cancel this application?')">
                        <i class="fa fa-times"></i> Cancel Application
                    </button>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
$(document).ready(function() {
    // Toggle disapproval reason field
    $('input[name="recommendation"]').change(function() {
        if ($(this).val() == 'for_disapproval') {
            $('#disapproval_reason_group').show();
        } else {
            $('#disapproval_reason_group').hide();
        }
    });
});
</script>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 10px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    padding-bottom: 20px;
}

.timeline-badge {
    position: absolute;
    left: -25px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

.timeline-badge i {
    display: none;
}

.timeline-content {
    padding-left: 10px;
}
</style>
