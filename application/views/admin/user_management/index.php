<div class="content">
    <div class="page-inner">
        <div class="page-header">
            <h4 class="page-title">User Management</h4>
            <ul class="breadcrumbs">
                <li class="nav-home">
                    <a href="<?= site_url('dashboard') ?>"><i class="flaticon-home"></i></a>
                </li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="#">User Management</a></li>
            </ul>
        </div>

        <?php if (isset($message) && $message): ?>
            <div class="alert alert-<?= isset($success) && $success ? 'success' : 'danger' ?> alert-dismissible fade show">
                <?= $message ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php endif; ?>

        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-sm-6 col-md-3">
                <div class="card card-stats card-round">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-icon">
                                <div class="icon-big text-center icon-warning bubble-shadow-small">
                                    <i class="fas fa-clock"></i>
                                </div>
                            </div>
                            <div class="col col-stats ml-3 ml-sm-0">
                                <div class="numbers">
                                    <p class="card-category">Pending</p>
                                    <h4 class="card-title"><?= isset($stats->pending) ? $stats->pending : 0 ?></h4>
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
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                            <div class="col col-stats ml-3 ml-sm-0">
                                <div class="numbers">
                                    <p class="card-category">Approved</p>
                                    <h4 class="card-title"><?= isset($stats->approved) ? $stats->approved : 0 ?></h4>
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
                                    <i class="fas fa-ban"></i>
                                </div>
                            </div>
                            <div class="col col-stats ml-3 ml-sm-0">
                                <div class="numbers">
                                    <p class="card-category">Blocked</p>
                                    <h4 class="card-title"><?= isset($stats->blocked) ? $stats->blocked : 0 ?></h4>
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
                                <div class="icon-big text-center icon-primary bubble-shadow-small">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                            <div class="col col-stats ml-3 ml-sm-0">
                                <div class="numbers">
                                    <p class="card-category">Total Users</p>
                                    <h4 class="card-title"><?= isset($stats->total) ? $stats->total : 0 ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Registrations -->
        <?php if (!empty($pending_users)): ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h4 class="card-title">
                                <i class="fas fa-clock text-warning mr-2"></i>
                                Pending Registrations
                                <span class="badge badge-warning ml-2"><?= count($pending_users) ?></span>
                            </h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Photo</th>
                                        <th>Name</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Position</th>
                                        <th>Registered</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pending_users as $user): ?>
                                    <tr>
                                        <td>
                                            <?php if ($user->profile_image || $user->personnel_profile_image): ?>
                                                <img src="<?= base_url('assets/uploads/profile_images/' . ($user->profile_image ?: $user->personnel_profile_image)) ?>" 
                                                     alt="Profile" class="avatar-img rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="avatar-sm">
                                                    <span class="avatar-title rounded-circle bg-secondary">
                                                        <?= strtoupper(substr($user->firstname, 0, 1)) ?>
                                                    </span>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?= htmlspecialchars($user->lastname . ', ' . $user->firstname . ' ' . $user->middlename) ?></strong>
                                        </td>
                                        <td><?= htmlspecialchars($user->username) ?></td>
                                        <td><?= htmlspecialchars($user->email) ?></td>
                                        <td><?= htmlspecialchars($user->position) ?></td>
                                        <td><?= date('M d, Y', strtotime($user->created_at)) ?></td>
                                        <td class="text-center">
                                            <button class="btn btn-success btn-sm" onclick="approveUser(<?= $user->id ?>)" title="Approve">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button class="btn btn-danger btn-sm" onclick="disapproveUser(<?= $user->id ?>)" title="Disapprove">
                                                <i class="fas fa-times"></i>
                                            </button>
                                            <a href="<?= site_url('usermanagement/view/' . $user->id) ?>" class="btn btn-info btn-sm" title="View Details">
                                                <i class="fas fa-eye"></i>
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

        <!-- Approved Users -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h4 class="card-title">
                                <i class="fas fa-users text-success mr-2"></i>
                                Approved Users
                            </h4>
                            <a href="<?= site_url('usermanagement/add') ?>" class="btn btn-primary btn-round ml-auto">
                                <i class="fa fa-plus"></i> Add User
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="approvedUsersTable">
                                <thead>
                                    <tr>
                                        <th>Photo</th>
                                        <th>Name</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Position</th>
                                        <th>Last Login</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($approved_users)): ?>
                                        <?php foreach ($approved_users as $user): ?>
                                        <tr>
                                            <td>
                                                <?php if ($user->profile_image || $user->personnel_profile_image): ?>
                                                    <img src="<?= base_url('assets/uploads/profile_images/' . ($user->profile_image ?: $user->personnel_profile_image)) ?>" 
                                                         alt="Profile" class="avatar-img rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                                <?php else: ?>
                                                    <div class="avatar-sm">
                                                        <span class="avatar-title rounded-circle bg-primary">
                                                            <?= strtoupper(substr($user->firstname, 0, 1)) ?>
                                                        </span>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?= htmlspecialchars($user->lastname . ', ' . $user->firstname) ?></strong>
                                            </td>
                                            <td><?= htmlspecialchars($user->username) ?></td>
                                            <td><?= htmlspecialchars($user->email) ?></td>
                                            <td><?= htmlspecialchars($user->position) ?></td>
                                            <td>
                                                <?= $user->last_login ? date('M d, Y H:i', strtotime($user->last_login)) : 'Never' ?>
                                            </td>
                                            <td class="text-center">
                                                <a href="<?= site_url('usermanagement/view/' . $user->id) ?>" class="btn btn-info btn-sm" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="<?= site_url('usermanagement/edit/' . $user->id) ?>" class="btn btn-warning btn-sm" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button class="btn btn-danger btn-sm" onclick="blockUser(<?= $user->id ?>)" title="Block">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center">No approved users found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Blocked Users -->
        <?php if (!empty($blocked_users)): ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="fas fa-ban text-danger mr-2"></i>
                            Blocked Users
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Reason</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($blocked_users as $user): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($user->lastname . ', ' . $user->firstname) ?></td>
                                        <td><?= htmlspecialchars($user->username) ?></td>
                                        <td><?= htmlspecialchars($user->email) ?></td>
                                        <td><?= htmlspecialchars($user->admin_notes ?: 'No reason provided') ?></td>
                                        <td class="text-center">
                                            <button class="btn btn-success btn-sm" onclick="unblockUser(<?= $user->id ?>)" title="Unblock">
                                                <i class="fas fa-unlock"></i> Unblock
                                            </button>
                                            <button class="btn btn-danger btn-sm" onclick="deleteUser(<?= $user->id ?>)" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
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
    </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Approve User Registration</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form id="approveForm" method="POST">
                <div class="modal-body">
                    <p>Are you sure you want to approve this user registration?</p>
                    <div class="form-group">
                        <label>Notes (Optional)</label>
                        <textarea class="form-control" name="notes" rows="3" placeholder="Add any notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Approve</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Disapprove Modal -->
