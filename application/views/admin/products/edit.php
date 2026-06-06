<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Edit Product</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="<?php echo base_url('admin_root/products') ?>">Products</a></li>
                            <li class="breadcrumb-item active">Edit Product</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->

        <?php
        $meta_data = !empty($product_data->meta_data) ? json_decode($product_data->meta_data, true) : array();
        ?>

        <form class="needs-validation" id="edit_product" name="edit_product" action="<?php echo base_url('admin_root/products/update_product') ?>" enctype="multipart/form-data">
            <input type="hidden" name="pid" value="<?php echo $product_data->id ?>">
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
                                        <input type="text" class="form-control" id="product_name" name="product_name" placeholder="Product Name" value="<?php echo $product_data->product_name ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="mrp">MRP<code>*</code></label>
                                        <input type="number" step="0.01" class="form-control" id="mrp" name="mrp" placeholder="MRP" value="<?php echo $product_data->mrp ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="sale_price">Sale Price<code>*</code></label>
                                        <input type="number" step="0.01" class="form-control" id="sale_price" name="sale_price" placeholder="Sale Price" value="<?php echo $product_data->sale_price ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="brand">Brand</label>
                                        <input type="text" class="form-control" id="brand" name="brand" placeholder="Brand Name" value="<?php echo $product_data->brand ?>">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="product_weight">Weight</label>
                                        <input type="text" class="form-control" id="product_weight" name="product_weight" placeholder="e.g., 500" value="<?php echo $product_data->product_weight ?>">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="weight_unit">Unit</label>
                                        <select name="weight_unit" id="weight_unit" class="form-control">
                                            <option value="">Select</option>
                                            <option value="g" <?php echo ($product_data->weight_unit == 'g') ? 'selected' : '' ?>>g</option>
                                            <option value="kg" <?php echo ($product_data->weight_unit == 'kg') ? 'selected' : '' ?>>kg</option>
                                            <option value="ml" <?php echo ($product_data->weight_unit == 'ml') ? 'selected' : '' ?>>ml</option>
                                            <option value="l" <?php echo ($product_data->weight_unit == 'l') ? 'selected' : '' ?>>l</option>
                                            <option value="pcs" <?php echo ($product_data->weight_unit == 'pcs') ? 'selected' : '' ?>>pcs</option>
                                            <option value="pack" <?php echo ($product_data->weight_unit == 'pack') ? 'selected' : '' ?>>pack</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="stock">Stock</label>
                                        <input type="number" step="0.01" class="form-control" id="stock" name="stock" placeholder="e.g., 100" value="<?php echo isset($product_data->stock) ? $product_data->stock : 0 ?>">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="is_variant">Has Variants?</label>
                                        <select name="is_variant" id="is_variant" class="form-control">
                                            <option value="0" <?php echo (isset($product_data->is_variant) && $product_data->is_variant == 0) ? 'selected' : '' ?>>No</option>
                                            <option value="1" <?php echo (isset($product_data->is_variant) && $product_data->is_variant == 1) ? 'selected' : '' ?>>Yes</option>
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
                                        <textarea class="form-control" id="short_description" name="short_description" rows="2" placeholder="Brief product description (displayed in listings)"><?php echo isset($product_data->short_description) ? $product_data->short_description : '' ?></textarea>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="description">Full Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="5" placeholder="Detailed product description"><?php echo isset($product_data->description) ? $product_data->description : '' ?></textarea>
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
                                <?php if (!empty($meta_data) && is_array($meta_data)): ?>
                                    <?php foreach ($meta_data as $title => $content): ?>
                                        <div class="row meta-row mb-2">
                                            <div class="col-md-4">
                                                <div class="form-group mb-0">
                                                    <input type="text" class="form-control" name="meta_title[]" placeholder="Title" value="<?php echo htmlspecialchars($title) ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-7">
                                                <div class="form-group mb-0">
                                                    <input type="text" class="form-control" name="meta_content[]" placeholder="Content" value="<?php echo htmlspecialchars($content) ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" class="btn btn-soft-danger btn-sm remove-meta-row" title="Remove">
                                                    <i class="uil uil-trash-alt"></i>
                                                </button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <!-- Default empty row -->
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
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Images Card -->
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Product Images</h4>
                            <p class="text-muted mb-3">Drag images to reorder. First image will be the primary image.</p>

                            <?php
                            $images = json_decode($product_data->images);
                            if (!empty($images)):
                            ?>
                                <div class="mb-4">
                                    <label class="form-label">Current Images <small class="text-muted">(drag to reorder)</small></label>
                                    <div id="sortable_images" class="d-flex flex-wrap gap-3">
                                        <?php foreach ($images as $index => $image): ?>
                                            <div class="image-item position-relative" data-image="<?php echo $image ?>" style="cursor: move;">
                                                <img src="<?php echo base_url('uploads/products/') . $image ?>" class="rounded" style="width: 100px; height: 100px; object-fit: cover;" alt="Product Image">
                                                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 remove-existing-image" data-image="<?php echo $image ?>" style="padding: 2px 6px; font-size: 10px; border-radius: 50%;">
                                                    <i class="uil uil-times"></i>
                                                </button>
                                                <input type="hidden" name="existing_images[]" value="<?php echo $image ?>">
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="product_images">Add More Images</label>
                                        <input type="file" class="form-control" id="product_images" name="product_images[]" accept="image/*" multiple>
                                        <small class="text-muted">Select multiple images to add</small>
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
                                    <?php if (!empty($product_data->instructions_image_desktop)): ?>
                                        <div id="desktop_preview" class="mt-2">
                                            <div class="position-relative d-inline-block">
                                                <img src="<?php echo base_url('uploads/products/instructions/') . $product_data->instructions_image_desktop ?>" class="rounded" style="max-width: 200px; height: auto;">
                                                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 remove-instructions-image" data-type="desktop" style="padding: 2px 6px; font-size: 10px; border-radius: 50%;">
                                                    <i class="uil uil-times"></i>
                                                </button>
                                                <input type="hidden" name="existing_instructions_desktop" value="<?php echo $product_data->instructions_image_desktop ?>">
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div id="desktop_preview" class="mt-2"></div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="instructions_image_mobile">Mobile Image</label>
                                        <input type="file" class="form-control" id="instructions_image_mobile" name="instructions_image_mobile" accept="image/*">
                                        <small class="text-muted">Recommended: 400x300px (Square/Portrait)</small>
                                    </div>
                                    <?php if (!empty($product_data->instructions_image_mobile)): ?>
                                        <div id="mobile_preview" class="mt-2">
                                            <div class="position-relative d-inline-block">
                                                <img src="<?php echo base_url('uploads/products/instructions/') . $product_data->instructions_image_mobile ?>" class="rounded" style="max-width: 200px; height: auto;">
                                                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 remove-instructions-image" data-type="mobile" style="padding: 2px 6px; font-size: 10px; border-radius: 50%;">
                                                    <i class="uil uil-times"></i>
                                                </button>
                                                <input type="hidden" name="existing_instructions_mobile" value="<?php echo $product_data->instructions_image_mobile ?>">
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div id="mobile_preview" class="mt-2"></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product Variants Card -->
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h4 class="card-title mb-0">Product Variants</h4>
                                    <p class="text-muted mb-0">Add size/weight variants (e.g., 5kg, 10kg, 25kg)</p>
                                </div>
                                <button type="button" class="btn btn-soft-success btn-sm" id="add_variant_row">
                                    <i class="uil uil-plus me-1"></i> Add Variant
                                </button>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-sm mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 20%;">Variant Name</th>
                                            <th style="width: 12%;">MRP</th>
                                            <th style="width: 12%;">Sale Price</th>
                                            <th style="width: 10%;">Deduct Qty</th>
                                            <th style="width: 12%;">SKU</th>
                                            <th style="width: 20%;">Serves Text</th>
                                            <th style="width: 20%;">Package Weight</th>
                                            <th style="width: 14%;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="variants_table">
                                        <?php
                                        if (!empty($product_variants)) {
                                            foreach ($product_variants as $variant) {
                                        ?>
                                                <tr class="variant-row existing-variant" data-variant-id="<?php echo $variant->id ?>">
                                                    <td>
                                                        <div class="form-group mb-0">
                                                            <input type="hidden" name="existing_variant_id[]" value="<?php echo $variant->id ?>">
                                                            <input type="text" class="form-control form-control-sm" name="existing_variant_name[]" placeholder="e.g., 5kg Pack" value="<?php echo $variant->variant_name ?>">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group mb-0">
                                                            <input type="number" step="0.01" class="form-control form-control-sm" name="existing_variant_mrp[]" placeholder="MRP" value="<?php echo $variant->mrp ?>">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group mb-0">
                                                            <input type="number" step="0.01" class="form-control form-control-sm" name="existing_variant_sale_price[]" placeholder="Sale Price" value="<?php echo $variant->sale_price ?>">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group mb-0">
                                                            <input type="number" step="0.01" class="form-control form-control-sm" name="existing_variant_stock_qty[]" placeholder="e.g., 5" value="<?php echo $variant->stock_qty ?>">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group mb-0">
                                                            <input type="text" class="form-control form-control-sm" name="existing_variant_sku[]" placeholder="SKU (optional)" value="<?php echo $variant->sku ?>">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group mb-0">
                                                            <input type="text" class="form-control form-control-sm" name="existing_variant_servers_text[]" placeholder="e.g., For 200gm chicken" value="<?php echo isset($variant->servers_text) ? $variant->servers_text : '' ?>">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group mb-0">
                                                            <input type="number" step="0.10" class="form-control form-control-sm" name="existing_variant_package_weight[]" placeholder="e.g., 0.45" value="<?php echo isset($variant->package_weight) ? $variant->package_weight : '0' ?>">
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-soft-danger btn-sm delete-variant" data-variant-id="<?php echo $variant->id ?>">
                                                            <i class="uil uil-trash-alt"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <small class="text-muted mt-2 d-block">Deduct Qty = Amount to subtract from main stock per sale (e.g., 5 for 5kg pack)</small>
                        </div>
                    </div>
                </div> <!-- end col -->

                <div class="col-xl-4">
                    <!-- Submit Button Card -->
                    <div class="card">
                        <div class="card-body text-center">
                            <button class="btn btn-primary w-100" type="submit" id="submit_button">
                                <i class="uil uil-check me-1"></i> Update Product
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
                            <input type="hidden" name="category_id" id="category_id" value="<?php echo $product_data->category_id ?>">
                            <input type="hidden" name="category_id_temp" id="category_id_temp" value="<?php echo $product_data->category_id ?>">
                        </div>
                    </div>
                </div>

            </div>
            <!-- end row -->
        </form>
    </div> <!-- container-fluid -->
</div>
<!-- End Page-content -->