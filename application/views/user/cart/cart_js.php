<!-- Cart Page Script -->
<script>
$(document).ready(function() {
    var FREE_SHIPPING_THRESHOLD = 249;
    var SHIPPING_CHARGE = 50;

    // Refresh cart summary from server
    function refreshCartSummary() {
        $.ajax({
            url: base_url + 'cart/get_cart_summary',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.status == 200) {
                    updateSummaryDisplay(response);
                }
            }
        });
    }

    // Update summary display with server data
    function updateSummaryDisplay(data) {
        $('#itemsCount').text(data.item_count);
        $('#cartSubtotal').text('Rs. ' + formatNumber(data.subtotal));

        // Update discount
        if (data.discount > 0) {
            $('#discountRow').removeClass('hidden');
            $('#discountAmount').text('- Rs. ' + formatNumber(data.discount));
        } else {
            $('#discountRow').addClass('hidden');
        }

        // Update shipping
        if (data.shipping == 0) {
            $('#shippingAmount').text('FREE').removeClass('text-dark').addClass('text-green-600');
            $('#freeShippingMsg').removeClass('hidden');
            $('#shippingProgressMsg').addClass('hidden');
        } else {
            $('#shippingAmount').text('Rs. ' + formatNumber(data.shipping)).removeClass('text-green-600').addClass('text-dark');
            $('#freeShippingMsg').addClass('hidden');
            $('#shippingProgressMsg').removeClass('hidden');
            $('#shippingProgressMsg').find('strong').text('Rs. ' + formatNumber(data.amount_for_free_shipping));
        }

        // Update total
        $('#cartTotal').text('Rs. ' + formatNumber(data.total));
    }

    // Update quantity
    function updateQuantity(cartItem, action) {
        var productId = cartItem.data('product-id');
        var variantId = cartItem.data('variant-id') || 0;
        var qtyInput = cartItem.find('.qty-input');

        // Disable buttons during update
        cartItem.find('.qty-minus-btn, .qty-plus-btn').prop('disabled', true);

        $.ajax({
            url: base_url + 'cart/update_quantity',
            type: 'POST',
            data: {
                product_id: productId,
                variant_id: variantId,
                action: action
            },
            dataType: 'json',
            success: function(response) {
                if (response.status == 200) {
                    if (response.item_removed) {
                        // Remove item from DOM
                        cartItem.fadeOut(300, function() {
                            $(this).remove();
                            // Check if cart is empty
                            if ($('.cart-item').length === 0) {
                                location.reload();
                            } else {
                                refreshCartSummary();
                            }
                        });
                    } else {
                        // Update quantity display
                        qtyInput.val(response.item_quantity);
                        // Update item total
                        cartItem.find('.item-total').text('Rs. ' + formatNumber(response.item_total));
                        // Refresh cart summary
                        refreshCartSummary();
                    }
                    // Update header cart count
                    $('.cart-count').text(response.cart_count);
                    if (response.cart_count == 0) {
                        $('.cart-count').addClass('hidden');
                    }
                } else {
                    toastr.error(response.message);
                }
                cartItem.find('.qty-minus-btn, .qty-plus-btn').prop('disabled', false);
            },
            error: function() {
                toastr.error('Something went wrong. Please try again.');
                cartItem.find('.qty-minus-btn, .qty-plus-btn').prop('disabled', false);
            }
        });
    }

    // Quantity minus button
    $(document).on('click', '.qty-minus-btn', function() {
        var cartItem = $(this).closest('.cart-item');
        updateQuantity(cartItem, 'decrement');
    });

    // Quantity plus button
    $(document).on('click', '.qty-plus-btn', function() {
        var cartItem = $(this).closest('.cart-item');
        updateQuantity(cartItem, 'increment');
    });

    // Remove item button
    $(document).on('click', '.remove-item-btn', function() {
        var cartItem = $(this).closest('.cart-item');
        var cartId = cartItem.data('cart-id');

        if (!confirm('Are you sure you want to remove this item?')) {
            return;
        }

        $.ajax({
            url: base_url + 'cart/remove_by_id',
            type: 'POST',
            data: { cart_id: cartId },
            dataType: 'json',
            success: function(response) {
                if (response.status == 200) {
                    cartItem.fadeOut(300, function() {
                        $(this).remove();
                        // Check if cart is empty
                        if ($('.cart-item').length === 0) {
                            location.reload();
                        } else {
                            refreshCartSummary();
                        }
                    });
                    // Update header cart count
                    $('.cart-count').text(response.cart_count);
                    if (response.cart_count == 0) {
                        $('.cart-count').addClass('hidden');
                    }
                    toastr.success('Item removed from cart');
                } else {
                    toastr.error(response.message);
                }
            },
            error: function() {
                toastr.error('Something went wrong. Please try again.');
            }
        });
    });

    // Apply coupon
    $('#applyCouponBtn').click(function() {
        var couponCode = $('#couponCode').val().trim();
        if (!couponCode) {
            $('#couponError').text('Please enter a coupon code.').removeClass('hidden');
            return;
        }

        var btn = $(this);
        btn.prop('disabled', true).text('Applying...');
        $('#couponError').addClass('hidden');

        $.ajax({
            url: base_url + 'cart/apply_coupon',
            type: 'POST',
            data: { coupon_code: couponCode },
            dataType: 'json',
            success: function(response) {
                btn.prop('disabled', false).text('Apply');

                if (response.status == 200) {
                    // Show applied coupon section
                    $('#couponInputSection').addClass('hidden');
                    $('#appliedCouponSection').removeClass('hidden');
                    $('#appliedCouponCode').text(response.coupon_code);
                    $('#appliedCouponDiscount').text('-Rs. ' + formatNumber(response.discount_amount) + ' discount');

                    // Refresh cart summary
                    refreshCartSummary();
                } else {
                    $('#couponError').text(response.message).removeClass('hidden');
                }
            },
            error: function() {
                btn.prop('disabled', false).text('Apply');
                $('#couponError').text('Something went wrong. Please try again.').removeClass('hidden');
            }
        });
    });

    // Remove coupon
    $(document).on('click', '#removeCouponBtn', function() {
        $.ajax({
            url: base_url + 'cart/remove_coupon',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.status == 200) {
                    // Show coupon input section
                    $('#appliedCouponSection').addClass('hidden');
                    $('#couponInputSection').removeClass('hidden');
                    $('#couponCode').val('');

                    // Refresh cart summary
                    refreshCartSummary();
                }
            }
        });
    });

    // Format number with 2 decimal places
    function formatNumber(num) {
        return parseFloat(num).toFixed(2);
    }

    // Enter key on coupon input
    $('#couponCode').keypress(function(e) {
        if (e.which == 13) {
            e.preventDefault();
            $('#applyCouponBtn').click();
        }
    });
});
</script>
