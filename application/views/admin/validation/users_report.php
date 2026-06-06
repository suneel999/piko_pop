<script>
    $(document).ready(function() {
        // Initialize Flatpickr for date inputs
        $('.flatpickr-date').flatpickr({
            dateFormat: 'Y-m-d',
            allowInput: true
        });

        // Initialize DataTable with empty data first
        var usersTable = $('#users_report_data').DataTable({
            dom: 'lBfrtip',
            buttons: {
                dom: {
                    button: {
                        tag: 'button',
                        className: ''
                    }
                },
                buttons: [{
                    extend: 'excel',
                    className: 'btn btn-success btn-sm',
                    titleAttr: 'Export to Excel',
                    text: '<i class="uil uil-file-export me-1"></i> Export to Excel',
                    filename: 'Users_Report_' + new Date().toISOString().slice(0, 10),
                    title: 'Users Report',
                    extension: '.xlsx',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
                    }
                }]
            },
            "lengthMenu": [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"]
            ],
            "order": [[4, "desc"]],
            "language": {
                "emptyTable": "Select filters and click 'Get Report' to load data"
            }
        });

        // Load all data on page load
        loadUsersReport();

        // Form submission handler
        $('#users_report_form').on('submit', function(e) {
            e.preventDefault();
            loadUsersReport();
        });

        // Clear filters
        $('#clear_filters').on('click', function() {
            $('#status').val('').trigger('change');
            $('#date_from').val('');
            $('#date_to').val('');
            loadUsersReport();
        });

        function loadUsersReport() {
            // Destroy existing DataTable
            if ($.fn.DataTable.isDataTable('#users_report_data')) {
                $('#users_report_data').DataTable().destroy();
            }

            // Reinitialize with AJAX
            $('#users_report_data').DataTable({
                dom: 'lBfrtip',
                buttons: {
                    dom: {
                        button: {
                            tag: 'button',
                            className: ''
                        }
                    },
                    buttons: [{
                        extend: 'excel',
                        className: 'btn btn-success btn-sm',
                        titleAttr: 'Export to Excel',
                        text: '<i class="uil uil-file-export me-1"></i> Export to Excel',
                        filename: 'Users_Report_' + new Date().toISOString().slice(0, 10),
                        title: 'Users Report',
                        extension: '.xlsx',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
                        }
                    }]
                },
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                "processing": true,
                "ajax": {
                    "url": base_url + 'admin_root/reports/users_data',
                    "type": 'POST',
                    "data": function(d) {
                        return $('#users_report_form').serialize();
                    },
                    "dataSrc": function(json) {
                        if (json.status == 200) {
                            // Update summary cards
                            if (json.totals) {
                                $('#summary_total_users').text(json.totals.total_users);
                                $('#summary_total_active').text(json.totals.total_active);
                                $('#summary_total_verified').text(json.totals.total_verified);
                            }

                            // Calculate footer totals
                            var totalOrders = 0;
                            var totalSpent = 0;
                            json.data.forEach(function(row) {
                                totalOrders += parseInt(row[6]) || 0;
                                // Remove currency symbol and parse
                                var spent = row[7].replace('₹', '').replace(/,/g, '');
                                totalSpent += parseFloat(spent) || 0;
                            });
                            $('#footer_total_orders').text(totalOrders);
                            $('#footer_total_spent').text('₹' + totalSpent.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));

                            return json.data;
                        } else {
                            // Reset summary
                            $('#summary_total_users').text('0');
                            $('#summary_total_active').text('0');
                            $('#summary_total_verified').text('0');
                            $('#footer_total_orders').text('-');
                            $('#footer_total_spent').text('-');
                            return [];
                        }
                    }
                },
                "order": [[4, "desc"]],
                "language": {
                    "emptyTable": "No users found matching the selected filters",
                    "processing": "<div class='spinner-border text-primary' role='status'><span class='visually-hidden'>Loading...</span></div>"
                },
                "drawCallback": function(settings) {
                    $('#submit_button').removeAttr('disabled').removeClass('disabled');
                }
            });
        }
    });
</script>
