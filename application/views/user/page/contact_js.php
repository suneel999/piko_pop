<!-- Contact Page Script -->
<script>
$(document).ready(function() {
    'use strict';

    // Show/hide order ID field based on subject selection
    $('select[name="subject"]').on('change', function() {
        var subject = $(this).val();
        if (subject === 'order') {
            $('#orderIdField').removeClass('hidden');
        } else {
            $('#orderIdField').addClass('hidden');
            $('input[name="orderId"]').val('');
        }
    });

    // FAQ toggle
    $('.faq-toggle').on('click', function() {
        var $item = $(this).closest('.faq-item');
        var $content = $item.find('.faq-content');
        var $icon = $(this).find('i');

        // Close other items
        $('.faq-item').not($item).find('.faq-content').addClass('hidden');
        $('.faq-item').not($item).find('.faq-toggle i').removeClass('rotate-180');

        // Toggle current item
        $content.toggleClass('hidden');
        $icon.toggleClass('rotate-180');
    });

    // jQuery Validation for contact form
    $('#contactForm').validate({
        rules: {
            firstName: {
                required: true,
                minlength: 2,
                maxlength: 50
            },
            lastName: {
                required: true,
                minlength: 2,
                maxlength: 50
            },
            email: {
                required: true,
                email: true
            },
            phone: {
                digits: true,
                minlength: 10,
                maxlength: 10
            },
            subject: {
                required: true
            },
            message: {
                required: true,
                minlength: 10,
                maxlength: 1000
            }
        },
        messages: {
            firstName: {
                required: 'Please enter your first name',
                minlength: 'First name must be at least 2 characters',
                maxlength: 'First name cannot exceed 50 characters'
            },
            lastName: {
                required: 'Please enter your last name',
                minlength: 'Last name must be at least 2 characters',
                maxlength: 'Last name cannot exceed 50 characters'
            },
            email: {
                required: 'Please enter your email address',
                email: 'Please enter a valid email address'
            },
            phone: {
                digits: 'Please enter only digits',
                minlength: 'Phone number must be 10 digits',
                maxlength: 'Phone number must be 10 digits'
            },
            subject: {
                required: 'Please select a subject'
            },
            message: {
                required: 'Please enter your message',
                minlength: 'Message must be at least 10 characters',
                maxlength: 'Message cannot exceed 1000 characters'
            }
        },
        errorElement: 'span',
        errorClass: 'text-red-500 text-sm mt-1 block',
        errorPlacement: function(error, element) {
            if (element.is('select')) {
                error.insertAfter(element);
            } else {
                error.insertAfter(element);
            }
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
            $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-2"></i> Sending...');

            $.ajax({
                url: BASE_URL + 'page/submit_contact',
                type: 'POST',
                data: $form.serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 200) {
                        toastr.success(response.message);
                        // Reset form
                        $form[0].reset();
                        $('#orderIdField').addClass('hidden');
                    } else {
                        toastr.error(response.message || 'Failed to send message.');
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
