<?php
$dtr_by_date = [];
foreach ($dtr_data as $record) {
    $dtr_by_date[$record->date] = $record;
}
$month_num = date('m', strtotime($selected_month . '-01'));
$year = date('Y', strtotime($selected_month . '-01'));
$days_in_month = date('t', strtotime($selected_month . '-01'));

$sys_query = $this->db->query("SELECT * FROM systems WHERE id=1");
$sys_info = $sys_query->row();

if (!function_exists('isWeekendApproval')) {
    function isWeekendApproval($date) {
        return in_array(date('w', strtotime($date)), [0, 6]);
    }
    function getWeekendLabelApproval($date) {
        $d = date('w', strtotime($date));
        return $d == 0 ? 'SUNDAY' : ($d == 6 ? 'SATURDAY' : 'WEEKEND');
    }
}

// Build changes lookup - include labels and undertime
$changes_lookup = [];
$label_changes = [];
foreach ($items as $date => $item_data) {
    foreach ($item_data['fields'] as $field => $item) {
        if ($field === 'label') {
            $label_changes[$date] = $item;
        } else {
            $changes_lookup[$date][$field] = $item;
        }
    }
}
?>

<style>
.dtr-copy { max-width: 600px; margin: 0 auto; }
.dtr-table { width: 100%; border-collapse: collapse; font-family: 'Times New Roman', serif; }
.dtr-table td { border: 1px solid black; padding: 4px; text-align: center; font-size: 12px; color: black; }
.dtr-table .no-border { border: none; }
.cell-repositioned { background: #fff3cd !important; font-weight: bold; }
.cell-manual { background: #f8d7da !important; font-weight: bold; }
.label-changed { background: #f8d7da !important; }
</style>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5><i class="fas fa-file-alt mr-2"></i>Review DTR Edit Request #<?= $request->id ?></h5>
        <a href="<?= site_url('dtrapproval') ?>" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i>Back</a>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <strong>Personnel:</strong> <?= $request->firstname ?> <?= $request->lastname ?> | 
            <strong>Position:</strong> <?= $request->position ?> | 
            <strong>Submitted:</strong> <?= date('M d, Y h:i A', strtotime($request->created_at)) ?>
            <br><strong>Reason:</strong> <?= $request->reason ?: 'N/A' ?>
        </div>

        <div class="mb-3">
            <span class="badge badge-warning mr-2"><i class="fas fa-arrows-alt mr-1"></i>Repositioned (Yellow)</span>
            <span class="badge badge-danger"><i class="fas fa-edit mr-1"></i>Manual Entry (Red)</span>
        </div>

        <div class="dtr-copy" style="border: 2px solid black; padding: 15px; background: #fff;">
            <table class="dtr-table">
                <thead>
                    <?php if (!empty($sys_info->system_logo)) : ?>
                    <tr><td colspan="7" class="no-border" style="text-align: center;"><img src="<?= base_url('assets/uploads/' . $sys_info->system_logo) ?>" style="max-height: 50px;"></td></tr>
                    <?php endif; ?>
                    <tr><td colspan="7" class="no-border"><strong>Republic of the Philippines</strong></td></tr>
                    <tr><td colspan="7" class="no-border"><strong>Department of Health - REGION XI</strong></td></tr>
                    <tr><td colspan="7" class="no-border"><strong>DAVAO OCCIDENTAL GENERAL HOSPITAL</strong></td></tr>
                    <tr><td colspan="7" class="no-border" style="height: 10px;"></td></tr>
                    <tr><td colspan="7" class="no-border" style="text-align: left;"><strong>Civil Service Form 48</strong></td></tr>
                    <tr>
                        <td class="no-border" style="text-align: left;">Name:</td>
                        <td colspan="2" class="no-border" style="border-bottom: 1px solid black !important; font-weight: bold;"><?= strtoupper($personnel->lastname) ?></td>
                        <td colspan="2" class="no-border" style="border-bottom: 1px solid black !important; font-weight: bold;"><?= strtoupper($personnel->firstname) ?></td>
                        <td colspan="2" class="no-border" style="border-bottom: 1px solid black !important;"><?= !empty($personnel->middlename) ? strtoupper(substr($personnel->middlename, 0, 1)) : '' ?></td>
                    </tr>
                    <tr><td class="no-border"></td><td colspan="2" class="no-border" style="font-size: 10px;">(Surname)</td><td colspan="2" class="no-border" style="font-size: 10px;">(Given Name)</td><td colspan="2" class="no-border" style="font-size: 10px;">(MI)</td></tr>
                    <tr><td class="no-border" style="text-align: left;">For the Month</td><td colspan="6" class="no-border" style="border-bottom: 1px solid black !important; font-weight: bold;"><?= strtoupper(date('F Y', strtotime($selected_month . '-01'))) ?></td></tr>
                    <tr><td colspan="7" class="no-border" style="height: 10px;"></td></tr>
                    <tr><td style="font-weight: bold;">Date</td><td colspan="2" style="font-weight: bold;">AM</td><td colspan="2" style="font-weight: bold;">PM</td><td colspan="2" style="font-weight: bold;">Undertime</td></tr>
                    <tr><td></td><td>Arrival</td><td>Departure</td><td>Arrival</td><td>Departure</td><td>Hours</td><td>Minutes</td></tr>
                </thead>
                <tbody>
                    <?php for ($i = 1; $i <= $days_in_month; $i++):
                        $date = $year . '-' . str_pad($month_num, 2, '0', STR_PAD_LEFT) . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
                        $is_weekend = isWeekendApproval($date);
                        $record = isset($dtr_by_date[$date]) ? $dtr_by_date[$date] : null;
                        $has_entries = $record && (!empty($record->am_in) || !empty($record->am_out) || !empty($record->pm_in) || !empty($record->pm_out));
                        $day_changes = isset($changes_lookup[$date]) ? $changes_lookup[$date] : [];
                        $has_label_change = isset($label_changes[$date]);
                    ?>
                    <tr>
                        <td><?= $i ?></td>
                        <?php if ($has_label_change): ?>
                            <td colspan="6" class="cell-manual"><strong><?= $label_changes[$date]->new_value ?></strong></td>
                        <?php elseif ($is_weekend && !$has_entries && empty($day_changes)): ?>
                            <td colspan="6"><strong><?= getWeekendLabelApproval($date) ?></strong></td>
                        <?php else:
                            $fields = ['morning_in' => 'am_in', 'morning_out' => 'am_out', 'afternoon_in' => 'pm_in', 'afternoon_out' => 'pm_out'];
                            foreach ($fields as $field => $db_field):
                                $change = isset($day_changes[$field]) ? $day_changes[$field] : null;
                                $cell_class = $change ? ($change->edit_type == 'repositioned' ? 'cell-repositioned' : 'cell-manual') : '';
                                $value = $change ? $change->new_value : (isset($record->$db_field) ? $record->$db_field : '');
                        ?>
                            <td class="<?= $cell_class ?>"><?= $value ? date('h:i', strtotime($value)) : '' ?></td>
                        <?php endforeach; 
                            // Undertime fields
                            $ut_hours_change = isset($day_changes['undertime_hours']) ? $day_changes['undertime_hours'] : null;
                            $ut_mins_change = isset($day_changes['undertime_minutes']) ? $day_changes['undertime_minutes'] : null;
                            $ut_hours_class = $ut_hours_change ? 'cell-manual' : '';
                            $ut_mins_class = $ut_mins_change ? 'cell-manual' : '';
                            $ut_hours = $ut_hours_change ? $ut_hours_change->new_value : (isset($record->undertime_hours) ? $record->undertime_hours : '');
                            $ut_mins = $ut_mins_change ? $ut_mins_change->new_value : (isset($record->undertime_minutes) ? $record->undertime_minutes : '');
                        ?>
                            <td class="<?= $ut_hours_class ?>"><?= $ut_hours ?></td>
                            <td class="<?= $ut_mins_class ?>"><?= $ut_mins ?></td>
                        <?php endif; ?>
                    </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>

        <?php if ($request->status == 'pending'): ?>
        <div class="mt-4">
            <div class="form-group">
                <label><strong>Remarks (Optional):</strong></label>
                <textarea class="form-control" id="remarks" rows="2" placeholder="Add remarks..."></textarea>
            </div>
            <form action="<?= site_url('dtrapproval/approve/' . $request->id) ?>" method="post" class="d-inline" id="approveForm">
                <input type="hidden" name="remarks" id="approveRemarks">
                <button type="submit" class="btn btn-success btn-lg mr-2" onclick="document.getElementById('approveRemarks').value=document.getElementById('remarks').value; return confirm('Approve this request?')">
                    <i class="fas fa-check mr-2"></i>Approve
                </button>
            </form>
            <form action="<?= site_url('dtrapproval/reject/' . $request->id) ?>" method="post" class="d-inline" id="rejectForm">
                <input type="hidden" name="remarks" id="rejectRemarks">
                <button type="submit" class="btn btn-danger btn-lg" onclick="document.getElementById('rejectRemarks').value=document.getElementById('remarks').value; return confirm('Reject this request?')">
                    <i class="fas fa-times mr-2"></i>Reject
                </button>
            </form>
        </div>
        <?php endif; ?>
    </div>
</div>
