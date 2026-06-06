<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Add Recipe</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="<?php echo base_url('admin_root/recipes'); ?>">Recipes</a></li>
                            <li class="breadcrumb-item active">Add</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <form id="add_recipe" enctype="multipart/form-data">
            <div class="row">
                <!-- Left: main fields -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Recipe Details</h5>

                            <div class="mb-3">
                                <label class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="title" class="form-control" placeholder="e.g., Andhra-Style Pakodi" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">YouTube URL <span class="text-danger">*</span></label>
                                <input type="url" name="youtube_url" id="youtube_url" class="form-control" placeholder="https://www.youtube.com/watch?v=..." required>
                                <small class="text-muted">Supports watch, youtu.be, shorts, and embed URLs.</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="4" placeholder="Short intro shown above the video..."></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Ingredients</label>
                                <textarea name="ingredients" class="form-control" rows="6" placeholder="One ingredient per line, e.g.,&#10;2 cups besan&#10;1 tsp salt"></textarea>
                                <small class="text-muted">One per line. Free-form text — separate from the products list below.</small>
                            </div>

                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Prep Time (min)</label>
                                    <input type="number" name="prep_time_minutes" class="form-control" min="0" value="0">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Cook Time (min)</label>
                                    <input type="number" name="cook_time_minutes" class="form-control" min="0" value="0">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Servings</label>
                                    <input type="number" name="servings" class="form-control" min="0" value="0">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Difficulty</label>
                                    <select name="difficulty" class="form-select">
                                        <option value="easy">Easy</option>
                                        <option value="medium">Medium</option>
                                        <option value="hard">Hard</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Linked Products -->
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Featured Products (Quick Add to Cart)</h5>
                            <div class="mb-3">
                                <label class="form-label">Select Products</label>
                                <select id="product_picker" class="form-control" multiple>
                                    <?php if (!empty($products)): ?>
                                        <?php foreach ($products as $p): ?>
                                            <option value="<?php echo $p->id; ?>"><?php echo htmlspecialchars($p->product_name); ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <small class="text-muted">Pick the products this recipe uses. They'll appear with an "Add to Cart" button.</small>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-sm" id="selected_products_table" style="display:none;">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th style="width:140px;">Default Qty</th>
                                            <th style="width:60px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="selected_products_body"></tbody>
                                </table>
                                <p class="text-muted small mb-0" id="no_products_msg">No products selected yet.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: thumbnail + actions -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Thumbnail</h5>
                            <p class="text-muted small">Optional — leave blank to use the YouTube thumbnail automatically.</p>
                            <input type="file" name="thumbnail" id="thumbnail" class="form-control" accept="image/jpeg,image/png,image/webp">
                            <div class="mt-3" id="thumbnail_preview" style="display:none;">
                                <img src="" id="thumbnail_preview_img" class="img-fluid rounded" style="max-height:200px;">
                            </div>
                            <div class="mt-3" id="youtube_thumb_preview" style="display:none;">
                                <small class="text-muted d-block mb-1">YouTube preview:</small>
                                <img src="" id="youtube_thumb_img" class="img-fluid rounded">
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Sort Order</label>
                                <input type="number" name="sort_order" class="form-control" value="0">
                                <small class="text-muted">Lower numbers appear first.</small>
                            </div>
                            <button type="submit" class="btn btn-success btn-lg w-100" id="save_btn">
                                <i class="mdi mdi-content-save me-1"></i> Save Recipe
                            </button>
                            <a href="<?php echo base_url('admin_root/recipes'); ?>" class="btn btn-light w-100 mt-2">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </div>
</div>
