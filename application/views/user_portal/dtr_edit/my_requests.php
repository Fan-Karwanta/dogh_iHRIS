<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-list mr-2"></i>My DTR Edit Requests</h5>
        <a href="<?= site_url('personneldtredit') ?>" class="btn btn-primary btn-sm float-right">
            <i class="fas fa-plus mr-1"></i>New Request
        </a>
    </div>
    <div class="card-body">
        <?php if (empty($requests)): ?>
        <div class="text-center py-5 text-muted">
            <i class="fas fa-inbox fa-3x mb-3"></i>
            <p>No DTR edit requests found.</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Month</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th>Reviewed</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($requests as $req): ?>
                <tr>
                    <td>#<?= $req->id ?></td>
                    <td><?= date('F Y', strtotime($req->request_month . '-01')) ?></td>
                    <td>
                        <?php
                        $badge = 'secondary';
                        if ($req->status == 'pending') $badge = 'warning';
                        elseif ($req->status == 'approved') $badge = 'success';
                        elseif ($req->status == 'rejected') $badge = 'danger';
                        ?>
                        <span class="badge badge-<?= $badge ?>"><?= ucfirst($req->status) ?></span>
                    </td>
                    <td><?= date('M d, Y h:i A', strtotime($req->created_at)) ?></td>
                    <td><?= $req->approved_at ? date('M d, Y h:i A', strtotime($req->approved_at)) : '-' ?></td>
                    <td>
                        <a href="<?= site_url('personneldtredit/view_request/' . $req->id) ?>" class="btn btn-sm btn-info" title="View">
                            <i class="fas fa-eye"></i>
                        </a>
                        <?php if ($req->status == 'approved'): ?>
                        <a href="<?= site_url('personneldtredit/print_dtr/' . $req->id) ?>" class="btn btn-sm btn-success" title="Print DTR" target="_blank">
                            <i class="fas fa-print"></i>
                        </a>
                        <?php endif; ?>
                        <?php if ($req->status == 'pending'): ?>
                        <a href="<?= site_url('personneldtredit/cancel_request/' . $req->id) ?>" 
                           class="btn btn-sm btn-danger" onclick="return confirm('Cancel this request?')" title="Cancel">
                            <i class="fas fa-times"></i>
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>
