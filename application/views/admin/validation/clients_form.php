<script>
$(document).ready(function() {

    // Form validation for add client
    if ($('#add_client').length > 0) {
        $('#add_client').formValidation({
            framework: 'bootstrap',
            icon: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                client_code: {
                    validators: {
                        notEmpty: {
                            message: 'Client code is required'
                        }
                    }
                },
                name: {
                    validators: {
                        notEmpty: {
                            message: 'Client name is required'
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
                phone: {
                    validators: {
                        regexp: {
                            regexp: /^[0-9+\-\s()]*$/,
                            message: 'Please enter a valid phone number'
                        }
                    }
                },
                alternate_phone: {
                    validators: {
                        regexp: {
                            regexp: /^[0-9+\-\s()]*$/,
                            message: 'Please enter a valid phone number'
                        }
                    }
                },
                pincode: {
                    validators: {
                        regexp: {
                            regexp: /^[0-9]*$/,
                            message: 'Please enter a valid pincode'
                        }
                    }
                },
                gst_number: {
                    validators: {
                        regexp: {
                            regexp: /^[0-9A-Z]*$/,
                            message: 'Please enter a valid GST number'
                        }
                    }
                },
                pan_number: {
                    validators: {
                        regexp: {
                            regexp: /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/,
                            message: 'Please enter a valid PAN number (e.g., AAAAA0000A)'
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
                            window.location.href = base_url + 'admin_root/clients';
                        }, 2000);
                    } else {
                        toastr["error"]("Error", obj.message);
                        $('#submit_button').prop('disabled', false).html('Add Client');
                    }
                },
                error: function() {
                    toastr["error"]("Error", "Something went wrong. Please try again.");
                    $('#submit_button').prop('disabled', false).html('Add Client');
                }
            });
        });
    }

    // Form validation for edit client
    if ($('#edit_client').length > 0) {
        $('#edit_client').formValidation({
            framework: 'bootstrap',
            icon: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                client_code: {
                    validators: {
                        notEmpty: {
                            message: 'Client code is required'
                        }
                    }
                },
                name: {
                    validators: {
                        notEmpty: {
                            message: 'Client name is required'
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
                phone: {
                    validators: {
                        regexp: {
                            regexp: /^[0-9+\-\s()]*$/,
                            message: 'Please enter a valid phone number'
                        }
                    }
                },
                alternate_phone: {
                    validators: {
                        regexp: {
                            regexp: /^[0-9+\-\s()]*$/,
                            message: 'Please enter a valid phone number'
                        }
                    }
                },
                pincode: {
                    validators: {
                        regexp: {
                            regexp: /^[0-9]*$/,
                            message: 'Please enter a valid pincode'
                        }
                    }
                },
                gst_number: {
                    validators: {
                        regexp: {
                            regexp: /^[0-9A-Z]*$/,
                            message: 'Please enter a valid GST number'
                        }
                    }
                },
                pan_number: {
                    validators: {
                        regexp: {
                            regexp: /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/,
                            message: 'Please enter a valid PAN number (e.g., AAAAA0000A)'
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
                            window.location.href = base_url + 'admin_root/clients';
                        }, 2000);
                    } else {
                        toastr["error"]("Error", obj.message);
                        $('#submit_button').prop('disabled', false).html('Update Client');
                    }
                },
                error: function() {
                    toastr["error"]("Error", "Something went wrong. Please try again.");
                    $('#submit_button').prop('disabled', false).html('Update Client');
                }
            });
        });
    }
});
</script>
