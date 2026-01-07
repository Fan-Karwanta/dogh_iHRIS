<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title : 'My Profile' ?> - DTR System</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/atlantis.min.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .user-sidebar {
            background: linear-gradient(135deg, #31ce36 0%, #1b8e20 100%);
            min-height: 100vh;
            width: 250px;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
        }
        .user-sidebar .logo { padding: 20px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .user-sidebar .logo img { width: 60px; height: 60px; border-radius: 50%; margin-bottom: 10px; }
        .user-sidebar .logo h4 { color: white; font-size: 16px; margin: 0; }
        .user-sidebar .nav-menu { padding: 20px 0; }
        .user-sidebar .nav-link { color: rgba(255,255,255,0.8); padding: 12px 25px; display: flex; align-items: center; transition: all 0.3s; }
        .user-sidebar .nav-link:hover, .user-sidebar .nav-link.active { color: white; background: rgba(255,255,255,0.1); }
        .user-sidebar .nav-link i { width: 25px; margin-right: 10px; }
        .user-content { margin-left: 250px; min-height: 100vh; background: #f4f5f7; }
        .user-topbar { background: white; padding: 15px 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .user-topbar .page-title { font-size: 20px; font-weight: 600; color: #333; margin: 0; }
        .user-main { padding: 25px; }
        .card-custom { background: white; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); margin-bottom: 20px; }
        .card-custom .card-header { background: transparent; border-bottom: 1px solid #eee; padding: 20px 25px; }
        .card-custom .card-body { padding: 25px; }
        .profile-header { 
            background-image: url('<?= base_url('assets/img/dogh_background.jpg') ?>');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            padding: 40px; 
            border-radius: 15px; 
            color: white; 
            text-align: center; 
            margin-bottom: 25px; 
            position: relative;
        }
        .profile-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.4);
            border-radius: 15px;
        }
        .profile-header * {
            position: relative;
            z-index: 1;
        }
        .profile-header .avatar { width: 120px; height: 120px; border-radius: 50%; border: 4px solid white; object-fit: cover; margin-bottom: 15px; }
        .profile-header .avatar-placeholder { width: 120px; height: 120px; border-radius: 50%; border: 4px solid white; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; font-size: 48px; }
        @media (max-width: 768px) { .user-sidebar { transform: translateX(-100%); } .user-content { margin-left: 0; } }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="user-sidebar">
        <div class="logo">
            <img src="<?= base_url('assets/img/logo.png') ?>" alt="Logo" onerror="this.src='<?= base_url('assets/img/default-logo.png') ?>'">
            <h4>DTR Portal</h4>
        </div>
        <nav class="nav-menu">
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link" href="<?= site_url('user/dashboard') ?>"><i class="fas fa-home"></i> Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= site_url('user/dtr') ?>"><i class="fas fa-clock"></i> My DTR Records</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= site_url('user/attendance_history') ?>"><i class="fas fa-history"></i> Attendance History</a></li>
                <li class="nav-item"><a class="nav-link active" href="<?= site_url('user/profile') ?>"><i class="fas fa-user"></i> My Profile</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= site_url('user/change_password') ?>"><i class="fas fa-lock"></i> Change Password</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= site_url('user/notifications') ?>"><i class="fas fa-bell"></i> Notifications</a></li>
            </ul>
        </nav>
        <div style="position: absolute; bottom: 20px; left: 0; right: 0; padding: 0 20px;">
            <a href="<?= site_url('userauth/logout') ?>" class="btn btn-light btn-block"><i class="fas fa-sign-out-alt mr-2"></i> Logout</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="user-content">
        <div class="user-topbar">
            <h4 class="page-title"><?= isset($title) ? $title : 'My Profile' ?></h4>
        </div>

        <div class="user-main">
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
                <h3><?= isset($personnel) ? htmlspecialchars($personnel->firstname . ' ' . $personnel->lastname) : 'User' ?></h3>
                <p class="mb-0"><?= isset($personnel) ? htmlspecialchars($personnel->position) : '' ?></p>
            </div>

            <div class="row">
                <!-- Profile Information -->
                <div class="col-md-6">
                    <div class="card-custom">
                        <div class="card-header">
                            <h5><i class="fas fa-user mr-2"></i>Personal Information</h5>
                        </div>
                        <div class="card-body">
                            <?php if (isset($personnel) && $personnel): ?>
                            <table class="table table-borderless">
                                <tr>
                                    <td class="text-muted" width="40%">First Name</td>
                                    <td class="font-weight-bold"><?= htmlspecialchars($personnel->firstname) ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Middle Name</td>
                                    <td class="font-weight-bold"><?= htmlspecialchars($personnel->middlename ?: '-') ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Last Name</td>
                                    <td class="font-weight-bold"><?= htmlspecialchars($personnel->lastname) ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Email</td>
                                    <td class="font-weight-bold"><?= htmlspecialchars($personnel->email ?: '-') ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Facebook</td>
                                    <td>
                                        <?php if ($personnel->fb): ?>
                                            <a href="<?= htmlspecialchars($personnel->fb) ?>" target="_blank"><?= htmlspecialchars($personnel->fb) ?></a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Employment Information -->
                <div class="col-md-6">
                    <div class="card-custom">
                        <div class="card-header">
                            <h5><i class="fas fa-briefcase mr-2"></i>Employment Information</h5>
                        </div>
                        <div class="card-body">
                            <?php if (isset($personnel) && $personnel): ?>
                            <table class="table table-borderless">
                                <tr>
                                    <td class="text-muted" width="40%">Position</td>
                                    <td class="font-weight-bold"><?= htmlspecialchars($personnel->position ?: '-') ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Department/Role</td>
                                    <td class="font-weight-bold"><?= htmlspecialchars($personnel->role ?: '-') ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Employment Type</td>
                                    <td class="font-weight-bold"><?= htmlspecialchars($personnel->employment_type ?: '-') ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Salary Grade</td>
                                    <td class="font-weight-bold"><?= htmlspecialchars($personnel->salary_grade ?: '-') ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Work Schedule</td>
                                    <td class="font-weight-bold"><?= htmlspecialchars($personnel->schedule_type ?: '8:00 AM - 5:00 PM') ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Biometric ID</td>
                                    <td class="font-weight-bold"><?= htmlspecialchars($personnel->bio_id ?: 'Not Assigned') ?></td>
                                </tr>
                            </table>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Profile Form -->
            <div class="card-custom">
                <div class="card-header">
                    <h5><i class="fas fa-edit mr-2"></i>Update Profile</h5>
                </div>
                <div class="card-body">
                    <form action="<?= site_url('user/update_profile') ?>" method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>First Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="firstname" required 
                                           value="<?= isset($personnel) ? htmlspecialchars($personnel->firstname) : '' ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Middle Name</label>
                                    <input type="text" class="form-control" name="middlename" 
                                           value="<?= isset($personnel) ? htmlspecialchars($personnel->middlename) : '' ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Last Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="lastname" required 
                                           value="<?= isset($personnel) ? htmlspecialchars($personnel->lastname) : '' ?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Facebook URL</label>
                                    <input type="url" class="form-control" name="fb" 
                                           value="<?= isset($personnel) ? htmlspecialchars($personnel->fb) : '' ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Profile Image</label>
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
        </div>
    </div>

    <script src="<?= base_url('assets/js/core/jquery.3.2.1.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/core/bootstrap.min.js') ?>"></script>
</body>
</html>
