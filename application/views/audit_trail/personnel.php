<div class="page-header">
    <h4 class="page-title"><?= $title ?></h4>
    <ul class="breadcrumbs">
        <li class="nav-home">
            <a href="<?= site_url('admin/audit_trail') ?>">
                <i class="fas fa-history"></i>
            </a>
        </li>
        <li class="separator">
            <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
            <a href="<?= site_url('admin/audit_trail') ?>">Audit Trail</a>
        </li>
        <li class="separator">
            <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
            <a href="javascript:void(0)"><?= $personnel->firstname ?> <?= $personnel->lastname ?></a>
        </li>
    </ul>
    <div class="ml-md-auto py-2 py-md-0">
        <a href="<?= site_url('admin/audit_trail/export_csv?personnel_email=' . urlencode($personnel->email)) ?>" class="btn btn-success btn-border btn-round btn-sm">
            <span class="btn-label">
                <i class="fas fa-file-csv"></i>
            </span>
            Export Personnel History
        </a>
        <a href="<?= site_url('admin/audit_trail') ?>" class="btn btn-secondary btn-border btn-round btn-sm">
            <span class="btn-label">
                <i class="fas fa-arrow-left"></i>
            </span>
            Back to General History
        </a>
    </div>
</div>

<!-- Personnel Info Card -->
<div class="row">
    <div class="col-md-12">
        <div class="card card-profile">
            <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="profile-picture">
                    <div class="avatar avatar-xl">
                        <img src="<?= base_url('assets/img/profile.jpg') ?>" alt="..." class="avatar-img rounded-circle">
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="user-profile text-center">
                    <div class="name"><?= $personnel->lastname ?>, <?= $personnel->firstname ?> <?= $personnel->middlename ?></div>
                    <div class="job"><?= $personnel->position ?></div>
                    <div class="desc"><?= $personnel->email ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Statistics -->
<div class="row">
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-primary bubble-shadow-small">
                            <i class="fas fa-edit"></i>
                        </div>
                    </div>
                    <div class="col col-stats ml-3 ml-sm-0">
                        <div class="numbers">
                            <p class="card-category">Total Edits</p>
                            <h4 class="card-title"><?= number_format($edit_frequency->total_edits) ?></h4>
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
                            <i class="fas fa-plus"></i>
                        </div>
                    </div>
                    <div class="col col-stats ml-3 ml-sm-0">
                        <div class="numbers">
                            <p class="card-category">Records Created</p>
                            <h4 class="card-title"><?= number_format($edit_frequency->creates) ?></h4>
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
                            <i class="fas fa-edit"></i>
                        </div>
                    </div>
                    <div class="col col-stats ml-3 ml-sm-0">
                        <div class="numbers">
                            <p class="card-category">Records Updated</p>
                            <h4 class="card-title"><?= number_format($edit_frequency->updates) ?></h4>
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
                        <div class="icon-big text-center icon-danger bubble-shadow-small">
                            <i class="fas fa-trash"></i>
                        </div>
                    </div>
                    <div class="col col-stats ml-3 ml-sm-0">
                        <div class="numbers">
                            <p class="card-category">Records Deleted</p>
                            <h4 class="card-title"><?= number_format($edit_frequency->deletes) ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Timeline -->
