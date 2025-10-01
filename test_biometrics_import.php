<?php
/**
 * Test script for the modified biometrics import functionality
 * This script tests the import with the new CSV format: bio_id,biometrics_time
 */

// Include CodeIgniter bootstrap
define('BASEPATH', TRUE);
require_once('index.php');

// Get CodeIgniter instance
$CI =& get_instance();
$CI->load->model('BiometricsModel', 'biometricsModel');
$CI->load->library('session');

echo "=== Biometrics Import Test ===\n\n";

// Test 1: Check if personnel with bio_id exists
echo "Test 1: Checking personnel existence\n";
$test_bio_ids = [307, 254, 508, 999]; // 999 should not exist

foreach ($test_bio_ids as $bio_id) {
    $exists = $CI->biometricsModel->checkPersonnelExists($bio_id);
    echo "Bio ID $bio_id: " . ($exists ? "EXISTS" : "NOT FOUND") . "\n";
}

echo "\n";

// Test 2: Parse sample CSV data
echo "Test 2: Testing CSV parsing logic\n";
$sample_csv_data = [
    ['bio_id', 'biometrics_time'], // header
    ['307', '8/1/2025 7:14'],
    ['254', '8/1/2025 7:15'],
    ['307', '8/1/2025 12:00'],
    ['307', '8/1/2025 13:00'],
    ['307', '8/1/2025 17:00']
];

$importRes = [];
for ($i = 1; $i < count($sample_csv_data); $i++) {
    $filedata = $sample_csv_data[$i];
    
    if (!empty($filedata[0]) && !empty($filedata[1]) && trim($filedata[0]) != '' && is_numeric(trim($filedata[0]))) {
        $bio_id = trim($filedata[0]);
        $biometrics_time = trim($filedata[1]);
        
        // Parse the biometrics_time datetime
        $datetime = DateTime::createFromFormat('m/d/Y H:i', $biometrics_time);
        if ($datetime) {
            $log_date = $datetime->format('Y-m-d');
            $log_time = $datetime->format('H:i:s');
            
            $importRes[] = array(
                'bio_id' => $bio_id,
                'date' => $log_date,
                'time' => $log_time,
                'device_code' => ''
            );
            
            echo "Parsed: Bio ID $bio_id, Date: $log_date, Time: $log_time\n";
        }
    }
}

echo "\n";

// Test 3: Group by bio_id and date
echo "Test 3: Grouping entries by bio_id and date\n";
$grouped_by_date = [];

foreach ($importRes as $data) {
    $bio_id = $data['bio_id'];
    $date = $data['date'];
    $time = $data['time'];
    
    $key = $bio_id . '_' . $date;
    if (!isset($grouped_by_date[$key])) {
        $grouped_by_date[$key] = [];
    }
    
    $grouped_by_date[$key][] = array(
        'bio_id' => $bio_id,
        'date' => $date,
        'time' => $time,
        'timestamp' => strtotime($date . ' ' . $time)
    );
}

foreach ($grouped_by_date as $key => $entries) {
    echo "Group $key: " . count($entries) . " entries\n";
    foreach ($entries as $entry) {
        echo "  - Bio ID: {$entry['bio_id']}, Time: {$entry['time']}\n";
    }
}

echo "\n";

// Test 4: Check existing attendance records
echo "Test 4: Checking existing attendance records\n";
$test_date = '2025-08-01';
foreach ([307, 254] as $bio_id) {
    $existing = $CI->biometricsModel->getBio($bio_id, $test_date);
    if ($existing) {
        echo "Bio ID $bio_id on $test_date: FOUND (ID: {$existing->id})\n";
        echo "  AM IN: {$existing->am_in}, AM OUT: {$existing->am_out}\n";
        echo "  PM IN: {$existing->pm_in}, PM OUT: {$existing->pm_out}\n";
    } else {
        echo "Bio ID $bio_id on $test_date: NOT FOUND\n";
    }
}

echo "\n=== Test Complete ===\n";
echo "The modified import functionality should now work with your CSV format.\n";
echo "To test the actual import, use the biometrics import feature in the web interface.\n";
?>
