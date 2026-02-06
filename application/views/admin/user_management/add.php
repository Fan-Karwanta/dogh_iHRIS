<div class="content">
    <div class="page-inner">
        <div class="page-header">
            <h4 class="page-title">Add New User</h4>
            <ul class="breadcrumbs">
                <li class="nav-home">
                    <a href="<?= site_url('dashboard') ?>"><i class="flaticon-home"></i></a>
                </li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="<?= site_url('usermanagement') ?>">User Management</a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="#">Add User</a></li>
            </ul>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Create New User Account</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($message) && $message): ?>
                            <div class="alert alert-danger"><?= $message ?></div>
                        <?php endif; ?>
                        
                        <?php if (validation_errors()): ?>
                            <div class="alert alert-danger"><?= validation_errors() ?></div>
                        <?php endif; ?>

                        <form action="<?= site_url('usermanagement/add') ?>" method="POST" enctype="multipart/form-data">
                            
                            <!-- Profile Image -->
                            <div class="form-group text-center">
                                <div id="imagePreview" style="width: 120px; height: 120px; border-radius: 50%; background: #f0f0f0; margin: 0 auto 15px; display: flex; align-items: center; justify-content: center; overflow: hidden; border: 3px solid #e0e0e0;">
                                    <i class="fas fa-user" style="font-size: 40px; color: #ccc;"></i>
                                </div>
                                <label for="profile_image" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-upload"></i> Upload Photo
                                </label>
                                <input type="file" id="profile_image" name="profile_image" accept="image/*" style="display: none;" onchange="previewImage(this)">
                            </div>

                            <h5 class="mt-4 mb-3"><i class="fas fa-user mr-2"></i>Personal Information</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>First Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="firstname" required value="<?= set_value('firstname') ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Middle Name</label>
                                        <input type="text" class="form-control" name="middlename" value="<?= set_value('middlename') ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Last Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="lastname" required value="<?= set_value('lastname') ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" name="email" required value="<?= set_value('email') ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Facebook URL</label>
                                        <input type="url" class="form-control" name="fb" value="<?= set_value('fb') ?>">
                                    </div>
                                </div>
                            </div>

                            <h5 class="mt-4 mb-3"><i class="fas fa-briefcase mr-2"></i>Employment Information</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Position <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="position" required value="<?= set_value('position') ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Department/Role</label>
                                        <input type="text" class="form-control" name="role" value="<?= set_value('role') ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Biometric ID</label>
                                        <input type="number" class="form-control" name="bio_id" value="<?= set_value('bio_id') ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Employment Type</label>
                                        <select class="form-control" name="employment_type">
                                            <option value="Regular">Regular</option>
                                            <option value="COS">COS</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Salary Grade</label>
                                        <input type="number" class="form-control" name="salary_grade" min="1" max="33" value="<?= set_value('salary_grade') ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Work Schedule</label>
                                        <select class="form-control" name="schedule_type">
                                            <option value="8:00 AM - 5:00 PM">8:00 AM - 5:00 PM</option>
                                            <option value="7:00 AM - 4:00 PM">7:00 AM - 4:00 PM</option>
                                            <option value="6:00 AM - 3:00 PM">6:00 AM - 3:00 PM</option>
                                            <option value="Shifting">Shifting</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <h5 class="mt-4 mb-3"><i class="fas fa-lock mr-2"></i>Account Credentials</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Username <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="username" required minlength="5" value="<?= set_value('username') ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Password <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control" name="password" required minlength="8">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Confirm Password <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control" name="confirm_password" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-2"></i>Create User
                                </button>
                                <a href="<?= site_url('usermanagement') ?>" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left mr-2"></i>Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function previewImage(input) {
    var preview = document.getElementById('imagePreview');
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = '<img src="' + e.target.result + '" style="width: 100%; height: 100%; object-fit: cover;">';
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
