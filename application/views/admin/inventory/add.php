<style>
    .remove_item_btn {
        background: #f46a6a;
        height: 30px;
        width: 30px;
        display: block;
        text-align: center;
        color: #fff;
        border-radius: 50%;
        font-size: 20px;
        cursor: pointer;
        border: none;
    }
</style>
<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Add Stock</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="<?php echo base_url('admin_root/inventory') ?>">Inventory</a></li>
                            <li class="breadcrumb-item active">Add Stock</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->

        <form class="needs-validation" id="add_stock_form" name="add_stock_form">
            <div class="row">
                <div class="col-xl-12 mx-auto">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center mb-3">
                                <div class="col-md-4">
                                    <h5 class="card-title mb-0">Stock Entry</h5>
                                    <small class="text-muted">Search products and add stock quantities</small>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-0">
                                        <input type="text" class="form-control typeahead" id="product_search" name="product_search" placeholder="Search by product name...">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-0">
                                        <select class="form-control select2" id="category_filter" name="category_filter">
                                            <option value="">Filter by Category</option>
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
                            </div>

                            <div class="table-responsive">
                                <table width="100%" class="table table-bordered table-sm" id="stock_table">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 50px;">S.No.</th>
                                            <th>Product Name</th>
                                            <th style="width: 120px;">Current Stock</th>
                                            <th style="width: 120px;">Add Qty</th>
                                            <th style="width: 100px;">Unit</th>
                                            <th style="width: 80px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="stock_tbody">
                                        <!-- Dynamic rows will be added here -->
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-end fw-bold">Total Quantity to Add:</td>
                                            <td class="fw-bold" id="total_qty">0</td>
                                            <td colspan="2"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer d-none" id="card_footer">
                            <div class="text-center">
                                <button class="btn btn-success" type="submit" id="submit_button">
                                    <i class="uil uil-check me-1"></i> Update Stock
                                </button>
                                <button class="btn btn-warning ms-2" type="button" id="cancel_button" onclick="cancelStock()">
                                    <i class="uil uil-times me-1"></i> Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- end card -->
                </div> <!-- end col -->
            </div>
            <!-- end row -->
        </form>
    </div> <!-- container-fluid -->
</div>
<!-- End Page-content -->
