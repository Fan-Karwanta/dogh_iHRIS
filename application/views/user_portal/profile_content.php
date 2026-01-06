<?php if (isset($message) && $message): ?>
    <div class="alert alert-<?= isset($success) && $success ? 'success' : 'danger' ?> alert-dismissible fade show">
        <?= $message ?>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
<?php endif; ?>

<!-- Profile Header -->
<div class="profile-header">
    <?php if (isset($personnel) && $personnel->profile_image): ?>
        <img src="<?= base_url('assets/uploads/profile_images/' . $personnel->profile_image) ?>" class="avatar" alt="Profile">
    <?php else: ?>
        <div class="avatar-placeholder">
            <?= isset($personnel) ? strtoupper(substr($personnel->firstname, 0, 1)) : 'U' ?>
        </div>
    <?php endif; ?>
    <h3 class="mb-1"><?= isset($personnel) ? htmlspecialchars($personnel->firstname . ' ' . $personnel->lastname) : 'User' ?></h3>
    <p class="mb-0 opacity-90"><?= isset($personnel) ? htmlspecialchars($personnel->position) : '' ?></p>
</div>

<div class="row">
    <!-- Profile Information -->
    <div class="col-lg-6">
        <div class="card-custom">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-user mr-2 text-primary"></i>Personal Information</h5>
            </div>
            <div class="card-body">
                <?php if (isset($personnel) && $personnel): ?>
                <div class="info-list">
                    <div class="info-item">
                        <span class="info-label">First Name</span>
                        <span class="info-value"><?= htmlspecialchars($personnel->firstname) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Middle Name</span>
                        <span class="info-value"><?= htmlspecialchars($personnel->middlename ?: '-') ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Last Name</span>
                        <span class="info-value"><?= htmlspecialchars($personnel->lastname) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Email</span>
                        <span class="info-value"><?= htmlspecialchars($personnel->email ?: '-') ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Facebook</span>
                        <span class="info-value">
                            <?php if ($personnel->fb): ?>
                                <a href="<?= htmlspecialchars($personnel->fb) ?>" target="_blank" class="text-primary"><?= htmlspecialchars($personnel->fb) ?></a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Employment Information -->
    <div class="col-lg-6">
        <div class="card-custom">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-briefcase mr-2 text-success"></i>Employment Information</h5>
            </div>
            <div class="card-body">
                <?php if (isset($personnel) && $personnel): ?>
                <div class="info-list">
                    <div class="info-item">
                        <span class="info-label">Position</span>
                        <span class="info-value"><?= htmlspecialchars($personnel->position ?: '-') ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Department/Role</span>
                        <span class="info-value"><?= htmlspecialchars($personnel->role ?: '-') ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Employment Type</span>
                        <span class="info-value"><?= htmlspecialchars($personnel->employment_type ?: '-') ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Salary Grade</span>
                        <span class="info-value"><?= htmlspecialchars($personnel->salary_grade ?: '-') ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Work Schedule</span>
                        <span class="info-value"><?= htmlspecialchars($personnel->schedule_type ?: '8:00 AM - 5:00 PM') ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Biometric ID</span>
                        <span class="info-value"><?= htmlspecialchars($personnel->bio_id ?: 'Not Assigned') ?></span>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Form -->
<div class="card-custom">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-edit mr-2 text-warning"></i>Update Profile</h5>
    </div>
    <div class="card-body">
        <form action="<?= site_url('user/update_profile') ?>" method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">First Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="firstname" required 
                               value="<?= isset($personnel) ? htmlspecialchars($personnel->firstname) : '' ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Middle Name</label>
                        <input type="text" class="form-control" name="middlename" 
                               value="<?= isset($personnel) ? htmlspecialchars($personnel->middlename) : '' ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Last Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="lastname" required 
                               value="<?= isset($personnel) ? htmlspecialchars($personnel->lastname) : '' ?>">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Facebook URL</label>
                        <input type="url" class="form-control" name="fb" placeholder="https://facebook.com/..."
                               value="<?= isset($personnel) ? htmlspecialchars($personnel->fb) : '' ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Profile Image</label>
                        <input type="file" class="form-control-file" name="profile_image" accept="image/*">
                        <small class="text-muted">Max 2MB (JPG, PNG, GIF)</small>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save mr-2"></i>Save Changes
            </button>
        </form>
    </div>
</div>

<style>
    .profile-header {
        background: linear-gradient(135deg, #1a2035 0%, #252d47 100%);
        padding: 40px;
        border-radius: 16px;
        color: white;
        text-align: center;
        margin-bottom: 24px;
        box-shadow: 0 8px 25px rgba(26, 32, 53, 0.3);
    }
    .profile-header .avatar {
        width: 110px;
        height: 110px;
        border-radius: 50%;
        border: 4px solid rgba(255,255,255,0.2);
        object-fit: cover;
        margin-bottom: 15px;
    }
    .profile-header .avatar-placeholder {
        width: 110px;
        height: 110px;
        border-radius: 50%;
        border: 4px solid rgba(255,255,255,0.2);
        background: linear-gradient(135deg, #31ce36 0%, #1b8e20 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
        font-size: 42px;
        font-weight: 600;
    }
    .info-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    .info-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 12px;
        border-bottom: 1px solid #f0f0f0;
    }
    .info-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    .info-label {
        color: #8898aa;
        font-size: 13px;
    }
    .info-value {
        font-weight: 600;
        color: #1a2035;
        font-size: 14px;
        text-align: right;
    }
    .form-label {
        font-weight: 600;
        font-size: 13px;
        color: #1a2035;
        margin-bottom: 6px;
    }
    .form-control {
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        padding: 10px 14px;
    }
    .form-control:focus {
        border-color: #31ce36;
        box-shadow: 0 0 0 3px rgba(49, 206, 54, 0.1);
    }
</style>
