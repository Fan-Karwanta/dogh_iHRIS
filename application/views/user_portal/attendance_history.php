<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title : 'Attendance History' ?> - DTR System</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/atlantis.min.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .user-sidebar { background: linear-gradient(135deg, #31ce36 0%, #1b8e20 100%); min-height: 100vh; width: 250px; position: fixed; left: 0; top: 0; z-index: 1000; }
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
                <li class="nav-item"><a class="nav-link active" href="<?= site_url('user/attendance_history') ?>"><i class="fas fa-history"></i> Attendance History</a></li>
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
            <h4 class="page-title"><?= isset($title) ? $title : 'Attendance History' ?></h4>
        </div>

        <div class="user-main">
            <div class="card-custom">
                <div class="card-header">
                    <h5><i class="fas fa-history mr-2"></i>Complete Attendance History</h5>
                </div>
                <div class="card-body">
                    <?php if (isset($attendance_history) && !empty($attendance_history)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover" id="historyTable">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Day</th>
                                        <th class="text-center">AM In</th>
                                        <th class="text-center">AM Out</th>
                                        <th class="text-center">PM In</th>
                                        <th class="text-center">PM Out</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($attendance_history as $record): ?>
                                        <?php
                                        $day_of_week = date('w', strtotime($record->date));
                                        $is_weekend = ($day_of_week == 0 || $day_of_week == 6);
                                        $has_all = $record->am_in && $record->am_out && $record->pm_in && $record->pm_out;
                                        ?>
                                        <tr class="<?= $is_weekend ? 'table-secondary' : '' ?>">
                                            <td><?= date('M d, Y', strtotime($record->date)) ?></td>
                                            <td><?= date('l', strtotime($record->date)) ?></td>
                                            <td class="text-center">
                                                <?= $record->am_in ? date('h:i A', strtotime($record->am_in)) : '<span class="text-danger">-</span>' ?>
                                            </td>
                                            <td class="text-center">
                                                <?= $record->am_out ? date('h:i A', strtotime($record->am_out)) : '<span class="text-danger">-</span>' ?>
                                            </td>
                                            <td class="text-center">
                                                <?= $record->pm_in ? date('h:i A', strtotime($record->pm_in)) : '<span class="text-danger">-</span>' ?>
                                            </td>
                                            <td class="text-center">
                                                <?= $record->pm_out ? date('h:i A', strtotime($record->pm_out)) : '<span class="text-danger">-</span>' ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($is_weekend): ?>
                                                    <span class="badge badge-secondary">Weekend</span>
                                                <?php elseif ($has_all): ?>
                                                    <span class="badge badge-success">Complete</span>
                                                <?php else: ?>
                                                    <span class="badge badge-warning">Incomplete</span>
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
                            <h5 class="mt-3 text-muted">No attendance history found</h5>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= base_url('assets/js/core/jquery.3.2.1.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/core/bootstrap.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/plugin/datatables/datatables.min.js') ?>"></script>
    <script>
        $(document).ready(function() {
            $('#historyTable').DataTable({
                "order": [[0, "desc"]],
                "pageLength": 25
            });
        });
    </script>
</body>
</html>
