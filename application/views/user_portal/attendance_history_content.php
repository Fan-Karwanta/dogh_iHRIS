<div class="card-custom">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-history mr-2 text-primary"></i>Attendance History</h5>
        <?php if (isset($attendance_history) && !empty($attendance_history)): ?>
            <span class="badge badge-primary"><?= count($attendance_history) ?> records</span>
        <?php endif; ?>
    </div>
    <div class="card-body p-0">
        <?php if (isset($attendance_history) && !empty($attendance_history)): ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0">Date</th>
                            <th class="text-center border-0">AM In</th>
                            <th class="text-center border-0">AM Out</th>
                            <th class="text-center border-0">PM In</th>
                            <th class="text-center border-0">PM Out</th>
                            <th class="text-center border-0">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($attendance_history as $record): ?>
                            <?php
                            $day_of_week = date('w', strtotime($record->date));
                            $is_weekend = ($day_of_week == 0 || $day_of_week == 6);
                            $has_all = $record->am_in && $record->am_out && $record->pm_in && $record->pm_out;
                            $has_some = $record->am_in || $record->am_out || $record->pm_in || $record->pm_out;
                            ?>
                            <tr class="<?= $is_weekend ? 'bg-light' : '' ?>">
                                <td>
                                    <div class="font-weight-bold"><?= date('M d, Y', strtotime($record->date)) ?></div>
                                    <small class="text-muted"><?= date('l', strtotime($record->date)) ?></small>
                                </td>
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
                                    <?php elseif ($has_all): ?>
                                        <span class="badge badge-success">Complete</span>
                                    <?php elseif ($has_some): ?>
                                        <span class="badge badge-warning">Incomplete</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Absent</span>
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
                <h5 class="mt-3 text-muted">No Attendance History</h5>
                <p class="text-muted mb-0">No attendance records found for your account.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Personnel Summary -->
<?php if (isset($personnel) && $personnel): ?>
<div class="card-custom mt-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 col-6 mb-3 mb-md-0">
                <small class="text-muted d-block mb-1">Employee</small>
                <p class="font-weight-bold mb-0"><?= htmlspecialchars($personnel->firstname . ' ' . $personnel->lastname) ?></p>
            </div>
            <div class="col-md-4 col-6 mb-3 mb-md-0">
                <small class="text-muted d-block mb-1">Position</small>
                <p class="font-weight-bold mb-0"><?= $personnel->position ?: '-' ?></p>
            </div>
            <div class="col-md-4 col-6">
                <small class="text-muted d-block mb-1">Department</small>
                <p class="font-weight-bold mb-0"><?= $personnel->role ?: '-' ?></p>
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
        padding: 14px 16px;
    }
    .table td {
        padding: 14px 16px;
        vertical-align: middle;
        font-size: 13px;
    }
</style>
