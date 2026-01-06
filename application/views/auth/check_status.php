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
    <title><?= isset($title) ? $title : 'Check Registration Status' ?> - <?= $sys->system_name ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/atlantis.min.css') ?>">
    <style>
        body {
            background: linear-gradient(135deg, #31ce36 0%, #1b8e20 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .status-container {
            max-width: 500px;
            width: 100%;
            padding: 20px;
        }
        .status-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .logo-section {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo-section h3 {
            color: #333;
            font-weight: 700;
            margin-bottom: 5px;
        }
        .logo-section p {
            color: #666;
            font-size: 14px;
        }
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
        }
        .form-control:focus {
            border-color: #31ce36;
            box-shadow: 0 0 0 3px rgba(49, 206, 54, 0.1);
        }
        .btn-check {
            background: linear-gradient(135deg, #31ce36 0%, #1b8e20 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            width: 100%;
            color: white;
            margin-top: 10px;
        }
        .status-result {
            text-align: center;
            padding: 30px;
            border-radius: 15px;
            margin-top: 20px;
        }
        .status-result.pending {
            background: #fff3cd;
            border: 2px solid #ffc107;
        }
        .status-result.approved {
            background: #d4edda;
            border: 2px solid #28a745;
        }
        .status-result.disapproved {
            background: #f8d7da;
            border: 2px solid #dc3545;
        }
        .status-result.blocked {
            background: #f8d7da;
            border: 2px solid #dc3545;
        }
        .status-result .icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
        .status-result.pending .icon { color: #ffc107; }
        .status-result.approved .icon { color: #28a745; }
        .status-result.disapproved .icon, .status-result.blocked .icon { color: #dc3545; }
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            color: #31ce36;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="status-container">
        <div class="status-card">
            <div class="logo-section">
                <?php if (!empty($sys->system_logo)) : ?>
                    <img src="<?= base_url('assets/uploads/' . $sys->system_logo) ?>" alt="<?= $sys->system_name ?>" style="width: 80px; height: 80px; border-radius: 50%; margin-bottom: 15px;">
                <?php else : ?>
                    <img src="<?= base_url('assets/img/logo.png') ?>" alt="Logo" style="width: 80px; height: 80px; border-radius: 50%; margin-bottom: 15px;">
                <?php endif; ?>
                <h3>DOGH <?= $sys->system_acronym ?></h3>
                <p>Enter your email to check your account status</p>
            </div>
            
            <form action="<?= site_url('userauth/check_status') ?>" method="POST">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" 
                           placeholder="Enter your registered email" required
                           value="<?= set_value('email') ?>">
                </div>
                <button type="submit" class="btn btn-check">Check Status</button>
            </form>
            
            <?php if (isset($user_status)): ?>
                <div class="status-result <?= $user_status ?>">
                    <?php if ($user_status === 'pending'): ?>
                        <div class="icon">⏳</div>
                        <h4>Pending Approval</h4>
                        <p>Your registration is awaiting admin approval. Please check back later.</p>
                    <?php elseif ($user_status === 'approved'): ?>
                        <div class="icon">✓</div>
                        <h4>Approved</h4>
                        <p>Your account has been approved! You can now <a href="<?= site_url('userauth/login') ?>">login</a>.</p>
                    <?php elseif ($user_status === 'disapproved'): ?>
                        <div class="icon">✗</div>
                        <h4>Disapproved</h4>
                        <p>Your registration was not approved.</p>
                        <?php if (isset($admin_notes) && $admin_notes): ?>
                            <p><strong>Reason:</strong> <?= htmlspecialchars($admin_notes) ?></p>
                        <?php endif; ?>
                    <?php elseif ($user_status === 'blocked'): ?>
                        <div class="icon">🚫</div>
                        <h4>Blocked</h4>
                        <p>Your account has been blocked. Please contact the administrator.</p>
                        <?php if (isset($admin_notes) && $admin_notes): ?>
                            <p><strong>Reason:</strong> <?= htmlspecialchars($admin_notes) ?></p>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($not_found) && $not_found): ?>
                <div class="alert alert-warning mt-3">
                    No registration found with this email address.
                </div>
            <?php endif; ?>
            
            <div class="back-link">
                <a href="<?= site_url('userauth/login') ?>">← Back to Login</a>
            </div>
        </div>
    </div>
    
    <script src="<?= base_url('assets/js/core/jquery.3.2.1.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/core/bootstrap.min.js') ?>"></script>
</body>
</html>
