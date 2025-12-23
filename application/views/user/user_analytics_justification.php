<?php
$user = $this->ion_auth->user()->row();
?>
<style>
.formula-box {
    background-color: #f8f9fa;
    border-left: 4px solid #1abc9c;
    padding: 15px;
    margin: 20px 0;
    border-radius: 4px;
}
.formula-title {
    font-weight: bold;
    color: #1abc9c;
    margin-bottom: 10px;
}
.frequency-badge {
    display: inline-block;
    padding: 5px 10px;
    border-radius: 4px;
    margin: 5px;
}
.mode-highlight {
    background-color: #1abc9c;
    color: white;
    font-weight: bold;
}
.frequency-normal {
    background-color: #e9ecef;
    color: #495057;
}
</style>

<div class="page-header">
    <h4 class="page-title">
        <a href="<?= site_url('auth/user_profile/' . $profile_data->id . '?month=' . $selected_month . '&year=' . $selected_year) ?>" class="btn btn-sm btn-secondary mr-2">
            <i class="fas fa-arrow-left"></i> Back
        </a>
        <?= $metric_title ?> - Calculation Justification
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
                    Employee: <strong><?= $profile_data->first_name . ' ' . $profile_data->last_name ?></strong>
                </p>
            </div>
            <div class="card-body">
                
                <?php if ($metric == 'total_hours'): ?>
                    <!-- Total Hours Worked Justification -->
                    <div class="formula-box">
                        <div class="formula-title"><i class="fas fa-calculator"></i> Formula</div>
                        <p class="mb-2"><strong>Total Hours Worked = Σ (Morning Hours + Afternoon Hours)</strong></p>
                        <p class="mb-0 text-muted">Where:</p>
                        <ul class="text-muted">
                            <li>Morning Hours = (AM Out - AM In) in hours</li>
                            <li>Afternoon Hours = (PM Out - PM In) in hours</li>
                            <li>Σ = Sum of all working days in the month</li>
                        </ul>
                    </div>

                    <div class="alert alert-info">
                        <strong>Result:</strong> <?= $breakdown['total_hours'] ?> hours worked across <?= $breakdown['total_days'] ?> days
                    </div>

                    <?php if (!empty($breakdown['breakdown'])): ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>AM In</th>
                                        <th>AM Out</th>
                                        <th>PM In</th>
                                        <th>PM Out</th>
                                        <th>Morning Hours</th>
                                        <th>Afternoon Hours</th>
                                        <th class="text-primary"><strong>Daily Total</strong></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $counter = 1; ?>
                                    <?php foreach ($breakdown['breakdown'] as $record): ?>
                                        <tr>
                                            <td><?= $counter++ ?></td>
                                            <td><?= date('M d, Y (D)', strtotime($record['date'])) ?></td>
                                            <td><?= $record['morning_in'] ? date('g:i A', strtotime($record['morning_in'])) : '<span class="text-muted">-</span>' ?></td>
                                            <td><?= $record['morning_out'] ? date('g:i A', strtotime($record['morning_out'])) : '<span class="text-muted">-</span>' ?></td>
                                            <td><?= $record['afternoon_in'] ? date('g:i A', strtotime($record['afternoon_in'])) : '<span class="text-muted">-</span>' ?></td>
                                            <td><?= $record['afternoon_out'] ? date('g:i A', strtotime($record['afternoon_out'])) : '<span class="text-muted">-</span>' ?></td>
                                            <td><?= $record['morning_hours'] ?> hrs</td>
                                            <td><?= $record['afternoon_hours'] ?> hrs</td>
                                            <td class="text-primary"><strong><?= $record['daily_total'] ?> hrs</strong></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr class="table-success">
                                        <td colspan="8" class="text-right"><strong>TOTAL HOURS:</strong></td>
                                        <td class="text-primary"><strong><?= $breakdown['total_hours'] ?> hrs</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No records found</h5>
                        </div>
                    <?php endif; ?>

                <?php elseif ($metric == 'mode_arrival'): ?>
                    <!-- Mode Arrival Justification -->
                    <div class="formula-box">
                        <div class="formula-title"><i class="fas fa-calculator"></i> Formula</div>
                        <p class="mb-2"><strong>Mode Arrival = Most Frequently Occurring Arrival Time (grouped in 15-minute intervals)</strong></p>
                        <p class="mb-0 text-muted">Process:</p>
                        <ol class="text-muted">
                            <li>Collect all morning arrival times (AM In) for the month</li>
                            <li>Round each time to the nearest 15-minute interval (e.g., 7:48 AM → 7:45 AM)</li>
                            <li>Count the frequency of each interval</li>
                            <li>The interval with the highest frequency is the MODE</li>
                        </ol>
                    </div>

                    <div class="alert alert-info">
                        <strong>Result:</strong> Mode Arrival Time = <strong><?= $breakdown['mode_time'] ? $breakdown['mode_time'] : 'N/A' ?></strong> (based on <?= $breakdown['total_records'] ?> records)
                    </div>

                    <?php if (!empty($breakdown['frequency_map'])): ?>
                        <h5 class="mt-4 mb-3">Frequency Distribution (15-minute intervals)</h5>
                        <div class="mb-4">
                            <?php foreach ($breakdown['frequency_map'] as $freq): ?>
                                <span class="frequency-badge <?= $freq['is_mode'] ? 'mode-highlight' : 'frequency-normal' ?>">
                                    <?= $freq['time_interval'] ?>: <?= $freq['count'] ?> times
                                    <?= $freq['is_mode'] ? '<i class="fas fa-star"></i>' : '' ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($breakdown['arrivals'])): ?>
                        <h5 class="mt-4 mb-3">Detailed Arrival Records</h5>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>Actual Arrival Time</th>
                                        <th>Rounded to 15-min Interval</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $counter = 1; ?>
                                    <?php foreach ($breakdown['arrivals'] as $record): ?>
                                        <tr>
                                            <td><?= $counter++ ?></td>
                                            <td><?= date('M d, Y (D)', strtotime($record['date'])) ?></td>
                                            <td><?= $record['actual_time'] ?></td>
                                            <td><span class="badge badge-secondary"><?= $record['rounded_interval'] ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No arrival records found</h5>
                        </div>
                    <?php endif; ?>

                <?php elseif ($metric == 'mode_departure'): ?>
                    <!-- Mode Departure Justification -->
                    <div class="formula-box">
                        <div class="formula-title"><i class="fas fa-calculator"></i> Formula</div>
                        <p class="mb-2"><strong>Mode Departure = Most Frequently Occurring Departure Time (grouped in 15-minute intervals)</strong></p>
                        <p class="mb-0 text-muted">Process:</p>
                        <ol class="text-muted">
                            <li>Collect all afternoon departure times (PM Out) for the month</li>
                            <li>Round each time to the nearest 15-minute interval (e.g., 5:03 PM → 5:00 PM)</li>
                            <li>Count the frequency of each interval</li>
                            <li>The interval with the highest frequency is the MODE</li>
                        </ol>
                    </div>

                    <div class="alert alert-info">
                        <strong>Result:</strong> Mode Departure Time = <strong><?= $breakdown['mode_time'] ? $breakdown['mode_time'] : 'N/A' ?></strong> (based on <?= $breakdown['total_records'] ?> records)
                    </div>

                    <?php if (!empty($breakdown['frequency_map'])): ?>
                        <h5 class="mt-4 mb-3">Frequency Distribution (15-minute intervals)</h5>
                        <div class="mb-4">
                            <?php foreach ($breakdown['frequency_map'] as $freq): ?>
                                <span class="frequency-badge <?= $freq['is_mode'] ? 'mode-highlight' : 'frequency-normal' ?>">
                                    <?= $freq['time_interval'] ?>: <?= $freq['count'] ?> times
                                    <?= $freq['is_mode'] ? '<i class="fas fa-star"></i>' : '' ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($breakdown['departures'])): ?>
                        <h5 class="mt-4 mb-3">Detailed Departure Records</h5>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>Actual Departure Time</th>
                                        <th>Rounded to 15-min Interval</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $counter = 1; ?>
                                    <?php foreach ($breakdown['departures'] as $record): ?>
                                        <tr>
                                            <td><?= $counter++ ?></td>
                                            <td><?= date('M d, Y (D)', strtotime($record['date'])) ?></td>
                                            <td><?= $record['actual_time'] ?></td>
                                            <td><span class="badge badge-secondary"><?= $record['rounded_interval'] ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No departure records found</h5>
                        </div>
                    <?php endif; ?>

                <?php endif; ?>

            </div>
        </div>
    </div>
</div>
