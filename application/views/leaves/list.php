<div class="page-header">
    <h4 class="page-title"><?= $title ?></h4>
    <ul class="breadcrumbs">
        <li class="nav-home">
            <a href="<?= site_url('admin/dashboard') ?>">
                <i class="fas fa-home"></i>
            </a>
        </li>
        <li class="separator">
            <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
            <a href="<?= site_url('leaves') ?>">Leave Management</a>
        </li>
        <li class="separator">
            <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
            <a href="#">All Applications</a>
        </li>
    </ul>
</div>

<?php if ($this->session->flashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= $this->session->flashdata('success') ?>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
<?php endif; ?>

<?php if ($this->session->flashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= $this->session->flashdata('error') ?>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-head-row">
                    <div class="card-title">All Leave Applications</div>
                    <div class="card-tools">
                        <!-- Filters -->
                        <form method="GET" class="form-inline">
                            <select name="status" class="form-control form-control-sm mr-2">
                                <option value="">All Status</option>
                                <option value="draft" <?= (isset($filters['status']) && $filters['status'] == 'draft') ? 'selected' : '' ?>>Draft</option>
                                <option value="pending" <?= (isset($filters['status']) && $filters['status'] == 'pending') ? 'selected' : '' ?>>Pending</option>
                                <option value="certified" <?= (isset($filters['status']) && $filters['status'] == 'certified') ? 'selected' : '' ?>>Certified</option>
                                <option value="recommended" <?= (isset($filters['status']) && $filters['status'] == 'recommended') ? 'selected' : '' ?>>Recommended</option>
                                <option value="approved" <?= (isset($filters['status']) && $filters['status'] == 'approved') ? 'selected' : '' ?>>Approved</option>
                                <option value="disapproved" <?= (isset($filters['status']) && $filters['status'] == 'disapproved') ? 'selected' : '' ?>>Disapproved</option>
                                <option value="cancelled" <?= (isset($filters['status']) && $filters['status'] == 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                            <select name="department" class="form-control form-control-sm mr-2">
                                <option value="">All Departments</option>
                                <option value="Medical" <?= (isset($filters['office_department']) && $filters['office_department'] == 'Medical') ? 'selected' : '' ?>>Medical</option>
                                <option value="Nursing" <?= (isset($filters['office_department']) && $filters['office_department'] == 'Nursing') ? 'selected' : '' ?>>Nursing</option>
                                <option value="Ancillary" <?= (isset($filters['office_department']) && $filters['office_department'] == 'Ancillary') ? 'selected' : '' ?>>Ancillary</option>
                                <option value="Administrative" <?= (isset($filters['office_department']) && $filters['office_department'] == 'Administrative') ? 'selected' : '' ?>>Administrative</option>
                            </select>
                            <select name="year" class="form-control form-control-sm mr-2">
                                <option value="">All Years</option>
                                <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                                <option value="<?= $y ?>" <?= (isset($filters['year']) && $filters['year'] == $y) ? 'selected' : '' ?>><?= $y ?></option>
                                <?php endfor; ?>
                            </select>
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="fa fa-filter"></i> Filter
                            </button>
                            <a href="<?= site_url('leaves/all') ?>" class="btn btn-sm btn-secondary ml-1">
                                <i class="fa fa-times"></i> Clear
                            </a>
                        </form>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="leavesTable" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Application #</th>
                                <th>Employee</th>
                                <th>Department</th>
                                <th>Leave Type</th>
                                <th>Date Filed</th>
                                <th>Inclusive Dates</th>
                                <th>Days</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($leaves as $leave): ?>
                            <tr>
                                <td><strong><?= $leave->application_number ?></strong></td>
                                <td><?= $leave->lastname . ', ' . $leave->firstname ?></td>
                                <td><?= $leave->office_department ?></td>
                                <td><?= $this->leaveModel->get_leave_type_label($leave->leave_type) ?></td>
                                <td><?= date('M d, Y', strtotime($leave->date_of_filing)) ?></td>
                                <td>
                                    <?= date('M d', strtotime($leave->inclusive_date_from)) ?> - 
                                    <?= date('M d, Y', strtotime($leave->inclusive_date_to)) ?>
                                </td>
                                <td class="text-center"><?= number_format($leave->working_days_applied, 1) ?></td>
                                <td>
                                    <span class="badge <?= $this->leaveModel->get_status_badge($leave->status) ?>">
                                        <?= ucfirst($leave->status) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="<?= site_url('leaves/view/' . $leave->id) ?>" class="btn btn-sm btn-info" title="View">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="<?= site_url('leaves/print_form/' . $leave->id) ?>" class="btn btn-sm btn-secondary" title="Print" target="_blank">
                                            <i class="fa fa-print"></i>
                                        </a>
                                        <?php if ($this->ion_auth->is_admin()): ?>
                                        <a href="<?= site_url('leaves/delete/' . $leave->id) ?>" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this application?')">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if (empty($leaves)): ?>
                <div class="text-center py-4">
                    <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No leave applications found matching your criteria.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#leavesTable').DataTable({
        "order": [[4, "desc"]],
        "pageLength": 25
    });
});
</script>
