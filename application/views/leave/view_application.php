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
            <a href="<?= site_url('leave_application') ?>">Leave Applications</a>
        </li>
        <li class="separator">
            <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
            <a href="#">View Application</a>
        </li>
    </ul>
    <div class="ml-md-auto py-2 py-md-0">
        <a href="<?= site_url('leave_application') ?>" class="btn btn-secondary btn-round btn-sm">
            <i class="fa fa-arrow-left"></i> Back
        </a>
        <a href="<?= site_url('leave_application/print_form/' . $leave->id) ?>" class="btn btn-info btn-round btn-sm" target="_blank">
            <i class="fa fa-print"></i> Print
        </a>
    </div>
</div>

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

                <?php if ($leave->other_purpose_monetization || $leave->other_purpose_terminal_leave): ?>
                <div class="alert alert-light">
                    <h6><strong>Other Purpose:</strong></h6>
                    <?php if ($leave->other_purpose_monetization): ?>
                        <p class="mb-1"><i class="fa fa-money-bill text-success"></i> Monetization of Leave Credits</p>
                    <?php endif; ?>
                    <?php if ($leave->other_purpose_terminal_leave): ?>
                        <p class="mb-0"><i class="fa fa-sign-out-alt text-danger"></i> Terminal Leave</p>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <!-- Action Details (if processed) -->
                <?php if (in_array($leave->status, ['certified', 'recommended', 'approved', 'disapproved'])): ?>
                <hr>
                <h6 class="text-muted mb-3">ACTION DETAILS</h6>

                <?php if ($leave->certified_date): ?>
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="card bg-light">
                            <div class="card-body py-2">
                                <h6><i class="fa fa-certificate text-info"></i> Leave Credits Certification</h6>
                                <p class="mb-1"><small>As of: <?= date('M d, Y', strtotime($leave->certification_as_of)) ?></small></p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Vacation Leave:</strong><br>
                                        Earned: <?= number_format($leave->vacation_leave_total_earned, 3) ?> |
                                        Less: <?= number_format($leave->vacation_leave_less_application, 3) ?> |
                                        Balance: <?= number_format($leave->vacation_leave_balance, 3) ?>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Sick Leave:</strong><br>
                                        Earned: <?= number_format($leave->sick_leave_total_earned, 3) ?> |
                                        Less: <?= number_format($leave->sick_leave_less_application, 3) ?> |
                                        Balance: <?= number_format($leave->sick_leave_balance, 3) ?>
                                    </div>
                                </div>
                                <p class="mb-0 mt-2"><small>Certified by: <?= $leave->certifier_fname . ' ' . $leave->certifier_lname ?> on <?= date('M d, Y h:i A', strtotime($leave->certified_date)) ?></small></p>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($leave->recommended_date): ?>
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="card <?= $leave->recommendation == 'for_approval' ? 'bg-success' : 'bg-warning' ?> text-white">
                            <div class="card-body py-2">
                                <h6><i class="fa fa-thumbs-<?= $leave->recommendation == 'for_approval' ? 'up' : 'down' ?>"></i> Recommendation</h6>
                                <p class="mb-1"><strong><?= $leave->recommendation == 'for_approval' ? 'FOR APPROVAL' : 'FOR DISAPPROVAL' ?></strong></p>
                                <?php if ($leave->recommendation_disapproval_reason): ?>
                                <p class="mb-1">Reason: <?= $leave->recommendation_disapproval_reason ?></p>
                                <?php endif; ?>
                                <p class="mb-0"><small>Recommended by: <?= $leave->recommender_fname . ' ' . $leave->recommender_lname ?> on <?= date('M d, Y h:i A', strtotime($leave->recommended_date)) ?></small></p>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($leave->approved_date): ?>
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="card <?= $leave->status == 'approved' ? 'bg-success' : 'bg-danger' ?> text-white">
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
                    </div>
                </div>
                <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Activity Log -->
    <div class="col-md-4">
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

        <!-- Quick Actions -->
        <?php if (in_array($leave->status, ['draft', 'pending'])): ?>
        <div class="card">
            <div class="card-header">
                <div class="card-title">Quick Actions</div>
            </div>
            <div class="card-body">
                <?php if ($leave->status == 'draft'): ?>
                <a href="<?= site_url('leave_application/edit/' . $leave->id) ?>" class="btn btn-warning btn-block mb-2">
                    <i class="fa fa-edit"></i> Edit Application
                </a>
                <a href="<?= site_url('leave_application/submit/' . $leave->id) ?>" class="btn btn-success btn-block mb-2" onclick="return confirm('Submit this leave application?')">
                    <i class="fa fa-paper-plane"></i> Submit Application
                </a>
                <?php endif; ?>
                <a href="<?= site_url('leave_application/cancel/' . $leave->id) ?>" class="btn btn-danger btn-block" onclick="return confirm('Are you sure you want to cancel this application?')">
                    <i class="fa fa-times"></i> Cancel Application
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

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
