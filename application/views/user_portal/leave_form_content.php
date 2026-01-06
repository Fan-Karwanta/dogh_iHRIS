<?php
// Get leave credits for JS
$vl = null; $sl = null; $spl = null;
foreach ($leave_credits as $credit) {
    if ($credit->leave_type == 'vacation') $vl = $credit;
    if ($credit->leave_type == 'sick') $sl = $credit;
    if ($credit->leave_type == 'special_privilege') $spl = $credit;
}
?>

<div class="card-custom">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-file-signature mr-2"></i>
            <?= isset($leave) ? 'Edit' : 'New' ?> Leave Application (CS Form No. 6)
        </h5>
    </div>
    <div class="card-body">
        <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger"><?= $this->session->flashdata('error') ?></div>
        <?php endif; ?>

        <form id="leaveForm" action="<?= site_url('user/save_leave') ?>" method="POST">
            <?php if (isset($leave)): ?>
                <input type="hidden" name="leave_id" value="<?= $leave->id ?>">
            <?php endif; ?>
            <input type="hidden" name="submit_type" id="submit_type" value="draft">

            <!-- Basic Information -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <label class="font-weight-bold">1. Office/Department <span class="text-danger">*</span></label>
                    <select name="office_department" id="office_department" class="form-control" required>
                        <option value="">-- Select --</option>
                        <option value="Medical" <?= (isset($leave) && $leave->office_department == 'Medical') ? 'selected' : '' ?>>Medical</option>
                        <option value="Nursing" <?= (isset($leave) && $leave->office_department == 'Nursing') ? 'selected' : '' ?>>Nursing</option>
                        <option value="Ancillary" <?= (isset($leave) && $leave->office_department == 'Ancillary') ? 'selected' : '' ?>>Ancillary</option>
                        <option value="Administrative" <?= (isset($leave) && $leave->office_department == 'Administrative') ? 'selected' : '' ?>>Administrative</option>
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="font-weight-bold">2. Name</label>
                    <div class="row">
                        <div class="col-4">
                            <input type="text" class="form-control text-center" value="<?= strtoupper($personnel->lastname) ?>" readonly>
                            <small class="text-muted">(Last)</small>
                        </div>
                        <div class="col-4">
                            <input type="text" class="form-control text-center" value="<?= strtoupper($personnel->firstname) ?>" readonly>
                            <small class="text-muted">(First)</small>
                        </div>
                        <div class="col-4">
                            <input type="text" class="form-control text-center" value="<?= strtoupper($personnel->middlename ?? '') ?>" readonly>
                            <small class="text-muted">(Middle)</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="font-weight-bold">3. Date of Filing</label>
                    <input type="text" class="form-control" value="<?= date('F d, Y') ?>" readonly>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="font-weight-bold">4. Position</label>
                    <input type="text" class="form-control" value="<?= $personnel->position ?? '' ?>" readonly>
                </div>
                <div class="col-md-6">
                    <label class="font-weight-bold">5. Salary Grade <span class="text-danger">*</span></label>
                    <select name="salary_grade" id="salary_grade" class="form-control" required>
                        <option value="">-- Select SG --</option>
                        <?php for ($i = 1; $i <= 33; $i++): ?>
                            <option value="<?= $i ?>" <?= ((isset($leave) && $leave->salary_grade == $i) || (!isset($leave) && isset($personnel->salary_grade) && $personnel->salary_grade == $i)) ? 'selected' : '' ?>>SG <?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>

            <hr>
            <h6 class="font-weight-bold mb-3">6. DETAILS OF APPLICATION</h6>

            <div class="row">
                <!-- 6.A Type of Leave -->
                <div class="col-md-6">
                    <label class="font-weight-bold">6.A Type of Leave <span class="text-danger">*</span></label>
                    
                    <div class="form-check">
                        <input class="form-check-input leave-type" type="radio" name="leave_type" id="vacation_leave" value="vacation_leave" <?= (isset($leave) && $leave->leave_type == 'vacation_leave') ? 'checked' : '' ?>>
                        <label class="form-check-label" for="vacation_leave">Vacation Leave</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input leave-type" type="radio" name="leave_type" id="mandatory_forced_leave" value="mandatory_forced_leave" <?= (isset($leave) && $leave->leave_type == 'mandatory_forced_leave') ? 'checked' : '' ?>>
                        <label class="form-check-label" for="mandatory_forced_leave">Mandatory/Forced Leave</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input leave-type" type="radio" name="leave_type" id="sick_leave" value="sick_leave" <?= (isset($leave) && $leave->leave_type == 'sick_leave') ? 'checked' : '' ?>>
                        <label class="form-check-label" for="sick_leave">Sick Leave</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input leave-type" type="radio" name="leave_type" id="maternity_leave" value="maternity_leave" <?= (isset($leave) && $leave->leave_type == 'maternity_leave') ? 'checked' : '' ?>>
                        <label class="form-check-label" for="maternity_leave">Maternity Leave (105 days)</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input leave-type" type="radio" name="leave_type" id="paternity_leave" value="paternity_leave" <?= (isset($leave) && $leave->leave_type == 'paternity_leave') ? 'checked' : '' ?>>
                        <label class="form-check-label" for="paternity_leave">Paternity Leave (7 days)</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input leave-type" type="radio" name="leave_type" id="special_privilege_leave" value="special_privilege_leave" <?= (isset($leave) && $leave->leave_type == 'special_privilege_leave') ? 'checked' : '' ?>>
                        <label class="form-check-label" for="special_privilege_leave">Special Privilege Leave (3 days)</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input leave-type" type="radio" name="leave_type" id="solo_parent_leave" value="solo_parent_leave" <?= (isset($leave) && $leave->leave_type == 'solo_parent_leave') ? 'checked' : '' ?>>
                        <label class="form-check-label" for="solo_parent_leave">Solo Parent Leave (7 days)</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input leave-type" type="radio" name="leave_type" id="study_leave" value="study_leave" <?= (isset($leave) && $leave->leave_type == 'study_leave') ? 'checked' : '' ?>>
                        <label class="form-check-label" for="study_leave">Study Leave (up to 6 months)</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input leave-type" type="radio" name="leave_type" id="vawc_leave" value="vawc_leave" <?= (isset($leave) && $leave->leave_type == 'vawc_leave') ? 'checked' : '' ?>>
                        <label class="form-check-label" for="vawc_leave">10-Day VAWC Leave</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input leave-type" type="radio" name="leave_type" id="rehabilitation_privilege" value="rehabilitation_privilege" <?= (isset($leave) && $leave->leave_type == 'rehabilitation_privilege') ? 'checked' : '' ?>>
                        <label class="form-check-label" for="rehabilitation_privilege">Rehabilitation Privilege</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input leave-type" type="radio" name="leave_type" id="special_leave_benefits_women" value="special_leave_benefits_women" <?= (isset($leave) && $leave->leave_type == 'special_leave_benefits_women') ? 'checked' : '' ?>>
                        <label class="form-check-label" for="special_leave_benefits_women">Special Leave Benefits for Women</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input leave-type" type="radio" name="leave_type" id="special_emergency_calamity" value="special_emergency_calamity" <?= (isset($leave) && $leave->leave_type == 'special_emergency_calamity') ? 'checked' : '' ?>>
                        <label class="form-check-label" for="special_emergency_calamity">Special Emergency (Calamity) Leave</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input leave-type" type="radio" name="leave_type" id="adoption_leave" value="adoption_leave" <?= (isset($leave) && $leave->leave_type == 'adoption_leave') ? 'checked' : '' ?>>
                        <label class="form-check-label" for="adoption_leave">Adoption Leave</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input leave-type" type="radio" name="leave_type" id="others" value="others" <?= (isset($leave) && $leave->leave_type == 'others') ? 'checked' : '' ?>>
                        <label class="form-check-label" for="others">Others:</label>
                        <input type="text" name="leave_type_others" id="leave_type_others" class="form-control form-control-sm mt-1" placeholder="Specify..." value="<?= isset($leave) ? $leave->leave_type_others : '' ?>">
                    </div>
                </div>

                <!-- 6.B Details of Leave -->
                <div class="col-md-6">
                    <label class="font-weight-bold">6.B Details of Leave</label>
                    
                    <!-- Instructions Panel -->
                    <div id="instructions_panel" class="alert alert-info mb-3" style="display: none;">
                        <strong><i class="fas fa-info-circle"></i> Instructions:</strong>
                        <p id="instructions_text" class="mb-0 mt-1" style="font-size: 12px;"></p>
                    </div>
                    
                    <!-- Balance Panel -->
                    <div id="balance_panel" class="alert alert-warning mb-3" style="display: none;">
                        <strong><i class="fas fa-calculator"></i> Available:</strong>
                        <span id="balance_text"></span>
                    </div>

                    <!-- Vacation/SPL Details -->
                    <div class="leave-details" id="vacation_details" style="display: none;">
                        <p class="text-muted mb-2"><em>In case of Vacation/Special Privilege Leave:</em></p>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="vacation_location" id="within_ph" value="within_ph">
                            <label class="form-check-label" for="within_ph">Within the Philippines</label>
                        </div>
                        <input type="text" name="vacation_special_within_ph" class="form-control form-control-sm mb-2" placeholder="Specify location..." value="<?= isset($leave) ? $leave->vacation_special_within_ph : '' ?>">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="vacation_location" id="abroad" value="abroad">
                            <label class="form-check-label" for="abroad">Abroad</label>
                        </div>
                        <input type="text" name="vacation_special_abroad" class="form-control form-control-sm" placeholder="Specify country..." value="<?= isset($leave) ? $leave->vacation_special_abroad : '' ?>">
                    </div>

                    <!-- Sick Leave Details -->
                    <div class="leave-details" id="sick_details" style="display: none;">
                        <p class="text-muted mb-2"><em>In case of Sick Leave:</em></p>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="sick_location" id="in_hospital" value="in_hospital">
                            <label class="form-check-label" for="in_hospital">In Hospital</label>
                        </div>
                        <input type="text" name="sick_in_hospital" class="form-control form-control-sm mb-2" placeholder="Specify illness..." value="<?= isset($leave) ? $leave->sick_in_hospital : '' ?>">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="sick_location" id="out_patient" value="out_patient">
                            <label class="form-check-label" for="out_patient">Out Patient</label>
                        </div>
                        <input type="text" name="sick_out_patient" class="form-control form-control-sm" placeholder="Specify illness..." value="<?= isset($leave) ? $leave->sick_out_patient : '' ?>">
                    </div>

                    <!-- Women Benefits Details -->
                    <div class="leave-details" id="women_details" style="display: none;">
                        <p class="text-muted mb-2"><em>In case of Special Leave Benefits for Women:</em></p>
                        <label>Specify Illness:</label>
                        <input type="text" name="special_women_illness" class="form-control form-control-sm" placeholder="Specify illness..." value="<?= isset($leave) ? $leave->special_women_illness : '' ?>">
                    </div>

                    <!-- Study Leave Details -->
                    <div class="leave-details" id="study_details" style="display: none;">
                        <p class="text-muted mb-2"><em>In case of Study Leave:</em></p>
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
                    <div class="mt-3">
                        <p class="text-muted mb-2"><em>Other Purpose:</em></p>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="other_purpose_monetization" id="other_purpose_monetization" value="1" <?= (isset($leave) && $leave->other_purpose_monetization) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="other_purpose_monetization">Monetization of Leave Credits</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="other_purpose_terminal_leave" id="other_purpose_terminal_leave" value="1" <?= (isset($leave) && $leave->other_purpose_terminal_leave) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="other_purpose_terminal_leave">Terminal Leave</label>
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            <div class="row mb-4">
                <!-- 6.C Working Days -->
                <div class="col-md-6">
                    <label class="font-weight-bold">6.C Number of Working Days <span class="text-danger">*</span></label>
                    <input type="number" name="working_days_applied" id="working_days_applied" class="form-control" step="0.5" min="0.5" required value="<?= isset($leave) ? $leave->working_days_applied : '' ?>" placeholder="Enter number of days">
                    
                    <label class="font-weight-bold mt-3">Inclusive Dates <span class="text-danger">*</span></label>
                    <div class="row">
                        <div class="col-6">
                            <label>From:</label>
                            <input type="date" name="inclusive_date_from" id="inclusive_date_from" class="form-control" required value="<?= isset($leave) ? $leave->inclusive_date_from : '' ?>">
                        </div>
                        <div class="col-6">
                            <label>To:</label>
                            <input type="date" name="inclusive_date_to" id="inclusive_date_to" class="form-control" required value="<?= isset($leave) ? $leave->inclusive_date_to : '' ?>">
                        </div>
                    </div>
                </div>

                <!-- 6.D Commutation -->
                <div class="col-md-6">
                    <label class="font-weight-bold">6.D Commutation</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="commutation_requested" id="commutation_no" value="0" <?= (!isset($leave) || !$leave->commutation_requested) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="commutation_no">Not Requested</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="commutation_requested" id="commutation_yes" value="1" <?= (isset($leave) && $leave->commutation_requested) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="commutation_yes">Requested</label>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="text-center mt-4">
                <a href="<?= site_url('user/leave_applications') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
                <button type="button" class="btn btn-warning" onclick="saveDraft()">
                    <i class="fas fa-save mr-1"></i> Save as Draft
                </button>
                <button type="button" class="btn btn-success" onclick="submitApplication()">
                    <i class="fas fa-paper-plane mr-1"></i> Submit Application
                </button>
            </div>
        </form>
    </div>
