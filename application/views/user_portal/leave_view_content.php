<?php
$this->load->model('LeaveModel', 'leaveModel');
?>

<div class="row">
    <div class="col-md-8">
        <div class="card-custom">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    Leave Application #<?= $leave->application_number ?>
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
                    <span class="badge badge-<?= $badge_class ?> ml-2"><?= ucfirst($leave->status) ?></span>
                </h5>
                <div>
                    <a href="<?= site_url('user/leave_applications') ?>" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    <?php if ($leave->status == 'approved'): ?>
                    <a href="<?= site_url('user/print_leave/' . $leave->id) ?>" class="btn btn-info btn-sm" target="_blank">
                        <i class="fas fa-print"></i> Print
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body">
                <!-- Applicant Information -->
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
                                <td><?= date('M d, Y', strtotime($leave->inclusive_date_from)) ?> - <?= date('M d, Y', strtotime($leave->inclusive_date_to)) ?></td>
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
                    <?php if ($leave->vacation_special_within_ph || $leave->vacation_special_abroad): ?>
                    <div class="alert alert-light">
                        <h6><strong>Vacation/Special Privilege Leave Details:</strong></h6>
                        <?php if ($leave->vacation_special_within_ph): ?>
                            <p class="mb-1"><i class="fas fa-map-marker-alt text-primary"></i> Within the Philippines: <?= $leave->vacation_special_within_ph ?></p>
                        <?php endif; ?>
                        <?php if ($leave->vacation_special_abroad): ?>
                            <p class="mb-0"><i class="fas fa-plane text-info"></i> Abroad: <?= $leave->vacation_special_abroad ?></p>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if ($leave->leave_type == 'sick_leave'): ?>
                    <?php if ($leave->sick_in_hospital || $leave->sick_out_patient): ?>
                    <div class="alert alert-light">
                        <h6><strong>Sick Leave Details:</strong></h6>
                        <?php if ($leave->sick_in_hospital): ?>
                            <p class="mb-1"><i class="fas fa-hospital text-danger"></i> In Hospital: <?= $leave->sick_in_hospital ?></p>
                        <?php endif; ?>
                        <?php if ($leave->sick_out_patient): ?>
                            <p class="mb-0"><i class="fas fa-user-md text-warning"></i> Out Patient: <?= $leave->sick_out_patient ?></p>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>

                <!-- Action Details (if processed) -->
                <?php if (in_array($leave->status, ['certified', 'recommended', 'approved', 'disapproved'])): ?>
                <hr>
                <h6 class="text-muted mb-3">ACTION DETAILS</h6>

                <?php if ($leave->certified_date): ?>
                <div class="alert alert-info">
                    <h6><i class="fas fa-certificate"></i> Leave Credits Certification</h6>
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
                <?php endif; ?>

                <?php if ($leave->recommended_date): ?>
                <div class="alert <?= $leave->recommendation == 'for_approval' ? 'alert-success' : 'alert-warning' ?>">
                    <h6><i class="fas fa-thumbs-<?= $leave->recommendation == 'for_approval' ? 'up' : 'down' ?>"></i> Recommendation</h6>
                    <p class="mb-1"><strong><?= $leave->recommendation == 'for_approval' ? 'FOR APPROVAL' : 'FOR DISAPPROVAL' ?></strong></p>
                    <?php if ($leave->recommendation_disapproval_reason): ?>
                    <p class="mb-1">Reason: <?= $leave->recommendation_disapproval_reason ?></p>
                    <?php endif; ?>
                    <p class="mb-0"><small>Recommended by: <?= $leave->recommender_fname . ' ' . $leave->recommender_lname ?> on <?= date('M d, Y h:i A', strtotime($leave->recommended_date)) ?></small></p>
                </div>
                <?php endif; ?>

                <?php if ($leave->approved_date): ?>
                <div class="alert <?= $leave->status == 'approved' ? 'alert-success' : 'alert-danger' ?>">
                    <h6><i class="fas fa-<?= $leave->status == 'approved' ? 'check-circle' : 'times-circle' ?>"></i> Final Decision</h6>
                    <?php if ($leave->status == 'approved'): ?>
                        <p class="mb-1"><strong>APPROVED</strong></p>
                        <?php if ($leave->approved_days_with_pay): ?>
                        <p class="mb-1"><?= number_format($leave->approved_days_with_pay, 1) ?> days with pay</p>
                        <?php endif; ?>
                        <?php if ($leave->approved_days_without_pay): ?>
                        <p class="mb-1"><?= number_format($leave->approved_days_without_pay, 1) ?> days without pay</p>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="mb-1"><strong>DISAPPROVED</strong></p>
                        <?php if ($leave->disapproval_reason): ?>
                        <p class="mb-1">Reason: <?= $leave->disapproval_reason ?></p>
                        <?php endif; ?>
                    <?php endif; ?>
                    <p class="mb-0"><small>By: <?= $leave->approver_fname . ' ' . $leave->approver_lname ?> on <?= date('M d, Y h:i A', strtotime($leave->approved_date)) ?></small></p>
                </div>
                <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Activity Log & Actions -->
    <div class="col-md-4">
        <?php if (in_array($leave->status, ['draft', 'pending'])): ?>
        <div class="card-custom mb-3">
            <div class="card-header">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <?php if ($leave->status == 'draft'): ?>
                <a href="<?= site_url('user/edit_leave/' . $leave->id) ?>" class="btn btn-warning btn-block mb-2">
                    <i class="fas fa-edit"></i> Edit Application
                </a>
                <a href="<?= site_url('user/submit_leave/' . $leave->id) ?>" class="btn btn-success btn-block mb-2" onclick="return confirm('Submit this leave application?')">
                    <i class="fas fa-paper-plane"></i> Submit Application
                </a>
                <?php endif; ?>
                <a href="<?= site_url('user/cancel_leave/' . $leave->id) ?>" class="btn btn-danger btn-block" onclick="return confirm('Cancel this application?')">
                    <i class="fas fa-times"></i> Cancel Application
                </a>
            </div>
        </div>
        <?php endif; ?>

        <div class="card-custom">
            <div class="card-header">
                <h5 class="mb-0">Activity Log</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($logs)): ?>
                <div class="timeline">
                    <?php foreach ($logs as $log): ?>
                    <div class="timeline-item mb-3">
                        <?php
                        $log_color = 'secondary';
                        switch($log->action) {
                            case 'created': $log_color = 'secondary'; break;
                            case 'submitted': $log_color = 'primary'; break;
                            case 'certified': $log_color = 'info'; break;
                            case 'recommended': $log_color = 'warning'; break;
                            case 'approved': $log_color = 'success'; break;
                            case 'disapproved': $log_color = 'danger'; break;
                            case 'cancelled': $log_color = 'dark'; break;
                        }
                        ?>
                        <span class="badge badge-<?= $log_color ?>"><?= ucfirst($log->action) ?></span>
                        <p class="mb-0"><small class="text-muted"><?= $log->first_name . ' ' . $log->last_name ?></small></p>
                        <p class="mb-0"><small class="text-muted"><?= date('M d, Y h:i A', strtotime($log->action_date)) ?></small></p>
                        <?php if ($log->remarks): ?>
                        <p class="mb-0 mt-1"><small><?= $log->remarks ?></small></p>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <p class="text-muted text-center">No activity recorded yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
