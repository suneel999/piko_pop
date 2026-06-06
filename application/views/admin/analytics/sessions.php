<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Sessions</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="<?php echo base_url('admin_root/analytics') ?>">Analytics</a></li>
                            <li class="breadcrumb-item active">Sessions</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <!-- Live Sessions Badge -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <span class="badge bg-success fs-6 px-3 py-2" id="live_badge">
                            <span class="live-dot me-2"></span>
                            <span id="live_count">0</span> Active Now
                        </span>
                    </div>
                    <small class="text-muted">Users active in the last 5 minutes &middot; Auto-refreshes every 15s</small>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form id="sessions_filter_form" class="row g-3 align-items-end">
                            <div class="col-md-2">
                                <label class="form-label">Date From</label>
                                <input type="text" class="form-control flatpickr-date" id="date_from" name="date_from" placeholder="Select date">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Date To</label>
                                <input type="text" class="form-control flatpickr-date" id="date_to" name="date_to" placeholder="Select date">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Device</label>
                                <select name="device_type" id="device_type" class="form-select">
                                    <option value="">All Devices</option>
                                    <option value="desktop">Desktop</option>
                                    <option value="mobile">Mobile</option>
                                    <option value="tablet">Tablet</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Behavior</label>
                                <select name="is_bounce" id="is_bounce" class="form-select">
                                    <option value="">All</option>
                                    <option value="1">Bounced Only</option>
                                    <option value="0">Engaged Only</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" id="has_purchase" name="has_purchase" value="1">
                                    <label class="form-check-label" for="has_purchase">Purchases Only</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary me-1">
                                    <i class="uil-filter me-1"></i> Filter
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="btn_reset">
                                    <i class="uil-redo"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sessions Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0"><i class="uil-browser text-primary me-2"></i>All Sessions</h5>
                            <span class="text-muted" id="sessions_total">0 sessions</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped table-sm" id="sessions_table" style="width:100%">
                                <thead class="table-light">
                                    <tr>
                                        <th width="3%">#</th>
                                        <th width="14%">Visitor</th>
                                        <th width="10%">Device</th>
                                        <th width="14%">Landing Page</th>
                                        <th width="5%">Pages</th>
                                        <th width="7%">Duration</th>
                                        <th width="8%">Source</th>
                                        <th width="10%">Location</th>
                                        <th width="12%">Status</th>
                                        <th width="10%">Date</th>
                                        <th width="7%">Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Live Sessions Card (expandable) -->
        <div class="row" id="live_sessions_section" style="display:none;">
            <div class="col-12">
                <div class="card border border-success">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <span class="live-dot me-2"></span>
                            <span class="text-success">Live Visitors</span>
                            <small class="text-muted ms-2">(Active in last 5 minutes)</small>
                        </h5>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Visitor</th>
                                        <th>Current Page</th>
                                        <th>Pages Viewed</th>
                                        <th>Device</th>
                                        <th>Location</th>
                                        <th>Last Activity</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="live_sessions_body"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
.live-dot {
    display: inline-block;
    width: 8px;
    height: 8px;
    background: #2ab57d;
    border-radius: 50%;
    animation: livePulse 1.5s ease-in-out infinite;
}
@keyframes livePulse {
    0%, 100% { opacity: 1; box-shadow: 0 0 0 0 rgba(42, 181, 125, 0.7); }
    50% { opacity: 0.7; box-shadow: 0 0 0 6px rgba(42, 181, 125, 0); }
}
</style>
