<script>
    var sessionsTable;

    $(document).ready(function() {
        // Init Flatpickr
        var today = new Date();
        var sevenDaysAgo = new Date();
        sevenDaysAgo.setDate(today.getDate() - 7);

        flatpickr('#date_from', {
            dateFormat: 'Y-m-d',
            defaultDate: sevenDaysAgo,
            maxDate: 'today'
        });
        flatpickr('#date_to', {
            dateFormat: 'Y-m-d',
            defaultDate: today,
            maxDate: 'today'
        });

        // Init DataTable
        sessionsTable = $('#sessions_table').DataTable({
            dom: 'lBfrtip',
            buttons: [{
                    extend: 'excel',
                    className: 'btn btn-success btn-sm',
                    text: '<i class="uil uil-file-export me-1"></i> Excel',
                    filename: 'Sessions_' + new Date().toISOString().slice(0, 10),
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
                    }
                },
                {
                    extend: 'print',
                    className: 'btn btn-info btn-sm',
                    text: '<i class="uil uil-print me-1"></i> Print',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
                    }
                }
            ],
            processing: true,
            serverSide: true,
            pageLength: 25,
            order: [
                [9, 'desc']
            ],
            ajax: {
                url: base_url + 'admin_root/analytics/sessions_data',
                type: 'POST',
                data: function(d) {
                    var form = $('#sessions_filter_form').serializeArray();
                    $.each(form, function(_, f) { d[f.name] = f.value; });
                    return d;
                },
                dataFilter: function(raw) {
                    try {
                        var json = JSON.parse(raw);
                        $('#sessions_total').text((json.recordsFiltered || 0) + ' sessions');
                    } catch (e) {}
                    return raw;
                }
            },
            columns: [{
                    title: '#'
                },
                {
                    title: 'Visitor'
                },
                {
                    title: 'Device'
                },
                {
                    title: 'Landing Page'
                },
                {
                    title: 'Pages'
                },
                {
                    title: 'Duration'
                },
                {
                    title: 'Source'
                },
                {
                    title: 'Location'
                },
                {
                    title: 'Status'
                },
                {
                    title: 'Date'
                },
                {
                    title: 'Action',
                    orderable: false
                }
            ],
            language: {
                emptyTable: 'No sessions found for this period',
                loadingRecords: '<div class="spinner-border spinner-border-sm text-primary"></div> Loading sessions...'
            }
        });

        // Filter form submit
        $('#sessions_filter_form').on('submit', function(e) {
            e.preventDefault();
            sessionsTable.ajax.reload();
        });

        // Reset filter
        $('#btn_reset').on('click', function() {
            document.getElementById('date_from')._flatpickr.setDate(sevenDaysAgo);
            document.getElementById('date_to')._flatpickr.setDate(today);
            $('#device_type').val('');
            $('#is_bounce').val('');
            $('#has_purchase').prop('checked', false);
            sessionsTable.ajax.reload();
        });

        // =============================================
        // Live Sessions - auto-refresh every 15s
        // =============================================
        loadLiveSessions();
        setInterval(loadLiveSessions, 15000);
    });

    function loadLiveSessions() {
        $.ajax({
            url: base_url + 'admin_root/analytics/live_sessions',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.status == 200) {
                    var count = response.count || 0;
                    $('#live_count').text(count);

                    // Update live badge color
                    if (count > 0) {
                        $('#live_badge').removeClass('bg-secondary').addClass('bg-success');
                        $('#live_sessions_section').slideDown(300);
                    } else {
                        $('#live_badge').removeClass('bg-success').addClass('bg-secondary');
                        $('#live_sessions_section').slideUp(300);
                    }

                    // Populate live sessions table
                    var tbody = $('#live_sessions_body');
                    tbody.empty();

                    if (response.sessions && response.sessions.length > 0) {
                        response.sessions.forEach(function(s) {
                            var deviceIcon = s.device_type == 'mobile' ? 'uil-mobile-android' : (s.device_type == 'tablet' ? 'uil-tablet' : 'uil-monitor');
                            var location = '';
                            if (s.city && s.country) location = s.city + ', ' + s.country;
                            else if (s.country) location = s.country;
                            else location = '-';

                            var agoText = '';
                            if (s.seconds_ago < 60) agoText = s.seconds_ago + 's ago';
                            else agoText = Math.floor(s.seconds_ago / 60) + 'm ago';

                            var cartBadge = s.has_cart ? ' <span class="badge bg-soft-warning text-warning">In Cart</span>' : '';

                            var currentPage = s.current_page || '/';
                            if (currentPage.length > 35) currentPage = currentPage.substring(0, 35) + '...';

                            var row = '<tr>' +
                                '<td><strong>' + (s.user_name || 'Guest') + '</strong>' + cartBadge + '</td>' +
                                '<td><code class="text-primary">' + currentPage + '</code></td>' +
                                '<td>' + s.page_views + '</td>' +
                                '<td><i class="' + deviceIcon + '"></i> ' + s.device_type + '</td>' +
                                '<td>' + location + '</td>' +
                                '<td><span class="text-success fw-semibold">' + agoText + '</span></td>' +
                                '<td><a href="' + base_url + 'admin_root/analytics/journey/' + s.id + '" class="btn btn-sm btn-soft-primary"><i class="uil-rss-alt"></i></a></td>' +
                                '</tr>';
                            tbody.append(row);
                        });
                    }
                }
            }
        });
    }
</script>