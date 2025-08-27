<div class="page-header">
    <h4 class="page-title"><?= $title ?></h4>
    <ul class="breadcrumbs">
        <li class="nav-home">
            <a href="#">
                <i class="fas fa-fingerprint"></i>
            </a>
        </li>
        <li class="separator">
            <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
            <a href="javascript:void(0)">Biometrics Report</a>
        </li>
    </ul>
    <div class="ml-md-auto py-2 py-md-0">
        <a href="javascript:void(0)" onclick="printDiv('printThis')" class="btn btn-danger btn-border btn-round btn-sm">
            <span class="btn-label">
                <i class="fas fa-print"></i>
            </span>
            Print Report
        </a>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-head-row">
                    <div class="card-title">Biometrics Attendance</div>
                    <div class="card-tools">
                        <input type="date" class="form-control" id="month" name="start" value="<?= isset($_GET['date']) ? $_GET['date'] : date('Y-m-d') ?>">
                    </div>
                </div>
            </div>
            <div class="card-body bg-white" id="printThis">
                <?php 
                // Get system logo and info
                $sys_query = $this->db->query("SELECT * FROM systems WHERE id=1");
                $sys_info = $sys_query->row();
                ?>
                <div class="text-center">
                    <?php if (!empty($sys_info->system_logo)) : ?>
                        <img src="<?= base_url('assets/uploads/' . $sys_info->system_logo) ?>" alt="System Logo" style="max-height: 100px; max-width: 150px; margin-bottom: 15px;">
                    <?php endif; ?>
                    <h4>Department of Health</h4>
                    <h2>Davao Occidental General Hospital</h2>
                    <h4 class="mb-3"><?= !empty($sys_info->system_name) ? strtoupper($sys_info->system_name) : 'BIOMETRICS ATTENDANCE' ?></h4>
                    <h4 class="text-uppercase"><?= isset($_GET['date']) ? date('F d, Y', strtotime($_GET['date'])) : date('F d, Y') ?></h4>
                </div>
                <div class="table-responsive">
                    <table class="display table-striped table-hover w-100">
                        <thead class="bg-dark text-light">
                            <tr>
                                <th class="p-1">Personnel Name</th>
                                <th class="p-1">Morning In</th>
                                <th class="p-1">Morning Out</th>
                                <th class="p-1">Afternoon In</th>
                                <th class="p-1">Afternoon Out</th>
                                <th class="p-1">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($bio)) : ?>
                                <?php foreach ($bio as $row) : ?>
                                    <tr>
                                        <td class="p-1"><?= $row->lastname . ', ' . $row->firstname . ' ' . $row->middlename[0]; ?>.</td>
                                        <td class="p-1"><?= empty($row->am_in) ? null : date('h:i A', strtotime($row->am_in)); ?></td>
                                        <td class="p-1"><?= empty($row->am_out) ? null : date('h:i A', strtotime($row->am_out)); ?></td>
                                        <td class="p-1"><?= empty($row->pm_in) ? null : date('h:i A', strtotime($row->pm_in)); ?></td>
                                        <td class="p-1"><?= empty($row->pm_in) ? null : date('h:i A', strtotime($row->pm_in)); ?></td>
                                        <td class="p-1"></td>
                                    </tr>
                                <?php endforeach ?>
                            <?php endif ?>
                        </tbody>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('bio/modal') ?>