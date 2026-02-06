<style>
.fctc-form-container {
    max-width: 700px;
    margin: 0 auto;
    background: #fff;
    border: 2px solid #333;
    padding: 30px 35px;
    font-family: 'Times New Roman', Times, serif;
    color: #000;
}
.fctc-header {
    text-align: center;
    margin-bottom: 15px;
    position: relative;
}
.fctc-header .logo-left {
    position: absolute; left: 0; top: 0; width: 70px; height: 70px; object-fit: contain;
}
.fctc-header .logo-right {
    position: absolute; right: 0; top: 0; width: 70px; height: 70px; object-fit: contain;
}
.fctc-header h6 { font-size: 13px; margin: 0; font-weight: normal; font-style: italic; }
.fctc-header h5 { font-size: 15px; margin: 0; font-weight: bold; letter-spacing: 1px; }
.fctc-header .sub-text { font-size: 12px; font-style: italic; margin: 0; }
.fctc-title { text-align: center; font-size: 16px; font-weight: bold; margin: 20px 0 15px; text-decoration: underline; }
.fctc-info-row { display: flex; align-items: baseline; margin-bottom: 8px; font-size: 13px; }
.fctc-info-row label { font-weight: bold; white-space: nowrap; margin-right: 5px; margin-bottom: 0; }
.fctc-info-row .info-value { flex: 1; border-bottom: 1px solid #000; min-height: 20px; padding: 0 5px; font-weight: bold; }
.fctc-table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 12px; }
.fctc-table th, .fctc-table td { border: 1px solid #000; padding: 5px 4px; text-align: center; vertical-align: middle; }
.fctc-table th { font-weight: bold; background: #f9f9f9; font-size: 11px; }
.fctc-signature { margin-top: 30px; text-align: right; font-size: 13px; }
.fctc-signature .sig-line { display: inline-block; width: 250px; border-top: 1px solid #000; text-align: center; padding-top: 3px; font-style: italic; }
.check-box-display { display: inline-block; width: 14px; height: 14px; border: 1px solid #000; text-align: center; line-height: 14px; font-size: 11px; font-weight: bold; }
</style>

<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title">Review Clock Change Request - <?= htmlspecialchars($request->control_no) ?></h4>
        <a href="<?= site_url('adminclockchangeapproval') ?>" class="btn btn-secondary btn-sm ml-auto">Back</a>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>Personnel:</strong> <?= htmlspecialchars($request->firstname . ' ' . $request->lastname) ?> |
                        <strong>Position:</strong> <?= htmlspecialchars($request->position) ?> |
                        <strong>Submitted:</strong> <?= date('M d, Y h:i A', strtotime($request->created_at)) ?>
                    </div>

                    <div class="fctc-form-container">
                        <div class="fctc-header">
                            <img src="<?= base_url('assets/img/doh_logo1.png') ?>" class="logo-left" alt="DOH Logo">
                            <img src="<?= base_url('assets/img/dogh_logo.png') ?>" class="logo-right" alt="DOGH Logo">
                            <h6>Republic of the Philippines</h6>
                            <h6>Department of Health</h6>
                            <h5>DAVAO OCCIDENTAL GENERAL HOSPITAL</h5>
                            <p class="sub-text">Lacaron, Malita, Davao Occidental</p>
                        </div>

                        <div class="fctc-title">Failure to Clock and Time Changes Form</div>

                        <div class="fctc-info-row">
                            <label>Control No.:</label>
                            <span class="info-value" style="max-width: 150px;"><?= htmlspecialchars($request->control_no) ?></span>
                        </div>

                        <div class="fctc-info-row">
                            <label>Employee ID:</label>
                            <span class="info-value" style="max-width: 100px;"><?= htmlspecialchars($personnel->bio_id) ?></span>
                            <label style="margin-left: 15px;">Name:</label>
                            <span class="info-value"><?= htmlspecialchars(strtoupper($personnel->lastname . ', ' . $personnel->firstname . ' ' . ($personnel->middlename ? substr($personnel->middlename, 0, 1) . '.' : ''))) ?></span>
                            <label style="margin-left: 15px;">Designation:</label>
                            <span class="info-value" style="max-width: 180px;"><?= htmlspecialchars($personnel->position) ?></span>
                        </div>

                        <table class="fctc-table">
                            <thead>
                                <tr>
                                    <th style="width: 110px;">Date</th>
                                    <th style="width: 55px;">AM/PM</th>
                                    <th colspan="2">Time</th>
                                    <th style="width: 100px;">Change</th>
                                    <th>Reason</th>
                                </tr>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th style="width: 35px;">IN</th>
                                    <th style="width: 35px;">OUT</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($items)): ?>
                                    <?php foreach ($items as $item): ?>
                                    <tr>
                                        <td><?= date('M d, Y', strtotime($item->date)) ?></td>
                                        <td><?= $item->am_pm ?></td>
                                        <td><span class="check-box-display"><?= $item->time_in ? '&#10003;' : '' ?></span></td>
                                        <td><span class="check-box-display"><?= $item->time_out ? '&#10003;' : '' ?></span></td>
                                        <td><?= htmlspecialchars($item->time_change ?: '') ?></td>
                                        <td style="text-align: left; padding-left: 8px;"><?= htmlspecialchars($item->reason ?: '') ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="6" class="text-muted">No entries</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>

                        <div class="fctc-signature">
                            <div class="sig-line">
                                <?php if (isset($supervisor) && $supervisor): ?>
                                    <?= htmlspecialchars(strtoupper($supervisor->lastname . ', ' . $supervisor->firstname)) ?>
                                <?php else: ?>
                                    &nbsp;
                                <?php endif; ?>
                            </div>
                            <br>
                            <div style="text-align: right;">
                                <span style="display: inline-block; width: 250px; text-align: center; font-style: italic; font-size: 12px;">Immediate Supervisor</span>
                            </div>
                        </div>
                    </div>

                    <?php if ($request->status == 'pending'): ?>
                    <div class="mt-4">
                        <textarea class="form-control mb-3" id="remarks" rows="2" placeholder="Remarks (optional)"></textarea>
                        <form action="<?= site_url('adminclockchangeapproval/approve/' . $request->id) ?>" method="post" class="d-inline">
                            <input type="hidden" name="remarks" id="approveRemarks">
                            <button type="submit" class="btn btn-success" onclick="document.getElementById('approveRemarks').value=document.getElementById('remarks').value; return confirm('Approve this request?')">
                                <i class="fas fa-check mr-1"></i>Approve
                            </button>
                        </form>
                        <form action="<?= site_url('adminclockchangeapproval/reject/' . $request->id) ?>" method="post" class="d-inline">
                            <input type="hidden" name="remarks" id="rejectRemarks">
                            <button type="submit" class="btn btn-danger" onclick="document.getElementById('rejectRemarks').value=document.getElementById('remarks').value; return confirm('Reject this request?')">
                                <i class="fas fa-times mr-1"></i>Reject
                            </button>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
