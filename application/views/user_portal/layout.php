<?php
// Use cached system settings
if (!isset($GLOBALS['_sys_cache'])) {
    $query = $this->db->query("SELECT * FROM systems WHERE id=1");
    $GLOBALS['_sys_cache'] = $query->row();
}
$sys = $GLOBALS['_sys_cache'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= isset($title) ? $title : 'User Portal' ?> - <?= $sys->system_name ?></title>
    
    <!-- DNS Prefetch -->
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Favicon - Use system logo from settings -->
    <?php if (!empty($sys->system_logo)): ?>
    <link rel="shortcut icon" href="<?= base_url('assets/uploads/' . $sys->system_logo) ?>">
    <link rel="icon" type="image/png" href="<?= base_url('assets/uploads/' . $sys->system_logo) ?>">
    <?php else: ?>
    <link rel="shortcut icon" href="<?= base_url() ?>favicon_folder/favicon.ico">
    <?php endif; ?>
    
    <!-- Critical CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/fonts.min.css') ?>">
    <style>
        :root {
            --primary-color: #31ce36;
            --primary-dark: #1b8e20;
        }
        /* Modern User Portal Theme - Consistent with Admin */
        * { box-sizing: border-box; }
        
        .user-sidebar {
            background: linear-gradient(180deg, #1a2035 0%, #252d47 100%);
            min-height: 100vh;
            width: 260px;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .user-sidebar .logo {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            background: rgba(0,0,0,0.1);
        }
        .user-sidebar .logo img {
            width: 50px;
            height: 50px;
            object-fit: contain;
            margin-bottom: 8px;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
        }
        .user-sidebar .logo h4 {
            color: white;
            font-size: 15px;
            margin: 0;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .user-sidebar .logo small {
            color: rgba(255,255,255,0.6);
            font-size: 11px;
        }
        .user-sidebar .nav-menu {
            padding: 15px 0;
        }
        .user-sidebar .nav-item {
            padding: 0;
            margin: 2px 12px;
        }
        .user-sidebar .nav-link {
            color: rgba(255,255,255,0.7);
            padding: 12px 15px;
            display: flex;
            align-items: center;
            transition: all 0.2s ease;
            border-radius: 8px;
            font-size: 14px;
        }
        .user-sidebar .nav-link:hover {
            color: white;
            background: rgba(255,255,255,0.08);
            transform: translateX(3px);
        }
        .user-sidebar .nav-link.active {
            color: white;
            background: linear-gradient(135deg, #31ce36 0%, #1b8e20 100%);
            box-shadow: 0 4px 15px rgba(49, 206, 54, 0.3);
        }
        .user-sidebar .nav-link i {
            width: 22px;
            margin-right: 12px;
            font-size: 16px;
        }
        .user-content {
            margin-left: 260px;
            min-height: 100vh;
            background: #f4f7fc;
            transition: margin-left 0.3s ease;
        }
        .user-topbar {
            background: white;
            padding: 15px 30px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.04);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .user-topbar .page-title {
            font-size: 22px;
            font-weight: 700;
            color: #1a2035;
            margin: 0;
        }
        .user-topbar .user-info {
            display: flex;
            align-items: center;
        }
        .user-topbar .user-info .avatar {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            margin-right: 10px;
            object-fit: cover;
            border: 2px solid #f0f0f0;
        }
        .user-topbar .dropdown-toggle::after {
            display: none;
        }
        .user-main {
            padding: 30px;
        }
        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.04);
            margin-bottom: 24px;
            border: 1px solid rgba(0,0,0,0.04);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        }
        .stat-card .icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: white;
        }
        .stat-card .icon.primary { background: linear-gradient(135deg, #1572e8 0%, #0d47a1 100%); }
        .stat-card .icon.success { background: linear-gradient(135deg, #31ce36 0%, #1b8e20 100%); }
        .stat-card .icon.warning { background: linear-gradient(135deg, #ffad46 0%, #f5a623 100%); }
        .stat-card .icon.danger { background: linear-gradient(135deg, #f25961 0%, #d32f2f 100%); }
        .stat-card .stat-value {
            font-size: 26px;
            font-weight: 700;
            color: #1a2035;
        }
        .stat-card .stat-label {
            color: #8898aa;
            font-size: 13px;
            font-weight: 500;
        }
        .card-custom {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.04);
            margin-bottom: 24px;
            border: 1px solid rgba(0,0,0,0.04);
            overflow: hidden;
        }
        .card-custom .card-header {
            background: transparent;
            border-bottom: 1px solid #f0f0f0;
            padding: 20px 24px;
        }
        .card-custom .card-header h5 {
            margin: 0;
            font-weight: 600;
            color: #1a2035;
            font-size: 16px;
        }
        .card-custom .card-body {
            padding: 24px;
        }
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: linear-gradient(135deg, #f25961 0%, #d32f2f 100%);
            color: white;
            font-size: 10px;
            padding: 2px 7px;
            border-radius: 10px;
            font-weight: 600;
        }
        
        /* Mobile Toggle Button */
        .mobile-toggle {
            display: none;
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1001;
            background: #1a2035;
            border: none;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            cursor: pointer;
        }
        
        /* Responsive */
        @media (max-width: 991px) {
            .mobile-toggle { display: flex; align-items: center; justify-content: center; }
            .user-sidebar { transform: translateX(-100%); }
            .user-sidebar.show { transform: translateX(0); }
            .user-content { margin-left: 0; }
            .user-topbar { padding-left: 70px; }
        }
        @media (max-width: 576px) {
            .user-main { padding: 20px 15px; }
            .stat-card { padding: 20px; }
        }
        
        /* Loading Animation */
        .page-loading {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: #f4f7fc;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: opacity 0.3s ease;
        }
        .page-loading.fade-out { opacity: 0; pointer-events: none; }
        .spinner {
            width: 40px;
            height: 40px;
            border: 3px solid #e0e0e0;
            border-top-color: #31ce36;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="user-sidebar">
        <div class="logo">
            <img src="<?= base_url('assets/img/logo.png') ?>" alt="<?= $sys->system_name ?>">
            <h4><?= $sys->system_acronym ?></h4>
            <small>Employee Portal</small>
        </div>
        
        <nav class="nav-menu">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?= $this->uri->segment(2) == 'dashboard' || $this->uri->segment(2) == '' ? 'active' : '' ?>" href="<?= site_url('user/dashboard') ?>">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $this->uri->segment(2) == 'dtr' ? 'active' : '' ?>" href="<?= site_url('user/dtr') ?>">
                        <i class="fas fa-clock"></i> My DTR Records
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $this->uri->segment(2) == 'attendance_history' ? 'active' : '' ?>" href="<?= site_url('user/attendance_history') ?>">
                        <i class="fas fa-history"></i> Attendance History
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $this->uri->segment(2) == 'profile' ? 'active' : '' ?>" href="<?= site_url('user/profile') ?>">
                        <i class="fas fa-user"></i> My Profile
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $this->uri->segment(2) == 'change_password' ? 'active' : '' ?>" href="<?= site_url('user/change_password') ?>">
                        <i class="fas fa-lock"></i> Change Password
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $this->uri->segment(2) == 'notifications' ? 'active' : '' ?>" href="<?= site_url('user/notifications') ?>">
                        <i class="fas fa-bell"></i> Notifications
                        <?php if (isset($unread_count) && $unread_count > 0): ?>
                            <span class="badge badge-danger ml-2"><?= $unread_count ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $this->uri->segment(2) == 'leave_applications' ? 'active' : '' ?>" href="<?= site_url('user/leave_applications') ?>">
                        <i class="fas fa-file-signature"></i> My Leave Applications
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $this->uri->segment(1) == 'personneldtredit' ? 'active' : '' ?>" href="<?= site_url('personneldtredit') ?>">
                        <i class="fas fa-edit"></i> Edit My DTR
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $this->uri->segment(1) == 'dtrapproval' ? 'active' : '' ?>" href="<?= site_url('dtrapproval') ?>">
                        <i class="fas fa-check-double"></i> DTR Approvals
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
                <!-- Notifications -->
                <div class="dropdown mr-3">
                    <a href="#" class="position-relative" data-toggle="dropdown">
                        <i class="fas fa-bell" style="font-size: 20px; color: #666;"></i>
                        <?php if (isset($unread_count) && $unread_count > 0): ?>
                            <span class="notification-badge"><?= $unread_count ?></span>
                        <?php endif; ?>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" style="width: 300px;">
                        <h6 class="dropdown-header">Notifications</h6>
                        <?php if (isset($notifications) && !empty($notifications)): ?>
                            <?php foreach ($notifications as $notif): ?>
                                <a class="dropdown-item <?= $notif->is_read ? '' : 'bg-light' ?>" href="<?= site_url('user/notifications') ?>">
                                    <small class="text-muted"><?= date('M d', strtotime($notif->created_at)) ?></small>
                                    <p class="mb-0" style="font-size: 13px;"><?= htmlspecialchars($notif->title) ?></p>
                                </a>
                            <?php endforeach; ?>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-center" href="<?= site_url('user/notifications') ?>">View All</a>
                        <?php else: ?>
                            <p class="dropdown-item text-muted mb-0">No notifications</p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- User Dropdown -->
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center" data-toggle="dropdown">
                        <?php if (isset($current_user) && ($current_user->profile_image || $current_user->personnel_profile_image)): ?>
                            <img src="<?= base_url('assets/uploads/profile_images/' . ($current_user->profile_image ?: $current_user->personnel_profile_image)) ?>" class="avatar" alt="Profile">
                        <?php else: ?>
                            <div class="avatar bg-success d-flex align-items-center justify-content-center text-white">
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
                        <a class="dropdown-item" href="<?= site_url('user/change_password') ?>">
                            <i class="fas fa-lock mr-2"></i> Change Password
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
            <?= isset($content) ? $content : '' ?>
        </div>
    </div>

    <!-- Mobile Toggle -->
    <button class="mobile-toggle" onclick="document.querySelector('.user-sidebar').classList.toggle('show')">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Core Scripts -->
    <script src="<?= base_url('assets/js/core/jquery.3.2.1.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/core/popper.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/core/bootstrap.min.js') ?>"></script>
    
    <!-- Deferred Chart.js loading -->
    <script>
    (function() {
        // Load Chart.js only when needed
        if (document.querySelector('canvas')) {
            var script = document.createElement('script');
            script.src = '<?= base_url('assets/js/plugin/chart.js/chart.min.js') ?>';
            script.async = true;
            script.onload = function() {
                document.dispatchEvent(new CustomEvent('chartJsLoaded'));
            };
            document.body.appendChild(script);
        }
        
        // Close sidebar on mobile when clicking outside
        document.addEventListener('click', function(e) {
            var sidebar = document.querySelector('.user-sidebar');
            var toggle = document.querySelector('.mobile-toggle');
            if (window.innerWidth <= 991 && sidebar.classList.contains('show')) {
                if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });
    })();
    </script>
</body>
</html>
