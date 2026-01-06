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
            <a href="#">Pending Applications</a>
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
                    <div class="card-title">
                        <i class="fas fa-clock text-warning"></i> Pending Leave Applications
                        <?php if (count($leaves) > 0): ?>
                            <span class="badge badge-warning"><?= count($leaves) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <?php if (!empty($leaves)): ?>
                <div class="table-responsive">
                    <table id="pendingTable" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Application #</th>
                                <th>Employee</th>
                                <th>Department</th>
                                <th>Leave Type</th>
                                <th>Date Filed</th>
                                <th>Inclusive Dates</th>
                                <th>Days</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($leaves as $leave): ?>
                            <tr>
                                <td><strong><?= $leave->application_number ?></strong></td>
                                <td><?= $leave->lastname . ', ' . $leave->firstname ?></td>
                                <td>
                                    <span class="badge badge-<?= 
                                        $leave->office_department == 'Medical' ? 'primary' : 
                                        ($leave->office_department == 'Nursing' ? 'success' : 
                                        ($leave->office_department == 'Ancillary' ? 'info' : 'secondary')) 
                                    ?>">
                                        <?= $leave->office_department ?>
                                    </span>
                                </td>
                                <td><?= $this->leaveModel->get_leave_type_label($leave->leave_type) ?></td>
                                <td><?= date('M d, Y', strtotime($leave->date_of_filing)) ?></td>
                                <td>
                                    <?= date('M d', strtotime($leave->inclusive_date_from)) ?> - 
                                    <?= date('M d', strtotime($leave->inclusive_date_to)) ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-light"><?= number_format($leave->working_days_applied, 1) ?></span>
                                </td>
                                <td>
                                    <a href="<?= site_url('leaves/view/' . $leave->id) ?>" class="btn btn-sm btn-primary">
                                        <i class="fa fa-cog"></i> Process
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                    <h5>All Caught Up!</h5>
                    <p class="text-muted">There are no pending leave applications at this time.</p>
                    <a href="<?= site_url('leaves/all') ?>" class="btn btn-primary">
                        <i class="fa fa-list"></i> View All Applications
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#pendingTable').DataTable({
        "order": [[4, "asc"]],
        "pageLength": 25
    });
});
</script>
