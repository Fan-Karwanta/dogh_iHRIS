// Time Changes - Personnel List Page
$(document).ready(function() {
    if ($('#personnelTable').length && $('#personnelTable').hasClass('time-changes-table')) {
        // Check if DataTable is already initialized
        if (!$.fn.DataTable.isDataTable('#personnelTable')) {
            $('#personnelTable').DataTable({
                "order": [[1, "asc"]],
                "pageLength": 25,
                "oLanguage": {
                    "sLengthMenu": "_MENU_ Personnel per page"
                }
            });
        }
    }
    
    // Initialize Select2 for modals
    if ($('#basic').length) {
        $('#basic').select2({
            theme: "bootstrap",
            dropdownParent: $('#addAttendance')
        });
    }
    
    if ($('#basic2').length) {
        $('#basic2').select2({
            theme: "bootstrap",
            dropdownParent: $('#editBio')
        });
    }
    
    // Bulk edit field change handler
    $('#bulkField').on('change', function() {
        if ($(this).val()) {
            $('#bulkValueGroup').slideDown();
        } else {
            $('#bulkValueGroup').slideUp();
        }
    });
    
    // Update bulk edit count when modal opens
    $('#bulkEdit').on('show.bs.modal', function() {
        var selectedCount = $('.bio-checkbox:checked').length;
        $('#bulkSelectedCount').text(selectedCount);
    });
});

