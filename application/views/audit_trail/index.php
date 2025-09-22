<div class="page-header">
    <h4 class="page-title"><?= $title ?></h4>
    <ul class="breadcrumbs">
        <li class="nav-home">
            <a href="#">
                <i class="fas fa-history"></i>
            </a>
        </li>
        <li class="separator">
            <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
            <a href="javascript:void(0)">Audit Trail</a>
        </li>
    </ul>
    <div class="ml-md-auto py-2 py-md-0">
        <a href="<?= site_url('admin/audit_trail/export_csv') ?>" class="btn btn-success btn-border btn-round btn-sm" id="exportBtn">
            <span class="btn-label">
                <i class="fas fa-file-csv"></i>
            </span>
            Export CSV
        </a>
    </div>
</div>

<!-- Statistics Cards -->
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
                            <h4 class="card-title" id="totalEdits">
                                <?php 
                                $total = 0;
                                foreach($statistics['action_stats'] as $stat) {
                                    $total += $stat->count;
                                }
                                echo number_format($total);
                                ?>
                            </h4>
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
                            <h4 class="card-title">
                                <?php 
                                $creates = 0;
                                foreach($statistics['action_stats'] as $stat) {
                                    if($stat->action_type == 'CREATE') $creates = $stat->count;
                                }
                                echo number_format($creates);
                                ?>
                            </h4>
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
                            <h4 class="card-title">
                                <?php 
                                $updates = 0;
                                foreach($statistics['action_stats'] as $stat) {
                                    if($stat->action_type == 'UPDATE') $updates = $stat->count;
                                }
                                echo number_format($updates);
                                ?>
                            </h4>
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
                            <h4 class="card-title">
                                <?php 
                                $deletes = 0;
                                foreach($statistics['action_stats'] as $stat) {
                                    if($stat->action_type == 'DELETE') $deletes = $stat->count;
                                }
                                echo number_format($deletes);
                                ?>
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters and Main Table -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-head-row">
                    <div class="card-title">DTR Edit History</div>
                    <div class="card-tools">
                        <button class="btn btn-info btn-border btn-round btn-sm" data-toggle="collapse" data-target="#filterPanel">
                            <i class="fas fa-filter"></i> Filters
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Filter Panel -->
            <div class="collapse" id="filterPanel">
                <div class="card-body border-bottom">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Personnel</label>
                                <select class="form-control" id="personnelFilter">
                                    <option value="">All Personnel</option>
                                    <?php foreach($personnel_list as $personnel): ?>
                                        <option value="<?= $personnel->email ?>">
                                            <?= $personnel->lastname . ', ' . $personnel->firstname . ' ' . $personnel->middlename ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Date From</label>
                                <input type="date" class="form-control" id="dateFromFilter" value="<?= date('Y-m-01') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Date To</label>
                                <input type="date" class="form-control" id="dateToFilter" value="<?= date('Y-m-d') ?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div>
                                    <button class="btn btn-primary btn-sm btn-block" onclick="applyFilters()">
                                        <i class="fas fa-search"></i> Apply
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table id="auditTable" class="display table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Date/Time</th>
                                <th>Personnel</th>
                                <th>Table</th>
                                <th>Action</th>
                                <th>Field</th>
                                <th>Changes</th>
                                <th>Admin</th>
                                <th>Reason</th>
                                <th>Actions</th>
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

<!-- Recent Activities -->
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Most Edited Personnel</div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Personnel</th>
                                <th>Edit Count</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach(array_slice($statistics['personnel_stats'], 0, 10) as $stat): ?>
                            <tr>
                                <td><?= htmlspecialchars($stat->personnel_name) ?></td>
                                <td><span class="badge badge-warning"><?= $stat->edit_count ?></span></td>
                                <td>
                                    <?php 
                                    // Get bio_id for this personnel
                                    $this->db->select('bio_id');
                                    $this->db->where('email', $stat->personnel_email);
                                    $personnel_data = $this->db->get('personnels')->row();
                                    $bio_id = $personnel_data ? $personnel_data->bio_id : null;
                                    ?>
                                    <?php if ($bio_id): ?>
                                    <a href="<?= site_url('admin/audit_trail/personnel_by_bio_id/' . $bio_id) ?>" 
                                       class="btn btn-sm btn-info" title="View Personnel History">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php else: ?>
                                    <a href="<?= site_url('admin/audit_trail/personnel_by_email/' . urlencode($stat->personnel_email)) ?>" 
                                       class="btn btn-sm btn-info" title="View Personnel History">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Most Active Admins</div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Admin</th>
                                <th>Edit Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach(array_slice($statistics['admin_stats'], 0, 10) as $stat): ?>
                            <tr>
                                <td><?= htmlspecialchars($stat->admin_name) ?></td>
                                <td><span class="badge badge-info"><?= $stat->edit_count ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Audit Details Modal -->
