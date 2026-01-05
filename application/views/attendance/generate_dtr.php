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
?>

<div class="page-header">
    <h4 class="page-title"><?= $title ?></h4>
    <ul class="breadcrumbs">
        <li class="nav-home">
            <a href="#">
                <i class="fas fa-users"></i>
            </a>
        </li>
        <li class="separator">
            <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
            <a href="javascript:void(0)">Personnel</a>
        </li>
    </ul>
    <div class="ml-md-auto py-2 py-md-0">
        <button id="toggleEditMode" class="btn btn-warning btn-border btn-round btn-sm" onclick="toggleEditMode()" title="Toggle Edit Mode">
            <span class="btn-label">
                <i class="fa fa-edit"></i>
            </span>
            Edit Mode
        </button>
        <button id="saveChanges" class="btn btn-success btn-border btn-round btn-sm" onclick="saveChanges()" style="display:none;" title="Save Changes">
            <span class="btn-label">
                <i class="fa fa-save"></i>
            </span>
            Save Changes
        </button>
        <a href="javascript:void(0)" class="btn btn-danger btn-border btn-round btn-sm" onclick="printDiv('printThis')" title="Print DTR">
            <span class=" btn-label">
                <i class="fa fa-print"></i>
            </span>
            Print
        </a>
        <!--
        <a href="<?= site_url('admin/generate_dtr') ?>" class="btn btn-primary btn-border btn-round btn-sm" title="Refresh">
            <span class=" btn-label">
                <i class="icon-refresh"></i>
            </span>
            Refresh
        </a>
        -->
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-head-row">
                    <div class="card-title">Personnel Time Record</div>
                    <div class="card-tools">
                        <?php 
                                        // Default to previous month instead of current month
                                        $default_month = isset($_GET['date']) ? $_GET['date'] : date('Y-m', strtotime('first day of last month'));
                                        ?>
                                        <input type="month" class="form-control" id="month" name="start" min="2021-01" value="<?= $default_month ?>">
                    </div>
                </div>

            </div>
            <div class="card-body text-center" id="printThis">
                <?php 
                // Get system logo and info
                $sys_query = $this->db->query("SELECT * FROM systems WHERE id=1");
                $sys_info = $sys_query->row();
                
                $a = 1;
                foreach ($person as $row) : ?>
                    <div style="width: 100%; page-break-inside: avoid; margin-bottom: 20px;">
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
                        
                        if (isset($_GET['date'])) {
                            $date = $_GET['date'];
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
                            
                        } else {
                            // Default to previous month instead of current month
                            $prev_month = date('m', strtotime('first day of last month'));
                            $prev_year = date('Y', strtotime('first day of last month'));
                            
                            // Get biometric data using bio_id if available for previous month
                            if (!empty($bio_id)) {
                                $bio_query = $this->db->query("SELECT date, am_in as morning_in, am_out as morning_out, pm_in as afternoon_in, pm_out as afternoon_out, undertime_hours, undertime_minutes 
                                                              FROM biometrics 
                                                              WHERE bio_id='$bio_id' AND MONTH(date) = $prev_month AND YEAR(date) = $prev_year 
                                                              ORDER BY date ASC");
                                $bio_time = $bio_query->result();
                            } else {
                                $bio_time = array();
                            }
                            
                            // Get manual attendance data using email
                            $att_query = $this->db->query("SELECT * FROM attendance WHERE email='$email' AND MONTH(date) = $prev_month
                                        AND YEAR(date) = $prev_year ORDER BY attendance.date ASC");
                            $att_time = $att_query->result();
                        }
                        
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
                        
                        // Debug: Check if we have data (commented out for production)
                        // echo "<pre>Personnel: {$row->firstname} {$row->lastname}, Bio ID: {$bio_id}, Records: " . count($time) . "</pre>";
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
                                        <?= isset($date) ? strtoupper(date('F Y', strtotime($date . '-01'))) : strtoupper(date('F Y', strtotime('first day of last month'))) ?>
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
                                
                                // Load holidays from database (done once per view)
                                if (!isset($GLOBALS['db_holidays_loaded'])) {
                                    $CI =& get_instance();
                                    $CI->load->model('HolidayModel', 'holidayModel');
                                    $GLOBALS['db_holidays_loaded'] = true;
                                    $GLOBALS['holiday_model'] = $CI->holidayModel;
                                }
                                
                                function isPhilippineHoliday($date, $department_id = null) {
                                    // Use database holidays
                                    if (isset($GLOBALS['holiday_model'])) {
                                        return $GLOBALS['holiday_model']->is_holiday($date, $department_id);
                                    }
                                    return false;
                                }
                                
                                $b = 0;
                                // Get the number of days in the selected month/year
                                $selected_month = isset($date) ? $date : date('Y-m', strtotime('first day of last month'));
                                $days_in_month = date("t", strtotime($selected_month . '-01'));
                                $selected_year = date('Y', strtotime($selected_month . '-01'));
                                
                                for ($i = 1; $i <= $days_in_month; $i++) :
                                    // Create the current date for this day
                                    $current_date = $selected_year . '-' . str_pad(date('m', strtotime($selected_month . '-01')), 2, '0', STR_PAD_LEFT) . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
                                    $is_weekend = isWeekend($current_date);
                                    $is_holiday = isPhilippineHoliday($current_date);
                                    
                                    if (!empty($time[$b]->date)) :
                                        if (date('j', strtotime($time[$b]->date)) == $i) : ?>
                                            <tr data-date="<?= $current_date ?>" data-copy="1">
                                                <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= $i ?></td>
                                                <?php if ($is_weekend || $is_holiday) : ?>
                                                    <?php 
                                                    // Check if there are time entries on this weekend/holiday
                                                    $has_entries = !empty($time[$b]->morning_in) || !empty($time[$b]->morning_out) || 
                                                                  !empty($time[$b]->afternoon_in) || !empty($time[$b]->afternoon_out);
                                                    
                                                    if ($has_entries) : // Show time entries without label (treat as regular workday)
                                                    ?>
                                                        <td class="editable-cell" data-field="morning_in" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= !empty($time[$b]->morning_in) ? date('h:i', strtotime($time[$b]->morning_in)) : '' ?></td>
                                                        <td class="editable-cell" data-field="morning_out" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= !empty($time[$b]->morning_out) ? date('h:i', strtotime($time[$b]->morning_out)) : '' ?></td>
                                                        <td class="editable-cell" data-field="afternoon_in" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= !empty($time[$b]->afternoon_in) ? date('h:i', strtotime($time[$b]->afternoon_in)) : '' ?></td>
                                                        <td class="editable-cell" data-field="afternoon_out" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= !empty($time[$b]->afternoon_out) ? date('h:i', strtotime($time[$b]->afternoon_out)) : '' ?></td>
                                                        <td class="editable-cell" data-field="undertime_hours" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= isset($time[$b]->undertime_hours) && $time[$b]->undertime_hours !== null ? $time[$b]->undertime_hours : '' ?></td>
                                                        <td class="editable-cell" data-field="undertime_minutes" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= isset($time[$b]->undertime_minutes) && $time[$b]->undertime_minutes !== null ? $time[$b]->undertime_minutes : '' ?></td>
                                                    <?php else : // Show only the label spanning columns ?>
                                                        <td colspan="6" class="editable-label" data-field="label" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><strong><?= $is_weekend ? getWeekendLabel($current_date) : 'HOLIDAY' ?></strong></td>
                                                    <?php endif; ?>
                                                <?php else : // Regular workday ?>
                                                    <?php 
                                                    // Check if this is a full-day absence (all time entries are empty)
                                                    $is_full_day_absent = empty($time[$b]->morning_in) && empty($time[$b]->morning_out) && 
                                                                          empty($time[$b]->afternoon_in) && empty($time[$b]->afternoon_out);
                                                    
                                                    if ($is_full_day_absent) : 
                                                        // Full day absence - show completely blank row
                                                    ?>
                                                        <td class="editable-cell" data-field="morning_in" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                                        <td class="editable-cell" data-field="morning_out" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                                        <td class="editable-cell" data-field="afternoon_in" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                                        <td class="editable-cell" data-field="afternoon_out" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                                        <td class="editable-cell" data-field="undertime_hours" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                                        <td class="editable-cell" data-field="undertime_minutes" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                                    <?php else : 
                                                        // Partial attendance - show times and undertime
                                                    ?>
                                                        <td class="editable-cell" data-field="morning_in" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= !empty($time[$b]->morning_in) ? date('h:i', strtotime($time[$b]->morning_in)) : '' ?></td>
                                                        <td class="editable-cell" data-field="morning_out" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= !empty($time[$b]->morning_out) ? date('h:i', strtotime($time[$b]->morning_out)) : '' ?></td>
                                                        <td class="editable-cell" data-field="afternoon_in" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= !empty($time[$b]->afternoon_in) ? date('h:i', strtotime($time[$b]->afternoon_in)) : '' ?></td>
                                                        <td class="editable-cell" data-field="afternoon_out" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= !empty($time[$b]->afternoon_out) ? date('h:i', strtotime($time[$b]->afternoon_out)) : '' ?></td>
                                                        <td class="editable-cell" data-field="undertime_hours" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= isset($time[$b]->undertime_hours) && $time[$b]->undertime_hours !== null ? $time[$b]->undertime_hours : '' ?></td>
                                                        <td class="editable-cell" data-field="undertime_minutes" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= isset($time[$b]->undertime_minutes) && $time[$b]->undertime_minutes !== null ? $time[$b]->undertime_minutes : '' ?></td>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </tr>
                                        <?php $b++;
                                        else : ?>
                                            <tr data-date="<?= $current_date ?>" data-copy="1">
                                                <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= $i ?></td>
                                                <?php if ($is_weekend || $is_holiday) : ?>
                                                    <td colspan="6" class="editable-label" data-field="label" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><strong><?= $is_weekend ? getWeekendLabel($current_date) : 'HOLIDAY' ?></strong></td>
                                                <?php else : ?>
                                                    <td class="editable-cell" data-field="morning_in" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                                    <td class="editable-cell" data-field="morning_out" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                                    <td class="editable-cell" data-field="afternoon_in" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                                    <td class="editable-cell" data-field="afternoon_out" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                                    <td class="editable-cell" data-field="undertime_hours" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                                    <td class="editable-cell" data-field="undertime_minutes" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                                <?php endif; ?>
                                            </tr>
                                        <?php endif ?>
                                    <?php else : ?>
                                        <tr data-date="<?= $current_date ?>" data-copy="1">
                                            <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= $i ?></td>
                                            <?php if ($is_weekend || $is_holiday) : ?>
                                                <td colspan="6" class="editable-label" data-field="label" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 10px; font-family: 'Times New Roman', serif; color: black;"><strong><?= $is_weekend ? getWeekendLabel($current_date) : 'HOLIDAY' ?></strong></td>
                                            <?php else : ?>
                                                <td class="editable-cell" data-field="morning_in" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                                <td class="editable-cell" data-field="morning_out" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                                <td class="editable-cell" data-field="afternoon_in" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                                <td class="editable-cell" data-field="afternoon_out" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                                <td class="editable-cell" data-field="undertime_hours" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                                <td class="editable-cell" data-field="undertime_minutes" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
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
                                        <!--<div style="border-bottom: 1px solid black; width: 250px; margin: 0 auto 2px auto; height: 15px;"></div>-->
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
                                        <!--<strong style="font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></strong><br>-->
                                        <span style="font-size: 13px; font-family: 'Times New Roman', serif; color: black;">Immediate Supervisor</span>
                                    </div>
                                </td>
                            </tr>
                            <!--
                            <tr>
                                <td colspan="7" style="padding: 5px;">
                                    <div style="text-align: left; font-size: 13px; font-family: 'Times New Roman', serif; color: black;">
                                        Approved by:
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="7" style="padding: 10px 5px;">
                                    <div style="text-align: center;">
                                        <div style="border-bottom: 1px solid black; width: 250px; margin: 0 auto 2px auto; height: 15px;"></div>
                                        <strong style="font-size: 13px; font-family: 'Times New Roman', serif; text-decoration: underline; color: black;">GLINARD L. QUEZADA, MD, FPSGS, MBA-HA</strong><br>
                                        <span style="font-size: 13px; font-family: 'Times New Roman', serif; color: black;">Medical Center Chief I</span>
                                    </div>
                                </td>
                            </tr> -->
                        </table>
                        
                        <!-- <small class="text-center" style="font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><i>Generated thru DOGH - Time Record System</i></small> -->
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
                                        <?= isset($date) ? strtoupper(date('F Y', strtotime($date . '-01'))) : strtoupper(date('F Y', strtotime('first day of last month'))) ?>
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
                                            <tr data-date="<?= $current_date ?>" data-copy="2">
                                                <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= $i ?></td>
                                                <?php if ($is_weekend || $is_holiday) : ?>
                                                    <?php 
                                                    $has_entries = !empty($time[$b2]->morning_in) || !empty($time[$b2]->morning_out) || 
                                                                  !empty($time[$b2]->afternoon_in) || !empty($time[$b2]->afternoon_out);
                                                    
                                                    if ($has_entries) : // Show time entries without label (treat as regular workday) ?>
                                                        <td class="editable-cell" data-field="morning_in" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= !empty($time[$b2]->morning_in) ? date('h:i', strtotime($time[$b2]->morning_in)) : '' ?></td>
                                                        <td class="editable-cell" data-field="morning_out" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= !empty($time[$b2]->morning_out) ? date('h:i', strtotime($time[$b2]->morning_out)) : '' ?></td>
                                                        <td class="editable-cell" data-field="afternoon_in" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= !empty($time[$b2]->afternoon_in) ? date('h:i', strtotime($time[$b2]->afternoon_in)) : '' ?></td>
                                                        <td class="editable-cell" data-field="afternoon_out" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= !empty($time[$b2]->afternoon_out) ? date('h:i', strtotime($time[$b2]->afternoon_out)) : '' ?></td>
                                                        <td class="editable-cell" data-field="undertime_hours" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= isset($time[$b2]->undertime_hours) && $time[$b2]->undertime_hours !== null ? $time[$b2]->undertime_hours : '' ?></td>
                                                        <td class="editable-cell" data-field="undertime_minutes" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= isset($time[$b2]->undertime_minutes) && $time[$b2]->undertime_minutes !== null ? $time[$b2]->undertime_minutes : '' ?></td>
                                                    <?php else : ?>
                                                        <td colspan="6" class="editable-label" data-field="label" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><strong><?= $is_weekend ? getWeekendLabel($current_date) : 'HOLIDAY' ?></strong></td>
                                                    <?php endif; ?>
                                                <?php else : // Regular workday for second DTR copy ?>
                                                    <?php 
                                                    // Check if this is a full-day absence (all time entries are empty)
                                                    $is_full_day_absent_2 = empty($time[$b2]->morning_in) && empty($time[$b2]->morning_out) && 
                                                                            empty($time[$b2]->afternoon_in) && empty($time[$b2]->afternoon_out);
                                                    
                                                    if ($is_full_day_absent_2) : 
                                                        // Full day absence - show completely blank row
                                                    ?>
                                                        <td class="editable-cell" data-field="morning_in" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                                        <td class="editable-cell" data-field="morning_out" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                                        <td class="editable-cell" data-field="afternoon_in" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                                        <td class="editable-cell" data-field="afternoon_out" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                                        <td class="editable-cell" data-field="undertime_hours" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                                        <td class="editable-cell" data-field="undertime_minutes" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                                    <?php else : 
                                                        // Partial attendance - show times and undertime
                                                    ?>
                                                        <td class="editable-cell" data-field="morning_in" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= !empty($time[$b2]->morning_in) ? date('h:i', strtotime($time[$b2]->morning_in)) : '' ?></td>
                                                        <td class="editable-cell" data-field="morning_out" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= !empty($time[$b2]->morning_out) ? date('h:i', strtotime($time[$b2]->morning_out)) : '' ?></td>
                                                        <td class="editable-cell" data-field="afternoon_in" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= !empty($time[$b2]->afternoon_in) ? date('h:i', strtotime($time[$b2]->afternoon_in)) : '' ?></td>
                                                        <td class="editable-cell" data-field="afternoon_out" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= !empty($time[$b2]->afternoon_out) ? date('h:i', strtotime($time[$b2]->afternoon_out)) : '' ?></td>
                                                        <td class="editable-cell" data-field="undertime_hours" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= isset($time[$b2]->undertime_hours) && $time[$b2]->undertime_hours !== null ? $time[$b2]->undertime_hours : '' ?></td>
                                                        <td class="editable-cell" data-field="undertime_minutes" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= isset($time[$b2]->undertime_minutes) && $time[$b2]->undertime_minutes !== null ? $time[$b2]->undertime_minutes : '' ?></td>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </tr>
                                        <?php $b2++;
                                        else : ?>
                                            <tr data-date="<?= $current_date ?>" data-copy="2">
                                                <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= $i ?></td>
                                                <?php if ($is_weekend || $is_holiday) : ?>
                                                    <td colspan="6" class="editable-label" data-field="label" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><strong><?= $is_weekend ? getWeekendLabel($current_date) : 'HOLIDAY' ?></strong></td>
                                                <?php else : ?>
                                                    <td class="editable-cell" data-field="morning_in" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                                    <td class="editable-cell" data-field="morning_out" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                                    <td class="editable-cell" data-field="afternoon_in" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                                    <td class="editable-cell" data-field="afternoon_out" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                                    <td class="editable-cell" data-field="undertime_hours" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                                    <td class="editable-cell" data-field="undertime_minutes" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                                <?php endif; ?>
                                            </tr>
                                        <?php endif ?>
                                    <?php else : ?>
                                        <tr data-date="<?= $current_date ?>" data-copy="2">
                                            <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= $i ?></td>
                                            <?php if ($is_weekend || $is_holiday) : ?>
                                                <td colspan="6" class="editable-label" data-field="label" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><strong><?= $is_weekend ? getWeekendLabel($current_date) : 'HOLIDAY' ?></strong></td>
                                            <?php else : ?>
                                                <td class="editable-cell" data-field="morning_in" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                                <td class="editable-cell" data-field="morning_out" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                                <td class="editable-cell" data-field="afternoon_in" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                                <td class="editable-cell" data-field="afternoon_out" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                                <td class="editable-cell" data-field="undertime_hours" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
                                                <td class="editable-cell" data-field="undertime_minutes" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></td>
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
                                        <!--<div style="border-bottom: 1px solid black; width: 250px; margin: 0 auto 2px auto; height: 15px;"></div>-->
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
                                        <!--<strong style="font-size: 13px; font-family: 'Times New Roman', serif; color: black;"></strong><br> -->
                                        <span style="font-size: 13px; font-family: 'Times New Roman', serif; color: black;">Immediate Supervisor</span>
                                    </div>
                                </td>
                            </tr>
                            <!--
                            <tr>
                                <td colspan="7" style="padding: 5px;">
                                    <div style="text-align: left; font-size: 13px; font-family: 'Times New Roman', serif; color: black;">
                                        Approved by:
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="7" style="padding: 10px 5px;">
                                    <div style="text-align: center;">
                                        <div style="border-bottom: 1px solid black; width: 250px; margin: 0 auto 2px auto; height: 15px;"></div>
                                        <strong style="font-size: 13px; font-family: 'Times New Roman', serif; text-decoration: underline; color: black;">GLINARD L. QUEZADA, MD, FPSGS, MBA-HA</strong><br>
                                        <span style="font-size: 13px; font-family: 'Times New Roman', serif; color: black;">Medical Center Chief I</span>
                                    </div>
                                </td>
                            </tr> -->
                        </table>
                        
                        <!-- <small class="text-center" style="font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><i>Generated thru DOGH - Time Record System</i></small> -->
                        </div>
                        
                        <div class="clearfix" style='clear: both;'></div>
                    </div>
                    
                <?php $a++;
                endforeach ?>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('attendance/modal') ?>

<style>
/* Edit Mode Styles */
.editable-cell.edit-mode,
.editable-label.edit-mode {
    background-color: #fff3cd !important;
    cursor: pointer;
    position: relative;
}

.editable-cell.edit-mode:hover,
.editable-label.edit-mode:hover {
    background-color: #ffeaa7 !important;
}

.editable-cell.editing,
.editable-label.editing {
    background-color: #d4edda !important;
    padding: 0 !important;
}

.editable-cell input,
.editable-label input,
.editable-label select {
    width: 100%;
    border: 2px solid #28a745;
    padding: 2px;
    text-align: center;
    font-size: 13px;
    font-family: 'Times New Roman', serif;
    box-sizing: border-box;
}

.editable-label select {
    font-weight: bold;
}

/* Partial label styling */
.editable-cell strong {
    font-weight: bold;
    font-size: 13px;
    font-family: 'Times New Roman', serif;
}

.edit-mode-indicator {
    position: fixed;
    top: 80px;
    right:25rem;
    background-color: #ffc107;
    color: #000;
    padding: 10px 20px;
    border-radius: 5px;
    font-weight: bold;
    z-index: 1000;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}

@media print {
    .editable-cell.edit-mode,
    .editable-label.edit-mode {
        background-color: transparent !important;
    }
    .edit-mode-indicator {
        display: none;
    }
}
</style>

<script>
let editMode = false;
let editedData = {};
// Store personnel info for saving
const personnelData = <?= json_encode(array_map(function($p) { 
    return ['bio_id' => $p->bio_id, 'email' => $p->email]; 
}, $person)) ?>;

// Undertime calculation constants (8-5pm work schedule, 12-1pm lunch break)
const STANDARD_AM_IN = 8 * 60;      // 8:00 AM = 480 minutes
const STANDARD_AM_OUT = 12 * 60;    // 12:00 PM = 720 minutes
const STANDARD_PM_IN = 13 * 60;     // 1:00 PM = 780 minutes
const STANDARD_PM_OUT = 17 * 60;    // 5:00 PM = 1020 minutes

// Convert time string (HH:MM or h:mm) to minutes from midnight
function timeToMinutes(timeStr) {
    if (!timeStr || timeStr.trim() === '') return null;
    
    // Handle various time formats
    let hours, minutes;
    const cleanTime = timeStr.trim();
    
    // Check for HH:MM format
    const match = cleanTime.match(/^(\d{1,2}):(\d{2})$/);
    if (match) {
        hours = parseInt(match[1]);
        minutes = parseInt(match[2]);
        
        // Convert 12-hour format to 24-hour if needed
        // Assume times 1-7 are PM (13:00-19:00) and 8-12 are AM
        if (hours >= 1 && hours <= 7) {
            hours += 12; // Convert to PM
        }
        
        return hours * 60 + minutes;
    }
    
    return null;
}

// Calculate undertime based on time entries
function calculateUndertime(amIn, amOut, pmIn, pmOut) {
    const hasAmIn = amIn && amIn.trim() !== '';
    const hasAmOut = amOut && amOut.trim() !== '';
    const hasPmIn = pmIn && pmIn.trim() !== '';
    const hasPmOut = pmOut && pmOut.trim() !== '';
    
    // If completely absent (no time entries at all), return null
    if (!hasAmIn && !hasAmOut && !hasPmIn && !hasPmOut) {
        return null;
    }
    
    let undertimeMinutes = 0;
    
    // Convert time strings to minutes from midnight
    const actualAmIn = hasAmIn ? timeToMinutes(amIn) : null;
    const actualAmOut = hasAmOut ? timeToMinutes(amOut) : null;
    const actualPmIn = hasPmIn ? timeToMinutes(pmIn) : null;
    const actualPmOut = hasPmOut ? timeToMinutes(pmOut) : null;
    
    // Check if we have complete sessions
    const hasCompleteMorning = hasAmIn && hasAmOut;
    const hasCompleteAfternoon = hasPmIn && hasPmOut;
    
    // Calculate morning session undertime
    if (hasCompleteMorning) {
        // Complete morning session - calculate based on actual times
        // Late arrival (after 8:00 AM)
        if (actualAmIn > STANDARD_AM_IN) {
            undertimeMinutes += (actualAmIn - STANDARD_AM_IN);
        }
        // Early departure (before 12:00 PM)
        if (actualAmOut < STANDARD_AM_OUT) {
            undertimeMinutes += (STANDARD_AM_OUT - actualAmOut);
        }
    } else if (hasAmIn || hasAmOut) {
        // Incomplete morning session (only in or only out) = 4 hours undertime
        undertimeMinutes += 240; // 4 hours = 240 minutes
    } else {
        // No morning session at all = 4 hours undertime
        undertimeMinutes += 240; // 4 hours = 240 minutes
    }
    
    // Calculate afternoon session undertime
    if (hasCompleteAfternoon) {
        // Complete afternoon session - calculate based on actual times
        // Late arrival (after 1:00 PM)
        if (actualPmIn > STANDARD_PM_IN) {
            undertimeMinutes += (actualPmIn - STANDARD_PM_IN);
        }
        // Early departure (before 5:00 PM)
        if (actualPmOut < STANDARD_PM_OUT) {
            undertimeMinutes += (STANDARD_PM_OUT - actualPmOut);
        }
    } else if (hasPmIn || hasPmOut) {
        // Incomplete afternoon session (only in or only out) = 4 hours undertime
        undertimeMinutes += 240; // 4 hours = 240 minutes
    } else {
        // No afternoon session at all = 4 hours undertime
        undertimeMinutes += 240; // 4 hours = 240 minutes
    }
    
    // Convert total undertime minutes to hours and minutes
    const undertimeHours = Math.floor(undertimeMinutes / 60);
    const remainingMinutes = undertimeMinutes % 60;
    
    return {
        hours: undertimeHours,
        minutes: remainingMinutes,
        totalMinutes: undertimeMinutes
    };
}

// Get current time values from a row
function getRowTimeValues(row) {
    const amInCell = row.querySelector('[data-field="morning_in"]');
    const amOutCell = row.querySelector('[data-field="morning_out"]');
    const pmInCell = row.querySelector('[data-field="afternoon_in"]');
    const pmOutCell = row.querySelector('[data-field="afternoon_out"]');
    
    return {
        amIn: amInCell ? amInCell.textContent.trim() : '',
        amOut: amOutCell ? amOutCell.textContent.trim() : '',
        pmIn: pmInCell ? pmInCell.textContent.trim() : '',
        pmOut: pmOutCell ? pmOutCell.textContent.trim() : ''
    };
}

// Update undertime cells in a row based on calculated values
function updateRowUndertime(row, date, copy) {
    const timeValues = getRowTimeValues(row);
    const undertime = calculateUndertime(timeValues.amIn, timeValues.amOut, timeValues.pmIn, timeValues.pmOut);
    
    const hoursCell = row.querySelector('[data-field="undertime_hours"]');
    const minutesCell = row.querySelector('[data-field="undertime_minutes"]');
    
    if (hoursCell && minutesCell) {
        if (undertime === null) {
            hoursCell.textContent = '';
            minutesCell.textContent = '';
        } else {
            hoursCell.textContent = undertime.hours;
            minutesCell.textContent = undertime.minutes;
        }
        
        // Store in editedData
        const key = `${date}_${copy}`;
        if (!editedData[key]) {
            editedData[key] = { date: date, copy: copy };
        }
        editedData[key]['undertime_hours'] = undertime ? undertime.hours.toString() : '';
        editedData[key]['undertime_minutes'] = undertime ? undertime.minutes.toString() : '';
        
        // Sync with other copy
        syncCopies(date, copy, 'undertime_hours', undertime ? undertime.hours.toString() : '');
        syncCopies(date, copy, 'undertime_minutes', undertime ? undertime.minutes.toString() : '');
    }
}

// Toggle Edit Mode
function toggleEditMode() {
    editMode = !editMode;
    const btn = document.getElementById('toggleEditMode');
    const saveBtn = document.getElementById('saveChanges');
    
    if (editMode) {
        btn.classList.remove('btn-warning');
        btn.classList.add('btn-secondary');
        btn.innerHTML = '<span class="btn-label"><i class="fa fa-eye"></i></span> View Mode';
        saveBtn.style.display = 'inline-block';
        
        // Add edit mode class to all editable cells
        document.querySelectorAll('.editable-cell, .editable-label').forEach(cell => {
            cell.classList.add('edit-mode');
        });
        
        // Show indicator
        if (!document.querySelector('.edit-mode-indicator')) {
            const indicator = document.createElement('div');
            indicator.className = 'edit-mode-indicator';
            indicator.innerHTML = '✏️ EDIT MODE ACTIVE - Click cells to edit';
            document.body.appendChild(indicator);
        }
        
        // Add click listeners
        addEditListeners();
    } else {
        btn.classList.remove('btn-secondary');
        btn.classList.add('btn-warning');
        btn.innerHTML = '<span class="btn-label"><i class="fa fa-edit"></i></span> Edit Mode';
        saveBtn.style.display = 'none';
        
        // Remove edit mode class
        document.querySelectorAll('.editable-cell, .editable-label').forEach(cell => {
            cell.classList.remove('edit-mode');
        });
        
        // Remove indicator
        const indicator = document.querySelector('.edit-mode-indicator');
        if (indicator) indicator.remove();
        
        // Remove click listeners
        removeEditListeners();
    }
}

// Add click listeners to editable cells
function addEditListeners() {
    document.querySelectorAll('.editable-cell.edit-mode').forEach(cell => {
        cell.addEventListener('click', handleCellClick);
    });
    
    document.querySelectorAll('.editable-label.edit-mode').forEach(cell => {
        cell.addEventListener('click', handleLabelClick);
    });
}

// Remove click listeners
function removeEditListeners() {
    document.querySelectorAll('.editable-cell').forEach(cell => {
        cell.removeEventListener('click', handleCellClick);
    });
    
    document.querySelectorAll('.editable-label').forEach(cell => {
        cell.removeEventListener('click', handleLabelClick);
    });
}

// Handle cell click for time/undertime fields
function handleCellClick(e) {
    if (!editMode) return;
    
    const cell = e.currentTarget;
    if (cell.classList.contains('editing')) return;
    
    const currentValue = cell.textContent.trim();
    const field = cell.getAttribute('data-field');
    const row = cell.closest('tr');
    const date = row.getAttribute('data-date');
    const copy = row.getAttribute('data-copy');
    
    cell.classList.add('editing');
    
    // Create input based on field type
    let input;
    if (field.includes('undertime')) {
        input = document.createElement('input');
        input.type = 'number';
        input.min = '0';
        input.max = field.includes('hours') ? '8' : '59';
        input.value = currentValue;
    } else {
        // For time fields, offer both time input and label selection
        const container = document.createElement('div');
        container.style.cssText = 'display: flex; flex-direction: column; gap: 2px;';
        
        input = document.createElement('input');
        input.type = 'text';
        input.placeholder = 'HH:MM';
        input.value = currentValue;
        input.style.cssText = 'width: 100%; border: 2px solid #28a745; padding: 2px; text-align: center; font-size: 13px; font-family: "Times New Roman", serif;';
        
        const select = document.createElement('select');
        select.style.cssText = 'width: 100%; border: 2px solid #28a745; padding: 2px; text-align: center; font-size: 13px; font-family: "Times New Roman", serif; font-weight: bold;';
        
        const labelOptions = [
            { value: '', text: '-- Or Select Label --' },
            { value: 'ABSENT:full', text: 'ABSENT (Full Row)' },
            { value: 'OFFICIAL BUSINESS:full', text: 'OFFICIAL BUSINESS (Full Row)' },
            { value: 'OFFICIAL TIME:full', text: 'OFFICIAL TIME (Full Row)' },
            { value: 'OFF:full', text: 'OFF (Full Row)' },
            { value: 'LEAVE:full', text: 'LEAVE (Full Row)' },
            { value: 'SPL:full', text: 'SPL (Full Row)' },
            { value: 'SICK LEAVE:full', text: 'SICK LEAVE (Full Row)' },
            { value: 'VACATION LEAVE:full', text: 'VACATION LEAVE (Full Row)' },
            { value: 'TRAINING:full', text: 'TRAINING (Full Row)' },
            { value: 'HOLIDAY:full', text: 'HOLIDAY (Full Row)' },
            { value: '---', text: '--- Partial Labels ---', disabled: true },
            { value: 'ABSENT:partial', text: 'ABSENT (This Cell Only)' },
            { value: 'OFFICIAL BUSINESS:partial', text: 'OFFICIAL BUSINESS (This Cell Only)' },
            { value: 'OFFICIAL TIME:partial', text: 'OFFICIAL TIME (This Cell Only)' },
            { value: 'OFF:partial', text: 'OFF (This Cell Only)' },
            { value: 'LEAVE:partial', text: 'LEAVE (This Cell Only)' },
            { value: 'SICK LEAVE:partial', text: 'SICK LEAVE (This Cell Only)' },
            { value: 'VACATION LEAVE:partial', text: 'VACATION LEAVE (This Cell Only)' },
            { value: 'TRAINING:partial', text: 'TRAINING (This Cell Only)' },
            { value: 'HOLIDAY:partial', text: 'HOLIDAY (This Cell Only)' }
        ];
        
        labelOptions.forEach(opt => {
            const option = document.createElement('option');
            option.value = opt.value;
            option.textContent = opt.text;
            if (opt.disabled) option.disabled = true;
            select.appendChild(option);
        });
        
        container.appendChild(input);
        container.appendChild(select);
        cell.innerHTML = '';
        cell.appendChild(container);
        input.focus();
        input.select();
        
        // Handle label selection
        select.addEventListener('change', function() {
            if (select.value && select.value !== '---') {
                const [labelText, mergeType] = select.value.split(':');
                
                if (mergeType === 'full') {
                    // Convert entire row to merged label cell
                    convertToLabelCell(row, date, copy, labelText);
                } else if (mergeType === 'partial') {
                    // Apply label to this cell only
                    cell.innerHTML = '<strong>' + labelText + '</strong>';
                    cell.classList.remove('editing');
                    
                    // Store the edit
                    const key = `${date}_${copy}`;
                    if (!editedData[key]) {
                        editedData[key] = { date: date, copy: copy };
                    }
                    editedData[key][field] = labelText;
                    
                    // Sync with the other copy
                    syncCopies(date, copy, field, labelText);
                }
            }
        });
        
        // Handle blur (save time)
        input.addEventListener('blur', function() {
            setTimeout(() => {
                if (document.activeElement !== select) {
                    const newValue = input.value.trim();
                    cell.textContent = newValue;
                    cell.classList.remove('editing');
                    
                    // Store the edit
                    const key = `${date}_${copy}`;
                    if (!editedData[key]) {
                        editedData[key] = { date: date, copy: copy };
                    }
                    editedData[key][field] = newValue;
                    
                    // Sync with the other copy
                    syncCopies(date, copy, field, newValue);
                    
                    // Auto-calculate undertime after time field change
                    updateRowUndertime(row, date, copy);
                }
            }, 200);
        });
        
        // Handle Enter key
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                input.blur();
            }
        });
        
        return; // Exit early since we've set up custom handling
    }
    
    cell.innerHTML = '';
    cell.appendChild(input);
    input.focus();
    input.select();
    
    // Handle blur (save)
    input.addEventListener('blur', function() {
        const newValue = input.value.trim();
        cell.textContent = newValue;
        cell.classList.remove('editing');
        
        // Store the edit
        const key = `${date}_${copy}`;
        if (!editedData[key]) {
            editedData[key] = { date: date, copy: copy };
        }
        editedData[key][field] = newValue;
        
        // Sync with the other copy
        syncCopies(date, copy, field, newValue);
    });
    
    // Handle Enter key
    input.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            input.blur();
        }
    });
}

