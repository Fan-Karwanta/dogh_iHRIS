<?php
/**
 * Test script for undertime calculation functionality
 * This script tests the undertime calculation logic with various scenarios
 */

// Simulate the undertime calculation function
function calculateUndertime($am_in, $am_out, $pm_in, $pm_out)
{
    $undertime_minutes = 0;
    
    // Standard work times in minutes from midnight
    $standard_am_in = 8 * 60;      // 8:00 AM = 480 minutes
    $standard_am_out = 12 * 60;    // 12:00 PM = 720 minutes
    $standard_pm_in = 13 * 60;     // 1:00 PM = 780 minutes
    $standard_pm_out = 17 * 60;    // 5:00 PM = 1020 minutes
    
    // Convert time strings to minutes from midnight
    $actual_am_in = !empty($am_in) ? timeToMinutes($am_in) : null;
    $actual_am_out = !empty($am_out) ? timeToMinutes($am_out) : null;
    $actual_pm_in = !empty($pm_in) ? timeToMinutes($pm_in) : null;
    $actual_pm_out = !empty($pm_out) ? timeToMinutes($pm_out) : null;
    
    // Calculate morning session undertime
    if (!empty($actual_am_in) && !empty($actual_am_out)) {
        // Late arrival (after 8:00 AM)
        if ($actual_am_in > $standard_am_in) {
            $undertime_minutes += ($actual_am_in - $standard_am_in);
        }
        
        // Early departure (before 12:00 PM)
        if ($actual_am_out < $standard_am_out) {
            $undertime_minutes += ($standard_am_out - $actual_am_out);
        }
    } else {
        // Missing morning session = 4 hours undertime
        $undertime_minutes += 240; // 4 hours = 240 minutes
    }
    
    // Calculate afternoon session undertime
    if (!empty($actual_pm_in) && !empty($actual_pm_out)) {
        // Late arrival (after 1:00 PM)
        if ($actual_pm_in > $standard_pm_in) {
            $undertime_minutes += ($actual_pm_in - $standard_pm_in);
        }
        
        // Early departure (before 5:00 PM)
        if ($actual_pm_out < $standard_pm_out) {
            $undertime_minutes += ($standard_pm_out - $actual_pm_out);
        }
    } else {
        // Missing afternoon session = 4 hours undertime
        $undertime_minutes += 240; // 4 hours = 240 minutes
    }
    
    // Convert total undertime minutes to hours and minutes
    $undertime_hours = intval($undertime_minutes / 60);
    $remaining_minutes = $undertime_minutes % 60;
    
    return array(
        'hours' => $undertime_hours,
        'minutes' => $remaining_minutes,
        'total_minutes' => $undertime_minutes
    );
}

function timeToMinutes($time_string)
{
    $time_parts = explode(':', $time_string);
    $hours = intval($time_parts[0]);
    $minutes = intval($time_parts[1]);
    
    return ($hours * 60) + $minutes;
}

// Test scenarios
$test_cases = [
    [
        'name' => 'Perfect Attendance',
        'am_in' => '08:00',
        'am_out' => '12:00',
        'pm_in' => '13:00',
        'pm_out' => '17:00',
        'expected_hours' => 0,
        'expected_minutes' => 0
    ],
    [
        'name' => 'Late Morning Arrival (30 minutes)',
        'am_in' => '08:30',
        'am_out' => '12:00',
        'pm_in' => '13:00',
        'pm_out' => '17:00',
        'expected_hours' => 0,
        'expected_minutes' => 30
    ],
    [
        'name' => 'Early Morning Departure (30 minutes)',
        'am_in' => '08:00',
        'am_out' => '11:30',
        'pm_in' => '13:00',
        'pm_out' => '17:00',
        'expected_hours' => 0,
        'expected_minutes' => 30
    ],
    [
        'name' => 'Late Afternoon Arrival (30 minutes)',
        'am_in' => '08:00',
        'am_out' => '12:00',
        'pm_in' => '13:30',
        'pm_out' => '17:00',
        'expected_hours' => 0,
        'expected_minutes' => 30
    ],
    [
        'name' => 'Early Afternoon Departure (1 hour)',
        'am_in' => '08:00',
        'am_out' => '12:00',
        'pm_in' => '13:00',
        'pm_out' => '16:00',
        'expected_hours' => 1,
        'expected_minutes' => 0
    ],
    [
        'name' => 'Missing Morning Session',
        'am_in' => '',
        'am_out' => '',
        'pm_in' => '13:00',
        'pm_out' => '17:00',
        'expected_hours' => 4,
        'expected_minutes' => 0
    ],
    [
        'name' => 'Missing Afternoon Session',
        'am_in' => '08:00',
        'am_out' => '12:00',
        'pm_in' => '',
        'pm_out' => '',
        'expected_hours' => 4,
        'expected_minutes' => 0
    ],
    [
        'name' => 'Completely Absent',
        'am_in' => '',
        'am_out' => '',
        'pm_in' => '',
        'pm_out' => '',
        'expected_hours' => 8,
        'expected_minutes' => 0
    ],
    [
        'name' => 'Multiple Issues (Late + Early)',
        'am_in' => '08:15',
        'am_out' => '11:45',
        'pm_in' => '13:15',
        'pm_out' => '16:30',
        'expected_hours' => 1,
        'expected_minutes' => 15
    ]
];

echo "<h1>Undertime Calculation Test Results</h1>\n";
echo "<table border='1' cellpadding='5' cellspacing='0'>\n";
echo "<tr><th>Test Case</th><th>AM In</th><th>AM Out</th><th>PM In</th><th>PM Out</th><th>Expected</th><th>Actual</th><th>Status</th></tr>\n";

$passed = 0;
$total = count($test_cases);

foreach ($test_cases as $test) {
    $result = calculateUndertime($test['am_in'], $test['am_out'], $test['pm_in'], $test['pm_out']);
    
    $expected = $test['expected_hours'] . 'h ' . $test['expected_minutes'] . 'm';
    $actual = $result['hours'] . 'h ' . $result['minutes'] . 'm';
    
    $status = ($result['hours'] == $test['expected_hours'] && $result['minutes'] == $test['expected_minutes']) ? 
              '<span style="color: green;">PASS</span>' : '<span style="color: red;">FAIL</span>';
    
    if ($result['hours'] == $test['expected_hours'] && $result['minutes'] == $test['expected_minutes']) {
        $passed++;
    }
    
    echo "<tr>";
    echo "<td>{$test['name']}</td>";
    echo "<td>" . ($test['am_in'] ?: 'N/A') . "</td>";
    echo "<td>" . ($test['am_out'] ?: 'N/A') . "</td>";
    echo "<td>" . ($test['pm_in'] ?: 'N/A') . "</td>";
    echo "<td>" . ($test['pm_out'] ?: 'N/A') . "</td>";
    echo "<td>$expected</td>";
    echo "<td>$actual</td>";
    echo "<td>$status</td>";
    echo "</tr>\n";
}

echo "</table>\n";
echo "<h2>Summary: $passed/$total tests passed</h2>\n";

if ($passed == $total) {
    echo "<p style='color: green; font-weight: bold;'>✅ All tests passed! The undertime calculation system is working correctly.</p>\n";
} else {
    echo "<p style='color: red; font-weight: bold;'>❌ Some tests failed. Please review the calculation logic.</p>\n";
}
?>
