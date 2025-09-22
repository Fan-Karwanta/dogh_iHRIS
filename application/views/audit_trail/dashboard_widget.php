<!-- Audit Trail Dashboard Widget -->
<div class="card">
    <div class="card-header">
        <div class="card-head-row">
            <div class="card-title">Recent DTR Edits</div>
            <div class="card-tools">
                <a href="<?= site_url('admin/audit_trail') ?>" class="btn btn-info btn-border btn-round btn-sm">
                    <span class="btn-label">
                        <i class="fas fa-history"></i>
                    </span>
                    View All
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <?php if (!empty($recent_audits)): ?>
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>Personnel</th>
                            <th>Action</th>
                            <th>Admin</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($recent_audits as $audit): ?>
                        <tr>
                            <td>
                                <small class="text-muted">
                                    <?= date('M j, g:i A', strtotime($audit->created_at)) ?>
                                </small>
                            </td>
                            <td>
                                <a href="<?= site_url('audit_trail/personnel_by_email/' . urlencode($audit->personnel_email)) ?>" 
                                   class="text-primary">
                                    <?= htmlspecialchars($audit->personnel_name) ?>
                                </a>
                            </td>
                            <td>
                                <?php
                                $badge_class = '';
                                switch($audit->action_type) {
                                    case 'CREATE': $badge_class = 'badge-success'; break;
                                    case 'UPDATE': $badge_class = 'badge-warning'; break;
                                    case 'DELETE': $badge_class = 'badge-danger'; break;
                                    default: $badge_class = 'badge-secondary';
                                }
                                ?>
                                <span class="badge <?= $badge_class ?>"><?= $audit->action_type ?></span>
                                <?php if ($audit->field_name): ?>
                                    <small class="text-muted">
                                        <?= ucfirst(str_replace('_', ' ', $audit->field_name)) ?>
                                    </small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <small><?= htmlspecialchars($audit->admin_name) ?></small>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-3">
                <i class="fas fa-history fa-3x text-muted mb-3"></i>
                <p class="text-muted">No recent audit activities</p>
            </div>
        <?php endif; ?>
    </div>
</div>
