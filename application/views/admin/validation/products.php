<?php
// Build category tree data for jsTree
$folders_arr = array();
$get_categories = get_all_categories_with_status();
$cat_id = isset($product_data) ? $product_data->category_id : '';

if (!empty($get_categories)) {
    foreach ($get_categories as $cat) {
        $parentid = $cat->parent_category_id;

        if ($parentid == '0') $parentid = "#";
        $selected = false;
        $opened = false;

        if (!empty($cat_id)) {
            $cat_id_array = explode(',', $cat_id);
            if (in_array($cat->id, $cat_id_array)) {
                $selected = true;
                $opened = true;
            }
        }

        if ($parentid == '#') {
            $icon = 'uil-folder';
        } else {
            $icon = 'uil-files-landscapes';
        }

        $folders_arr[] = array(
            "id" => $cat->id,
            "parent" => $parentid,
            "text" => preg_replace('/(\v|\s)+/', ' ', $cat->category_name),
            "icon" => $icon,
            "state" => array("selected" => $selected, "opened" => $opened)
        );
    }
}
?>

<script>
    var category_json_data = '<?= json_encode($folders_arr) ?>';

    $(document).ready(function() {
        var folder_jsondata = JSON.parse(category_json_data);

        $('#category_tree').jstree({
                'core': {
                    'data': folder_jsondata,
                    'multiple': true
                },
                'checkbox': {
                    'deselect_all': true,
                    'three_state': false,
                    "two_state": false
                },
                'plugins': ["checkbox", "changed"]
            })
            .bind("loaded.jstree", function(event, data) {
                $(this).jstree("open_all");
            });

        // For edit section - pre-select categories
        var selected_cats = $('#category_id_temp').val();
        if (selected_cats) {
            selected_cat_array = selected_cats.split(',');
            console.log(selected_cat_array);
        }

        // Add meta row functionality
        $('#add_meta_row').on('click', function() {
            var newRow = `
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
            `;
            $('#meta_fields_container').append(newRow);
        });

        // Remove meta row functionality
        $(document).on('click', '.remove-meta-row', function() {
            var rowCount = $('.meta-row').length;
            if (rowCount > 1) {
                $(this).closest('.meta-row').remove();
            } else {
                // Clear the inputs instead of removing the last row
                $(this).closest('.meta-row').find('input').val('');
            }
        });

        // Initialize sortable for existing images (edit page)
        if ($('#sortable_images').length) {
            $('#sortable_images').sortable({
                placeholder: 'ui-state-highlight',
                cursor: 'move',
                opacity: 0.7,
                update: function(event, ui) {
                    updateImageOrder();
                }
            });
        }

        // Remove existing image
        $(document).on('click', '.remove-existing-image', function(e) {
            e.preventDefault();
            $(this).closest('.image-item').fadeOut(300, function() {
                $(this).remove();
                updateImageOrder();
            });
        });

        // Remove instructions image (desktop/mobile)
        $(document).on('click', '.remove-instructions-image', function(e) {
            e.preventDefault();
            var type = $(this).data('type'); // 'desktop' or 'mobile'
            var $preview = $('#' + type + '_preview');

            $preview.fadeOut(300, function() {
                $preview.html('<input type="hidden" name="remove_instructions_' + type + '" value="1">');
                $preview.show();
            });
        });

        // Store selected files in an array for manipulation
        var selectedFiles = [];

        // Preview new images with remove option
        $('#product_images').on('change', function() {
            var files = Array.from(this.files);

            // Add new files to our array
            files.forEach(function(file) {
                selectedFiles.push(file);
            });

            renderImagePreviews();
        });

        // Render image previews from selectedFiles array
        function renderImagePreviews() {
            var preview = $('#new_images_preview');
            preview.empty();

            selectedFiles.forEach(function(file, index) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var html = `
                        <div class="new-image-item position-relative" data-index="${index}" style="cursor: move;">
                            <img src="${e.target.result}" class="rounded" style="width: 100px; height: 100px; object-fit: cover;" alt="New Image">
                            <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 remove-new-image" data-index="${index}" style="padding: 2px 6px; font-size: 10px; border-radius: 50%;">
                                <i class="uil uil-times"></i>
                            </button>
                        </div>
                    `;
                    preview.append(html);

                    // Initialize sortable after all images are loaded
                    if (preview.children().length === selectedFiles.length) {
                        initNewImagesSortable();
                    }
                };
                reader.readAsDataURL(file);
            });

            // Update the file input with current files
            updateFileInput();
        }

        // Initialize sortable for new images preview
        function initNewImagesSortable() {
            if ($('#new_images_preview').children().length > 0) {
                $('#new_images_preview').sortable({
                    placeholder: 'ui-state-highlight',
                    cursor: 'move',
                    opacity: 0.7,
                    update: function(event, ui) {
                        // Reorder the selectedFiles array based on new order
                        var newOrder = [];
                        $('#new_images_preview .new-image-item').each(function() {
                            var idx = $(this).data('index');
                            newOrder.push(selectedFiles[idx]);
                        });
                        selectedFiles = newOrder;

                        // Re-render with new indices
                        renderImagePreviews();
                    }
                });
            }
        }

        // Remove new image from preview
        $(document).on('click', '.remove-new-image', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var index = $(this).data('index');

            // Remove from array
            selectedFiles.splice(index, 1);

            // Re-render previews
            renderImagePreviews();
        });

        // Update file input with selectedFiles array using DataTransfer
        function updateFileInput() {
            var dt = new DataTransfer();
            selectedFiles.forEach(function(file) {
                dt.items.add(file);
            });
            document.getElementById('product_images').files = dt.files;
        }

        // Function to update hidden inputs order for existing images
        function updateImageOrder() {
            $('#sortable_images input[name="existing_images[]"]').remove();

            $('#sortable_images .image-item').each(function() {
                var imageName = $(this).data('image');
                $(this).append('<input type="hidden" name="existing_images[]" value="' + imageName + '">');
            });
        }

        // ==================== VARIANTS FUNCTIONALITY ====================

        // Add new variant row
        $('#add_variant_row').on('click', function() {
            var newRow = `
                <tr class="variant-row new-variant">
                    <td>
                        <div class="form-group mb-0">
                            <input type="text" class="form-control form-control-sm" name="new_variant_name[]" placeholder="e.g., 5kg Pack">
                        </div>
                    </td>
                    <td>
                        <div class="form-group mb-0">
                            <input type="number" step="0.01" class="form-control form-control-sm" name="new_variant_mrp[]" placeholder="MRP">
                        </div>
                    </td>
                    <td>
                        <div class="form-group mb-0">
                            <input type="number" step="0.01" class="form-control form-control-sm" name="new_variant_sale_price[]" placeholder="Sale Price">
                        </div>
                    </td>
                    <td>
                        <div class="form-group mb-0">
                            <input type="number" step="0.01" class="form-control form-control-sm" name="new_variant_stock_qty[]" placeholder="e.g., 5" value="0">
                        </div>
                    </td>
                    <td>
                        <div class="form-group mb-0">
                            <input type="text" class="form-control form-control-sm" name="new_variant_sku[]" placeholder="SKU (optional)">
                        </div>
                    </td>
                    <td>
                        <div class="form-group mb-0">
                            <input type="text" class="form-control form-control-sm" name="new_variant_servers_text[]" placeholder="e.g., For 200gm chicken">
                        </div>
                    </td>
                    <td>
                        <div class="form-group mb-0">
                            <input type="number" step="0.10" class="form-control form-control-sm" name="new_variant_package_weight[]" placeholder="e.g., 0.45">
                        </div>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-soft-danger btn-sm remove-new-variant">
                            <i class="uil uil-trash-alt"></i>
                        </button>
                    </td>
                </tr>
            `;
            $('#variants_table').append(newRow);
        });

        // Remove new variant row (not yet saved)
        $(document).on('click', '.remove-new-variant', function() {
            $(this).closest('tr').remove();
        });

        // Delete existing variant (mark for deletion)
        var deletedVariantIds = [];
        $(document).on('click', '.delete-variant', function() {
            var variantId = $(this).data('variant-id');
            var row = $(this).closest('tr');

            Swal.fire({
                title: 'Delete Variant?',
                text: "This variant will be deleted when you save the product.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    deletedVariantIds.push(variantId);
                    row.fadeOut(300, function() {
                        $(this).remove();
                    });
                    // Add hidden input to track deleted variants
                    $('#edit_product').append('<input type="hidden" name="deleted_variant_ids[]" value="' + variantId + '">');
                }
            });
        });
    });

    // Update hidden input when category selection changes
    $('#category_tree').on('changed.jstree', function(e, data) {
        getCatFromTree();
    });

    function getCatFromTree() {
        var cat_id = $("#category_tree").jstree("get_selected").toString();
        console.log('Selected categories:', cat_id);
        $('#category_id').val(cat_id);
    }

    $(function() {
        // Add Product Form
        $('#add_product').formValidation({
            framework: 'bootstrap',
            message: 'This value is not valid',
            icon: {
                validating: 'glyphicon glyphicon-refresh'
            },
            row: {
                selector: '.form-group',
                valid: '',
                invalid: ''
            },
            excluded: [':disabled', ':hidden', ':not(:visible)', '[name^="meta_"]'],
            fields: {
                product_name: {
                    validators: {
                        notEmpty: {
                            message: 'Please enter Product Name'
                        }
                    }
                },
                mrp: {
                    validators: {
                        notEmpty: {
                            message: 'Please enter MRP'
                        },
                        numeric: {
                            message: 'Please enter a valid number'
                        }
                    }
                },
                sale_price: {
                    validators: {
                        notEmpty: {
                            message: 'Please enter Sale Price'
                        },
                        numeric: {
                            message: 'Please enter a valid number'
                        }
                    }
                }
            }
        }).on('success.form.fv', function(e) {
            e.preventDefault();

            // Get selected categories before submit
            getCatFromTree();

            var category_id = $('#category_id').val();
            if (!category_id) {
                toastr["error"]("Error", "Please select at least one category");
                return false;
            }

            var $form = $(e.target);
            var bv = $form.data('formValidation');

            $.ajax({
                url: $form.attr('action'),
                type: "POST",
                data: new FormData(document.getElementById('add_product')),
                contentType: false,
                cache: false,
                processData: false,
                success: function(result) {
                    var obj = JSON.parse(result);

                    if (obj.status == 200) {
                        toastr["success"]("Success", obj.message);

                        setTimeout(function() {
                            window.location.href = base_url + 'admin_root/products';
                        }, 2000);
                    } else {
                        toastr["error"]("Error", obj.message);
                    }
                }
            });
        });

        // Edit Product Form
        $('#edit_product').formValidation({
            framework: 'bootstrap',
            message: 'This value is not valid',
            icon: {
                validating: 'glyphicon glyphicon-refresh'
            },
            row: {
                selector: '.form-group',
                valid: '',
                invalid: ''
            },
            excluded: [':disabled', ':hidden', ':not(:visible)', '[name^="existing_variant_"]', '[name^="new_variant_"]', '[name^="meta_"]', '[name^="existing_images"]'],
            fields: {
                product_name: {
                    validators: {
                        notEmpty: {
                            message: 'Please enter Product Name'
                        }
                    }
                },
                mrp: {
                    validators: {
                        notEmpty: {
                            message: 'Please enter MRP'
                        },
                        numeric: {
                            message: 'Please enter a valid number'
                        }
                    }
                },
                sale_price: {
                    validators: {
                        notEmpty: {
                            message: 'Please enter Sale Price'
                        },
                        numeric: {
                            message: 'Please enter a valid number'
                        }
                    }
                }
            }
        }).on('success.form.fv', function(e) {
            e.preventDefault();

            // Get selected categories before submit
            getCatFromTree();

            var $form = $(e.target);
            var bv = $form.data('formValidation');

            $.ajax({
                url: $form.attr('action'),
                type: "POST",
                data: new FormData(document.getElementById('edit_product')),
                contentType: false,
                cache: false,
                processData: false,
                success: function(result) {
                    var obj = JSON.parse(result);

                    if (obj.status == 200) {
                        toastr["success"]("Success", obj.message);

                        setTimeout(function() {
                            window.location.href = base_url + 'admin_root/products';
                        }, 2000);
                    } else {
                        toastr["error"]("Error", obj.message);
                    }
                }
            });
        });
    });
</script>