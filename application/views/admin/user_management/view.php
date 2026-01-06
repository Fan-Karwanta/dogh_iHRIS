<div class="content">
    <div class="page-inner">
        <div class="page-header">
            <h4 class="page-title">User Details</h4>
            <ul class="breadcrumbs">
                <li class="nav-home">
                    <a href="<?= site_url('dashboard') ?>"><i class="flaticon-home"></i></a>
                </li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="<?= site_url('usermanagement') ?>">User Management</a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="#">View User</a></li>
            </ul>
        </div>

        <?php if (isset($message) && $message): ?>
            <div class="alert alert-<?= isset($success) && $success ? 'success' : 'danger' ?> alert-dismissible fade show">
                <?= $message ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-4">
                <div class="card card-profile">
                    <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <div class="profile-picture">
                            <div class="avatar avatar-xl">
                                <?php if ($user->profile_image || $user->personnel_profile_image): ?>
                                    <img src="<?= base_url('assets/uploads/profile_images/' . ($user->profile_image ?: $user->personnel_profile_image)) ?>" 
                                         alt="Profile" class="avatar-img rounded-circle">
                                <?php else: ?>
                                    <span class="avatar-title rounded-circle bg-white text-primary" style="font-size: 40px;">
                                        <?= strtoupper(substr($user->firstname, 0, 1)) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="user-profile text-center">
                            <div class="name"><?= htmlspecialchars($user->firstname . ' ' . $user->lastname) ?></div>
                            <div class="job"><?= htmlspecialchars($user->position) ?></div>
                            <div class="desc">@<?= htmlspecialchars($user->username) ?></div>
                            
                            <div class="mt-3">
                                <?php
                                $status_class = [
                                    'pending' => 'warning',
                                    'approved' => 'success',
                                    'disapproved' => 'danger',
                                    'blocked' => 'danger'
                                ];
                                ?>
                                <span class="badge badge-<?= $status_class[$user->status] ?? 'secondary' ?>" style="font-size: 14px; padding: 8px 15px;">
                                    <?= ucfirst($user->status) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row user-stats text-center">
                            <div class="col">
                                <div class="number"><?= $user->last_login ? date('M d', strtotime($user->last_login)) : '-' ?></div>
                                <div class="title">Last Login</div>
                            </div>
                            <div class="col">
                                <div class="number"><?= date('M d', strtotime($user->created_at)) ?></div>
                                <div class="title">Registered</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions Card -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Actions</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($user->status === 'pending'): ?>
                            <button class="btn btn-success btn-block mb-2" onclick="approveUser(<?= $user->id ?>)">
                                <i class="fas fa-check mr-2"></i>Approve
                            </button>
                            <button class="btn btn-danger btn-block mb-2" onclick="disapproveUser(<?= $user->id ?>)">
                                <i class="fas fa-times mr-2"></i>Disapprove
                            </button>
                        <?php elseif ($user->status === 'approved'): ?>
                            <a href="<?= site_url('usermanagement/edit/' . $user->id) ?>" class="btn btn-warning btn-block mb-2">
                                <i class="fas fa-edit mr-2"></i>Edit User
                            </a>
                            <button class="btn btn-danger btn-block mb-2" onclick="blockUser(<?= $user->id ?>)">
                                <i class="fas fa-ban mr-2"></i>Block User
                            </button>
                        <?php elseif ($user->status === 'blocked'): ?>
                            <button class="btn btn-success btn-block mb-2" onclick="unblockUser(<?= $user->id ?>)">
                                <i class="fas fa-unlock mr-2"></i>Unblock User
                            </button>
                        <?php endif; ?>
                        
                        <button class="btn btn-danger btn-block" onclick="deleteUser(<?= $user->id ?>)">
                            <i class="fas fa-trash mr-2"></i>Delete Account
                        </button>
                        
                        <a href="<?= site_url('usermanagement') ?>" class="btn btn-secondary btn-block mt-3">
                            <i class="fas fa-arrow-left mr-2"></i>Back to List
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <!-- Account Information -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title"><i class="fas fa-user-circle mr-2"></i>Account Information</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="text-muted">Username</label>
                                    <p class="form-control-static font-weight-bold"><?= htmlspecialchars($user->username) ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="text-muted">Email</label>
                                    <p class="form-control-static font-weight-bold"><?= htmlspecialchars($user->email) ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="text-muted">Account Status</label>
                                    <p class="form-control-static">
                                        <span class="badge badge-<?= $status_class[$user->status] ?? 'secondary' ?>">
                                            <?= ucfirst($user->status) ?>
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="text-muted">Registration Date</label>
                                    <p class="form-control-static font-weight-bold"><?= date('F d, Y h:i A', strtotime($user->created_at)) ?></p>
                                </div>
                            </div>
                        </div>
                        <?php if ($user->approved_at): ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="text-muted">Approved Date</label>
                                    <p class="form-control-static font-weight-bold"><?= date('F d, Y h:i A', strtotime($user->approved_at)) ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="text-muted">Last Login</label>
                                    <p class="form-control-static font-weight-bold">
                                        <?= $user->last_login ? date('F d, Y h:i A', strtotime($user->last_login)) : 'Never' ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if ($user->admin_notes): ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="text-muted">Admin Notes</label>
                                    <p class="form-control-static"><?= htmlspecialchars($user->admin_notes) ?></p>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Personnel Information -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title"><i class="fas fa-id-card mr-2"></i>Personnel Information</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="text-muted">First Name</label>
                                    <p class="form-control-static font-weight-bold"><?= htmlspecialchars($user->firstname) ?></p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="text-muted">Middle Name</label>
                                    <p class="form-control-static font-weight-bold"><?= htmlspecialchars($user->middlename ?: '-') ?></p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="text-muted">Last Name</label>
                                    <p class="form-control-static font-weight-bold"><?= htmlspecialchars($user->lastname) ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="text-muted">Position</label>
                                    <p class="form-control-static font-weight-bold"><?= htmlspecialchars($user->position ?: '-') ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="text-muted">Department/Role</label>
                                    <p class="form-control-static font-weight-bold"><?= htmlspecialchars($user->role ?: '-') ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="text-muted">Employment Type</label>
                                    <p class="form-control-static font-weight-bold"><?= htmlspecialchars($user->employment_type ?: '-') ?></p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="text-muted">Salary Grade</label>
                                    <p class="form-control-static font-weight-bold"><?= htmlspecialchars($user->salary_grade ?: '-') ?></p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="text-muted">Work Schedule</label>
                                    <p class="form-control-static font-weight-bold"><?= htmlspecialchars($user->schedule_type ?: '-') ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="text-muted">Biometric ID</label>
                                    <p class="form-control-static font-weight-bold"><?= htmlspecialchars($user->bio_id ?: 'Not assigned') ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="text-muted">Personnel Email</label>
                                    <p class="form-control-static font-weight-bold"><?= htmlspecialchars($user->personnel_email ?: '-') ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include modals from index page -->
<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="true" data-keyboard="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Approve User Registration</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">&times;</button>
            </div>
            <form id="approveForm" method="POST">
                <div class="modal-body">
                    <p>Are you sure you want to approve this user registration?</p>
                    <div class="form-group">
                        <label>Notes (Optional)</label>
                        <textarea class="form-control" name="notes" rows="3"></textarea>
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
<div class="modal fade" id="disapproveModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="true" data-keyboard="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
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
                        <textarea class="form-control" name="notes" rows="3" required></textarea>
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
<div class="modal fade" id="blockModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="true" data-keyboard="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
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
                        <textarea class="form-control" name="notes" rows="3" required></textarea>
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
<div class="modal fade" id="unblockModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="true" data-keyboard="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
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
                        <textarea class="form-control" name="notes" rows="3"></textarea>
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

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="true" data-keyboard="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
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
</script>
