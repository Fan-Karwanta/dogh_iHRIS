<?php
/**
 * Personnel Import Test Script
 * This script tests the enhanced personnel management system
 */

// Include CodeIgniter bootstrap
require_once 'index.php';

// Get CI instance
$CI =& get_instance();
$CI->load->model('PersonnelModel', 'personnelModel');

echo "<h1>Personnel Management System Test</h1>\n";

// Test 1: Check if new database columns exist
echo "<h2>Test 1: Database Schema Verification</h2>\n";
$query = $CI->db->query("DESCRIBE personnels");
$columns = $query->result_array();

$expected_columns = ['timestamp', 'employment_type', 'salary_grade', 'schedule_type', 'created_at', 'updated_at'];
$existing_columns = array_column($columns, 'Field');

echo "<h3>Existing Columns:</h3>\n";
echo "<ul>\n";
foreach ($existing_columns as $col) {
    echo "<li>$col</li>\n";
}
echo "</ul>\n";

echo "<h3>New Columns Status:</h3>\n";
echo "<ul>\n";
foreach ($expected_columns as $col) {
    $status = in_array($col, $existing_columns) ? '✅ EXISTS' : '❌ MISSING';
    echo "<li>$col: $status</li>\n";
}
echo "</ul>\n";

// Test 2: Test personnel statistics
echo "<h2>Test 2: Personnel Statistics</h2>\n";
try {
    $stats = $CI->personnelModel->get_personnel_statistics();
    echo "<ul>\n";
    echo "<li>Total Personnel: " . $stats->total . "</li>\n";
    echo "<li>Active Personnel: " . $stats->active . "</li>\n";
    echo "<li>Regular Employees: " . $stats->regular . "</li>\n";
    echo "<li>Contract Personnel: " . $stats->contract . "</li>\n";
    echo "</ul>\n";
} catch (Exception $e) {
    echo "<p style='color: red;'>Error getting statistics: " . $e->getMessage() . "</p>\n";
}

// Test 3: Test batch insert functionality
echo "<h2>Test 3: Batch Insert Test</h2>\n";
$test_data = [
    [
        'firstname' => 'Test',
        'lastname' => 'User1',
        'middlename' => 'Sample',
        'email' => 'test1@example.com',
        'bio_id' => 9999,
        'position' => 'Test Position',
        'employment_type' => 'Regular',
        'salary_grade' => 15,
        'schedule_type' => '8:00 AM - 5:00 PM',
        'status' => 1
    ],
    [
        'firstname' => 'Test',
        'lastname' => 'User2',
        'middlename' => 'Sample',
        'email' => 'test2@example.com',
        'bio_id' => 9998,
        'position' => 'Test Position 2',
        'employment_type' => 'Contract of Service',
        'salary_grade' => 12,
        'schedule_type' => '9:00 AM - 6:00 PM',
        'status' => 1
    ]
];

try {
    $result = $CI->personnelModel->create_personnel_batch($test_data);
    echo "<p style='color: green;'>✅ Batch insert successful. Inserted $result records.</p>\n";
    
    // Clean up test data
    $CI->db->where_in('email', ['test1@example.com', 'test2@example.com']);
    $CI->db->delete('personnels');
    echo "<p>🧹 Test data cleaned up.</p>\n";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Batch insert failed: " . $e->getMessage() . "</p>\n";
}

// Test 4: CSV Format Validation
echo "<h2>Test 4: CSV Format Validation</h2>\n";
$csv_file_path = 'csv_files/Employee Enrollment for Automated Monthly DTR Generation (Responses) - ADMIN.csv';

if (file_exists($csv_file_path)) {
    $handle = fopen($csv_file_path, 'r');
    $header = fgetcsv($handle);
    fclose($handle);
    
    $expected_headers = [
        'Timestamp', 'Biometrics ID', 'Employee ID', 'Last Name ', 
        'First Name', 'Middle Name', 'Type of Employment', 'Position', 
        'Salary Grade', 'Email Address', 'Type of Schedule'
    ];
    
    echo "<h3>CSV Headers:</h3>\n";
    echo "<ul>\n";
    foreach ($header as $i => $col) {
        $expected = isset($expected_headers[$i]) ? $expected_headers[$i] : 'UNEXPECTED';
        $match = trim($col) === trim($expected) ? '✅' : '❌';
        echo "<li>Column $i: '$col' (Expected: '$expected') $match</li>\n";
    }
    echo "</ul>\n";
} else {
    echo "<p style='color: red;'>❌ CSV file not found at: $csv_file_path</p>\n";
}

echo "<h2>Test Summary</h2>\n";
echo "<p>All tests completed. Please review the results above.</p>\n";
echo "<p><strong>Next Steps:</strong></p>\n";
echo "<ol>\n";
echo "<li>Run the database migration: <code>db/personnel_enhancement_migration.sql</code></li>\n";
echo "<li>Test the CSV import functionality through the web interface</li>\n";
echo "<li>Verify the enhanced personnel management page displays correctly</li>\n";
echo "</ol>\n";
?>
