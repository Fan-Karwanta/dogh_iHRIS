<?php
$dtr_by_date = [];
foreach ($dtr_data as $record) {
    $dtr_by_date[$record->date] = $record;
}
$month_num = date('m', strtotime($selected_month . '-01'));
$year = date('Y', strtotime($selected_month . '-01'));
$days_in_month = date('t', strtotime($selected_month . '-01'));

// Get system info
$sys_query = $this->db->query("SELECT * FROM systems WHERE id=1");
$sys_info = $sys_query->row();

// Load holidays
$this->load->model('HolidayModel', 'holidayModel');

function isWeekendEdit($date) {
    return in_array(date('w', strtotime($date)), [0, 6]);
}

function getWeekendLabelEdit($date) {
    $d = date('w', strtotime($date));
    return $d == 0 ? 'SUNDAY' : ($d == 6 ? 'SATURDAY' : 'WEEKEND');
}

function isHolidayEdit($date, $holidayModel) {
    return $holidayModel->is_holiday($date);
}
?>

<style>
.dtr-preview-container {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
.dtr-copy {
    border: 2px solid black;
    padding: 15px;
    background: #fff;
    max-width: 600px;
    margin: 0 auto;
}
.dtr-table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
    font-family: 'Times New Roman', serif;
}
.dtr-table td, .dtr-table th {
    border: 1px solid black;
    padding: 4px;
    text-align: center;
    font-size: 12px;
    color: black;
}
.dtr-table .no-border {
    border: none;
}

/* Editable cells */
.editable-cell {
    cursor: pointer;
    transition: all 0.2s;
    min-height: 22px;
    position: relative;
}
.editable-cell:hover {
    background: #e3f2fd !important;
}
.editable-cell.has-data {
    cursor: grab;
}
.editable-cell.dragging {
    opacity: 0.5;
    background: #bbdefb !important;
}
.editable-cell.drag-over {
    background: #c8e6c9 !important;
    outline: 2px dashed #4caf50;
}
.editable-cell.editing {
    background: #fff !important;
    padding: 0 !important;
}

/* Highlight colors for changes */
.cell-repositioned {
    background: #fff3cd !important;
    font-weight: bold;
}
.cell-manual {
    background: #f8d7da !important;
    font-weight: bold;
}
.cell-clock-change {
    background: #d4eaff !important;
    font-weight: bold;
}

/* Label cells (LEAVE, OB, etc.) */
.editable-label {
    cursor: pointer;
    transition: all 0.2s;
}
.editable-label:hover {
    background: #e3f2fd !important;
}

