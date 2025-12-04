<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .header h1 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        .header h2 {
            font-size: 14px;
            font-weight: normal;
            color: #666;
        }
        .header .date-range {
            font-size: 12px;
            margin-top: 10px;
        }
        .summary-cards {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .summary-card {
            flex: 1;
            text-align: center;
            padding: 10px;
            border: 1px solid #ddd;
            margin: 0 5px;
        }
        .summary-card:first-child {
            margin-left: 0;
        }
        .summary-card:last-child {
            margin-right: 0;
        }
        .summary-card .value {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        .summary-card .label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
        }
        td {
            font-size: 10px;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        .badge-warning {
            background-color: #ffc107;
            color: #333;
        }
        .badge-danger {
            background-color: #dc3545;
            color: white;
        }
        .badge-info {
            background-color: #17a2b8;
            color: white;
        }
        .progress-bar {
            height: 12px;
            background-color: #e9ecef;
            border-radius: 6px;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            text-align: center;
            font-size: 8px;
            color: white;
            line-height: 12px;
        }
        .bg-success { background-color: #28a745; }
        .bg-info { background-color: #17a2b8; }
        .bg-warning { background-color: #ffc107; }
        .bg-danger { background-color: #dc3545; }
        .highlight-danger {
            background-color: #f8d7da;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .rank-badge {
            display: inline-block;
            width: 20px;
            height: 20px;
            line-height: 20px;
            text-align: center;
            border-radius: 50%;
            font-weight: bold;
            font-size: 10px;
        }
        .rank-1 { background-color: #ffd700; color: #333; }
        .rank-2 { background-color: #c0c0c0; color: #333; }
        .rank-3 { background-color: #cd7f32; color: white; }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .print-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <button class="print-btn no-print" onclick="window.print()">
        Print Report
    </button>

    <div class="header">
        <h1>DAVAO OCCIDENTAL GENERAL HOSPITAL</h1>
        <h2>Schedule Compliance Report</h2>
        <div class="date-range">
            <strong>Period:</strong> <?= date('F d, Y', strtotime($start_date)) ?> - <?= date('F d, Y', strtotime($end_date)) ?>
            <br>
            <strong>Department:</strong> <?= $department_name ?>
        </div>
    </div>

    <div class="summary-cards">
        <div class="summary-card">
            <div class="value"><?= $overall_stats['perfect_employees'] ?></div>
            <div class="label">Perfect Attendance</div>
        </div>
        <div class="summary-card">
            <div class="value"><?= $overall_stats['total_employees'] ?></div>
            <div class="label">Total Employees</div>
        </div>
        <div class="summary-card">
            <div class="value"><?= $overall_stats['average_compliance'] ?>%</div>
            <div class="label">Average Compliance</div>
        </div>
        <div class="summary-card">
            <div class="value"><?= number_format($overall_stats['total_missing_entries']) ?></div>
            <div class="label">Total Missing Entries</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" width="5%">Rank</th>
                <th width="22%">Employee Name</th>
                <th width="12%">Department</th>
                <th class="text-center" width="8%">Working Days*</th>
                <th class="text-center" width="8%">Complete Days</th>
                <th class="text-center" width="12%">Compliance Rate</th>
                <th class="text-center" width="7%">AM IN</th>
                <th class="text-center" width="7%">AM OUT</th>
                <th class="text-center" width="7%">PM IN</th>
                <th class="text-center" width="7%">PM OUT</th>
                <th class="text-center" width="5%">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php $rank = 1; foreach ($compliance_data as $emp): ?>
                <?php
                    $compliance_class = 'danger';
                    if ($emp['compliance_rate'] == 100) {
                        $compliance_class = 'success';
                    } elseif ($emp['compliance_rate'] >= 80) {
                        $compliance_class = 'info';
                    } elseif ($emp['compliance_rate'] >= 50) {
                        $compliance_class = 'warning';
                    }
                ?>
                <tr>
                    <td class="text-center">
                        <?php if ($rank <= 3 && $emp['compliance_rate'] == 100): ?>
                            <span class="rank-badge rank-<?= $rank ?>"><?= $rank ?></span>
                        <?php else: ?>
                            <?= $rank ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <strong><?= $emp['name'] ?></strong>
                        <br><small><?= $emp['position'] ?: '-' ?></small>
                    </td>
                    <td><?= $emp['department_name'] ?></td>
                    <td class="text-center">
                        <?= $emp['working_days'] ?>
                        <?php if (isset($emp['calendar_working_days']) && $emp['calendar_working_days'] != $emp['working_days']): ?>
                        <br><small>(<?= $emp['calendar_working_days'] ?> cal)</small>
                        <?php endif; ?>
                    </td>
                    <td class="text-center"><?= $emp['complete_days'] ?></td>
                    <td>
                        <div class="progress-bar">
                            <div class="progress-fill bg-<?= $compliance_class ?>" style="width: <?= $emp['compliance_rate'] ?>%;">
                                <?= $emp['compliance_rate'] ?>%
                            </div>
                        </div>
                    </td>
                    <td class="text-center <?= $emp['missing_am_in'] > 0 ? 'highlight-danger' : '' ?>">
                        <?= $emp['missing_am_in'] ?>
                    </td>
                    <td class="text-center <?= $emp['missing_am_out'] > 0 ? 'highlight-danger' : '' ?>">
                        <?= $emp['missing_am_out'] ?>
                    </td>
                    <td class="text-center <?= $emp['missing_pm_in'] > 0 ? 'highlight-danger' : '' ?>">
                        <?= $emp['missing_pm_in'] ?>
                    </td>
                    <td class="text-center <?= $emp['missing_pm_out'] > 0 ? 'highlight-danger' : '' ?>">
                        <?= $emp['missing_pm_out'] ?>
                    </td>
                    <td class="text-center">
                        <strong><?= $emp['total_missing_entries'] ?></strong>
                    </td>
                </tr>
            <?php $rank++; endforeach; ?>
        </tbody>
    </table>

    <div class="footer">
        <p>Generated on <?= date('F d, Y h:i A') ?></p>
        <p>Schedule: 8:00 AM - 5:00 PM (12:00 PM - 1:00 PM Lunch Break)</p>
        <p>A complete schedule requires all 4 clock entries: AM IN, AM OUT, PM IN, PM OUT</p>
        <p><strong>*Working Days:</strong> Calculated based on actual attendance (days with at least one clock-in entry). "cal" = calendar working days.</p>
    </div>
</body>
</html>
