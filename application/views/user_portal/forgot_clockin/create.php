<style>
.fctc-form-container {
    max-width: 700px;
    margin: 0 auto;
    background: #fff;
    border: 2px solid #333;
    padding: 30px 35px;
    font-family: 'Times New Roman', Times, serif;
    color: #000;
}
.fctc-header {
    text-align: center;
    margin-bottom: 15px;
    position: relative;
}
.fctc-header .logo-left {
    position: absolute;
    left: 0;
    top: 0;
    width: 70px;
    height: 70px;
    object-fit: contain;
}
.fctc-header .logo-right {
    position: absolute;
    right: 0;
    top: 0;
    width: 70px;
    height: 70px;
    object-fit: contain;
}
.fctc-header h6 {
    font-size: 13px;
    margin: 0;
    font-weight: normal;
    font-style: italic;
}
.fctc-header h5 {
    font-size: 15px;
    margin: 0;
    font-weight: bold;
    letter-spacing: 1px;
}
.fctc-header .sub-text {
    font-size: 12px;
    font-style: italic;
    margin: 0;
}
.fctc-title {
    text-align: center;
    font-size: 16px;
    font-weight: bold;
    margin: 20px 0 15px;
    text-decoration: underline;
}
.fctc-info-row {
    display: flex;
    align-items: baseline;
    margin-bottom: 8px;
    font-size: 13px;
}
.fctc-info-row label {
    font-weight: bold;
    white-space: nowrap;
    margin-right: 5px;
    margin-bottom: 0;
}
.fctc-info-row .info-value {
    flex: 1;
    border-bottom: 1px solid #000;
    min-height: 20px;
    padding: 0 5px;
    font-weight: bold;
}
.fctc-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
    font-size: 12px;
}
.fctc-table th, .fctc-table td {
    border: 1px solid #000;
    padding: 5px 4px;
    text-align: center;
    vertical-align: middle;
}
.fctc-table th {
    font-weight: bold;
    background: #f9f9f9;
    font-size: 11px;
}
.fctc-table input[type="date"],
.fctc-table input[type="text"],
.fctc-table select {
    border: none;
    border-bottom: 1px solid #999;
    background: transparent;
    text-align: center;
    font-family: 'Times New Roman', Times, serif;
    font-size: 12px;
    width: 100%;
    padding: 2px;
    outline: none;
}
.fctc-table input[type="checkbox"] {
    width: 16px;
    height: 16px;
    cursor: pointer;
}
.fctc-signature {
    margin-top: 30px;
    text-align: right;
    font-size: 13px;
}
.fctc-signature .sig-line {
    display: inline-block;
    width: 250px;
    border-top: 1px solid #000;
    text-align: center;
    padding-top: 3px;
    font-style: italic;
}
.entry-row { transition: background 0.2s; }
.entry-row:hover { background: #f0f8ff; }
.btn-remove-row {
    background: none;
    border: none;
    color: #d32f2f;
    cursor: pointer;
    font-size: 14px;
    padding: 2px 5px;
}
.btn-remove-row:hover { color: #b71c1c; }
</style>

<div class="card-custom">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5><i class="fas fa-file-alt mr-2"></i>New Failure to Clock / Time Changes Request</h5>
        <a href="<?= site_url('clockchangerequest') ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left mr-1"></i>Back
        </a>
    </div>
    <div class="card-body">
        <div class="fctc-form-container" id="fctcFormPreview">
            <!-- Header with logos -->
            <div class="fctc-header">
                <img src="<?= base_url('assets/img/doh_logo1.png') ?>" class="logo-left" alt="DOH Logo">
                <img src="<?= base_url('assets/img/dogh_logo.png') ?>" class="logo-right" alt="DOGH Logo">
                <h6>Republic of the Philippines</h6>
                <h6>Department of Health</h6>
                <h5>DAVAO OCCIDENTAL GENERAL HOSPITAL</h5>
                <p class="sub-text">Lacaron, Malita, Davao Occidental</p>
            </div>

            <div class="fctc-title">Failure to Clock and Time Changes Form</div>

            <!-- Control No -->
            <div class="fctc-info-row">
                <label>Control No.:</label>
                <span class="info-value" style="max-width: 150px;"><?= htmlspecialchars($control_no) ?></span>
            </div>

            <!-- Employee Info -->
            <div class="fctc-info-row">
                <label>Employee ID:</label>
                <span class="info-value" style="max-width: 100px;"><?= htmlspecialchars($personnel->bio_id) ?></span>
                <label style="margin-left: 15px;">Name:</label>
                <span class="info-value"><?= htmlspecialchars(strtoupper($personnel->lastname . ', ' . $personnel->firstname . ' ' . ($personnel->middlename ? substr($personnel->middlename, 0, 1) . '.' : ''))) ?></span>
                <label style="margin-left: 15px;">Designation:</label>
                <span class="info-value" style="max-width: 180px;"><?= htmlspecialchars($personnel->position) ?></span>
            </div>

            <!-- Table -->
            <table class="fctc-table" id="fctcTable">
                <thead>
                    <tr>
                        <th style="width: 120px;">Date</th>
                        <th style="width: 60px;">AM/PM</th>
                        <th colspan="2">Time</th>
                        <th style="width: 100px;">Change</th>
                        <th>Reason</th>
                        <th style="width: 30px;"></th>
                    </tr>
                    <tr>
                        <th></th>
                        <th></th>
                        <th style="width: 35px;">IN</th>
                        <th style="width: 35px;">OUT</th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="fctcTableBody">
                    <!-- Rows will be added dynamically -->
                </tbody>
            </table>

            <!-- Add Row Button -->
            <div class="mt-2 text-center">
                <button type="button" class="btn btn-outline-success btn-sm" id="btnAddRow">
                    <i class="fas fa-plus mr-1"></i>Add Entry
                </button>
            </div>

            <!-- Signature -->
            <div class="fctc-signature">
                <div class="sig-line">
                    <?php if ($supervisor): ?>
                        <?= htmlspecialchars(strtoupper($supervisor->lastname . ', ' . $supervisor->firstname)) ?>
                    <?php else: ?>
                        &nbsp;
                    <?php endif; ?>
                </div>
                <br>
                <div style="text-align: right;">
                    <span style="display: inline-block; width: 250px; text-align: center; font-style: italic; font-size: 12px;">Immediate Supervisor</span>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="text-center mt-4">
            <button type="button" class="btn btn-success btn-lg" id="btnSubmitRequest">
                <i class="fas fa-paper-plane mr-2"></i>Submit Request
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var waitForJQuery = setInterval(function() {
        if (typeof $ !== 'undefined' && typeof jQuery !== 'undefined') {
            clearInterval(waitForJQuery);
            initClockChangeForm();
        }
    }, 50);
});

