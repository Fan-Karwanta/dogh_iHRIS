<div class="card-custom">
    <div class="card-header">
        <h5><i class="fas fa-check-double mr-2"></i>Failure to Clock / Time Changes - Approvals</h5>
    </div>
    <div class="card-body">
        <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= $this->session->flashdata('success') ?>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
        <?php endif; ?>
        <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= $this->session->flashdata('error') ?>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
        <?php endif; ?>

        <?php if (empty($pending_requests)): ?>
        <div class="text-center py-5 text-muted">
            <i class="fas fa-inbox fa-3x mb-3"></i>
            <p>No pending Failure to Clock / Time Changes requests to approve.</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Control No.</th>
                        <th>Personnel</th>
                        <th>Position</th>
                        <th>Submitted</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($pending_requests as $req): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($req->control_no) ?></strong></td>
                    <td><?= htmlspecialchars($req->firstname . ' ' . $req->lastname) ?></td>
                    <td><?= htmlspecialchars($req->position) ?></td>
                    <td><?= date('M d, Y h:i A', strtotime($req->created_at)) ?></td>
                    <td>
                        <a href="<?= site_url('clockchangeapproval/view/' . $req->id) ?>" class="btn btn-sm btn-primary">
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