/* Edit mode banner */
.edit-mode-banner {
    background: linear-gradient(135deg, #ff9800, #f57c00);
    color: white;
    padding: 12px 20px;
    border-radius: 8px;
    margin-bottom: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
}
.edit-mode-banner .badge {
    font-size: 11px;
    padding: 5px 10px;
}

/* Input styling in cells */
.editable-cell input, .editable-cell select {
    width: 100%;
    border: 2px solid #1572e8;
    padding: 2px;
    text-align: center;
    font-size: 11px;
    font-family: 'Times New Roman', serif;
    box-sizing: border-box;
}
.editable-cell select {
    font-weight: bold;
}

/* Changes summary */
.changes-summary {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    margin-top: 20px;
}
.changes-summary .badge {
    margin: 2px;
    font-size: 11px;
}

/* Instructions panel */
.instructions-panel {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 15px;
}
.instructions-panel h6 {
    margin-bottom: 10px;
    font-weight: 600;
}
.instructions-panel ul {
    margin: 0;
    padding-left: 20px;
}
.instructions-panel li {
    margin-bottom: 5px;
    font-size: 13px;
}
</style>

<div class="page-header">
    <h4 class="page-title">Edit My DTR</h4>
    <div class="ml-md-auto py-2 py-md-0">
        <input type="month" class="form-control d-inline-block" style="width:150px" id="monthSelector" value="<?= $selected_month ?>">
        <a href="<?= site_url('personneldtredit/my_requests') ?>" class="btn btn-info btn-border btn-round btn-sm ml-2">
            <span class="btn-label"><i class="fas fa-list"></i></span> My Requests
        </a>
    </div>
</div>

<?php if ($has_pending_request): ?>
<div class="alert alert-warning"><i class="fas fa-exclamation-triangle mr-2"></i>You have a pending request for this month. You cannot submit another until it's reviewed.</div>
<?php endif; ?>

<div class="instructions-panel">
    <h6><i class="fas fa-info-circle mr-2"></i>How to Edit Your DTR</h6>
    <ul>
        <li><strong>Click</strong> any time cell to enter a time or select a label (Leave, OB, etc.)</li>
        <li><strong>Drag & Drop</strong> existing time blocks to reposition them</li>
        <li><strong>Click</strong> weekend/holiday labels to change them (e.g., if you worked that day)</li>
        <li><strong>Yellow</strong> = Repositioned data | <strong>Red</strong> = Manual entry</li>
    </ul>
</div>

<div class="edit-mode-banner">
    <div>
        <i class="fas fa-edit mr-2"></i><strong>EDIT MODE ACTIVE</strong> - Click cells to edit
    </div>
    <div>
        <span class="badge badge-warning mr-2"><i class="fas fa-arrows-alt mr-1"></i>Repositioned (Yellow)</span>
        <span class="badge badge-danger mr-2"><i class="fas fa-keyboard mr-1"></i>Manual Entry (Red)</span>
        <span class="badge" style="background:#d4eaff;color:#000;"><i class="fas fa-clock mr-1"></i>Clock Change (Light Blue)</span>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="dtr-preview-container" id="printThis">
                    <div class="dtr-copy">
                        <table class="dtr-table">
                            <thead>
                                <?php if (!empty($sys_info->system_logo)) : ?>
                                <tr><td colspan="7" class="no-border" style="text-align: center; padding: 5px;">
                                    <img src="<?= base_url('assets/uploads/' . $sys_info->system_logo) ?>" alt="Logo" style="max-height: 50px;">
                                </td></tr>
                                <?php endif; ?>
                                <tr><td colspan="7" class="no-border" style="font-size: 12px;"><strong>Republic of the Philippines</strong></td></tr>
                                <tr><td colspan="7" class="no-border" style="font-size: 12px;"><strong>Department of Health</strong></td></tr>
                                <tr><td colspan="7" class="no-border" style="font-size: 12px;"><strong>REGION XI</strong></td></tr>
                                <tr><td colspan="7" class="no-border" style="font-size: 12px;"><strong>DAVAO OCCIDENTAL GENERAL HOSPITAL</strong></td></tr>
                                <tr><td colspan="7" class="no-border" style="height: 10px;"></td></tr>
                                <tr><td colspan="7" class="no-border" style="text-align: left; font-size: 12px;"><strong>Civil Service Form 48</strong></td></tr>
                                <tr><td colspan="7" class="no-border" style="height: 5px;"></td></tr>
                                <tr>
                                    <td class="no-border" style="text-align: left; width: 10%;">Name:</td>
                                    <td colspan="2" class="no-border" style="border-bottom: 1px solid black !important; font-weight: bold;"><?= strtoupper($personnel->lastname) ?></td>
                                    <td colspan="2" class="no-border" style="border-bottom: 1px solid black !important; font-weight: bold;"><?= strtoupper($personnel->firstname) ?></td>
                                    <td colspan="2" class="no-border" style="border-bottom: 1px solid black !important; font-weight: bold;"><?= !empty($personnel->middlename) ? strtoupper(substr($personnel->middlename, 0, 1)) : '' ?></td>
                                </tr>
                                <tr>
                                    <td class="no-border"></td>
                                    <td colspan="2" class="no-border" style="font-size: 10px;">(Surname)</td>
                                    <td colspan="2" class="no-border" style="font-size: 10px;">(Given Name)</td>
                                    <td colspan="2" class="no-border" style="font-size: 10px;">(MI)</td>
                                </tr>
                                <tr><td colspan="7" class="no-border" style="height: 8px;"></td></tr>
                                <tr>
                                    <td class="no-border" style="text-align: left;">For the Month</td>
                                    <td colspan="6" class="no-border" style="border-bottom: 1px solid black !important; font-weight: bold;"><?= strtoupper(date('F Y', strtotime($selected_month . '-01'))) ?></td>
                                </tr>
                                <tr><td colspan="7" class="no-border" style="height: 10px;"></td></tr>
                                <tr><td colspan="7" class="no-border" style="text-align: left;">Official Hours For Arrival and Departure</td></tr>
                                <tr><td colspan="7" class="no-border" style="height: 5px;"></td></tr>
                                <tr>
                                    <td class="no-border" style="text-align: left;">Regular Days:</td>
                                    <td colspan="2" class="no-border" style="border-bottom: 1px solid black !important;">8:00 AM - 5:00 PM</td>
                                    <td class="no-border" style="text-align: left;">Saturdays:</td>
                                    <td colspan="3" class="no-border" style="border-bottom: 1px solid black !important;">AS REQUIRED</td>
                                </tr>
                                <tr><td colspan="7" class="no-border" style="height: 10px;"></td></tr>
                                <tr>
                                    <td style="font-weight: bold;">Date</td>
                                    <td colspan="2" style="font-weight: bold; font-style: italic;">AM</td>
                                    <td colspan="2" style="font-weight: bold; font-style: italic;">PM</td>
                                    <td colspan="2" style="font-weight: bold; font-style: italic;">Undertime</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td style="font-style: italic;">Arrival</td>
                                    <td style="font-style: italic;">Departure</td>
                                    <td style="font-style: italic;">Arrival</td>
                                    <td style="font-style: italic;">Departure</td>
                                    <td style="font-style: italic;">Hours</td>
                                    <td style="font-style: italic;">Minutes</td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $cc_entries = isset($clock_change_entries) ? $clock_change_entries : [];
                                ?>
                                <?php for ($i = 1; $i <= $days_in_month; $i++):
                                    $date = $year . '-' . str_pad($month_num, 2, '0', STR_PAD_LEFT) . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
                                    $is_weekend = isWeekendEdit($date);
                                    $is_holiday = isHolidayEdit($date, $this->holidayModel);
                                    $record = isset($dtr_by_date[$date]) ? $dtr_by_date[$date] : null;
                                    $has_entries = $record && (!empty($record->am_in) || !empty($record->am_out) || !empty($record->pm_in) || !empty($record->pm_out));
                                    $cc_am_in = isset($cc_entries[$date . '_am_in']);
                                    $cc_am_out = isset($cc_entries[$date . '_am_out']);
                                    $cc_pm_in = isset($cc_entries[$date . '_pm_in']);
                                    $cc_pm_out = isset($cc_entries[$date . '_pm_out']);
                                ?>
                                <tr data-date="<?= $date ?>">
                                    <td><?= $i ?></td>
                                    <?php if (($is_weekend || $is_holiday) && !$has_entries): ?>
                                        <td colspan="6" class="editable-label" data-field="label" data-original="<?= $is_holiday ? 'HOLIDAY' : getWeekendLabelEdit($date) ?>">
                                            <strong><?= $is_holiday ? 'HOLIDAY' : getWeekendLabelEdit($date) ?></strong>
                                        </td>
                                    <?php else: ?>
                                        <td class="editable-cell <?= !empty($record->am_in) ? 'has-data' : '' ?> <?= $cc_am_in ? 'cell-clock-change' : '' ?>" data-field="morning_in" data-original="<?= $record->am_in ?? '' ?>" data-original-source="<?= !empty($record->am_in) ? 'biometric' : '' ?>">
                                            <?= !empty($record->am_in) ? date('h:i', strtotime($record->am_in)) : '' ?>
                                        </td>
                                        <td class="editable-cell <?= !empty($record->am_out) ? 'has-data' : '' ?> <?= $cc_am_out ? 'cell-clock-change' : '' ?>" data-field="morning_out" data-original="<?= $record->am_out ?? '' ?>" data-original-source="<?= !empty($record->am_out) ? 'biometric' : '' ?>">
                                            <?= !empty($record->am_out) ? date('h:i', strtotime($record->am_out)) : '' ?>
                                        </td>
                                        <td class="editable-cell <?= !empty($record->pm_in) ? 'has-data' : '' ?> <?= $cc_pm_in ? 'cell-clock-change' : '' ?>" data-field="afternoon_in" data-original="<?= $record->pm_in ?? '' ?>" data-original-source="<?= !empty($record->pm_in) ? 'biometric' : '' ?>">
                                            <?= !empty($record->pm_in) ? date('h:i', strtotime($record->pm_in)) : '' ?>
                                        </td>
                                        <td class="editable-cell <?= !empty($record->pm_out) ? 'has-data' : '' ?> <?= $cc_pm_out ? 'cell-clock-change' : '' ?>" data-field="afternoon_out" data-original="<?= $record->pm_out ?? '' ?>" data-original-source="<?= !empty($record->pm_out) ? 'biometric' : '' ?>">
                                            <?= !empty($record->pm_out) ? date('h:i', strtotime($record->pm_out)) : '' ?>
                                        </td>
                                        <td class="editable-cell" data-field="undertime_hours" data-original="<?= $record->undertime_hours ?? '' ?>" data-original-source="<?= isset($record->undertime_hours) && $record->undertime_hours !== '' ? 'biometric' : '' ?>">
                                            <?= isset($record->undertime_hours) ? $record->undertime_hours : '' ?>
                                        </td>
                                        <td class="editable-cell" data-field="undertime_minutes" data-original="<?= $record->undertime_minutes ?? '' ?>" data-original-source="<?= isset($record->undertime_minutes) && $record->undertime_minutes !== '' ? 'biometric' : '' ?>">
                                            <?= isset($record->undertime_minutes) ? $record->undertime_minutes : '' ?>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                                <?php endfor; ?>
                            </tbody>
                        </table>
                        
                        <!-- Signature Section -->
                        <table class="dtr-table" style="margin-top: 15px;">
                            <tr><td colspan="7" class="no-border" style="text-align: left; padding: 5px; font-size: 11px;">
                                I certify on my honor that the above is true and correct report of the hours of work performed, record of which was made daily at the time of arrival and departure from the office.
                            </td></tr>
                            <tr><td colspan="7" class="no-border" style="padding: 15px; text-align: center;">
                                <strong style="text-decoration: underline;"><?= strtoupper($personnel->firstname . ' ' . (!empty($personnel->middlename) ? substr($personnel->middlename, 0, 1) . '. ' : '') . $personnel->lastname) ?></strong><br>
                                <span style="font-size: 11px;">Employee</span>
                            </td></tr>
                        </table>
                    </div>
                </div>
                
                <div class="mt-4 p-3 bg-light rounded" id="changesSummary" style="display:none;">
                    <h6><i class="fas fa-edit mr-2"></i>Pending Changes: <span id="changesCount" class="badge badge-primary">0</span></h6>
                    <div id="changesList" class="mt-2"></div>
                </div>
                
                <div class="mt-4">
                    <div class="form-group">
                        <label><strong>Reason for DTR Edit Request:</strong></label>
                        <textarea class="form-control" id="editReason" placeholder="Please provide a detailed reason for the DTR edit request..." rows="3"></textarea>
                    </div>
                    <div class="d-flex align-items-center">
                        <button class="btn btn-secondary btn-lg mr-3" id="undoBtn" onclick="undoLastChange()" disabled>
                            <i class="fas fa-undo mr-2"></i>Undo <span class="undo-count"></span>
                        </button>
                        <button class="btn btn-primary btn-lg" id="submitBtn" onclick="submitRequest()" <?= $has_pending_request ? 'disabled' : '' ?>>
                            <i class="fas fa-paper-plane mr-2"></i>Submit DTR Edit Request
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let changes = {};
let originalData = {}; // Store original biometric data for comparison
let undoStack = []; // Stack for undo functionality
const baseUrl = '<?= site_url() ?>';
const selectedMonth = '<?= $selected_month ?>';

// Label options for dropdown
const labelOptions = [
    { value: '', text: '-- Select Label --' },
    { value: 'ABSENT:full', text: 'ABSENT (Full Row)' },
    { value: 'OFFICIAL BUSINESS:full', text: 'OFFICIAL BUSINESS (Full Row)' },
    { value: 'OFFICIAL TIME:full', text: 'OFFICIAL TIME (Full Row)' },
    { value: 'OFF:full', text: 'OFF (Full Row)' },
    { value: 'LEAVE:full', text: 'LEAVE (Full Row)' },
    { value: 'SICK LEAVE:full', text: 'SICK LEAVE (Full Row)' },
    { value: 'VACATION LEAVE:full', text: 'VACATION LEAVE (Full Row)' },
    { value: 'SPL:full', text: 'SPL (Full Row)' },
    { value: 'TRAINING:full', text: 'TRAINING (Full Row)' },
    { value: 'HOLIDAY:full', text: 'HOLIDAY (Full Row)' },
    { value: 'SATURDAY:full', text: 'SATURDAY (Full Row)' },
    { value: 'SUNDAY:full', text: 'SUNDAY (Full Row)' }
];

// Initialize - store original data and set up event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Store original biometric data
    document.querySelectorAll('.editable-cell').forEach(cell => {
        const row = cell.closest('tr');
        const date = row.dataset.date;
        const field = cell.dataset.field;
        const key = date + '_' + field;
        originalData[key] = {
            value: cell.dataset.original || '',
            source: cell.dataset.originalSource || ''
        };
    });
    
    // Set up editable cells
    document.querySelectorAll('.editable-cell').forEach(cell => {
        cell.setAttribute('draggable', cell.classList.contains('has-data'));
        cell.addEventListener('dragstart', handleDragStart);
        cell.addEventListener('dragover', handleDragOver);
        cell.addEventListener('dragleave', handleDragLeave);
        cell.addEventListener('drop', handleDrop);
        cell.addEventListener('dragend', handleDragEnd);
        cell.addEventListener('click', handleCellClick);
    });
    
    // Set up label cells (weekend/holiday)
    document.querySelectorAll('.editable-label').forEach(cell => {
        cell.addEventListener('click', handleLabelClick);
    });
});

