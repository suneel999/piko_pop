<!-- Order Detail Page Script -->
<script>
    $(document).ready(function() {
        'use strict';

        // Reorder Items button click
        $('.reorder-btn').on('click', function() {
            var $btn = $(this);
            var orderId = $btn.data('order-id');
            var originalHtml = $btn.html();

            // Disable button and show loading
            $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-1.5"></i> Adding...');

            $.ajax({
                url: BASE_URL + 'account/reorder',
                type: 'POST',
                data: {
                    order_id: orderId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 200) {
                        // Update cart count in header
                        if (response.cart_count !== undefined) {
                            $('.cart-count').text(response.cart_count);
                        }

                        // Show success message
                        // showToast('success', response.message);
                        toastr.success(response.message);

                        // Redirect to cart after short delay
                        if (response.redirect) {
                            setTimeout(function() {
                                window.location.href = response.redirect;
                            }, 1000);
                        }
                    } else {
                        // showToast('error', response.message || 'Failed to add items to cart.');
                        toastr.error(response.message || 'Failed to add items to cart.');
                        $btn.prop('disabled', false).html(originalHtml);
                    }
                },
                error: function() {
                    // showToast('error', 'Something went wrong. Please try again.');
                    toastr.error('Something went wrong. Please try again.');
                    $btn.prop('disabled', false).html(originalHtml);
                }
            });
        });

        // Buy Again button click
        $('.buy-again-btn').on('click', function() {
            var $btn = $(this);
            var productId = $btn.data('product-id');
            var variantId = $btn.data('variant-id') || '';
            var originalHtml = $btn.html();

            // Disable button and show loading
            $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i>');

            $.ajax({
                url: BASE_URL + 'account/buy_again',
                type: 'POST',
                data: {
                    product_id: productId,
                    variant_id: variantId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 200) {
                        // Update cart count in header
                        if (response.cart_count !== undefined) {
                            $('.cart-count').text(response.cart_count);
                        }

                        // Show success message
                        // showToast('success', response.message);
                        toastr.success(response.message);

                        // Restore button
                        $btn.prop('disabled', false).html(originalHtml);
                    } else {
                        // showToast('error', response.message || 'Failed to add item to cart.');
                        toastr.error(response.message || 'Failed to add item to cart.');
                        $btn.prop('disabled', false).html(originalHtml);
                    }
                },
                error: function() {
                    // showToast('error', 'Something went wrong. Please try again.');
                    toastr.error('Something went wrong. Please try again.');
                    $btn.prop('disabled', false).html(originalHtml);
                }
            });
        });
    });
</script>