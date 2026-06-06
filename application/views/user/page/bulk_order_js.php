<!-- Bulk Order Page Script -->
<script>
$(document).ready(function() {
    'use strict';

    // jQuery Validation for bulk order form
    $('#bulkOrderForm').validate({
        rules: {
            fullName: {
                required: true,
                minlength: 2,
                maxlength: 100
            },
            email: {
                required: true,
                email: true
            },
            phone: {
                required: true,
                digits: true,
                minlength: 10,
                maxlength: 10
            },
            companyName: {
                maxlength: 150
            },
            quantity: {
                maxlength: 100
            },
            message: {
                maxlength: 1000
            }
        },
        messages: {
            fullName: {
                required: 'Please enter your full name',
                minlength: 'Name must be at least 2 characters',
                maxlength: 'Name cannot exceed 100 characters'
            },
            email: {
                required: 'Please enter your email address',
                email: 'Please enter a valid email address'
            },
            phone: {
                required: 'Please enter your phone number',
                digits: 'Please enter only digits',
                minlength: 'Phone number must be 10 digits',
                maxlength: 'Phone number must be 10 digits'
            },
            companyName: {
                maxlength: 'Company name cannot exceed 150 characters'
            },
            quantity: {
                maxlength: 'Quantity cannot exceed 100 characters'
            },
            message: {
                maxlength: 'Message cannot exceed 1000 characters'
            }
        },
        errorElement: 'span',
        errorClass: 'text-red-500 text-sm mt-1 block',
        errorPlacement: function(error, element) {
            error.insertAfter(element);
        },
        highlight: function(element) {
            $(element).addClass('border-red-500').removeClass('border-gray-200');
        },
        unhighlight: function(element) {
            $(element).removeClass('border-red-500').addClass('border-gray-200');
        },
        submitHandler: function(form) {
            var $form = $(form);
            var $btn = $form.find('button[type="submit"]');
            var originalHtml = $btn.html();

            // Disable button and show loading
            $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-2"></i> Submitting...');

            $.ajax({
                url: BASE_URL + 'page/submit_bulk_order',
                type: 'POST',
                data: $form.serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 200) {
                        toastr.success(response.message);
                        $form[0].reset();
                    } else {
                        toastr.error(response.message || 'Failed to submit enquiry.');
                    }
                    $btn.prop('disabled', false).html(originalHtml);
                },
                error: function() {
                    toastr.error('Something went wrong. Please try again.');
                    $btn.prop('disabled', false).html(originalHtml);
                }
            });
        }
    });
});
</script>
