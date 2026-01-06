<?php
// Debug script to identify dashboard loading issues
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('max_execution_time', 30);

echo "<pre>";
echo "=== Dashboard Debug ===\n\n";

// Database config directly
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'snhs';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
echo "✓ Database connection OK\n";

// Test each table
$tables = ['personnels', 'attendance', 'biometrics', 'audit_trail', 'holidays', 'systems', 'users'];
foreach ($tables as $table) {
    $result = $conn->query("SELECT COUNT(*) as cnt FROM $table");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "✓ Table '$table' exists - {$row['cnt']} rows\n";
    } else {
        echo "✗ Table '$table' error: " . $conn->error . "\n";
    }
}

echo "\n=== Testing Dashboard Queries ===\n\n";

// Test the queries that dashboard uses
$today = date('Y-m-d');
$month_start = date('Y-m-01');
$month_end = date('Y-m-t');

echo "Testing personnel count...\n";
$start = microtime(true);
$result = $conn->query("SELECT COUNT(*) as cnt FROM personnels WHERE status = 1");
echo "  Time: " . round((microtime(true) - $start) * 1000, 2) . "ms\n";

echo "Testing today attendance...\n";
$start = microtime(true);
$result = $conn->query("SELECT COUNT(*) as cnt FROM attendance WHERE date = '$today'");
echo "  Time: " . round((microtime(true) - $start) * 1000, 2) . "ms\n";

echo "Testing today biometrics...\n";
$start = microtime(true);
$result = $conn->query("SELECT COUNT(*) as cnt FROM biometrics WHERE date = '$today'");
echo "  Time: " . round((microtime(true) - $start) * 1000, 2) . "ms\n";

echo "Testing audit_trail count...\n";
$start = microtime(true);
$edit_start_date = date('Y-m-01');
$edit_end_date = date('Y-m-t');
$result = $conn->query("
    SELECT COUNT(*) as total_edits 
    FROM audit_trail 
    WHERE table_name = 'biometrics' 
    AND action_type = 'UPDATE'
    AND created_at >= '$edit_start_date 00:00:00'
    AND created_at <= '$edit_end_date 23:59:59'
");
echo "  Time: " . round((microtime(true) - $start) * 1000, 2) . "ms\n";

echo "Testing missing logs query (this might be slow)...\n";
$start = microtime(true);
$dtr_start_date = date('Y-m-01', strtotime('-1 month'));
$dtr_end_date = date('Y-m-t', strtotime('-1 month'));
$result = $conn->query("
    SELECT 
        SUM(CASE WHEN b.am_in IS NULL OR b.am_in = '' THEN 1 ELSE 0 END) +
        SUM(CASE WHEN b.am_out IS NULL OR b.am_out = '' THEN 1 ELSE 0 END) +
        SUM(CASE WHEN b.pm_in IS NULL OR b.pm_in = '' THEN 1 ELSE 0 END) +
        SUM(CASE WHEN b.pm_out IS NULL OR b.pm_out = '' THEN 1 ELSE 0 END) as missing_count
    FROM biometrics b
    INNER JOIN personnels p ON p.bio_id = b.bio_id
    WHERE b.date >= '$dtr_start_date'
    AND b.date <= '$dtr_end_date'
    AND DAYOFWEEK(b.date) NOT IN (1, 7)
    AND b.date NOT IN (SELECT date FROM holidays WHERE status = 1 AND date BETWEEN '$dtr_start_date' AND '$dtr_end_date')
    AND (b.am_in IS NOT NULL OR b.am_out IS NOT NULL OR b.pm_in IS NOT NULL OR b.pm_out IS NOT NULL)
");
$time = round((microtime(true) - $start) * 1000, 2);
echo "  Time: {$time}ms\n";
if ($time > 5000) {
    echo "  ⚠ WARNING: This query is very slow!\n";
}

echo "Testing top attendees query...\n";
$start = microtime(true);
$result = $conn->query("
    SELECT p.firstname, p.lastname, COUNT(*) as total_days
    FROM attendance a
    INNER JOIN personnels p ON p.email = a.email
    WHERE a.date BETWEEN '$month_start' AND '$month_end'
    GROUP BY a.email
    ORDER BY total_days DESC
    LIMIT 5
");
echo "  Time: " . round((microtime(true) - $start) * 1000, 2) . "ms\n";

echo "Testing recent activity query...\n";
$start = microtime(true);
$result = $conn->query("
    SELECT a.date, p.firstname, p.lastname, a.morning_in, a.afternoon_out
    FROM attendance a
    INNER JOIN personnels p ON p.email = a.email
    ORDER BY a.date DESC, a.id DESC
    LIMIT 5
");
echo "  Time: " . round((microtime(true) - $start) * 1000, 2) . "ms\n";

echo "\n=== All tests completed ===\n";
echo "</pre>";

$conn->close();