// Drag and Drop handlers
function handleDragStart(e) {
    if (!this.classList.contains('has-data')) { e.preventDefault(); return; }
    const row = this.closest('tr');
    e.dataTransfer.setData('text/plain', JSON.stringify({
        sourceDate: row.dataset.date,
        sourceField: this.dataset.field,
        value: this.dataset.original,
        display: this.textContent.trim(),
        hadOriginalData: this.dataset.originalSource === 'biometric'
    }));
    this.classList.add('dragging');
}

function handleDragOver(e) { 
    e.preventDefault(); 
    if (!this.classList.contains('dragging')) {
        this.classList.add('drag-over'); 
    }
}

function handleDragLeave(e) { 
    this.classList.remove('drag-over'); 
}

function handleDragEnd(e) { 
    document.querySelectorAll('.editable-cell').forEach(c => c.classList.remove('dragging', 'drag-over')); 
}

function handleDrop(e) {
    e.preventDefault();
    this.classList.remove('drag-over');
    
    const data = JSON.parse(e.dataTransfer.getData('text/plain'));
    const targetRow = this.closest('tr');
    const targetDate = targetRow.dataset.date;
    const targetField = this.dataset.field;
    
    // If dropped on same location, do nothing (no highlight)
    if (data.sourceDate === targetDate && data.sourceField === targetField) {
        return;
    }
    
    // ONLY allow repositioning within the SAME DATE
    if (data.sourceDate !== targetDate) {
        alert('You can only reposition time within the same date.');
        return;
    }
    
    // Save state for undo before making changes
    saveUndoState();
    
    // Check if target cell originally had biometric data
    const targetKey = targetDate + '_' + targetField;
    const targetOriginal = originalData[targetKey];
    
    // Find and clear the source cell
    const sourceRow = document.querySelector(`tr[data-date="${data.sourceDate}"]`);
    const sourceCell = sourceRow.querySelector(`[data-field="${data.sourceField}"]`);
    const sourceKey = data.sourceDate + '_' + data.sourceField;
    if (sourceCell) {
        sourceCell.textContent = '';
        sourceCell.classList.remove('has-data', 'cell-repositioned', 'cell-manual');
        sourceCell.dataset.original = '';
        sourceCell.dataset.originalSource = ''; // Clear original source marker
        sourceCell.setAttribute('draggable', 'false');
        
        // IMPORTANT: Update originalData to mark source cell as no longer having biometric data
        // This ensures any manual entry in this cell will be marked as "manual" (red) not "repositioned" (yellow)
        if (originalData[sourceKey]) {
            originalData[sourceKey] = { value: '', source: '' };
        }
    }
    
    // Update the target cell
    this.textContent = data.display;
    this.classList.add('has-data', 'cell-repositioned');
    this.classList.remove('cell-manual');
    this.dataset.original = data.value;
    this.setAttribute('draggable', 'true');
    
    // Record the change - this is repositioned data
    changes[targetKey] = { 
        date: targetDate, 
        field: targetField, 
        value: data.value, 
        type: 'repositioned', 
        display: data.display,
        originalValue: targetOriginal ? targetOriginal.value : ''
    };
    
    updateChangesSummary();
}

