<script>
    $(document).ready(function() {
        // Desktop image preview
        $('#desktop_image').on('change', function() {
            var file = this.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#desktop_preview').attr('src', e.target.result);
                    $('#desktop_preview_container').show();
                    $('#current_desktop_image').hide();
                };
                reader.readAsDataURL(file);
            } else {
                $('#desktop_preview_container').hide();
                $('#current_desktop_image').show();
            }
        });

        // Mobile image preview
        $('#mobile_image').on('change', function() {
            var file = this.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#mobile_preview').attr('src', e.target.result);
                    $('#mobile_preview_container').show();
                    $('#current_mobile_image').hide();
                };
                reader.readAsDataURL(file);
            } else {
                $('#mobile_preview_container').hide();
                $('#current_mobile_image').show();
            }
        });
    });

    $(function() {
        $('#edit_banner').formValidation({
            framework: 'bootstrap',
            message: 'This value is not valid',
            icon: {
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                title: {
                    validators: {
                        notEmpty: {
                            message: 'Please enter banner title'
                        }
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
                data: new FormData(document.getElementById('edit_banner')),
                contentType: false,
                cache: false,
                processData: false,
                success: function(result) {
                    var obj = JSON.parse(result);

                    if (obj.status == 200) {
                        toastr["success"]("Success", obj.message);
                        setTimeout(function() {
                            window.location.href = base_url + 'admin_root/banners';
                        }, 2000);
                    } else {
                        toastr["error"]("Error", obj.message);
                    }
                }
            });
        });
    });
</script>