</div>

<script>
var leaveInstructions = {
    'vacation_leave': 'File 5 days in advance when possible. Indicate location (within PH or abroad) for travel authority.',
    'mandatory_forced_leave': 'Max 5 days from VL balance. Forfeited if not taken during the year.',
    'sick_leave': 'File immediately upon return. Medical certificate required if >5 days.',
    'maternity_leave': '105 days. Attach proof of pregnancy.',
    'paternity_leave': '7 days. Attach birth certificate and marriage contract.',
    'special_privilege_leave': '3 days/year. File 1 week prior except emergencies.',
    'solo_parent_leave': '7 days/year. Attach Solo Parent ID Card.',
    'study_leave': 'Up to 6 months. Requires contract with agency.',
    'vawc_leave': '10 days. Attach protection order or certification.',
    'rehabilitation_privilege': 'Up to 6 months. Apply within 1 week of accident.',
    'special_leave_benefits_women': 'Up to 2 months. Attach medical certificate.',
    'special_emergency_calamity': 'Up to 5 days. Once per year.',
    'adoption_leave': 'Attach Pre-Adoptive Placement Authority from DSWD.',
    'others': 'Specify the type and attach required documents.'
};

var leaveCredits = {
    'vacation': <?= $vl ? $vl->balance : 0 ?>,
    'sick': <?= $sl ? $sl->balance : 0 ?>,
    'special_privilege': <?= $spl ? $spl->balance : 0 ?>
};