// Cell click handler - for time entry and label selection
function handleCellClick(e) {
    const cell = this;
    if (cell.classList.contains('editing')) return;
    
    const field = cell.dataset.field;
    const row = cell.closest('tr');
    const date = row.dataset.date;
    const currentValue = cell.textContent.trim();
    const originalValue = cell.dataset.original || '';
    const key = date + '_' + field;
    const originalInfo = originalData[key];
    const hadOriginalData = originalInfo && originalInfo.source === 'biometric';
    
    cell.classList.add('editing');
    
    // For undertime fields, use number input
    if (field.includes('undertime')) {
        const input = document.createElement('input');
        input.type = 'number';
        input.min = '0';
        input.max = field.includes('hours') ? '8' : '59';
        input.value = currentValue;
        input.style.cssText = 'width: 100%; text-align: center;';
        
        cell.innerHTML = '';
        cell.appendChild(input);
        input.focus();
        input.select();
        
        input.addEventListener('blur', function() {
            const newVal = this.value.trim();
            
            // Check if value changed from original
            if (newVal !== (originalInfo ? originalInfo.value : '')) {
                // Save state for undo before making changes
                saveUndoState();
                
                cell.textContent = newVal;
                cell.classList.remove('editing');
                // Undertime edits are always manual (red) since user is manually changing it
                cell.classList.add('cell-manual');
                cell.classList.remove('cell-repositioned');
                changes[key] = { 
                    date: date, 
                    field: field, 
                    value: newVal, 
                    type: 'manual', 
                    display: newVal,
                    originalValue: originalInfo ? originalInfo.value : ''
                };
            } else {
                cell.textContent = newVal;
                cell.classList.remove('editing');
                // Value is same as original, remove any change
                cell.classList.remove('cell-manual', 'cell-repositioned');
                delete changes[key];
            }
            updateChangesSummary();
        });
        
        input.addEventListener('keydown', function(e) { if (e.key === 'Enter') this.blur(); });
        return;
    }
    
    // For time fields, show time input + label dropdown
    const container = document.createElement('div');
    container.style.cssText = 'display: flex; flex-direction: column; gap: 2px;';
    
    const input = document.createElement('input');
    input.type = 'time';
    input.value = originalValue ? originalValue.substring(0, 5) : '';
    
    const select = document.createElement('select');
    labelOptions.forEach(opt => {
        const option = document.createElement('option');
        option.value = opt.value;
        option.textContent = opt.text;
        select.appendChild(option);
    });
    
    container.appendChild(input);
    container.appendChild(select);
    cell.innerHTML = '';
    cell.appendChild(container);
    input.focus();
    
    // Handle label selection
    select.addEventListener('change', function() {
        if (select.value) {
            const [labelText, mergeType] = select.value.split(':');
            if (mergeType === 'full') {
                convertToLabelCell(row, date, labelText);
            }
        }
    });
    
    // Handle time input blur
    input.addEventListener('blur', function() {
        setTimeout(() => {
            if (document.activeElement === select) return;
            
            const newVal = input.value;
            cell.classList.remove('editing');
            
            if (newVal) {
                // Save state for undo before making changes
                saveUndoState();
                
                const displayTime = formatTime(newVal);
                cell.textContent = displayTime;
                cell.classList.add('has-data');
                cell.dataset.original = newVal + ':00';
                cell.setAttribute('draggable', 'true');
                
                // Determine if this is repositioned or manual
                // Manual = cell originally had NO biometric data
                // Repositioned = cell originally HAD biometric data (even if different value)
                const editType = hadOriginalData ? 'repositioned' : 'manual';
                
                cell.classList.remove('cell-repositioned', 'cell-manual');
                cell.classList.add(editType === 'repositioned' ? 'cell-repositioned' : 'cell-manual');
                
                changes[key] = { 
                    date: date, 
                    field: field, 
                    value: newVal + ':00', 
                    type: editType, 
                    display: displayTime,
                    originalValue: originalInfo ? originalInfo.value : ''
                };
            } else {
                cell.textContent = '';
                cell.classList.remove('has-data', 'cell-repositioned', 'cell-manual');
                cell.setAttribute('draggable', 'false');
                delete changes[key];
            }
            updateChangesSummary();
        }, 100);
    });
    
    input.addEventListener('keydown', function(e) { if (e.key === 'Enter') this.blur(); });
}

