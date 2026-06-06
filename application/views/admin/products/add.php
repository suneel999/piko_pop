<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Add Product</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="<?php echo base_url('admin_root/products') ?>">Products</a></li>
                            <li class="breadcrumb-item active">Add Product</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->

        <form class="needs-validation" id="add_product" name="add_product" action="<?php echo base_url('admin_root/products/save_product') ?>" enctype="multipart/form-data">
            <div class="row">
                <div class="col-xl-8">
                    <!-- Basic Information Card -->
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Basic Information</h4>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="product_name">Product Name<code>*</code></label>
                                        <input type="text" class="form-control" id="product_name" name="product_name" placeholder="Product Name" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="mrp">MRP<code>*</code></label>
                                        <input type="number" step="0.01" class="form-control" id="mrp" name="mrp" placeholder="MRP" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="sale_price">Sale Price<code>*</code></label>
                                        <input type="number" step="0.01" class="form-control" id="sale_price" name="sale_price" placeholder="Sale Price" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="brand">Brand</label>
                                        <input type="text" class="form-control" id="brand" name="brand" placeholder="Brand Name">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="product_weight">Weight</label>
                                        <input type="text" class="form-control" id="product_weight" name="product_weight" placeholder="e.g., 500">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="weight_unit">Unit</label>
                                        <select name="weight_unit" id="weight_unit" class="form-control">
                                            <option value="">Select</option>
                                            <option value="g">g</option>
                                            <option value="kg">kg</option>
                                            <option value="ml">ml</option>
                                            <option value="l">l</option>
                                            <option value="pcs">pcs</option>
                                            <option value="pack">pack</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="stock">Stock</label>
                                        <input type="number" step="0.01" class="form-control" id="stock" name="stock" placeholder="e.g., 100" value="0">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="is_variant">Has Variants?</label>
                                        <select name="is_variant" id="is_variant" class="form-control">
                                            <option value="0">No</option>
                                            <option value="1">Yes</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description Card -->
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Description</h4>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="short_description">Short Description</label>
                                        <textarea class="form-control" id="short_description" name="short_description" rows="2" placeholder="Brief product description (displayed in listings)"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="description">Full Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="5" placeholder="Detailed product description"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Info / Meta Data Card -->
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h4 class="card-title mb-0">Additional Information</h4>
                                    <p class="text-muted mb-0">Add product specifications and details</p>
                                </div>
                                <button type="button" class="btn btn-soft-primary btn-sm" id="add_meta_row">
                                    <i class="uil uil-plus me-1"></i> Add Field
                                </button>
                            </div>

                            <div id="meta_fields_container">
                                <!-- Dynamic rows will be added here -->
                                <div class="row meta-row mb-2">
                                    <div class="col-md-4">
                                        <div class="form-group mb-0">
                                            <input type="text" class="form-control" name="meta_title[]" placeholder="Title (e.g., Specialty)">
                                        </div>
                                    </div>
                                    <div class="col-md-7">
                                        <div class="form-group mb-0">
                                            <input type="text" class="form-control" name="meta_content[]" placeholder="Content (e.g., Organic, Vegetarian)">
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-soft-danger btn-sm remove-meta-row" title="Remove">
                                            <i class="uil uil-trash-alt"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Images Card -->
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Product Images</h4>
                            <p class="text-muted mb-3">Drag images to reorder after selection. First image will be the primary image.</p>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="product_images">Upload Images</label>
                                        <input type="file" class="form-control" id="product_images" name="product_images[]" accept="image/*" multiple>
                                        <small class="text-muted">Select multiple images to upload</small>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div id="new_images_preview" class="d-flex flex-wrap gap-3"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Instructions Image Card -->
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Instructions Image</h4>
                            <p class="text-muted mb-3">Upload product instructions/usage guide images for desktop and mobile views.</p>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="instructions_image_desktop">Desktop Image</label>
                                        <input type="file" class="form-control" id="instructions_image_desktop" name="instructions_image_desktop" accept="image/*">
                                        <small class="text-muted">Recommended: 600x200px (Landscape)</small>
                                    </div>
                                    <div id="desktop_preview" class="mt-2"></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="instructions_image_mobile">Mobile Image</label>
                                        <input type="file" class="form-control" id="instructions_image_mobile" name="instructions_image_mobile" accept="image/*">
                                        <small class="text-muted">Recommended: 400x300px (Square/Portrait)</small>
                                    </div>
                                    <div id="mobile_preview" class="mt-2"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!-- end col -->

                <div class="col-xl-4">
                    <!-- Submit Button Card -->
                    <div class="card">
                        <div class="card-body text-center">
                            <button class="btn btn-primary w-100" type="submit" id="submit_button">
                                <i class="uil uil-plus me-1"></i> Add Product
                            </button>
                            <a href="<?php echo base_url('admin_root/products') ?>" class="btn btn-outline-secondary mt-2 w-100">
                                <i class="uil uil-arrow-left me-1"></i> Back to Products
                            </a>
                        </div>
                    </div>

                    <!-- Category Card -->
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Select Category<code>*</code></h4>
                            <p class="text-muted mb-3">Select one or more categories for this product</p>
                            <div id="category_tree"></div>
                            <input type="hidden" name="category_id" id="category_id">
                        </div>
                    </div>
                </div>

            </div>
            <!-- end row -->
        </form>
    </div> <!-- container-fluid -->
</div>
<!-- End Page-content -->
