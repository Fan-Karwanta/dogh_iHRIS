<?php $current_page = $this->uri->segment(2); ?>
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
                <li class="nav-item <?= $current_page == 'biometrics' || $current_page == 'generate_biometrics' ? 'active' : null ?>">
                    <a href="<?= site_url('admin/biometrics') ?>">
                        <i class="fas fa-fingerprint"></i>
                        <p>Biometrics</p>
                    </a>
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
                    <li class="nav-section">
                        <span class="sidebar-mini-icon">
                            <i class="fa fa-ellipsis-h"></i>
                        </span>
                        <h4 class="text-section">System</h4>
                    </li>
                    <li class="nav-item <?= $current_page == 'users' ? 'active' : null ?>">
                        <a data-toggle="collapse" href="#settings">
                            <i class="fas fa-cogs"></i>
                            <p>Settings</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse <?= $current_page == 'users' ? 'show' : null ?>" id="settings">
                            <ul class="nav nav-collapse">
                                <li class="<?= $current_page == 'users' ? 'active' : null ?>">
                                    <a href="<?= site_url('admin/users') ?>">
                                        <span class="sub-item">Users</span>
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