// Time Changes - Personnel Biometrics Page
if ($('#personnelBioTable').length) {
    var bio_id = $('#personnelBioTable').data('bio-id');
    var date = '';
    var table;
    var LOCKED_MONTH = ''; // This will NEVER change unless user changes month filter

    // PREVENT ALL PAGE NAVIGATION
    window.onbeforeunload = function() {
        return "Are you sure you want to leave? Your month selection will be preserved.";
    };

    // Update status function
    function updateStatus(text, action) {
        $('#statusText').text(text);
        $('#lastAction').text(action + ' at ' + new Date().toLocaleTimeString());
        $('#debugMonth').text($('#month').val());
        console.log('[TIME CHANGES] ' + text + ' | Month: ' + $('#month').val());
    }

    $(document).ready(function() {
        date = $('#month').val();
        LOCKED_MONTH = date; // LOCK the month
        
        updateStatus('Initializing...', 'Page Load');
        
        // Store the selected month in localStorage as backup
        var storedMonth = localStorage.getItem('timechanges_month_' + bio_id);
        if (storedMonth && !date) {
            date = storedMonth;
            LOCKED_MONTH = date;
            $('#month').val(date);
        }
        
        console.log('[TIME CHANGES] Month LOCKED to: ' + LOCKED_MONTH);
        updateStatus('Ready - Month Locked', 'Initialization Complete');
        
        loadBioTable();
        
        $('#month').on('change', function() {
            date = $(this).val();
            // Store in localStorage
            localStorage.setItem('timechanges_month_' + bio_id, date);
            // Update URL with selected month to persist it
            var currentUrl = window.location.href.split('?')[0];
            var newUrl = currentUrl + '?month=' + date;
            window.history.replaceState({path: newUrl}, '', newUrl);
            table.ajax.reload(null, false);
        });
        
        // Select all checkbox
        $('#selectAll').on('change', function() {
            $('.bio-checkbox').prop('checked', this.checked);
            updateBulkEditAlert();
        });
        
        // Individual checkbox change
        $(document).on('change', '.bio-checkbox', function() {
            updateBulkEditAlert();
            
            // Update select all checkbox
            var allChecked = $('.bio-checkbox:checked').length === $('.bio-checkbox').length;
            $('#selectAll').prop('checked', allChecked);
        });
        
        // Handle Edit button clicks with event delegation
        $(document).on('click', '.edit-bio-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            console.log('[TIME CHANGES] Edit button clicked - NO NAVIGATION SHOULD OCCUR');
            updateStatus('Loading record...', 'Edit Button Clicked');
            
            var id = $(this).data('id');
            
            // Load the bio data
            $.ajax({
                url: SITE_URL + 'timechanges/getBio',
                type: 'POST',
                dataType: 'json',
                data: { id: id },
                success: function(data) {
                    console.log('[TIME CHANGES] Record loaded successfully');
                    updateStatus('Record loaded', 'Data Retrieved');
                    
                    $('#biometrics_id').val(data.data.id);
                    $('#date').val(data.data.date);
                    $('#basic2').val(data.data.bio_id).trigger('change');
                    $('#am_in').val(data.data.am_in);
                    $('#am_out').val(data.data.am_out);
                    $('#pm_in').val(data.data.pm_in);
                    $('#pm_out').val(data.data.pm_out);
                    $('#undertime_hours').val(data.data.undertime_hours || 0);
                    $('#undertime_minutes').val(data.data.undertime_minutes || 0);
                    $('#edit_reason').val('');
                    
                    // Open modal
                    console.log('[TIME CHANGES] Opening modal - Month still: ' + $('#month').val());
                    $('#editBio').modal('show');
                    updateStatus('Modal opened', 'Edit Form Ready');
                },
                error: function() {
                    console.error('[TIME CHANGES] Failed to load record');
                    updateStatus('ERROR loading record', 'AJAX Error');
                    alert('Failed to load record data');
                }
            });
            
            return false;
        });
        
        // Handle Delete button clicks with event delegation
        $(document).on('click', '.delete-bio-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            if (!confirm('Are you sure you want to delete this record?')) {
                return false;
            }
            
            var id = $(this).data('id');
            var bioId = $(this).data('bio-id');
            var currentMonth = $('#month').val();
            
            // Redirect to delete with month parameter
            window.location.href = SITE_URL + 'timechanges/delete/' + id + '/' + bioId + '?month=' + currentMonth;
            
            return false;
        });
    });

    function loadBioTable() {
        table = $('#personnelBioTable').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [[1, "desc"]],
            "pageLength": 30,
            "lengthMenu": [[10, 30, 50, 100, -1], [10, 30, 50, 100, "All"]],
            "oLanguage": {
                "sLengthMenu": "_MENU_ Records per page"
            },
            "ajax": {
                "url": SITE_URL + "timechanges/get_personnel_bio",
                "type": "POST",
                "dataType": "json",
                "data": function(d) {
                    d.bio_id = bio_id;
                    // Always get the current value from the month input
                    d.date = $('#month').val() || date;
                }
            },
            "columnDefs": [
                { 
                    "targets": [0, 8],
                    "orderable": false
                }
            ],
            "drawCallback": function() {
                updateBulkEditAlert();
            },
            "deferRender": true,
            "stateSave": false
        });
    }

    function updateBulkEditAlert() {
        var selectedCount = $('.bio-checkbox:checked').length;
        $('#selectedCount').text(selectedCount);
        
        if (selectedCount > 0) {
            $('#bulkEditAlert').slideDown();
        } else {
            $('#bulkEditAlert').slideUp();
        }
    }

    function clearSelection() {
        $('.bio-checkbox').prop('checked', false);
        $('#selectAll').prop('checked', false);
        updateBulkEditAlert();
    }

    function openBulkEditModal() {
        var selectedCount = $('.bio-checkbox:checked').length;
        if (selectedCount === 0) {
            alert('Please select at least one record');
            return;
        }
        $('#bulkEdit').modal('show');
    }

    function submitBulkEdit() {
        var field = $('#bulkField').val();
        var value = $('#bulkValue').val();
        var reason = $('#bulkReason').val();
        
        if (!field || !value || !reason) {
            alert('Please fill in all required fields');
            return;
        }
        
        var selectedIds = [];
        $('.bio-checkbox:checked').each(function() {
            selectedIds.push($(this).data('id'));
        });
        
        if (selectedIds.length === 0) {
            alert('No records selected');
            return;
        }
        
        if (!confirm('Are you sure you want to update ' + selectedIds.length + ' record(s)?')) {
            return;
        }
        
        $.ajax({
            url: SITE_URL + 'timechanges/bulk_update',
            type: 'POST',
            dataType: 'json',
            data: {
                ids: selectedIds,
                field: field,
                value: value,
                reason: reason
            },
            success: function(response) {
                if (response.success) {
                    $('#bulkEdit').modal('hide');
                    
                    // Reload table without losing current page position
                    if (typeof table !== 'undefined') {
                        table.ajax.reload(null, false);
                    }
                    
                    clearSelection();
                    
                    // Show success notification
                    $.notify({
                        icon: 'fas fa-check',
                        message: response.message
                    }, {
                        type: 'success',
                        placement: {
                            from: "top",
                            align: "right"
                        },
                        time: 1000,
                    });
                    
                    // Reset form
                    $('#bulkEditForm')[0].reset();
                    $('#bulkValueGroup').hide();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred while updating records');
            }
        });
    }

    // Quick apply times to selected rows
    function applyQuickTimes() {
        var selectedIds = [];
        $('.bio-checkbox:checked').each(function() {
            selectedIds.push($(this).data('id'));
        });
        
        if (selectedIds.length === 0) {
            alert('Please select at least one record to apply times');
            return;
        }
        
        var am_in = $('#quickAM_In').val();
        var am_out = $('#quickAM_Out').val();
        var pm_in = $('#quickPM_In').val();
        var pm_out = $('#quickPM_Out').val();
        
        if (!am_in) {
            alert('Please set at least Morning In time');
            return;
        }
        
        if (!confirm('Apply these times to ' + selectedIds.length + ' selected record(s)?')) {
            return;
        }
        
        // Apply times to each selected record
        var updates = [];
        var completed = 0;
        var failed = 0;
        
        selectedIds.forEach(function(id) {
            $.ajax({
                url: SITE_URL + 'timechanges/getBio',
                type: 'POST',
                dataType: 'json',
                data: { id: id },
                success: function(bioData) {
                    // Update with quick times
                    $.ajax({
                        url: SITE_URL + 'timechanges/update',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            id: id,
                            date: bioData.data.date,
                            bio_id: bioData.data.bio_id,
                            am_in: am_in,
                            am_out: am_out,
                            pm_in: pm_in,
                            pm_out: pm_out,
                            reason: 'Quick time application'
                        },
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        success: function(response) {
                            completed++;
                            checkCompletion();
                        },
                        error: function() {
                            failed++;
                            checkCompletion();
                        }
                    });
                }
            });
        });
        
        function checkCompletion() {
            if (completed + failed === selectedIds.length) {
                // Reload table
                if (typeof table !== 'undefined') {
                    table.ajax.reload(null, false);
                }
                clearSelection();
                
                // Show notification
                $.notify({
                    icon: 'fas fa-check',
                    message: 'Updated ' + completed + ' record(s)' + (failed > 0 ? ', ' + failed + ' failed' : '')
                }, {
                    type: completed > 0 ? 'success' : 'danger',
                    placement: {
                        from: "top",
                        align: "right"
                    },
                    time: 2000,
                });
            }
        }
    }

    // Make functions globally accessible
    window.clearSelection = clearSelection;
    window.openBulkEditModal = openBulkEditModal;
    window.submitBulkEdit = submitBulkEdit;
    window.applyQuickTimes = applyQuickTimes;
}

