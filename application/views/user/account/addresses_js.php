<!-- Addresses Page Script -->
<script>
$(document).ready(function() {
    'use strict';

    // =====================
    // Address Form Validation
    // =====================
    var addressValidator = $('#addressForm').validate({
        rules: {
            fullname: {
                required: true,
                minlength: 2,
                maxlength: 100
            },
            phone: {
                required: true,
                digits: true,
                minlength: 10,
                maxlength: 10
            },
            address_line1: {
                required: true,
                minlength: 5,
                maxlength: 200
            },
            city: {
                required: true,
                minlength: 2,
                maxlength: 100
            },
            pincode: {
                required: true,
                digits: true,
                minlength: 6,
                maxlength: 6
            },
            state: {
                required: true
            }
        },
        messages: {
            fullname: {
                required: 'Please enter full name',
                minlength: 'Name must be at least 2 characters',
                maxlength: 'Name cannot exceed 100 characters'
            },
            phone: {
                required: 'Please enter phone number',
                digits: 'Please enter only digits',
                minlength: 'Phone number must be 10 digits',
                maxlength: 'Phone number must be 10 digits'
            },
            address_line1: {
                required: 'Please enter address',
                minlength: 'Address must be at least 5 characters',
                maxlength: 'Address cannot exceed 200 characters'
            },
            city: {
                required: 'Please enter city',
                minlength: 'City must be at least 2 characters',
                maxlength: 'City cannot exceed 100 characters'
            },
            pincode: {
                required: 'Please enter pincode',
                digits: 'Please enter only digits',
                minlength: 'Pincode must be 6 digits',
                maxlength: 'Pincode must be 6 digits'
            },
            state: {
                required: 'Please select state'
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
    // Modal Functions
    // =====================
    function openAddressModal(isEdit) {
        var $modal = $('#addressModal');
        var $content = $modal.find('.address-modal-content');

        if (isEdit) {
            $('#addressModalTitle').text('Edit Address');
            $('#saveAddressBtnText').text('Update Address');
        } else {
            $('#addressModalTitle').text('Add New Address');
            $('#saveAddressBtnText').text('Save Address');
            $('#addressForm')[0].reset();
            $('#addressId').val('');
        }

        $modal.removeClass('hidden');
        $('body').css('overflow', 'hidden');

        setTimeout(function() {
            $content.removeClass('scale-95 opacity-0').addClass('scale-100 opacity-100');
        }, 10);
    }

    function closeAddressModal() {
        var $modal = $('#addressModal');
        var $content = $modal.find('.address-modal-content');

        $content.removeClass('scale-100 opacity-100').addClass('scale-95 opacity-0');

        setTimeout(function() {
            $modal.addClass('hidden');
            $('body').css('overflow', '');
            $('#addressForm')[0].reset();
            $('#addressId').val('');
            addressValidator.resetForm();
        }, 300);
    }

    // Open modal - Add new
    $('#addNewAddressBtn, #addFirstAddressBtn').on('click', function() {
        openAddressModal(false);
    });

    // Close modal
    $('#closeAddressModal, #cancelAddressBtn').on('click', function() {
        closeAddressModal();
    });

    // Close on backdrop click
    $('.address-modal-backdrop').on('click', function() {
        closeAddressModal();
    });

    // Close on Escape key
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && !$('#addressModal').hasClass('hidden')) {
            closeAddressModal();
        }
    });

    // Phone number validation
    $('#addrPhone').on('input', function() {
        $(this).val($(this).val().replace(/[^0-9]/g, ''));
    });

    // Pincode validation
    $('#addrPincode').on('input', function() {
        $(this).val($(this).val().replace(/[^0-9]/g, ''));
    });

    // =====================
    // Edit Address
    // =====================
    $(document).on('click', '.edit-address-btn', function() {
        var address = $(this).data('address');

        $('#addressId').val(address.id);
        $('input[name="address_type"][value="' + address.address_type + '"]').prop('checked', true);
        $('#addrFullName').val(address.fullname);
        $('#addrPhone').val(address.phone);
        $('#addrLine1').val(address.address_line1);
        $('#addrLine2').val(address.address_line2 || '');
        $('#addrCity').val(address.city);
        $('#addrState').val(address.state);
        $('#addrPincode').val(address.pincode);
        $('#addrDefault').prop('checked', address.is_default == 1);

        openAddressModal(true);
    });

    // =====================
    // Save/Update Address
    // =====================
    $('#addressForm').on('submit', function(e) {
        e.preventDefault();

        if (!$(this).valid()) {
            return;
        }

        var $btn = $('#saveAddressBtn');
        var originalHtml = $btn.html();
        var addressId = $('#addressId').val();
        var isEdit = addressId !== '';
        var url = isEdit ? base_url + 'account/update_address' : base_url + 'account/add_address';

        $btn.html('<i class="fa-solid fa-spinner fa-spin mr-2"></i> Saving...').prop('disabled', true);

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                address_id: addressId,
                address_type: $('input[name="address_type"]:checked').val(),
                fullname: $('#addrFullName').val().trim(),
                phone: $('#addrPhone').val().trim(),
                address_line1: $('#addrLine1').val().trim(),
                address_line2: $('#addrLine2').val().trim(),
                city: $('#addrCity').val().trim(),
                state: $('#addrState').val(),
                pincode: $('#addrPincode').val().trim(),
                is_default: $('#addrDefault').is(':checked') ? 1 : 0
            },
            dataType: 'json',
            success: function(response) {
                $btn.html(originalHtml).prop('disabled', false);

                if (response.status == 200) {
                    toastr.success(response.message);
                    closeAddressModal();
                    // Reload page to show updated addresses
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    toastr.error(response.message || 'Failed to save address.');
                }
            },
            error: function() {
                $btn.html(originalHtml).prop('disabled', false);
                toastr.error('Something went wrong. Please try again.');
            }
        });
    });

    // =====================
    // Set Default Address
    // =====================
    $(document).on('click', '.set-default-btn', function() {
        var addressId = $(this).data('address-id');
        var $btn = $(this);

        $btn.html('<i class="fa-solid fa-spinner fa-spin"></i>');

        $.ajax({
            url: base_url + 'account/set_default_address',
            type: 'POST',
            data: { address_id: addressId },
            dataType: 'json',
            success: function(response) {
                if (response.status == 200) {
                    toastr.success(response.message);
                    location.reload();
                } else {
                    toastr.error(response.message || 'Failed to set default address.');
                    $btn.html('<i class="fa-solid fa-check mr-1"></i> Set as Default');
                }
            },
            error: function() {
                toastr.error('Something went wrong. Please try again.');
                $btn.html('<i class="fa-solid fa-check mr-1"></i> Set as Default');
            }
        });
    });

    // =====================
    // Delete Address Modal
    // =====================
    function openDeleteModal(addressId, addressType) {
        var $modal = $('#deleteModal');
        var $content = $modal.find('.delete-modal-content');

        $('#deleteAddressId').val(addressId);
        $('#deleteAddressType').text(addressType);

        $modal.removeClass('hidden');
        $('body').css('overflow', 'hidden');

        setTimeout(function() {
            $content.removeClass('scale-95 opacity-0').addClass('scale-100 opacity-100');
        }, 10);
    }

    function closeDeleteModal() {
        var $modal = $('#deleteModal');
        var $content = $modal.find('.delete-modal-content');

        $content.removeClass('scale-100 opacity-100').addClass('scale-95 opacity-0');

        setTimeout(function() {
            $modal.addClass('hidden');
            $('body').css('overflow', '');
        }, 300);
    }

    // Open delete modal
    $(document).on('click', '.delete-address-btn', function() {
        var addressId = $(this).data('address-id');
        var $card = $(this).closest('.address-card');
        var addressType = $card.find('h4').first().text();
        openDeleteModal(addressId, addressType);
    });

    // Cancel delete
    $('#cancelDeleteBtn').on('click', function() {
        closeDeleteModal();
    });

    // Close on backdrop click
    $('.delete-modal-backdrop').on('click', function() {
        closeDeleteModal();
    });

    // Close on Escape key
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && !$('#deleteModal').hasClass('hidden')) {
            closeDeleteModal();
        }
    });

    // Confirm delete
    $('#confirmDeleteBtn').on('click', function() {
        var $btn = $(this);
        var originalHtml = $btn.html();
        var addressId = $('#deleteAddressId').val();

        $btn.html('<i class="fa-solid fa-spinner fa-spin mr-2"></i> Deleting...').prop('disabled', true);

        $.ajax({
            url: base_url + 'account/delete_address',
            type: 'POST',
            data: { address_id: addressId },
            dataType: 'json',
            success: function(response) {
                $btn.html(originalHtml).prop('disabled', false);

                if (response.status == 200) {
                    toastr.success(response.message);
                    closeDeleteModal();
                    // Remove the card from DOM
                    $('.address-card[data-address-id="' + addressId + '"]').fadeOut(300, function() {
                        $(this).remove();
                        // Check if no addresses left
                        if ($('.address-card').length === 0) {
                            location.reload();
                        }
                    });
                } else {
                    toastr.error(response.message || 'Failed to delete address.');
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
