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
            <a href="#"><?= isset($leave) ? 'Edit' : 'New' ?> Application</a>
        </li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-head-row">
                    <div class="card-title">CS Form No. 6 - Application for Leave</div>
                    <div class="card-tools">
                        <button type="button" class="btn btn-info btn-sm" onclick="printForm()">
                            <i class="fa fa-print"></i> Print Preview
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <?php if ($this->session->flashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <?= $this->session->flashdata('error') ?>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                <?php endif; ?>

                <form id="leaveApplicationForm" action="<?= site_url('leave_application/save') ?>" method="POST">
                    <?php if (isset($leave)): ?>
                        <input type="hidden" name="leave_id" value="<?= $leave->id ?>">
                    <?php endif; ?>
                    <input type="hidden" name="submit_type" id="submit_type" value="draft">

                    <!-- CS Form No. 6 Layout -->
                    <div id="printableForm" class="cs-form-container" style="max-width: 900px; margin: 0 auto; border: 2px solid #000; padding: 0; background: #fff;">
                        
                        <!-- Civil Service Form No. 6 - Upper Left Corner -->
                        <div style="padding: 5px 10px;">
                            <span style="font-size: 10px; font-style: italic;">Civil Service Form No. 6</span><br>
                            <span style="font-size: 10px; font-style: italic;">Revised 2020</span>
                        </div>
                        
                        <!-- Header Row with Logos and Title -->
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="width: 15%; text-align: center; vertical-align: middle; padding: 5px;">
                                    <img src="<?= base_url('assets/img/doh_logo1.png') ?>" alt="DOH Logo" style="max-height: 70px;">
                                </td>
                                <td style="width: 50%; text-align: center; vertical-align: middle; padding: 5px;">
                                    <p style="margin: 0; font-size: 12px;">Republic of the Philippines</p>
                                    <p style="margin: 0; font-size: 12px;"><strong>Department of Health</strong></p>
                                    <p style="margin: 0; font-size: 12px;"><strong>DAVAO OCCIDENTAL GENERAL HOSPITAL</strong></p>
                                </td>
                                <td style="width: 15%; text-align: center; vertical-align: middle; padding: 5px;">
                                    <img src="<?= base_url('assets/img/dogh_logo.png') ?>" alt="DOGH Logo" style="max-height: 70px;">
                                </td>
                                <td style="width: 20%; text-align: left; vertical-align: top; padding: 5px;">
                                </td>
                            </tr>
                        </table>
                        
                        <!-- Title and Date Received -->
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="width: 70%; text-align: center; padding: 5px;">
                                    <h4 style="margin: 0;"><strong>APPLICATION FOR LEAVE</strong></h4>
                                </td>
                                <td style="width: 30%; text-align: left; vertical-align: middle; padding: 5px; font-size: 11px;">
                                    <span style="white-space: nowrap;">DATE RECEIVED: _______________</span>
                                </td>
                            </tr>
                        </table>

                        <!-- Section 1-5: Basic Information -->
                        <table style="width: 100%; border-collapse: collapse; border-top: 1px solid #000;">
                            <tr>
                                <td style="width: 20%; padding: 8px; border-right: 1px solid #000; vertical-align: top;">
                                    <span>1. OFFICE/DEPARTMENT</span><br>
                                    <select name="office_department" id="office_department" class="form-control form-control-sm" required style="margin-top: 5px;">
                                        <option value="">-- Select --</option>
                                        <option value="Medical" <?= (isset($leave) && $leave->office_department == 'Medical') ? 'selected' : '' ?>>Medical</option>
                                        <option value="Nursing" <?= (isset($leave) && $leave->office_department == 'Nursing') ? 'selected' : '' ?>>Nursing</option>
                                        <option value="Ancillary" <?= (isset($leave) && $leave->office_department == 'Ancillary') ? 'selected' : '' ?>>Ancillary</option>
                                        <option value="Administrative" <?= (isset($leave) && $leave->office_department == 'Administrative') ? 'selected' : '' ?>>Administrative</option>
                                    </select>
                                </td>
                                <td style="width: 55%; padding: 8px; vertical-align: top;">
                                    <table style="width: 100%;">
                                        <tr>
                                            <td style="width: 15%; vertical-align: top;">
                                                <span>2. NAME:</span>
                                            </td>
                                            <td style="width: 28%; text-align: center;">
                                                <input type="text" class="form-control form-control-sm text-center" value="<?= strtoupper($personnel->lastname) ?>" readonly style="border-bottom: 1px solid #000; border-top: none; border-left: none; border-right: none; background: transparent;">
                                                <small>(Last)</small>
                                            </td>
                                            <td style="width: 28%; text-align: center;">
                                                <input type="text" class="form-control form-control-sm text-center" value="<?= strtoupper($personnel->firstname) ?>" readonly style="border-bottom: 1px solid #000; border-top: none; border-left: none; border-right: none; background: transparent;">
                                                <small>(First)</small>
                                            </td>
                                            <td style="width: 28%; text-align: center;">
                                                <input type="text" class="form-control form-control-sm text-center" value="<?= strtoupper($personnel->middlename ?? '') ?>" readonly style="border-bottom: 1px solid #000; border-top: none; border-left: none; border-right: none; background: transparent;">
                                                <small>(Middle)</small>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td style="width: 25%; padding: 8px; border-left: 1px solid #000; vertical-align: top;">
                                </td>
                            </tr>
                        </table>
                        
                        <table style="width: 100%; border-collapse: collapse; border-top: 1px solid #000;">
                            <tr>
                                <td style="width: 20%; padding: 8px; border-right: 1px solid #000; vertical-align: top;">
                                    <span>3 DATE OF FILING:</span><br>
                                    <input type="text" class="form-control form-control-sm" value="<?= date('F d, Y') ?>" readonly style="margin-top: 5px; border-bottom: 1px solid #000; border-top: none; border-left: none; border-right: none; background: transparent;">
                                </td>
                                <td style="width: 40%; padding: 8px; border-right: 1px solid #000; vertical-align: top;">
                                    <span>4. POSITION:</span><br>
                                    <input type="text" class="form-control form-control-sm" value="<?= $personnel->position ?? '' ?>" readonly style="margin-top: 5px; border-bottom: 1px solid #000; border-top: none; border-left: none; border-right: none; background: transparent;">
                                </td>
                                <td style="width: 40%; padding: 8px; vertical-align: top;">
                                    <span>5. SALARY:</span><br>
                                    <select name="salary_grade" id="salary_grade" class="form-control form-control-sm" required style="margin-top: 5px;">
                                        <option value="">-- Select SG --</option>
                                        <?php for ($i = 1; $i <= 33; $i++): ?>
                                            <option value="<?= $i ?>" <?= ((isset($leave) && $leave->salary_grade == $i) || (!isset($leave) && isset($personnel->salary_grade) && $personnel->salary_grade == $i)) ? 'selected' : '' ?>>SG <?= $i ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </td>
                            </tr>
                        </table>

                        <!-- Section 6: Details of Application -->
                        <table style="width: 100%; border-collapse: collapse; border-top: 2px solid #000;">
                            <tr>
                                <td colspan="2" style="text-align: center; padding: 8px; background: #f0f0f0; border: 1px solid #000;">
                                    <strong style="font-size: 14px;">6. DETAILS OF APPLICATION</strong>
                                </td>
                            </tr>
                        </table>

                        <table style="width: 100%; border-collapse: collapse; border: 1px solid #000; border-top: none;">
                            <tr>
                                <!-- 6.A Type of Leave -->
                                <td style="width: 50%; padding: 10px; border-right: 1px solid #000; vertical-align: top;">
                                    <strong>6.A TYPE OF LEAVE TO BE AVAILED OF</strong><br><br>
                                    
                                    <div class="form-check">
                                        <input class="form-check-input leave-type-radio" type="checkbox" name="leave_type" id="vacation_leave" value="vacation_leave" <?= (isset($leave) && $leave->leave_type == 'vacation_leave') ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="vacation_leave">
                                            <small>Vacation Leave <em>(Sec. 51, Rule XVI, Omnibus Rules Implementing E.O. No. 292)</em></small>
                                        </label>
                                    </div>
                                    
                                    <div class="form-check">
                                        <input class="form-check-input leave-type-radio" type="checkbox" name="leave_type" id="mandatory_forced_leave" value="mandatory_forced_leave" <?= (isset($leave) && $leave->leave_type == 'mandatory_forced_leave') ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="mandatory_forced_leave">
                                            <small>Mandatory/Forced Leave<em>(Sec. 25, Rule XVI, Omnibus Rules Implementing E.O. No. 292)</em></small>
                                        </label>
                                    </div>
                                    
                                    <div class="form-check">
                                        <input class="form-check-input leave-type-radio" type="checkbox" name="leave_type" id="sick_leave" value="sick_leave" <?= (isset($leave) && $leave->leave_type == 'sick_leave') ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="sick_leave">
                                            <small>Sick Leave <em>(Sec. 43, Rule XVI, Omnibus Rules Implementing E.O. No. 292)</em></small>
                                        </label>
                                    </div>
                                    
                                    <div class="form-check">
                                        <input class="form-check-input leave-type-radio" type="checkbox" name="leave_type" id="maternity_leave" value="maternity_leave" <?= (isset($leave) && $leave->leave_type == 'maternity_leave') ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="maternity_leave">
                                            <small>Maternity Leave <em>(R.A. No. 11210 / IRR issued by CSC, DOLE and SSS)</em></small>
                                        </label>
                                    </div>
                                    
                                    <div class="form-check">
                                        <input class="form-check-input leave-type-radio" type="checkbox" name="leave_type" id="paternity_leave" value="paternity_leave" <?= (isset($leave) && $leave->leave_type == 'paternity_leave') ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="paternity_leave">
                                            <small>Paternity Leave <em>(R.A. No. 8187 / CSC MC No. 71, s. 1998, as amended)</em></small>
                                        </label>
                                    </div>
                                    
                                    <div class="form-check">
                                        <input class="form-check-input leave-type-radio" type="checkbox" name="leave_type" id="special_privilege_leave" value="special_privilege_leave" <?= (isset($leave) && $leave->leave_type == 'special_privilege_leave') ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="special_privilege_leave">
                                            <small>Special Privilege Leave <em>(Sec. 21, Rule XVI, Omnibus Rules Implementing E.O. No. 292)</em></small>
                                        </label>
                                    </div>
                                    
                                    <div class="form-check">
                                        <input class="form-check-input leave-type-radio" type="checkbox" name="leave_type" id="solo_parent_leave" value="solo_parent_leave" <?= (isset($leave) && $leave->leave_type == 'solo_parent_leave') ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="solo_parent_leave">
                                            <small>Solo Parent Leave <em>(RA No. 8972 / CSC MC No. 8, s. 2004)</em></small>
                                        </label>
                                    </div>
                                    
                                    <div class="form-check">
                                        <input class="form-check-input leave-type-radio" type="checkbox" name="leave_type" id="study_leave" value="study_leave" <?= (isset($leave) && $leave->leave_type == 'study_leave') ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="study_leave">
                                            <small>Study Leave <em>(Sec. 68, Rule XVI, Omnibus Rules Implementing E.O. No. 292)</em></small>
                                        </label>
                                    </div>
                                    
                                    <div class="form-check">
                                        <input class="form-check-input leave-type-radio" type="checkbox" name="leave_type" id="vawc_leave" value="vawc_leave" <?= (isset($leave) && $leave->leave_type == 'vawc_leave') ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="vawc_leave">
                                            <small>10-Day VAWC Leave <em>(RA No. 9262 / CSC MC No. 15, s. 2005)</em></small>
                                        </label>
                                    </div>
                                    
                                    <div class="form-check">
                                        <input class="form-check-input leave-type-radio" type="checkbox" name="leave_type" id="rehabilitation_privilege" value="rehabilitation_privilege" <?= (isset($leave) && $leave->leave_type == 'rehabilitation_privilege') ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="rehabilitation_privilege">
                                            <small>Rehabilitation Privilege <em>(Sec. 55, Rule XVI, Omnibus Rules Implementing E.O. No. 292)</em></small>
                                        </label>
                                    </div>
                                    
                                    <div class="form-check">
                                        <input class="form-check-input leave-type-radio" type="checkbox" name="leave_type" id="special_leave_benefits_women" value="special_leave_benefits_women" <?= (isset($leave) && $leave->leave_type == 'special_leave_benefits_women') ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="special_leave_benefits_women">
                                            <small>Special Leave Benefits for Women <em>(RA No. 9710 / CSC MC No. 25, s. 2010)</em></small>
                                        </label>
                                    </div>
                                    
                                    <div class="form-check">
                                        <input class="form-check-input leave-type-radio" type="checkbox" name="leave_type" id="special_emergency_calamity" value="special_emergency_calamity" <?= (isset($leave) && $leave->leave_type == 'special_emergency_calamity') ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="special_emergency_calamity">
                                            <small>Special Emergency (Calamity) Leave <em>(CSC MC No. 2, s. 2012, as amended)</em></small>
                                        </label>
                                    </div>
                                    
                                    <div class="form-check">
                                        <input class="form-check-input leave-type-radio" type="checkbox" name="leave_type" id="adoption_leave" value="adoption_leave" <?= (isset($leave) && $leave->leave_type == 'adoption_leave') ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="adoption_leave">
                                            <small>Adoption Leave <em>(R.A. No. 8552)</em></small>
                                        </label>
                                    </div>
                                    
                                    <div style="margin-top: 10px;">
                                        <small>Others:</small>
                                        <input type="text" name="leave_type_others" id="leave_type_others" class="form-control form-control-sm" style="border-bottom: 1px solid #000; border-top: none; border-left: none; border-right: none; background: transparent;" placeholder="________________________________" value="<?= isset($leave) ? $leave->leave_type_others : '' ?>">
                                    </div>
                                </td>

                                <!-- 6.B Details of Leave -->
                                <td style="width: 50%; padding: 10px; vertical-align: top;">
                                    <strong>6.B DETAILS OF LEAVE</strong><br><br>
                                    
                                    <!-- Instructions Panel -->
                                    <div id="leave_instructions_panel" class="alert alert-info mb-3" style="display: none; font-size: 11px;">
                                        <strong><i class="fa fa-info-circle"></i> Instructions:</strong>
                                        <p id="leave_instructions_text" class="mb-0 mt-1"></p>
                                    </div>
                                    
                                    <!-- Available Balance Display -->
                                    <div id="leave_balance_panel" class="alert alert-warning mb-3" style="display: none; font-size: 11px;">
                                        <strong><i class="fa fa-calculator"></i> Available Balance:</strong>
                                        <span id="leave_balance_text"></span>
                                    </div>
                                    
                                    <!-- Vacation/Special Privilege Leave -->
                                    <div id="vacation_details">
                                        <em>In case of Vacation/Special Privilege Leave:</em><br>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="vacation_within_ph" id="within_ph" value="1" <?= (isset($leave) && !empty($leave->vacation_special_within_ph)) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="within_ph">Within the Philippines</label>
                                            <input type="text" name="vacation_special_within_ph" class="form-control form-control-sm" style="border-bottom: 1px solid #000; border-top: none; border-left: none; border-right: none; background: transparent; display: inline-block; width: 60%;" value="<?= isset($leave) ? $leave->vacation_special_within_ph : '' ?>">
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="vacation_abroad" id="abroad" value="1" <?= (isset($leave) && !empty($leave->vacation_special_abroad)) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="abroad">Abroad (Specify)</label>
                                            <input type="text" name="vacation_special_abroad" class="form-control form-control-sm" style="border-bottom: 1px solid #000; border-top: none; border-left: none; border-right: none; background: transparent; display: inline-block; width: 60%;" value="<?= isset($leave) ? $leave->vacation_special_abroad : '' ?>">
                                        </div>
                                    </div>

                                    <!-- Sick Leave -->
                                    <div id="sick_details" style="margin-top: 10px;">
                                        <em>In case of Sick Leave:</em><br>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="sick_in_hospital_check" id="in_hospital" value="1" <?= (isset($leave) && !empty($leave->sick_in_hospital)) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="in_hospital">In Hospital (Specify Illness)</label>
                                            <input type="text" name="sick_in_hospital" class="form-control form-control-sm" style="border-bottom: 1px solid #000; border-top: none; border-left: none; border-right: none; background: transparent; display: inline-block; width: 55%;" value="<?= isset($leave) ? $leave->sick_in_hospital : '' ?>">
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="sick_out_patient_check" id="out_patient" value="1" <?= (isset($leave) && !empty($leave->sick_out_patient)) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="out_patient">Out Patient (Specify Illness)</label>
                                            <input type="text" name="sick_out_patient" class="form-control form-control-sm" style="border-bottom: 1px solid #000; border-top: none; border-left: none; border-right: none; background: transparent; display: inline-block; width: 55%;" value="<?= isset($leave) ? $leave->sick_out_patient : '' ?>">
                                        </div>
                                        <input type="text" name="sick_illness_details" class="form-control form-control-sm" style="border-bottom: 1px solid #000; border-top: none; border-left: none; border-right: none; background: transparent; margin-top: 5px;" placeholder="____________________________________________" value="<?= isset($leave) ? $leave->sick_illness_details : '' ?>">
                                    </div>

                                    <!-- Special Leave Benefits for Women -->
                                    <div id="women_details" style="margin-top: 10px;">
                                        <em>In case of Special Leave Benefits for Women:</em><br>
                                        <small>(Specify Illness)</small>
                                        <input type="text" name="special_women_illness" class="form-control form-control-sm" style="border-bottom: 1px solid #000; border-top: none; border-left: none; border-right: none; background: transparent;" placeholder="________________________________" value="<?= isset($leave) ? $leave->special_women_illness : '' ?>">
                                        <input type="text" name="special_women_illness_2" class="form-control form-control-sm" style="border-bottom: 1px solid #000; border-top: none; border-left: none; border-right: none; background: transparent; margin-top: 5px;" placeholder="____________________________________________" value="<?= isset($leave) ? $leave->special_women_illness_2 : '' ?>">
                                    </div>

                                    <!-- Study Leave -->
                                    <div id="study_details" style="margin-top: 10px;">
                                        <em>In case of Study Leave:</em><br>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="study_completion_masters" id="study_completion_masters" value="1" <?= (isset($leave) && $leave->study_completion_masters) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="study_completion_masters">Completion of Master's Degree</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="study_bar_review" id="study_bar_review" value="1" <?= (isset($leave) && $leave->study_bar_review) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="study_bar_review">BAR/Board Examination Review</label>
                                        </div>
                                    </div>

                                    <!-- Other Purpose -->
                                    <div style="margin-top: 10px;">
                                        <em>Other purpose:</em><br>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="other_purpose_monetization" id="other_purpose_monetization" value="1" <?= (isset($leave) && $leave->other_purpose_monetization) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="other_purpose_monetization">Monetization of Leave Credits</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="other_purpose_terminal_leave" id="other_purpose_terminal_leave" value="1" <?= (isset($leave) && $leave->other_purpose_terminal_leave) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="other_purpose_terminal_leave">Terminal Leave</label>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>

                        <!-- Section 6.C and 6.D -->
                        <table style="width: 100%; border-collapse: collapse; border: 1px solid #000; border-top: none;">
                            <tr>
                                <td style="width: 50%; padding: 10px; border-right: 1px solid #000; vertical-align: top;">
                                    <strong>6.C NUMBER OF WORKING DAYS APPLIED FOR</strong><br><br>
                                    <input type="number" name="working_days_applied" id="working_days_applied" class="form-control form-control-sm" style="border-bottom: 1px solid #000; border-top: none; border-left: none; border-right: none; background: transparent; width: 50%;" step="0.5" min="0.5" required value="<?= isset($leave) ? $leave->working_days_applied : '' ?>" placeholder="_______________">
                                    
                                    <br><br><strong>INCLUSIVE DATES</strong><br>
                                    <div style="display: flex; gap: 10px; margin-top: 5px;">
                                        <div style="flex: 1;">
                                            <input type="date" name="inclusive_date_from" id="inclusive_date_from" class="form-control form-control-sm" required value="<?= isset($leave) ? $leave->inclusive_date_from : '' ?>">
                                        </div>
                                        <div style="flex: 1;">
                                            <input type="date" name="inclusive_date_to" id="inclusive_date_to" class="form-control form-control-sm" required value="<?= isset($leave) ? $leave->inclusive_date_to : '' ?>">
                                        </div>
                                    </div>
                                </td>
                                <td style="width: 50%; padding: 10px; vertical-align: top;">
                                    <strong>6.D COMMUTATION</strong><br><br>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="commutation_not_requested" id="commutation_not_requested" value="1" <?= (!isset($leave) || !$leave->commutation_requested) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="commutation_not_requested">Not Requested</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="commutation_requested" id="commutation_requested" value="1" <?= (isset($leave) && $leave->commutation_requested) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="commutation_requested">Requested</label>
                                    </div>
                                    
                                    <div style="margin-top: 30px; text-align: right;">
                                        <p style="margin: 0;">_________________________________</p>
                                        <p style="margin: 0;"><small>(Signature of Applicant)</small></p>
                                    </div>
                                </td>
                            </tr>
                        </table>

                        <!-- Section 7: Details of Action on Application -->
                        <table style="width: 100%; border-collapse: collapse; border-top: 2px solid #000; margin-top: 10px;">
                            <tr>
                                <td colspan="2" style="text-align: center; padding: 8px; background: #f0f0f0; border: 1px solid #000;">
                                    <strong style="font-size: 14px;">7. DETAILS OF ACTION ON APPLICATION</strong>
                                </td>
                            </tr>
                        </table>

                        <table style="width: 100%; border-collapse: collapse; border: 1px solid #000; border-top: none;">
                            <tr>
                                <!-- 7.A Certification -->
                                <td style="width: 50%; padding: 10px; border-right: 1px solid #000; vertical-align: top;">
                                    <strong>7.A CERTIFICATION OF LEAVE CREDITS</strong><br><br>
                                    <p style="margin: 0;">As of <input type="text" style="border: none; border-bottom: 1px solid #000; width: 150px; background: transparent;" readonly></p>
                                    
                                    <table style="width: 100%; border-collapse: collapse; margin-top: 10px; border: 1px solid #000;">
                                        <tr>
                                            <td style="border: 1px solid #000; padding: 5px;"></td>
                                            <td style="border: 1px solid #000; padding: 5px; text-align: center;"><small>Vacation Leave</small></td>
                                            <td style="border: 1px solid #000; padding: 5px; text-align: center;"><small>Sick Leave</small></td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1px solid #000; padding: 5px;"><small><em>Total Earned</em></small></td>
                                            <td style="border: 1px solid #000; padding: 5px; text-align: center;" id="vl_earned">
                                                <?php 
                                                $vl = null; $sl = null;
                                                foreach ($leave_credits as $credit) {
                                                    if ($credit->leave_type == 'vacation') $vl = $credit;
                                                    if ($credit->leave_type == 'sick') $sl = $credit;
                                                }
                                                echo $vl ? number_format($vl->earned, 3) : '';
                                                ?>
                                            </td>
                                            <td style="border: 1px solid #000; padding: 5px; text-align: center;" id="sl_earned"><?= $sl ? number_format($sl->earned, 3) : '' ?></td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1px solid #000; padding: 5px;"><small><em>Less this application</em></small></td>
                                            <td style="border: 1px solid #000; padding: 5px; text-align: center;" id="vl_less"></td>
                                            <td style="border: 1px solid #000; padding: 5px; text-align: center;" id="sl_less"></td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1px solid #000; padding: 5px;"><small><em>Balance</em></small></td>
                                            <td style="border: 1px solid #000; padding: 5px; text-align: center;" id="vl_balance"><?= $vl ? number_format($vl->balance, 3) : '' ?></td>
                                            <td style="border: 1px solid #000; padding: 5px; text-align: center;" id="sl_balance"><?= $sl ? number_format($sl->balance, 3) : '' ?></td>
                                        </tr>
                                    </table>
                                    
                                    <div style="text-align: center; margin-top: 20px;">
                                        <p style="margin: 0;"><strong><u>FREDERICK R. FLORDELIZA</u></strong></p>
                                        <p style="margin: 0; font-size: 10px;">Administrative Officer IV - Human Resource Management Officer II</p>
                                    </div>
                                </td>

                                <!-- 7.B Recommendation -->
                                <td style="width: 50%; padding: 10px; vertical-align: top;">
                                    <strong>7.B RECOMMENDATION</strong><br><br>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="rec_approval" disabled>
                                        <label class="form-check-label" for="rec_approval">For approval</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="rec_disapproval" disabled>
                                        <label class="form-check-label" for="rec_disapproval">For disapproval due to</label>
                                        <input type="text" style="border: none; border-bottom: 1px solid #000; width: 100%; background: transparent;" disabled>
                                    </div>
                                    <input type="text" style="border: none; border-bottom: 1px solid #000; width: 100%; background: transparent; margin-top: 5px;" disabled>
                                    <input type="text" style="border: none; border-bottom: 1px solid #000; width: 100%; background: transparent; margin-top: 5px;" disabled>
                                    <input type="text" style="border: none; border-bottom: 1px solid #000; width: 100%; background: transparent; margin-top: 5px;" disabled>
                                    
                                    <div style="text-align: center; margin-top: 20px;">
                                        <p style="margin: 0;">_________________________________</p>
                                        <p style="margin: 0; font-size: 10px;">Authorized Official</p>
                                    </div>
                                </td>
                            </tr>
                        </table>

                        <table style="width: 100%; border-collapse: collapse; border: 1px solid #000; border-top: none;">
                            <tr>
                                <!-- 7.C Approved For -->
                                <td style="width: 50%; padding: 10px; border-right: 1px solid #000; vertical-align: top;">
                                    <strong>7.C APPROVED FOR:</strong><br><br>
                                    <p style="margin: 5px 0;"><input type="text" style="border: none; border-bottom: 1px solid #000; width: 50px; background: transparent;" disabled> days with pay</p>
                                    <p style="margin: 5px 0;"><input type="text" style="border: none; border-bottom: 1px solid #000; width: 50px; background: transparent;" disabled> days without pay</p>
                                    <p style="margin: 5px 0;"><input type="text" style="border: none; border-bottom: 1px solid #000; width: 50px; background: transparent;" disabled> others (Specify)</p>
                                </td>

                                <!-- 7.D Disapproved -->
                                <td style="width: 50%; padding: 10px; vertical-align: top;">
                                    <strong>7.D DISAPPROVED DUE TO:</strong><br><br>
                                    <input type="text" style="border: none; border-bottom: 1px solid #000; width: 100%; background: transparent;" disabled>
                                    <input type="text" style="border: none; border-bottom: 1px solid #000; width: 100%; background: transparent; margin-top: 10px;" disabled>
                                    <input type="text" style="border: none; border-bottom: 1px solid #000; width: 100%; background: transparent; margin-top: 10px;" disabled>
                                </td>
                            </tr>
                        </table>

                        <table style="width: 100%; border-collapse: collapse; border: 1px solid #000; border-top: none;">
                            <tr>
                                <td style="text-align: center; padding: 20px;">
                                    <p style="margin: 0;"><strong><u>GLINARD L. QUEZADA, MD, FPSGS, MBA-HA</u></strong></p>
                                    <p style="margin: 0; font-size: 11px;">Medical Center Chief I</p>
                                </td>
                            </tr>
                        </table>

                    </div>
                    <!-- End CS Form Container -->

                    <!-- Action Buttons -->
                    <div class="row mt-4">
                        <div class="col-12 text-center">
                            <a href="<?= site_url('leave_application') ?>" class="btn btn-secondary">
                                <i class="fa fa-arrow-left"></i> Back
                            </a>
                            <button type="button" class="btn btn-warning" onclick="saveDraft()">
                                <i class="fa fa-save"></i> Save as Draft
                            </button>
                            <button type="button" class="btn btn-success" onclick="submitApplication()">
                                <i class="fa fa-paper-plane"></i> Submit Application
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Leave type instructions
var leaveInstructions = {
    'vacation_leave': 'File 5 days in advance when possible. Indicate location (within PH or abroad) for travel authority and clearance purposes. Credits: +1.25 days/month (cumulative).',
    'mandatory_forced_leave': 'Annual 5-day vacation leave forfeited if not taken. Uses Vacation Leave balance (max 5 days). Cancelled leave due to exigency of service will not be deducted.',
    'sick_leave': 'File immediately upon return. If filed in advance or exceeding 5 days, attach medical certificate. If no medical consultation, execute an affidavit. Credits: +1.25 days/month (cumulative).',
    'maternity_leave': '105 days. Attach proof of pregnancy (ultrasound/doctor\'s certificate). Complete CS Form No. 6a if allocating credits.',
    'paternity_leave': '7 days. Attach birth certificate, medical certificate, and marriage contract.',
    'special_privilege_leave': '3 days/year (reset yearly). File at least 1 week prior except emergencies. Indicate location for travel authority purposes.',
    'solo_parent_leave': '7 days/year. File 5 days in advance when possible. Attach updated Solo Parent ID Card.',
    'study_leave': 'Up to 6 months. Meet agency requirements. Requires contract between agency head and employee.',
    'vawc_leave': '10 days. File in advance or immediately upon return. Attach BPO/TPO/PPO, certification, or police report with medical certificate.',
    'rehabilitation_privilege': 'Up to 6 months. Apply within 1 week of accident. Attach police report, medical certificate, and government physician concurrence.',
    'special_leave_benefits_women': 'Up to 2 months. File 5 days prior to surgery or immediately upon return. Attach medical certificate with clinical summary and operative details.',
    'special_emergency_calamity': 'Up to 5 days (straight or staggered within 30 days of calamity). Once per year. Head of office verifies residence in declared calamity area.',
    'adoption_leave': 'Attach authenticated Pre-Adoptive Placement Authority from DSWD.',
    'others': 'Specify the type of leave and attach required documentation.'
};

// Leave credits data from PHP
var leaveCredits = {
    'vacation': <?= isset($vl) ? $vl->balance : 0 ?>,
    'sick': <?= isset($sl) ? $sl->balance : 0 ?>,
    'special_privilege': <?= isset($leave_credits) ? (function() use ($leave_credits) { foreach($leave_credits as $c) { if($c->leave_type == 'special_privilege') return $c->balance; } return 0; })() : 0 ?>
};

$(document).ready(function() {
    // Handle leave type checkbox selection (only one can be selected at a time for main leave types)
    $('.leave-type-radio').change(function() {
        var $this = $(this);
        var leaveType = $this.val();
        
        // Uncheck all other leave type checkboxes when one is checked
        if ($this.is(':checked')) {
            $('.leave-type-radio').not($this).prop('checked', false);
        }
        
        // Show instructions for selected leave type
        if ($this.is(':checked') && leaveInstructions[leaveType]) {
            $('#leave_instructions_text').text(leaveInstructions[leaveType]);
            $('#leave_instructions_panel').show();
        } else {
            $('#leave_instructions_panel').hide();
        }
        
        // Show available balance for applicable leave types
        var balanceText = '';
        if ($this.is(':checked')) {
            if (leaveType === 'vacation_leave' || leaveType === 'mandatory_forced_leave') {
                var maxDays = leaveType === 'mandatory_forced_leave' ? Math.min(5, leaveCredits.vacation) : leaveCredits.vacation;
                balanceText = maxDays.toFixed(3) + ' days' + (leaveType === 'mandatory_forced_leave' ? ' (max 5 days from VL)' : ' (Vacation Leave)');
            } else if (leaveType === 'sick_leave') {
                balanceText = leaveCredits.sick.toFixed(3) + ' days (Sick Leave)';
            } else if (leaveType === 'special_privilege_leave') {
                balanceText = leaveCredits.special_privilege.toFixed(3) + ' days (resets yearly)';
            }
        }
        
        if (balanceText) {
            $('#leave_balance_text').text(balanceText);
            $('#leave_balance_panel').show();
        } else {
            $('#leave_balance_panel').hide();
        }
    });
    
    // Trigger change on page load if editing
    $('input[name="leave_type"]:checked').trigger('change');
    
    // Calculate working days when dates change
    $('#inclusive_date_from, #inclusive_date_to').change(function() {
        calculateWorkingDays();
    });
});

function calculateWorkingDays() {
    var fromDate = new Date($('#inclusive_date_from').val());
    var toDate = new Date($('#inclusive_date_to').val());
    
    if (fromDate && toDate && fromDate <= toDate) {
        var count = 0;
        var current = new Date(fromDate);
        
        while (current <= toDate) {
            var dayOfWeek = current.getDay();
            if (dayOfWeek !== 0 && dayOfWeek !== 6) { // Exclude weekends
                count++;
            }
            current.setDate(current.getDate() + 1);
        }
        
        $('#working_days_applied').val(count);
    }
}

function saveDraft() {
    $('#submit_type').val('draft');
    if (validateForm(false)) {
        $('#leaveApplicationForm').submit();
    }
}

function submitApplication() {
    $('#submit_type').val('submit');
    if (validateForm(true)) {
        if (confirm('Are you sure you want to submit this leave application? Once submitted, you cannot edit it.')) {
            $('#leaveApplicationForm').submit();
        }
    }
}

function validateForm(strict) {
    var isValid = true;
    var errors = [];
    
    if (!$('#office_department').val()) {
        errors.push('Please select Office/Department');
        isValid = false;
    }
    
    if (!$('#salary_grade').val()) {
        errors.push('Please select Salary Grade');
        isValid = false;
    }
    
    if (!$('input[name="leave_type"]:checked').val()) {
        errors.push('Please select Type of Leave');
        isValid = false;
    }
    
    if (strict) {
        if (!$('#working_days_applied').val() || parseFloat($('#working_days_applied').val()) <= 0) {
            errors.push('Please enter number of working days');
            isValid = false;
        }
        
        if (!$('#inclusive_date_from').val() || !$('#inclusive_date_to').val()) {
            errors.push('Please enter inclusive dates');
            isValid = false;
        }
    }
    
    if (!isValid) {
        alert('Please fix the following errors:\n\n' + errors.join('\n'));
    }
    
    return isValid;
}

function printForm() {
    window.open('<?= site_url('leave_application/print_form/' . (isset($leave) ? $leave->id : '0')) ?>', '_blank');
}
</script>

<style>
.cs-form-container {
    font-family: 'Times New Roman', serif;
    font-size: 12px;
}

.cs-form-container .form-check-label {
    font-size: 11px;
}

.cs-form-container h6 {
    font-size: 12px;
    margin-bottom: 8px;
}

.cs-form-container .form-control-sm {
    font-size: 11px;
}

.cs-form-container .table-sm td, .cs-form-container .table-sm th {
    font-size: 10px;
    padding: 3px 5px;
}

@media print {
    .page-header, .card-header, .btn, .no-print {
        display: none !important;
    }
    
    .cs-form-container {
        border: 1px solid #000 !important;
        max-width: 100% !important;
    }
}
</style>
