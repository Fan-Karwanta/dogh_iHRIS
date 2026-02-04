<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-check-circle mr-2"></i>DTR Edit Requests for Approval</h5>
    </div>
    <div class="card-body">
        <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success"><?= $this->session->flashdata('success') ?></div>
        <?php endif; ?>
        <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger"><?= $this->session->flashdata('error') ?></div>
        <?php endif; ?>

        <?php if (empty($pending_requests)): ?>
        <div class="text-center py-5 text-muted">
            <i class="fas fa-inbox fa-3x mb-3"></i>
            <p>No pending DTR edit requests to approve.</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Personnel</th>
                        <th>Position</th>
                        <th>Month</th>
                        <th>Submitted</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($pending_requests as $req): ?>
                <tr>
                    <td>#<?= $req->id ?></td>
                    <td><?= $req->firstname ?> <?= $req->lastname ?></td>
                    <td><?= $req->position ?></td>
                    <td><?= date('F Y', strtotime($req->request_month . '-01')) ?></td>
                    <td><?= date('M d, Y h:i A', strtotime($req->created_at)) ?></td>
                    <td>
                        <a href="<?= site_url('dtrapproval/view/' . $req->id) ?>" class="btn btn-sm btn-primary">
                            <i class="fas fa-eye mr-1"></i>Review
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>
