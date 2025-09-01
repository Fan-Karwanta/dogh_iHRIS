<?php
/**
 * Test script for absence handling functionality
 * Tests full-day absence (blank entries) vs half-day absence (4-0 undertime)
 */

// Include CodeIgniter framework for database access
require_once('index.php');

// Simulate the updated calculateUndertime function
function testCalculateUndertime($am_in, $am_out, $pm_in, $pm_out)
{
    // Check for full day absence - all time entries are empty
    $has_am_in = !empty($am_in);
    $has_am_out = !empty($am_out);
    $has_pm_in = !empty($pm_in);
    $has_pm_out = !empty($pm_out);
    
    // If completely absent (no time entries at all), return null to indicate blank entry
    if (!$has_am_in && !$has_am_out && !$has_pm_in && !$has_pm_out) {
        return null; // This will be handled as blank in database and DTR
    }
    
    $undertime_minutes = 0;
    
    // Standard work times in minutes from midnight
    $standard_am_in = 8 * 60;      // 8:00 AM = 480 minutes
    $standard_am_out = 12 * 60;    // 12:00 PM = 720 minutes
    $standard_pm_in = 13 * 60;     // 1:00 PM = 780 minutes
    $standard_pm_out = 17 * 60;    // 5:00 PM = 1020 minutes
    
    // Convert time strings to minutes from midnight
    $actual_am_in = $has_am_in ? timeToMinutes($am_in) : null;
    $actual_am_out = $has_am_out ? timeToMinutes($am_out) : null;
    $actual_pm_in = $has_pm_in ? timeToMinutes($pm_in) : null;
    $actual_pm_out = $has_pm_out ? timeToMinutes($pm_out) : null;
    
    // Check if morning session has any attendance
    $has_morning_session = $has_am_in || $has_am_out;
    // Check if afternoon session has any attendance
    $has_afternoon_session = $has_pm_in || $has_pm_out;
    
    // Calculate morning session undertime
    if ($has_morning_session) {
        if (!empty($actual_am_in) && !empty($actual_am_out)) {
            // Complete morning session - calculate based on actual times
            // Late arrival (after 8:00 AM)
            if ($actual_am_in > $standard_am_in) {
                $undertime_minutes += ($actual_am_in - $standard_am_in);
            }
            
            // Early departure (before 12:00 PM)
            if ($actual_am_out < $standard_am_out) {
                $undertime_minutes += ($standard_am_out - $actual_am_out);
            }
        } else {
            // Incomplete morning session (only in or only out) = 4 hours undertime
            $undertime_minutes += 240; // 4 hours = 240 minutes
        }
    } else {
        // No morning session at all = 4 hours undertime
        $undertime_minutes += 240; // 4 hours = 240 minutes
    }
    
    // Calculate afternoon session undertime
    if ($has_afternoon_session) {
        if (!empty($actual_pm_in) && !empty($actual_pm_out)) {
            // Complete afternoon session - calculate based on actual times
            // Late arrival (after 1:00 PM)
            if ($actual_pm_in > $standard_pm_in) {
                $undertime_minutes += ($actual_pm_in - $standard_pm_in);
            }
            
            // Early departure (before 5:00 PM)
            if ($actual_pm_out < $standard_pm_out) {
                $undertime_minutes += ($standard_pm_out - $actual_pm_out);
            }
        } else {
            // Incomplete afternoon session (only in or only out) = 4 hours undertime
            $undertime_minutes += 240; // 4 hours = 240 minutes
        }
    } else {
        // No afternoon session at all = 4 hours undertime
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

// Test scenarios for absence handling
$test_cases = [
    [
        'name' => 'Perfect Attendance',
        'am_in' => '08:00',
        'am_out' => '12:00',
        'pm_in' => '13:00',
        'pm_out' => '17:00',
        'expected_result' => 'undertime',
        'expected_hours' => 0,
        'expected_minutes' => 0
    ],
    [
        'name' => 'Full Day Absence (All Empty)',
        'am_in' => '',
        'am_out' => '',
        'pm_in' => '',
        'pm_out' => '',
        'expected_result' => 'blank',
        'expected_hours' => null,
        'expected_minutes' => null
    ],
    [
        'name' => 'Half Day Absence - Missing Morning Session',
        'am_in' => '',
        'am_out' => '',
        'pm_in' => '13:00',
        'pm_out' => '17:00',
        'expected_result' => 'undertime',
        'expected_hours' => 4,
        'expected_minutes' => 0
    ],
    [
        'name' => 'Half Day Absence - Missing Afternoon Session',
        'am_in' => '08:00',
        'am_out' => '12:00',
        'pm_in' => '',
        'pm_out' => '',
        'expected_result' => 'undertime',
        'expected_hours' => 4,
        'expected_minutes' => 0
    ],
    [
        'name' => 'Partial Morning Session (Only AM In)',
        'am_in' => '08:00',
        'am_out' => '',
        'pm_in' => '13:00',
        'pm_out' => '17:00',
        'expected_result' => 'undertime',
        'expected_hours' => 4,
        'expected_minutes' => 0
    ],
    [
        'name' => 'Partial Afternoon Session (Only PM Out)',
        'am_in' => '08:00',
        'am_out' => '12:00',
        'pm_in' => '',
        'pm_out' => '17:00',
        'expected_result' => 'undertime',
        'expected_hours' => 4,
        'expected_minutes' => 0
    ],
    [
        'name' => 'Late Morning + Early Afternoon',
        'am_in' => '08:30',
        'am_out' => '12:00',
        'pm_in' => '13:00',
        'pm_out' => '16:30',
        'expected_result' => 'undertime',
        'expected_hours' => 1,
        'expected_minutes' => 0
    ]
];

echo "<h1>Absence Handling Test Results</h1>\n";
echo "<p><strong>NEW RULES:</strong></p>\n";
echo "<ul>\n";
echo "<li><strong>Full Day Absence:</strong> All time entries empty → Database stores NULL, DTR shows blank</li>\n";
echo "<li><strong>Half Day Absence:</strong> Missing one session → Calculate undertime normally (4-0 hours)</li>\n";
echo "</ul>\n";

echo "<table border='1' cellpadding='5' cellspacing='0' style='border-collapse: collapse;'>\n";
echo "<tr style='background-color: #f0f0f0;'><th>Test Case</th><th>AM In</th><th>AM Out</th><th>PM In</th><th>PM Out</th><th>Expected Result</th><th>Actual Result</th><th>Status</th></tr>\n";

$passed = 0;
$total = count($test_cases);

foreach ($test_cases as $test) {
    $result = testCalculateUndertime($test['am_in'], $test['am_out'], $test['pm_in'], $test['pm_out']);
    
    if ($test['expected_result'] === 'blank') {
        $expected = 'BLANK (NULL)';
        $actual = ($result === null) ? 'BLANK (NULL)' : $result['hours'] . 'h ' . $result['minutes'] . 'm';
        $status = ($result === null) ? '<span style="color: green; font-weight: bold;">✓ PASS</span>' : '<span style="color: red; font-weight: bold;">✗ FAIL</span>';
        
        if ($result === null) {
            $passed++;
        }
    } else {
        $expected = $test['expected_hours'] . 'h ' . $test['expected_minutes'] . 'm';
        $actual = $result['hours'] . 'h ' . $result['minutes'] . 'm';
        $status = ($result['hours'] == $test['expected_hours'] && $result['minutes'] == $test['expected_minutes']) ? 
                  '<span style="color: green; font-weight: bold;">✓ PASS</span>' : '<span style="color: red; font-weight: bold;">✗ FAIL</span>';
        
        if ($result['hours'] == $test['expected_hours'] && $result['minutes'] == $test['expected_minutes']) {
            $passed++;
        }
    }
    
    echo "<tr>";
    echo "<td><strong>{$test['name']}</strong></td>";
    echo "<td>" . ($test['am_in'] ?: '<em>Empty</em>') . "</td>";
    echo "<td>" . ($test['am_out'] ?: '<em>Empty</em>') . "</td>";
    echo "<td>" . ($test['pm_in'] ?: '<em>Empty</em>') . "</td>";
    echo "<td>" . ($test['pm_out'] ?: '<em>Empty</em>') . "</td>";
    echo "<td>$expected</td>";
    echo "<td>$actual</td>";
    echo "<td>$status</td>";
    echo "</tr>\n";
}

echo "</table>\n";
echo "<h2>Summary: $passed/$total tests passed</h2>\n";

if ($passed == $total) {
    echo "<div style='color: green; font-weight: bold; padding: 10px; background-color: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px;'>\n";
    echo "✅ All tests passed! The absence handling system is working correctly.<br>\n";
    echo "• Full-day absences will show as blank entries in DTR<br>\n";
    echo "• Half-day absences will calculate undertime properly (4-0 hours)\n";
    echo "</div>\n";
} else {
    echo "<div style='color: red; font-weight: bold; padding: 10px; background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px;'>\n";
    echo "❌ Some tests failed. Please review the implementation.\n";
    echo "</div>\n";
}

echo "<h3>Database Impact:</h3>\n";
echo "<ul>\n";
echo "<li><strong>Full Day Absence:</strong> undertime_hours = NULL, undertime_minutes = NULL</li>\n";
echo "<li><strong>Half Day Absence:</strong> undertime_hours = 4, undertime_minutes = 0</li>\n";
echo "<li><strong>Partial Attendance:</strong> Calculated undertime values</li>\n";
echo "</ul>\n";

echo "<h3>DTR Print Impact:</h3>\n";
echo "<ul>\n";
echo "<li><strong>Full Day Absence:</strong> All cells (AM In, AM Out, PM In, PM Out, Hours, Minutes) show blank</li>\n";
echo "<li><strong>Half Day Absence:</strong> Missing session shows blank, present session shows times, undertime shows 4-0</li>\n";
echo "</ul>\n";
?>
