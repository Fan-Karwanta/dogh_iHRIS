<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title : 'User Login' ?> - DTR System</title>
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
        .login-container {
            max-width: 450px;
            width: 100%;
            padding: 20px;
        }
        .login-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .logo-section {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo-section img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin-bottom: 15px;
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
        .form-group label {
            font-weight: 600;
            color: #333;
        }
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #31ce36;
            box-shadow: 0 0 0 3px rgba(49, 206, 54, 0.1);
        }
        .btn-login {
            background: linear-gradient(135deg, #31ce36 0%, #1b8e20 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            width: 100%;
            color: white;
            margin-top: 10px;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #28b62c 0%, #167a1a 100%);
            color: white;
        }
        .links {
            text-align: center;
            margin-top: 20px;
        }
        .links a {
            color: #31ce36;
            text-decoration: none;
            font-weight: 500;
        }
        .links a:hover {
            text-decoration: underline;
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            color: #666;
            text-decoration: none;
        }
        .back-link a:hover {
            color: #333;
        }
        .alert {
            border-radius: 10px;
        }
        .custom-checkbox .custom-control-label {
            cursor: pointer;
        }
        .password-toggle {
            position: relative;
        }
        .password-toggle .toggle-btn {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="logo-section">
                <img src="<?= base_url('assets/img/logo.png') ?>" alt="Logo" onerror="this.src='<?= base_url('assets/img/default-logo.png') ?>'">
                <h3>Employee Login</h3>
                <p>Sign in to view your DTR records</p>
            </div>
            
            <?php if (isset($message) && $message): ?>
                <div class="alert alert-<?= $this->session->flashdata('success') ? 'success' : 'danger' ?>" role="alert">
                    <?= $message ?>
                </div>
            <?php endif; ?>
            
            <?php if (validation_errors()): ?>
                <div class="alert alert-danger" role="alert">
                    <?= validation_errors() ?>
                </div>
            <?php endif; ?>
            
            <form action="<?= site_url('userauth/login') ?>" method="POST">
                <div class="form-group">
                    <label for="identity">Username or Email</label>
                    <input type="text" class="form-control" id="identity" name="identity" 
                           placeholder="Enter your username or email" required
                           value="<?= set_value('identity') ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-toggle">
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Enter your password" required>
                        <span class="toggle-btn" onclick="togglePassword()">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16" id="eye-icon">
                                <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                                <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                            </svg>
                        </span>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="remember" name="remember" value="1">
                        <label class="custom-control-label" for="remember">Remember me</label>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-login">Sign In</button>
            </form>
            
            <div class="links">
                <p>Don't have an account? <a href="<?= site_url('userauth/register') ?>">Sign Up</a></p>
                <p><a href="<?= site_url('userauth/check_status') ?>">Check Registration Status</a></p>
            </div>
            
            <div class="back-link">
                <a href="<?= site_url('userauth') ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
                    </svg>
                    Back to User Type Selection
                </a>
            </div>
        </div>
    </div>
    
    <script src="<?= base_url('assets/js/core/jquery.3.2.1.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/core/bootstrap.min.js') ?>"></script>
    <script>
        function togglePassword() {
            var passwordField = document.getElementById('password');
            var eyeIcon = document.getElementById('eye-icon');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.innerHTML = '<path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7.028 7.028 0 0 0-2.79.588l.77.771A5.944 5.944 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.134 13.134 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755-.165.165-.337.328-.517.486l.708.709z"/><path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829l.822.822zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829z"/><path d="M3.35 5.47c-.18.16-.353.322-.518.487A13.134 13.134 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7.029 7.029 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12-.708.708z"/>';
            } else {
                passwordField.type = 'password';
                eyeIcon.innerHTML = '<path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/><path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>';
            }
        }
    </script>
</body>
</html>
