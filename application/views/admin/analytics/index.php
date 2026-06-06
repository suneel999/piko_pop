<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Traffic Analytics</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="<?php echo base_url('admin_root/') ?>">Dashboard</a></li>
                            <li class="breadcrumb-item active">Traffic Analytics</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <!-- Live Sessions Banner -->
        <div class="row mb-2">
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <span class="badge bg-success fs-6 px-3 py-2 me-3" id="live_badge">
                            <span class="live-dot me-2"></span>
                            <span id="live_count">0</span> Active Now
                        </span>
                        <small class="text-muted">Users on site right now</small>
                    </div>
                    <a href="<?php echo base_url('admin_root/analytics/sessions') ?>" class="btn btn-sm btn-outline-primary">
                        <i class="uil-list-ul me-1"></i>View All Sessions
                    </a>
                </div>
            </div>
        </div>

        <style>
        .live-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            background: #fff;
            border-radius: 50%;
            animation: livePulse 1.5s ease-in-out infinite;
        }
        @keyframes livePulse {
            0%, 100% { opacity: 1; box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.7); }
            50% { opacity: 0.7; box-shadow: 0 0 0 6px rgba(255, 255, 255, 0); }
        }
        </style>

        <!-- Date Filter -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form id="analytics_filter_form" class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label">Date From</label>
                                <input type="text" class="form-control flatpickr-date" id="date_from" name="date_from" placeholder="Select date">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Date To</label>
                                <input type="text" class="form-control flatpickr-date" id="date_to" name="date_to" placeholder="Select date">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="uil-filter me-1"></i> Apply Filter
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="btn_reset_filter">
                                    <i class="uil-redo me-1"></i> Reset
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Stat Cards -->
        <div class="row">
            <div class="col-xl-3 col-md-6">
                <div class="card dashboard-card">
                    <div class="stat-card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="flex-grow-1">
                                <p class="stat-label">Site Sessions</p>
                                <h3 class="stat-value" id="stat_sessions">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                </h3>
                            </div>
                            <div class="stat-icon icon-bg-primary">
                                <i class="uil uil-globe"></i>
                            </div>
                        </div>
                    </div>
                    <div class="stat-footer">
                        <span class="stat-badge bg-primary-subtle text-primary">
                            <i class="uil uil-eye me-1"></i> <span id="stat_pageviews">0</span> Page Views
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card dashboard-card">
                    <div class="stat-card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="flex-grow-1">
                                <p class="stat-label">Unique Visitors</p>
                                <h3 class="stat-value" id="stat_visitors">
                                    <div class="spinner-border spinner-border-sm text-success" role="status"></div>
                                </h3>
                            </div>
                            <div class="stat-icon icon-bg-success">
                                <i class="uil uil-users-alt"></i>
                            </div>
                        </div>
                    </div>
                    <div class="stat-footer">
                        <span class="stat-badge bg-success-subtle text-success">
                            <i class="uil uil-user-check"></i> Distinct Visitors
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card dashboard-card">
                    <div class="stat-card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="flex-grow-1">
                                <p class="stat-label">Avg Session Duration</p>
                                <h3 class="stat-value" id="stat_duration">
                                    <div class="spinner-border spinner-border-sm text-warning" role="status"></div>
                                </h3>
                            </div>
                            <div class="stat-icon icon-bg-warning">
                                <i class="uil uil-clock-three"></i>
                            </div>
                        </div>
                    </div>
                    <div class="stat-footer">
                        <span class="stat-badge bg-warning-subtle text-warning">
                            <i class="uil uil-stopwatch"></i> Average Time
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card dashboard-card">
                    <div class="stat-card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="flex-grow-1">
                                <p class="stat-label">Bounce Rate</p>
                                <h3 class="stat-value" id="stat_bounce">
                                    <div class="spinner-border spinner-border-sm text-danger" role="status"></div>
                                </h3>
                            </div>
                            <div class="stat-icon icon-bg-danger">
                                <i class="uil uil-chart-down"></i>
                            </div>
                        </div>
                    </div>
                    <div class="stat-footer">
                        <span class="stat-badge bg-danger-subtle text-danger">
                            <i class="uil uil-arrow-down-right"></i> Single-page Sessions
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart Row 1: Sessions Over Time + New vs Returning -->
        <div class="row">
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4"><i class="uil uil-chart-line text-primary me-2"></i>Sessions Over Time</h5>
                        <div id="sessions-over-time-chart" style="min-height: 350px;"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4"><i class="uil uil-user-plus text-success me-2"></i>New vs Returning Visitors</h5>
                        <div id="new-vs-returning-chart" style="min-height: 350px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart Row 2: Device + Sources + Day of Week -->
        <div class="row">
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4"><i class="uil uil-monitor text-primary me-2"></i>Sessions by Device</h5>
                        <div id="device-chart" style="min-height: 320px;"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4"><i class="uil uil-link-alt text-info me-2"></i>Traffic Sources</h5>
                        <div id="sources-chart" style="min-height: 320px;"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4"><i class="uil uil-calendar-alt text-warning me-2"></i>Avg Sessions by Day</h5>
                        <div id="day-of-week-chart" style="min-height: 320px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart Row 3: Top Pages + Conversion Funnel -->
        <div class="row">
            <div class="col-xl-7">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4"><i class="uil uil-file-alt text-primary me-2"></i>Top Pages</h5>
                        <div class="table-responsive">
                            <table class="table table-hover table-sm mb-0" id="top_pages_table">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Page</th>
                                        <th>Type</th>
                                        <th>Views</th>
                                        <th>Avg Time</th>
                                    </tr>
                                </thead>
                                <tbody id="top_pages_body">
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            <div class="spinner-border spinner-border-sm text-primary" role="status"></div> Loading...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-5">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4"><i class="uil uil-filter text-success me-2"></i>Conversion Funnel</h5>
                        <div id="funnel-chart" style="min-height: 320px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart Row 4: Sessions by Country -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4"><i class="uil uil-map-marker text-info me-2"></i>Sessions by Country</h5>
                        <div id="country-chart" style="min-height: 300px;"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
