<?php
$selected_product_ids = array();
$selected_qtys = array();
if (!empty($recipe_products)) {
    foreach ($recipe_products as $rp) {
        $selected_product_ids[] = $rp->product_id;
        $selected_qtys[$rp->product_id] = $rp->default_quantity;
    }
}
?>
<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Edit Recipe</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="<?php echo base_url('admin_root/recipes'); ?>">Recipes</a></li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <form id="edit_recipe" enctype="multipart/form-data">
            <input type="hidden" name="recipe_id" value="<?php echo $recipe->id; ?>">

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Recipe Details</h5>

                            <div class="mb-3">
                                <label class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="title" class="form-control" value="<?php echo htmlspecialchars($recipe->title); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">YouTube URL <span class="text-danger">*</span></label>
                                <input type="url" name="youtube_url" id="youtube_url" class="form-control" value="<?php echo htmlspecialchars($recipe->youtube_url); ?>" required>
                                <small class="text-muted">Supports watch, youtu.be, shorts, and embed URLs.</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($recipe->description ?? ''); ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Ingredients</label>
                                <textarea name="ingredients" class="form-control" rows="6"><?php echo htmlspecialchars($recipe->ingredients ?? ''); ?></textarea>
                                <small class="text-muted">One per line.</small>
                            </div>

                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Prep Time (min)</label>
                                    <input type="number" name="prep_time_minutes" class="form-control" min="0" value="<?php echo intval($recipe->prep_time_minutes); ?>">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Cook Time (min)</label>
                                    <input type="number" name="cook_time_minutes" class="form-control" min="0" value="<?php echo intval($recipe->cook_time_minutes); ?>">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Servings</label>
                                    <input type="number" name="servings" class="form-control" min="0" value="<?php echo intval($recipe->servings); ?>">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Difficulty</label>
                                    <select name="difficulty" class="form-select">
                                        <?php foreach (array('easy', 'medium', 'hard') as $d): ?>
                                            <option value="<?php echo $d; ?>" <?php echo $recipe->difficulty == $d ? 'selected' : ''; ?>><?php echo ucfirst($d); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Featured Products (Quick Add to Cart)</h5>
                            <div class="mb-3">
                                <label class="form-label">Select Products</label>
                                <select id="product_picker" class="form-control" multiple>
                                    <?php if (!empty($products)): ?>
                                        <?php foreach ($products as $p): ?>
                                            <option value="<?php echo $p->id; ?>" <?php echo in_array($p->id, $selected_product_ids) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($p->product_name); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-sm" id="selected_products_table" style="<?php echo empty($selected_product_ids) ? 'display:none;' : ''; ?>">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th style="width:140px;">Default Qty</th>
                                            <th style="width:60px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="selected_products_body">
                                        <?php if (!empty($recipe_products)): ?>
                                            <?php foreach ($recipe_products as $rp): ?>
                                                <?php
                                                $name = '';
                                                foreach ($products as $p) {
                                                    if ($p->id == $rp->product_id) { $name = $p->product_name; break; }
                                                }
                                                ?>
                                                <tr data-pid="<?php echo $rp->product_id; ?>">
                                                    <td><?php echo htmlspecialchars($name); ?></td>
                                                    <td><input type="number" min="1" class="form-control form-control-sm" name="product_quantities[<?php echo $rp->product_id; ?>]" value="<?php echo intval($rp->default_quantity); ?>"></td>
                                                    <td><button type="button" class="btn btn-sm btn-soft-danger remove-product-row" data-pid="<?php echo $rp->product_id; ?>"><i class="mdi mdi-close"></i></button></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                                <p class="text-muted small mb-0" id="no_products_msg" style="<?php echo empty($selected_product_ids) ? '' : 'display:none;'; ?>">No products selected yet.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Thumbnail</h5>
                            <?php if (!empty($recipe->thumbnail)): ?>
                                <div class="mb-2">
                                    <img src="<?php echo base_url('uploads/recipes/' . $recipe->thumbnail); ?>" class="img-fluid rounded" style="max-height:200px;">
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" name="remove_thumbnail" value="1" id="remove_thumb">
                                        <label class="form-check-label" for="remove_thumb">Remove and use YouTube thumbnail</label>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <p class="text-muted small">Upload a new file to replace the current one. Leave blank to keep as-is.</p>
                            <input type="file" name="thumbnail" id="thumbnail" class="form-control" accept="image/jpeg,image/png,image/webp">
                            <div class="mt-3" id="thumbnail_preview" style="display:none;">
                                <img src="" id="thumbnail_preview_img" class="img-fluid rounded" style="max-height:200px;">
                            </div>
                            <div class="mt-3">
                                <small class="text-muted d-block mb-1">YouTube preview:</small>
                                <img src="https://img.youtube.com/vi/<?php echo $recipe->youtube_video_id; ?>/hqdefault.jpg" id="youtube_thumb_img" class="img-fluid rounded">
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Sort Order</label>
                                <input type="number" name="sort_order" class="form-control" value="<?php echo intval($recipe->sort_order); ?>">
                            </div>
                            <button type="submit" class="btn btn-success btn-lg w-100" id="save_btn">
                                <i class="mdi mdi-content-save me-1"></i> Update Recipe
                            </button>
                            <a href="<?php echo base_url('admin_root/recipes'); ?>" class="btn btn-light w-100 mt-2">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </div>
</div>
