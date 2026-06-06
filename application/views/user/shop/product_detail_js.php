<!-- Product Detail Page Script -->
<script>
$(document).ready(function() {
    // Thumbnail click - change main image
    $(document).on('click', '.thumbnail-btn', function() {
        var newImage = $(this).data('image');
        $('#mainImage').attr('src', newImage);
        $('.thumbnail-btn').removeClass('border-secondary').addClass('border-primary/15');
        $(this).removeClass('border-primary/15').addClass('border-secondary');
    });

    // Thumbnail slider navigation
    $('#thumbPrev').click(function() {
        $('#thumbnailContainer').animate({scrollLeft: '-=100'}, 300);
    });
    $('#thumbNext').click(function() {
        $('#thumbnailContainer').animate({scrollLeft: '+=100'}, 300);
    });

    // Variant selection
    $(document).on('click', '.variant-option', function() {
        $('.variant-option').removeClass('border-secondary bg-secondary/5').addClass('border-primary/15');
        $(this).removeClass('border-primary/15').addClass('border-secondary bg-secondary/5');

        var price = parseFloat($(this).data('price'));
        var mrp = parseFloat($(this).data('mrp'));
        var variantId = $(this).data('variant-id');
        var serversText = $(this).data('servers-text') || '';

        $('#productPrice').text('₹' + Math.round(price).toLocaleString('en-IN'));
        if (mrp > price) {
            if ($('#productMrp').length) {
                $('#productMrp').text('MRP ₹' + Math.round(mrp).toLocaleString('en-IN')).show();
            } else {
                $('#productPrice').after('<span id="productMrp" class="text-base text-gray line-through">MRP ₹' + Math.round(mrp).toLocaleString('en-IN') + '</span>');
            }
        } else {
            $('#productMrp').hide();
        }

        $('#serversText').text(serversText || 'Inclusive of all taxes · Fast delivery available');

        // Update variant ID in buttons
        $('#addToCartBtn').data('variant-id', variantId);
        $('#buyNowBtn').data('variant-id', variantId);
    });

    // // Quantity controls
    // $('#qtyMinus').click(function() {
    //     var qty = parseInt($('#productQty').val());
    //     if (qty > 1) {
    //         $('#productQty').val(qty - 1);
    //     }
    // });
    // $('#qtyPlus').click(function() {
    //     var qty = parseInt($('#productQty').val());
    //     $('#productQty').val(qty + 1);
    // });

    // Add to Cart
    $('#addToCartBtn').click(function() {
        if (!is_logged_in) {
            $('#loginPromptModal').removeClass('hidden');
            $('body').addClass('overflow-hidden');
            return;
        }

        var btn = $(this);
        var productId = btn.data('product-id');
        var variantId = btn.data('variant-id') || 0;
        var qty = parseInt($('#productQty').val());

        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-2"></i> Adding...');

        $.ajax({
            url: base_url + 'cart/add',
            type: 'POST',
            data: {
                product_id: productId,
                variant_id: variantId,
                quantity: qty
            },
            dataType: 'json',
            success: function(response) {
                if (response.status == 200) {
                    $('.cart-count').text(response.cart_count).removeClass('hidden');
                    btn.html('<i class="fa-solid fa-check mr-2"></i> Added to Cart!');
                    setTimeout(function() {
                        btn.prop('disabled', false).html('<i class="fa-solid fa-cart-plus mr-2"></i> Add to Cart');
                    }, 2000);
                } else {
                    alert(response.message || 'Something went wrong. Please try again.');
                    btn.prop('disabled', false).html('<i class="fa-solid fa-cart-plus mr-2"></i> Add to Cart');
                }
            },
            error: function() {
                alert('Oops! Something went wrong. Please try again.');
                btn.prop('disabled', false).html('<i class="fa-solid fa-cart-plus mr-2"></i> Add to Cart');
            }
        });
    });

    // Buy Now
    $('#buyNowBtn').click(function() {
        if (!is_logged_in) {
            $('#loginPromptModal').removeClass('hidden');
            $('body').addClass('overflow-hidden');
            return;
        }

        var btn = $(this);
        var productId = btn.data('product-id');
        var variantId = btn.data('variant-id') || 0;
        var qty = parseInt($('#productQty').val());

        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-2"></i> Processing...');

        $.ajax({
            url: base_url + 'cart/add',
            type: 'POST',
            data: {
                product_id: productId,
                variant_id: variantId,
                quantity: qty
            },
            dataType: 'json',
            success: function(response) {
                if (response.status == 200) {
                    window.location.href = base_url + 'checkout';
                } else {
                    alert(response.message || 'Something went wrong. Please try again.');
                    btn.prop('disabled', false).html('Buy Now');
                }
            },
            error: function() {
                alert('Oops! Something went wrong. Please try again.');
                btn.prop('disabled', false).html('Buy Now');
            }
        });
    });

    // Accordion toggle
    $(document).on('click', '.accordion-toggle', function() {
        var $content = $(this).next('.accordion-content');
        var $icon = $(this).find('i:last');

        if ($content.hasClass('hidden')) {
            $content.removeClass('hidden');
            $icon.removeClass('fa-plus').addClass('fa-minus');
        } else {
            $content.addClass('hidden');
            $icon.removeClass('fa-minus').addClass('fa-plus');
        }
    });

    // Share button
    $('#shareBtn').click(function() {
        if (navigator.share) {
            navigator.share({
                title: document.title,
                url: window.location.href
            });
        } else {
            // Fallback - copy to clipboard
            navigator.clipboard.writeText(window.location.href).then(function() {
                alert('Link copied! Share this cute find with friends.');
            });
        }
    });
});
</script>
