<?php
// Build DTR data lookup by date
$dtr_by_date = [];
foreach ($dtr_data as $record) {
    $dtr_by_date[$record->date] = $record;
}

// Get month info
$month_num = date('m', strtotime($selected_month . '-01'));
$year = date('Y', strtotime($selected_month . '-01'));
$days_in_month = date('t', strtotime($selected_month . '-01'));

// Get system info
$sys_query = $this->db->query("SELECT * FROM systems WHERE id=1");
$sys_info = $sys_query->row();

// Convert logo to base64 for reliable printing
$logo_base64 = '';
if (!empty($sys_info->system_logo)) {
    $logo_path = FCPATH . 'assets/uploads/' . $sys_info->system_logo;
    if (file_exists($logo_path)) {
        $logo_data = file_get_contents($logo_path);
        $logo_ext = pathinfo($logo_path, PATHINFO_EXTENSION);
        $mime_type = 'image/' . ($logo_ext === 'jpg' ? 'jpeg' : $logo_ext);
        $logo_base64 = 'data:' . $mime_type . ';base64,' . base64_encode($logo_data);
    }
}

// Helper functions
if (!function_exists('isWeekendPrint')) {
    function isWeekendPrint($date) { 
        $day_of_week = date('w', strtotime($date));
        return ($day_of_week == 0 || $day_of_week == 6);
    }
    function getWeekendLabelPrint($date) { 
        $day_of_week = date('w', strtotime($date));
        if ($day_of_week == 0) return 'SUNDAY';
        if ($day_of_week == 6) return 'SATURDAY';
        return 'WEEKEND';
    }
}

// Build changes lookup
$changes_lookup = [];
$label_changes = [];
foreach ($items as $date => $item_data) {
    foreach ($item_data['fields'] as $field => $item) {
        if ($field === 'label') {
            $label_changes[$date] = $item;
        } else {
            $changes_lookup[$date][$field] = $item;
        }
    }
}

// Calculate dynamic font size based on full name length (same as admin panel)
$full_name = strtoupper($personnel->lastname . ' ' . $personnel->firstname . ' ' . (!empty($personnel->middlename) ? substr($personnel->middlename, 0, 1) : ''));
$name_length = strlen($full_name);
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

