<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="page-header">
                <h4 class="page-title">Main Department Biometrics</h4>
                <ul class="breadcrumbs">
                    <li class="nav-home">
                        <a href="<?= site_url('dashboard') ?>">
                            <i class="flaticon-home"></i>
                        </a>
                    </li>
                    <li class="separator">
                        <i class="flaticon-right-arrow"></i>
                    </li>
                    <li class="nav-item">
                        <a href="#">Main Biometrics</a>
                    </li>
                </ul>
            </div>

            <!-- Flash Messages -->
            <?php if ($this->session->flashdata('message')) : ?>
                <div class="alert alert-<?= $this->session->flashdata('success') == 'success' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
                    <?= $this->session->flashdata('message') ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <div class="row">
                <!-- Statistics Cards -->
                <div class="col-sm-6 col-md-3">
                    <div class="card card-stats card-round">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-icon">
                                    <div class="icon-big text-center icon-primary bubble-shadow-small">
                                        <i class="fas fa-fingerprint"></i>
                                    </div>
                                </div>
                                <div class="col col-stats ml-3 ml-sm-0">
                                    <div class="numbers">
                                        <p class="card-category">Total Logs</p>
                                        <h4 class="card-title"><?= number_format($statistics['total_logs']) ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="card card-stats card-round">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-icon">
                                    <div class="icon-big text-center icon-info bubble-shadow-small">
                                        <i class="fas fa-users"></i>
                                    </div>
                                </div>
                                <div class="col col-stats ml-3 ml-sm-0">
                                    <div class="numbers">
                                        <p class="card-category">Unique Staff</p>
                                        <h4 class="card-title"><?= number_format($statistics['unique_staff']) ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="card card-stats card-round">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-icon">
                                    <div class="icon-big text-center icon-success bubble-shadow-small">
                                        <i class="fas fa-user-check"></i>
                                    </div>
                                </div>
                                <div class="col col-stats ml-3 ml-sm-0">
                                    <div class="numbers">
                                        <p class="card-category">Matched</p>
                                        <h4 class="card-title"><?= number_format($statistics['matched_personnel']) ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="card card-stats card-round">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-icon">
                                    <div class="icon-big text-center icon-warning bubble-shadow-small">
                                        <i class="fas fa-user-times"></i>
                                    </div>
                                </div>
                                <div class="col col-stats ml-3 ml-sm-0">
                                    <div class="numbers">
                                        <p class="card-category">Unmatched</p>
                                        <h4 class="card-title"><?= number_format($statistics['unmatched_personnel']) ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Import Section -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">
                                <i class="fas fa-upload mr-2"></i>Import Main Department Biometrics
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <strong><i class="fas fa-info-circle"></i> CSV Format (Main Department Hardware):</strong>
                                <ul class="mb-0 mt-2">
                                    <li><strong>Staff Code</strong> - 8 digits (last 4 digits used, leading zeros ignored)</li>
                                    <li><strong>Department</strong> - Department name</li>
                                    <li><strong>Week</strong> - Day of week</li>
                                    <li><strong>Date</strong> - MM/DD/YYYY format</li>
                                    <li><strong>Time</strong> - HH:MM:SS format</li>
                                    <li><strong>Remark2</strong> - IN or OUT</li>
                                </ul>
                            </div>
                            
                            <form method="POST" action="<?= site_url('mainbiometrics/importCSV') ?>" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label><strong>Select CSV File</strong></label>
                                    <input type="file" class="form-control" name="import_file" accept=".csv" required>
                                    <small class="form-text text-muted">Select the CSV file exported from Main department biometric device</small>
                                </div>
                                
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="sync_to_attendance" name="sync_to_attendance" value="1">
                                        <label class="custom-control-label" for="sync_to_attendance">
                                            <strong>Auto-sync to Attendance Records</strong>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        <i class="fa fa-info-circle"></i> When checked, imported logs will automatically create/update attendance records in the main biometrics table.
                                    </small>
                                </div>
                                
                                <div class="form-group" id="override_group" style="display: none;">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="override_existing" name="override_existing" value="1">
                                        <label class="custom-control-label" for="override_existing">
                                            <strong>Override existing attendance records</strong>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted text-warning">
                                        <i class="fa fa-exclamation-triangle"></i> When checked, existing time entries will be replaced. Otherwise, only empty slots will be filled.
                                    </small>
                                </div>
                                
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-upload mr-2"></i>Import CSV
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Actions Section -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">
                                <i class="fas fa-cogs mr-2"></i>Actions
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Manual Sync -->
                            <div class="mb-4">
                                <h6><i class="fas fa-sync mr-2"></i>Sync to Attendance</h6>
                                <p class="text-muted small">Sync imported logs to the main attendance system.</p>
                                <form method="POST" action="<?= site_url('mainbiometrics/syncToAttendance') ?>" class="form-inline">
                                    <input type="date" class="form-control mr-2" name="sync_date" placeholder="Optional: specific date">
                                    <div class="custom-control custom-checkbox mr-2">
                                        <input type="checkbox" class="custom-control-input" id="sync_override" name="override_existing" value="1">
                                        <label class="custom-control-label" for="sync_override">Override</label>
                                    </div>
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="fas fa-sync"></i> Sync Now
                                    </button>
                                </form>
                            </div>
                            
                            <hr>
                            
                            <!-- Re-match Personnel -->
                            <div class="mb-4">
                                <h6><i class="fas fa-user-check mr-2"></i>Re-match Personnel</h6>
                                <p class="text-muted small">Re-attempt matching unlinked logs to personnel records.</p>
                                <form method="POST" action="<?= site_url('mainbiometrics/rematchPersonnel') ?>">
                                    <button type="submit" class="btn btn-info btn-sm">
                                        <i class="fas fa-user-check"></i> Re-match All
                                    </button>
                                </form>
                            </div>
                            
                            <hr>
                            
                            <!-- View Logs -->
                            <div>
                                <h6><i class="fas fa-list mr-2"></i>View Logs</h6>
                                <p class="text-muted small">View detailed biometric logs.</p>
                                <a href="<?= site_url('mainbiometrics/viewLogs') ?>" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-list"></i> View All Logs
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Unmatched Staff Codes -->
            <?php if (!empty($unmatched)) : ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">
                                <i class="fas fa-exclamation-triangle text-warning mr-2"></i>Unmatched Staff Codes
                            </div>
                            <div class="card-category">
                                Staff codes that don't have matching personnel records. Add these Bio IDs to personnel to enable attendance tracking.
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-sm">
                                    <thead>
                                        <tr>
                                            <th>Staff Code (Bio ID)</th>
                                            <th>Department</th>
                                            <th>Punch Count</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($unmatched as $row) : ?>
                                        <tr>
                                            <td><strong><?= $row->staff_code ?></strong></td>
                                            <td><?= $row->department ?></td>
                                            <td><?= number_format($row->punch_count) ?></td>
                                            <td>
                                                <a href="<?= site_url('personnel?add_bio_id=' . $row->staff_code) ?>" class="btn btn-xs btn-primary" title="Add to Personnel">
                                                    <i class="fas fa-user-plus"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Import History -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">
                                <i class="fas fa-history mr-2"></i>Import History
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (empty($import_history)) : ?>
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p>No imports yet. Upload a CSV file to get started.</p>
                                </div>
                            <?php else : ?>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Batch ID</th>
                                                <th>Filename</th>
                                                <th>Total</th>
                                                <th>Imported</th>
                                                <th>Matched</th>
                                                <th>Unmatched</th>
                                                <th>Duplicates</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($import_history as $history) : ?>
                                            <tr>
                                                <td><code><?= $history->batch_id ?></code></td>
                                                <td><?= $history->filename ?></td>
                                                <td><?= number_format($history->total_records) ?></td>
                                                <td><span class="badge badge-success"><?= number_format($history->imported_records) ?></span></td>
                                                <td><span class="badge badge-info"><?= number_format($history->matched_personnel) ?></span></td>
                                                <td><span class="badge badge-warning"><?= number_format($history->unmatched_personnel) ?></span></td>
                                                <td><span class="badge badge-secondary"><?= number_format($history->duplicate_skipped) ?></span></td>
                                                <td><?= date('M d, Y h:i A', strtotime($history->created_at)) ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
document.getElementById('sync_to_attendance').addEventListener('change', function() {
    document.getElementById('override_group').style.display = this.checked ? 'block' : 'none';
});
</script>
