<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Inventory Report</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Reports</a></li>
                            <li class="breadcrumb-item active">Inventory Report</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->


        <div class="row">
            <div class="col-xl-12 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <form class="needs-validation" id="inventory_report_form" name="inventory_report_form" action="">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="category">Category</label>
                                        <select class="form-control select2" name="category" id="category">
                                            <option value="all">All Categories</option>
                                            <?php
                                            $categories = get_all_categories_with_status();
                                            if (!empty($categories)) {
                                                foreach ($categories as $category) {
                                                    $prefix = ($category->parent_category_id != 0) ? '-- ' : '';
                                                    echo '<option value="' . $category->id . '">' . $prefix . $category->category_name . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="brand">Brand</label>
                                        <select class="form-control select2" name="brand" id="brand">
                                            <option value="">All Brands</option>
                                            <?php
                                            if (!empty($brands)) {
                                                foreach ($brands as $brand) {
                                                    echo '<option value="' . htmlspecialchars($brand) . '">' . htmlspecialchars($brand) . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="stock_status">Stock Status</label>
                                        <select class="form-control select2" name="stock_status" id="stock_status">
                                            <option value="">All</option>
                                            <option value="in_stock">In Stock</option>
                                            <option value="low_stock">Low Stock (≤10)</option>
                                            <option value="out_of_stock">Out of Stock</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-3">
                                        <label class="form-label">&nbsp;</label>
                                        <button class="btn btn-primary w-100" type="submit" id="submit_button">
                                            <i class="uil-search-alt me-1"></i> Get Report
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- end card -->
            </div> <!-- end col -->
        </div>

        <style>
            #inventory_data_length {
                float: right !important;
            }
        </style>
        <div class="row">
            <div class="col-xl-12 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="inventory_data" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Product Name</th>
                                        <th>Category</th>
                                        <th>Brand</th>
                                        <th>Weight</th>
                                        <th>MRP</th>
                                        <th>Sale Price</th>
                                        <th>Stock</th>
                                        <th>Stock Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="7" class="text-end">Totals:</th>
                                        <th id="total_stock">-</th>
                                        <th id="total_value">-</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- end card -->
            </div> <!-- end col -->
        </div>
    </div> <!-- container-fluid -->
</div>
<!-- End Page-content -->
