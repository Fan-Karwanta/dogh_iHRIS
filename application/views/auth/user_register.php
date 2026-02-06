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
    <title><?= isset($title) ? $title : 'User Registration' ?> - <?= $sys->system_name ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/atlantis.min.css') ?>">
    <style>
        body {
            background-image: url('<?= base_url('assets/img/dogh_background.jpg') ?>');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            min-height: 100vh;
            padding: 40px 0;
        }
        .register-container {
            max-width: 700px;
            width: 100%;
            margin: 0 auto;
            padding: 20px;
        }
        .register-card {
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
        .section-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #31ce36;
        }
        .form-group label {
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }
        .form-group label .required {
            color: #f25961;
        }
        .form-control {
            border-radius: 10px;
            padding: 10px 15px;
            border: 2px solid #e0e0e0;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #31ce36;
            box-shadow: 0 0 0 3px rgba(49, 206, 54, 0.1);
        }
        .btn-register {
            background: linear-gradient(135deg, #31ce36 0%, #1b8e20 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            width: 100%;
            color: white;
            margin-top: 20px;
        }
        .btn-register:hover {
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
        .profile-upload {
            text-align: center;
            margin-bottom: 20px;
        }
        .profile-upload .preview {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: #f0f0f0;
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border: 3px solid #e0e0e0;
        }
        .profile-upload .preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .profile-upload .preview svg {
            width: 50px;
            height: 50px;
            color: #ccc;
        }
        .profile-upload input[type="file"] {
            display: none;
        }
        .profile-upload .btn-upload {
            background: #f0f0f0;
            border: none;
            padding: 8px 20px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 14px;
            color: #666;
        }
        .profile-upload .btn-upload:hover {
            background: #e0e0e0;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <div class="logo-section">
                <?php if (!empty($sys->system_logo)) : ?>
                    <img src="<?= base_url('assets/uploads/' . $sys->system_logo) ?>" alt="<?= $sys->system_name ?>">
                <?php else : ?>
                    <img src="<?= base_url('assets/img/logo.png') ?>" alt="Logo">
                <?php endif; ?>
                <h3>DOGH <?= $sys->system_acronym ?></h3>
                <p>Create your account to access the DTR system</p>
            </div>
            
            <?php if (isset($message) && $message): ?>
                <div class="alert alert-danger" role="alert">
                    <?= $message ?>
                </div>
            <?php endif; ?>
            
            <?php if (validation_errors()): ?>
                <div class="alert alert-danger" role="alert">
                    <?= validation_errors() ?>
                </div>
            <?php endif; ?>
            
            <form action="<?= site_url('userauth/register') ?>" method="POST" enctype="multipart/form-data">
                
                <!-- Profile Image Upload -->
                <div class="profile-upload">
                    <div class="preview" id="imagePreview">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                            <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
                        </svg>
                    </div>
                    <label for="profile_image" class="btn-upload">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 5px;">
                            <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                            <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z"/>
                        </svg>
                        Upload Photo
                    </label>
                    <input type="file" id="profile_image" name="profile_image" accept="image/*" onchange="previewImage(this)">
                    <p class="text-muted mt-2" style="font-size: 12px;">Optional - Max 2MB (JPG, PNG, GIF)</p>
                </div>
                
                <!-- Personal Information -->
                <h5 class="section-title">Personal Information</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="firstname">First Name <span class="required">*</span></label>
                            <input type="text" class="form-control" id="firstname" name="firstname" 
                                   placeholder="First Name" required value="<?= set_value('firstname') ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="middlename">Middle Name</label>
                            <input type="text" class="form-control" id="middlename" name="middlename" 
                                   placeholder="Middle Name" value="<?= set_value('middlename') ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="lastname">Last Name <span class="required">*</span></label>
                            <input type="text" class="form-control" id="lastname" name="lastname" 
                                   placeholder="Last Name" required value="<?= set_value('lastname') ?>">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email">Email Address <span class="required">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   placeholder="your.email@example.com" required value="<?= set_value('email') ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fb">Facebook Profile URL</label>
                            <input type="url" class="form-control" id="fb" name="fb" 
                                   placeholder="https://facebook.com/yourprofile" value="<?= set_value('fb') ?>">
                        </div>
                    </div>
                </div>
                
                <!-- Employment Information -->
                <h5 class="section-title mt-4">Employment Information</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="position">Position <span class="required">*</span></label>
                            <input type="text" class="form-control" id="position" name="position" 
                                   placeholder="e.g., Nurse, Doctor, Admin Staff" required value="<?= set_value('position') ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="role">Department/Role</label>
                            <input type="text" class="form-control" id="role" name="role" 
                                   placeholder="e.g., Emergency, OPD, Admin" value="<?= set_value('role') ?>">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="employment_type">Employment Type <span class="required">*</span></label>
                            <select class="form-control" id="employment_type" name="employment_type" required>
                                <option value="">Select Type</option>
                                <option value="Regular" <?= set_select('employment_type', 'Regular') ?>>Regular</option>
                                <option value="COS" <?= set_select('employment_type', 'COS') ?>>COS</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="salary_grade">Salary Grade</label>
                            <input type="number" class="form-control" id="salary_grade" name="salary_grade" 
                                   placeholder="e.g., 11" min="1" max="33" value="<?= set_value('salary_grade') ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="schedule_type">Work Schedule</label>
                            <select class="form-control" id="schedule_type" name="schedule_type">
                                <option value="8:00 AM - 5:00 PM" <?= set_select('schedule_type', '8:00 AM - 5:00 PM', TRUE) ?>>8:00 AM - 5:00 PM</option>
                                <option value="7:00 AM - 4:00 PM" <?= set_select('schedule_type', '7:00 AM - 4:00 PM') ?>>7:00 AM - 4:00 PM</option>
                                <option value="6:00 AM - 3:00 PM" <?= set_select('schedule_type', '6:00 AM - 3:00 PM') ?>>6:00 AM - 3:00 PM</option>
                                <option value="Shifting" <?= set_select('schedule_type', 'Shifting') ?>>Shifting</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Account Credentials -->
                <h5 class="section-title mt-4">Account Credentials</h5>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="username">Username <span class="required">*</span></label>
                            <input type="text" class="form-control" id="username" name="username" 
                                   placeholder="Choose a username (min 5 characters)" required 
                                   minlength="5" value="<?= set_value('username') ?>">
                            <small class="text-muted">Only letters, numbers, underscores and dashes allowed</small>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="password">Password <span class="required">*</span></label>
                            <input type="password" class="form-control" id="password" name="password" 
                                   placeholder="Min 8 characters" required minlength="8">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="confirm_password">Confirm Password <span class="required">*</span></label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                   placeholder="Re-enter password" required>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="terms" required>
                        <label class="custom-control-label" for="terms">
                            I agree that the information provided is accurate and correct.
                        </label>
                    </div>
                </div>

                                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="terms2" required>
                        <label class="custom-control-label" for="terms2">
                            I agree to share my information and get approval from admin.
                        </label>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-register">Create Account</button>
            </form>
            
            <div class="links">
                <p>Already have an account? <a href="<?= site_url('userauth/login') ?>">Sign In</a></p>
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
        function previewImage(input) {
            var preview = document.getElementById('imagePreview');
            
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        // Password match validation
        document.getElementById('confirm_password').addEventListener('input', function() {
            var password = document.getElementById('password').value;
            var confirm = this.value;
            
            if (password !== confirm) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html>
