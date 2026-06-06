<script>
    $(document).ready(function() {
        // Load all charts and data with a slight delay to ensure DOM is ready
        setTimeout(function() {
            loadMonthlyOrdersChart();
            loadOrderStatusChart();
            loadPaymentMethodChart();
            loadRecentOrders();
            loadTopProducts();
        }, 100);
    });

    // Monthly Orders & Revenue Chart
    function loadMonthlyOrdersChart() {
        $.ajax({
            url: base_url + 'admin_root/home/get_monthly_orders',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                var monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                var categories = [];
                var orders = [];
                var revenue = [];
                var hasData = false;

                // Initialize all months with 0
                for (var i = 1; i <= 12; i++) {
                    categories.push(monthNames[i - 1]);
                    orders.push(0);
                    revenue.push(0);
                }

                // Fill in actual data if available
                if (response.status == 200 && response.data && response.data.length > 0) {
                    response.data.forEach(function(item) {
                        var monthIndex = parseInt(item.month) - 1;
                        if (monthIndex >= 0 && monthIndex < 12) {
                            var orderCount = parseInt(item.orders) || 0;
                            var revenueAmount = parseFloat(item.revenue) || 0;
                            orders[monthIndex] = orderCount;
                            revenue[monthIndex] = revenueAmount;
                            if (orderCount > 0 || revenueAmount > 0) {
                                hasData = true;
                            }
                        }
                    });
                }

                var container = document.querySelector("#monthly-orders-chart");
                if (!container) {
                    console.error('Monthly orders chart container not found');
                    return;
                }

                if (!hasData) {
                    renderNoDataChart('monthly-orders-chart', 'No monthly order data available for this year');
                    return;
                }

                try {
                    var options = {
                        series: [{
                            name: 'Orders',
                            type: 'column',
                            data: orders
                        }, {
                            name: 'Revenue (₹)',
                            type: 'line',
                            data: revenue
                        }],
                        chart: {
                            type: 'line',
                            height: 400,
                            toolbar: {
                                show: false
                            },
                            fontFamily: 'Inter, sans-serif'
                        },
                        stroke: {
                            width: [0, 3]
                        },
                        dataLabels: {
                            enabled: false
                        },
                        colors: ['#5156be', '#2ab57d'],
                        xaxis: {
                            categories: categories,
                        },
                        yaxis: [{
                            title: {
                                text: 'Orders'
                            },
                            min: 0,
                            forceNiceScale: true
                        }, {
                            opposite: true,
                            title: {
                                text: 'Revenue (₹)'
                            },
                            min: 0,
                            forceNiceScale: true,
                            labels: {
                                formatter: function(val) {
                                    return '₹' + val.toLocaleString('en-IN');
                                }
                            }
                        }],
                        tooltip: {
                            y: {
                                formatter: function(val, opts) {
                                    if (opts.seriesIndex === 1) {
                                        return '₹' + parseFloat(val).toLocaleString('en-IN', {minimumFractionDigits: 2});
                                    }
                                    return parseInt(val);
                                }
                            }
                        }
                    };

                    var chart = new ApexCharts(container, options);
                    chart.render();
                } catch (error) {
                    console.error('Error rendering monthly orders chart:', error);
                    renderNoDataChart('monthly-orders-chart', 'Error rendering chart');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading monthly orders chart:', error);
                renderNoDataChart('monthly-orders-chart', 'Error loading monthly data');
            }
        });
    }

    // Order Status Breakdown Chart
    function loadOrderStatusChart() {
        $.ajax({
            url: base_url + 'admin_root/home/get_order_status_breakdown',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.status == 200 && response.data && response.data.length > 0) {
                    var statusLabels = {
                        'pending': 'Pending',
                        'confirmed': 'Confirmed',
                        'processing': 'Processing',
                        'shipped': 'Shipped',
                        'out_for_delivery': 'Out for Delivery',
                        'delivered': 'Delivered',
                        'returned': 'Returned'
                    };
                    var statusColors = {
                        'pending': '#ffbf53',
                        'confirmed': '#5156be',
                        'processing': '#4ba6ef',
                        'shipped': '#2ab57d',
                        'out_for_delivery': '#ff6b6b',
                        'delivered': '#34c38f',
                        'returned': '#f46a6a'
                    };

                    var labels = [];
                    var series = [];
                    var colors = [];

                    response.data.forEach(function(item) {
                        var count = parseInt(item.count) || 0;
                        if (count > 0) {
                            labels.push(statusLabels[item.order_status] || item.order_status);
                            series.push(count);
                            colors.push(statusColors[item.order_status] || '#95a5a6');
                        }
                    });

                    if (series.length === 0) {
                        renderNoDataChart('order-status-chart', 'No order status data available');
                        return;
                    }

                    var container = document.querySelector("#order-status-chart");
                    if (!container) {
                        console.error('Order status chart container not found');
                        return;
                    }

                    try {
                        var options = {
                            series: series,
                            chart: {
                                type: 'donut',
                                height: 350,
                                fontFamily: 'Inter, sans-serif'
                            },
                            labels: labels,
                            colors: colors,
                            legend: {
                                position: 'bottom'
                            },
                            dataLabels: {
                                enabled: true,
                                formatter: function(val, opts) {
                                    return opts.w.config.series[opts.seriesIndex];
                                }
                            },
                            plotOptions: {
                                pie: {
                                    donut: {
                                        size: '70%'
                                    }
                                }
                            }
                        };

                        var chart = new ApexCharts(container, options);
                        chart.render();
                    } catch (error) {
                        console.error('Error rendering order status chart:', error);
                        renderNoDataChart('order-status-chart', 'Error rendering chart');
                    }
                } else {
                    renderNoDataChart('order-status-chart', 'No order status data available');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading order status chart:', error);
                renderNoDataChart('order-status-chart', 'Error loading status data');
            }
        });
    }

    // Payment Method Chart
    function loadPaymentMethodChart() {
        $.ajax({
            url: base_url + 'admin_root/home/get_payment_breakdown',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.status == 200 && response.data && response.data.length > 0) {
                    var methodLabels = {
                        'cod': 'Cash on Delivery',
                        'online': 'Online Payment',
                        'wallet': 'Wallet',
                        'stripe': 'Stripe',
                        'upi': 'UPI'
                    };

                    var labels = [];
                    var series = [];

                    response.data.forEach(function(item) {
                        var count = parseInt(item.count) || 0;
                        if (count > 0) {
                            labels.push(methodLabels[item.payment_method] || item.payment_method);
                            series.push(count);
                        }
                    });

                    if (series.length === 0) {
                        renderNoDataChart('payment-method-chart', 'No payment data available');
                        return;
                    }

                    var container = document.querySelector("#payment-method-chart");
                    if (!container) {
                        console.error('Payment method chart container not found');
                        return;
                    }

                    try {
                        var options = {
                            series: series,
                            chart: {
                                type: 'donut',
                                height: 350,
                                fontFamily: 'Inter, sans-serif'
                            },
                            labels: labels,
                            colors: ['#5156be', '#2ab57d', '#ffbf53', '#4ba6ef', '#f46a6a'],
                            legend: {
                                position: 'bottom'
                            },
                            dataLabels: {
                                enabled: true
                            },
                            plotOptions: {
                                pie: {
                                    donut: {
                                        size: '70%'
                                    }
                                }
                            }
                        };

                        var chart = new ApexCharts(container, options);
                        chart.render();
                    } catch (error) {
                        console.error('Error rendering payment method chart:', error);
                        renderNoDataChart('payment-method-chart', 'Error rendering chart');
                    }
                } else {
                    renderNoDataChart('payment-method-chart', 'No payment data available');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading payment method chart:', error);
                renderNoDataChart('payment-method-chart', 'Error loading payment data');
            }
        });
    }

    // Load Recent Orders
    function loadRecentOrders() {
        $.ajax({
            url: base_url + 'admin_root/home/get_recent_orders',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                var tbody = $('#recent-orders-table tbody');
                tbody.empty();

                if (response.status == 200 && response.data && response.data.length > 0) {
                    response.data.forEach(function(order) {
                        var statusClass = '';
                        switch (order.order_status) {
                            case 'delivered':
                                statusClass = 'success';
                                break;
                            case 'shipped':
                            case 'out_for_delivery':
                                statusClass = 'info';
                                break;
                            case 'processing':
                            case 'confirmed':
                                statusClass = 'primary';
                                break;
                            case 'pending':
                                statusClass = 'warning';
                                break;
                            case 'cancelled':
                            case 'returned':
                                statusClass = 'danger';
                                break;
                            default:
                                statusClass = 'secondary';
                        }

                        var statusText = order.order_status.replace('_', ' ');
                        statusText = statusText.charAt(0).toUpperCase() + statusText.slice(1);

                        var date = new Date(order.created_at);
                        var formattedDate = date.toLocaleDateString('en-IN', {day: '2-digit', month: 'short'});

                        var row = '<tr>' +
                            '<td><a href="' + base_url + 'admin_root/orders/view/' + order.id + '" class="text-primary">#' + order.order_number + '</a></td>' +
                            '<td>' + (order.customer_name || 'Guest') + '</td>' +
                            '<td>₹' + parseFloat(order.total_amount).toLocaleString('en-IN', {minimumFractionDigits: 2}) + '</td>' +
                            '<td><span class="badge bg-soft-' + statusClass + ' text-' + statusClass + '">' + statusText + '</span></td>' +
                            '<td>' + formattedDate + '</td>' +
                            '</tr>';
                        tbody.append(row);
                    });
                } else {
                    tbody.append('<tr><td colspan="5" class="text-center text-muted">No recent orders</td></tr>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading recent orders:', error);
                $('#recent-orders-table tbody').html('<tr><td colspan="5" class="text-center text-muted">Error loading orders</td></tr>');
            }
        });
    }

    // Load Top Products
    function loadTopProducts() {
        $.ajax({
            url: base_url + 'admin_root/home/get_top_products',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                var tbody = $('#top-products-table tbody');
                tbody.empty();

                if (response.status == 200 && response.data && response.data.length > 0) {
                    response.data.forEach(function(product, index) {
                        var row = '<tr>' +
                            '<td>' +
                            '<span class="badge bg-soft-primary text-primary me-2">' + (index + 1) + '</span>' +
                            product.product_name.substring(0, 30) + (product.product_name.length > 30 ? '...' : '') +
                            '</td>' +
                            '<td>' + parseInt(product.total_sold) + '</td>' +
                            '<td>₹' + parseFloat(product.total_revenue).toLocaleString('en-IN', {minimumFractionDigits: 2}) + '</td>' +
                            '</tr>';
                        tbody.append(row);
                    });
                } else {
                    tbody.append('<tr><td colspan="3" class="text-center text-muted">No product data available</td></tr>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading top products:', error);
                $('#top-products-table tbody').html('<tr><td colspan="3" class="text-center text-muted">Error loading products</td></tr>');
            }
        });
    }

    // Helper function to render "No Data" message
    function renderNoDataChart(elementId, message) {
        var container = document.getElementById(elementId);
        if (container) {
            container.innerHTML = '<div style="display: flex; align-items: center; justify-content: center; height: 300px; color: #94a3b8; font-size: 14px;">' +
                '<div style="text-align: center;"><i class="uil uil-chart-bar" style="font-size: 48px; opacity: 0.3; display: block; margin-bottom: 10px;"></i>' +
                message + '</div></div>';
        }
    }
</script>