function initClockChangeForm() {
$(document).ready(function() {
    var rowIndex = 0;

    function addRow() {
        rowIndex++;
        var rn = 'inout_' + rowIndex;
        var row = '<tr class="entry-row" data-row="' + rowIndex + '">' +
            '<td><input type="date" class="fctc-date" data-row="' + rowIndex + '"></td>' +
            '<td><select class="fctc-ampm" data-row="' + rowIndex + '" style="border:none;background:transparent;font-family:inherit;font-size:12px;text-align:center;width:100%;outline:none;">' +
                '<option value="AM">AM</option><option value="PM">PM</option></select></td>' +
            '<td style="text-align:center;"><input type="radio" name="' + rn + '" class="fctc-radio-in" value="IN"></td>' +
            '<td style="text-align:center;"><input type="radio" name="' + rn + '" class="fctc-radio-out" value="OUT"></td>' +
            '<td><input type="text" class="fctc-time" data-row="' + rowIndex + '" placeholder="HH:MM" maxlength="5" style="width:80px;text-align:center;"></td>' +
            '<td><input type="text" class="fctc-reason" data-row="' + rowIndex + '" placeholder=""></td>' +
            '<td><button type="button" class="btn-remove-row" title="Remove"><i class="fas fa-trash-alt"></i></button></td>' +
            '</tr>';
        $('#fctcTableBody').append(row);
    }

    // Auto-format time input: insert colon after 2 digits
    $(document).on('input', '.fctc-time', function() {
        var val = $(this).val().replace(/[^0-9:]/g, '');
        // Auto-insert colon after 2 digits if user hasn't typed one
        if (val.length === 2 && val.indexOf(':') === -1) {
            val = val + ':';
        }
        // Limit to HH:MM format
        if (val.length > 5) {
            val = val.substring(0, 5);
        }
        $(this).val(val);
    });

    // Validate time on blur
    $(document).on('blur', '.fctc-time', function() {
        var val = $(this).val().trim();
        if (!val) return;
        var match = val.match(/^(\d{1,2}):(\d{2})$/);
        if (!match) {
            $(this).css('border-color', 'red');
            return;
        }
        var h = parseInt(match[1], 10);
        var m = parseInt(match[2], 10);
        if (h < 1 || h > 12 || m < 0 || m > 59) {
            $(this).css('border-color', 'red');
            return;
        }
        // Pad hour
        $(this).val(('0' + h).slice(-2) + ':' + ('0' + m).slice(-2));
        $(this).css('border-color', '');
    });

    // Add initial 5 rows
    for (var i = 0; i < 5; i++) {
        addRow();
    }

    $('#btnAddRow').click(function() {
        addRow();
    });

    $(document).on('click', '.btn-remove-row', function() {
        if ($('#fctcTableBody tr').length > 1) {
            $(this).closest('tr').remove();
        }
    });

    $('#btnSubmitRequest').click(function() {
        var items = [];
        var valid = true;
        var errors = [];
        var duplicateCheck = {};

        // Clear previous error highlights
        $('#fctcTableBody tr').css('background', '');

        $('#fctcTableBody tr.entry-row').each(function() {
            var row = $(this);
            var dateVal = (row.find('.fctc-date').val() || '').trim();
            var amPm = (row.find('.fctc-ampm').val() || 'AM');
            var selectedRadio = row.find('input[type="radio"]:checked').val() || '';
            var timeIn = (selectedRadio === 'IN') ? 1 : 0;
            var timeOut = (selectedRadio === 'OUT') ? 1 : 0;
            var timeChange = (row.find('.fctc-time').val() || '').trim();
            var reason = (row.find('.fctc-reason').val() || '').trim();

            // Skip completely empty rows
            if (!dateVal) return;

            // Validation 1: Must select IN or OUT
            if (!selectedRadio) {
                valid = false;
                row.css('background', '#fff3cd');
                errors.push('Row ' + dateVal + ': Please select IN or OUT.');
                return;
            }

            // Validation 2: Time Change is required
            if (!timeChange) {
                valid = false;
                row.css('background', '#fff3cd');
                errors.push('Row ' + dateVal + ': Change time is required.');
                return;
            }

            // Validation 3: Validate time format (HH:MM, 1-12 hours)
            var timeMatch = timeChange.match(/^(\d{1,2}):(\d{2})$/);
            if (!timeMatch) {
                valid = false;
                row.css('background', '#fff3cd');
                errors.push('Row ' + dateVal + ': Invalid time format. Use HH:MM (e.g., 07:30, 12:01).');
                return;
            }
            var hours = parseInt(timeMatch[1], 10);
            var mins = parseInt(timeMatch[2], 10);
            if (hours < 1 || hours > 12 || mins < 0 || mins > 59) {
                valid = false;
                row.css('background', '#fff3cd');
                errors.push('Row ' + dateVal + ': Hours must be 1-12 and minutes 0-59.');
                return;
            }

            // Validation 4: Date cannot be in the future
            var today = new Date();
            today.setHours(0, 0, 0, 0);
            var entryDate = new Date(dateVal + 'T00:00:00');
            if (entryDate > today) {
                valid = false;
                row.css('background', '#fff3cd');
                errors.push('Row ' + dateVal + ': Date cannot be in the future.');
                return;
            }

            // Validation 5: Duplicate check (same date + AM/PM + IN/OUT)
            var dupKey = dateVal + '_' + amPm + '_' + selectedRadio;
            if (duplicateCheck[dupKey]) {
                valid = false;
                row.css('background', '#f8d7da');
                errors.push('Duplicate: ' + dateVal + ' ' + amPm + ' ' + selectedRadio + ' already exists.');
                return;
            }
            duplicateCheck[dupKey] = true;

            items.push({
                date: dateVal,
                am_pm: amPm,
                time_in: timeIn,
                time_out: timeOut,
                time_change: timeChange,
                reason: reason
            });
        });

        if (items.length === 0 && valid) {
            alert('Please fill in at least one entry with a date.');
            return;
        }

        if (!valid) {
            alert('Please fix the following errors:\n\n' + errors.join('\n'));
            return;
        }

        if (!confirm('Submit this Failure to Clock / Time Changes request with ' + items.length + ' entry/entries?')) {
            return;
        }

        var btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Submitting...');

        $.ajax({
            url: '<?= site_url('clockchangerequest/save') ?>',
            type: 'POST',
            data: { items: items },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    window.location.href = '<?= site_url('clockchangerequest/view/') ?>' + response.request_id;
                } else {
                    alert(response.message || 'Failed to submit request.');
                    btn.prop('disabled', false).html('<i class="fas fa-paper-plane mr-2"></i>Submit Request');
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
                btn.prop('disabled', false).html('<i class="fas fa-paper-plane mr-2"></i>Submit Request');
            }
        });
    });
});
}
</script>