<?php if ($edit_frequency->first_edit): ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Edit Timeline</div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-info"><i class="fas fa-calendar-alt"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">First Edit</span>
                                <span class="info-box-number"><?= date('M j, Y g:i A', strtotime($edit_frequency->first_edit)) ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-success"><i class="fas fa-calendar-check"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Last Edit</span>
                                <span class="info-box-number"><?= date('M j, Y g:i A', strtotime($edit_frequency->last_edit)) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Detailed Edit History -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Detailed Edit History</div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="personnelAuditTable" class="display table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Date/Time</th>
                                <th>Action</th>
                                <th>Field Changed</th>
                                <th>Changes</th>
                                <th>Admin</th>
                                <th>Reason</th>
                                <th>IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity Timeline -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Recent Activity Timeline</div>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <?php 
                    $timeline_limit = 10;
                    $recent_activities = array_slice($audit_history, 0, $timeline_limit);
                    foreach($recent_activities as $activity): 
                    ?>
                    <div class="timeline-item">
                        <div class="timeline-badge 
                            <?php 
                            switch($activity->action_type) {
                                case 'CREATE': echo 'bg-success'; break;
                                case 'UPDATE': echo 'bg-warning'; break;
                                case 'DELETE': echo 'bg-danger'; break;
                                default: echo 'bg-secondary';
                            }
                            ?>">
                            <i class="fas fa-
                            <?php 
                            switch($activity->action_type) {
                                case 'CREATE': echo 'plus'; break;
                                case 'UPDATE': echo 'edit'; break;
                                case 'DELETE': echo 'trash'; break;
                                default: echo 'circle';
                            }
                            ?>"></i>
                        </div>
                        <div class="timeline-panel">
                            <div class="timeline-heading">
                                <h4 class="timeline-title">
                                    <?= ucfirst($activity->action_type) ?> 
                                    <?= $activity->field_name ? '- ' . ucfirst(str_replace('_', ' ', $activity->field_name)) : 'Record' ?>
                                </h4>
                                <p><small class="text-muted">
                                    <i class="fas fa-clock"></i> <?= date('M j, Y g:i A', strtotime($activity->created_at)) ?>
                                    by <?= htmlspecialchars($activity->admin_name) ?>
                                </small></p>
                            </div>
                            <div class="timeline-body">
                                <?php if ($activity->action_type == 'UPDATE' && $activity->old_value && $activity->new_value): ?>
                                    <p><strong>Changed from:</strong> <code><?= htmlspecialchars($activity->old_value) ?></code></p>
                                    <p><strong>Changed to:</strong> <code><?= htmlspecialchars($activity->new_value) ?></code></p>
                                <?php endif; ?>
                                
                                <?php if ($activity->reason): ?>
                                    <p><strong>Reason:</strong> <?= htmlspecialchars($activity->reason) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <?php if (count($audit_history) > $timeline_limit): ?>
                    <div class="timeline-item">
                        <div class="timeline-badge bg-info">
                            <i class="fas fa-ellipsis-h"></i>
                        </div>
                        <div class="timeline-panel">
                            <div class="timeline-body">
                                <p class="text-muted">
                                    <?= count($audit_history) - $timeline_limit ?> more activities...
                                    View the detailed table above for complete history.
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding: 20px 0;
}

.timeline:before {
    content: '';
    position: absolute;
    top: 0;
    left: 25px;
    height: 100%;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-badge {
    position: absolute;
    left: 0;
    top: 0;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 16px;
    z-index: 1;
}

.timeline-panel {
    margin-left: 70px;
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 15px;
    position: relative;
}

.timeline-panel:before {
    content: '';
    position: absolute;
    left: -8px;
    top: 20px;
    width: 0;
    height: 0;
    border-top: 8px solid transparent;
    border-bottom: 8px solid transparent;
    border-right: 8px solid #e9ecef;
}

.timeline-panel:after {
    content: '';
    position: absolute;
    left: -7px;
    top: 20px;
    width: 0;
    height: 0;
    border-top: 8px solid transparent;
    border-bottom: 8px solid transparent;
    border-right: 8px solid #f8f9fa;
}

.timeline-title {
    margin: 0 0 5px 0;
    font-size: 16px;
    font-weight: 600;
}

.timeline-body {
    margin-top: 10px;
}

.info-box {
    display: block;
    min-height: 90px;
    background: #fff;
    width: 100%;
    box-shadow: 0 1px 1px rgba(0,0,0,0.1);
    border-radius: 2px;
    margin-bottom: 15px;
}

.info-box-icon {
    border-top-left-radius: 2px;
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
    border-bottom-left-radius: 2px;
    display: block;
    float: left;
    height: 90px;
    width: 90px;
    text-align: center;
    font-size: 45px;
    line-height: 90px;
    background: rgba(0,0,0,0.2);
}

.info-box-content {
    padding: 5px 10px;
    margin-left: 90px;
}

.info-box-text {
    text-transform: uppercase;
    font-weight: bold;
    font-size: 13px;
}

.info-box-number {
    display: block;
    font-weight: bold;
    font-size: 18px;
}
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script>
$(document).ready(function() {
    // Initialize DataTable for personnel audit history
    $('#personnelAuditTable').DataTable({
        "processing": true,
        "ajax": {
            "url": "<?= site_url('admin/audit_trail/get_personnel_audit_data') ?>",
            "type": "POST",
            "data": {
                "personnel_email": "<?= $personnel->email ?>"
            }
        },
        "columns": [
            { "data": 0 },
            { "data": 1 },
            { "data": 2 },
            { "data": 3 },
            { "data": 4 },
            { "data": 5 },
            { "data": 6 }
        ],
        "order": [[ 0, "desc" ]],
        "pageLength": 25,
        "responsive": true
    });
});
</script>
