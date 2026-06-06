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
        var salesTable = $('#sales_report_data').DataTable({
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
                    filename: 'Sales_Report_' + new Date().toISOString().slice(0, 10),
                    title: 'Sales Report',
                    extension: '.xlsx',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
                    }
                }, {
                    extend: 'pdf',
                    className: 'btn btn-danger btn-sm ms-1',
                    titleAttr: 'Export to PDF',
                    text: '<i class="uil uil-file-download me-1"></i> Export to PDF',
                    filename: 'Sales_Report_' + new Date().toISOString().slice(0, 10),
                    title: 'Sales Report',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7]
                    }
                }, {
                    extend: 'print',
                    className: 'btn btn-info btn-sm ms-1',
                    titleAttr: 'Print Report',
                    text: '<i class="uil uil-print me-1"></i> Print',
                    title: 'Sales Report',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7]
                    }
                }]
            },
            "lengthMenu": [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"]
            ],
            "order": [[1, "desc"]],
            "language": {
                "emptyTable": "Select filters and click 'Get Report' to load data"
            }
        });

        // Load all data on page load
        loadSalesReport();

        // Form submission handler
        $('#sales_report_form').on('submit', function(e) {
            e.preventDefault();
            loadSalesReport();
        });

        // Clear filters - reset to current month
        $('#clear_filters').on('click', function() {
            $('#payment_method').val('').trigger('change');
            $('#date_from').val(formatDate(firstDay));
            $('#date_to').val(formatDate(lastDay));
            $('#date_from').flatpickr().setDate(firstDay);
            $('#date_to').flatpickr().setDate(lastDay);
            loadSalesReport();
        });

        function formatCurrency(amount) {
            return '₹' + parseFloat(amount).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }

        function loadSalesReport() {
            // Destroy existing DataTable
            if ($.fn.DataTable.isDataTable('#sales_report_data')) {
                $('#sales_report_data').DataTable().destroy();
            }

            // Reinitialize with AJAX
            $('#sales_report_data').DataTable({
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
                        filename: 'Sales_Report_' + new Date().toISOString().slice(0, 10),
                        title: 'Sales Report',
                        extension: '.xlsx',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
                        }
                    }, {
                        extend: 'pdf',
                        className: 'btn btn-danger btn-sm ms-1',
                        titleAttr: 'Export to PDF',
                        text: '<i class="uil uil-file-download me-1"></i> Export to PDF',
                        filename: 'Sales_Report_' + new Date().toISOString().slice(0, 10),
                        title: 'Sales Report',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7]
                        }
                    }, {
                        extend: 'print',
                        className: 'btn btn-info btn-sm ms-1',
                        titleAttr: 'Print Report',
                        text: '<i class="uil uil-print me-1"></i> Print',
                        title: 'Sales Report',
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
                    "url": base_url + 'admin_root/reports/sales_data',
                    "type": 'POST',
                    "data": function(d) {
                        return $('#sales_report_form').serialize();
                    },
                    "dataSrc": function(json) {
                        if (json.status == 200) {
                            // Update summary cards
                            if (json.totals) {
                                $('#summary_total_sales').text(formatCurrency(json.totals.total_sales));
                                $('#summary_total_orders').text(json.totals.total_orders);
                                $('#summary_avg_order').text(formatCurrency(json.totals.avg_order_value));
                                $('#summary_total_discount').text(formatCurrency(json.totals.total_discount));
                                $('#summary_total_items').text(json.totals.total_items);
                                $('#summary_subtotal').text(formatCurrency(json.totals.total_subtotal));
                                $('#summary_shipping').text(formatCurrency(json.totals.total_shipping));
                            }

                            // Update footer totals
                            $('#footer_subtotal').text(formatCurrency(json.totals.total_subtotal));
                            $('#footer_discount').text(formatCurrency(json.totals.total_discount));
                            $('#footer_shipping').text(formatCurrency(json.totals.total_shipping));
                            $('#footer_total').text(formatCurrency(json.totals.total_sales));

                            return json.data;
                        } else {
                            // Reset summary
                            $('#summary_total_sales').text('₹0.00');
                            $('#summary_total_orders').text('0');
                            $('#summary_avg_order').text('₹0.00');
                            $('#summary_total_discount').text('₹0.00');
                            $('#summary_total_items').text('0');
                            $('#summary_subtotal').text('₹0.00');
                            $('#summary_shipping').text('₹0.00');
                            $('#footer_subtotal').text('-');
                            $('#footer_discount').text('-');
                            $('#footer_shipping').text('-');
                            $('#footer_total').text('-');
                            return [];
                        }
                    }
                },
                "order": [[1, "desc"]],
                "language": {
                    "emptyTable": "No sales found matching the selected filters",
                    "processing": "<div class='spinner-border text-primary' role='status'><span class='visually-hidden'>Loading...</span></div>"
                },
                "drawCallback": function(settings) {
                    $('#submit_button').removeAttr('disabled').removeClass('disabled');
                }
            });
        }
    });
</script>
