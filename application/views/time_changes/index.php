<div class="page-header">
    <h4 class="page-title"><?= $title ?></h4>
    <ul class="breadcrumbs">
        <li class="nav-home">
            <a href="#">
                <i class="fas fa-clock"></i>
            </a>
        </li>
        <li class="separator">
            <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
            <a href="javascript:void(0)">Time Changes</a>
        </li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-head-row">
                    <div class="card-title">Select Personnel</div>

                </div>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> <strong>Instructions:</strong> Click on a personnel name to view and edit their biometric time records. You can make individual edits or bulk updates to multiple records at once.
                </div>
                
                <div class="table-responsive">
                    <table id="personnelTable" class="display table table-striped table-hover time-changes-table">
                        <thead>
                            <tr>
                                <th>Bio ID</th>
                                <th>Name</th>
                                <th>Position</th>
                                <th>Employment Type</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($personnel as $person) : ?>
                                <tr>
                                    <td><?= $person->bio_id ?></td>
                                    <td><?= $person->lastname . ', ' . $person->firstname . ' ' . $person->middlename ?></td>
                                    <td><?= isset($person->position) ? $person->position : 'N/A' ?></td>
                                    <td>
                                        <?php if (isset($person->employment_type)) : ?>
                                            <span class="badge badge-<?= $person->employment_type == 'Regular' ? 'success' : 'warning' ?>">
                                                <?= $person->employment_type ?>
                                            </span>
                                        <?php else : ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (isset($person->status)) : ?>
                                            <span class="badge badge-<?= $person->status == 1 ? 'success' : 'secondary' ?>">
                                                <?= $person->status == 1 ? 'Active' : 'Inactive' ?>
                                            </span>
                                        <?php else : ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= site_url('timechanges/personnel_biometrics/' . $person->bio_id) ?>" class="btn btn-primary btn-sm">
                                            <i class="fas fa-clock"></i> View Time Records
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
