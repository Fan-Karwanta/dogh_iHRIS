<?php if (isset($message) && $message): ?>
    <div class="alert alert-<?= isset($success) && $success ? 'success' : 'danger' ?> alert-dismissible fade show">
        <?= $message ?>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
<?php endif; ?>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card-custom">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-lock mr-2 text-warning"></i>Change Your Password</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="password-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <p class="text-muted mt-3 mb-0">Keep your account secure by using a strong password</p>
                </div>

                <form action="<?= site_url('user/change_password') ?>" method="POST">
                    <div class="form-group">
                        <label class="form-label">Current Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control" name="current_password" id="current_password" required>
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="current_password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">New Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control" name="new_password" id="new_password" required minlength="8">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="new_password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <small class="text-muted">Minimum 8 characters</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control" name="confirm_password" id="confirm_password" required minlength="8">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="confirm_password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block mt-4">
                        <i class="fas fa-key mr-2"></i>Update Password
                    </button>
                </form>
            </div>
        </div>

        <!-- Password Tips -->
        <div class="card-custom">
            <div class="card-body">
                <h6 class="mb-3"><i class="fas fa-lightbulb text-warning mr-2"></i>Password Tips</h6>
                <ul class="password-tips mb-0">
                    <li>Use at least 8 characters</li>
                    <li>Include uppercase and lowercase letters</li>
                    <li>Add numbers and special characters</li>
                    <li>Avoid using personal information</li>
                    <li>Don't reuse passwords from other accounts</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
    .password-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, #ffad46 0%, #f5a623 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        font-size: 32px;
        color: white;
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
    .input-group .btn {
        border-radius: 0 8px 8px 0;
    }
    .password-tips {
        padding-left: 20px;
    }
    .password-tips li {
        color: #666;
        font-size: 13px;
        margin-bottom: 8px;
    }
    .password-tips li:last-child {
        margin-bottom: 0;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.toggle-password').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var targetId = this.getAttribute('data-target');
            var input = document.getElementById(targetId);
            var icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
});
</script>
