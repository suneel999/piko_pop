<!-- Orders Page Script -->
<script>
$(document).ready(function() {
    'use strict';

    // Filter tabs
    $('.order-filter-btn').on('click', function() {
        var filter = $(this).data('filter');

        // Update active state
        $('.order-filter-btn').removeClass('active');
        $(this).addClass('active');

        // Filter orders
        if (filter === 'all') {
            $('.order-card').show();
        } else {
            $('.order-card').hide();
            $('.order-card[data-status="' + filter + '"]').show();
        }

        // Show/hide no results message
        var visibleOrders = $('.order-card:visible').length;
        if (visibleOrders === 0) {
            $('#noOrdersMessage').removeClass('hidden');
        } else {
            $('#noOrdersMessage').addClass('hidden');
        }
    });

    // Reorder button click
    $('.reorder-btn').on('click', function() {
        var $btn = $(this);
        var orderId = $btn.data('order-id');
        var originalHtml = $btn.html();

        // Disable button and show loading
        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-1.5"></i> Adding...');

        $.ajax({
            url: BASE_URL + 'account/reorder',
            type: 'POST',
            data: { order_id: orderId },
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
});
</script>