// Handle label click for weekend/holiday labels
function handleLabelClick(e) {
    if (!editMode) return;
    
    const cell = e.currentTarget;
    if (cell.classList.contains('editing')) return;
    
    const currentValue = cell.textContent.trim();
    const row = cell.closest('tr');
    const date = row.getAttribute('data-date');
    const copy = row.getAttribute('data-copy');
    
    cell.classList.add('editing');
    
    // Create select dropdown with common options
    const select = document.createElement('select');
    const options = [
        '', 
        'SATURDAY', 
        'SUNDAY', 
        'HOLIDAY', 
        'ABSENT', 
        'OFFICIAL BUSINESS',
        'OFFICIAL TIME',
        'OFF', 
        'LEAVE',
        'SICK LEAVE',
        'VACATION LEAVE',
        'TRAINING'
    ];
    
    options.forEach(opt => {
        const option = document.createElement('option');
        option.value = opt;
        option.textContent = opt;
        if (opt === currentValue || (opt && currentValue.includes(opt))) {
            option.selected = true;
        }
        select.appendChild(option);
    });
    
    cell.innerHTML = '';
    cell.appendChild(select);
    select.focus();
    
    // Handle change
    select.addEventListener('change', function() {
        const newValue = select.value;
        if (newValue) {
            cell.innerHTML = '<strong>' + newValue + '</strong>';
        } else {
            // Convert to individual cells
            convertLabelToTimeCells(row, date, copy);
            return;
        }
        cell.classList.remove('editing');
        
        // Store the edit
        const key = `${date}_${copy}`;
        if (!editedData[key]) {
            editedData[key] = { date: date, copy: copy };
        }
        editedData[key]['label'] = newValue;
        
        // Sync with the other copy
        syncCopies(date, copy, 'label', newValue);
    });
    
    // Handle blur
    select.addEventListener('blur', function() {
        const newValue = select.value;
        if (newValue) {
            cell.innerHTML = '<strong>' + newValue + '</strong>';
        } else {
            cell.textContent = currentValue;
        }
        cell.classList.remove('editing');
    });
}

