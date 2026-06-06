<script>
    $(document).ready(function() {
        // Initialize DataTable with empty data first
        var inventoryTable = $('#inventory_data').DataTable({
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
                    filename: 'Inventory_Report_' + new Date().toISOString().slice(0, 10),
                    title: 'Inventory Report',
                    extension: '.xlsx',
                    exportOptions: {
                        columns: ':not(:last-child)' // Exclude last column if needed
                    }
                }]
            },
            "lengthMenu": [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"]
            ],
            "order": [[0, "asc"]],
            "language": {
                "emptyTable": "Select filters and click 'Get Report' to load data"
            }
        });

        // Form submission handler
        $('#inventory_report_form').on('submit', function(e) {
            e.preventDefault();

            // Destroy existing DataTable
            if ($.fn.DataTable.isDataTable('#inventory_data')) {
                $('#inventory_data').DataTable().destroy();
            }

            // Reinitialize with AJAX
            $('#inventory_data').DataTable({
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
                        filename: 'Inventory_Report_' + new Date().toISOString().slice(0, 10),
                        title: 'Inventory Report',
                        extension: '.xlsx'
                    }]
                },
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                "processing": true,
                "ajax": {
                    "url": base_url + 'admin_root/inventory/report_data',
                    "type": 'POST',
                    "data": function(d) {
                        return $('#inventory_report_form').serialize();
                    },
                    "dataSrc": function(json) {
                        if (json.status == 200) {
                            // Update footer totals
                            if (json.totals) {
                                $('#total_stock').text(json.totals.total_stock);
                                $('#total_value').text('₹' + json.totals.total_value);
                            }
                            return json.data;
                        } else {
                            $('#total_stock').text('-');
                            $('#total_value').text('-');
                            return [];
                        }
                    }
                },
                "order": [[0, "asc"]],
                "language": {
                    "emptyTable": "No products found matching the selected filters",
                    "processing": "<div class='spinner-border text-primary' role='status'><span class='visually-hidden'>Loading...</span></div>"
                },
                "drawCallback": function(settings) {
                    $('#submit_button').removeAttr('disabled').removeClass('disabled');
                }
            });
        });
    });
</script>
