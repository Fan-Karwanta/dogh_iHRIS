<?php
// Attendance History View
?>
<style>
.history-card {
    transition: transform 0.2s;
}
.history-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.badge-late {
    background-color: #f39c12;
    color: white;
}
.badge-early {
    background-color: #e67e22;
    color: white;
}
</style>

<div class="page-header">
    <h4 class="page-title">
        <a href="<?= site_url('personnel/personnel_profile/' . $personnel->id . '?month=' . $selected_month . '&year=' . $selected_year) ?>" class="btn btn-sm btn-secondary mr-2">
            <i class="fas fa-arrow-left"></i> Back
        </a>
        <?= $metric_title ?> History - <?= $personnel->firstname . ' ' . $personnel->lastname ?>
    </h4>
    <div class="ml-auto">
        <form method="GET" class="form-inline d-inline">
            <select name="month" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                <?php for ($m = 1; $m <= 12; $m++): ?>
                    <option value="<?= str_pad($m, 2, '0', STR_PAD_LEFT) ?>" <?= $selected_month == str_pad($m, 2, '0', STR_PAD_LEFT) ? 'selected' : '' ?>>
                        <?= date('F', mktime(0, 0, 0, $m, 1)) ?>
                    </option>
                <?php endfor; ?>
            </select>
            <select name="year" class="form-control form-control-sm" onchange="this.form.submit()">
                <?php for ($y = date('Y'); $y >= date('Y') - 3; $y--): ?>
                    <option value="<?= $y ?>" <?= $selected_year == $y ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">
                    <?= $metric_title ?> - <?= date('F Y', mktime(0, 0, 0, $selected_month, 1, $selected_year)) ?>
                </h4>
                <p class="text-muted mb-0">
                    Total Records: <strong><?= count($history) ?></strong>
                </p>
            </div>
            <div class="card-body">
                <?php if (!empty($history)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <?php if ($metric == 'late_arrivals'): ?>
                                        <th>Scheduled Time</th>
                                        <th>Actual Arrival</th>
                                        <th>Late By</th>
                                        <th>Status</th>
                                    <?php elseif ($metric == 'early_departures'): ?>
                                        <th>Scheduled Time</th>
                                        <th>Actual Departure</th>
                                        <th>Early By</th>
                                        <th>Status</th>
                                    <?php elseif ($metric == 'present_days' || $metric == 'complete_dtr'): ?>
                                        <th>AM In</th>
                                        <th>AM Out</th>
                                        <th>PM In</th>
                                        <th>PM Out</th>
                                        <th>Status</th>
                                    <?php elseif ($metric == 'absent_days'): ?>
                                        <th>Day of Week</th>
                                        <th>Status</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $counter = 1; ?>
                                <?php foreach ($history as $record): ?>
                                    <tr>
                                        <td><?= $counter++ ?></td>
                                        <td><?= date('M d, Y (D)', strtotime($record['date'])) ?></td>
                                        
                                        <?php if ($metric == 'late_arrivals'): ?>
                                            <td><?= date('g:i A', strtotime($record['scheduled_time'])) ?></td>
                                            <td><?= date('g:i A', strtotime($record['actual_time'])) ?></td>
                                            <td><span class="badge badge-late"><?= $record['late_by_minutes'] ?> mins</span></td>
                                            <td><span class="badge badge-warning"><?= $record['status'] ?></span></td>
                                        
                                        <?php elseif ($metric == 'early_departures'): ?>
                                            <td><?= date('g:i A', strtotime($record['scheduled_time'])) ?></td>
                                            <td><?= date('g:i A', strtotime($record['actual_time'])) ?></td>
                                            <td><span class="badge badge-early"><?= $record['early_by_minutes'] ?> mins</span></td>
                                            <td><span class="badge badge-warning"><?= $record['status'] ?></span></td>
                                        
                                        <?php elseif ($metric == 'present_days' || $metric == 'complete_dtr'): ?>
                                            <td><?= $record['morning_in'] ? date('g:i A', strtotime($record['morning_in'])) : '<span class="text-muted">-</span>' ?></td>
                                            <td><?= $record['morning_out'] ? date('g:i A', strtotime($record['morning_out'])) : '<span class="text-muted">-</span>' ?></td>
                                            <td><?= $record['afternoon_in'] ? date('g:i A', strtotime($record['afternoon_in'])) : '<span class="text-muted">-</span>' ?></td>
                                            <td><?= $record['afternoon_out'] ? date('g:i A', strtotime($record['afternoon_out'])) : '<span class="text-muted">-</span>' ?></td>
                                            <td><span class="badge badge-success"><?= $record['status'] ?></span></td>
                                        
                                        <?php elseif ($metric == 'absent_days'): ?>
                                            <td><?= $record['day_of_week'] ?></td>
                                            <td><span class="badge badge-danger"><?= $record['status'] ?></span></td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No records found for this period</h5>
                        <p class="text-muted">There are no <?= strtolower($metric_title) ?> records for <?= date('F Y', mktime(0, 0, 0, $selected_month, 1, $selected_year)) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
