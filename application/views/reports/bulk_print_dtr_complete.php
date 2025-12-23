<?php
/**
 * Calculate total working days from DTR data
 * Handles full days, half days, and weekend/holiday work
 * TEMPORARILY DISABLED - Leave blank for manual entry
 */
/*
function calculateWorkingDays($time_data, $selected_year, $selected_month) {
    $total_days = 0;
    $b = 0;
    $days_in_month = date('t', strtotime($selected_year . '-' . date('m', strtotime($selected_month . '-01')) . '-01'));
    
    for ($i = 1; $i <= $days_in_month; $i++) {
        $current_date = $selected_year . '-' . str_pad(date('m', strtotime($selected_month . '-01')), 2, '0', STR_PAD_LEFT) . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
        $is_weekend = isWeekend($current_date);
        $is_holiday = isPhilippineHoliday($current_date);
        
        $day_value = 0;
        
        // Check if there's attendance data for this day
        if (!empty($time_data[$b]->date) && date('j', strtotime($time_data[$b]->date)) == $i) {
            $morning_in = !empty($time_data[$b]->morning_in);
            $morning_out = !empty($time_data[$b]->morning_out);
            $afternoon_in = !empty($time_data[$b]->afternoon_in);
            $afternoon_out = !empty($time_data[$b]->afternoon_out);
            
            // Count attendance sessions
            $morning_session = $morning_in && $morning_out;
            $afternoon_session = $afternoon_in && $afternoon_out;
            
            // Calculate day value based on sessions present
            if ($morning_session && $afternoon_session) {
                $day_value = 1.0; // Full day
            } elseif ($morning_session || $afternoon_session) {
                $day_value = 0.5; // Half day
            } elseif ($morning_in || $morning_out || $afternoon_in || $afternoon_out) {
                // Partial attendance (at least one time entry)
                $day_value = 0.5; // Count as half day for incomplete entries
            }
            
            $b++;
        } else {
            // No attendance data for this day
            // For regular workdays without data, count as 0
            // For weekends/holidays, only count if there was supposed to be work
            $day_value = 0;
        }
        
        $total_days += $day_value;
    }
    
    return $total_days;
}
*/

// Helper functions for weekend and holiday detection
function isWeekend($date) {
    $day_of_week = date('w', strtotime($date));
    return ($day_of_week == 0 || $day_of_week == 6); // Sunday = 0, Saturday = 6
}

function getWeekendLabel($date) {
    $day_of_week = date('w', strtotime($date));
    if ($day_of_week == 0) return 'SUNDAY';
    if ($day_of_week == 6) return 'SATURDAY';
    return 'WEEKEND'; // fallback
}

function isPhilippineHoliday($date) {
    $year = date('Y', strtotime($date));
    $month_day = date('m-d', strtotime($date));
    
    // Fixed Philippine holidays
    $fixed_holidays = [
        '01-01', // New Year's Day
        '02-25', // EDSA People Power Revolution Anniversary
        '04-09', // Araw ng Kagitingan (Day of Valor)
        '05-01', // Labor Day
        '06-12', // Independence Day
        '08-21', // Ninoy Aquino Day
        '08-25', // National Heroes Day (last Monday of August - approximation)
        '10-28', // Davao Occ Araw
        '10-31', // All Souls' Evening
        '11-17', // Araw ng Malita
        '11-30', // Bonifacio Day
        '12-25', // Christmas Day
        '12-30', // Rizal Day
        '12-31'  // New Year's Eve
    ];
    
    // Check fixed holidays
    if (in_array($month_day, $fixed_holidays)) {
        return true;
    }
    
    // Variable holidays (simplified - you may need to adjust these)
    // Maundy Thursday and Good Friday (varies each year)
    // For 2024: April 18-19, 2025: April 17-18
    if ($year == 2024 && ($month_day == '04-18' || $month_day == '04-19')) {
        return true;
    }
    if ($year == 2025 && ($month_day == '04-17' || $month_day == '04-18')) {
        return true;
    }
    if ($year == 2026 && ($month_day == '04-02' || $month_day == '04-03')) {
        return true;
    }
    
    // Eid al-Fitr (varies each year - approximate dates)
    if ($year == 2024 && $month_day == '04-10') {
        return true;
    }
    if ($year == 2025 && $month_day == '03-31') {
        return true;
    }
    
    return false;
}
?>

<style>
@media print {
    .no-print {
        display: none !important;
    }
    
    .page-break {
        page-break-after: always;
    }
    
    body {
        margin: 0;
        padding: 0;
    }
}
</style>

