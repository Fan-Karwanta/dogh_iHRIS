<?php
// Get system settings for dynamic logo and branding
$this->db->where('id', 1);
$sys = $this->db->get('systems')->row();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title : 'Select User Type' ?> - <?= $sys->system_name ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/atlantis.min.css') ?>">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .selection-container {
            max-width: 800px;
            width: 100%;
            padding: 20px;
        }
        .selection-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .logo-section {
            text-align: center;
            margin-bottom: 40px;
        }
        .logo-section img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin-bottom: 20px;
        }
        .logo-section h2 {
            color: #333;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .logo-section p {
            color: #666;
            font-size: 16px;
        }
        .user-type-cards {
            display: flex;
            gap: 30px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .user-type-card {
            flex: 1;
            min-width: 250px;
            max-width: 300px;
            background: #f8f9fa;
            border-radius: 15px;
            padding: 40px 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 3px solid transparent;
        }
        .user-type-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }
        .user-type-card.admin:hover {
            border-color: #1572e8;
            background: linear-gradient(135deg, #e8f4fd 0%, #d1e8fc 100%);
        }
        .user-type-card.employee:hover {
            border-color: #31ce36;
            background: linear-gradient(135deg, #e8fde9 0%, #d1fcd3 100%);
        }
        .user-type-card .icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 36px;
        }
        .user-type-card.admin .icon {
            background: linear-gradient(135deg, #1572e8 0%, #0d47a1 100%);
            color: white;
        }
        .user-type-card.employee .icon {
            background: linear-gradient(135deg, #31ce36 0%, #1b8e20 100%);
            color: white;
        }
        .user-type-card h4 {
            font-weight: 700;
            margin-bottom: 10px;
            color: #333;
        }
        .user-type-card p {
            color: #666;
            font-size: 14px;
            margin-bottom: 20px;
        }
        .user-type-card .btn {
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .user-type-card.admin .btn {
            background: linear-gradient(135deg, #1572e8 0%, #0d47a1 100%);
            border: none;
            color: white;
        }
        .user-type-card.employee .btn {
            background: linear-gradient(135deg, #31ce36 0%, #1b8e20 100%);
            border: none;
            color: white;
        }
        .footer-text {
            text-align: center;
            margin-top: 30px;
            color: #999;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="selection-container">
        <div class="selection-card">
            <div class="logo-section">
                <?php if (!empty($sys->system_logo)) : ?>
                    <img src="<?= base_url('assets/uploads/' . $sys->system_logo) ?>" alt="<?= $sys->system_name ?>">
                <?php else : ?>
                    <img src="<?= base_url('assets/img/logo.png') ?>" alt="Logo">
                <?php endif; ?>
                <h2>DOGH <?= $sys->system_acronym ?></h2>
                <p>Please select your user type to continue</p>
            </div>
            
            <div class="user-type-cards">
                <div class="user-type-card admin" onclick="window.location.href='<?= site_url('auth/login') ?>'">
                    <div class="icon">
                        <i class="fas fa-user-shield"></i>
                        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/>
                            <path fill-rule="evenodd" d="M13.5 5a.5.5 0 0 1 .5.5V7h1.5a.5.5 0 0 1 0 1H14v1.5a.5.5 0 0 1-1 0V8h-1.5a.5.5 0 0 1 0-1H13V5.5a.5.5 0 0 1 .5-.5z"/>
                        </svg>
                    </div>
                    <h4>Administrator</h4>
                    <p>Access the admin panel to manage personnel, DTR records, and system settings</p>
                    <a href="<?= site_url('auth/login') ?>" class="btn">Login as Admin</a>
                </div>
                
                <div class="user-type-card employee" onclick="window.location.href='<?= site_url('userauth/login') ?>'">
                    <div class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                            <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
                        </svg>
                    </div>
                    <h4>Employee</h4>
                    <p>View your personal DTR records, attendance history, and account information</p>
                    <a href="<?= site_url('userauth/login') ?>" class="btn">Login as Employee</a>
                </div>
            </div>
            
            <div class="footer-text">
                <p>&copy; <?= date('Y') ?> Davao Occidental General Hospital - <?= $sys->system_acronym ?></p>
            </div>
        </div>
    </div>
    
    <script src="<?= base_url('assets/js/core/jquery.3.2.1.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/core/bootstrap.min.js') ?>"></script>
</body>
</html>