// Convert DTR data to indexed array for iteration (same as admin panel)
$time = array_values($dtr_data);
?>
<!DOCTYPE html>
<html>
<head>
    <title>DTR - <?= $personnel->firstname ?> <?= $personnel->lastname ?> - <?= date('F Y', strtotime($selected_month . '-01')) ?></title>
    <style>
        @page {
            size: portrait;
            margin: 5mm;
        }
        
        * { 
            box-sizing: border-box; 
            margin: 0;
            padding: 0;
        }
        body { 
            font-family: 'Times New Roman', serif; 
            font-size: 9px; 
            color: black; 
            margin: 0; 
            padding: 5px;
            background: white;
        }
        table {
            border-spacing: 0;
        }
        .w-100 { width: 100%; }
        .print-btn { 
            position: fixed; 
            top: 10px; 
            right: 10px; 
            padding: 10px 20px; 
            background: #4CAF50; 
            color: white; 
            border: none; 
            cursor: pointer; 
            font-size: 14px; 
            border-radius: 5px; 
            z-index: 1000; 
        }
        .print-btn:hover { background: #45a049; }
        .dtr-container {
            width: 100%;
            page-break-inside: avoid;
        }
        .dtr-container::after {
            content: "";
            clear: both;
            display: table;
        }
        .dtr-copy {
            width: 49%;
            float: left;
            page-break-inside: avoid;
            transform: scale(0.95);
            transform-origin: top left;
            border: 2px solid black;
            padding: 8px;
        }
        .dtr-copy:first-child {
            margin-right: 2%;
        }
        .dtr-copy:last-child {
            float: right;
            transform-origin: top right;
        }
        
        @media print {
            @page {
                size: portrait;
                margin: 5mm;
            }
            body { 
                margin: 0; 
                padding: 0;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <button class="print-btn no-print" onclick="window.print()"><i class="fas fa-print"></i> Print DTR</button>
    
    <div class="dtr-container">
        <!-- First DTR Copy -->
        <div class="dtr-copy">
            <table class="w-100" style="border-collapse: collapse; table-layout: fixed;">
                <thead>
                    <?php if (!empty($logo_base64)) : ?>
                    <tr>
                        <td colspan="7" style="text-align: center; border: none; padding: 5px;">
                            <img src="<?= $logo_base64 ?>" alt="System Logo" style="max-height: 60px; max-width: 100px;">
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
                            <?= strtoupper($personnel->lastname) ?>
                        </td>
                        <td colspan="2" style="text-align: center; border: none; border-bottom: 1px solid black; padding: 5px 2px; font-size: <?= $name_font_size ?>; font-family: 'Times New Roman', serif; color: black; font-weight: bold;">
                            <?= strtoupper($personnel->firstname) ?>
                        </td>
                        <td colspan="2" style="text-align: center; border: none; border-bottom: 1px solid black; padding: 5px 2px; font-size: <?= $name_font_size ?>; font-family: 'Times New Roman', serif; color: black; font-weight: bold;"><?= !empty($personnel->middlename) ? strtoupper(substr($personnel->middlename, 0, 1)) : '' ?></td>
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
                            <?= strtoupper(date('F Y', strtotime($selected_month . '-01'))) ?>
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
                    <?php for ($i = 1; $i <= $days_in_month; $i++):
                        $date = $year . '-' . str_pad($month_num, 2, '0', STR_PAD_LEFT) . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
                        $is_weekend = isWeekendPrint($date);
                        $record = isset($dtr_by_date[$date]) ? $dtr_by_date[$date] : null;
                        $has_entries = $record && (!empty($record->am_in) || !empty($record->am_out) || !empty($record->pm_in) || !empty($record->pm_out));
                        $day_changes = isset($changes_lookup[$date]) ? $changes_lookup[$date] : [];
                        $has_label_change = isset($label_changes[$date]);
                    ?>
                    <tr>
                        <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= $i ?></td>
                        <?php if ($has_label_change): ?>
                            <td colspan="6" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><strong><?= $label_changes[$date]->new_value ?></strong></td>
                        <?php elseif ($is_weekend && !$has_entries && empty($day_changes)): ?>
                            <td colspan="6" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><strong><?= getWeekendLabelPrint($date) ?></strong></td>
                        <?php else:
                            $fields = ['morning_in' => 'am_in', 'morning_out' => 'am_out', 'afternoon_in' => 'pm_in', 'afternoon_out' => 'pm_out'];
                            foreach ($fields as $field => $db_field):
                                $change = isset($day_changes[$field]) ? $day_changes[$field] : null;
                                $value = $change ? $change->new_value : (isset($record->$db_field) ? $record->$db_field : '');
                        ?>
                            <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= $value ? date('h:i', strtotime($value)) : '' ?></td>
                        <?php endforeach; 
                            $ut_hours_change = isset($day_changes['undertime_hours']) ? $day_changes['undertime_hours'] : null;
                            $ut_mins_change = isset($day_changes['undertime_minutes']) ? $day_changes['undertime_minutes'] : null;
                            $ut_hours = $ut_hours_change ? $ut_hours_change->new_value : (isset($record->undertime_hours) ? $record->undertime_hours : '');
                            $ut_mins = $ut_mins_change ? $ut_mins_change->new_value : (isset($record->undertime_minutes) ? $record->undertime_minutes : '');
                        ?>
                            <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= $ut_hours ?></td>
                            <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= $ut_mins ?></td>
                        <?php endif; ?>
                    </tr>
                    <?php endfor; ?>
                    
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
                            <strong style="font-size: 13px; font-family: 'Times New Roman', serif; text-decoration: underline; color: black;"><?= strtoupper(!empty($personnel->middlename[0]) ? $personnel->firstname . ' ' . $personnel->middlename[0] . '. ' . $personnel->lastname : $personnel->firstname . ' ' . $personnel->lastname) ?></strong><br>
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
                            <?php if ($approver): ?>
                            <strong style="font-size: 13px; font-family: 'Times New Roman', serif; text-decoration: underline; color: black;"><?= strtoupper(!empty($approver->middlename[0]) ? $approver->firstname . ' ' . $approver->middlename[0] . '. ' . $approver->lastname : $approver->firstname . ' ' . $approver->lastname) ?></strong><br>
                            <?php else: ?>
                            <div style="border-bottom: 1px solid black; width: 250px; margin: 0 auto 2px auto; height: 15px;"></div>
                            <?php endif; ?>
                            <span style="font-size: 13px; font-family: 'Times New Roman', serif; color: black;">Immediate Supervisor</span>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        
        <!-- Second DTR Copy (Duplicate) -->
        <div class="dtr-copy">
            <table class="w-100" style="border-collapse: collapse; table-layout: fixed;">
                <thead>
                    <?php if (!empty($logo_base64)) : ?>
                    <tr>
                        <td colspan="7" style="text-align: center; border: none; padding: 5px;">
                            <img src="<?= $logo_base64 ?>" alt="System Logo" style="max-height: 60px; max-width: 100px;">
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
                            <?= strtoupper($personnel->lastname) ?>
                        </td>
                        <td colspan="2" style="text-align: center; border: none; border-bottom: 1px solid black; padding: 5px 2px; font-size: <?= $name_font_size ?>; font-family: 'Times New Roman', serif; color: black; font-weight: bold;">
                            <?= strtoupper($personnel->firstname) ?>
                        </td>
                        <td colspan="2" style="text-align: center; border: none; border-bottom: 1px solid black; padding: 5px 2px; font-size: <?= $name_font_size ?>; font-family: 'Times New Roman', serif; color: black; font-weight: bold;"><?= !empty($personnel->middlename) ? strtoupper(substr($personnel->middlename, 0, 1)) : '' ?></td>
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
                            <?= strtoupper(date('F Y', strtotime($selected_month . '-01'))) ?>
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
                    <?php for ($i = 1; $i <= $days_in_month; $i++):
                        $date = $year . '-' . str_pad($month_num, 2, '0', STR_PAD_LEFT) . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
                        $is_weekend = isWeekendPrint($date);
                        $record = isset($dtr_by_date[$date]) ? $dtr_by_date[$date] : null;
                        $has_entries = $record && (!empty($record->am_in) || !empty($record->am_out) || !empty($record->pm_in) || !empty($record->pm_out));
                        $day_changes = isset($changes_lookup[$date]) ? $changes_lookup[$date] : [];
                        $has_label_change = isset($label_changes[$date]);
                    ?>
                    <tr>
                        <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= $i ?></td>
                        <?php if ($has_label_change): ?>
                            <td colspan="6" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><strong><?= $label_changes[$date]->new_value ?></strong></td>
                        <?php elseif ($is_weekend && !$has_entries && empty($day_changes)): ?>
                            <td colspan="6" style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><strong><?= getWeekendLabelPrint($date) ?></strong></td>
                        <?php else:
                            $fields = ['morning_in' => 'am_in', 'morning_out' => 'am_out', 'afternoon_in' => 'pm_in', 'afternoon_out' => 'pm_out'];
                            foreach ($fields as $field => $db_field):
                                $change = isset($day_changes[$field]) ? $day_changes[$field] : null;
                                $value = $change ? $change->new_value : (isset($record->$db_field) ? $record->$db_field : '');
                        ?>
                            <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= $value ? date('h:i', strtotime($value)) : '' ?></td>
                        <?php endforeach; 
                            $ut_hours_change = isset($day_changes['undertime_hours']) ? $day_changes['undertime_hours'] : null;
                            $ut_mins_change = isset($day_changes['undertime_minutes']) ? $day_changes['undertime_minutes'] : null;
                            $ut_hours = $ut_hours_change ? $ut_hours_change->new_value : (isset($record->undertime_hours) ? $record->undertime_hours : '');
                            $ut_mins = $ut_mins_change ? $ut_mins_change->new_value : (isset($record->undertime_minutes) ? $record->undertime_minutes : '');
                        ?>
                            <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= $ut_hours ?></td>
                            <td style="border: 1px solid black; padding: 2px; text-align: center; font-size: 13px; font-family: 'Times New Roman', serif; color: black;"><?= $ut_mins ?></td>
                        <?php endif; ?>
                    </tr>
                    <?php endfor; ?>
                    
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
                            <strong style="font-size: 13px; font-family: 'Times New Roman', serif; text-decoration: underline; color: black;"><?= strtoupper(!empty($personnel->middlename[0]) ? $personnel->firstname . ' ' . $personnel->middlename[0] . '. ' . $personnel->lastname : $personnel->firstname . ' ' . $personnel->lastname) ?></strong><br>
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
                            <?php if ($approver): ?>
                            <strong style="font-size: 13px; font-family: 'Times New Roman', serif; text-decoration: underline; color: black;"><?= strtoupper(!empty($approver->middlename[0]) ? $approver->firstname . ' ' . $approver->middlename[0] . '. ' . $approver->lastname : $approver->firstname . ' ' . $approver->lastname) ?></strong><br>
                            <?php else: ?>
                            <div style="border-bottom: 1px solid black; width: 250px; margin: 0 auto 2px auto; height: 15px;"></div>
                            <?php endif; ?>
                            <span style="font-size: 13px; font-family: 'Times New Roman', serif; color: black;">Immediate Supervisor</span>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    
    <script>
        window.onload = function() {
            setTimeout(function() { window.print(); }, 500);
        };
    </script>
</body>
</html>
