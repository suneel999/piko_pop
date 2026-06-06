<script>
var chartInstances = {};

$(document).ready(function() {
    // Init Flatpickr on date fields
    var today = new Date();
    var thirtyDaysAgo = new Date();
    thirtyDaysAgo.setDate(today.getDate() - 30);

    flatpickr('#date_from', {
        dateFormat: 'Y-m-d',
        defaultDate: thirtyDaysAgo,
        maxDate: 'today'
    });
    flatpickr('#date_to', {
        dateFormat: 'Y-m-d',
        defaultDate: today,
        maxDate: 'today'
    });

    // Load all charts
    setTimeout(function() {
        loadAllCharts();
    }, 100);

    // Live sessions counter - refresh every 15s
    loadLiveCount();
    setInterval(loadLiveCount, 15000);

    // Filter form submit
    $('#analytics_filter_form').on('submit', function(e) {
        e.preventDefault();
        loadAllCharts();
    });

    // Reset filter
    $('#btn_reset_filter').on('click', function() {
        $('#date_from').val(formatDate(thirtyDaysAgo));
        $('#date_to').val(formatDate(today));
        // Update flatpickr instances
        document.getElementById('date_from')._flatpickr.setDate(thirtyDaysAgo);
        document.getElementById('date_to')._flatpickr.setDate(today);
        loadAllCharts();
    });
});

function formatDate(date) {
    return date.getFullYear() + '-' + String(date.getMonth() + 1).padStart(2, '0') + '-' + String(date.getDate()).padStart(2, '0');
}

function getFilterDates() {
    return {
        date_from: $('#date_from').val(),
        date_to: $('#date_to').val()
    };
}

function formatDuration(seconds) {
    if (!seconds || seconds == 0) return '0s';
    var m = Math.floor(seconds / 60);
    var s = seconds % 60;
    if (m > 0) return m + 'm ' + s + 's';
    return s + 's';
}

function destroyChart(key) {
    if (chartInstances[key]) {
        chartInstances[key].destroy();
        delete chartInstances[key];
    }
}

function renderNoDataChart(elementId, message) {
    var container = document.getElementById(elementId);
    if (container) {
        container.innerHTML = '<div style="display: flex; align-items: center; justify-content: center; height: 300px; color: #94a3b8; font-size: 14px;">' +
            '<div style="text-align: center;"><i class="uil uil-chart-bar" style="font-size: 48px; opacity: 0.3; display: block; margin-bottom: 10px;"></i>' +
            message + '</div></div>';
    }
}

function loadAllCharts() {
    loadOverviewStats();
    loadSessionsOverTime();
    loadNewVsReturning();
    loadDeviceChart();
    loadSourcesChart();
    loadDayOfWeekChart();
    loadTopPages();
    loadFunnelChart();
    loadCountryChart();
}

// =============================================
// 1. Overview Stats (4 cards)
// =============================================
function loadOverviewStats() {
    $.ajax({
        url: base_url + 'admin_root/analytics/get_overview_stats',
        type: 'POST',
        data: getFilterDates(),
        dataType: 'json',
        success: function(response) {
            if (response.status == 200 && response.data) {
                $('#stat_sessions').text(response.data.total_sessions.toLocaleString('en-IN'));
                $('#stat_visitors').text(response.data.unique_visitors.toLocaleString('en-IN'));
                $('#stat_duration').text(formatDuration(response.data.avg_duration));
                $('#stat_bounce').text(response.data.bounce_rate + '%');
                $('#stat_pageviews').text(response.data.total_pageviews.toLocaleString('en-IN'));
            } else {
                $('#stat_sessions').text('0');
                $('#stat_visitors').text('0');
                $('#stat_duration').text('0s');
                $('#stat_bounce').text('0%');
                $('#stat_pageviews').text('0');
            }
        },
        error: function() {
            $('#stat_sessions, #stat_visitors, #stat_duration, #stat_bounce').text('-');
        }
    });
}

