<div class="card-custom">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5><i class="fas fa-clock mr-2"></i>Failure to Clock / Time Changes Requests</h5>
        <a href="<?= site_url('clockchangerequest/create') ?>" class="btn btn-success btn-sm">
            <i class="fas fa-plus mr-1"></i>New Request
        </a>
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

        <?php if (empty($requests)): ?>
        <div class="text-center py-5 text-muted">
            <i class="fas fa-inbox fa-3x mb-3"></i>
            <p>No requests yet. Click "New Request" to submit one.</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Control No.</th>
                        <th>Date Submitted</th>
                        <th>Entries</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($requests as $req): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($req->control_no) ?></strong></td>
                    <td><?= date('M d, Y h:i A', strtotime($req->created_at)) ?></td>
                    <td>
                        <?php
                        $this->db->where('request_id', $req->id);
                        $item_count = $this->db->count_all_results('clock_change_request_items');
                        echo $item_count . ' entr' . ($item_count == 1 ? 'y' : 'ies');
                        ?>
                    </td>
                    <td>
                        <?php
                        $badge_class = 'secondary';
                        if ($req->status == 'pending') $badge_class = 'warning';
                        elseif ($req->status == 'approved') $badge_class = 'success';
                        elseif ($req->status == 'rejected') $badge_class = 'danger';
                        elseif ($req->status == 'cancelled') $badge_class = 'secondary';
                        ?>
                        <span class="badge badge-<?= $badge_class ?>"><?= ucfirst($req->status) ?></span>
                    </td>
                    <td>
                        <a href="<?= site_url('clockchangerequest/view/' . $req->id) ?>" class="btn btn-sm btn-primary" title="View">
                            <i class="fas fa-eye"></i>
                        </a>
                        <?php if ($req->status == 'approved'): ?>
                        <a href="<?= site_url('clockchangerequest/print_request/' . $req->id) ?>" class="btn btn-sm btn-info" title="Print" target="_blank">
                            <i class="fas fa-print"></i>
                        </a>
                        <?php endif; ?>
                        <?php if ($req->status == 'pending'): ?>
                        <a href="<?= site_url('clockchangerequest/cancel/' . $req->id) ?>" class="btn btn-sm btn-danger" title="Cancel" onclick="return confirm('Cancel this request?')">
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
