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

        // Initialize Flatpickr
        $('#date_from').flatpickr({ dateFormat: 'Y-m-d', allowInput: true, defaultDate: firstDay });
        $('#date_to').flatpickr({ dateFormat: 'Y-m-d', allowInput: true, defaultDate: lastDay });

        // Initialize DataTable
        var reviewsTable = $('#reviews_table').DataTable({
            dom: 'lBfrtip',
            buttons: {
                dom: { button: { tag: 'button', className: '' } },
                buttons: [{
                    extend: 'excel',
                    className: 'btn btn-success btn-sm',
                    text: '<i class="uil uil-file-export me-1"></i> Export to Excel',
                    filename: 'Customer_Reviews_' + new Date().toISOString().slice(0, 10),
                    title: 'Customer Reviews',
                    exportOptions: { columns: [0, 1, 2, 3, 4, 5] }
                }, {
                    extend: 'pdf',
                    className: 'btn btn-danger btn-sm ms-1',
                    text: '<i class="uil uil-file-download me-1"></i> Export to PDF',
                    filename: 'Customer_Reviews_' + new Date().toISOString().slice(0, 10),
                    title: 'Customer Reviews',
                    exportOptions: { columns: [0, 1, 2, 3, 4, 5] }
                }, {
                    extend: 'print',
                    className: 'btn btn-info btn-sm ms-1',
                    text: '<i class="uil uil-print me-1"></i> Print',
                    title: 'Customer Reviews',
                    exportOptions: { columns: [0, 1, 2, 3, 4, 5] }
                }]
            },
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "order": [[5, "desc"]],
            "language": { "emptyTable": "Select filters and click 'Get Report' to load data" }
        });

        // Load data on page load
        loadReviews();

        // Filter form submit
        $('#reviews_filter_form').on('submit', function(e) {
            e.preventDefault();
            loadReviews();
        });

        // Clear filters
        $('#clear_filters').on('click', function() {
            $('#rating').val('').trigger('change');
            $('#date_from').val(formatDate(firstDay));
            $('#date_to').val(formatDate(lastDay));
            $('#date_from').flatpickr().setDate(firstDay);
            $('#date_to').flatpickr().setDate(lastDay);
            loadReviews();
        });

        function loadReviews() {
            if ($.fn.DataTable.isDataTable('#reviews_table')) {
                $('#reviews_table').DataTable().destroy();
            }

            $('#reviews_table').DataTable({
                dom: 'lBfrtip',
                buttons: {
                    dom: { button: { tag: 'button', className: '' } },
                    buttons: [{
                        extend: 'excel',
                        className: 'btn btn-success btn-sm',
                        text: '<i class="uil uil-file-export me-1"></i> Export to Excel',
                        filename: 'Customer_Reviews_' + new Date().toISOString().slice(0, 10),
                        title: 'Customer Reviews',
                        exportOptions: { columns: [0, 1, 2, 3, 4, 5] }
                    }, {
                        extend: 'pdf',
                        className: 'btn btn-danger btn-sm ms-1',
                        text: '<i class="uil uil-file-download me-1"></i> Export to PDF',
                        filename: 'Customer_Reviews_' + new Date().toISOString().slice(0, 10),
                        title: 'Customer Reviews',
                        exportOptions: { columns: [0, 1, 2, 3, 4, 5] }
                    }, {
                        extend: 'print',
                        className: 'btn btn-info btn-sm ms-1',
                        text: '<i class="uil uil-print me-1"></i> Print',
                        title: 'Customer Reviews',
                        exportOptions: { columns: [0, 1, 2, 3, 4, 5] }
                    }]
                },
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                "processing": true,
                "ajax": {
                    "url": base_url + 'admin_root/reviews/reviews_data',
                    "type": 'POST',
                    "data": function(d) {
                        return $('#reviews_filter_form').serialize();
                    },
                    "dataSrc": function(json) {
                        if (json.status == 200) {
                            if (json.totals) {
                                $('#summary_total_reviews').text(json.totals.total_reviews);
                                $('#summary_avg_rating').text(json.totals.avg_rating + ' / 5');
                                $('#summary_five_star').text(json.totals.five_star_count);
                                $('#summary_recommend_rate').text(json.totals.recommend_rate + '%');
                            }
                            return json.data;
                        } else {
                            $('#summary_total_reviews').text('0');
                            $('#summary_avg_rating').text('0');
                            $('#summary_five_star').text('0');
                            $('#summary_recommend_rate').text('0%');
                            return [];
                        }
                    }
                },
                "order": [[5, "desc"]],
                "columnDefs": [{ "orderable": false, "targets": [6] }],
                "language": {
                    "emptyTable": "No reviews found matching the selected filters",
                    "processing": "<div class='spinner-border text-primary' role='status'><span class='visually-hidden'>Loading...</span></div>"
                },
                "drawCallback": function() {
                    $('#submit_button').removeAttr('disabled').removeClass('disabled');
                }
            });
        }

        // Copy review link
        $(document).on('click', '.copy-review-link', function() {
            var token = $(this).data('token');
            var link = base_url + 'feedback/' + token;
            var temp = $('<input>');
            $('body').append(temp);
            temp.val(link).select();
            document.execCommand('copy');
            temp.remove();
            toastr["success"]("Success", "Review link copied to clipboard!");
        });

        // Delete review
        $(document).on('click', '.delete-review', function() {
            var reviewId = $(this).data('id');

            Swal.fire({
                title: "Are you sure?",
                text: "This review will be deleted.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#f46a6a",
                cancelButtonColor: "#34c38f",
                confirmButtonText: "Yes, delete it!"
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: base_url + 'admin_root/reviews/delete_review',
                        type: "POST",
                        data: { id: reviewId },
                        success: function(result) {
                            var obj = JSON.parse(result);
                            if (obj.status == 200) {
                                toastr["success"]("Success", obj.message);
                                loadReviews();
                            } else {
                                toastr["error"]("Error", obj.message);
                            }
                        },
                        error: function() {
                            toastr["error"]("Error", "Something went wrong.");
                        }
                    });
                }
            });
        });
    });
</script>
