<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="page-header">
                <h4 class="page-title">Main Department Biometric Logs</h4>
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
                        <a href="<?= site_url('mainbiometrics') ?>">Main Biometrics</a>
                    </li>
                    <li class="separator">
                        <i class="flaticon-right-arrow"></i>
                    </li>
                    <li class="nav-item">
                        <a href="#">Logs</a>
                    </li>
                </ul>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="card-title">
                                    <i class="fas fa-list mr-2"></i>Biometric Logs
                                </div>
                                <a href="<?= site_url('mainbiometrics') ?>" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-arrow-left"></i> Back
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Filters -->
                            <form method="GET" action="<?= site_url('mainbiometrics/viewLogs') ?>" class="mb-4">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Start Date</label>
                                            <input type="date" class="form-control" name="start_date" value="<?= $start_date ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>End Date</label>
                                            <input type="date" class="form-control" name="end_date" value="<?= $end_date ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Staff Code (Bio ID)</label>
                                            <input type="number" class="form-control" name="staff_code" value="<?= $staff_code ?>" placeholder="e.g., 99">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <button type="submit" class="btn btn-primary btn-block">
                                                <i class="fas fa-search"></i> Filter
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <!-- Results Summary -->
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> 
                                Showing <strong><?= count($logs) ?></strong> records 
                                from <strong><?= date('M d, Y', strtotime($start_date)) ?></strong> 
                                to <strong><?= date('M d, Y', strtotime($end_date)) ?></strong>
                                <?php if ($staff_code) : ?>
                                    for Staff Code <strong><?= $staff_code ?></strong>
                                <?php endif; ?>
                            </div>

                            <!-- Logs Table -->
                            <?php if (empty($logs)) : ?>
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p>No logs found for the selected criteria.</p>
                                </div>
                            <?php else : ?>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover" id="logsTable">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Staff Code</th>
                                                <th>Personnel</th>
                                                <th>Department</th>
                                                <th>Day</th>
                                                <th>Date</th>
                                                <th>Time</th>
                                                <th>Type</th>
                                                <th>Batch</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($logs as $log) : ?>
                                            <tr>
                                                <td><?= $log->id ?></td>
                                                <td><strong><?= $log->staff_code ?></strong></td>
                                                <td>
                                                    <?php if ($log->personnel_id) : ?>
                                                        <span class="badge badge-success">
                                                            <i class="fas fa-user-check"></i> Linked
                                                        </span>
                                                    <?php else : ?>
                                                        <span class="badge badge-warning">
                                                            <i class="fas fa-user-times"></i> Unlinked
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= $log->department ?></td>
                                                <td><?= $log->week_day ?></td>
                                                <td><?= date('M d, Y', strtotime($log->log_date)) ?></td>
                                                <td><?= date('h:i:s A', strtotime($log->log_time)) ?></td>
                                                <td>
                                                    <?php if ($log->remark == 'IN') : ?>
                                                        <span class="badge badge-success">
                                                            <i class="fas fa-sign-in-alt"></i> IN
                                                        </span>
                                                    <?php else : ?>
                                                        <span class="badge badge-danger">
                                                            <i class="fas fa-sign-out-alt"></i> OUT
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><code class="small"><?= substr($log->import_batch, 0, 15) ?>...</code></td>
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
$(document).ready(function() {
    $('#logsTable').DataTable({
        "pageLength": 25,
        "order": [[5, "desc"], [6, "asc"]],
        "language": {
            "emptyTable": "No logs found"
        }
    });
});
</script>
