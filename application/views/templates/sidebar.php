<?php 
$current_page = $this->uri->segment(2); 
$current_controller = $this->uri->segment(1);
// Use cached system settings
if (!isset($GLOBALS['_sys_cache'])) {
    $query = $this->db->query("SELECT * FROM systems WHERE id=1");
    $GLOBALS['_sys_cache'] = $query->row();
}
$sys = $GLOBALS['_sys_cache'];
?>
<!-- Sidebar -->
<div class="sidebar sidebar-style-2" data-background-color="dark2">
    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
            <ul class="nav nav-primary">
                <li class="nav-item <?= $current_page == 'dashboard' ? 'active' : null ?>">
                    <a href="<?= site_url('admin/dashboard') ?>">
                        <i class="fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-section">
                    <span class="sidebar-mini-icon">
                        <i class="fa fa-ellipsis-h"></i>
                    </span>
                    <h4 class="text-section">MENU</h4>
                </li>
                <li class="nav-item <?= $current_page == 'personnel' || $current_page == 'personnel_attendace' || $current_page == 'generate_dtr' ? 'active' : null ?>">
                    <a href="<?= site_url('admin/personnel') ?>">
                        <i class="fas fa-users"></i>
                        <p>Personnel</p>
                    </a>
                </li>
                <!-- Attendance nav item commented out as requested
                <li class="nav-item <?= $current_page == 'attendance' || $current_page == 'generate_dtr' ? 'active' : null ?>">
                    <a href="<?= site_url('admin/attendance') ?>">
                        <i class="fas fa-calendar-check"></i>
                        <p>Attendance</p>
                    </a>
                </li>
                -->
                <li class="nav-item <?= $current_page == 'biometrics' || $current_page == 'generate_biometrics' || $current_controller == 'mainbiometrics' ? 'active' : null ?>">
                    <a data-toggle="collapse" href="#biometricsMenu">
                        <i class="fas fa-fingerprint"></i>
                        <p>Biometrics</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse <?= $current_page == 'biometrics' || $current_page == 'generate_biometrics' || $current_controller == 'mainbiometrics' ? 'show' : null ?>" id="biometricsMenu">
                        <ul class="nav nav-collapse">
                            <li class="<?= $current_page == 'biometrics' ? 'active' : null ?>">
                                <a href="<?= site_url('admin/biometrics') ?>">
                                    <span class="sub-item">Dialysis/Admin Dept</span>
                                </a>
                            </li>
                            <li class="<?= $current_controller == 'mainbiometrics' ? 'active' : null ?>">
                                <a href="<?= site_url('mainbiometrics') ?>">
                                    <span class="sub-item">Main Department</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item <?= $current_page == 'timechanges' || $current_page == 'personnel_biometrics' ? 'active' : null ?>">
                    <a href="<?= site_url('admin/timechanges') ?>">
                        <i class="fas fa-user-clock"></i>
                        <p>Time Changes</p>
                    </a>
                </li>
                <li class="nav-item <?= $current_page == 'generate_dtr' || $current_page == 'generate_bulk_dtr' || $current_page == 'failure_summary' || $current_page == 'schedule_compliance' ? 'active' : null ?>">
                    <a data-toggle="collapse" href="#dtrMenu">
                        <i class="fas fa-file-alt"></i>
                        <p>Reports</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse <?= $current_page == 'generate_dtr' || $current_page == 'generate_bulk_dtr' || $current_page == 'failure_summary' || $current_page == 'schedule_compliance' ? 'show' : null ?>" id="dtrMenu">
                        <ul class="nav nav-collapse">
                            <li class="<?= $current_page == 'generate_dtr' ? 'active' : null ?>">
                                <a href="<?= site_url('attendance/generate_dtr') ?>">
                                    <span class="sub-item">Individual DTR</span>
                                </a>
                            </li>
                            <li class="<?= $current_page == 'generate_bulk_dtr' ? 'active' : null ?>">
                                <a href="<?= site_url('attendance/generate_bulk_dtr') ?>">
                                    <span class="sub-item">Bulk DTR (All Personnel)</span>
                                </a>
                            </li>
                            <li class="<?= $current_page == 'failure_summary' ? 'active' : null ?>">
                                <a href="<?= site_url('biometrics/failure_summary') ?>">
                                    <span class="sub-item">Failure to Clock In/Out</span>
                                </a>
                            </li>
                            <li class="<?= $current_page == 'schedule_compliance' ? 'active' : null ?>">
                                <a href="<?= site_url('reports/schedule_compliance') ?>">
                                    <span class="sub-item"><i class="fas fa-trophy text-warning mr-1"></i>Schedule Compliance</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item <?= $current_page == 'audit_trail' || $current_page == 'audit_reports' ? 'active' : null ?>">
                    <a data-toggle="collapse" href="#auditMenu">
                        <i class="fas fa-history"></i>
                        <p>Audit Trail</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse <?= $current_page == 'audit_trail' || $current_page == 'audit_reports' ? 'show' : null ?>" id="auditMenu">
                        <ul class="nav nav-collapse">
                            <li class="<?= $current_page == 'audit_trail' ? 'active' : null ?>">
                                <a href="<?= site_url('admin/audit_trail') ?>">
                                    <span class="sub-item">Edit History</span>
                                </a>
                            </li>
                            <li class="<?= $current_page == 'audit_reports' ? 'active' : null ?>">
                                <a href="<?= site_url('admin/audit_reports') ?>">
                                    <span class="sub-item">Reports & Analytics</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                
                <?php if ($this->ion_auth->is_admin()) : ?>
                
                <!-- Leave Management (Admin Only) -->
                <li class="nav-item <?= $current_controller == 'leaves' ? 'active' : null ?>">
                    <a data-toggle="collapse" href="#leavesMenu">
                        <i class="fas fa-calendar-minus"></i>
                        <p>Leave Management</p>
                        <span class="caret"></span>
                        <?php 
                        if (!isset($GLOBALS['_pending_leaves_count'])) {
                            $GLOBALS['_pending_leaves_count'] = 0;
                            try {
                                if ($this->db->table_exists('leave_applications')) {
                                    $this->db->where_in('status', array('pending', 'certified', 'recommended'));
                                    $GLOBALS['_pending_leaves_count'] = $this->db->count_all_results('leave_applications');
                                }
                            } catch (Exception $e) {
                                $GLOBALS['_pending_leaves_count'] = 0;
                            }
                        }
                        if ($GLOBALS['_pending_leaves_count'] > 0): ?>
                            <span class="badge badge-warning"><?= $GLOBALS['_pending_leaves_count'] ?></span>
                        <?php endif; ?>
                    </a>
                    <div class="collapse <?= $current_controller == 'leaves' ? 'show' : null ?>" id="leavesMenu">
                        <ul class="nav nav-collapse">
                            <li class="<?= ($current_controller == 'leaves' && empty($current_page)) ? 'active' : null ?>">
                                <a href="<?= site_url('leaves') ?>">
                                    <span class="sub-item">Dashboard</span>
                                </a>
                            </li>
                            <li class="<?= $current_page == 'pending' ? 'active' : null ?>">
                                <a href="<?= site_url('leaves/pending') ?>">
                                    <span class="sub-item">Pending Applications</span>
                                    <?php if ($GLOBALS['_pending_leaves_count'] > 0): ?>
                                        <span class="badge badge-warning"><?= $GLOBALS['_pending_leaves_count'] ?></span>
                                    <?php endif; ?>
                                </a>
                            </li>
                            <li class="<?= $current_page == 'all' ? 'active' : null ?>">
                                <a href="<?= site_url('leaves/all') ?>">
                                    <span class="sub-item">All Applications</span>
                                </a>
                            </li>
                            <li class="<?= $current_page == 'credits' ? 'active' : null ?>">
                                <a href="<?= site_url('leaves/credits') ?>">
                                    <span class="sub-item">Leave Credits</span>
                                </a>
                            </li>
                            <li class="<?= $current_page == 'reports' ? 'active' : null ?>">
                                <a href="<?= site_url('leaves/reports') ?>">
                                    <span class="sub-item">Reports</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <?php endif ?>
                
                <?php if ($this->ion_auth->is_admin()) : ?>
                    <li class="nav-section">
                        <span class="sidebar-mini-icon">
                            <i class="fa fa-ellipsis-h"></i>
                        </span>
                        <h4 class="text-section">System</h4>
                    </li>
                    <li class="nav-item <?= $current_controller == 'usermanagement' ? 'active' : null ?>">
                        <a href="<?= site_url('usermanagement') ?>">
                            <i class="fas fa-user-check"></i>
                            <p>User Management</p>
                            <?php 
                            // Cache pending count to avoid repeated queries
                            if (!isset($GLOBALS['_pending_users_count'])) {
                                $GLOBALS['_pending_users_count'] = 0;
                                try {
                                    // Check if user_accounts table exists first
                                    if ($this->db->table_exists('user_accounts')) {
                                        $this->load->model('UserAccountModel', 'userAccountModel');
                                        if (isset($this->userAccountModel)) {
                                            $stats = $this->userAccountModel->get_statistics();
                                            $GLOBALS['_pending_users_count'] = ($stats && isset($stats->pending)) ? $stats->pending : 0;
                                        }
                                    }
                                } catch (Exception $e) {
                                    $GLOBALS['_pending_users_count'] = 0;
                                }
                            }
                            if ($GLOBALS['_pending_users_count'] > 0): ?>
                                <span class="badge badge-warning"><?= $GLOBALS['_pending_users_count'] ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item <?= $current_controller == 'hierarchyapproval' ? 'active' : null ?>">
                        <a href="<?= site_url('hierarchyapproval') ?>">
                            <i class="fas fa-sitemap"></i>
                            <p>Hierarchy Approval</p>
                        </a>
                    </li>
                    <li class="nav-item <?= $current_controller == 'admindtrapproval' ? 'active' : null ?>">
                        <a href="<?= site_url('admindtrapproval') ?>">
                            <i class="fas fa-check-double"></i>
                            <p>DTR Edit Requests</p>
                        </a>
                    </li>
                    <li class="nav-item <?= $current_page == 'users' || ($current_controller == 'settings' && ($current_page == 'departments' || $current_page == 'holidays')) ? 'active' : null ?>">
                        <a data-toggle="collapse" href="#settings">
                            <i class="fas fa-cogs"></i>
                            <p>Settings</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse <?= $current_page == 'users' || ($current_controller == 'settings' && ($current_page == 'departments' || $current_page == 'holidays')) ? 'show' : null ?>" id="settings">
                            <ul class="nav nav-collapse">
                                <li class="<?= $current_page == 'users' ? 'active' : null ?>">
                                    <a href="<?= site_url('admin/users') ?>">
                                        <span class="sub-item">Users</span>
                                    </a>
                                </li>
                                <li class="<?= ($current_controller == 'settings' && $current_page == 'departments') ? 'active' : null ?>">
                                    <a href="<?= site_url('settings/departments') ?>">
                                        <span class="sub-item">Departments</span>
                                    </a>
                                </li>
                                <li class="<?= ($current_controller == 'settings' && $current_page == 'holidays') ? 'active' : null ?>">
                                    <a href="<?= site_url('settings/holidays') ?>">
                                        <span class="sub-item">Holidays</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#sett" data-toggle="modal">
                                        <span class="sub-item">Settings</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#restore" data-toggle="modal">
                                        <span class="sub-item">Restore</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?= site_url('settings/backup') ?>">
                                        <span class="sub-item">Backup</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                <?php endif ?>
            </ul>
        </div>
    </div>
</div>
<!-- End Sidebar -->