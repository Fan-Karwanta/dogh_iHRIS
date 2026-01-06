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
            <a href="#">Leave Management</a>
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

<!-- Statistics Cards -->
<div class="row">
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-warning bubble-shadow-small">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                    <div class="col col-stats ml-3 ml-sm-0">
                        <div class="numbers">
                            <p class="card-category">Pending</p>
                            <h4 class="card-title"><?= $stats->pending ?? 0 ?></h4>
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
                            <i class="fas fa-certificate"></i>
                        </div>
                    </div>
                    <div class="col col-stats ml-3 ml-sm-0">
                        <div class="numbers">
                            <p class="card-category">Certified</p>
                            <h4 class="card-title"><?= $stats->certified ?? 0 ?></h4>
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
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                    <div class="col col-stats ml-3 ml-sm-0">
                        <div class="numbers">
                            <p class="card-category">Approved</p>
                            <h4 class="card-title"><?= $stats->approved ?? 0 ?></h4>
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
                        <div class="icon-big text-center icon-secondary bubble-shadow-small">
                            <i class="fas fa-file-alt"></i>
                        </div>
                    </div>
                    <div class="col col-stats ml-3 ml-sm-0">
                        <div class="numbers">
                            <p class="card-category">Total (<?= date('Y') ?>)</p>
                            <h4 class="card-title"><?= $stats->total ?? 0 ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Quick Actions</div>
            </div>
            <div class="card-body">
                <a href="<?= site_url('leaves/pending') ?>" class="btn btn-warning btn-round mr-2">
                    <i class="fas fa-clock"></i> Pending Applications
                    <?php if (($stats->pending ?? 0) > 0): ?>
                        <span class="badge badge-light"><?= $stats->pending ?></span>
                    <?php endif; ?>
                </a>
                <a href="<?= site_url('leaves/all') ?>" class="btn btn-primary btn-round mr-2">
                    <i class="fas fa-list"></i> All Applications
                </a>
                <a href="<?= site_url('leaves/credits') ?>" class="btn btn-info btn-round mr-2">
                    <i class="fas fa-calculator"></i> Leave Credits
                </a>
                <a href="<?= site_url('leaves/reports') ?>" class="btn btn-secondary btn-round">
                    <i class="fas fa-chart-bar"></i> Reports
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Pending Applications Table -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-head-row">
                    <div class="card-title">Applications Requiring Action</div>
                    <div class="card-tools">
                        <a href="<?= site_url('leaves/all') ?>" class="btn btn-sm btn-primary">
                            View All <i class="fa fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Application #</th>
                                <th>Employee</th>
                                <th>Department</th>
                                <th>Leave Type</th>
                                <th>Date Filed</th>
                                <th>Days</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($pending_leaves)): ?>
                                <?php foreach ($pending_leaves as $leave): ?>
                                <tr>
                                    <td><strong><?= $leave->application_number ?></strong></td>
                                    <td><?= $leave->lastname . ', ' . $leave->firstname ?></td>
                                    <td><?= $leave->office_department ?></td>
                                    <td><?= $this->leaveModel->get_leave_type_label($leave->leave_type) ?></td>
                                    <td><?= date('M d, Y', strtotime($leave->date_of_filing)) ?></td>
                                    <td class="text-center"><?= number_format($leave->working_days_applied, 1) ?></td>
                                    <td>
                                        <span class="badge <?= $this->leaveModel->get_status_badge($leave->status) ?>">
                                            <?= ucfirst($leave->status) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?= site_url('leaves/view/' . $leave->id) ?>" class="btn btn-sm btn-info">
                                            <i class="fa fa-eye"></i> Process
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="fas fa-check-circle fa-2x mb-2"></i><br>
                                        No pending applications at this time.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