// Label cell click handler (for weekend/holiday rows)
function handleLabelClick(e) {
    const cell = this;
    if (cell.classList.contains('editing')) return;
    
    const row = cell.closest('tr');
    const date = row.dataset.date;
    const currentLabel = cell.dataset.original || cell.textContent.trim();
    
    cell.classList.add('editing');
    
    const select = document.createElement('select');
    select.style.cssText = 'width: 100%; font-weight: bold; text-align: center;';
    
    const options = ['', 'SATURDAY', 'SUNDAY', 'HOLIDAY', 'ABSENT', 'OFFICIAL BUSINESS', 'OFFICIAL TIME', 'OFF', 'LEAVE', 'SICK LEAVE', 'VACATION LEAVE', 'SPL', 'TRAINING'];
    options.forEach(opt => {
        const option = document.createElement('option');
        option.value = opt;
        option.textContent = opt || '-- Convert to Time Cells --';
        if (opt === currentLabel) option.selected = true;
        select.appendChild(option);
    });
    
    cell.innerHTML = '';
    cell.appendChild(select);
    select.focus();
    
    select.addEventListener('change', function() {
        const newLabel = select.value;
        cell.classList.remove('editing');
        
        if (newLabel) {
            if (newLabel !== currentLabel) {
                // Save state for undo before making changes
                saveUndoState();
                
                cell.innerHTML = '<strong>' + newLabel + '</strong>';
                cell.dataset.original = newLabel;
                changes[date + '_label'] = { date: date, field: 'label', value: newLabel, type: 'manual', display: newLabel, originalValue: currentLabel };
                cell.classList.add('cell-manual');
            } else {
                cell.innerHTML = '<strong>' + newLabel + '</strong>';
                cell.dataset.original = newLabel;
            }
        } else {
            // Save state for undo before converting
            saveUndoState();
            // Convert to time cells
            convertLabelToTimeCells(row, date);
        }
        updateChangesSummary();
    });
    
    select.addEventListener('blur', function() {
        if (!select.value && select.value !== '') {
            cell.innerHTML = '<strong>' + currentLabel + '</strong>';
        }
        cell.classList.remove('editing');
    });
}

