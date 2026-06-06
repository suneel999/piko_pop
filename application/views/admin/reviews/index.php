<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Customer Reviews</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Reviews</a></li>
                            <li class="breadcrumb-item active">Customer Reviews</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <!-- Filters -->
        <div class="row">
            <div class="col-xl-12 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <form id="reviews_filter_form">
                            <div class="row align-items-end">
                                <div class="col-md-2">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="date_from">Date From</label>
                                        <input type="text" class="form-control flatpickr-date" name="date_from" id="date_from" placeholder="Select date">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="date_to">Date To</label>
                                        <input type="text" class="form-control flatpickr-date" name="date_to" id="date_to" placeholder="Select date">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="rating">Rating</label>
                                        <select class="form-control select2" name="rating" id="rating">
                                            <option value="">All Ratings</option>
                                            <option value="5">5 Stars</option>
                                            <option value="4">4 Stars</option>
                                            <option value="3">3 Stars</option>
                                            <option value="2">2 Stars</option>
                                            <option value="1">1 Star</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group mb-3">
                                        <button class="btn btn-primary w-100" type="submit" id="submit_button">
                                            <i class="uil-search-alt me-1"></i> Get Report
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group mb-3">
                                        <button class="btn btn-outline-secondary w-100" type="button" id="clear_filters">
                                            <i class="uil-times me-1"></i> Clear
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row">
            <div class="col-md-3">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium mb-2">Total Reviews</p>
                                <h4 class="mb-0 text-primary" id="summary_total_reviews">0</h4>
                            </div>
                            <div class="mini-stat-icon avatar-sm align-self-center rounded-circle bg-primary">
                                <span class="avatar-title"><i class="uil-comment-alt-message font-size-24"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium mb-2">Average Rating</p>
                                <h4 class="mb-0 text-warning" id="summary_avg_rating">0</h4>
                            </div>
                            <div class="mini-stat-icon avatar-sm align-self-center rounded-circle bg-warning">
                                <span class="avatar-title"><i class="uil-star font-size-24"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium mb-2">5-Star Reviews</p>
                                <h4 class="mb-0 text-success" id="summary_five_star">0</h4>
                            </div>
                            <div class="mini-stat-icon avatar-sm align-self-center rounded-circle bg-success">
                                <span class="avatar-title"><i class="uil-trophy font-size-24"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium mb-2">Recommend Rate</p>
                                <h4 class="mb-0 text-info" id="summary_recommend_rate">0%</h4>
                            </div>
                            <div class="mini-stat-icon avatar-sm align-self-center rounded-circle bg-info">
                                <span class="avatar-title"><i class="uil-thumbs-up font-size-24"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- DataTable -->
        <style>
            #reviews_table_length { float: right !important; }
        </style>
        <div class="row">
            <div class="col-xl-12 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="reviews_table" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Order</th>
                                        <th>Customer</th>
                                        <th>Phone</th>
                                        <th>Rating</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
