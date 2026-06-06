<script>
(function() {
    // ============================================
    // Embed YouTube video on poster click
    // ============================================
    var card = document.getElementById('videoCard');
    var poster = document.getElementById('videoPoster');
    if (poster && card) {
        poster.addEventListener('click', function() {
            var vid = card.getAttribute('data-video-id');
            if (!vid) return;
            var iframe = document.createElement('iframe');
            var params = [
                'autoplay=1',
                'rel=0',              // related = same channel only
                'modestbranding=1',   // reduce YouTube logo
                'iv_load_policy=3',   // hide annotations
                'playsinline=1',      // inline on iOS
                'fs=1',               // allow fullscreen
                'color=white',
                'disablekb=0'
            ].join('&');
            iframe.src = 'https://www.youtube-nocookie.com/embed/' + vid + '?' + params;
            iframe.className = 'absolute inset-0 w-full h-full';
            iframe.setAttribute('frameborder', '0');
            iframe.setAttribute('allow', 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share');
            iframe.setAttribute('allowfullscreen', '');
            card.appendChild(iframe);
            poster.remove();
        });
    }

    // ============================================
    // Add single product
    // ============================================
    document.querySelectorAll('.recipe-add-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var productId = btn.getAttribute('data-product-id');
            var variantId = btn.getAttribute('data-variant-id') || 0;
            var qty = btn.getAttribute('data-qty') || 1;
            btn.disabled = true;
            var originalHTML = btn.innerHTML;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-sm"></i>';
            addProductToCart(productId, variantId, qty)
                .then(function(res) {
                    if (res.status === 200) {
                        btn.innerHTML = '<i class="fa-solid fa-check text-sm"></i>';
                        btn.classList.remove('bg-primary/10', 'text-primary');
                        btn.classList.add('bg-secondary', 'text-white');
                        updateCartBadge(res.cart_count);
                        if (typeof toastr !== 'undefined') toastr.success('Added to cart');
                    } else if (res.status === 401) {
                        window.location.href = BASE_URL + 'login';
                    } else {
                        btn.disabled = false;
                        btn.innerHTML = originalHTML;
                        if (typeof toastr !== 'undefined') toastr.error(res.message || 'Could not add');
                    }
                })
                .catch(function() {
                    btn.disabled = false;
                    btn.innerHTML = originalHTML;
                    if (typeof toastr !== 'undefined') toastr.error('Request failed');
                });
        });
    });

    // ============================================
    // Add all to cart
    // ============================================
    var addAllBtn = document.getElementById('addAllBtn');
    if (addAllBtn) {
        addAllBtn.addEventListener('click', function() {
            var items = [];
            // Only collect non-variant products (those with .recipe-add-btn)
            document.querySelectorAll('#recipeProducts .recipe-add-btn').forEach(function(btn) {
                items.push({
                    product_id: btn.getAttribute('data-product-id'),
                    variant_id: btn.getAttribute('data-variant-id') || 0,
                    qty: btn.getAttribute('data-qty') || 1,
                });
            });

            if (items.length === 0) {
                if (typeof toastr !== 'undefined') toastr.info('No items to add.');
                return;
            }

            addAllBtn.disabled = true;
            var originalHTML = addAllBtn.innerHTML;
            addAllBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Adding...';

            var added = 0;
            var failed = 0;
            var needLogin = false;
            var latestCount = null;

            // Sequential adds (avoid race conditions on cart updates)
            var chain = Promise.resolve();
            items.forEach(function(item) {
                chain = chain.then(function() {
                    return addProductToCart(item.product_id, item.variant_id, item.qty)
                        .then(function(res) {
                            if (res.status === 200) {
                                added++;
                                if (typeof res.cart_count !== 'undefined') latestCount = res.cart_count;
                            } else if (res.status === 401) needLogin = true;
                            else failed++;
                        })
                        .catch(function() { failed++; });
                });
            });

            chain.then(function() {
                if (needLogin) {
                    window.location.href = BASE_URL + 'login';
                    return;
                }
                addAllBtn.disabled = false;
                addAllBtn.innerHTML = originalHTML;
                if (latestCount !== null) updateCartBadge(latestCount);
                if (added > 0 && typeof toastr !== 'undefined') {
                    toastr.success('Added ' + added + ' item' + (added === 1 ? '' : 's') + ' to your cart');
                }
                if (failed > 0 && typeof toastr !== 'undefined') {
                    toastr.warning(failed + ' item' + (failed === 1 ? '' : 's') + ' could not be added');
                }
            });
        });
    }

    function addProductToCart(productId, variantId, qty) {
        return fetch(BASE_URL + 'cart/add', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
            body: 'product_id=' + encodeURIComponent(productId) +
                  '&variant_id=' + encodeURIComponent(variantId) +
                  '&quantity=' + encodeURIComponent(qty)
        }).then(function(r) { return r.json(); });
    }

    function updateCartBadge(count) {
        if (typeof count === 'undefined' || count === null) return;
        document.querySelectorAll('.cart-count').forEach(function(b) {
            b.textContent = count;
            if (count > 0) b.classList.remove('hidden');
            else b.classList.add('hidden');
        });
    }
})();
</script>
