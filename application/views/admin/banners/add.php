<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Add Banner</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="<?php echo base_url('admin_root/banners') ?>">Banners</a></li>
                            <li class="breadcrumb-item active">Add Banner</li>
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
                        <h4 class="card-title">Add Banner</h4>
                        <form class="needs-validation" id="add_banner" name="add_banner" action="<?php echo base_url('admin_root/banners/save_banner') ?>">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="title">Banner Title<code>*</code></label>
                                        <input type="text" class="form-control" id="title" name="title" placeholder="Banner Title" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="sort_order">Sort Order</label>
                                        <input type="number" class="form-control" id="sort_order" name="sort_order" placeholder="Sort Order" value="0">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="desktop_image">Desktop Banner Image<code>*</code></label>
                                        <input type="file" class="form-control" id="desktop_image" name="desktop_image" accept="image/*" required>
                                        <small class="text-muted">Recommended: 1920x600px (Landscape)</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="mobile_image">Mobile Banner Image<code>*</code></label>
                                        <input type="file" class="form-control" id="mobile_image" name="mobile_image" accept="image/*" required>
                                        <small class="text-muted">Recommended: 768x400px (Portrait/Square)</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3" id="desktop_preview_container" style="display:none;">
                                        <label class="form-label">Desktop Preview</label>
                                        <div>
                                            <img id="desktop_preview" src="" class="img-fluid rounded" style="max-height: 150px;">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3" id="mobile_preview_container" style="display:none;">
                                        <label class="form-label">Mobile Preview</label>
                                        <div>
                                            <img id="mobile_preview" src="" class="img-fluid rounded" style="max-height: 150px;">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="target_link">Target Link</label>
                                        <input type="url" class="form-control" id="target_link" name="target_link" placeholder="https://example.com/page">
                                        <small class="text-muted">URL where the banner will redirect</small>
                                    </div>
                                </div>
                            </div>
                            <button class="btn btn-primary mx-auto d-block" type="submit" id="submit_button">Add Banner</button>
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
