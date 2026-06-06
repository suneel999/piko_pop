<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Edit Category</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="<?php echo base_url('admin_root/products/categories') ?>">Categories</a></li>
                            <li class="breadcrumb-item active">Edit Category</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-xl-8 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Edit Category</h4>
                        <form class="needs-validation" id="update_category" name="update_category" action="<?php echo base_url('admin_root/products/update_category') ?>">
                            <input type="hidden" name="cid" value="<?php echo $category_data->id ?>">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="category_name">Category Name<code>*</code></label>
                                        <input type="text" class="form-control" id="category_name" name="category_name" placeholder="Category Name" value="<?php echo $category_data->category_name ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="parent_category">Parent Category</label>
                                        <select name="parent_category" id="parent_category" class="form-control select2">
                                            <option value="">Select Parent Category (Optional)</option>
                                            <?php
                                            $categories = get_parent_categories();
                                            if (!empty($categories)) {
                                                foreach ($categories as $category) {
                                                    $selected = ($category_data->parent_category_id == $category->id) ? 'selected' : '';
                                                    echo '<option value="' . $category->id . '" ' . $selected . '>' . $category->category_name . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="icon">Icon Image</label>
                                        <input type="file" class="form-control" id="icon" name="icon" accept="image/*">
                                        <?php if (!empty($category_data->icon)): ?>
                                            <div class="mt-2">
                                                <img src="<?php echo base_url('uploads/categories/') . $category_data->icon ?>" class="rounded avatar-md" alt="Current Icon">
                                                <small class="text-muted d-block">Current Icon</small>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="sort_order">Sort Order</label>
                                        <input type="number" class="form-control" id="sort_order" name="sort_order" placeholder="Sort Order" value="<?php echo $category_data->sort_order ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label d-block">Featured Category</label>
                                        <div class="form-check form-switch form-switch-lg">
                                            <input class="form-check-input" type="checkbox" id="featured" name="featured" value="1" <?php echo ($category_data->featured == 1) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="featured">Show on homepage as featured</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label d-block">Menu Item</label>
                                        <div class="form-check form-switch form-switch-lg">
                                            <input class="form-check-input" type="checkbox" id="is_menu_item" name="is_menu_item" value="1" <?php echo ($category_data->is_menu_item == 1) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="is_menu_item">Show in navigation menu</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button class="btn btn-primary mx-auto d-block" type="submit" id="submit_button">Update Category</button>
                        </form>
                    </div>
                </div>
                <!-- end card -->
            </div> <!-- end col -->
        </div>
        <!-- end row -->

    </div> <!-- container-fluid -->
</div>
<!-- End Page-content -->
