<script>
    $(document).ready(function() {
        var orderId = $('#order_id').val();

        // Move to Next Status
        $('#btn_next_status').on('click', function() {
            var $btn = $(this);
            var nextStatus = $btn.data('next-status');
            var nextLabel = $btn.data('next-label');

            Swal.fire({
                title: 'Update Order Status',
                text: 'Move this order to "' + nextLabel + '"?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, update it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $btn.prop('disabled', true).html('<i class="spinner-border spinner-border-sm me-1"></i> Updating...');

                    $.ajax({
                        url: base_url + 'admin_root/orders/update_status',
                        type: 'POST',
                        data: {
                            order_id: orderId,
                            new_status: nextStatus
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status == 200) {
                                Swal.fire({
                                    title: 'Updated!',
                                    text: response.message,
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(function() {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Error!', response.message, 'error');
                                $btn.prop('disabled', false).html('<i class="uil uil-arrow-right me-1"></i> Move to ' + nextLabel);
                            }
                        },
                        error: function() {
                            Swal.fire('Error!', 'Something went wrong. Please try again.', 'error');
                            $btn.prop('disabled', false).html('<i class="uil uil-arrow-right me-1"></i> Move to ' + nextLabel);
                        }
                    });
                }
            });
        });

        // Cancel Order
        $('#btn_cancel_order').on('click', function() {
            var $btn = $(this);

            Swal.fire({
                title: 'Cancel Order',
                text: 'Are you sure you want to cancel this order?',
                icon: 'warning',
                input: 'textarea',
                inputLabel: 'Cancellation Reason',
                inputPlaceholder: 'Enter reason for cancellation...',
                inputAttributes: {
                    'aria-label': 'Reason for cancellation'
                },
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, cancel order!',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Please provide a reason for cancellation';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $btn.prop('disabled', true).html('<i class="spinner-border spinner-border-sm me-1"></i> Cancelling...');

                    $.ajax({
                        url: base_url + 'admin_root/orders/cancel_order',
                        type: 'POST',
                        data: {
                            order_id: orderId,
                            reason: result.value
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status == 200) {
                                Swal.fire({
                                    title: 'Cancelled!',
                                    text: response.message,
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(function() {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Error!', response.message, 'error');
                                $btn.prop('disabled', false).html('<i class="uil uil-times-circle me-1"></i> Cancel Order');
                            }
                        },
                        error: function() {
                            Swal.fire('Error!', 'Something went wrong. Please try again.', 'error');
                            $btn.prop('disabled', false).html('<i class="uil uil-times-circle me-1"></i> Cancel Order');
                        }
                    });
                }
            });
        });

        // Issue Refund
        $('#btn_refund_order').on('click', function() {
            var $btn = $(this);
            var btnOriginalHtml = $btn.html();

            Swal.fire({
                title: 'Issue Refund',
                html: '<p>This will refund the full amount to the customer via Stripe.</p><p class="text-danger"><strong>This action cannot be undone!</strong></p>',
                icon: 'warning',
                input: 'textarea',
                inputLabel: 'Refund Reason',
                inputPlaceholder: 'Enter reason for refund...',
                inputAttributes: {
                    'aria-label': 'Reason for refund'
                },
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, issue refund!',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Please provide a reason for the refund';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $btn.prop('disabled', true).html('<i class="spinner-border spinner-border-sm me-1"></i> Processing refund...');

                    $.ajax({
                        url: base_url + 'admin_root/orders/issue_refund',
                        type: 'POST',
                        data: {
                            order_id: orderId,
                            reason: result.value
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status == 200) {
                                Swal.fire({
                                    title: 'Refund Processed!',
                                    text: response.message,
                                    icon: 'success',
                                    timer: 3000,
                                    showConfirmButton: true
                                }).then(function() {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Error!', response.message, 'error');
                                $btn.prop('disabled', false).html(btnOriginalHtml);
                            }
                        },
                        error: function() {
                            Swal.fire('Error!', 'Something went wrong. Please try again.', 'error');
                            $btn.prop('disabled', false).html(btnOriginalHtml);
                        }
                    });
                }
            });
        });
        // Generate Review Link
        $('#btn_generate_review_link').on('click', function() {
            var $btn = $(this);
            $btn.prop('disabled', true).html('<i class="spinner-border spinner-border-sm me-1"></i> Generating...');

            $.ajax({
                url: base_url + 'admin_root/reviews/generate_link',
                type: 'POST',
                data: { order_id: orderId },
                dataType: 'json',
                success: function(response) {
                    if (response.status == 200) {
                        $('#generated_review_link').val(response.link);
                        $('#feedback_link_section').addClass('d-none');
                        $('#feedback_link_result').removeClass('d-none');

                        if (response.submitted) {
                            $('#feedback_status_info').html(
                                '<a href="' + base_url + 'admin_root/reviews/view_review/' + response.review_id + '" class="btn btn-sm btn-soft-success w-100">' +
                                '<i class="uil uil-eye me-1"></i> Feedback Submitted - View Review</a>'
                            );
                        } else {
                            $('#feedback_status_info').html(
                                '<small class="text-muted"><i class="uil uil-clock me-1"></i>Awaiting customer feedback</small>'
                            );
                        }
                    } else {
                        Swal.fire('Error!', response.message, 'error');
                        $btn.prop('disabled', false).html('<i class="uil uil-link me-1"></i> Generate Review Link');
                    }
                },
                error: function() {
                    Swal.fire('Error!', 'Something went wrong.', 'error');
                    $btn.prop('disabled', false).html('<i class="uil uil-link me-1"></i> Generate Review Link');
                }
            });
        });

        // Copy Review Link
        $('#btn_copy_review_link').on('click', function() {
            var input = document.getElementById('generated_review_link');
            input.select();
            document.execCommand('copy');
            toastr["success"]("Success", "Review link copied to clipboard!");
        });

        // Auto-generate link if order is delivered (check on page load)
        if ($('#btn_generate_review_link').length) {
            $('#btn_generate_review_link').trigger('click');
        }
    });
</script>