// Convert row to label cell
function convertToLabelCell(row, date, labelText) {
    // Save state for undo before making changes
    saveUndoState();
    
    // Remove existing cells except date
    const cells = row.querySelectorAll('td');
    for (let i = cells.length - 1; i > 0; i--) {
        cells[i].remove();
    }
    
    // Create label cell
    const labelCell = document.createElement('td');
    labelCell.colSpan = 6;
    labelCell.className = 'editable-label cell-manual';
    labelCell.dataset.field = 'label';
    labelCell.dataset.original = labelText;
    labelCell.innerHTML = '<strong>' + labelText + '</strong>';
    labelCell.addEventListener('click', handleLabelClick);
    row.appendChild(labelCell);
    
    // Clear any time changes for this date and add label change
    Object.keys(changes).forEach(key => {
        if (key.startsWith(date + '_') && !key.endsWith('_label')) {
            delete changes[key];
        }
    });
    changes[date + '_label'] = { date: date, field: 'label', value: labelText, type: 'manual', display: labelText, originalValue: '' };
    updateChangesSummary();
}

// Convert label cell to time cells
function convertLabelToTimeCells(row, date) {
    const labelCell = row.querySelector('.editable-label');
    if (labelCell) labelCell.remove();
    
    const fields = ['morning_in', 'morning_out', 'afternoon_in', 'afternoon_out', 'undertime_hours', 'undertime_minutes'];
    fields.forEach(field => {
        const td = document.createElement('td');
        td.className = 'editable-cell';
        td.dataset.field = field;
        td.dataset.original = '';
        td.dataset.originalSource = '';
        td.setAttribute('draggable', 'false');
        td.addEventListener('dragstart', handleDragStart);
        td.addEventListener('dragover', handleDragOver);
        td.addEventListener('dragleave', handleDragLeave);
        td.addEventListener('drop', handleDrop);
        td.addEventListener('dragend', handleDragEnd);
        td.addEventListener('click', handleCellClick);
        row.appendChild(td);
        
        // Store in originalData
        originalData[date + '_' + field] = { value: '', source: '' };
    });
    
    // Remove label change
    delete changes[date + '_label'];
    updateChangesSummary();
}

