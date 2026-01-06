<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title : 'My Dashboard' ?> - DTR System</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/atlantis.min.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        :root {
            --primary-color: #31ce36;
            --primary-dark: #1b8e20;
        }
        .user-sidebar {
            background: linear-gradient(135deg, #31ce36 0%, #1b8e20 100%);
            min-height: 100vh;
            width: 250px;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
        }
        .user-sidebar .logo {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .user-sidebar .logo img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin-bottom: 10px;
        }
        .user-sidebar .logo h4 {
            color: white;
            font-size: 16px;
            margin: 0;
        }
        .user-sidebar .nav-menu {
            padding: 20px 0;
        }
        .user-sidebar .nav-item {
            padding: 0;
        }
        .user-sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 25px;
            display: flex;
            align-items: center;
            transition: all 0.3s;
        }
        .user-sidebar .nav-link:hover,
        .user-sidebar .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.1);
        }
        .user-sidebar .nav-link i {
            width: 25px;
            margin-right: 10px;
        }
        .user-content {
            margin-left: 250px;
            min-height: 100vh;
            background: #f4f5f7;
        }
        .user-topbar {
            background: white;
            padding: 15px 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .user-topbar .page-title {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            margin: 0;
        }
        .user-topbar .user-info {
            display: flex;
            align-items: center;
        }
        .user-topbar .user-info .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
            object-fit: cover;
        }
        .user-main {
            padding: 25px;
        }
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            height: 100%;
        }
        .stat-card .icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }
        .stat-card .icon.primary { background: linear-gradient(135deg, #1572e8 0%, #0d47a1 100%); }
        .stat-card .icon.success { background: linear-gradient(135deg, #31ce36 0%, #1b8e20 100%); }
        .stat-card .icon.warning { background: linear-gradient(135deg, #ffad46 0%, #f5a623 100%); }
        .stat-card .icon.danger { background: linear-gradient(135deg, #f25961 0%, #d32f2f 100%); }
        .stat-card .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: #333;
        }
        .stat-card .stat-label {
            color: #999;
            font-size: 14px;
        }
        .card-custom {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        .card-custom .card-header {
            background: transparent;
            border-bottom: 1px solid #eee;
            padding: 20px 25px;
        }
        .card-custom .card-header h5 {
            margin: 0;
            font-weight: 600;
        }
        .card-custom .card-body {
            padding: 25px;
        }
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #f25961;
            color: white;
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 10px;
        }
        .welcome-card {
            background: linear-gradient(135deg, #31ce36 0%, #1b8e20 100%);
            color: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 25px;
        }
        .welcome-card h3 {
            margin-bottom: 10px;
        }
        .welcome-card p {
            opacity: 0.9;
            margin: 0;
        }
        .missing-clock-item {
            padding: 15px;
            border-left: 4px solid #f25961;
            background: #fff5f5;
            margin-bottom: 10px;
            border-radius: 0 8px 8px 0;
        }
        @media (max-width: 768px) {
            .user-sidebar {
                transform: translateX(-100%);
            }
            .user-content {
                margin-left: 0;
            }
        }
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
                <li class="nav-item">
                    <a class="nav-link active" href="<?= site_url('user/dashboard') ?>">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= site_url('user/dtr') ?>">
                        <i class="fas fa-clock"></i> My DTR Records
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= site_url('user/attendance_history') ?>">
                        <i class="fas fa-history"></i> Attendance History
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= site_url('user/profile') ?>">
                        <i class="fas fa-user"></i> My Profile
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= site_url('user/change_password') ?>">
                        <i class="fas fa-lock"></i> Change Password
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= site_url('user/notifications') ?>">
                        <i class="fas fa-bell"></i> Notifications
                        <?php if (isset($unread_count) && $unread_count > 0): ?>
                            <span class="badge badge-danger ml-2"><?= $unread_count ?></span>
                        <?php endif; ?>
                    </a>
                </li>
            </ul>
        </nav>
        
        <div style="position: absolute; bottom: 20px; left: 0; right: 0; padding: 0 20px;">
            <a href="<?= site_url('userauth/logout') ?>" class="btn btn-light btn-block">
                <i class="fas fa-sign-out-alt mr-2"></i> Logout
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="user-content">
        <!-- Topbar -->
        <div class="user-topbar">
            <h4 class="page-title"><?= isset($title) ? $title : 'Dashboard' ?></h4>
            
            <div class="user-info">
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center" data-toggle="dropdown">
                        <?php if (isset($current_user) && ($current_user->profile_image || $current_user->personnel_profile_image)): ?>
                            <img src="<?= base_url('assets/uploads/profile_images/' . ($current_user->profile_image ?: $current_user->personnel_profile_image)) ?>" class="avatar" alt="Profile">
                        <?php else: ?>
                            <div class="avatar bg-success d-flex align-items-center justify-content-center text-white" style="width:40px;height:40px;border-radius:50%;">
                                <?= isset($current_user) ? strtoupper(substr($current_user->firstname, 0, 1)) : 'U' ?>
                            </div>
                        <?php endif; ?>
                        <span class="ml-2" style="color: #333;">
                            <?= isset($current_user) ? htmlspecialchars($current_user->firstname) : 'User' ?>
                            <i class="fas fa-chevron-down ml-1" style="font-size: 10px;"></i>
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="<?= site_url('user/profile') ?>">
                            <i class="fas fa-user mr-2"></i> My Profile
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item text-danger" href="<?= site_url('userauth/logout') ?>">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <div class="user-main">
            <!-- Welcome Card -->
            <div class="welcome-card">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h3>Welcome back, <?= isset($current_user) ? htmlspecialchars($current_user->firstname) : 'User' ?>!</h3>
                        <p>Here's your DTR summary for <?= date('F Y') ?>. Keep up the good work!</p>
                    </div>
                    <div class="col-md-4 text-right">
                        <p class="mb-0" style="font-size: 14px;">Today is</p>
                        <h4 class="mb-0"><?= date('l, F d, Y') ?></h4>
                    </div>
                </div>
            </div>

            <!-- Month/Year Filter -->
            <div class="card-custom mb-4">
                <div class="card-body py-3">
                    <form method="GET" class="form-inline">
                        <label class="mr-2">Select Period:</label>
                        <select name="month" class="form-control mr-2">
                            <?php for ($m = 1; $m <= 12; $m++): ?>
                                <option value="<?= str_pad($m, 2, '0', STR_PAD_LEFT) ?>" <?= $selected_month == $m ? 'selected' : '' ?>>
                                    <?= date('F', mktime(0, 0, 0, $m, 1)) ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                        <select name="year" class="form-control mr-2">
                            <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                                <option value="<?= $y ?>" <?= $selected_year == $y ? 'selected' : '' ?>><?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter mr-1"></i> Filter
                        </button>
                    </form>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="icon success">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="ml-3">
                                <div class="stat-value"><?= isset($monthly_stats->present_days) ? $monthly_stats->present_days : 0 ?></div>
                                <div class="stat-label">Present Days</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="icon danger">
                                <i class="fas fa-calendar-times"></i>
                            </div>
                            <div class="ml-3">
                                <div class="stat-value"><?= isset($monthly_stats->absent_days) ? $monthly_stats->absent_days : 0 ?></div>
                                <div class="stat-label">Absent Days</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="icon warning">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="ml-3">
                                <div class="stat-value"><?= isset($monthly_stats->late_count) ? $monthly_stats->late_count : 0 ?></div>
                                <div class="stat-label">Late Arrivals</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="icon primary">
                                <i class="fas fa-hourglass-half"></i>
                            </div>
                            <div class="ml-3">
                                <div class="stat-value"><?= isset($monthly_stats->total_hours) ? number_format($monthly_stats->total_hours, 1) : 0 ?></div>
                                <div class="stat-label">Total Hours</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Missing Clock-ins -->
                <div class="col-md-6">
                    <div class="card-custom">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5><i class="fas fa-exclamation-triangle text-warning mr-2"></i>Missing Clock-ins</h5>
                            <span class="badge badge-warning"><?= isset($missing_clockins) ? count($missing_clockins) : 0 ?> records</span>
                        </div>
                        <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                            <?php if (isset($missing_clockins) && !empty($missing_clockins)): ?>
                                <?php foreach (array_slice($missing_clockins, 0, 10) as $missing): ?>
                                    <div class="missing-clock-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong><?= date('l, M d, Y', strtotime($missing['date'])) ?></strong>
                                                <div class="text-muted" style="font-size: 13px;">
                                                    Missing: <?= implode(', ', $missing['missing']) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                <?php if (count($missing_clockins) > 10): ?>
                                    <p class="text-center text-muted mt-3">
                                        <a href="<?= site_url('user/dtr') ?>">View all <?= count($missing_clockins) ?> records</a>
                                    </p>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-check-circle text-success" style="font-size: 48px;"></i>
                                    <p class="mt-3 text-muted">No missing clock-ins for this period!</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Recent Attendance -->
                <div class="col-md-6">
                    <div class="card-custom">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5><i class="fas fa-history text-primary mr-2"></i>Recent Attendance</h5>
                            <a href="<?= site_url('user/dtr') ?>" class="btn btn-sm btn-outline-primary">View All</a>
                        </div>
                        <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                            <?php if (isset($recent_attendance) && !empty($recent_attendance)): ?>
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>AM In</th>
                                            <th>AM Out</th>
                                            <th>PM In</th>
                                            <th>PM Out</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (array_slice($recent_attendance, 0, 10) as $record): ?>
                                            <tr>
                                                <td><?= date('M d', strtotime($record->date)) ?></td>
                                                <td><?= $record->am_in ? date('h:i A', strtotime($record->am_in)) : '-' ?></td>
                                                <td><?= $record->am_out ? date('h:i A', strtotime($record->am_out)) : '-' ?></td>
                                                <td><?= $record->pm_in ? date('h:i A', strtotime($record->pm_in)) : '-' ?></td>
                                                <td><?= $record->pm_out ? date('h:i A', strtotime($record->pm_out)) : '-' ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-calendar-alt text-muted" style="font-size: 48px;"></i>
                                    <p class="mt-3 text-muted">No attendance records found.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Summary -->
            <?php if (isset($performance) && $performance): ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="card-custom">
                        <div class="card-header">
                            <h5><i class="fas fa-chart-line text-success mr-2"></i>Performance Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 text-center">
                                    <div class="mb-2">
                                        <span style="font-size: 36px; font-weight: 700; color: #31ce36;">
                                            <?= isset($performance->attendance_rate) ? number_format($performance->attendance_rate, 1) : 0 ?>%
                                        </span>
                                    </div>
                                    <div class="text-muted">Attendance Rate</div>
                                </div>
                                <div class="col-md-3 text-center">
                                    <div class="mb-2">
                                        <span style="font-size: 36px; font-weight: 700; color: #1572e8;">
                                            <?= isset($performance->punctuality_rate) ? number_format($performance->punctuality_rate, 1) : 0 ?>%
                                        </span>
                                    </div>
                                    <div class="text-muted">Punctuality Rate</div>
                                </div>
                                <div class="col-md-3 text-center">
                                    <div class="mb-2">
                                        <span style="font-size: 36px; font-weight: 700; color: #ffad46;">
                                            <?= isset($performance->complete_dtr_rate) ? number_format($performance->complete_dtr_rate, 1) : 0 ?>%
                                        </span>
                                    </div>
                                    <div class="text-muted">Complete DTR Rate</div>
                                </div>
                                <div class="col-md-3 text-center">
                                    <div class="mb-2">
                                        <span style="font-size: 36px; font-weight: 700; color: #333;">
                                            <?= isset($yearly_stats->total_days_present) ? $yearly_stats->total_days_present : 0 ?>
                                        </span>
                                    </div>
                                    <div class="text-muted">Days Present (Year)</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Personnel Info Card -->
            <?php if (isset($personnel) && $personnel): ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="card-custom">
                        <div class="card-header">
                            <h5><i class="fas fa-id-card text-info mr-2"></i>My Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="text-muted">Full Name</label>
                                    <p class="font-weight-bold"><?= htmlspecialchars($personnel->firstname . ' ' . $personnel->middlename . ' ' . $personnel->lastname) ?></p>
                                </div>
                                <div class="col-md-3">
                                    <label class="text-muted">Position</label>
                                    <p class="font-weight-bold"><?= htmlspecialchars($personnel->position ?: '-') ?></p>
                                </div>
                                <div class="col-md-3">
                                    <label class="text-muted">Employment Type</label>
                                    <p class="font-weight-bold"><?= htmlspecialchars($personnel->employment_type ?: '-') ?></p>
                                </div>
                                <div class="col-md-3">
                                    <label class="text-muted">Work Schedule</label>
                                    <p class="font-weight-bold"><?= htmlspecialchars($personnel->schedule_type ?: '8:00 AM - 5:00 PM') ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="<?= base_url('assets/js/core/jquery.3.2.1.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/core/popper.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/core/bootstrap.min.js') ?>"></script>
</body>
</html>
