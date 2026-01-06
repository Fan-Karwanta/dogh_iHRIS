<!-- Filter -->
<div class="card-custom mb-4">
    <div class="card-body py-3">
        <form method="GET" class="form-inline flex-wrap">
            <label class="mr-2 mb-2">Select Period:</label>
            <select name="month" class="form-control form-control-sm mr-2 mb-2" style="width: auto;">
                <?php for ($m = 1; $m <= 12; $m++): ?>
                    <option value="<?= str_pad($m, 2, '0', STR_PAD_LEFT) ?>" <?= $selected_month == $m ? 'selected' : '' ?>>
                        <?= date('F', mktime(0, 0, 0, $m, 1)) ?>
                    </option>
                <?php endfor; ?>
            </select>
            <select name="year" class="form-control form-control-sm mr-2 mb-2" style="width: auto;">
                <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                    <option value="<?= $y ?>" <?= $selected_year == $y ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
            <button type="submit" class="btn btn-sm btn-primary mb-2">
                <i class="fas fa-filter mr-1"></i> Filter
            </button>
        </form>
    </div>
</div>

<!-- DTR Table -->
<div class="card-custom">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-table mr-2 text-primary"></i>DTR Records for <?= date('F Y', mktime(0, 0, 0, $selected_month, 1, $selected_year)) ?></h5>
        <?php if (isset($dtr_records) && !empty($dtr_records)): ?>
            <span class="badge badge-primary"><?= count($dtr_records) ?> records</span>
        <?php endif; ?>
    </div>
    <div class="card-body p-0">
        <?php if (isset($dtr_records) && !empty($dtr_records)): ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center border-0">Day</th>
                            <th class="text-center border-0">Date</th>
                            <th class="text-center border-0">AM In</th>
                            <th class="text-center border-0">AM Out</th>
                            <th class="text-center border-0">PM In</th>
                            <th class="text-center border-0">PM Out</th>
                            <th class="text-center border-0">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dtr_records as $record): ?>
                            <?php
                            $day_of_week = date('w', strtotime($record->date));
                            $is_weekend = ($day_of_week == 0 || $day_of_week == 6);
                            $has_missing = (!$record->am_in || !$record->am_out || !$record->pm_in || !$record->pm_out) && !$is_weekend;
                            ?>
                            <tr class="<?= $is_weekend ? 'bg-light' : '' ?>">
                                <td class="text-center font-weight-medium"><?= date('D', strtotime($record->date)) ?></td>
                                <td class="text-center"><?= date('M d, Y', strtotime($record->date)) ?></td>
                                <td class="text-center">
                                    <?php if ($record->am_in): ?>
                                        <span class="badge badge-success"><?= date('h:i A', strtotime($record->am_in)) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($record->am_out): ?>
                                        <span class="badge badge-info"><?= date('h:i A', strtotime($record->am_out)) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($record->pm_in): ?>
                                        <span class="badge badge-success"><?= date('h:i A', strtotime($record->pm_in)) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($record->pm_out): ?>
                                        <span class="badge badge-info"><?= date('h:i A', strtotime($record->pm_out)) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($is_weekend): ?>
                                        <span class="badge badge-secondary">Weekend</span>
                                    <?php elseif ($has_missing): ?>
                                        <span class="badge badge-warning">Incomplete</span>
                                    <?php else: ?>
                                        <span class="badge badge-success">Complete</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-calendar-alt text-muted" style="font-size: 64px;"></i>
                <h5 class="mt-3 text-muted">No DTR records found for this period</h5>
                <p class="text-muted mb-0">Your biometric ID may not be linked or no records exist for the selected month.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Personnel Info -->
<?php if (isset($personnel) && $personnel): ?>
<div class="card-custom mt-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 col-6 mb-3 mb-md-0">
                <small class="text-muted d-block mb-1">Name</small>
                <p class="font-weight-bold mb-0"><?= htmlspecialchars($personnel->firstname . ' ' . $personnel->lastname) ?></p>
            </div>
            <div class="col-md-4 col-6 mb-3 mb-md-0">
                <small class="text-muted d-block mb-1">Biometric ID</small>
                <p class="font-weight-bold mb-0"><?= $personnel->bio_id ?: 'Not Assigned' ?></p>
            </div>
            <div class="col-md-4 col-6">
                <small class="text-muted d-block mb-1">Schedule</small>
                <p class="font-weight-bold mb-0"><?= $personnel->schedule_type ?: '8:00 AM - 5:00 PM' ?></p>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<style>
    .table th {
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        color: #8898aa;
        padding: 14px 12px;
    }
    .table td {
        padding: 12px;
        vertical-align: middle;
        font-size: 13px;
    }
    .font-weight-medium {
        font-weight: 500;
    }
</style>