// Convert individual time cells to merged label cell
function convertToLabelCell(row, date, copy, labelValue) {
    // Remove all existing time and undertime cells
    const cellsToRemove = row.querySelectorAll('.editable-cell');
    cellsToRemove.forEach(cell => cell.remove());
    
    // Create new merged label cell
    const labelCell = document.createElement('td');
    labelCell.colSpan = 6;
    labelCell.className = 'editable-label edit-mode';
    labelCell.setAttribute('data-field', 'label');
    labelCell.style.cssText = 'border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: "Times New Roman", serif; color: black;';
    labelCell.innerHTML = '<strong>' + labelValue + '</strong>';
    
    // Add to row
    row.appendChild(labelCell);
    
    // Add click listener
    labelCell.addEventListener('click', handleLabelClick);
    
    // Store the edit
    const key = `${date}_${copy}`;
    if (!editedData[key]) {
        editedData[key] = { date: date, copy: copy };
    }
    editedData[key]['label'] = labelValue;
    // Clear any time entries
    editedData[key]['morning_in'] = '';
    editedData[key]['morning_out'] = '';
    editedData[key]['afternoon_in'] = '';
    editedData[key]['afternoon_out'] = '';
    editedData[key]['undertime_hours'] = '';
    editedData[key]['undertime_minutes'] = '';
    
    // Sync with the other copy
    const otherCopy = copy === '1' ? '2' : '1';
    const otherRow = document.querySelector(`tr[data-date="${date}"][data-copy="${otherCopy}"]`);
    if (otherRow) {
        // Check if other row already has a label cell
        const existingLabel = otherRow.querySelector('.editable-label');
        if (!existingLabel) {
            convertToLabelCell(otherRow, date, otherCopy, labelValue);
        } else {
            existingLabel.innerHTML = '<strong>' + labelValue + '</strong>';
        }
    }
}