<div class="modal fade" id="auditDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Audit Details</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="auditDetailsContent">
                <!-- Content loaded via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script>
$(document).ready(function() {
    // Initialize DataTable with simple data loading
    var table = $('#auditTable').DataTable({
        "processing": true,
        "serverSide": false,
        "ajax": {
            "url": "<?= site_url('admin/audit_trail/get_audit_data') ?>",
            "type": "POST",
            "data": function(d) {
                d.personnel_email = $('#personnelFilter').val();
                d.date_from = $('#dateFromFilter').val();
                d.date_to = $('#dateToFilter').val();
            },
            "dataSrc": "data",
            "error": function(xhr, error, code) {
                console.log('AJAX Error:', xhr.responseText);
                alert('Error loading audit data: ' + error);
            }
        },
        "columns": [
            { "data": 0, "title": "Date/Time" },
            { "data": 1, "title": "Personnel" },
            { "data": 2, "title": "Table" },
            { "data": 3, "title": "Action" },
            { "data": 4, "title": "Field" },
            { "data": 5, "title": "Changes" },
            { "data": 6, "title": "Admin" },
            { "data": 7, "title": "Reason" },
            { "data": 8, "title": "Actions" }
        ],
        "order": [[ 0, "desc" ]],
        "pageLength": 25,
        "responsive": true
    });

    // Apply filters function
    window.applyFilters = function() {
        table.ajax.reload();
        updateExportUrl();
    };

    // Update export URL with current filters
    function updateExportUrl() {
        var params = new URLSearchParams();
        if ($('#personnelFilter').val()) params.append('personnel_email', $('#personnelFilter').val());
        if ($('#dateFromFilter').val()) params.append('date_from', $('#dateFromFilter').val());
        if ($('#dateToFilter').val()) params.append('date_to', $('#dateToFilter').val());
        
        $('#exportBtn').attr('href', '<?= site_url('admin/audit_trail/export_csv') ?>?' + params.toString());
    }

    // View audit details function
    window.viewAuditDetails = function(auditId) {
        $.post('<?= site_url('admin/audit_trail/get_audit_details') ?>', {audit_id: auditId}, function(response) {
            if (response.success) {
                var data = response.data;
                var content = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Basic Information</h6>
                            <table class="table table-sm">
                                <tr><th>Date/Time:</th><td>${data.created_at}</td></tr>
                                <tr><th>Personnel:</th><td>${data.personnel_name || 'N/A'}</td></tr>
                                <tr><th>Email:</th><td>${data.personnel_email || 'N/A'}</td></tr>
                                <tr><th>Table:</th><td>${data.table_name}</td></tr>
                                <tr><th>Record ID:</th><td>${data.record_id}</td></tr>
                                <tr><th>Action:</th><td>${data.action_type}</td></tr>
                                <tr><th>Field:</th><td>${data.field_name || 'All Fields'}</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Admin Information</h6>
                            <table class="table table-sm">
                                <tr><th>Admin:</th><td>${data.admin_name}</td></tr>
                                <tr><th>IP Address:</th><td>${data.ip_address}</td></tr>
                                <tr><th>User Agent:</th><td>${data.user_agent ? data.user_agent.substring(0, 50) + '...' : 'N/A'}</td></tr>
                            </table>
                            
                            <h6>Changes</h6>
                            <table class="table table-sm">
                                <tr><th>Old Value:</th><td>${data.old_value || '<em>empty</em>'}</td></tr>
                                <tr><th>New Value:</th><td>${data.new_value || '<em>empty</em>'}</td></tr>
                            </table>
                        </div>
                    </div>
                    ${data.reason ? '<div class="row"><div class="col-12"><h6>Reason</h6><p class="alert alert-info">' + data.reason + '</p></div></div>' : ''}
                `;
                $('#auditDetailsContent').html(content);
                $('#auditDetailsModal').modal('show');
            } else {
                alert('Error loading audit details: ' + response.message);
            }
        }, 'json');
    };

    // Initialize export URL
    updateExportUrl();
});
</script>
