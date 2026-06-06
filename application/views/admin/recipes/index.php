<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Recipes</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Content</a></li>
                            <li class="breadcrumb-item active">Recipes</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-md-4">
                <a href="<?php echo base_url('admin_root/recipes/add') ?>" class="btn btn-success waves-effect waves-light mb-3">
                    <i class="mdi mdi-plus me-1"></i> Add Recipe
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive mb-4">
                            <table class="table table-centered datatable dt-responsive nowrap table-card-list" style="border-collapse: collapse; border-spacing: 0 12px; width: 100%;" id="recipes_table">
                                <thead>
                                    <tr class="bg-transparent">
                                        <th>ID</th>
                                        <th>Thumbnail</th>
                                        <th>Title</th>
                                        <th>YouTube</th>
                                        <th>Times</th>
                                        <th>Difficulty</th>
                                        <th>Status</th>
                                        <th style="width: 150px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($recipes)): ?>
                                        <?php foreach ($recipes as $recipe): ?>
                                            <?php
                                            $thumb_url = !empty($recipe->thumbnail)
                                                ? base_url('uploads/recipes/' . $recipe->thumbnail)
                                                : 'https://img.youtube.com/vi/' . $recipe->youtube_video_id . '/mqdefault.jpg';

                                            if ($recipe->status == 1) {
                                                $status_badge = '<span class="badge bg-soft-success text-success">Active</span>';
                                                $next_status = 2;
                                            } else {
                                                $status_badge = '<span class="badge bg-soft-warning text-warning">Inactive</span>';
                                                $next_status = 1;
                                            }

                                            $difficulty_colors = array('easy' => 'success', 'medium' => 'warning', 'hard' => 'danger');
                                            $dcolor = isset($difficulty_colors[$recipe->difficulty]) ? $difficulty_colors[$recipe->difficulty] : 'secondary';
                                            ?>
                                            <tr>
                                                <td><?php echo $recipe->id; ?></td>
                                                <td>
                                                    <img src="<?php echo $thumb_url; ?>" alt="" style="width:80px; height:50px; object-fit:cover; border-radius:6px;">
                                                </td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($recipe->title); ?></strong>
                                                    <br><small class="text-muted">/<?php echo htmlspecialchars($recipe->slug); ?></small>
                                                </td>
                                                <td>
                                                    <a href="<?php echo htmlspecialchars($recipe->youtube_url); ?>" target="_blank" class="text-danger">
                                                        <i class="mdi mdi-youtube"></i> <?php echo htmlspecialchars($recipe->youtube_video_id); ?>
                                                    </a>
                                                </td>
                                                <td>
                                                    <small>Prep: <?php echo intval($recipe->prep_time_minutes); ?>m</small><br>
                                                    <small>Cook: <?php echo intval($recipe->cook_time_minutes); ?>m</small>
                                                </td>
                                                <td><span class="badge bg-soft-<?php echo $dcolor; ?> text-<?php echo $dcolor; ?>"><?php echo ucfirst($recipe->difficulty); ?></span></td>
                                                <td>
                                                    <a href="javascript:void(0)" onclick="change_status(<?php echo $recipe->id; ?>, <?php echo $next_status; ?>)"><?php echo $status_badge; ?></a>
                                                </td>
                                                <td>
                                                    <a href="<?php echo base_url('admin_root/recipes/edit/' . $recipe->id); ?>" class="btn btn-sm btn-soft-primary" title="Edit"><i class="uil-pen"></i></a>
                                                    <a href="javascript:void(0)" onclick="delete_recipe(<?php echo $recipe->id; ?>)" class="btn btn-sm btn-soft-danger" title="Delete"><i class="uil-trash"></i></a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
