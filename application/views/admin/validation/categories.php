<script>
    $(function() {
        // Add Category Form
        $('#add_category').formValidation({
            message: 'This value is not valid',
            icon: {
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                category_name: {
                    validators: {
                        notEmpty: {
                            message: 'Please enter Category Name'
                        }
                    }
                },
                icon: {
                    validators: {
                        file: {
                            extension: 'jpeg,jpg,png,svg,gif',
                            type: 'image/jpeg,image/png,image/svg+xml,image/gif',
                            maxSize: 2097152,
                            message: 'The selected file is not valid (max 2MB)'
                        },
                    }
                }
            }
        }).on('success.form.fv', function(e) {
            e.preventDefault();

            var $form = $(e.target);
            var bv = $form.data('formValidation');

            $.ajax({
                url: $form.attr('action'),
                type: "POST",
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                success: function(result) {
                    var obj = JSON.parse(result);

                    if (obj.status == 200) {
                        toastr["success"]("Success", obj.message);

                        setTimeout(function() {
                            window.location.href = base_url + 'admin_root/products/categories';
                        }, 2000);
                    } else {
                        toastr["error"]("Error", obj.message);
                    }
                }
            });
        });

        // Update Category Form
        $('#update_category').formValidation({
            message: 'This value is not valid',
            icon: {
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                category_name: {
                    validators: {
                        notEmpty: {
                            message: 'Please enter Category Name'
                        }
                    }
                },
                icon: {
                    validators: {
                        file: {
                            extension: 'jpeg,jpg,png,svg,gif',
                            type: 'image/jpeg,image/png,image/svg+xml,image/gif',
                            maxSize: 2097152,
                            message: 'The selected file is not valid (max 2MB)'
                        },
                    }
                }
            }
        }).on('success.form.fv', function(e) {
            e.preventDefault();

            var $form = $(e.target);
            var bv = $form.data('formValidation');

            $.ajax({
                url: $form.attr('action'),
                type: "POST",
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                success: function(result) {
                    var obj = JSON.parse(result);

                    if (obj.status == 200) {
                        toastr["success"]("Success", obj.message);

                        setTimeout(function() {
                            window.location.href = base_url + 'admin_root/products/categories';
                        }, 2000);
                    } else {
                        toastr["error"]("Error", obj.message);
                    }
                }
            });
        });
    });
</script>