// editBio function removed - now handled by event delegation above

function submitEditForm() {
    var formData = {
        id: $('#biometrics_id').val(),
        date: $('#date').val(),
        bio_id: $('#basic2').val(),
        am_in: $('#am_in').val(),
        am_out: $('#am_out').val(),
        pm_in: $('#pm_in').val(),
        pm_out: $('#pm_out').val(),
        undertime_hours: $('#undertime_hours').val(),
        undertime_minutes: $('#undertime_minutes').val(),
        reason: $('#edit_reason').val()
    };
    
    // Validate required fields
    if (!formData.date || !formData.bio_id || !formData.am_in) {
        alert('Please fill in all required fields');
        return;
    }
    
    $.ajax({
        url: SITE_URL + 'timechanges/update',
        type: 'POST',
        dataType: 'json',
        data: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function(response) {
            if (response.success) {
                $('#editBio').modal('hide');
                
                // Reload only the current page of the table without losing position
                if (typeof table !== 'undefined') {
                    table.ajax.reload(null, false);
                }
                
                // Show success notification
                $.notify({
                    icon: 'fas fa-check',
                    message: response.message
                }, {
                    type: 'success',
                    placement: {
                        from: "top",
                        align: "right"
                    },
                    time: 1000,
                });
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            alert('An error occurred while updating the record: ' + error);
        }
    });
}
