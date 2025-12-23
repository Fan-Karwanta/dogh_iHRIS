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
        .header .subtitle {
            font-size: 12px;
            color: #28a745;
            font-weight: bold;
            margin-top: 5px;
        }
        .header .date-range {
            font-size: 12px;
            margin-top: 10px;
        }
        .summary-cards {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
            gap: 20px;
        }
        .summary-card {
            text-align: center;
            padding: 15px 30px;
            border: 2px solid #28a745;
            border-radius: 8px;
            background-color: #f8fff8;
        }
        .summary-card .value {
            font-size: 28px;
            font-weight: bold;
            color: #28a745;
        }
        .summary-card .label {
            font-size: 11px;
            color: #666;
            text-transform: uppercase;
        }
        .department-summary {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        .department-summary h3 {
            font-size: 12px;
            margin-bottom: 10px;
            color: #333;
        }
        .department-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .dept-badge {
            display: inline-flex;
            align-items: center;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 10px;
            color: white;
        }
        .dept-badge .count {
            font-weight: bold;
            margin-right: 5px;
            background: rgba(255,255,255,0.3);
            padding: 2px 6px;
            border-radius: 3px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px 10px;
            text-align: left;
        }
        th {
            background-color: #28a745;
            color: white;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
        }
        td {
            font-size: 10px;
        }
        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tbody tr:hover {
            background-color: #e8f5e9;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        .badge-primary {
            background-color: #007bff;
            color: white;
        }
        .rank-badge {
            display: inline-block;
            width: 22px;
            height: 22px;
            line-height: 22px;
            text-align: center;
            border-radius: 50%;
            font-weight: bold;
            font-size: 10px;
        }
        .rank-1 { background-color: #ffd700; color: #333; }
        .rank-2 { background-color: #c0c0c0; color: #333; }
        .rank-3 { background-color: #cd7f32; color: white; }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .footer p {
            margin: 3px 0;
        }
        .trophy-icon {
            color: #ffd700;
        }
        .check-icon {
            color: #28a745;
            font-weight: bold;
        }
        @media print {
            body {
                padding: 10px;
            }
            .no-print {
                display: none !important;
            }
            .header {
                margin-bottom: 15px;
                padding-bottom: 10px;
            }
            th {
                background-color: #28a745 !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            tbody tr:nth-child(even) {
                background-color: #f9f9f9 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .badge-success, .badge-primary {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .rank-1, .rank-2, .rank-3 {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .dept-badge {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 25px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .print-btn:hover {
            background-color: #218838;
        }
        .back-btn {
            position: fixed;
            top: 20px;
            right: 150px;
            padding: 12px 25px;
            background-color: #6c757d;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .back-btn:hover {
            background-color: #5a6268;
            color: white;
        }
        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            width: 30%;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 40px;
            padding-top: 5px;
        }
        .signature-label {
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <a href="javascript:history.back()" class="back-btn no-print">
        ← Back
    </a>
    <button class="print-btn no-print" onclick="window.print()">
        🖨️ Print Report
    </button>

    <div class="header">
        <h1>DAVAO OCCIDENTAL GENERAL HOSPITAL</h1>
        <h2>Schedule Compliance Report</h2>
        <div class="subtitle">✓ COMPLETE SCHEDULE PERSONNEL</div>
        <div class="date-range">
            <strong>Period:</strong> <?= date('F d, Y', strtotime($start_date)) ?> - <?= date('F d, Y', strtotime($end_date)) ?>
            <br>
            <strong>Department:</strong> <?= $department_name ?>
        </div>
    </div>

    <div class="summary-cards">
        <div class="summary-card">
            <div class="value"><?= $total_complete ?></div>
            <div class="label">Complete Schedule Personnel</div>
        </div>
        <div class="summary-card">
            <div class="value"><?= $total_employees ?></div>
            <div class="label">Total Employees</div>
        </div>
        <div class="summary-card">
            <div class="value"><?= $total_employees > 0 ? round(($total_complete / $total_employees) * 100, 1) : 0 ?>%</div>
            <div class="label">Completion Rate</div>
        </div>
    </div>

    <?php if (!empty($department_summary)): ?>
    <div class="department-summary">
        <h3>📊 Department Breakdown:</h3>
        <div class="department-badges">
            <?php foreach ($department_summary as $dept): ?>
                <span class="dept-badge" style="background-color: <?= $dept['color'] ?>;">
                    <span class="count"><?= $dept['count'] ?></span>
                    <?= $dept['name'] ?>
                </span>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if (empty($complete_personnel)): ?>
        <div style="text-align: center; padding: 50px; color: #666;">
            <h3>No Complete Schedule Personnel Found</h3>
            <p>No employees have 100% complete schedules for the selected period and department filter.</p>
        </div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th class="text-center" width="5%">#</th>
                    <th width="25%">Employee Name</th>
                    <th width="15%">Department</th>
                    <th width="20%">Position</th>
                    <th class="text-center" width="10%">Bio ID</th>
                    <th class="text-center" width="10%">Working Days</th>
                    <th class="text-center" width="10%">Complete Days</th>
                    <th class="text-center" width="5%">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $rank = 1; foreach ($complete_personnel as $emp): ?>
                    <tr>
                        <td class="text-center">
                            <?php if ($rank <= 3): ?>
                                <span class="rank-badge rank-<?= $rank ?>"><?= $rank ?></span>
                            <?php else: ?>
                                <?= $rank ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?= $emp['name'] ?></strong>
                        </td>
                        <td><?= $emp['department_name'] ?></td>
                        <td><?= $emp['position'] ?: '-' ?></td>
                        <td class="text-center"><?= $emp['bio_id'] ?></td>
                        <td class="text-center">
                            <span class="badge badge-primary"><?= $emp['working_days'] ?></span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-success"><?= $emp['complete_days'] ?></span>
                        </td>
                        <td class="text-center">
                            <span class="check-icon">✓</span>
                        </td>
                    </tr>
                <?php $rank++; endforeach; ?>
            </tbody>
        </table>

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line">
                    <div class="signature-label">Prepared by</div>
                </div>
            </div>
            <div class="signature-box">
                <div class="signature-line">
                    <div class="signature-label">Reviewed by</div>
                </div>
            </div>
            <div class="signature-box">
                <div class="signature-line">
                    <div class="signature-label">Approved by</div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="footer">
        <p><strong>Generated on:</strong> <?= date('F d, Y h:i A') ?></p>
        <p><strong>Schedule:</strong> 8:00 AM - 5:00 PM (12:00 PM - 1:00 PM Lunch Break)</p>
        <p>A complete schedule requires all 4 clock entries: AM IN, AM OUT, PM IN, PM OUT</p>
        <p>Personnel listed have 100% compliance rate for the specified period.</p>
    </div>
</body>
</html>
