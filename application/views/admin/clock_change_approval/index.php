<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title">Failure to Clock / Time Changes Requests</h4>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success"><?= $this->session->flashdata('success') ?></div>
            <?php endif; ?>
            <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger"><?= $this->session->flashdata('error') ?></div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Pending Requests</h4>
                </div>
                <div class="card-body">
                    <?php if (empty($pending_requests)): ?>
                    <p class="text-muted text-center py-4">No pending requests.</p>
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
                                    <a href="<?= site_url('adminclockchangeapproval/view/' . $req->id) ?>" class="btn btn-sm btn-primary">Review</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
