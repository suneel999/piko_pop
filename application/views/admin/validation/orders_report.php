<script>
    $(document).ready(function() {
        // Get current month start and end dates
        var now = new Date();
        var firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
        var lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);

        var formatDate = function(date) {
            var year = date.getFullYear();
            var month = ('0' + (date.getMonth() + 1)).slice(-2);
            var day = ('0' + date.getDate()).slice(-2);
            return year + '-' + month + '-' + day;
        };

        // Set default values
        $('#date_from').val(formatDate(firstDay));
        $('#date_to').val(formatDate(lastDay));

        // Initialize Flatpickr for date inputs
        $('#date_from').flatpickr({
            dateFormat: 'Y-m-d',
            allowInput: true,
            defaultDate: firstDay
        });

        $('#date_to').flatpickr({
            dateFormat: 'Y-m-d',
            allowInput: true,
            defaultDate: lastDay
        });

        // Initialize DataTable with empty data first
        var ordersTable = $('#orders_report_data').DataTable({
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
                    filename: 'Order_Report_' + new Date().toISOString().slice(0, 10),
                    title: 'Order Report',
                    extension: '.xlsx',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7] // Exclude action column
                    }
                }]
            },
            "lengthMenu": [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"]
            ],
            "order": [[1, "desc"], [2, "desc"]],
            "language": {
                "emptyTable": "Select filters and click 'Get Report' to load data"
            }
        });

        // Load all data on page load
        loadOrdersReport();

        // Form submission handler
        $('#orders_report_form').on('submit', function(e) {
            e.preventDefault();
            loadOrdersReport();
        });

        // Clear filters - reset to current month
        $('#clear_filters').on('click', function() {
            $('#status').val('').trigger('change');
            $('#payment_status').val('').trigger('change');
            $('#date_from').val(formatDate(firstDay));
            $('#date_to').val(formatDate(lastDay));
            $('#date_from').flatpickr().setDate(firstDay);
            $('#date_to').flatpickr().setDate(lastDay);
            loadOrdersReport();
        });

        function loadOrdersReport() {
            // Destroy existing DataTable
            if ($.fn.DataTable.isDataTable('#orders_report_data')) {
                $('#orders_report_data').DataTable().destroy();
            }

            // Reinitialize with AJAX
            $('#orders_report_data').DataTable({
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
                        filename: 'Order_Report_' + new Date().toISOString().slice(0, 10),
                        title: 'Order Report',
                        extension: '.xlsx',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7]
                        }
                    }]
                },
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                "processing": true,
                "ajax": {
                    "url": base_url + 'admin_root/orders/report_data',
                    "type": 'POST',
                    "data": function(d) {
                        return $('#orders_report_form').serialize();
                    },
                    "dataSrc": function(json) {
                        if (json.status == 200) {
                            // Update summary cards
                            if (json.totals) {
                                $('#summary_total_orders').text(json.totals.total_orders);
                                $('#summary_total_amount').text(json.totals.total_amount);
                                $('#summary_total_paid').text(json.totals.total_paid);
                                $('#footer_total_amount').text('₹' + json.totals.total_amount);
                            }
                            return json.data;
                        } else {
                            // Reset summary
                            $('#summary_total_orders').text('0');
                            $('#summary_total_amount').text('0.00');
                            $('#summary_total_paid').text('0.00');
                            $('#footer_total_amount').text('-');
                            return [];
                        }
                    }
                },
                "order": [[1, "desc"], [2, "desc"]],
                "language": {
                    "emptyTable": "No orders found matching the selected filters",
                    "processing": "<div class='spinner-border text-primary' role='status'><span class='visually-hidden'>Loading...</span></div>"
                },
                "drawCallback": function(settings) {
                    $('#submit_button').removeAttr('disabled').removeClass('disabled');
                }
            });
        }
    });
</script>
