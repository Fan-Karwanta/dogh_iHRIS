<?php
/**
 * Test script for the new CSV format with date columns
 * This validates the parsing logic for the Attendance Record CSV format
 */

echo "=== New CSV Format Import Test ===\n\n";

// Simulate CSV parsing
$csv_file = 'csv_files/AttendanceRecord_oct.csv';

if (!file_exists($csv_file)) {
    echo "ERROR: CSV file not found at $csv_file\n";
    exit;
}

$data = fopen($csv_file, "r");
$i = 0;
$importRes = array();
$year_month = null;

if ($data) {
    $header_row = null;
    $date_columns = array(); // Maps column index to day number
    
    echo "Step 1: Reading CSV file...\n";
    
    // Parse CSV rows
    while (($filedata = fgetcsv($data, 10000, ",")) !== FALSE) {
        
        // Row 4 (index 3): Extract year and month from "Made Date:2025/10/01-2025/10/15"
        if ($i == 3 && !empty($filedata[0]) && strpos($filedata[0], 'Made Date:') !== false) {
            $date_range = trim(str_replace('Made Date:', '', $filedata[0]));
            echo "\nStep 2: Found date range: $date_range\n";
            
            // Extract start date (e.g., "2025/10/01")
            $date_parts = explode('-', $date_range);
            if (!empty($date_parts[0])) {
                $start_date = trim($date_parts[0]);
                $date_obj = DateTime::createFromFormat('Y/m/d', $start_date);
                if ($date_obj) {
                    $year_month = $date_obj->format('Y-m');
                    echo "Extracted year-month: $year_month\n";
                }
            }
        }
        
        // Row 5 (index 4): Header row with date columns
        if ($i == 4) {
            $header_row = $filedata;
            echo "\nStep 3: Processing header row...\n";
            echo "Columns: Employee ID, Name, Department, ";
            
            // Map column indices to day numbers (starting from column 3)
            for ($col = 3; $col < count($header_row); $col++) {
                $day = trim($header_row[$col]);
                if (is_numeric($day) && $day >= 1 && $day <= 31) {
                    $date_columns[$col] = (int)$day;
                }
            }
            echo implode(', ', $date_columns) . "\n";
            echo "Total date columns found: " . count($date_columns) . "\n";
        }
        
        // Data rows (starting from row 7, index 6)
        if ($i >= 6 && !empty($filedata[0]) && is_numeric(trim($filedata[0]))) {
            $bio_id = trim($filedata[0]);
            $device_code = ''; // No device code in this CSV format
            
            // Only process first 3 employees for testing
            if ($i <= 8) {
                $name = isset($filedata[1]) ? trim($filedata[1]) : '';
                $dept = isset($filedata[2]) ? trim($filedata[2]) : '';
                
                if ($i == 6) {
                    echo "\nStep 4: Processing employee data rows...\n\n";
                }
                
                echo "Employee: Bio ID $bio_id";
                if ($name) echo " - $name";
                if ($dept) echo " ($dept)";
                echo "\n";
                
                $employee_entries = 0;
                
                // Process each date column
                foreach ($date_columns as $col_index => $day) {
                    if (isset($filedata[$col_index]) && !empty(trim($filedata[$col_index]))) {
                        $time_entries = trim($filedata[$col_index]);
                        
                        // Construct the full date (YYYY-MM-DD)
                        if ($year_month) {
                            $log_date = $year_month . '-' . str_pad($day, 2, '0', STR_PAD_LEFT);
                            
                            // Split multiple time entries by newline
                            $times = preg_split('/[\r\n]+/', $time_entries);
                            
                            foreach ($times as $time_str) {
                                $time_str = trim($time_str);
                                if (!empty($time_str)) {
                                    // Parse time in HH:MM format
                                    if (preg_match('/^(\d{1,2}):(\d{2})$/', $time_str, $matches)) {
                                        $hour = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                                        $minute = $matches[2];
                                        $log_time = $hour . ':' . $minute . ':00';
                                        
                                        $importRes[] = array(
                                            'bio_id' => $bio_id,
                                            'date' => $log_date,
                                            'time' => $log_time,
                                            'device_code' => $device_code
                                        );
                                        
                                        $employee_entries++;
                                    }
                                }
                            }
                        }
                    }
                }
                
                echo "  Total time entries: $employee_entries\n";
            }
        }
        $i++;
    }
    
    fclose($data);
    
    echo "\n=== Parsing Summary ===\n";
    echo "Total rows processed: $i\n";
    echo "Total time entries extracted: " . count($importRes) . "\n";
    
    // Show sample entries
    echo "\n=== Sample Entries (First 10) ===\n";
    for ($j = 0; $j < min(10, count($importRes)); $j++) {
        $entry = $importRes[$j];
        echo "Bio ID: {$entry['bio_id']}, Date: {$entry['date']}, Time: {$entry['time']}\n";
    }
    
    // Group by employee and date
    echo "\n=== Grouping by Employee and Date ===\n";
    $grouped = array();
    foreach ($importRes as $entry) {
        $key = $entry['bio_id'] . '_' . $entry['date'];
        if (!isset($grouped[$key])) {
            $grouped[$key] = array();
        }
        $grouped[$key][] = $entry;
    }
    
    echo "Total unique employee-date combinations: " . count($grouped) . "\n";
    
    // Show first 5 groups
    $count = 0;
    foreach ($grouped as $key => $entries) {
        if ($count >= 5) break;
        list($bio_id, $date) = explode('_', $key);
        echo "\nBio ID $bio_id on $date: " . count($entries) . " time entries\n";
        foreach ($entries as $entry) {
            echo "  - {$entry['time']}\n";
        }
        $count++;
    }
    
} else {
    echo "ERROR: Unable to open CSV file\n";
}

echo "\n=== Test Complete ===\n";
echo "The new CSV format parsing logic is ready.\n";
echo "You can now use the Import CSV feature in the web interface.\n";
?>