<div class="modal fade" id="disapproveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Disapprove User Registration</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form id="disapproveForm" method="POST">
                <div class="modal-body">
                    <p>Are you sure you want to disapprove this user registration?</p>
                    <div class="form-group">
                        <label>Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="notes" rows="3" placeholder="Please provide a reason..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Disapprove</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Block Modal -->
<div class="modal fade" id="blockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Block User Account</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form id="blockForm" method="POST">
                <div class="modal-body">
                    <p>Are you sure you want to block this user account?</p>
                    <div class="form-group">
                        <label>Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="notes" rows="3" placeholder="Please provide a reason..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Block User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Unblock Modal -->
<div class="modal fade" id="unblockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Unblock User Account</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form id="unblockForm" method="POST">
                <div class="modal-body">
                    <p>Are you sure you want to unblock this user account?</p>
                    <div class="form-group">
                        <label>Notes (Optional)</label>
                        <textarea class="form-control" name="notes" rows="3" placeholder="Add any notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Unblock User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Delete User Account</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to permanently delete this user account? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <a href="#" id="deleteConfirmBtn" class="btn btn-danger">Delete</a>
            </div>
        </div>
    </div>
</div>

<script>
function approveUser(id) {
    $('#approveForm').attr('action', '<?= site_url('usermanagement/approve/') ?>' + id);
    $('#approveModal').modal('show');
}

function disapproveUser(id) {
    $('#disapproveForm').attr('action', '<?= site_url('usermanagement/disapprove/') ?>' + id);
    $('#disapproveModal').modal('show');
}

function blockUser(id) {
    $('#blockForm').attr('action', '<?= site_url('usermanagement/block/') ?>' + id);
    $('#blockModal').modal('show');
}

function unblockUser(id) {
    $('#unblockForm').attr('action', '<?= site_url('usermanagement/unblock/') ?>' + id);
    $('#unblockModal').modal('show');
}

function deleteUser(id) {
    $('#deleteConfirmBtn').attr('href', '<?= site_url('usermanagement/delete/') ?>' + id);
    $('#deleteModal').modal('show');
}

$(document).ready(function() {
    $('#approvedUsersTable').DataTable({
        "pageLength": 10,
        "order": [[1, "asc"]]
    });
});
</script>
