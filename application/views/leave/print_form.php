<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CS Form No. 6 - Application for Leave</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Times New Roman', serif;
            font-size: 11px;
            line-height: 1.3;
            padding: 10px;
        }
        
        .form-container {
            max-width: 8.5in;
            margin: 0 auto;
            border: 2px solid #000;
            padding: 15px;
        }
        
        .header {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
        }
        
        .header-logo {
            width: 100px;
            text-align: center;
            padding: 0 10px;
        }
        
        .header-logo img {
            max-height: 70px;
            max-width: 70px;
        }
        
        .header-center {
            text-align: center;
            padding: 0 20px;
        }
        
        .header-right {
            width: 100px;
            text-align: center;
            font-size: 9px;
            padding: 0 10px;
        }
        
        .header-right img {
            max-height: 70px;
            max-width: 70px;
        }
        
        .form-title {
            font-size: 16px;
            font-weight: bold;
            margin-top: 5px;
            margin-left: 12rem;
        }
        
        .form-number {
            font-size: 9px;
            font-style: italic;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .info-row {
            display: flex;
            border-top: 1px solid #000;
            padding: 5px 0;
        }
        
        .info-cell {
            flex: 1;
            padding: 0 5px;
        }
        
        .underline {
            border-bottom: 1px solid #000;
            min-width: 100px;
            display: inline-block;
            text-align: center;
        }
        
        .section-header {
            background: #f0f0f0;
            text-align: center;
            padding: 5px;
            font-weight: bold;
            border: 1px solid #000;
        }
        
        .details-section {
            display: flex;
            border: 1px solid #000;
            border-top: none;
        }
        
        .details-left, .details-right {
            width: 50%;
            padding: 8px;
        }
        
        .details-left {
            border-right: 1px solid #000;
        }
        
        .checkbox {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 1px solid #000;
            margin-right: 5px;
            vertical-align: middle;
            text-align: center;
            line-height: 10px;
            font-size: 10px;
        }
        
        .checkbox.checked::after {
            content: '✓';
        }
        
        .leave-item {
            margin-bottom: 3px;
        }
        
        .leave-item small {
            font-size: 9px;
            font-style: italic;
        }
        
        .sub-section {
            margin-top: 10px;
            padding-top: 5px;
        }
        
        .signature-line {
            border-top: 1px solid #000;
            text-align: center;
            padding-top: 3px;
            margin-top: 20px;
        }
        
        .action-section {
            display: flex;
            border: 1px solid #000;
            border-top: none;
        }
        
        .action-left, .action-right {
            width: 50%;
            padding: 8px;
        }
        
        .action-left {
            border-right: 1px solid #000;
        }
        
        .credits-table {
            width: 100%;
            border-collapse: collapse;
            margin: 5px 0;
            font-size: 10px;
        }
        
        .credits-table th, .credits-table td {
            border: 1px solid #000;
            padding: 3px;
            text-align: center;
        }
        
        .final-approval {
            text-align: center;
            padding: 15px;
            border: 1px solid #000;
            border-top: none;
        }
        
        @media print {
            body {
                padding: 0;
            }
            
            .form-container {
                border: 1px solid #000;
            }
            
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 30px; font-size: 14px; cursor: pointer;">
            <i class="fa fa-print"></i> Print Form
        </button>
        <button onclick="window.close()" style="padding: 10px 30px; font-size: 14px; cursor: pointer; margin-left: 10px;">
            Close
        </button>
    </div>

    <div class="form-container">
        <!-- Civil Service Form No. 6 - Upper Left Corner -->
        <div style="text-align: left; padding: 2px 5px;">
            <span style="font-size: 9px; font-style: italic;">Civil Service Form No. 6</span><br>
            <span style="font-size: 9px; font-style: italic;">Revised 2020</span>
        </div>
        
        <!-- Header with Logos -->
        <div class="header">
            <div class="header-logo">
                <img src="<?= base_url('assets/img/doh_logo1.png') ?>" alt="DOH Logo">
            </div>
            <div class="header-center">
                <p>Republic of the Philippines</p>
                <p><strong>Department of Health</strong></p>
                <p><strong>DAVAO OCCIDENTAL GENERAL HOSPITAL</strong></p>
            </div>
            <div class="header-right">
                <img src="<?= base_url('assets/img/dogh_logo.png') ?>" alt="DOGH Logo">
            </div>
        </div>
        
        <!-- Title and Date Received Row -->
        <table style="width: 100%;">
            <tr>
                <td style="width: 65%; text-align: center; padding: 5px 0;">
                    <p class="form-title">APPLICATION FOR LEAVE</p>
                </td>
                <td style="width: 25%; text-align: left; font-size: 10px; vertical-align: middle;">
                    <span>DATE RECEIVED: <span class="underline" style="min-width: 100px;"><?= $leave->date_received ? date('m/d/Y', strtotime($leave->date_received)) : '' ?></span></span>
                </td>
            </tr>
        </table>

        <!-- Basic Info Section - Row 1: Office/Department and Name -->
        <table style="border-top: 1px solid #000;">
            <tr>
                <td width="20%" style="padding: 8px; border-right: 1px solid #000; vertical-align: top;">
                    <span>1. OFFICE/DEPARTMENT</span><br>
                    <span class="underline" style="width: 90%;"><?= $leave->office_department ?></span>
                </td>
                <td width="55%" style="padding: 8px; vertical-align: top;">
                    <table style="width: 100%;">
                        <tr>
                            <td style="width: 15%; vertical-align: top;">
                                <span>2. NAME:</span>
                            </td>
                            <td style="width: 28%; text-align: center;">
                                <span style="display: block; border-bottom: 1px solid #000;"><?= strtoupper($leave->lastname) ?></span>
                                <small style="font-size: 9px;">(Last)</small>
                            </td>
                            <td style="width: 28%; text-align: center;">
                                <span style="display: block; border-bottom: 1px solid #000;"><?= strtoupper($leave->firstname) ?></span>
                                <small style="font-size: 9px;">(First)</small>
                            </td>
                            <td style="width: 28%; text-align: center;">
                                <span style="display: block; border-bottom: 1px solid #000;"><?= strtoupper($leave->middlename ?? '') ?></span>
                                <small style="font-size: 9px;">(Middle)</small>
                            </td>
                        </tr>
                    </table>
                </td>
                <td width="25%" style="padding: 8px; border-left: 1px solid #000; vertical-align: top;">
                </td>
            </tr>
        </table>

        <!-- Basic Info Section - Row 2: Date of Filing, Position, Salary -->
        <table style="border-top: 1px solid #000;">
            <tr>
                <td width="20%" style="padding: 8px; border-right: 1px solid #000; vertical-align: top;">
                    <span>3 DATE OF FILING:</span><br>
                    <span class="underline" style="width: 90%;"><?= date('F d, Y', strtotime($leave->date_of_filing)) ?></span>
                </td>
                <td width="40%" style="padding: 8px; border-right: 1px solid #000; vertical-align: top;">
                    <span>4. POSITION:</span><br>
                    <span class="underline" style="width: 90%;"><?= $leave->position ?? '' ?></span>
                </td>
                <td width="40%" style="padding: 8px; vertical-align: top;">
                    <span>5. SALARY:</span><br>
                    <span class="underline" style="width: 90%;">SG <?= $leave->salary_grade ?></span>
                </td>
            </tr>
        </table>

        <!-- Section 6: Details of Application -->
        <div class="section-header">6. DETAILS OF APPLICATION</div>
        
        <div class="details-section">
            <div class="details-left">
                <strong>6.A TYPE OF LEAVE TO BE AVAILED OF</strong>
                
                <div class="leave-item">
                    <span class="checkbox <?= $leave->leave_type == 'vacation_leave' ? 'checked' : '' ?>"></span>
                    Vacation Leave <small>(Sec. 51, Rule XVI, Omnibus Rules Implementing E.O. No. 292)</small>
                </div>
                
                <div class="leave-item">
                    <span class="checkbox <?= $leave->leave_type == 'mandatory_forced_leave' ? 'checked' : '' ?>"></span>
                    Mandatory/Forced Leave <small>(Sec. 25, Rule XVI, Omnibus Rules Implementing E.O. No. 292)</small>
                </div>
                
                <div class="leave-item">
                    <span class="checkbox <?= $leave->leave_type == 'sick_leave' ? 'checked' : '' ?>"></span>
                    Sick Leave <small>(Sec. 43, Rule XVI, Omnibus Rules Implementing E.O. No. 292)</small>
                </div>
                
                <div class="leave-item">
                    <span class="checkbox <?= $leave->leave_type == 'maternity_leave' ? 'checked' : '' ?>"></span>
                    Maternity Leave <small>(R.A. No. 11210 / IRR issued by CSC, DOLE and SSS)</small>
                </div>
                
                <div class="leave-item">
                    <span class="checkbox <?= $leave->leave_type == 'paternity_leave' ? 'checked' : '' ?>"></span>
                    Paternity Leave <small>(R.A. No. 8187 / CSC MC No. 71, s. 1998, as amended)</small>
                </div>
                
                <div class="leave-item">
                    <span class="checkbox <?= $leave->leave_type == 'special_privilege_leave' ? 'checked' : '' ?>"></span>
                    Special Privilege Leave <small>(Sec. 21, Rule XVI, Omnibus Rules Implementing E.O. No. 292)</small>
                </div>
                
                <div class="leave-item">
                    <span class="checkbox <?= $leave->leave_type == 'solo_parent_leave' ? 'checked' : '' ?>"></span>
                    Solo Parent Leave <small>(RA No. 8972 / CSC MC No. 8, s. 2004)</small>
                </div>
                
                <div class="leave-item">
                    <span class="checkbox <?= $leave->leave_type == 'study_leave' ? 'checked' : '' ?>"></span>
                    Study Leave <small>(Sec. 68, Rule XVI, Omnibus Rules Implementing E.O. No. 292)</small>
                </div>
                
                <div class="leave-item">
                    <span class="checkbox <?= $leave->leave_type == 'vawc_leave' ? 'checked' : '' ?>"></span>
                    10-Day VAWC Leave <small>(RA No. 9262 / CSC MC No. 15, s. 2005)</small>
                </div>
                
                <div class="leave-item">
                    <span class="checkbox <?= $leave->leave_type == 'rehabilitation_privilege' ? 'checked' : '' ?>"></span>
                    Rehabilitation Privilege <small>(Sec. 55, Rule XVI, Omnibus Rules Implementing E.O. No. 292)</small>
                </div>
                
                <div class="leave-item">
                    <span class="checkbox <?= $leave->leave_type == 'special_leave_benefits_women' ? 'checked' : '' ?>"></span>
                    Special Leave Benefits for Women <small>(RA No. 9710 / CSC MC No. 25, s. 2010)</small>
                </div>
                
                <div class="leave-item">
                    <span class="checkbox <?= $leave->leave_type == 'special_emergency_calamity' ? 'checked' : '' ?>"></span>
                    Special Emergency (Calamity) Leave <small>(CSC MC No. 2, s. 2012, as amended)</small>
                </div>
                
                <div class="leave-item">
                    <span class="checkbox <?= $leave->leave_type == 'adoption_leave' ? 'checked' : '' ?>"></span>
                    Adoption Leave <small>(R.A. No. 8552)</small>
                </div>
                
                <div class="leave-item">
                    <span class="checkbox <?= $leave->leave_type == 'others' ? 'checked' : '' ?>"></span>
                    Others: <span class="underline" style="min-width: 150px;"><?= $leave->leave_type_others ?? '' ?></span>
                </div>
            </div>
            
            <div class="details-right">
                <strong>6.B DETAILS OF LEAVE</strong>
                
                <div class="sub-section">
                    <em>In case of Vacation/Special Privilege Leave:</em><br>
                    <span class="checkbox <?= !empty($leave->vacation_special_within_ph) ? 'checked' : '' ?>"></span>
                    Within the Philippines <span class="underline" style="min-width: 100px;"><?= $leave->vacation_special_within_ph ?? '' ?></span><br>
                    <span class="checkbox <?= !empty($leave->vacation_special_abroad) ? 'checked' : '' ?>"></span>
                    Abroad (Specify) <span class="underline" style="min-width: 100px;"><?= $leave->vacation_special_abroad ?? '' ?></span>
                </div>
                
                <div class="sub-section">
                    <em>In case of Sick Leave:</em><br>
                    <span class="checkbox <?= !empty($leave->sick_in_hospital) ? 'checked' : '' ?>"></span>
                    In Hospital (Specify Illness) <span class="underline" style="min-width: 80px;"><?= $leave->sick_in_hospital ?? '' ?></span><br>
                    <span class="checkbox <?= !empty($leave->sick_out_patient) ? 'checked' : '' ?>"></span>
                    Out Patient (Specify Illness) <span class="underline" style="min-width: 80px;"><?= $leave->sick_out_patient ?? '' ?></span>
                </div>
                
                <div class="sub-section">
                    <em>In case of Special Leave Benefits for Women:</em><br>
                    (Specify Illness) <span class="underline" style="min-width: 150px;"><?= $leave->special_women_illness ?? '' ?></span>
                </div>
                
                <div class="sub-section">
                    <em>In case of Study Leave:</em><br>
                    <span class="checkbox <?= $leave->study_completion_masters ? 'checked' : '' ?>"></span> Completion of Master's Degree<br>
                    <span class="checkbox <?= $leave->study_bar_review ? 'checked' : '' ?>"></span> BAR/Board Examination Review
                </div>
                
                <div class="sub-section">
                    <em>Other Purpose:</em><br>
                    <span class="checkbox <?= $leave->other_purpose_monetization ? 'checked' : '' ?>"></span> Monetization of Leave Credits<br>
                    <span class="checkbox <?= $leave->other_purpose_terminal_leave ? 'checked' : '' ?>"></span> Terminal Leave
                </div>
            </div>
        </div>

        <!-- Section 6.C and 6.D -->
        <div class="details-section">
            <div class="details-left">
                <strong>6.C NUMBER OF WORKING DAYS APPLIED FOR</strong><br>
                <span class="underline" style="min-width: 100px;"><?= number_format($leave->working_days_applied, 1) ?></span>
                
                <div style="margin-top: 10px;">
                    <strong>INCLUSIVE DATES</strong><br>
                    <span class="underline" style="min-width: 200px;">
                        <?= date('F d, Y', strtotime($leave->inclusive_date_from)) ?> - <?= date('F d, Y', strtotime($leave->inclusive_date_to)) ?>
                    </span>
                </div>
            </div>
            
            <div class="details-right">
                <strong>6.D COMMUTATION</strong><br>
                <span class="checkbox <?= !$leave->commutation_requested ? 'checked' : '' ?>"></span> Not Requested<br>
                <span class="checkbox <?= $leave->commutation_requested ? 'checked' : '' ?>"></span> Requested
                
                <div class="signature-line" style="margin-top: 30px;">
                    (Signature of Applicant)
                </div>
            </div>
        </div>

        <!-- Section 7: Details of Action on Application -->
        <div class="section-header">7. DETAILS OF ACTION ON APPLICATION</div>
        
        <div class="action-section">
            <div class="action-left">
                <strong>7.A CERTIFICATION OF LEAVE CREDITS</strong><br>
                As of <span class="underline" style="min-width: 100px;"><?= $leave->certification_as_of ? date('F d, Y', strtotime($leave->certification_as_of)) : '' ?></span>
                
                <table class="credits-table">
                    <tr>
                        <th></th>
                        <th>Vacation Leave</th>
                        <th>Sick Leave</th>
                    </tr>
                    <tr>
                        <td>Total Earned</td>
                        <td><?= $leave->vacation_leave_total_earned ? number_format($leave->vacation_leave_total_earned, 3) : '' ?></td>
                        <td><?= $leave->sick_leave_total_earned ? number_format($leave->sick_leave_total_earned, 3) : '' ?></td>
                    </tr>
                    <tr>
                        <td>Less this application</td>
                        <td><?= $leave->vacation_leave_less_application ? number_format($leave->vacation_leave_less_application, 3) : '' ?></td>
                        <td><?= $leave->sick_leave_less_application ? number_format($leave->sick_leave_less_application, 3) : '' ?></td>
                    </tr>
                    <tr>
                        <td><strong>Balance</strong></td>
                        <td><strong><?= $leave->vacation_leave_balance ? number_format($leave->vacation_leave_balance, 3) : '' ?></strong></td>
                        <td><strong><?= $leave->sick_leave_balance ? number_format($leave->sick_leave_balance, 3) : '' ?></strong></td>
                    </tr>
                </table>
                
                <div style="text-align: center; margin-top: 15px;">
                    <strong><u>FREDERICK R. FLORDELIZA</u></strong><br>
                    <small>Administrative Officer IV - Human Resource Management Officer II</small>
                </div>
            </div>
            
            <div class="action-right">
                <strong>7.B RECOMMENDATION</strong><br>
                <span class="checkbox <?= $leave->recommendation == 'for_approval' ? 'checked' : '' ?>"></span> For approval<br>
                <span class="checkbox <?= $leave->recommendation == 'for_disapproval' ? 'checked' : '' ?>"></span> For disapproval due to<br>
                <span class="underline" style="width: 90%;"><?= $leave->recommendation_disapproval_reason ?? '' ?></span>
                
                <div class="signature-line" style="margin-top: 30px;">
                    Authorized Official
                </div>
            </div>
        </div>

        <div class="action-section">
            <div class="action-left">
                <strong>7.C APPROVED FOR:</strong><br>
                <span class="underline" style="min-width: 50px;"><?= $leave->approved_days_with_pay ?? '' ?></span> days with pay<br>
                <span class="underline" style="min-width: 50px;"><?= $leave->approved_days_without_pay ?? '' ?></span> days without pay<br>
                <span class="underline" style="min-width: 50px;"><?= $leave->approved_others ?? '' ?></span> others (Specify)
            </div>
            
            <div class="action-right">
                <strong>7.D DISAPPROVED DUE TO:</strong><br>
                <span class="underline" style="width: 90%;"><?= $leave->disapproval_reason ?? '' ?></span><br>
                <span class="underline" style="width: 90%;"></span>
            </div>
        </div>

        <div class="final-approval">
            <strong><u>GLINARD L. QUEZADA, MD, FPSGS, MBA-HA</u></strong><br>
            Medical Center Chief I
        </div>
    </div>
</body>
</html>
