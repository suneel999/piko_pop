<script>
$(document).ready(function() {

    // Initialize Select2
    if ($('.select2').length > 0) {
        $('.select2').select2();
    }

    // Form validation for add client user
    if ($('#add_client_user').length > 0) {
        $('#add_client_user').formValidation({
            framework: 'bootstrap',
            icon: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                client_id: {
                    validators: {
                        notEmpty: {
                            message: 'Please select a client'
                        }
                    }
                },
                email: {
                    validators: {
                        notEmpty: {
                            message: 'Email is required'
                        },
                        emailAddress: {
                            message: 'Please enter a valid email address'
                        }
                    }
                },
                first_name: {
                    validators: {
                        notEmpty: {
                            message: 'First name is required'
                        }
                    }
                },
                phone: {
                    validators: {
                        regexp: {
                            regexp: /^[0-9+\-\s()]*$/,
                            message: 'Please enter a valid phone number'
                        }
                    }
                },
                password: {
                    validators: {
                        notEmpty: {
                            message: 'Password is required'
                        },
                        stringLength: {
                            min: 6,
                            message: 'Password must be at least 6 characters'
                        }
                    }
                },
                confirm_password: {
                    validators: {
                        notEmpty: {
                            message: 'Please confirm your password'
                        },
                        identical: {
                            field: 'password',
                            message: 'Passwords do not match'
                        }
                    }
                }
            }
        }).on('success.form.fv', function(e) {
            e.preventDefault();
            var $form = $(e.target);
            $('#submit_button').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');

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
                            window.location.href = base_url + 'admin_root/clients/users';
                        }, 2000);
                    } else {
                        toastr["error"]("Error", obj.message);
                        $('#submit_button').prop('disabled', false).html('Add User');
                    }
                },
                error: function() {
                    toastr["error"]("Error", "Something went wrong. Please try again.");
                    $('#submit_button').prop('disabled', false).html('Add User');
                }
            });
        });
    }

    // Form validation for edit client user
    if ($('#edit_client_user').length > 0) {
        $('#edit_client_user').formValidation({
            framework: 'bootstrap',
            icon: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                client_id: {
                    validators: {
                        notEmpty: {
                            message: 'Please select a client'
                        }
                    }
                },
                email: {
                    validators: {
                        notEmpty: {
                            message: 'Email is required'
                        },
                        emailAddress: {
                            message: 'Please enter a valid email address'
                        }
                    }
                },
                first_name: {
                    validators: {
                        notEmpty: {
                            message: 'First name is required'
                        }
                    }
                },
                phone: {
                    validators: {
                        regexp: {
                            regexp: /^[0-9+\-\s()]*$/,
                            message: 'Please enter a valid phone number'
                        }
                    }
                },
                password: {
                    validators: {
                        stringLength: {
                            min: 6,
                            message: 'Password must be at least 6 characters'
                        }
                    }
                },
                confirm_password: {
                    validators: {
                        identical: {
                            field: 'password',
                            message: 'Passwords do not match'
                        }
                    }
                }
            }
        }).on('success.form.fv', function(e) {
            e.preventDefault();
            var $form = $(e.target);
            $('#submit_button').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');

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
                            window.location.href = base_url + 'admin_root/clients/users';
                        }, 2000);
                    } else {
                        toastr["error"]("Error", obj.message);
                        $('#submit_button').prop('disabled', false).html('Update User');
                    }
                },
                error: function() {
                    toastr["error"]("Error", "Something went wrong. Please try again.");
                    $('#submit_button').prop('disabled', false).html('Update User');
                }
            });
        });
    }
});
</script>
