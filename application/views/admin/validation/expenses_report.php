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

        // Initialize Flatpickr for filter date inputs
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

        // Initialize Flatpickr for modal expense date (default today)
        $('#expense_date').flatpickr({
            dateFormat: 'Y-m-d',
            allowInput: true,
            defaultDate: new Date()
        });

        // Initialize DataTable with empty data first
        var expensesTable = $('#expenses_report_data').DataTable({
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
                    filename: 'Expenses_Report_' + new Date().toISOString().slice(0, 10),
                    title: 'Expenses Report',
                    extension: '.xlsx',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6]
                    }
                }, {
                    extend: 'pdf',
                    className: 'btn btn-danger btn-sm ms-1',
                    titleAttr: 'Export to PDF',
                    text: '<i class="uil uil-file-download me-1"></i> Export to PDF',
                    filename: 'Expenses_Report_' + new Date().toISOString().slice(0, 10),
                    title: 'Expenses Report',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6]
                    }
                }, {
                    extend: 'print',
                    className: 'btn btn-info btn-sm ms-1',
                    titleAttr: 'Print Report',
                    text: '<i class="uil uil-print me-1"></i> Print',
                    title: 'Expenses Report',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6]
                    }
                }]
            },
            "lengthMenu": [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"]
            ],
            "order": [[3, "desc"]],
            "language": {
                "emptyTable": "Select filters and click 'Get Report' to load data"
            }
        });

        // Load all data on page load
        loadExpensesReport();

        // Form submission handler
        $('#expenses_report_form').on('submit', function(e) {
            e.preventDefault();
            loadExpensesReport();
        });

        // Clear filters - reset to current month
        $('#clear_filters').on('click', function() {
            $('#category').val('').trigger('change');
            $('#date_from').val(formatDate(firstDay));
            $('#date_to').val(formatDate(lastDay));
            $('#date_from').flatpickr().setDate(firstDay);
            $('#date_to').flatpickr().setDate(lastDay);
            loadExpensesReport();
        });

        function formatCurrency(amount) {
            return '₹' + parseFloat(amount).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }

        function loadExpensesReport() {
            // Destroy existing DataTable
            if ($.fn.DataTable.isDataTable('#expenses_report_data')) {
                $('#expenses_report_data').DataTable().destroy();
            }

            // Reinitialize with AJAX
            $('#expenses_report_data').DataTable({
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
                        filename: 'Expenses_Report_' + new Date().toISOString().slice(0, 10),
                        title: 'Expenses Report',
                        extension: '.xlsx',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6]
                        }
                    }, {
                        extend: 'pdf',
                        className: 'btn btn-danger btn-sm ms-1',
                        titleAttr: 'Export to PDF',
                        text: '<i class="uil uil-file-download me-1"></i> Export to PDF',
                        filename: 'Expenses_Report_' + new Date().toISOString().slice(0, 10),
                        title: 'Expenses Report',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6]
                        }
                    }, {
                        extend: 'print',
                        className: 'btn btn-info btn-sm ms-1',
                        titleAttr: 'Print Report',
                        text: '<i class="uil uil-print me-1"></i> Print',
                        title: 'Expenses Report',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6]
                        }
                    }]
                },
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                "processing": true,
                "ajax": {
                    "url": base_url + 'admin_root/reports/expenses_data',
                    "type": 'POST',
                    "data": function(d) {
                        return $('#expenses_report_form').serialize();
                    },
                    "dataSrc": function(json) {
                        if (json.status == 200) {
                            // Update summary cards
                            if (json.totals) {
                                $('#summary_total_expenses').text(formatCurrency(json.totals.total_expenses));
                                $('#summary_total_count').text(json.totals.total_count);
                                $('#summary_avg_expense').text(formatCurrency(json.totals.avg_expense));
                                $('#summary_highest_expense').text(formatCurrency(json.totals.highest_expense));
                            }

                            // Update footer total
                            $('#footer_total').text(formatCurrency(json.totals.total_expenses));

                            return json.data;
                        } else {
                            // Reset summary
                            $('#summary_total_expenses').text('₹0.00');
                            $('#summary_total_count').text('0');
                            $('#summary_avg_expense').text('₹0.00');
                            $('#summary_highest_expense').text('₹0.00');
                            $('#footer_total').text('-');
                            return [];
                        }
                    }
                },
                "order": [[3, "desc"]],
                "columnDefs": [{
                    "orderable": false,
                    "targets": [7]
                }],
                "language": {
                    "emptyTable": "No expenses found matching the selected filters",
                    "processing": "<div class='spinner-border text-primary' role='status'><span class='visually-hidden'>Loading...</span></div>"
                },
                "drawCallback": function(settings) {
                    $('#submit_button').removeAttr('disabled').removeClass('disabled');
                }
            });
        }

        // Add Expense Form Submit
        $('#add_expense_form').on('submit', function(e) {
            e.preventDefault();

            var $form = $(this);
            var $btn = $('#save_expense_btn');

            // Basic validation
            if (!$('#expense_title').val().trim()) {
                toastr["error"]("Error", "Please enter expense title");
                return;
            }
            if (!$('#expense_category').val()) {
                toastr["error"]("Error", "Please select a category");
                return;
            }
            if (!$('#expense_date').val()) {
                toastr["error"]("Error", "Please select expense date");
                return;
            }
            if (!$('#expense_amount').val() || parseFloat($('#expense_amount').val()) <= 0) {
                toastr["error"]("Error", "Please enter a valid amount");
                return;
            }

            $btn.attr('disabled', true).html('<i class="uil-spinner-alt spinner-border spinner-border-sm me-1"></i> Saving...');

            $.ajax({
                url: $form.attr('action'),
                type: "POST",
                data: $form.serialize(),
                success: function(result) {
                    var obj = JSON.parse(result);

                    if (obj.status == 200) {
                        toastr["success"]("Success", obj.message);
                        // Reset form
                        $form[0].reset();
                        $('#expense_date').flatpickr().setDate(new Date());
                        // Close modal
                        $('#addExpenseModal').modal('hide');
                        // Reload report
                        loadExpensesReport();
                    } else {
                        toastr["error"]("Error", obj.message);
                    }

                    $btn.attr('disabled', false).html('<i class="uil-check me-1"></i> Save Expense');
                },
                error: function() {
                    toastr["error"]("Error", "Something went wrong. Please try again.");
                    $btn.attr('disabled', false).html('<i class="uil-check me-1"></i> Save Expense');
                }
            });
        });

        // Delete expense handler
        $(document).on('click', '.delete-expense', function() {
            var expenseId = $(this).data('id');

            Swal.fire({
                title: "Are you sure?",
                text: "This expense entry will be deleted.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#f46a6a",
                cancelButtonColor: "#34c38f",
                confirmButtonText: "Yes, delete it!"
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: base_url + 'admin_root/reports/delete_expense',
                        type: "POST",
                        data: { id: expenseId },
                        success: function(result) {
                            var obj = JSON.parse(result);

                            if (obj.status == 200) {
                                toastr["success"]("Success", obj.message);
                                loadExpensesReport();
                            } else {
                                toastr["error"]("Error", obj.message);
                            }
                        },
                        error: function() {
                            toastr["error"]("Error", "Something went wrong. Please try again.");
                        }
                    });
                }
            });
        });

        // Reset modal form when modal is closed
        $('#addExpenseModal').on('hidden.bs.modal', function() {
            $('#add_expense_form')[0].reset();
            $('#expense_date').flatpickr().setDate(new Date());
        });
    });
</script>
