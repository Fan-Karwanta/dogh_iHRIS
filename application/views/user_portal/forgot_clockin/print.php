<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Failure to Clock and Time Changes Form - <?= htmlspecialchars($request->control_no) ?></title>
    <style>
        @page {
            size: letter;
            margin: 15mm 15mm 10mm 15mm;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            background: #fff;
            font-size: 12px;
        }
        .print-page {
            width: 100%;
            max-width: 650px;
            margin: 0 auto;
            padding: 10px 0;
        }
        /* Header */
        .form-header {
            text-align: center;
            position: relative;
            padding: 0 80px;
            margin-bottom: 10px;
        }
        .form-header .logo-left {
            position: absolute;
            left: 0;
            top: 0;
            width: 65px;
            height: 65px;
            object-fit: contain;
        }
        .form-header .logo-right {
            position: absolute;
            right: 0;
            top: 0;
            width: 65px;
            height: 65px;
            object-fit: contain;
        }
        .form-header .line1 {
            font-size: 12px;
            font-style: italic;
        }
        .form-header .line2 {
            font-size: 12px;
            font-style: italic;
        }
        .form-header .line3 {
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 2px;
        }
        .form-header .line4 {
            font-size: 11px;
            font-style: italic;
        }
        /* Title */
        .form-title {
            text-align: center;
            font-size: 15px;
            font-weight: bold;
            margin: 18px 0 12px;
        }
        /* Control No */
        .control-row {
            font-size: 12px;
            margin-bottom: 8px;
        }
        .control-row .label {
            font-weight: normal;
        }
        .control-row .value {
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 100px;
            padding: 0 5px;
            font-weight: bold;
        }
        /* Employee Info Row */
        .emp-info {
            display: flex;
            align-items: baseline;
            font-size: 12px;
            margin-bottom: 10px;
            flex-wrap: wrap;
        }
        .emp-info .label {
            font-weight: bold;
            white-space: nowrap;
            margin-right: 3px;
        }
        .emp-info .value {
            border-bottom: 1px solid #000;
            min-height: 16px;
            padding: 0 5px;
            font-weight: normal;
        }
        .emp-info .value-id { min-width: 60px; }
        .emp-info .value-name { flex: 1; min-width: 180px; }
        .emp-info .value-designation { min-width: 120px; }
        .emp-info .spacer { width: 15px; }
        /* Table */
        .form-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 11px;
        }
        .form-table th,
        .form-table td {
            border: 1px solid #000;
            padding: 4px 3px;
            text-align: center;
            vertical-align: middle;
        }
        .form-table th {
            font-weight: bold;
            font-size: 11px;
        }
        .form-table .cb {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 1px solid #000;
            text-align: center;
            line-height: 12px;
            font-size: 10px;
            font-weight: bold;
            vertical-align: middle;
        }
        .form-table .date-cell {
            text-align: left;
            padding-left: 5px;
            border-bottom: 1px solid #000;
        }
        .form-table .reason-cell {
            text-align: left;
            padding-left: 5px;
        }
        /* Empty rows for unfilled lines */
        .form-table .empty-line td {
            height: 22px;
        }
        .form-table .empty-line .date-cell {
            border-bottom: 1px solid #000;
        }
        /* Signature */
        .signature-area {
            margin-top: 25px;
            text-align: right;
        }
        .sig-line {
            display: inline-block;
            width: 220px;
            border-top: 1px solid #000;
            text-align: center;
            padding-top: 2px;
            font-size: 12px;
        }
        .sig-label {
            display: inline-block;
            width: 220px;
            text-align: center;
            font-style: italic;
            font-size: 11px;
            margin-top: 2px;
        }
        /* Duplicate copy separator */
        .copy-separator {
            border-top: 1px dashed #999;
            margin: 25px 0 15px;
            page-break-before: auto;
        }
        @media print {
            body { background: #fff; }
            .no-print { display: none !important; }
            .print-page { max-width: 100%; }
            .copy-separator { border-top: 1px dashed #000; }
        }
        @media screen {
            body { background: #e0e0e0; padding: 20px; }
            .print-page { background: #fff; padding: 30px 40px; box-shadow: 0 2px 10px rgba(0,0,0,0.15); margin-bottom: 20px; }
        }
        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 100;
            padding: 10px 25px;
            background: #31ce36;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        .print-btn:hover { background: #1b8e20; }
    </style>
</head>
<body>
    <button class="print-btn no-print" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" class="no-print">

    <?php
    // Determine supervisor name
    $supervisor_name = '';
    if (isset($approver) && $approver) {
        $supervisor_name = strtoupper($approver->lastname . ', ' . $approver->firstname);
    } elseif (isset($supervisor) && $supervisor) {
        $supervisor_name = strtoupper($supervisor->lastname . ', ' . $supervisor->firstname);
    }

    $employee_name = strtoupper($personnel->lastname . ', ' . $personnel->firstname . ' ' . ($personnel->middlename ? substr($personnel->middlename, 0, 1) . '.' : ''));

    // We print 2 copies on one page (matching the image)
    $min_rows = 7;
    $total_items = count($items);
    $empty_rows = max(0, $min_rows - $total_items);

    for ($copy = 1; $copy <= 2; $copy++):
    ?>

    <?php if ($copy == 2): ?>
    <div class="copy-separator"></div>
    <?php endif; ?>

    <div class="print-page">
        <!-- Header -->
        <div class="form-header">
            <img src="<?= base_url('assets/img/doh_logo1.png') ?>" class="logo-left" alt="DOH">
            <img src="<?= base_url('assets/img/dogh_logo.png') ?>" class="logo-right" alt="DOGH">
            <div class="line1">Republic of the Philippines</div>
            <div class="line2">Department of Health</div>
            <div class="line3">DAVAO OCCIDENTAL GENERAL HOSPITAL</div>
            <div class="line4">Lacaron, Malita, Davao Occidental</div>
        </div>

        <!-- Title -->
        <div class="form-title">Failure to Clock and Time Changes Form</div>

        <!-- Control No -->
        <div class="control-row">
            <span class="label">Control No.:</span>
            <span class="value"><?= htmlspecialchars($request->control_no) ?></span>
        </div>

        <!-- Employee Info -->
        <div class="emp-info">
            <span class="label">Employee ID:</span>
            <span class="value value-id"><?= htmlspecialchars($personnel->bio_id) ?></span>
            <span class="spacer"></span>
            <span class="label">Name:</span>
            <span class="value value-name"><?= htmlspecialchars($employee_name) ?></span>
            <span class="spacer"></span>
            <span class="label">Designation:</span>
            <span class="value value-designation"><?= htmlspecialchars($personnel->position) ?></span>
        </div>

        <!-- Table -->
        <table class="form-table">
            <thead>
                <tr>
                    <th style="width: 100px;">Date</th>
                    <th style="width: 50px;">AM/PM</th>
                    <th colspan="2">Time</th>
                    <th style="width: 80px;">Change</th>
                    <th>Reason</th>
                </tr>
                <tr>
                    <th></th>
                    <th></th>
                    <th style="width: 30px;">IN</th>
                    <th style="width: 30px;">OUT</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td class="date-cell"><?= date('m/d/Y', strtotime($item->date)) ?></td>
                    <td><?= $item->am_pm ?></td>
                    <td><span class="cb"><?= $item->time_in ? '&#10003;' : '' ?></span></td>
                    <td><span class="cb"><?= $item->time_out ? '&#10003;' : '' ?></span></td>
                    <td><?= htmlspecialchars($item->time_change ?: '') ?></td>
                    <td class="reason-cell"><?= htmlspecialchars($item->reason ?: '') ?></td>
                </tr>
                <?php endforeach; ?>
                <?php for ($e = 0; $e < $empty_rows; $e++): ?>
                <tr class="empty-line">
                    <td class="date-cell">&nbsp;</td>
                    <td>&nbsp;</td>
                    <td><span class="cb"></span></td>
                    <td><span class="cb"></span></td>
                    <td>&nbsp;</td>
                    <td class="reason-cell">&nbsp;</td>
                </tr>
                <?php endfor; ?>
            </tbody>
        </table>

        <!-- Signature -->
        <div class="signature-area">
            <div class="sig-line">
                <?php if ($request->status == 'approved' && !empty($supervisor_name)): ?>
                    <?= htmlspecialchars($supervisor_name) ?>
                <?php else: ?>
                    &nbsp;
                <?php endif; ?>
            </div>
            <br>
            <div style="text-align: right;">
                <span class="sig-label">Immediate Supervisor</span>
            </div>
        </div>
    </div>

    <?php endfor; ?>

</body>
</html>