// Convert label cell to individual time cells
function convertLabelToTimeCells(row, date, copy) {
    const labelCell = row.querySelector('.editable-label');
    if (!labelCell) return;
    
    // Remove the label cell
    labelCell.remove();
    
    // Add individual cells
    const fields = ['morning_in', 'morning_out', 'afternoon_in', 'afternoon_out', 'undertime_hours', 'undertime_minutes'];
    fields.forEach(field => {
        const td = document.createElement('td');
        td.className = 'editable-cell edit-mode';
        td.setAttribute('data-field', field);
        td.style.cssText = 'border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: "Times New Roman", serif; color: black;';
        td.textContent = '';
        row.appendChild(td);
        td.addEventListener('click', handleCellClick);
    });
    
    // Store the conversion
    const key = `${date}_${copy}`;
    if (!editedData[key]) {
        editedData[key] = { date: date, copy: copy };
    }
    editedData[key]['converted'] = true;
    editedData[key]['label'] = ''; // Clear the label
    
    // Sync with other copy - but prevent infinite loop
    const otherCopy = copy === '1' ? '2' : '1';
    const otherRow = document.querySelector(`tr[data-date="${date}"][data-copy="${otherCopy}"]`);
    if (otherRow) {
        const otherLabelCell = otherRow.querySelector('.editable-label');
        if (otherLabelCell) {
            // Only convert if it still has a label cell
            convertLabelToTimeCells(otherRow, date, otherCopy);
        }
    }
}

