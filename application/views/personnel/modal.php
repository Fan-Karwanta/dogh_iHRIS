<!-- Modal -->
<div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Create Personnel</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="<?= site_url('create_personnel') ?>" id="create_personnel_form">
                    <div class="form-group form-floating-label">
                        <label>Biometrics ID</label>
                        <input type="text" class="form-control" name="bio" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group form-floating-label">
                                <label>Last Name</label>
                                <input type="text" class="form-control" name="lname" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-floating-label">
                                <label>First Name</label>
                                <input type="text" class="form-control" name="fname" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group form-floating-label">
                                <label>Middle Name</label>
                                <input type="text" class="form-control" name="mname" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-floating-label">
                                <label>Position</label>
                                <input type="text" class="form-control" name="position" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group form-floating-label">
                        <label>Email Address</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group form-floating-label">
                                <label>Employment Type</label>
                                <select class="form-control" name="employment_type" required>
                                    <option value="Regular">Regular</option>
                                    <option value="Contract of Service">Contract of Service</option>
                                    <option value="COS / JO">COS / JO</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-floating-label">
                                <label>Salary Grade</label>
                                <input type="number" class="form-control" name="salary_grade" min="1" max="33">
                            </div>
                        </div>
                    </div>
                    <div class="form-group form-floating-label">
                        <label>Schedule Type</label>
                        <input type="text" class="form-control" name="schedule_type" value="8:00 AM - 5:00 PM">
                    </div>
                    <div class="form-group form-floating-label">
                        <label>Facebook URL</label>
                        <input type="url" class="form-control" name="fb_url">
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Create</button>
            </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Personnel</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="<?= site_url('personnel/update') ?>">
                    <div class="form-group form-floating-label">
                        <label>Biometrics ID</label>
                        <input type="text" class="form-control" name="bio" required id="bio">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group form-floating-label">
                                <label>Last Name</label>
                                <input type="text" class="form-control" name="lname" required id="lname">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-floating-label">
                                <label>First Name</label>
                                <input type="text" class="form-control" name="fname" required id="fname">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group form-floating-label">
                                <label>Middle Name</label>
                                <input type="text" class="form-control" name="mname" required id="mname">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-floating-label">
                                <label>Position</label>
                                <input type="text" class="form-control" name="position" required id="position">
                            </div>
                        </div>
                    </div>
                    <div class="form-group form-floating-label">
                        <label>Email Address</label>
                        <input type="email" class="form-control" name="email" required id="email">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group form-floating-label">
                                <label>Employment Type</label>
                                <select class="form-control" name="employment_type" id="employment_type" required>
                                    <option value="Regular">Regular</option>
                                    <option value="Contract of Service">Contract of Service</option>
                                    <option value="COS / JO">COS / JO</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-floating-label">
                                <label>Salary Grade</label>
                                <input type="number" class="form-control" name="salary_grade" id="salary_grade" min="1" max="33">
                            </div>
                        </div>
                    </div>
                    <div class="form-group form-floating-label">
                        <label>Schedule Type</label>
                        <input type="text" class="form-control" name="schedule_type" id="schedule_type">
                    </div>
                    <div class="form-group form-floating-label">
                        <label>Facebook URL</label>
                        <input type="url" class="form-control" name="fb_url" id="fb_url">
                    </div>
                    <div class="form-group form-floating-label">
                        <label>Status</label>
                        <select class="form-control" name="status" id="status">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="id" id="personnel_id">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="import" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Import Personnel</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="<?= site_url('personnel/importCSV') ?>" enctype="multipart/form-data">
                    <div class="form-group form-floating-label">
                        <label>Upload CSV</label>
                        <input type="file" class="form-control" name="import_file" accept=".csv" required>
                    </div>
                    <div class="alert alert-info">
                        <strong>CSV Format Required:</strong><br>
                        <small>Timestamp, Biometrics ID, Employee ID, Last Name, First Name, Middle Name, Type of Employment, Position, Salary Grade, Email Address, Type of Schedule</small><br>
                        <small class="text-muted">Note: Employee ID column will be ignored as it duplicates Biometrics ID</small>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" id="importCSV">Upload</button>
            </div>
            </form>
        </div>
    </div>
</div>