// =============================================
// 2. Sessions Over Time (area chart)
// =============================================
function loadSessionsOverTime() {
    $.ajax({
        url: base_url + 'admin_root/analytics/get_sessions_over_time',
        type: 'POST',
        data: getFilterDates(),
        dataType: 'json',
        success: function(response) {
            destroyChart('sessionsOverTime');
            var container = document.querySelector('#sessions-over-time-chart');
            if (!container) return;

            if (response.status == 200 && response.data && response.data.length > 0) {
                var categories = [];
                var sessions = [];
                response.data.forEach(function(item) {
                    var d = new Date(item.date);
                    categories.push(d.toLocaleDateString('en-IN', { day: '2-digit', month: 'short' }));
                    sessions.push(parseInt(item.sessions));
                });

                var options = {
                    series: [{ name: 'Sessions', data: sessions }],
                    chart: {
                        type: 'area',
                        height: 350,
                        toolbar: { show: false },
                        fontFamily: 'Inter, sans-serif'
                    },
                    colors: ['#5156be'],
                    fill: {
                        type: 'gradient',
                        gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.1, stops: [0, 100] }
                    },
                    stroke: { curve: 'smooth', width: 2 },
                    dataLabels: { enabled: false },
                    xaxis: { categories: categories, labels: { rotate: -45, rotateAlways: categories.length > 15 } },
                    yaxis: { min: 0, forceNiceScale: true, title: { text: 'Sessions' } },
                    tooltip: {
                        y: { formatter: function(val) { return parseInt(val) + ' sessions'; } }
                    }
                };

                chartInstances['sessionsOverTime'] = new ApexCharts(container, options);
                chartInstances['sessionsOverTime'].render();
            } else {
                renderNoDataChart('sessions-over-time-chart', 'No session data for this period');
            }
        },
        error: function() {
            renderNoDataChart('sessions-over-time-chart', 'Error loading data');
        }
    });
}

// =============================================
// 3. New vs Returning Visitors (donut chart)
// =============================================
function loadNewVsReturning() {
    $.ajax({
        url: base_url + 'admin_root/analytics/get_new_vs_returning',
        type: 'POST',
        data: getFilterDates(),
        dataType: 'json',
        success: function(response) {
            destroyChart('newVsReturning');
            var container = document.querySelector('#new-vs-returning-chart');
            if (!container) return;

            if (response.status == 200 && response.data) {
                var newV = parseInt(response.data.new_visitors) || 0;
                var retV = parseInt(response.data.returning_visitors) || 0;

                if (newV == 0 && retV == 0) {
                    renderNoDataChart('new-vs-returning-chart', 'No visitor data');
                    return;
                }

                var options = {
                    series: [newV, retV],
                    chart: { type: 'donut', height: 350, fontFamily: 'Inter, sans-serif' },
                    labels: ['New Visitors', 'Returning Visitors'],
                    colors: ['#2ab57d', '#5156be'],
                    legend: { position: 'bottom' },
                    dataLabels: {
                        enabled: true,
                        formatter: function(val) { return Math.round(val) + '%'; }
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '70%',
                                labels: {
                                    show: true,
                                    total: {
                                        show: true,
                                        label: 'Total',
                                        formatter: function(w) {
                                            return w.globals.seriesTotals.reduce(function(a, b) { return a + b; }, 0);
                                        }
                                    }
                                }
                            }
                        }
                    }
                };

                chartInstances['newVsReturning'] = new ApexCharts(container, options);
                chartInstances['newVsReturning'].render();
            } else {
                renderNoDataChart('new-vs-returning-chart', 'No visitor data');
            }
        },
        error: function() {
            renderNoDataChart('new-vs-returning-chart', 'Error loading data');
        }
    });
}

// =============================================
// 4. Sessions by Device (donut chart)
// =============================================
function loadDeviceChart() {
    $.ajax({
        url: base_url + 'admin_root/analytics/get_sessions_by_device',
        type: 'POST',
        data: getFilterDates(),
        dataType: 'json',
        success: function(response) {
            destroyChart('device');
            var container = document.querySelector('#device-chart');
            if (!container) return;

            if (response.status == 200 && response.data && response.data.length > 0) {
                var labels = [];
                var series = [];
                var colorMap = { 'desktop': '#5156be', 'mobile': '#2ab57d', 'tablet': '#ffbf53' };
                var colors = [];

                response.data.forEach(function(item) {
                    var label = item.device_type.charAt(0).toUpperCase() + item.device_type.slice(1);
                    labels.push(label);
                    series.push(parseInt(item.count));
                    colors.push(colorMap[item.device_type] || '#4ba6ef');
                });

                var options = {
                    series: series,
                    chart: { type: 'donut', height: 320, fontFamily: 'Inter, sans-serif' },
                    labels: labels,
                    colors: colors,
                    legend: { position: 'bottom' },
                    dataLabels: { enabled: true },
                    plotOptions: { pie: { donut: { size: '70%' } } }
                };

                chartInstances['device'] = new ApexCharts(container, options);
                chartInstances['device'].render();
            } else {
                renderNoDataChart('device-chart', 'No device data');
            }
        },
        error: function() {
            renderNoDataChart('device-chart', 'Error loading data');
        }
    });
}

