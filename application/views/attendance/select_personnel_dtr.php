<div class="page-header">
    <h4 class="page-title"><?= $title ?></h4>
    <ul class="breadcrumbs">
        <li class="nav-home">
            <a href="#">
                <i class="fas fa-users"></i>
            </a>
        </li>
        <li class="separator">
            <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
            <a href="javascript:void(0)">DTR Reports</a>
        </li>
        <li class="separator">
            <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
            <a href="javascript:void(0)">Individual DTR</a>
        </li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-head-row">
                    <div class="card-title">Select Personnel to Generate DTR</div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 offset-md-3">
                        <div class="form-group">
                            <label for="personnel_select"><strong>Choose Personnel:</strong></label>
                            <select class="form-control" id="personnel_select" style="width:100%;">
                                <option value="">-- Select Personnel --</option>
                                <?php foreach ($all_personnel as $person) : ?>
                                    <option value="<?= $person->id ?>">
                                        <?= $person->lastname . ', ' . $person->firstname . ' ' . $person->middlename ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <div class="form-group text-center mt-4">
                            <button type="button" class="btn btn-primary btn-lg" id="generate_btn" disabled>
                                <i class="fa fa-file-alt"></i> Generate DTR
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row mt-5">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <h5><i class="fas fa-info-circle"></i> Instructions:</h5>
                            <ul class="mb-0">
                                <li>Select a personnel from the dropdown above</li>
                                <li>Click "Generate DTR" to view their Daily Time Record</li>
                                <li>You can then print or export the DTR as PDF</li>
                                <li>To generate DTRs for all personnel at once, use <a href="<?= site_url('attendance/generate_bulk_dtr') ?>"><strong>Bulk DTR</strong></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize select2 for better dropdown
    $('#personnel_select').select2({
        placeholder: "Search personnel by name...",
        allowClear: true
    });

    // Enable/disable generate button based on selection
    $('#personnel_select').on('change', function() {
        var personnelId = $(this).val();
        if (personnelId) {
            $('#generate_btn').prop('disabled', false);
        } else {
            $('#generate_btn').prop('disabled', true);
        }
    });

    // Generate DTR button click
    $('#generate_btn').on('click', function() {
        var personnelId = $('#personnel_select').val();
        if (personnelId) {
            window.location.href = '<?= site_url('attendance/generate_dtr/') ?>' + personnelId;
        }
    });

    // Allow Enter key to generate
    $('#personnel_select').on('select2:select', function() {
        $('#generate_btn').trigger('click');
    });
});
</script>