// Sync changes between both DTR copies
function syncCopies(date, currentCopy, field, value) {
    const otherCopy = currentCopy === '1' ? '2' : '1';
    const otherRow = document.querySelector(`tr[data-date="${date}"][data-copy="${otherCopy}"]`);
    
    if (!otherRow) return;
    
    if (field === 'label') {
        const otherLabel = otherRow.querySelector('.editable-label');
        if (otherLabel) {
            if (value) {
                otherLabel.innerHTML = '<strong>' + value + '</strong>';
            }
        }
    } else {
        // For time and undertime fields
        const otherCell = otherRow.querySelector(`[data-field="${field}"]`);
        if (otherCell) {
            // Only update if not currently being edited
            if (!otherCell.classList.contains('editing')) {
                // Check if value is a label (text) or time (contains colon or is number)
                const isLabel = value && !value.match(/^\d{1,2}:\d{2}$/) && !value.match(/^\d+$/);
                
                if (isLabel) {
                    // It's a partial label, apply bold formatting
                    otherCell.innerHTML = '<strong>' + value + '</strong>';
                } else {
                    // It's a time or number, just set text
                    otherCell.textContent = value;
                }
            }
            
            // Also store in editedData for the other copy
            const otherKey = `${date}_${otherCopy}`;
            if (!editedData[otherKey]) {
                editedData[otherKey] = { date: date, copy: otherCopy };
            }
            editedData[otherKey][field] = value;
        }
    }
}

