<script>
    $(document).ready(function() {
        // Initialize DataTable
        var ordersTable = $('#orders_data').DataTable({
            "lengthMenu": [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"]
            ],
            "order": [[1, "desc"]],
            "language": {
                "emptyTable": "No orders found",
                "processing": "<div class='spinner-border text-primary' role='status'><span class='visually-hidden'>Loading...</span></div>"
            }
        });

        // Load data on page load
        loadOrdersData();

        // Form submission handler
        $('#orders_filter_form').on('submit', function(e) {
            e.preventDefault();
            loadOrdersData();
        });

        function loadOrdersData() {
            // Destroy existing DataTable
            if ($.fn.DataTable.isDataTable('#orders_data')) {
                $('#orders_data').DataTable().destroy();
            }

            // Reinitialize with AJAX
            $('#orders_data').DataTable({
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                "processing": true,
                "ajax": {
                    "url": base_url + 'admin_root/orders/pending_data',
                    "type": 'POST',
                    "data": function(d) {
                        return $('#orders_filter_form').serialize();
                    },
                    "dataSrc": function(json) {
                        if (json.status == 200) {
                            return json.data;
                        } else {
                            return [];
                        }
                    }
                },
                "order": [[1, "desc"]],
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
