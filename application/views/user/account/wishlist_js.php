<script>
$(document).ready(function() {
    'use strict';

    $(document).on('click', '.remove-wishlist-btn', function(e) {
        e.preventDefault();

        var $btn = $(this);
        var productId = $btn.data('product-id');
        var $item = $btn.closest('.wishlist-item');

        $btn.prop('disabled', true);

        $.ajax({
            url: base_url + 'wishlist/remove',
            type: 'POST',
            data: { product_id: productId },
            dataType: 'json',
            success: function(response) {
                if (response.status == 200) {
                    $item.fadeOut(250, function() {
                        $(this).remove();
                        if ($('#wishlistGrid .wishlist-item').length === 0) {
                            window.location.reload();
                        }
                    });

                    if (typeof response.wishlist_count !== 'undefined') {
                        var count = parseInt(response.wishlist_count, 10);
                        $('.wishlist-count').text(count);
                        if (count === 0) {
                            $('.wishlist-count').addClass('hidden');
                        }
                    }

                    toastr.success(response.message || 'Removed from wishlist.');
                } else {
                    $btn.prop('disabled', false);
                    toastr.error(response.message || 'Could not remove item.');
                }
            },
            error: function() {
                $btn.prop('disabled', false);
                toastr.error('Something went wrong. Please try again.');
            }
        });
    });
});
</script>
