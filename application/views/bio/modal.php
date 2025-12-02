<!-- Modal -->
<div class="modal fade" id="addAttendance" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Create Biometrics Attendance</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="<?= site_url('biometrics/create') ?>">
                    <div class="form-group form-floating-label">
                        <label>Date</label>
                        <input type="date" class="form-control" name="date" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="form-group form-floating-label">
                        <label>Select Personnel</label>
                        <select class="form-control" name="bio_id" id="basic" style="width:100%;" required>
                            <optgroup label="DOGH Personnel">
                                <?php foreach ($person as $row) : ?>
                                    <option value="<?= $row->bio_id ?>"><?= $row->lastname . ', ' . $row->firstname . ' ' . $row->middlename ?></option>
                                <?php endforeach ?>
                            </optgroup>
                        </select>
                    </div>
                    <div class="form-group form-floating-label">
                        <label>Morning In</label>
                        <input type="time" class="form-control" name="am_in" value="07:30" required>
                    </div>
                    <div class="form-group form-floating-label">
                        <label>Morning Out</label>
                        <input type="time" class="form-control" name="am_out" value="12:00">
                    </div>
                    <div class="form-group form-floating-label">
                        <label>Afternoon In</label>
                        <input type="time" class="form-control" name="pm_in" value="12:30">
                    </div>
                    <div class="form-group form-floating-label">
                        <label>Afternoon Out</label>
                        <input type="time" class="form-control" name="pm_out" value="16:30">
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Create</button>
            </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="editBio" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update Biometrics Attendance</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="<?= site_url('biometrics/update') ?>">
                    <div class="form-group form-floating-label">
                        <label>Date</label>
                        <input type="date" class="form-control" name="date" value="<?= date('Y-m-d') ?>" required id="date">
                    </div>
                    <div class="form-group form-floating-label">
                        <label>Select Personnel</label>
                        <select class="form-control" name="bio_id" id="basic2" style="width:100%;" required>
                            <optgroup label="DOGH Personnel">
                                <?php foreach ($person as $row) : ?>
                                    <option value="<?= $row->bio_id ?>"><?= $row->lastname . ', ' . $row->firstname . ' ' . $row->middlename ?></option>
                                <?php endforeach ?>
                            </optgroup>
                        </select>
                    </div>
                    <div class="form-group form-floating-label">
                        <label>Morning In</label>
                        <input type="time" class="form-control" name="am_in" value="07:30" required id="am_in">
                    </div>
                    <div class="form-group form-floating-label">
                        <label>Morning Out</label>
                        <input type="time" class="form-control" name="am_out" value="12:00" id="am_out">
                    </div>
                    <div class="form-group form-floating-label">
                        <label>Afternoon In</label>
                        <input type="time" class="form-control" name="pm_in" value="12:30" id="pm_in">
                    </div>
                    <div class="form-group form-floating-label">
                        <label>Afternoon Out</label>
                        <input type="time" class="form-control" name="pm_out" value="16:30" id="pm_out">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group form-floating-label">
                                <label>Undertime Hours</label>
                                <input type="number" class="form-control" name="undertime_hours" value="0" min="0" max="8" id="undertime_hours">
                                <small class="form-text text-muted">Hours of undertime (0-8)</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-floating-label">
                                <label>Undertime Minutes</label>
                                <input type="number" class="form-control" name="undertime_minutes" value="0" min="0" max="59" id="undertime_minutes">
                                <small class="form-text text-muted">Minutes of undertime (0-59)</small>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <small><strong>Note:</strong> Undertime is automatically calculated based on the standard 8:00 AM - 5:00 PM schedule. You can manually adjust these values if needed.</small>
                    </div>

            </div>
            <div class="modal-footer">
                <input type="hidden" name="id" id="biometrics_id">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="import" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Import Biometric Attendance</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <strong>CSV Format Requirements:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Column 1: Employee No.</li>
                        <li>Column 2: Name</li>
                        <li>Column 3: Attendance log (MM/DD/YYYY HH:MM format)</li>
                        <li>Column 4: Device Code</li>
                    </ul>
                    <small class="text-muted">Leave date filter empty to import all records from the CSV file.</small>
                </div>
                <form method="POST" action="<?= site_url('biometrics/importCSV') ?>" enctype="multipart/form-data">
                    <div class="form-group form-floating-label">
                        <label>Filter by Date (Optional)</label>
                        <input type="date" class="form-control" name="from" id="from_date">
                        <small class="form-text text-muted">Leave empty to import all records</small>
                    </div>
                    <div class="form-group form-floating-label">
                        <label>Upload Biometric CSV File</label>
                        <input type="file" class="form-control" name="import_file" accept=".csv" required>
                        <small class="form-text text-muted">Select the CSV file exported from your biometric device</small>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="override_existing" name="override_existing" value="1">
                            <label class="custom-control-label" for="override_existing">
                                <strong>Override existing records</strong>
                            </label>
                        </div>
                        <small class="form-text text-muted">
                            <i class="fa fa-info-circle"></i> When checked, imported data will replace existing time entries for the same employee and date. 
                            When unchecked, only empty time slots will be filled.
                        </small>
                    </div>
                    <div class="alert alert-warning" id="override_warning" style="display: none;">
                        <i class="fa fa-exclamation-triangle"></i> <strong>Warning:</strong> Override mode will replace all existing time entries with the new imported data. This action cannot be undone.
                    </div>
                    <script>
                        document.getElementById('override_existing').addEventListener('change', function() {
                            document.getElementById('override_warning').style.display = this.checked ? 'block' : 'none';
                        });
                    </script>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" id="importCSV">Upload</button>
            </div>
            </form>
        </div>
    </div>
</div>