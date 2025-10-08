<div class="page-header">
    <h4 class="page-title"><?= $title ?></h4>
    <ul class="breadcrumbs">
        <li class="nav-home">
            <a href="<?= site_url('timechanges') ?>">
                <i class="fas fa-clock"></i>
            </a>
        </li>
        <li class="separator">
            <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
            <a href="<?= site_url('timechanges') ?>">Time Changes</a>
        </li>
        <li class="separator">
            <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
            <a href="javascript:void(0)"><?= $personnel->lastname . ', ' . $personnel->firstname ?></a>
        </li>
    </ul>
    <div class="ml-md-auto py-2 py-md-0">
        <a href="<?= site_url('timechanges') ?>" class="btn btn-secondary btn-border btn-round btn-sm">
            <span class="btn-label">
                <i class="fas fa-arrow-left"></i>
            </span>
            Back to Personnel List
        </a>
        <a href="#addAttendance" data-toggle="modal" class="btn btn-primary btn-border btn-round btn-sm">
            <span class="btn-label">
                <i class="far fa-clock"></i>
            </span>
            Add Time Record
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-head-row">
                    <div class="card-title">
                        <strong><?= $personnel->lastname . ', ' . $personnel->firstname . ' ' . $personnel->middlename ?></strong>
                        <span class="text-muted ml-2">(Bio ID: <?= $personnel->bio_id ?>)</span>
                    </div>
                    <div class="card-tools d-flex align-items-center">
                        <label class="mb-0 mr-2"><strong>Month:</strong></label>
                        <input type="month" class="form-control form-control-sm mr-3" id="month" name="start" value="<?= $selected_month ?>" style="width: 150px;">
                        <span class="badge badge-info" id="currentMonthDisplay"><?= date('F Y', strtotime($selected_month . '-01')) ?></span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Debug Panel -->
                <div class="row mb-2">
                    <div class="col-md-12">
                        <div class="alert alert-success" id="statusPanel">
                            <strong>✅ Status:</strong> <span id="statusText">Ready</span> | 
                            <strong>Current Month:</strong> <span id="debugMonth"><?= $selected_month ?></span> | 
                            <strong>Last Action:</strong> <span id="lastAction">None</span>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Edit Panel -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="card bg-light">
                            <div class="card-body p-3">
                                <h5 class="mb-3"><i class="fas fa-bolt text-warning"></i> Quick Edit Mode</h5>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="alert alert-warning mb-2">
                                            <strong>⚠️ IMPORTANT:</strong> This page uses AJAX - NO page refreshes! If you see a page refresh, please report it immediately.
                                            <br><strong>Current Month Lock:</strong> <span class="badge badge-danger"><?= $selected_month ?></span> (This will NOT change unless you select a different month)
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-8">
                                        <label class="font-weight-bold">Quick Apply Times:</label>
                                        <div class="d-flex gap-2">
                                            <input type="time" class="form-control form-control-sm" id="quickAM_In" placeholder="AM In" value="07:30">
                                            <input type="time" class="form-control form-control-sm" id="quickAM_Out" placeholder="AM Out" value="12:00">
                                            <input type="time" class="form-control form-control-sm" id="quickPM_In" placeholder="PM In" value="13:00">
                                            <input type="time" class="form-control form-control-sm" id="quickPM_Out" placeholder="PM Out" value="17:00">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="font-weight-bold">&nbsp;</label>
                                        <button class="btn btn-primary btn-block btn-sm" onclick="applyQuickTimes()">
                                            <i class="fas fa-magic"></i> Apply to Selected Rows
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="alert alert-warning" id="bulkEditAlert" style="display: none;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-check-square"></i> <strong><span id="selectedCount">0</span> record(s) selected</strong>
                                </div>
                                <div>
                                    <button class="btn btn-sm btn-primary" onclick="openBulkEditModal()">
                                        <i class="fas fa-edit"></i> Bulk Edit Selected
                                    </button>
                                    <button class="btn btn-sm btn-secondary" onclick="clearSelection()">
                                        <i class="fas fa-times"></i> Clear Selection
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <style>
                        #personnelBioTable tbody tr {
                            cursor: default !important;
                        }
                        #personnelBioTable .btn {
                            pointer-events: auto !important;
                            z-index: 10 !important;
                            position: relative !important;
                        }
                        #personnelBioTable a {
                            text-decoration: none !important;
                        }
                    </style>
                    <table id="personnelBioTable" class="display table table-striped table-hover" data-bio-id="<?= $personnel->bio_id ?>">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="selectAll"></th>
                                <th>Date</th>
                                <th>Morning In</th>
                                <th>Morning Out</th>
                                <th>Afternoon In</th>
                                <th>Afternoon Out</th>
                                <th>UT Hours</th>
                                <th>UT Minutes</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('time_changes/modal', ['personnel' => $personnel, 'person' => $person]) ?>