// Format time for display
function formatTime(timeStr) {
    if (!timeStr) return '';
    const [hours, minutes] = timeStr.split(':');
    const h = parseInt(hours);
    const ampm = h >= 12 ? 'PM' : 'AM';
    const h12 = h % 12 || 12;
    return String(h12).padStart(2, '0') + ':' + minutes;
}

// Save current state for undo
function saveUndoState() {
    const state = {
        changes: JSON.parse(JSON.stringify(changes)),
        cells: []
    };
    
    // Save all editable cells state
    document.querySelectorAll('.editable-cell').forEach(cell => {
        const row = cell.closest('tr');
        state.cells.push({
            date: row.dataset.date,
            field: cell.dataset.field,
            content: cell.textContent,
            original: cell.dataset.original,
            hasData: cell.classList.contains('has-data'),
            isRepositioned: cell.classList.contains('cell-repositioned'),
            isManual: cell.classList.contains('cell-manual')
        });
    });
    
    // Save label cells state
    document.querySelectorAll('.editable-label').forEach(cell => {
        const row = cell.closest('tr');
        state.cells.push({
            date: row.dataset.date,
            field: 'label',
            content: cell.innerHTML,
            original: cell.dataset.original,
            isLabel: true,
            isManual: cell.classList.contains('cell-manual')
        });
    });
    
    undoStack.push(state);
    updateUndoButton();
}

