<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title : 'My DTR Records' ?> - DTR System</title>
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
        .user-sidebar .nav-menu { padding: 20px 0; }
        .user-sidebar .nav-item { padding: 0; }
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
        .user-sidebar .nav-link i { width: 25px; margin-right: 10px; }
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
        .user-main { padding: 25px; }
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
        .card-custom .card-body { padding: 25px; }
        .time-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 13px;
            font-weight: 500;
        }
        .time-badge.present { background: #d4edda; color: #155724; }
        .time-badge.missing { background: #f8d7da; color: #721c24; }
        .dtr-table th { background: #f8f9fa; font-weight: 600; }
        .dtr-table td { vertical-align: middle; }
        @media (max-width: 768px) {
            .user-sidebar { transform: translateX(-100%); }
            .user-content { margin-left: 0; }
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
                <li class="nav-item"><a class="nav-link" href="<?= site_url('user/dashboard') ?>"><i class="fas fa-home"></i> Dashboard</a></li>
                <li class="nav-item"><a class="nav-link active" href="<?= site_url('user/dtr') ?>"><i class="fas fa-clock"></i> My DTR Records</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= site_url('user/attendance_history') ?>"><i class="fas fa-history"></i> Attendance History</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= site_url('user/profile') ?>"><i class="fas fa-user"></i> My Profile</a></li>
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
            <h4 class="page-title"><?= isset($title) ? $title : 'My DTR Records' ?></h4>
        </div>

        <div class="user-main">
            <!-- Filter -->
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
                        <button type="submit" class="btn btn-primary"><i class="fas fa-filter mr-1"></i> Filter</button>
                    </form>
                </div>
            </div>

            <!-- DTR Table -->
            <div class="card-custom">
                <div class="card-header">
                    <h5><i class="fas fa-table mr-2"></i>DTR Records for <?= date('F Y', mktime(0, 0, 0, $selected_month, 1, $selected_year)) ?></h5>
                </div>
                <div class="card-body">
                    <?php if (isset($dtr_records) && !empty($dtr_records)): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered dtr-table">
                                <thead>
                                    <tr>
                                        <th class="text-center">Day</th>
                                        <th class="text-center">Date</th>
                                        <th class="text-center">AM In</th>
                                        <th class="text-center">AM Out</th>
                                        <th class="text-center">PM In</th>
                                        <th class="text-center">PM Out</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dtr_records as $record): ?>
                                        <?php
                                        $day_of_week = date('w', strtotime($record->date));
                                        $is_weekend = ($day_of_week == 0 || $day_of_week == 6);
                                        $has_missing = (!$record->am_in || !$record->am_out || !$record->pm_in || !$record->pm_out) && !$is_weekend;
                                        ?>
                                        <tr class="<?= $is_weekend ? 'table-secondary' : '' ?>">
                                            <td class="text-center"><?= date('D', strtotime($record->date)) ?></td>
                                            <td class="text-center"><?= date('M d, Y', strtotime($record->date)) ?></td>
                                            <td class="text-center">
                                                <?php if ($record->am_in): ?>
                                                    <span class="time-badge present"><?= date('h:i A', strtotime($record->am_in)) ?></span>
                                                <?php else: ?>
                                                    <span class="time-badge missing">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($record->am_out): ?>
                                                    <span class="time-badge present"><?= date('h:i A', strtotime($record->am_out)) ?></span>
                                                <?php else: ?>
                                                    <span class="time-badge missing">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($record->pm_in): ?>
                                                    <span class="time-badge present"><?= date('h:i A', strtotime($record->pm_in)) ?></span>
                                                <?php else: ?>
                                                    <span class="time-badge missing">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($record->pm_out): ?>
                                                    <span class="time-badge present"><?= date('h:i A', strtotime($record->pm_out)) ?></span>
                                                <?php else: ?>
                                                    <span class="time-badge missing">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($is_weekend): ?>
                                                    <span class="badge badge-secondary">Weekend</span>
                                                <?php elseif ($has_missing): ?>
                                                    <span class="badge badge-warning">Incomplete</span>
                                                <?php else: ?>
                                                    <span class="badge badge-success">Complete</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-alt text-muted" style="font-size: 64px;"></i>
                            <h5 class="mt-3 text-muted">No DTR records found for this period</h5>
                            <p class="text-muted">Your biometric ID may not be linked or no records exist for the selected month.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Personnel Info -->
            <?php if (isset($personnel) && $personnel): ?>
            <div class="card-custom">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <small class="text-muted">Name</small>
                            <p class="font-weight-bold mb-0"><?= htmlspecialchars($personnel->firstname . ' ' . $personnel->lastname) ?></p>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted">Biometric ID</small>
                            <p class="font-weight-bold mb-0"><?= $personnel->bio_id ?: 'Not Assigned' ?></p>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted">Schedule</small>
                            <p class="font-weight-bold mb-0"><?= $personnel->schedule_type ?: '8:00 AM - 5:00 PM' ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="<?= base_url('assets/js/core/jquery.3.2.1.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/core/bootstrap.min.js') ?>"></script>
</body>
</html>
