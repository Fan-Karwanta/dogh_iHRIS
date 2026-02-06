<?php if (isset($all_notifications) && !empty($all_notifications)): ?>
<div class="card-custom">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-bell mr-2 text-primary"></i>All Notifications</h5>
        <div>
            <span class="badge badge-primary mr-2"><?= count($all_notifications) ?> total</span>
            <?php
            $unread_total = 0;
            foreach ($all_notifications as $n) { if (!$n->is_read) $unread_total++; }
            if ($unread_total > 0): ?>
                <a href="<?= site_url('user/mark_all_notifications_read') ?>" class="btn btn-sm btn-outline-success">
                    <i class="fas fa-check-double mr-1"></i> Mark All Read (<?= $unread_total ?>)
                </a>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="notification-list">
            <?php foreach ($all_notifications as $notif): ?>
                <div class="notification-item <?= $notif->is_read ? '' : 'unread' ?>" data-id="<?= $notif->id ?>">
                    <?php
                    $icon_class = 'fa-info-circle';
                    $icon_bg = '';
                    if ($notif->type == 'success') { $icon_class = 'fa-check-circle'; $icon_bg = 'background: linear-gradient(135deg, #31ce36 0%, #1b8e20 100%);'; }
                    elseif ($notif->type == 'danger') { $icon_class = 'fa-times-circle'; $icon_bg = 'background: linear-gradient(135deg, #f25961 0%, #d32f2f 100%);'; }
                    elseif ($notif->type == 'warning') { $icon_class = 'fa-exclamation-triangle'; $icon_bg = 'background: linear-gradient(135deg, #ffad46 0%, #f5a623 100%);'; }
                    else { $icon_bg = 'background: linear-gradient(135deg, #1572e8 0%, #0d47a1 100%);'; }
                    ?>
                    <div class="notification-icon <?= $notif->is_read ? 'read' : '' ?>" <?= !$notif->is_read ? 'style="' . $icon_bg . '"' : '' ?>>
                        <i class="fas <?= $icon_class ?>"></i>
                    </div>
                    <div class="notification-content">
                        <h6 class="notification-title"><?= htmlspecialchars($notif->title) ?></h6>
                        <p class="notification-message"><?= htmlspecialchars($notif->message) ?></p>
                        <small class="notification-time">
                            <i class="fas fa-clock mr-1"></i>
                            <?= date('M d, Y h:i A', strtotime($notif->created_at)) ?>
                        </small>
                    </div>
                    <?php if (!$notif->is_read): ?>
                        <button class="btn btn-sm btn-outline-primary mark-read-btn" onclick="markAsRead(<?= $notif->id ?>)">
                            Mark Read
                        </button>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php else: ?>
<div class="card-custom">
    <div class="card-body text-center py-5">
        <div class="empty-state">
            <i class="fas fa-bell-slash"></i>
        </div>
        <h5 class="mt-4 text-muted">No Notifications</h5>
        <p class="text-muted mb-0">You're all caught up! Check back later for updates.</p>
    </div>
</div>
<?php endif; ?>

<style>
    .notification-list {
        max-height: 600px;
        overflow-y: auto;
    }
    .notification-item {
        display: flex;
        align-items: flex-start;
        padding: 20px 24px;
        border-bottom: 1px solid #f0f0f0;
        transition: background 0.2s ease;
    }
    .notification-item:last-child {
        border-bottom: none;
    }
    .notification-item:hover {
        background: #f8f9fa;
    }
    .notification-item.unread {
        background: linear-gradient(90deg, rgba(49, 206, 54, 0.05) 0%, #fff 100%);
        border-left: 3px solid #31ce36;
    }
    .notification-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: linear-gradient(135deg, #31ce36 0%, #1b8e20 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 18px;
        margin-right: 16px;
        flex-shrink: 0;
    }
    .notification-icon.read {
        background: #e0e0e0;
        color: #999;
    }
    .notification-content {
        flex: 1;
        min-width: 0;
    }
    .notification-title {
        font-weight: 600;
        color: #1a2035;
        margin-bottom: 4px;
        font-size: 15px;
    }
    .notification-message {
        color: #666;
        font-size: 13px;
        margin-bottom: 6px;
        line-height: 1.5;
    }
    .notification-time {
        color: #999;
        font-size: 12px;
    }
    .mark-read-btn {
        flex-shrink: 0;
        margin-left: 12px;
        font-size: 12px;
    }
    .empty-state {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: #f0f0f0;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        font-size: 40px;
        color: #ccc;
    }
</style>

<script>
function markAsRead(id) {
    fetch('<?= site_url('user/mark_notification_read/') ?>' + id, {
        method: 'GET',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            var item = document.querySelector('.notification-item[data-id="' + id + '"]');
            if (item) {
                item.classList.remove('unread');
                item.querySelector('.notification-icon').classList.add('read');
                var btn = item.querySelector('.mark-read-btn');
                if (btn) btn.remove();
            }
        }
    });
}
</script>