<div class="page-header no-print">
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
            <a href="<?= site_url('reports/schedule_compliance') ?>">Schedule Compliance</a>
        </li>
        <li class="separator">
            <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
            <a href="<?= site_url('reports/schedule_compliance/bulk_print_complete?start_date=' . $start_date . '&end_date=' . $end_date . ($department_id ? '&department_id=' . $department_id : '')) ?>">Bulk Print Complete</a>
        </li>
        <li class="separator">
            <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
            <a href="javascript:void(0)">Bulk DTR Print</a>
        </li>
    </ul>
    <div class="ml-md-auto py-2 py-md-0">
        <a href="<?= site_url('reports/schedule_compliance/bulk_print_complete?start_date=' . $start_date . '&end_date=' . $end_date . ($department_id ? '&department_id=' . $department_id : '')) ?>" class="btn btn-secondary btn-border btn-round btn-sm mr-2" title="Back">
            <span class="btn-label">
                <i class="fa fa-arrow-left"></i>
            </span>
            Back
        </a>
        <a href="javascript:void(0)" class="btn btn-danger btn-border btn-round btn-sm" onclick="printDiv('printThis')" title="Print All DTR">
            <span class="btn-label">
                <i class="fa fa-print"></i>
            </span>
            Print All (<?= $total_complete ?> DTRs)
        </a>
    </div>
</div>

<div class="row no-print">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-head-row">
                    <div class="card-title">
                        <i class="fas fa-check-circle text-success mr-2"></i>
                        Bulk DTR - Complete Schedule Personnel (<?= $total_complete ?> personnel)
                    </div>
                    <div class="card-tools">
                        <span class="badge badge-success mr-2">Period: <?= date('F Y', strtotime($dtr_month . '-01')) ?></span>
                        <span class="badge badge-info"><?= $department_name ?></span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="alert alert-success">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Complete Schedule Personnel DTRs:</strong> This page shows DTRs for all <?= $total_complete ?> personnel with 100% compliance rate.
                    Click "Print All" to print all DTRs at once. Each personnel will have 2 copies of their DTR on one page.
                </div>
            </div>
        </div>
    </div>
</div>

