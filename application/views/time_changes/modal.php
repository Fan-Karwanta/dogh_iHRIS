<!-- Add Time Record Modal -->
<div class="modal fade" id="addAttendance" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Time Record</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="<?= site_url('timechanges/create') ?>">
                    <div class="form-group form-floating-label">
                        <label>Date</label>
                        <input type="date" class="form-control" name="date" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="form-group form-floating-label">
                        <label>Personnel</label>
                        <select class="form-control" name="bio_id" id="basic" style="width:100%;" required>
                            <optgroup label="DOGH Personnel">
                                <?php foreach ($person as $row) : ?>
                                    <option value="<?= $row->bio_id ?>" <?= $row->bio_id == $personnel->bio_id ? 'selected' : '' ?>>
                                        <?= $row->lastname . ', ' . $row->firstname . ' ' . $row->middlename ?>
                                    </option>
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
                        <input type="time" class="form-control" name="pm_in" value="13:00">
                    </div>
                    <div class="form-group form-floating-label">
                        <label>Afternoon Out</label>
                        <input type="time" class="form-control" name="pm_out" value="17:00">
                    </div>
                    <div class="form-group">
                        <label>Reason for Manual Entry</label>
                        <textarea class="form-control" name="reason" rows="2" placeholder="Optional: Explain why this record is being manually added"></textarea>
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

<!-- Edit Time Record Modal -->
<div class="modal fade" id="editBio" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Time Record</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editBioForm">
                    <div class="form-group form-floating-label">
                        <label>Date</label>
                        <input type="date" class="form-control" name="date" value="<?= date('Y-m-d') ?>" required id="date">
                    </div>
                    <div class="form-group form-floating-label">
                        <label>Personnel</label>
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
                        <input type="time" class="form-control" name="pm_in" value="13:00" id="pm_in">
                    </div>
                    <div class="form-group form-floating-label">
                        <label>Afternoon Out</label>
                        <input type="time" class="form-control" name="pm_out" value="17:00" id="pm_out">
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
                    <div class="form-group">
                        <label>Reason for Change</label>
                        <textarea class="form-control" name="reason" id="edit_reason" rows="2" placeholder="Optional: Explain why this record is being modified"></textarea>
                    </div>
                    <input type="hidden" name="id" id="biometrics_id">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitEditForm()">Update</button>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Edit Modal -->
<div class="modal fade" id="bulkEdit" tabindex="-1" role="dialog" aria-labelledby="bulkEditLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkEditLabel">Bulk Edit Time Records</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> <strong>Warning:</strong> This will update <strong><span id="bulkSelectedCount">0</span> selected record(s)</strong> at once.
                </div>
                
                <form id="bulkEditForm">
                    <div class="form-group">
                        <label>Select Field to Update</label>
                        <select class="form-control" id="bulkField" required>
                            <option value="">-- Select Field --</option>
                            <option value="am_in">Morning In</option>
                            <option value="am_out">Morning Out</option>
                            <option value="pm_in">Afternoon In</option>
                            <option value="pm_out">Afternoon Out</option>
                        </select>
                    </div>
                    
                    <div class="form-group" id="bulkValueGroup" style="display: none;">
                        <label>New Time Value</label>
                        <input type="time" class="form-control" id="bulkValue" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Reason for Bulk Change</label>
                        <textarea class="form-control" id="bulkReason" rows="3" placeholder="Required: Explain why these records are being bulk updated" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitBulkEdit()">
                    <i class="fas fa-save"></i> Update All Selected
                </button>
            </div>
        </div>
    </div>
</div>

