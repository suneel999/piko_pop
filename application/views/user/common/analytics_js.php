<!-- ThumbmarkJS for browser fingerprinting -->
<script src="https://cdn.jsdelivr.net/npm/@thumbmarkjs/thumbmarkjs/dist/thumbmark.umd.js"></script>

<script>
(function() {
    var analyticsBase = base_url + 'analytics_track/';

    // Helper: send analytics AJAX (fire-and-forget)
    function sendAnalytics(endpoint, data) {
        $.ajax({
            url: analyticsBase + endpoint,
            type: 'POST',
            data: data,
            dataType: 'json'
        });
    }

    // =========================================
    // 1. ThumbmarkJS Fingerprint + ipinfo Geo
    // =========================================
    $(document).ready(function() {

        // Generate fingerprint and send to server
        if (typeof ThumbmarkJS !== 'undefined') {
            ThumbmarkJS.getFingerprint().then(function(fp) {
                var data = {
                    fingerprint: fp,
                    screen_resolution: window.screen.width + 'x' + window.screen.height
                };

                // Fetch geo data from ipinfo (only on new sessions - check sessionStorage)
                var geoSent = sessionStorage.getItem('pp_geo_sent');
                if (!geoSent) {
                    $.ajax({
                        url: 'https://ipinfo.io/json',
                        type: 'GET',
                        dataType: 'json',
                        timeout: 3000,
                        success: function(geo) {
                            data.country = geo.country || '';
                            data.city = geo.city || '';
                            data.region = geo.region || '';
                            sendAnalytics('update_visitor', data);
                            sessionStorage.setItem('pp_geo_sent', '1');
                        },
                        error: function() {
                            // Geo failed, still send fingerprint
                            sendAnalytics('update_visitor', data);
                            sessionStorage.setItem('pp_geo_sent', '1');
                        }
                    });
                } else {
                    // Just send fingerprint + screen resolution
                    sendAnalytics('update_visitor', data);
                }
            }).catch(function() {
                // Fingerprint failed, still try geo
                var geoSent = sessionStorage.getItem('pp_geo_sent');
                if (!geoSent) {
                    $.ajax({
                        url: 'https://ipinfo.io/json',
                        type: 'GET',
                        dataType: 'json',
                        timeout: 3000,
                        success: function(geo) {
                            sendAnalytics('update_visitor', {
                                country: geo.country || '',
                                city: geo.city || '',
                                region: geo.region || '',
                                screen_resolution: window.screen.width + 'x' + window.screen.height
                            });
                            sessionStorage.setItem('pp_geo_sent', '1');
                        }
                    });
                }
            });
        }

        // =========================================
        // 2. Event Tracking
        // =========================================

        // Track: Add to Cart (intercept AJAX success)
        $(document).on('ajaxComplete', function(event, xhr, settings) {
            if (!settings.url) return;

            // Cart add
            if (settings.url.indexOf('cart/add') !== -1) {
                try {
                    var resp = JSON.parse(xhr.responseText);
                    if (resp.status == 200) {
                        var postData = settings.data;
                        var productId = null;
                        var variantId = null;
                        if (typeof postData === 'string') {
                            var params = new URLSearchParams(postData);
                            productId = params.get('product_id');
                            variantId = params.get('variant_id');
                        }
                        sendAnalytics('event', {
                            event_type: 'cart_add',
                            product_id: productId,
                            variant_id: variantId,
                            page_url: window.location.href
                        });
                    }
                } catch(e) {}
            }

            // Cart remove
            if (settings.url.indexOf('cart/remove') !== -1 || settings.url.indexOf('cart/delete') !== -1) {
                try {
                    var resp = JSON.parse(xhr.responseText);
                    if (resp.status == 200) {
                        sendAnalytics('event', {
                            event_type: 'cart_remove',
                            page_url: window.location.href
                        });
                    }
                } catch(e) {}
            }

            // Cart update
            if (settings.url.indexOf('cart/update') !== -1) {
                try {
                    var resp = JSON.parse(xhr.responseText);
                    if (resp.status == 200) {
                        sendAnalytics('event', {
                            event_type: 'cart_update',
                            page_url: window.location.href
                        });
                    }
                } catch(e) {}
            }
        });

        // Track: Checkout page view
        if (window.location.pathname.indexOf('/checkout') !== -1) {
            sendAnalytics('event', {
                event_type: 'checkout_start',
                page_url: window.location.href
            });
        }

        // Track: Search
        $(document).on('submit', 'form[action*="search"]', function() {
            var query = $(this).find('input[name="q"], input[name="keyword"], input[type="search"]').val();
            if (query) {
                sendAnalytics('event', {
                    event_type: 'search',
                    event_label: query,
                    page_url: window.location.href
                });
            }
        });

        // Track: Wishlist add
        $(document).on('ajaxComplete', function(event, xhr, settings) {
            if (!settings.url) return;
            if (settings.url.indexOf('wishlist/add') !== -1 || settings.url.indexOf('wishlist/toggle') !== -1) {
                try {
                    var resp = JSON.parse(xhr.responseText);
                    if (resp.status == 200) {
                        sendAnalytics('event', {
                            event_type: 'wishlist_add',
                            page_url: window.location.href
                        });
                    }
                } catch(e) {}
            }
        });
    });

    // =========================================
    // 3. Global function for purchase tracking
    //    Call from order success page:
    //    trackPurchase(orderId, orderTotal)
    // =========================================
    window.trackPurchase = function(orderId, orderTotal) {
        sendAnalytics('event', {
            event_type: 'purchase',
            order_id: orderId,
            event_value: orderTotal,
            page_url: window.location.href
        });
    };
})();
</script>
