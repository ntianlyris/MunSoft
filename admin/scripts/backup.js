$(document).ready(function() {
    
    // Initial Load
    loadBackupLogs();

    // Attach Generate Button Process
    $('#btnGenerateBackup').on('click', function(e) {
        e.preventDefault();
        
        let btn = $(this);
        let progress = $('#backupProgress');

        Swal.fire({
            title: 'Generate Full Database Snapshot?',
            text: "This process may take a while depending on the size of the tables and logs.",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, start extraction!'
        }).then((result) => {
            if (result.isConfirmed) {
                // UI Disable state during stream dump
                btn.prop('disabled', true);
                progress.slideDown();
                
                $.ajax({
                    url: 'backup_handler.php',
                    type: 'POST',
                    data: { action: 'generate_backup' },
                    dataType: 'json',
                    success: function(response) {
                        btn.prop('disabled', false);
                        progress.slideUp();
                        
                        if (response.status === 'success') {
                            Swal.fire('Snapshot Captured!', response.message, 'success');
                            loadBackupLogs(); // reload UI list table
                        } else {
                            Swal.fire('Failed', response.message, 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        btn.prop('disabled', false);
                        progress.slideUp();
                        Swal.fire('Network Error', 'The request timed out or the server failed: ' + error, 'error');
                    }
                });
            }
        });
    });

    // Attach File Upload Restore Event Handler
    $('#restoreForm').on('submit', function(e) {
        e.preventDefault();
        
        let fileInput = $('#backupFile')[0].files[0];
        if (!fileInput) {
            Swal.fire('Missing File', 'Please select an SQL file to restore.', 'warning');
            return;
        }

        let formData = new FormData();
        formData.append('backup_file', fileInput);
        formData.append('action', 'restore_backup');

        let submitBtn = $('#btnRestoreBackup');
        let progress = $('#restoreProgress');

        Swal.fire({
            title: 'WARNING: OVERWRITE DATABASE?',
            text: "This action cannot be undone! Your live database tables will be completely DROP and restored to the exact snapshot provided in your file.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, Restore and Overwrite!'
        }).then((result) => {
            if (result.isConfirmed) {
                
                // Block DOM state since executing massive SQL statements
                submitBtn.prop('disabled', true);
                progress.slideDown();

                $.ajax({
                    url: 'backup_handler.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        submitBtn.prop('disabled', false);
                        progress.slideUp();
                        
                        if (response.status === 'success') {
                            Swal.fire({
                                title: 'Database Restored!',
                                text: response.message,
                                icon: 'success',
                                confirmButtonText: 'Reload Dashboard'
                            }).then(() => {
                                window.location.href = './'; // redirect to prevent stale view states
                            });
                        } else {
                            Swal.fire('Restore Failed', response.message, 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        submitBtn.prop('disabled', false);
                        progress.slideUp();
                        Swal.fire('CRITICAL NETWORK ERROR', 'The restore stream was interrupted. Check your tables for corruption: ' + error, 'error');
                    }
                });
            }
        });
    });

    // Sub-routine to render server backend directories into Table List View
    function loadBackupLogs() {
        $.ajax({
            url: 'backup_handler.php',
            type: 'POST',
            data: { action: 'list_backups' },
            success: function(response) {
                $('#backupListTable').html(response);
            },
            error: function() {
                $('#backupListTable').html('<tr><td colspan="4" class="text-center text-danger">Failed to fetch backups list constraint.</td></tr>');
            }
        });
    }

});
