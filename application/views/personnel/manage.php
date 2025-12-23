<div class="page-header">
    <h4 class="page-title"><?= $title ?></h4>
    <ul class="breadcrumbs">
        <li class="nav-home">
            <a href="#">
                <i class="fas fa-users"></i>
            </a>
        </li>
        <li class="separator">
            <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
            <a href="javascript:void(0)">Personnel</a>
        </li>
    </ul>
    <div class="ml-md-auto py-2 py-md-0">
        <a href="#import" data-toggle="modal" class="btn btn-danger btn-border btn-round btn-sm">
            <span class="btn-label">
                <i class="fas fa-file-import"></i>
            </span>
            Import Personnel
        </a>
        <a href="#add" data-toggle="modal" class="btn btn-primary btn-border btn-round btn-sm">
            <span class="btn-label">
                <i class="far fa-address-book"></i>
            </span>
            Add Personnel
        </a>
    </div>
</div>

<!-- Personnel Statistics Dashboard -->
<div class="row">
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-primary bubble-shadow-small">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    <div class="col col-stats ml-3 ml-sm-0">
                        <div class="numbers">
                            <p class="card-category">Total Personnel</p>
                            <h4 class="card-title"><?= isset($statistics) ? number_format($statistics->total) : '0' ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-success bubble-shadow-small">
                            <i class="fas fa-user-check"></i>
                        </div>
                    </div>
                    <div class="col col-stats ml-3 ml-sm-0">
                        <div class="numbers">
                            <p class="card-category">Active Personnel</p>
                            <h4 class="card-title"><?= isset($statistics) ? number_format($statistics->active) : '0' ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-info bubble-shadow-small">
                            <i class="fas fa-user-tie"></i>
                        </div>
                    </div>
                    <div class="col col-stats ml-3 ml-sm-0">
                        <div class="numbers">
                            <p class="card-category">Regular Employees</p>
                            <h4 class="card-title"><?= isset($statistics) ? number_format($statistics->regular) : '0' ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-warning bubble-shadow-small">
                            <i class="fas fa-user-clock"></i>
                        </div>
                    </div>
                    <div class="col col-stats ml-3 ml-sm-0">
                        <div class="numbers">
                            <p class="card-category">Contract Personnel</p>
                            <h4 class="card-title"><?= isset($statistics) ? number_format($statistics->contract) : '0' ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">List of Personnel</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="personnelTable" class="display table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Photo</th>
                                <th>Fullname</th>
                                <th>Position</th>
                                <th>Employment Type</th>
                                <th>Salary Grade</th>
                                <th>Schedule</th>
                                <th>Email</th>
                                <th>Biometrics ID</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>No</th>
                                <th>Photo</th>
                                <th>Full name</th>
                                <th>Position</th>
                                <th>Employment Type</th>
                                <th>Salary Grade</th>
                                <th>Schedule</th>
                                <th>Email</th>
                                <th>Biometrics ID</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            <?php $no = 1;
                            foreach ($person as $row) : 
                                $profile_img = $row->profile_image 
                                    ? site_url('assets/uploads/profile_images/' . $row->profile_image) 
                                    : site_url('assets/img/person.png');
                            ?>
                                <tr>
                                    <td><?= $no ?></td>
                                    <td>
                                        <div class="avatar avatar-sm">
                                            <img src="<?= $profile_img ?>" alt="<?= htmlspecialchars($row->firstname, ENT_QUOTES, 'UTF-8') ?>" class="avatar-img rounded-circle" style="object-fit: cover;">
                                        </div>
                                    </td>
                                    <td><a href="<?= site_url('personnel/personnel_profile/') . $row->id ?>" class="font-weight-bold"><?= htmlspecialchars($row->lastname, ENT_QUOTES, 'UTF-8') . ', ' . htmlspecialchars($row->firstname, ENT_QUOTES, 'UTF-8') . ' ' . htmlspecialchars($row->middlename, ENT_QUOTES, 'UTF-8') ?></a></td>
                                    <td><?= htmlspecialchars($row->position, ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td>
                                        <?php 
                                        $employment_type = isset($row->employment_type) ? $row->employment_type : 'Regular';
                                        $badge_class = $employment_type == 'Regular' ? 'badge-success' : 'badge-warning';
                                        ?>
                                        <span class="badge <?= $badge_class ?>"><?= htmlspecialchars($employment_type, ENT_QUOTES, 'UTF-8'); ?></span>
                                    </td>
                                    <td><?= isset($row->salary_grade) ? 'SG ' . $row->salary_grade : 'N/A' ?></td>
                                    <td><small><?= isset($row->schedule_type) ? htmlspecialchars($row->schedule_type, ENT_QUOTES, 'UTF-8') : '8:00 AM - 5:00 PM' ?></small></td>
                                    <td><a href="mailto:<?= htmlspecialchars($row->email, ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($row->email, ENT_QUOTES, 'UTF-8'); ?></a></td>
                                    <td><?= $row->bio_id ?></td>
                                    <td><?= $row->status == 1 ? '<span class="badge badge-primary">Active</span>' : '<span class="badge badge-danger">Inactive</span>' ?></td>
                                    <td>
                                        <div class="form-button-action">
                                            <a type="button" href="<?= site_url('personnel/personnel_profile/') . $row->id ?>" class="btn btn-link btn-info mt-1 p-1" title="View Profile & Analytics">
                                                <i class="fas fa-user-circle"></i>
                                            </a>
                                            <?php if ($row->fb) : ?>
                                                <a type="button" href="<?= $row->fb ?>" data-toggle="tooltip" class="btn btn-link btn-primary mt-1 p-1" data-original-title="Facebook URL" target="_blank">
                                                    <i class="fab fa-facebook"></i>
                                                </a>
                                            <?php endif ?>
                                            <a type="button" href="#edit" data-toggle="modal" class="btn btn-link btn-success mt-1 p-1" title="Edit Personnel" data-id="<?= $row->id ?>" onclick="editPersonnel(this)">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <a type="button" href="<?= site_url('admin/generate_dtr/') . $row->id ?>" class="btn btn-link btn-secondary mt-1 p-1" title="Generate DTR">
                                                <i class="fas fa-file"></i>
                                            </a>
                                            <a type="button" href="<?= site_url('admin/personnel_attendace/') . $row->id ?>" class="btn btn-link btn-warning mt-1 p-1" title="View Attendance Records">
                                                <i class="fas fa-calendar-check"></i>
                                            </a>
                                            <a type="button" href="<?= site_url("personnel/delete/" . $row->id); ?>" data-toggle="tooltip" onclick="return confirm('Are you sure you want to delete this personnel?');" class="btn btn-link btn-danger mt-1 p-1" data-original-title="Remove">
                                                <i class="fa fa-times"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php $no++;
                            endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('personnel/modal') ?>

<script>
function editPersonnel(element) {
    var id = $(element).data('id');
    
    // Make AJAX request to get personnel data
    $.ajax({
        url: '<?= site_url("personnel/getPersonnel") ?>',
        type: 'POST',
        data: { id: id },
        dataType: 'json',
        success: function(response) {
            if (response.data) {
                var personnel = response.data;
                
                // Populate form fields
                $('#personnel_id').val(personnel.id);
                $('#bio').val(personnel.bio_id);
                $('#lname').val(personnel.lastname);
                $('#fname').val(personnel.firstname);
                $('#mname').val(personnel.middlename);
                $('#position').val(personnel.position);
                $('#email').val(personnel.email);
                $('#fb_url').val(personnel.fb);
                $('#status').val(personnel.status);
                
                // Populate new fields with fallback values
                $('#employment_type').val(personnel.employment_type || 'Regular');
                $('#salary_grade').val(personnel.salary_grade || '');
                $('#schedule_type').val(personnel.schedule_type || '8:00 AM - 5:00 PM');
                
                // Show modal
                $('#edit').modal('show');
            }
        },
        error: function() {
            alert('Error loading personnel data');
        }
    });
}

$(document).ready(function() {
    // Initialize DataTable with enhanced columns
    $('#personnelTable').DataTable({
        "responsive": true,
        "pageLength": 25,
        "order": [[ 0, "asc" ]],
        "columnDefs": [
            { "orderable": false, "targets": -1 } // Disable ordering on Action column
        ]
    });
    
    // Form validation for create form
    $('#create_personnel_form').on('submit', function(e) {
        var bioId = $('input[name="bio"]').val();
        var email = $('input[name="email"]').val();
        
        if (!bioId || !email) {
            e.preventDefault();
            alert('Please fill in all required fields');
            return false;
        }
    });
});
</script>