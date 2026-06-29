<!-- Razorpay Checkout SDK -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<!-- Checkout Page Script -->
<script>
$(document).ready(function() {
    'use strict';

    var razorpayKeyId = '<?php echo htmlspecialchars($razorpay_key_id, ENT_QUOTES, 'UTF-8'); ?>';
    var razorpayLogoUrl = '<?php echo brand_logo_url(); ?>';

    // =====================
    // Address Form Validation
    // =====================
    var addressValidator = $('#checkoutAddressForm').validate({
        rules: {
            fullname: {
                required: true,
                minlength: 2,
                maxlength: 100
            },
            phone: {
                required: true,
                digits: true,
                minlength: 10,
                maxlength: 10
            },
            address_line1: {
                required: true,
                minlength: 5,
                maxlength: 200
            },
            address_line2: {
                maxlength: 200
            },
            city: {
                required: true,
                minlength: 2,
                maxlength: 100
            },
            pincode: {
                required: true,
                digits: true,
                minlength: 6,
                maxlength: 6
            },
            state: {
                required: true
            }
        },
        messages: {
            fullname: {
                required: 'Please enter full name',
                minlength: 'Name must be at least 2 characters',
                maxlength: 'Name cannot exceed 100 characters'
            },
            phone: {
                required: 'Please enter phone number',
                digits: 'Please enter only digits',
                minlength: 'Phone number must be 10 digits',
                maxlength: 'Phone number must be 10 digits'
            },
            address_line1: {
                required: 'Please enter address',
                minlength: 'Address must be at least 5 characters',
                maxlength: 'Address cannot exceed 200 characters'
            },
            city: {
                required: 'Please enter city',
                minlength: 'City must be at least 2 characters',
                maxlength: 'City cannot exceed 100 characters'
            },
            pincode: {
                required: 'Please enter pincode',
                digits: 'Please enter only digits',
                minlength: 'Pincode must be 6 digits',
                maxlength: 'Pincode must be 6 digits'
            },
            state: {
                required: 'Please select state'
            }
        },
        errorElement: 'span',
        errorPlacement: function(error, element) {
            error.addClass('error');
            if (element.attr('name') === 'phone') {
                error.insertAfter(element.closest('.relative'));
            } else {
                error.insertAfter(element);
            }
        },
        highlight: function(element) {
            $(element).addClass('error').removeClass('valid');
        },
        unhighlight: function(element) {
            $(element).removeClass('error').addClass('valid');
        }
    });

    // =====================
    // Address Selection
    // =====================
    function updateAddressSelection() {
        // Reset all cards
        $('.address-card').removeClass('border-primary bg-primary/5').addClass('border-gray-200');
        $('.radio-circle').removeClass('border-primary').addClass('border-gray-300');
        $('.radio-dot').addClass('hidden');

        // Highlight selected card
        var $checkedInput = $('input[name="deliveryAddress"]:checked');
        if ($checkedInput.length) {
            var $card = $checkedInput.siblings('.address-card');
            $card.removeClass('border-gray-200').addClass('border-primary bg-primary/5');
            $card.find('.radio-circle').removeClass('border-gray-300').addClass('border-primary');
            $card.find('.radio-dot').removeClass('hidden');
            $('#selectedAddressId').val($checkedInput.val());
        }
    }

    // Initial update
    updateAddressSelection();

    // On address change
    $(document).on('change', 'input[name="deliveryAddress"]', function() {
        updateAddressSelection();
    });

    // =====================
    // Address Modal Functions
    // =====================
    function openCheckoutAddressModal() {
        var $modal = $('#checkoutAddressModal');
        var $content = $modal.find('.checkout-address-modal-content');

        $modal.removeClass('hidden');
        $('body').css('overflow', 'hidden');

        setTimeout(function() {
            $content.removeClass('scale-95 opacity-0').addClass('scale-100 opacity-100');
        }, 10);
    }

    function closeCheckoutAddressModal() {
        var $modal = $('#checkoutAddressModal');
        var $content = $modal.find('.checkout-address-modal-content');

        $content.removeClass('scale-100 opacity-100').addClass('scale-95 opacity-0');

        setTimeout(function() {
            $modal.addClass('hidden');
            $('body').css('overflow', '');
            // Reset form
            $('#checkoutAddressForm')[0].reset();
        }, 300);
    }

    // Open modal buttons
    $('#addNewAddressBtn, #addFirstAddressBtn').on('click', function() {
        openCheckoutAddressModal();
    });

    // Close modal buttons
    $('#closeCheckoutAddressModal, #cancelCheckoutAddressBtn').on('click', function() {
        closeCheckoutAddressModal();
    });

    // Close on backdrop click
    $('.checkout-address-modal-backdrop').on('click', function() {
        closeCheckoutAddressModal();
    });

    // Close on Escape key
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && !$('#checkoutAddressModal').hasClass('hidden')) {
            closeCheckoutAddressModal();
        }
    });

    // Phone number validation
    $('#checkoutAddrPhone').on('input', function() {
        $(this).val($(this).val().replace(/[^0-9]/g, ''));
    });

    // Pincode validation
    $('#checkoutAddrPincode').on('input', function() {
        $(this).val($(this).val().replace(/[^0-9]/g, ''));
    });

    // Save address button
    $('#saveAddressBtn').on('click', function() {
        // Validate form using jQuery Validate
        if (!$('#checkoutAddressForm').valid()) {
            return;
        }

        var $form = $('#checkoutAddressForm');
        var $btn = $(this);
        var originalHtml = $btn.html();

        $btn.html('<i class="fa-solid fa-spinner fa-spin mr-2"></i> Saving...').prop('disabled', true);

        $.ajax({
            url: base_url + 'checkout/add_address',
            type: 'POST',
            data: {
                address_type: $('input[name="address_type"]:checked').val(),
                fullname: $('#checkoutAddrFullName').val().trim(),
                phone: $('#checkoutAddrPhone').val().trim(),
                address_line1: $('#checkoutAddrLine1').val().trim(),
                address_line2: $('#checkoutAddrLine2').val().trim(),
                city: $('#checkoutAddrCity').val().trim(),
                state: $('#checkoutAddrState').val(),
                pincode: $('#checkoutAddrPincode').val().trim(),
                is_default: $('#checkoutAddrDefault').is(':checked') ? 1 : 0
            },
            dataType: 'json',
            success: function(response) {
                $btn.html(originalHtml).prop('disabled', false);

                if (response.status == 200) {
                    closeCheckoutAddressModal();

                    // Add new address to the list
                    var addr = response.address;
                    var iconClass = addr.address_type == 'home' ? 'fa-house text-primary' :
                                    (addr.address_type == 'work' ? 'fa-briefcase text-gray' : 'fa-location-dot text-gray');

                    var addressHtml = '<label class="address-option block cursor-pointer">' +
                        '<input type="radio" name="deliveryAddress" value="' + addr.id + '" class="sr-only address-radio" checked>' +
                        '<div class="address-card relative border-2 border-gray-200 rounded-xl p-4 transition-all">' +
                        (addr.is_default == 1 ? '<span class="absolute top-3 right-3 px-2 py-0.5 bg-primary text-white text-xs font-semibold rounded-full">Default</span>' : '') +
                        '<div class="flex items-start gap-3">' +
                        '<div class="radio-circle w-5 h-5 border-2 border-gray-300 rounded-full flex items-center justify-center mt-0.5 flex-shrink-0">' +
                        '<div class="radio-dot w-2.5 h-2.5 bg-primary rounded-full hidden"></div>' +
                        '</div>' +
                        '<div class="flex-1">' +
                        '<div class="flex items-center gap-2 mb-1">' +
                        '<i class="fa-solid ' + iconClass + ' text-sm"></i>' +
                        '<h4 class="font-semibold text-dark">' + addr.address_type.charAt(0).toUpperCase() + addr.address_type.slice(1) + '</h4>' +
                        '</div>' +
                        '<p class="text-sm text-dark font-medium">' + addr.fullname + '</p>' +
                        '<p class="text-sm text-gray leading-relaxed">' +
                        addr.address_line1 +
                        (addr.address_line2 ? ', ' + addr.address_line2 : '') + ', ' +
                        addr.city + ', ' + addr.state + ' - ' + addr.pincode +
                        '</p>' +
                        '<p class="text-sm text-gray mt-1">' +
                        '<i class="fa-solid fa-phone text-xs mr-1"></i>' +
                        '+91 ' + addr.phone +
                        '</p>' +
                        '</div>' +
                        '</div>' +
                        '</div>' +
                        '</label>';

                    // Show addresses list and hide no address state
                    $('#savedAddressesList').removeClass('hidden').prepend(addressHtml);
                    $('#noAddressState').addClass('hidden');
                    $('#addNewAddressBtn').removeClass('hidden');

                    // Update selection
                    updateAddressSelection();

                    window.location.reload();

                } else {
                    toastr.error(response.message || 'Failed to add address.');
                }
            },
            error: function() {
                $btn.html(originalHtml).prop('disabled', false);
                toastr.error('Something went wrong. Please try again.');
            }
        });
    });

    // =====================
    // Checkout Form Submission with Razorpay
    // =====================
    $('#checkoutForm').on('submit', function(e) {
        e.preventDefault();

        // Check if address is selected
        var selectedAddress = $('#selectedAddressId').val();
        if (!razorpayKeyId) {
            toastr.error('Payment gateway is not configured. Please contact support.');
            return;
        }

        if (!selectedAddress) {
            toastr.warning('Please select a delivery address');
            return;
        }

        var $btn = $('#placeOrderBtn');
        var originalHtml = $btn.html();
        $btn.html('<i class="fa-solid fa-spinner fa-spin mr-2"></i> Processing...').prop('disabled', true);

        // Create order on server
        $.ajax({
            url: base_url + 'checkout/create_order',
            type: 'POST',
            data: {
                address_id: selectedAddress
            },
            dataType: 'json',
            success: function(response) {
                if (response.status == 200) {
                    var checkoutKey = response.key_id || razorpayKeyId;
                    // Open Razorpay checkout
                    var options = {
                        key: checkoutKey,
                        amount: response.amount,
                        currency: response.currency,
                        name: 'PIKO POP',
                        description: 'Order #' + response.order_number,
                        order_id: response.razorpay_order_id,
                        prefill: response.prefill,
                        theme: {
                            color: '#E91E8C'
                        },
                        handler: function(paymentResponse) {
                            verifyPayment(paymentResponse, response.order_id, $btn, originalHtml);
                        },
                        modal: {
                            ondismiss: function() {
                                $btn.html(originalHtml).prop('disabled', false);
                            }
                        }
                    };

                    if (razorpayLogoUrl) {
                        options.image = razorpayLogoUrl;
                    }

                    var rzp = new Razorpay(options);

                    rzp.on('payment.failed', function(response) {
                        $btn.html(originalHtml).prop('disabled', false);
                        toastr.error('Payment failed: ' + response.error.description);
                    });

                    rzp.open();

                } else {
                    $btn.html(originalHtml).prop('disabled', false);
                    toastr.error(response.message || 'Failed to create order.');
                }
            },
            error: function(xhr) {
                $btn.html(originalHtml).prop('disabled', false);
                var msg = 'Something went wrong. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                toastr.error(msg);
            }
        });
    });

    // Verify payment on server
    function verifyPayment(paymentResponse, orderId, $btn, originalHtml) {
        $btn.html('<i class="fa-solid fa-spinner fa-spin mr-2"></i> Verifying...');

        $.ajax({
            url: base_url + 'checkout/verify_payment',
            type: 'POST',
            data: {
                razorpay_payment_id: paymentResponse.razorpay_payment_id,
                razorpay_order_id: paymentResponse.razorpay_order_id,
                razorpay_signature: paymentResponse.razorpay_signature,
                order_id: orderId
            },
            dataType: 'json',
            success: function(response) {
                if (response.status == 200) {
                    // Redirect to success page
                    toastr.success('Payment successful! Redirecting...');
                    window.location.href = response.redirect_url;
                } else {
                    $btn.html(originalHtml).prop('disabled', false);
                    toastr.error(response.message || 'Payment verification failed.');
                }
            },
            error: function() {
                $btn.html(originalHtml).prop('disabled', false);
                toastr.error('Payment verification failed. Please contact support.');
            }
        });
    }
});
</script>
