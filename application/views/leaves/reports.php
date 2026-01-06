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
            <a href="#">Reports</a>
        </li>
    </ul>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <form method="GET" class="form-inline">
            <label class="mr-2">Year:</label>
            <select name="year" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                <option value="<?= $y ?>" <?= $year == $y ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
        </form>
    </div>
</div>

<div class="row">
    <!-- Leave by Type -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Approved Leaves by Type (<?= $year ?>)</div>
            </div>
            <div class="card-body">
                <?php if (!empty($type_stats)): ?>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Leave Type</th>
                                <th class="text-center">Count</th>
                                <th class="text-center">Total Days</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $total_count = 0;
                            $total_days = 0;
                            foreach ($type_stats as $stat): 
                                $total_count += $stat->total;
                                $total_days += $stat->total_days;
                            ?>
                            <tr>
                                <td><?= $this->leaveModel->get_leave_type_label($stat->leave_type) ?></td>
                                <td class="text-center"><?= $stat->total ?></td>
                                <td class="text-center"><?= number_format($stat->total_days, 1) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="font-weight-bold">
                                <td>Total</td>
                                <td class="text-center"><?= $total_count ?></td>
                                <td class="text-center"><?= number_format($total_days, 1) ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-muted text-center">No approved leaves for this year.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Leave by Department -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Approved Leaves by Department (<?= $year ?>)</div>
            </div>
            <div class="card-body">
                <?php if (!empty($dept_stats)): ?>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Department</th>
                                <th class="text-center">Count</th>
                                <th class="text-center">Total Days</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dept_stats as $stat): ?>
                            <tr>
                                <td>
                                    <span class="badge badge-<?= 
                                        $stat->office_department == 'Medical' ? 'primary' : 
                                        ($stat->office_department == 'Nursing' ? 'success' : 
                                        ($stat->office_department == 'Ancillary' ? 'info' : 'secondary')) 
                                    ?>">
                                        <?= $stat->office_department ?>
                                    </span>
                                </td>
                                <td class="text-center"><?= $stat->total ?></td>
                                <td class="text-center"><?= number_format($stat->total_days, 1) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-muted text-center">No approved leaves for this year.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Monthly Summary -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Monthly Leave Applications Summary (<?= $year ?>)</div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th class="text-center">Pending</th>
                                <th class="text-center">Certified</th>
                                <th class="text-center">Recommended</th>
                                <th class="text-center">Approved</th>
                                <th class="text-center">Disapproved</th>
                                <th class="text-center">Cancelled</th>
                                <th class="text-center">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $months = array('', 'January', 'February', 'March', 'April', 'May', 'June', 
                                           'July', 'August', 'September', 'October', 'November', 'December');
                            $grand_totals = array('pending' => 0, 'certified' => 0, 'recommended' => 0, 
                                                 'approved' => 0, 'disapproved' => 0, 'cancelled' => 0, 'total' => 0);
                            
                            for ($m = 1; $m <= 12; $m++): 
                                $month_data = isset($monthly_stats[$m]) ? $monthly_stats[$m] : array();
                                $counts = array('pending' => 0, 'certified' => 0, 'recommended' => 0, 
                                               'approved' => 0, 'disapproved' => 0, 'cancelled' => 0);
                                
                                foreach ($month_data as $stat) {
                                    if (isset($counts[$stat->status])) {
                                        $counts[$stat->status] = $stat->total;
                                    }
                                }
                                
                                $month_total = array_sum($counts);
                                foreach ($counts as $key => $val) {
                                    $grand_totals[$key] += $val;
                                }
                                $grand_totals['total'] += $month_total;
                            ?>
                            <tr>
                                <td><?= $months[$m] ?></td>
                                <td class="text-center"><?= $counts['pending'] ?: '-' ?></td>
                                <td class="text-center"><?= $counts['certified'] ?: '-' ?></td>
                                <td class="text-center"><?= $counts['recommended'] ?: '-' ?></td>
                                <td class="text-center text-success"><?= $counts['approved'] ?: '-' ?></td>
                                <td class="text-center text-danger"><?= $counts['disapproved'] ?: '-' ?></td>
                                <td class="text-center text-muted"><?= $counts['cancelled'] ?: '-' ?></td>
                                <td class="text-center font-weight-bold"><?= $month_total ?: '-' ?></td>
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                        <tfoot>
                            <tr class="font-weight-bold bg-light">
                                <td>Total</td>
                                <td class="text-center"><?= $grand_totals['pending'] ?></td>
                                <td class="text-center"><?= $grand_totals['certified'] ?></td>
                                <td class="text-center"><?= $grand_totals['recommended'] ?></td>
                                <td class="text-center text-success"><?= $grand_totals['approved'] ?></td>
                                <td class="text-center text-danger"><?= $grand_totals['disapproved'] ?></td>
                                <td class="text-center text-muted"><?= $grand_totals['cancelled'] ?></td>
                                <td class="text-center"><?= $grand_totals['total'] ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
