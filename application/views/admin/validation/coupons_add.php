<script>
    $(document).ready(function() {
        // Initialize datepicker
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true
        });

        // Set default start date to today
        $('#start_date').datepicker('setDate', new Date());

        // Generate random coupon code
        $('#generate_code').on('click', function() {
            var code = generateCouponCode(8);
            $('#coupon_code').val(code);
            updatePreview();
        });

        // Toggle max discount field based on coupon type
        $('#coupon_type').on('change', function() {
            if ($(this).val() == '1') {
                $('#max_discount_group').show();
            } else {
                $('#max_discount_group').hide();
                $('#max_discount').val('');
            }
            updatePreview();
        });

        // Update preview on input change
        $('#coupon_code, #discount_value, #coupon_type, #min_amount').on('input change', function() {
            updatePreview();
        });

        // Initial preview update
        updatePreview();
    });

    function generateCouponCode(length) {
        var chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        var code = '';
        for (var i = 0; i < length; i++) {
            code += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        return code;
    }

    function updatePreview() {
        var code = $('#coupon_code').val() || 'CODE';
        var discountValue = $('#discount_value').val() || '0';
        var couponType = $('#coupon_type').val();
        var minAmount = $('#min_amount').val() || '0';

        $('#preview_code').text(code.toUpperCase());

        if (couponType == '1') {
            $('#preview_discount').text(discountValue + '% off');
        } else {
            $('#preview_discount').text('₹' + discountValue + ' off');
        }

        $('#preview_min').text('Min order: ₹' + parseFloat(minAmount).toFixed(2));
    }

    $(function() {
        $('#add_coupon').formValidation({
            framework: 'bootstrap',
            message: 'This value is not valid',
            icon: {
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                title: {
                    validators: {
                        notEmpty: {
                            message: 'Please enter coupon title'
                        }
                    }
                },
                coupon_code: {
                    validators: {
                        notEmpty: {
                            message: 'Please enter coupon code'
                        },
                        stringLength: {
                            min: 3,
                            max: 20,
                            message: 'Code must be between 3 and 20 characters'
                        }
                    }
                },
                coupon_type: {
                    validators: {
                        notEmpty: {
                            message: 'Please select discount type'
                        }
                    }
                },
                discount_value: {
                    validators: {
                        notEmpty: {
                            message: 'Please enter discount value'
                        },
                        numeric: {
                            message: 'Please enter a valid number'
                        }
                    }
                },
                start_date: {
                    validators: {
                        notEmpty: {
                            message: 'Please select start date'
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
                data: new FormData(document.getElementById('add_coupon')),
                contentType: false,
                cache: false,
                processData: false,
                success: function(result) {
                    var obj = JSON.parse(result);

                    if (obj.status == 200) {
                        toastr["success"]("Success", obj.message);
                        setTimeout(function() {
                            window.location.href = base_url + 'admin_root/coupons';
                        }, 2000);
                    } else {
                        toastr["error"]("Error", obj.message);
                    }
                }
            });
        });
    });
</script>