// =============================================
// 5. Traffic Sources (donut chart)
// =============================================
function loadSourcesChart() {
    $.ajax({
        url: base_url + 'admin_root/analytics/get_sessions_by_source',
        type: 'POST',
        data: getFilterDates(),
        dataType: 'json',
        success: function(response) {
            destroyChart('sources');
            var container = document.querySelector('#sources-chart');
            if (!container) return;

            if (response.status == 200 && response.data && response.data.length > 0) {
                var labels = [];
                var series = [];
                var colors = ['#5156be', '#2ab57d', '#ffbf53', '#4ba6ef', '#fd625e', '#95a5a6'];

                response.data.forEach(function(item) {
                    labels.push(item.source);
                    series.push(parseInt(item.count));
                });

                var options = {
                    series: series,
                    chart: { type: 'donut', height: 320, fontFamily: 'Inter, sans-serif' },
                    labels: labels,
                    colors: colors.slice(0, labels.length),
                    legend: { position: 'bottom' },
                    dataLabels: { enabled: true },
                    plotOptions: { pie: { donut: { size: '70%' } } }
                };

                chartInstances['sources'] = new ApexCharts(container, options);
                chartInstances['sources'].render();
            } else {
                renderNoDataChart('sources-chart', 'No source data');
            }
        },
        error: function() {
            renderNoDataChart('sources-chart', 'Error loading data');
        }
    });
}

