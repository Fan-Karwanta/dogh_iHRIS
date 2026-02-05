<?php
// Get system settings for dynamic logo and branding
$this->db->where('id', 1);
$sys = $this->db->get('systems')->row();
?>
<style>
    .admin-login-card {
        background: white;
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        max-width: 450px;
        width: 100%;
        margin: 0 auto;
    }
    .admin-logo-section {
        text-align: center;
        margin-bottom: 30px;
    }
    .admin-logo-section img {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        margin-bottom: 15px;
    }
    .admin-logo-section h3 {
        color: #333;
        font-weight: 700;
        margin-bottom: 5px;
    }
    .admin-logo-section p {
        color: #666;
        font-size: 14px;
    }
    .admin-form-group label {
        font-weight: 600;
        color: #333;
    }
    .admin-form-control {
        border-radius: 10px;
        padding: 12px 15px;
        border: 2px solid #e0e0e0;
        transition: all 0.3s ease;
        width: 100%;
    }
    .admin-form-control:focus {
        border-color: #1572e8;
        box-shadow: 0 0 0 3px rgba(21, 114, 232, 0.1);
        outline: none;
    }
    .btn-admin-login {
        background: linear-gradient(135deg, #1572e8 0%, #0d47a1 100%);
        border: none;
        border-radius: 10px;
        padding: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        width: 100%;
        color: white;
        margin-top: 10px;
        cursor: pointer;
    }
    .btn-admin-login:hover {
        background: linear-gradient(135deg, #1260cc 0%, #0a3d8f 100%);
        color: white;
    }
    .admin-links {
        text-align: center;
        margin-top: 20px;
    }
    .admin-links a {
        color: #1572e8;
        text-decoration: none;
        font-weight: 500;
    }
    .admin-links a:hover {
        text-decoration: underline;
    }
    .admin-back-link {
        text-align: center;
        margin-top: 20px;
    }
    .admin-back-link a {
        color: #666;
        text-decoration: none;
    }
    .admin-back-link a:hover {
        color: #333;
    }
    .admin-password-toggle {
        position: relative;
    }
    .admin-password-toggle .toggle-btn {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #999;
    }
</style>

<div class="admin-login-card">
    <div class="admin-logo-section">
        <?php if (!empty($sys->system_logo)) : ?>
            <img src="<?= base_url('assets/uploads/' . $sys->system_logo) ?>" alt="<?= $sys->system_name ?>">
        <?php else : ?>
            <img src="<?= base_url('assets/img/logo.png') ?>" alt="Logo">
        <?php endif; ?>
        <h3>DOGH <?= $sys->system_acronym ?></h3>
        <p>Administrator Portal</p>
    </div>
    
    <?php if ($message !== null) : ?>
        <div class="alert alert-danger" role="alert" style="border-radius: 10px;">
            <?= $message ?>
        </div>
    <?php endif ?>
    
    <form action="<?= site_url('auth/login') ?>" method="POST">
        <div class="form-group admin-form-group">
            <label for="identity">Username</label>
            <input type="text" class="admin-form-control" id="identity" name="identity" 
                   placeholder="Enter your username" required>
        </div>
        
        <div class="form-group admin-form-group">
            <label for="password">Password</label>
            <div class="admin-password-toggle">
                <input type="password" class="admin-form-control" id="password" name="password" 
                       placeholder="Enter your password" required>
                <span class="toggle-btn" onclick="toggleAdminPassword()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16" id="admin-eye-icon">
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
        
        <button type="submit" class="btn-admin-login">Sign In</button>
    </form>
    
    <div class="admin-back-link">
        <a href="<?= site_url('userauth') ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
            </svg>
            Back to User Type Selection
        </a>
    </div>
    
    <div class="admin-links" style="margin-top: 15px;">
        <span class="text-muted">Powered by: </span>
        <a href="https://web.facebook.com/p/Davao-Occidental-General-Hospital-100089814696152/?_rdc=1&_rdr#" target="_blank">DOGH</a>
    </div>
</div>

<script>
function toggleAdminPassword() {
    var passwordField = document.getElementById('password');
    var eyeIcon = document.getElementById('admin-eye-icon');
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        eyeIcon.innerHTML = '<path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7.028 7.028 0 0 0-2.79.588l.77.771A5.944 5.944 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.134 13.134 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755-.165.165-.337.328-.517.486l.708.709z"/><path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829l.822.822zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829z"/><path d="M3.35 5.47c-.18.16-.353.322-.518.487A13.134 13.134 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7.029 7.029 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12-.708.708z"/>';
    } else {
        passwordField.type = 'password';
        eyeIcon.innerHTML = '<path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/><path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>';
    }
}
</script>