$(document).ready(function() {
    $('.leave-type').change(function() {
        var type = $(this).val();
        
        // Hide all details
        $('.leave-details').hide();
        
        // Show relevant details
        if (type == 'vacation_leave' || type == 'special_privilege_leave' || type == 'mandatory_forced_leave') {
            $('#vacation_details').show();
        } else if (type == 'sick_leave') {
            $('#sick_details').show();
        } else if (type == 'special_leave_benefits_women') {
            $('#women_details').show();
        } else if (type == 'study_leave') {
            $('#study_details').show();
        }
        
        // Show instructions
        if (leaveInstructions[type]) {
            $('#instructions_text').text(leaveInstructions[type]);
            $('#instructions_panel').show();
        } else {
            $('#instructions_panel').hide();
        }
        
        // Show balance
        var balanceText = '';
        if (type == 'vacation_leave' || type == 'mandatory_forced_leave') {
            var max = type == 'mandatory_forced_leave' ? Math.min(5, leaveCredits.vacation) : leaveCredits.vacation;
            balanceText = max.toFixed(3) + ' days' + (type == 'mandatory_forced_leave' ? ' (max 5 from VL)' : ' (VL)');
        } else if (type == 'sick_leave') {
            balanceText = leaveCredits.sick.toFixed(3) + ' days (SL)';
        } else if (type == 'special_privilege_leave') {
            balanceText = leaveCredits.special_privilege.toFixed(3) + ' days (SPL)';
        }
        
        if (balanceText) {
            $('#balance_text').text(balanceText);
            $('#balance_panel').show();
        } else {
            $('#balance_panel').hide();
        }
    });
    
    // Trigger on load
    $('input[name="leave_type"]:checked').trigger('change');
    
    // Calculate working days
    $('#inclusive_date_from, #inclusive_date_to').change(function() {
        var from = new Date($('#inclusive_date_from').val());
        var to = new Date($('#inclusive_date_to').val());
        if (from && to && from <= to) {
            var count = 0;
            var current = new Date(from);
            while (current <= to) {
                var day = current.getDay();
                if (day !== 0 && day !== 6) count++;
                current.setDate(current.getDate() + 1);
            }
            $('#working_days_applied').val(count);
        }
    });
});

function saveDraft() {
    $('#submit_type').val('draft');
    if (validateForm(false)) {
        $('#leaveForm').submit();
    }
}

function submitApplication() {
    $('#submit_type').val('submit');
    if (validateForm(true)) {
        if (confirm('Submit this leave application? Once submitted, you cannot edit it.')) {
            $('#leaveForm').submit();
        }
    }
}

function validateForm(strict) {
    var errors = [];
    
    if (!$('#office_department').val()) errors.push('Select Office/Department');
    if (!$('#salary_grade').val()) errors.push('Select Salary Grade');
    if (!$('input[name="leave_type"]:checked').val()) errors.push('Select Type of Leave');
    
    if (strict) {
        if (!$('#working_days_applied').val() || parseFloat($('#working_days_applied').val()) <= 0) {
            errors.push('Enter number of working days');
        }
        if (!$('#inclusive_date_from').val() || !$('#inclusive_date_to').val()) {
            errors.push('Enter inclusive dates');
        }
    }
    
    if (errors.length > 0) {
        alert('Please fix:\n\n' + errors.join('\n'));
        return false;
    }
    return true;
}
</script>