// Save changes
function saveChanges() {
    if (Object.keys(editedData).length === 0) {
        alert('No changes to save.');
        return;
    }
    
    // Prompt for reason before saving
    const reason = prompt('Please provide a reason for this DTR edit (required for audit trail):', 'DTR Edit via Generate DTR Page');
    
    if (reason === null) {
        // User cancelled
        return;
    }
    
    if (!reason.trim()) {
        alert('A reason is required to save changes.');
        return;
    }
    
    if (!confirm('Save all changes? This will update the DTR records and be recorded in the edit monitoring.')) {
        return;
    }
    
    // Show loading
    const saveBtn = document.getElementById('saveChanges');
    const originalText = saveBtn.innerHTML;
    saveBtn.innerHTML = '<span class="btn-label"><i class="fa fa-spinner fa-spin"></i></span> Saving...';
    saveBtn.disabled = true;
    
    // Prepare data for submission - only use copy 1 to avoid duplicates
    const changesArray = Object.values(editedData).filter(change => change.copy === '1');
    
    // Send AJAX request
    fetch('<?= site_url('attendance/save_dtr_edits') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            changes: changesArray,
            month: document.getElementById('month').value,
            personnel: personnelData,
            reason: reason.trim()
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Changes saved successfully! ' + data.message);
            editedData = {};
            // Reload the page to show updated data
            location.reload();
        } else {
            alert('Error saving changes: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        alert('Error saving changes: ' + error.message);
    })
    .finally(() => {
        saveBtn.innerHTML = originalText;
        saveBtn.disabled = false;
    });
}

// Month change handler
document.getElementById('month').addEventListener('change', function() {
    window.location.href = '<?= site_url('admin/generate_dtr') ?>?date=' + this.value;
});
</script>