// Undo last change
function undoLastChange() {
    if (undoStack.length === 0) {
        alert('Nothing to undo.');
        return;
    }
    
    const state = undoStack.pop();
    changes = state.changes;
    
    // Restore cells
    state.cells.forEach(cellState => {
        const row = document.querySelector(`tr[data-date="${cellState.date}"]`);
        if (!row) return;
        
        if (cellState.isLabel) {
            const labelCell = row.querySelector('.editable-label');
            if (labelCell) {
                labelCell.innerHTML = cellState.content;
                labelCell.dataset.original = cellState.original;
                labelCell.classList.toggle('cell-manual', cellState.isManual);
            }
        } else {
            const cell = row.querySelector(`[data-field="${cellState.field}"]`);
            if (cell && cell.classList.contains('editable-cell')) {
                cell.textContent = cellState.content;
                cell.dataset.original = cellState.original;
                cell.classList.toggle('has-data', cellState.hasData);
                cell.classList.toggle('cell-repositioned', cellState.isRepositioned);
                cell.classList.toggle('cell-manual', cellState.isManual);
                cell.setAttribute('draggable', cellState.hasData ? 'true' : 'false');
            }
        }
    });
    
    updateChangesSummary();
    updateUndoButton();
}

// Update undo button state
function updateUndoButton() {
    const btn = document.getElementById('undoBtn');
    if (btn) {
        btn.disabled = undoStack.length === 0;
        btn.querySelector('.undo-count').textContent = undoStack.length > 0 ? `(${undoStack.length})` : '';
    }
}

// Update changes summary
function updateChangesSummary() {
    const count = Object.keys(changes).length;
    document.getElementById('changesCount').textContent = count;
    document.getElementById('changesSummary').style.display = count > 0 ? 'block' : 'none';
    
    let html = '';
    for (const [key, change] of Object.entries(changes)) {
        const badge = change.type === 'repositioned' ? 'warning' : 'danger';
        const fieldDisplay = change.field.replace(/_/g, ' ');
        html += '<span class="badge badge-' + badge + ' mr-1 mb-1">' + change.date + ' - ' + fieldDisplay + '</span>';
    }
    document.getElementById('changesList').innerHTML = html;
}

// Submit request
function submitRequest() {
    const reason = document.getElementById('editReason').value.trim();
    if (!reason) { alert('Please provide a reason for the DTR edit.'); return; }
    if (Object.keys(changes).length === 0) { alert('No changes to submit.'); return; }
    
    if (!confirm('Submit DTR edit request for approval?')) return;
    
    fetch(baseUrl + 'personneldtredit/submit_request', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ month: selectedMonth, changes: Object.values(changes), reason: reason })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) { alert(data.message); location.reload(); }
        else { alert('Error: ' + data.message); }
    })
    .catch(err => alert('Error: ' + err.message));
}

// Month selector
document.getElementById('monthSelector').addEventListener('change', function() {
    window.location.href = baseUrl + 'personneldtredit?month=' + this.value;
});
</script>