// =============================================
// 6. Avg Sessions by Day of Week (bar chart)
// =============================================
function loadDayOfWeekChart() {
    $.ajax({
        url: base_url + 'admin_root/analytics/get_avg_sessions_by_day',
        type: 'POST',
        data: getFilterDates(),
        dataType: 'json',
        success: function(response) {
            destroyChart('dayOfWeek');
            var container = document.querySelector('#day-of-week-chart');
            if (!container) return;

            if (response.status == 200 && response.data && response.data.length > 0) {
                var dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                var categories = [];
                var avgSessions = [];

                // Initialize all days with 0
                for (var i = 0; i < 7; i++) {
                    categories.push(dayNames[i]);
                    avgSessions.push(0);
                }

                response.data.forEach(function(item) {
                    var idx = parseInt(item.dow) - 1; // DAYOFWEEK: 1=Sun, 7=Sat
                    if (idx >= 0 && idx < 7 && parseInt(item.num_days) > 0) {
                        avgSessions[idx] = Math.round(parseInt(item.total_sessions) / parseInt(item.num_days));
                    }
                });

                var options = {
                    series: [{ name: 'Avg Sessions', data: avgSessions }],
                    chart: { type: 'bar', height: 320, toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
                    colors: ['#5156be'],
                    plotOptions: { bar: { borderRadius: 4, columnWidth: '50%' } },
                    dataLabels: { enabled: false },
                    xaxis: { categories: categories },
                    yaxis: { min: 0, forceNiceScale: true, title: { text: 'Avg Sessions' } }
                };

                chartInstances['dayOfWeek'] = new ApexCharts(container, options);
                chartInstances['dayOfWeek'].render();
            } else {
                renderNoDataChart('day-of-week-chart', 'No data');
            }
        },
        error: function() {
            renderNoDataChart('day-of-week-chart', 'Error loading data');
        }
    });
}

// =============================================
// 7. Top Pages (table)
// =============================================
function loadTopPages() {
    $.ajax({
        url: base_url + 'admin_root/analytics/get_top_pages',
        type: 'POST',
        data: getFilterDates(),
        dataType: 'json',
        success: function(response) {
            var tbody = $('#top_pages_body');
            tbody.empty();

            if (response.status == 200 && response.data && response.data.length > 0) {
                response.data.forEach(function(item, index) {
                    // Clean URL for display - remove base URL
                    var displayUrl = item.page_url.replace(/^https?:\/\/[^\/]+/, '');
                    if (!displayUrl) displayUrl = '/';
                    if (displayUrl.length > 50) displayUrl = displayUrl.substring(0, 50) + '...';

                    var typeClass = 'secondary';
                    switch (item.page_type) {
                        case 'home': typeClass = 'primary'; break;
                        case 'product': typeClass = 'success'; break;
                        case 'category': typeClass = 'info'; break;
                        case 'cart': typeClass = 'warning'; break;
                        case 'checkout': typeClass = 'danger'; break;
                    }

                    var row = '<tr>' +
                        '<td>' + (index + 1) + '</td>' +
                        '<td><span class="text-primary" title="' + item.page_url + '">' + displayUrl + '</span></td>' +
                        '<td><span class="badge bg-soft-' + typeClass + ' text-' + typeClass + '">' + (item.page_type || 'other') + '</span></td>' +
                        '<td><strong>' + parseInt(item.views).toLocaleString('en-IN') + '</strong></td>' +
                        '<td>' + formatDuration(parseInt(item.avg_time) || 0) + '</td>' +
                        '</tr>';
                    tbody.append(row);
                });
            } else {
                tbody.append('<tr><td colspan="5" class="text-center text-muted">No page view data for this period</td></tr>');
            }
        },
        error: function() {
            $('#top_pages_body').html('<tr><td colspan="5" class="text-center text-muted">Error loading data</td></tr>');
        }
    });
}

// =============================================
// 8. Conversion Funnel (horizontal bar)
// =============================================
function loadFunnelChart() {
    $.ajax({
        url: base_url + 'admin_root/analytics/get_conversion_funnel',
        type: 'POST',
        data: getFilterDates(),
        dataType: 'json',
        success: function(response) {
            destroyChart('funnel');
            var container = document.querySelector('#funnel-chart');
            if (!container) return;

            if (response.status == 200 && response.data) {
                var d = response.data;
                var totalSessions = parseInt(d.total_sessions) || 0;
                var cartAdds = parseInt(d.cart_adds) || 0;
                var checkouts = parseInt(d.checkouts) || 0;
                var purchases = parseInt(d.purchases) || 0;

                if (totalSessions == 0) {
                    renderNoDataChart('funnel-chart', 'No funnel data');
                    return;
                }

                var options = {
                    series: [{ name: 'Sessions', data: [totalSessions, cartAdds, checkouts, purchases] }],
                    chart: { type: 'bar', height: 320, toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
                    plotOptions: {
                        bar: { borderRadius: 4, horizontal: true, barHeight: '60%', distributed: true }
                    },
                    colors: ['#5156be', '#4ba6ef', '#ffbf53', '#2ab57d'],
                    dataLabels: {
                        enabled: true,
                        formatter: function(val, opts) {
                            if (opts.dataPointIndex == 0) return val;
                            var pct = totalSessions > 0 ? Math.round((val / totalSessions) * 100) : 0;
                            return val + ' (' + pct + '%)';
                        }
                    },
                    xaxis: { categories: ['Sessions', 'Added to Cart', 'Checkout', 'Purchased'] },
                    yaxis: { labels: { style: { fontSize: '13px' } } },
                    legend: { show: false }
                };

                chartInstances['funnel'] = new ApexCharts(container, options);
                chartInstances['funnel'].render();
            } else {
                renderNoDataChart('funnel-chart', 'No funnel data');
            }
        },
        error: function() {
            renderNoDataChart('funnel-chart', 'Error loading data');
        }
    });
}

// =============================================
// 9. Sessions by Country (horizontal bar)
// =============================================
function loadCountryChart() {
    $.ajax({
        url: base_url + 'admin_root/analytics/get_sessions_by_country',
        type: 'POST',
        data: getFilterDates(),
        dataType: 'json',
        success: function(response) {
            destroyChart('country');
            var container = document.querySelector('#country-chart');
            if (!container) return;

            if (response.status == 200 && response.data && response.data.length > 0) {
                var categories = [];
                var series = [];

                response.data.forEach(function(item) {
                    categories.push(item.country || 'Unknown');
                    series.push(parseInt(item.count));
                });

                var options = {
                    series: [{ name: 'Sessions', data: series }],
                    chart: { type: 'bar', height: 300, toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
                    plotOptions: { bar: { borderRadius: 4, horizontal: true, barHeight: '60%' } },
                    colors: ['#4ba6ef'],
                    dataLabels: { enabled: true },
                    xaxis: { categories: categories },
                    yaxis: { labels: { style: { fontSize: '13px' } } }
                };

                chartInstances['country'] = new ApexCharts(container, options);
                chartInstances['country'].render();
            } else {
                renderNoDataChart('country-chart', 'No country data yet (requires ipinfo geo lookup)');
            }
        },
        error: function() {
            renderNoDataChart('country-chart', 'Error loading data');
        }
    });
}

// =============================================
// 10. Live Sessions Count
// =============================================
function loadLiveCount() {
    $.ajax({
        url: base_url + 'admin_root/analytics/live_sessions',
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response.status == 200) {
                var count = response.count || 0;
                $('#live_count').text(count);
                if (count > 0) {
                    $('#live_badge').removeClass('bg-secondary').addClass('bg-success');
                } else {
                    $('#live_badge').removeClass('bg-success').addClass('bg-secondary');
                }
            }
        }
    });
}
</script>