<div id="printThis">
    <?php 
    // Get system logo and info
    $sys_query = $this->db->query("SELECT * FROM systems WHERE id=1");
    $sys_info = $sys_query->row();
    
    // Use dtr_month passed from controller
    $date = $dtr_month;
    
    $personnel_count = 0;
    $total_personnel = count($person);
    
    if (empty($person)): ?>
        <div class="alert alert-warning text-center no-print" style="margin: 50px;">
            <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
            <h4>No Complete Schedule Personnel Found</h4>
            <p>No personnel with 100% compliance rate found for the selected period and department.</p>
            <a href="<?= site_url('reports/schedule_compliance/bulk_print_complete?start_date=' . $start_date . '&end_date=' . $end_date . ($department_id ? '&department_id=' . $department_id : '')) ?>" class="btn btn-primary mt-3">
                <i class="fas fa-arrow-left mr-2"></i>Go Back
            </a>
        </div>
    <?php else:
    
    foreach ($person as $row) : 
        $personnel_count++;
        ?>
        <div class="<?= ($personnel_count < $total_personnel) ? 'page-break' : '' ?>" style="width: 100%; margin-bottom: 20px;">
            <!-- First DTR Copy -->
            <div class="dtr-copy" style="width: 49%; float: left; margin-right: 2%; transform: scale(0.95); transform-origin: top left; border: 2px solid black; padding: 8px; box-sizing: border-box;">
            <?php
            $bio_id = $row->bio_id;
            $email = $row->email;
            
            // Calculate dynamic font size based on full name length
            $full_name = strtoupper($row->lastname . ' ' . $row->firstname . ' ' . (!empty($row->middlename) ? substr($row->middlename, 0, 1) : ''));
            $name_length = strlen($full_name);
            
            // Dynamic font sizing logic
            if ($name_length <= 20) {
                $name_font_size = '13px';
            } elseif ($name_length <= 25) {
                $name_font_size = '12px';
            } elseif ($name_length <= 30) {
                $name_font_size = '11px';
            } elseif ($name_length <= 35) {
                $name_font_size = '10px';
            } else {
                $name_font_size = '9px';
            }
            
            // Use $date from dtr_month (set at top of file)
            $month = date('m', strtotime($date));
            $year = date('Y', strtotime($date));
            
            // Get biometric data using bio_id if available
            if (!empty($bio_id)) {
                $bio_query = $this->db->query("SELECT date, am_in as morning_in, am_out as morning_out, pm_in as afternoon_in, pm_out as afternoon_out, undertime_hours, undertime_minutes 
                                              FROM biometrics 
                                              WHERE bio_id='$bio_id' AND MONTH(date)=$month AND YEAR(date)=$year 
                                              ORDER BY date ASC");
                $bio_time = $bio_query->result();
            } else {
                $bio_time = array();
            }
            
            // Get manual attendance data using email
            $att_query = $this->db->query("SELECT * FROM attendance WHERE email='$email' AND MONTH(date)=$month AND YEAR(date)=$year ORDER BY attendance.date ASC");
            $att_time = $att_query->result();
            
            // Merge biometric and manual attendance data, prioritizing biometric data
            $time = array();
            $merged_dates = array();
            
            // Add biometric data first
            foreach ($bio_time as $bio_record) {
                $time[] = $bio_record;
                $merged_dates[] = $bio_record->date;
            }
            
            // Add manual attendance data for dates not covered by biometric data
            foreach ($att_time as $att_record) {
                if (!in_array($att_record->date, $merged_dates)) {
                    $time[] = $att_record;
                }
            }
            
            // Sort by date
            usort($time, function($a, $b) {
                return strtotime($a->date) - strtotime($b->date);
            });
            ?>
            <table class="w-100" style="border-collapse: collapse; table-layout: fixed;">
                <thead>
                    <?php if (!empty($sys_info->system_logo)) : ?>
                    <tr>
                        <td colspan="7" style="text-align: center; border: none; padding: 5px;">
                            <img src="<?= base_url('assets/uploads/' . $sys_info->system_logo) ?>" alt="System Logo" style="max-height: 60px; max-width: 100px;">
                        </td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td colspan="7" style="text-align: center; border: none; padding: 2px; font-size: 13px; font-family: 'Times New Roman', serif; color: black;">
                            <strong>Republic of the Philippines</strong>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="7" style="text-align: center; border: none; padding: 2px; font-size: 13px; font-family: 'Times New Roman', serif; color: black;">
                            <strong>Department of Health</strong>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="7" style="text-align: center; border: none; padding: 2px; font-size: 13px; font-family: 'Times New Roman', serif; color: black;">
                            <strong>REGION XI</strong>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="7" style="text-align: center; border: none; padding: 2px; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><strong>DAVAO OCCIDENTAL GENERAL HOSPITAL</strong></td>
                    </tr>
                    <tr>
                        <td colspan="7" style="border: none; height: 10px;"></td>
                    </tr>
                    <tr>
                        <td colspan="7" style="text-align: left; border: none; padding: 2px; font-size: 13px; font-family: 'Times New Roman', serif; color: black;">
                            <strong>Civil Service Form 48</strong>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="7" style="border: none; height: 5px;"></td>
                    </tr>
                    <tr>
                        <td style="text-align: left; border: none; padding: 5px 2px; font-size: 13px; font-family: 'Times New Roman', serif; color: black; vertical-align: bottom; width: 10%;">Name:</td>
                        <td colspan="2" style="text-align: center; border: none; border-bottom: 1px solid black; padding: 5px 2px; font-size: <?= $name_font_size ?>; font-family: 'Times New Roman', serif; color: black; font-weight: bold;">
                            <?= strtoupper(!empty($row->middlename[0]) ? $row->lastname : $row->lastname) ?>
                        </td>
                        <td colspan="2" style="text-align: center; border: none; border-bottom: 1px solid black; padding: 5px 2px; font-size: <?= $name_font_size ?>; font-family: 'Times New Roman', serif; color: black; font-weight: bold;">
                            <?= strtoupper($row->firstname) ?>
                        </td>
                        <td colspan="2" style="text-align: center; border: none; border-bottom: 1px solid black; padding: 5px 2px; font-size: <?= $name_font_size ?>; font-family: 'Times New Roman', serif; color: black; font-weight: bold;"><?= !empty($row->middlename) ? strtoupper(substr($row->middlename, 0, 1)) : '' ?></td>
                    </tr>
                    <tr>
                        <td style="border: none; padding: 2px;"></td>
                        <td colspan="2" style="text-align: center; border: none; padding: 2px; font-size: 11px; font-family: 'Times New Roman', serif; color: black;">(Surname)</td>
                        <td colspan="2" style="text-align: center; border: none; padding: 2px; font-size: 11px; font-family: 'Times New Roman', serif; color: black;">(Given Name)</td>
                        <td colspan="2" style="text-align: center; border: none; padding: 2px; font-size: 11px; font-family: 'Times New Roman', serif; color: black;">(MI)</td>
                    </tr>
                    <tr>
                        <td colspan="7" style="border: none; height: 8px;"></td>
                    </tr>
                    <tr>
                        <td style="text-align: left; border: none; padding: 5px 2px; font-size: 13px; font-family: 'Times New Roman', serif; color: black; vertical-align: bottom; width: 15%;">For the Month</td>
                        <td colspan="3" style="text-align: center; border: none; border-bottom: 1px solid black; padding: 5px 2px; font-size: 13px; font-family: 'Times New Roman', serif; color: black; width: 85%; font-weight: bold;">
                            <?= isset($date) ? strtoupper(date('F Y', strtotime($date . '-01'))) : strtoupper(date('F Y')) ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="7" style="border: none; height: 10px;"></td>
                    </tr>
                    <tr>
                        <td colspan="7" style="text-align: left; border: none; padding: 5px 2px; font-size: 13px; font-family: 'Times New Roman', serif; color: black;">Official Hours For Arrival and Departure</td>
                    </tr>
                    <tr>
                        <td colspan="7" style="border: none; height: 5px;"></td>
                    </tr>
                    <tr>
                        <td style="text-align: left; border: none; padding: 2px; font-size: 13px; font-family: 'Times New Roman', serif; color: black;">Regular Days:</td>
                        <td colspan="2" style="text-align: center; border: none; border-bottom: 1px solid black; padding: 2px; font-size: 13px; font-family: 'Times New Roman', serif; color: black;">8:00 AM - 5:00 PM</td>
                        <td style="text-align: left; border: none; padding: 2px; font-size: 13px; font-family: 'Times New Roman', serif; color: black;">Saturdays:</td>
                        <td colspan="3" style="text-align: center; border: none; border-bottom: 1px solid black; padding: 2px; font-size: 13px; font-family: 'Times New Roman', serif; color: black;">AS REQUIRED</td>
                    </tr>
                    <tr>
                        <td colspan="7" style="border: none; height: 10px;"></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid black; padding: 4px; font-size: 13px; font-family: 'Times New Roman', serif; text-align: center; vertical-align: middle; color: black; width: 14.28%;">Date</td>
                        <td colspan="2" style="border: 1px solid black; padding: 4px; font-size: 13px; font-family: 'Times New Roman', serif; text-align: center; color: black; font-style: italic; font-weight: bold;">AM</td>
                        <td colspan="2" style="border: 1px solid black; padding: 4px; font-size: 13px; font-family: 'Times New Roman', serif; text-align: center; color: black; font-style: italic; font-weight: bold;">PM</td>
                        <td colspan="2" style="border: 1px solid black; padding: 4px; font-size: 13px; font-family: 'Times New Roman', serif; text-align: center; color: black; font-style: italic; font-weight: bold;">Undertime</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid black; padding: 2px; font-size: 13px; font-family: 'Times New Roman', serif; text-align: center; color: black; width: 14.28%;"></td>
                        <td style="border: 1px solid black; padding: 2px; font-size: 13px; font-family: 'Times New Roman', serif; text-align: center; color: black; width: 14.28%; font-style: italic;">Arrival</td>
                        <td style="border: 1px solid black; padding: 2px; font-size: 13px; font-family: 'Times New Roman', serif; text-align: center; color: black; width: 14.28%; font-style: italic;">Departure</td>
                        <td style="border: 1px solid black; padding: 2px; font-size: 13px; font-family: 'Times New Roman', serif; text-align: center; color: black; width: 14.28%; font-style: italic;">Arrival</td>
                        <td style="border: 1px solid black; padding: 2px; font-size: 13px; font-family: 'Times New Roman', serif; text-align: center; color: black; width: 14.28%; font-style: italic;">Departure</td>
                        <td style="border: 1px solid black; padding: 2px; font-size: 13px; font-family: 'Times New Roman', serif; text-align: center; color: black; width: 14.28%; font-style: italic;">Hours</td>
                        <td style="border: 1px solid black; padding: 2px; font-size: 13px; font-family: 'Times New Roman', serif; text-align: center; color: black; width: 14.28%; font-style: italic;">Minutes</td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $b = 0;
                    // Get the number of days in the selected month/year
                    $selected_month = isset($date) ? $date : date('Y-m');
                    $days_in_month = date("t", strtotime($selected_month . '-01'));
                    $selected_year = date('Y', strtotime($selected_month . '-01'));
                    
                    for ($i = 1; $i <= $days_in_month; $i++) :
                        // Create the current date for this day
                        $current_date = $selected_year . '-' . str_pad(date('m', strtotime($selected_month . '-01')), 2, '0', STR_PAD_LEFT) . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
                        $is_weekend = isWeekend($current_date);
                        $is_holiday = isPhilippineHoliday($current_date);
                        
                        if (!empty($time[$b]->date)) :
                            if (date('j', strtotime($time[$b]->date)) == $i) : ?>
                                <tr>
                                    <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= $i ?></td>
                                    <?php if ($is_weekend || $is_holiday) : ?>
                                        <?php 
                                        // Check if there are time entries on this weekend/holiday
                                        $has_entries = !empty($time[$b]->morning_in) || !empty($time[$b]->morning_out) || 
                                                      !empty($time[$b]->afternoon_in) || !empty($time[$b]->afternoon_out);
                                        
                                        if ($has_entries) : // Show time entries ONLY (no label) - treat as regular workday
                                        ?>
                                            <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= !empty($time[$b]->morning_in) ? date('h:i', strtotime($time[$b]->morning_in)) : '' ?></td>
                                            <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= !empty($time[$b]->morning_out) ? date('h:i', strtotime($time[$b]->morning_out)) : '' ?></td>
                                            <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= !empty($time[$b]->afternoon_in) ? date('h:i', strtotime($time[$b]->afternoon_in)) : '' ?></td>
                                            <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= !empty($time[$b]->afternoon_out) ? date('h:i', strtotime($time[$b]->afternoon_out)) : '' ?></td>
                                            <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= isset($time[$b]->undertime_hours) && $time[$b]->undertime_hours !== null ? $time[$b]->undertime_hours : '' ?></td>
                                            <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= isset($time[$b]->undertime_minutes) && $time[$b]->undertime_minutes !== null ? $time[$b]->undertime_minutes : '' ?></td>
                                        <?php else : // Show only the label spanning columns (no work on this day) ?>
                                            <td colspan="6" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><strong><?= $is_weekend ? getWeekendLabel($current_date) : 'HOLIDAY' ?></strong></td>
                                        <?php endif; ?>
                                    <?php else : // Regular workday ?>
                                        <?php 
                                        // Check if this is a full-day absence (all time entries are empty)
                                        $is_full_day_absent = empty($time[$b]->morning_in) && empty($time[$b]->morning_out) && 
                                                              empty($time[$b]->afternoon_in) && empty($time[$b]->afternoon_out);
                                        
                                        if ($is_full_day_absent) : 
                                            // Full day absence - show completely blank row
                                        ?>
                                            <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                            <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                            <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                            <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                            <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                            <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                        <?php else : 
                                            // Partial attendance - show times and undertime
                                        ?>
                                            <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= !empty($time[$b]->morning_in) ? date('h:i', strtotime($time[$b]->morning_in)) : '' ?></td>
                                            <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= !empty($time[$b]->morning_out) ? date('h:i', strtotime($time[$b]->morning_out)) : '' ?></td>
                                            <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= !empty($time[$b]->afternoon_in) ? date('h:i', strtotime($time[$b]->afternoon_in)) : '' ?></td>
                                            <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= !empty($time[$b]->afternoon_out) ? date('h:i', strtotime($time[$b]->afternoon_out)) : '' ?></td>
                                            <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= isset($time[$b]->undertime_hours) && $time[$b]->undertime_hours !== null ? $time[$b]->undertime_hours : '' ?></td>
                                            <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= isset($time[$b]->undertime_minutes) && $time[$b]->undertime_minutes !== null ? $time[$b]->undertime_minutes : '' ?></td>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </tr>
                            <?php $b++;
                            else : ?>
                                <tr>
                                    <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= $i ?></td>
                                    <?php if ($is_weekend || $is_holiday) : ?>
                                        <td colspan="6" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><strong><?= $is_weekend ? getWeekendLabel($current_date) : 'HOLIDAY' ?></strong></td>
                                    <?php else : ?>
                                        <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                        <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                        <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                        <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                        <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                        <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                    <?php endif; ?>
                                </tr>
                            <?php endif ?>
                        <?php else : ?>
                            <tr>
                                <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= $i ?></td>
                                <?php if ($is_weekend || $is_holiday) : ?>
                                    <td colspan="6" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 10px; font-family: 'Times New Roman', serif; color: black;"><strong><?= $is_weekend ? getWeekendLabel($current_date) : 'HOLIDAY' ?></strong></td>
                                <?php else : ?>
                                    <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                    <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                    <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                    <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                    <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                    <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                <?php endif; ?>
                            </tr>
                        <?php endif ?>

                    <?php endfor ?>
                    
                    <!-- Total Working Days Row -->
                    <?php // $total_working_days = calculateWorkingDays($time, $selected_year, $selected_month); ?>
                    <tr style="background-color: #f8f9fa; font-weight: bold;">
                        <td colspan="6" style="border: 1px solid black; padding: 5px; text-align: left; font-size: 13px; font-family: 'Times New Roman', serif; color: black;">
                            <strong>Total Number of Days Present</strong>
                        </td>
                        <td style="border: 1px solid black; padding: 5px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;">
                            <strong></strong>
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <!-- Signature Section -->
            <table class="w-100" style="margin-top: 10px; border-collapse: collapse;">
                <tr>
                    <td colspan="7" style="padding: 5px;">
                        <div style="text-align: left; font-size: 13px; font-family: 'Times New Roman', serif; color: black;">
                            I certify on my honor that the above is true and correct report of the hours of work performed, record of which was made daily at the time of arrival and departure from the office.
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="7" style="padding: 10px 5px;">
                        <div style="text-align: center;">
                            <strong style="font-size: 13px; font-family: 'Times New Roman', serif; text-decoration: underline; color: black;"><?= strtoupper(!empty($row->middlename[0]) ? $row->firstname . ' ' . $row->middlename[0] . '. ' . $row->lastname : $row->firstname . ' ' . $row->lastname) ?></strong><br>
                            <span style="font-size: 13px; font-family: 'Times New Roman', serif; color: black;">Employee</span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="7" style="padding: 5px;">
                        <div style="text-align: left; font-size: 13px; font-family: 'Times New Roman', serif; color: black;">
                            Verified as the prescribed office hours:
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="7" style="padding: 10px 5px;">
                        <div style="text-align: center;">
                            <div style="border-bottom: 1px solid black; width: 250px; margin: 0 auto 2px auto; height: 15px;"></div>
                            <span style="font-size: 13px; font-family: 'Times New Roman', serif; color: black;">Immediate Supervisor</span>
                        </div>
                    </td>
                </tr>
            </table>
            </div>
            
            <!-- Second DTR Copy (Duplicate) -->
            <div class="dtr-copy" style="width: 49%; float: right; transform: scale(0.95); transform-origin: top right; border: 2px solid black; padding: 8px; box-sizing: border-box;">
            <table class="w-100" style="border-collapse: collapse; table-layout: fixed;">
                <thead>
                    <?php if (!empty($sys_info->system_logo)) : ?>
                    <tr>
                        <td colspan="7" style="text-align: center; border: none; padding: 5px;">
                            <img src="<?= base_url('assets/uploads/' . $sys_info->system_logo) ?>" alt="System Logo" style="max-height: 60px; max-width: 100px;">
                        </td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td colspan="7" style="text-align: center; border: none; padding: 2px; font-size: 13px; font-family: 'Times New Roman', serif; color: black;">
                            <strong>Republic of the Philippines</strong>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="7" style="text-align: center; border: none; padding: 2px; font-size: 13px; font-family: 'Times New Roman', serif; color: black;">
                            <strong>Department of Health</strong>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="7" style="text-align: center; border: none; padding: 2px; font-size: 13px; font-family: 'Times New Roman', serif; color: black;">
                            <strong>REGION XI</strong>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="7" style="text-align: center; border: none; padding: 2px; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><strong>DAVAO OCCIDENTAL GENERAL HOSPITAL</strong></td>
                    </tr>
                    <tr>
                        <td colspan="7" style="border: none; height: 10px;"></td>
                    </tr>
                    <tr>
                        <td colspan="7" style="text-align: left; border: none; padding: 2px; font-size: 13px; font-family: 'Times New Roman', serif; color: black;">
                            <strong>Civil Service Form 48</strong>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="7" style="border: none; height: 5px;"></td>
                    </tr>
                    <tr>
                        <td style="text-align: left; border: none; padding: 5px 2px; font-size: 13px; font-family: 'Times New Roman', serif; color: black; vertical-align: bottom; width: 10%;">Name:</td>
                        <td colspan="2" style="text-align: center; border: none; border-bottom: 1px solid black; padding: 5px 2px; font-size: <?= $name_font_size ?>; font-family: 'Times New Roman', serif; color: black; font-weight: bold;">
                            <?= strtoupper(!empty($row->middlename[0]) ? $row->lastname : $row->lastname) ?>
                        </td>
                        <td colspan="2" style="text-align: center; border: none; border-bottom: 1px solid black; padding: 5px 2px; font-size: <?= $name_font_size ?>; font-family: 'Times New Roman', serif; color: black; font-weight: bold;">
                            <?= strtoupper($row->firstname) ?>
                        </td>
                        <td colspan="2" style="text-align: center; border: none; border-bottom: 1px solid black; padding: 5px 2px; font-size: <?= $name_font_size ?>; font-family: 'Times New Roman', serif; color: black; font-weight: bold;"><?= !empty($row->middlename) ? strtoupper(substr($row->middlename, 0, 1)) : '' ?></td>
                    </tr>
                    <tr>
                        <td style="border: none; padding: 2px;"></td>
                        <td colspan="2" style="text-align: center; border: none; padding: 2px; font-size: 11px; font-family: 'Times New Roman', serif; color: black;">(Surname)</td>
                        <td colspan="2" style="text-align: center; border: none; padding: 2px; font-size: 11px; font-family: 'Times New Roman', serif; color: black;">(Given Name)</td>
                        <td colspan="2" style="text-align: center; border: none; padding: 2px; font-size: 11px; font-family: 'Times New Roman', serif; color: black;">(MI)</td>
                    </tr>
                    <tr>
                        <td colspan="7" style="border: none; height: 8px;"></td>
                    </tr>
                    <tr>
                        <td style="text-align: left; border: none; padding: 5px 2px; font-size: 13px; font-family: 'Times New Roman', serif; color: black; vertical-align: bottom; width: 15%;">For the Month</td>
                        <td colspan="3" style="text-align: center; border: none; border-bottom: 1px solid black; padding: 5px 2px; font-size: 13px; font-family: 'Times New Roman', serif; color: black; width: 85%; font-weight: bold;">
                            <?= isset($date) ? strtoupper(date('F Y', strtotime($date . '-01'))) : strtoupper(date('F Y')) ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="7" style="border: none; height: 10px;"></td>
                    </tr>
                    <tr>
                        <td colspan="7" style="text-align: left; border: none; padding: 5px 2px; font-size: 13px; font-family: 'Times New Roman', serif; color: black;">Official Hours For Arrival and Departure</td>
                    </tr>
                    <tr>
                        <td colspan="7" style="border: none; height: 5px;"></td>
                    </tr>
                    <tr>
                        <td style="text-align: left; border: none; padding: 2px; font-size: 13px; font-family: 'Times New Roman', serif; color: black;">Regular Days:</td>
                        <td colspan="2" style="text-align: center; border: none; border-bottom: 1px solid black; padding: 2px; font-size: 13px; font-family: 'Times New Roman', serif; color: black;">8:00 AM - 5:00 PM</td>
                        <td style="text-align: left; border: none; padding: 2px; font-size: 13px; font-family: 'Times New Roman', serif; color: black;">Saturdays:</td>
                        <td colspan="3" style="text-align: center; border: none; border-bottom: 1px solid black; padding: 2px; font-size: 13px; font-family: 'Times New Roman', serif; color: black;">AS REQUIRED</td>
                    </tr>
                    <tr>
                        <td colspan="7" style="border: none; height: 10px;"></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid black; padding: 4px; font-size: 13px; font-family: 'Times New Roman', serif; text-align: center; vertical-align: middle; color: black; width: 14.28%;">Date</td>
                        <td colspan="2" style="border: 1px solid black; padding: 4px; font-size: 13px; font-family: 'Times New Roman', serif; text-align: center; color: black; font-style: italic; font-weight: bold;">AM</td>
                        <td colspan="2" style="border: 1px solid black; padding: 4px; font-size: 13px; font-family: 'Times New Roman', serif; text-align: center; color: black; font-style: italic; font-weight: bold;">PM</td>
                        <td colspan="2" style="border: 1px solid black; padding: 4px; font-size: 13px; font-family: 'Times New Roman', serif; text-align: center; color: black; font-style: italic; font-weight: bold;">Undertime</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid black; padding: 2px; font-size: 13px; font-family: 'Times New Roman', serif; text-align: center; color: black; width: 14.28%;"></td>
                        <td style="border: 1px solid black; padding: 2px; font-size: 13px; font-family: 'Times New Roman', serif; text-align: center; color: black; width: 14.28%; font-style: italic;">Arrival</td>
                        <td style="border: 1px solid black; padding: 2px; font-size: 13px; font-family: 'Times New Roman', serif; text-align: center; color: black; width: 14.28%; font-style: italic;">Departure</td>
                        <td style="border: 1px solid black; padding: 2px; font-size: 13px; font-family: 'Times New Roman', serif; text-align: center; color: black; width: 14.28%; font-style: italic;">Arrival</td>
                        <td style="border: 1px solid black; padding: 2px; font-size: 13px; font-family: 'Times New Roman', serif; text-align: center; color: black; width: 14.28%; font-style: italic;">Departure</td>
                        <td style="border: 1px solid black; padding: 2px; font-size: 13px; font-family: 'Times New Roman', serif; text-align: center; color: black; width: 14.28%; font-style: italic;">Hours</td>
                        <td style="border: 1px solid black; padding: 2px; font-size: 13px; font-family: 'Times New Roman', serif; text-align: center; color: black; width: 14.28%; font-style: italic;">Minutes</td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $b2 = 0; // Reset counter for second DTR
                    for ($i = 1; $i <= $days_in_month; $i++) :
                        $current_date = $selected_year . '-' . str_pad(date('m', strtotime($selected_month . '-01')), 2, '0', STR_PAD_LEFT) . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
                        $is_weekend = isWeekend($current_date);
                        $is_holiday = isPhilippineHoliday($current_date);
                        
                        if (!empty($time[$b2]->date)) :
                            if (date('j', strtotime($time[$b2]->date)) == $i) : ?>
                                <tr>
                                    <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= $i ?></td>
                                    <?php if ($is_weekend || $is_holiday) : ?>
                                        <?php 
                                        $has_entries = !empty($time[$b2]->morning_in) || !empty($time[$b2]->morning_out) || 
                                                      !empty($time[$b2]->afternoon_in) || !empty($time[$b2]->afternoon_out);
                                        
                                        if ($has_entries) : // Show time entries ONLY (no label) - treat as regular workday ?>
                                            <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= !empty($time[$b2]->morning_in) ? date('h:i', strtotime($time[$b2]->morning_in)) : '' ?></td>
                                            <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= !empty($time[$b2]->morning_out) ? date('h:i', strtotime($time[$b2]->morning_out)) : '' ?></td>
                                            <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= !empty($time[$b2]->afternoon_in) ? date('h:i', strtotime($time[$b2]->afternoon_in)) : '' ?></td>
                                            <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= !empty($time[$b2]->afternoon_out) ? date('h:i', strtotime($time[$b2]->afternoon_out)) : '' ?></td>
                                            <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= isset($time[$b2]->undertime_hours) && $time[$b2]->undertime_hours !== null ? $time[$b2]->undertime_hours : '' ?></td>
                                            <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= isset($time[$b2]->undertime_minutes) && $time[$b2]->undertime_minutes !== null ? $time[$b2]->undertime_minutes : '' ?></td>
                                        <?php else : // Show only the label spanning columns (no work on this day) ?>
                                            <td colspan="6" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><strong><?= $is_weekend ? getWeekendLabel($current_date) : 'HOLIDAY' ?></strong></td>
                                        <?php endif; ?>
                                    <?php else : // Regular workday for second DTR copy ?>
                                        <?php 
                                        // Check if this is a full-day absence (all time entries are empty)
                                        $is_full_day_absent_2 = empty($time[$b2]->morning_in) && empty($time[$b2]->morning_out) && 
                                                                empty($time[$b2]->afternoon_in) && empty($time[$b2]->afternoon_out);
                                        
                                        if ($is_full_day_absent_2) : 
                                            // Full day absence - show completely blank row
                                        ?>
                                            <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                            <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                            <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                            <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                            <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                            <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                        <?php else : 
                                            // Partial attendance - show times and undertime
                                        ?>
                                            <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= !empty($time[$b2]->morning_in) ? date('h:i', strtotime($time[$b2]->morning_in)) : '' ?></td>
                                            <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= !empty($time[$b2]->morning_out) ? date('h:i', strtotime($time[$b2]->morning_out)) : '' ?></td>
                                            <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= !empty($time[$b2]->afternoon_in) ? date('h:i', strtotime($time[$b2]->afternoon_in)) : '' ?></td>
                                            <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= !empty($time[$b2]->afternoon_out) ? date('h:i', strtotime($time[$b2]->afternoon_out)) : '' ?></td>
                                            <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= isset($time[$b2]->undertime_hours) && $time[$b2]->undertime_hours !== null ? $time[$b2]->undertime_hours : '' ?></td>
                                            <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= isset($time[$b2]->undertime_minutes) && $time[$b2]->undertime_minutes !== null ? $time[$b2]->undertime_minutes : '' ?></td>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </tr>
                            <?php $b2++;
                            else : ?>
                                <tr>
                                    <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= $i ?></td>
                                    <?php if ($is_weekend || $is_holiday) : ?>
                                        <td colspan="6" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><strong><?= $is_weekend ? getWeekendLabel($current_date) : 'HOLIDAY' ?></strong></td>
                                    <?php else : ?>
                                        <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                        <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                        <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                        <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                        <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                        <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                    <?php endif; ?>
                                </tr>
                            <?php endif ?>
                        <?php else : ?>
                            <tr>
                                <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= $i ?></td>
                                <?php if ($is_weekend || $is_holiday) : ?>
                                    <td colspan="6" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><strong><?= $is_weekend ? getWeekendLabel($current_date) : 'HOLIDAY' ?></strong></td>
                                <?php else : ?>
                                    <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                    <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                    <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                    <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                    <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                    <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                <?php endif; ?>
                            </tr>
                        <?php endif ?>
                    <?php endfor ?>
                    
                    <!-- Total Working Days Row -->
                    <tr style="background-color: #f8f9fa; font-weight: bold;">
                        <td colspan="6" style="border: 1px solid black; padding: 5px; text-align: left; font-size: 13px; font-family: 'Times New Roman', serif; color: black;">
                            <strong>Total Number of Days Present</strong>
                        </td>
                        <td style="border: 1px solid black; padding: 5px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;">
                            <strong></strong>
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <!-- Signature Section -->
            <table class="w-100" style="margin-top: 10px; border-collapse: collapse;">
                <tr>
                    <td colspan="7" style="padding: 5px;">
                        <div style="text-align: left; font-size: 13px; font-family: 'Times New Roman', serif; color: black;">
                            I certify on my honor that the above is true and correct report of the hours of work performed, record of which was made daily at the time of arrival and departure from the office.
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="7" style="padding: 10px 5px;">
                        <div style="text-align: center;">
                            <strong style="font-size: 13px; font-family: 'Times New Roman', serif; text-decoration: underline; color: black;"><?= strtoupper(!empty($row->middlename[0]) ? $row->firstname . ' ' . $row->middlename[0] . '. ' . $row->lastname : $row->firstname . ' ' . $row->lastname) ?></strong><br>
                            <span style="font-size: 13px; font-family: 'Times New Roman', serif; color: black;">Employee</span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="7" style="padding: 5px;">
                        <div style="text-align: left; font-size: 13px; font-family: 'Times New Roman', serif; color: black;">
                            Verified as the prescribed office hours:
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="7" style="padding: 10px 5px;">
                        <div style="text-align: center;">
                            <div style="border-bottom: 1px solid black; width: 250px; margin: 0 auto 2px auto; height: 15px;"></div>
                            <span style="font-size: 13px; font-family: 'Times New Roman', serif; color: black;">Immediate Supervisor</span>
                        </div>
                    </td>
                </tr>
            </table>
            </div>
            
            <div class="clearfix" style='clear: both;'></div>
        </div>
        
    <?php endforeach; 
    endif; // End of if (!empty($person)) ?>
</div>

<script>
// Print function
function printDiv(divId) {
    var printContents = document.getElementById(divId).innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
    location.reload();
}
</script>
