<!-- Profile Page Script -->
<script>
$(document).ready(function() {
    'use strict';

    // =====================
    // Profile Edit Toggle
    // =====================
    $('#editProfileBtn').on('click', function() {
        $('#profileView').addClass('hidden');
        $('#profileEditForm').removeClass('hidden');
        $(this).addClass('hidden');
    });

    $('#cancelEditBtn').on('click', function() {
        $('#profileEditForm').addClass('hidden');
        $('#profileView').removeClass('hidden');
        $('#editProfileBtn').removeClass('hidden');
        // Reset form to original values
        $('#profileEditForm')[0].reset();
    });

    // Phone number validation - only digits
    $('input[name="phone"]').on('input', function() {
        $(this).val($(this).val().replace(/[^0-9]/g, ''));
    });

    // =====================
    // Profile Form Validation
    // =====================
    var profileValidator = $('#profileEditForm').validate({
        rules: {
            fullname: {
                required: true,
                minlength: 2,
                maxlength: 100
            },
            email: {
                required: true,
                email: true
            },
            phone: {
                digits: true,
                minlength: 10,
                maxlength: 10
            }
        },
        messages: {
            fullname: {
                required: 'Please enter your full name',
                minlength: 'Name must be at least 2 characters',
                maxlength: 'Name cannot exceed 100 characters'
            },
            email: {
                required: 'Please enter your email',
                email: 'Please enter a valid email address'
            },
            phone: {
                digits: 'Please enter only digits',
                minlength: 'Phone number must be 10 digits',
                maxlength: 'Phone number must be 10 digits'
            }
        },
        errorElement: 'span',
        errorPlacement: function(error, element) {
            error.addClass('error');
            if (element.attr('name') === 'phone') {
                error.insertAfter(element.closest('.relative'));
            } else {
                error.insertAfter(element);
            }
        },
        highlight: function(element) {
            $(element).addClass('error').removeClass('valid');
        },
        unhighlight: function(element) {
            $(element).removeClass('error').addClass('valid');
        }
    });

    // =====================
    // Profile Form Submission
    // =====================
    $('#profileEditForm').on('submit', function(e) {
        e.preventDefault();

        if (!$(this).valid()) {
            return;
        }

        var $btn = $('#saveProfileBtn');
        var originalHtml = $btn.html();
        $btn.html('<i class="fa-solid fa-spinner fa-spin mr-2"></i> Saving...').prop('disabled', true);

        $.ajax({
            url: base_url + 'account/update_profile',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                $btn.html(originalHtml).prop('disabled', false);

                if (response.status == 200) {
                    toastr.success(response.message);
                    // Reload to show updated data
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    toastr.error(response.message || 'Failed to update profile.');
                }
            },
            error: function() {
                $btn.html(originalHtml).prop('disabled', false);
                toastr.error('Something went wrong. Please try again.');
            }
        });
    });

    // =====================
    // Password Modal Functions
    // =====================
    function openPasswordModal() {
        var $modal = $('#passwordModal');
        var $content = $modal.find('.password-modal-content');

        $modal.removeClass('hidden');
        $('body').css('overflow', 'hidden');

        setTimeout(function() {
            $content.removeClass('scale-95 opacity-0').addClass('scale-100 opacity-100');
        }, 10);
    }

    function closePasswordModal() {
        var $modal = $('#passwordModal');
        var $content = $modal.find('.password-modal-content');

        $content.removeClass('scale-100 opacity-100').addClass('scale-95 opacity-0');

        setTimeout(function() {
            $modal.addClass('hidden');
            $('body').css('overflow', '');
            // Reset form
            $('#passwordForm')[0].reset();
            passwordValidator.resetForm();
        }, 300);
    }

    // Open password modal
    $('#changePasswordBtn').on('click', function() {
        openPasswordModal();
    });

    // Close password modal
    $('#closePasswordModal, #cancelPasswordBtn').on('click', function() {
        closePasswordModal();
    });

    // Close on backdrop click
    $('.password-modal-backdrop').on('click', function() {
        closePasswordModal();
    });

    // Close on Escape key
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && !$('#passwordModal').hasClass('hidden')) {
            closePasswordModal();
        }
    });

    // =====================
    // Password Form Validation
    // =====================
    var passwordValidator = $('#passwordForm').validate({
        rules: {
            current_password: {
                required: true,
                minlength: 6
            },
            new_password: {
                required: true,
                minlength: 6
            },
            confirm_password: {
                required: true,
                minlength: 6,
                equalTo: 'input[name="new_password"]'
            }
        },
        messages: {
            current_password: {
                required: 'Please enter your current password',
                minlength: 'Password must be at least 6 characters'
            },
            new_password: {
                required: 'Please enter a new password',
                minlength: 'Password must be at least 6 characters'
            },
            confirm_password: {
                required: 'Please confirm your new password',
                minlength: 'Password must be at least 6 characters',
                equalTo: 'Passwords do not match'
            }
        },
        errorElement: 'span',
        errorPlacement: function(error, element) {
            error.addClass('error');
            error.insertAfter(element);
        },
        highlight: function(element) {
            $(element).addClass('error').removeClass('valid');
        },
        unhighlight: function(element) {
            $(element).removeClass('error').addClass('valid');
        }
    });

    // =====================
    // Password Form Submission
    // =====================
    $('#passwordForm').on('submit', function(e) {
        e.preventDefault();

        if (!$(this).valid()) {
            return;
        }

        var $btn = $('#savePasswordBtn');
        var originalHtml = $btn.html();
        $btn.html('<i class="fa-solid fa-spinner fa-spin mr-2"></i> Updating...').prop('disabled', true);

        $.ajax({
            url: base_url + 'account/change_password',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                $btn.html(originalHtml).prop('disabled', false);

                if (response.status == 200) {
                    toastr.success(response.message);
                    closePasswordModal();
                } else {
                    toastr.error(response.message || 'Failed to change password.');
                }
            },
            error: function() {
                $btn.html(originalHtml).prop('disabled', false);
                toastr.error('Something went wrong. Please try again.');
            }
        });
    });
});
</script>
