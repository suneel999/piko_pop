<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js"></script>
<script>
    var productMap = {};

    $(document).ready(function() {
        // Initialize typeahead for product search
        $('#product_search').typeahead({
            source: function(query, result) {
                productMap = {};
                var category_id = $('#category_filter').val();

                $.ajax({
                    url: base_url + "admin_root/inventory/search_products",
                    data: {
                        query: query,
                        category_id: category_id
                    },
                    dataType: "json",
                    type: "POST",
                    success: function(data) {
                        var newData = [];
                        $.each(data, function() {
                            productMap[this.product_name] = this;
                            newData.push(this.product_name);
                        });
                        return result(newData);
                    }
                });
            },
            afterSelect: function(args) {
                addProductToTable(productMap[args]);
                $('#product_search').val('');
            }
        });
    });

    function addProductToTable(product) {
        var productId = product.product_id;
        var rowId = 'row_' + productId;
        var rowIdHash = '#' + rowId;

        // Check if product already exists in table
        if ($(rowIdHash).length) {
            // Focus on the quantity field
            $('#add_qty_' + productId).focus().select();
            toastr["info"]("Info", "Product already in list. Update the quantity.");
            return;
        }

        var rowCount = $('#stock_tbody tr').length;
        var newCount = rowCount + 1;
        var weightUnit = product.weight_unit || '';

        var html = '<tr id="' + rowId + '" data-product-id="' + productId + '">';
        html += '<td>' + newCount + '</td>';
        html += '<td>' + product.product_name + '<small class="error-message text-danger d-block"></small></td>';
        html += '<td class="text-center"><span class="badge bg-soft-info text-info">' + product.current_stock + ' ' + weightUnit + '</span></td>';
        html += '<td class="form-group mb-0">';
        html += '<input type="hidden" name="product_id[]" value="' + productId + '">';
        html += '<input type="number" step="0.01" class="form-control form-control-sm add-qty" id="add_qty_' + productId + '" name="add_qty[]" placeholder="Qty" value="" required>';
        html += '</td>';
        html += '<td class="text-center">' + weightUnit + '</td>';
        html += '<td class="text-center">';
        html += '<button type="button" class="remove_item_btn" onclick="removeItem(' + productId + ')" title="Remove">';
        html += '<i class="uil uil-times"></i>';
        html += '</button>';
        html += '</td>';
        html += '</tr>';

        $('#stock_tbody').append(html);
        $('#card_footer').removeClass('d-none');
        $('#add_qty_' + productId).focus();
        updateTotalQty();
    }

    function removeItem(productId) {
        $('#row_' + productId).fadeOut(200, function() {
            $(this).remove();
            updateRowNumbers();
            updateTotalQty();

            if ($('#stock_tbody tr').length === 0) {
                $('#card_footer').addClass('d-none');
            }
        });
    }

    function updateRowNumbers() {
        $('#stock_tbody tr').each(function(index) {
            $(this).find('td:first').text(index + 1);
        });
    }

    function updateTotalQty() {
        var total = 0;
        $(".add-qty").each(function() {
            total += parseFloat($(this).val()) || 0;
        });
        $('#total_qty').text(total);
    }

    // Update total on quantity change
    $(document).on('input', '.add-qty', function() {
        updateTotalQty();
    });

    function cancelStock() {
        if ($('#stock_tbody tr').length === 0) {
            window.location.href = base_url + 'admin_root/inventory';
            return;
        }

        Swal.fire({
            title: "Are you sure?",
            text: 'All entries will be cleared.',
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#f46a6a",
            cancelButtonColor: "#34c38f",
            confirmButtonText: 'Yes, cancel'
        }).then(function(result) {
            if (result.isConfirmed) {
                window.location.reload();
            }
        });
    }

    // Form submission
    $('#add_stock_form').on('submit', function(e) {
        e.preventDefault();

        var rows = $('#stock_tbody tr');
        if (rows.length === 0) {
            toastr["error"]("Error", "Please add at least one product");
            return;
        }

        // Validate all quantities
        var isValid = true;
        rows.each(function() {
            var qty = parseFloat($(this).find('.add-qty').val());
            if (isNaN(qty) || qty <= 0) {
                isValid = false;
                $(this).find('.add-qty').addClass('is-invalid');
            } else {
                $(this).find('.add-qty').removeClass('is-invalid');
            }
        });

        if (!isValid) {
            toastr["error"]("Error", "Please enter valid quantities for all products");
            return;
        }

        Swal.fire({
            title: "Confirm Stock Update",
            text: 'Are you sure you want to update stock for ' + rows.length + ' product(s)?',
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#34c38f",
            cancelButtonColor: "#f46a6a",
            confirmButtonText: 'Yes, Update Stock'
        }).then(function(result) {
            if (result.isConfirmed) {
                processRows();
            }
        });
    });

    function processRows() {
        var rows = $('#stock_tbody tr');
        var successCount = 0;
        var errorCount = 0;
        var totalRows = rows.length;
        var processedRows = 0;

        rows.each(function() {
            var row = $(this);
            var errorMessage = row.find('.error-message');
            errorMessage.html('');
            row.removeClass('table-danger');

            var productId = row.find('input[name="product_id[]"]').val();
            var addQty = row.find('input[name="add_qty[]"]').val();

            $.ajax({
                url: base_url + 'admin_root/inventory/update_stock_single',
                method: 'POST',
                data: {
                    product_id: productId,
                    add_qty: addQty
                },
                success: function(result) {
                    var response = JSON.parse(result);
                    processedRows++;

                    if (response.status === 200) {
                        successCount++;
                        row.fadeOut(300, function() {
                            $(this).remove();
                            updateRowNumbers();
                            updateTotalQty();
                            checkCompletion();
                        });
                    } else {
                        errorCount++;
                        errorMessage.html(response.message);
                        row.addClass('table-danger');
                        checkCompletion();
                    }
                },
                error: function() {
                    processedRows++;
                    errorCount++;
                    errorMessage.html('Network error occurred');
                    row.addClass('table-danger');
                    checkCompletion();
                }
            });
        });

        function checkCompletion() {
            if ($('#stock_tbody tr').length === 0) {
                $('#card_footer').addClass('d-none');
                Swal.fire({
                    title: "Success!",
                    text: 'All stock updated successfully.',
                    icon: "success",
                    confirmButtonColor: "#34c38f",
                    confirmButtonText: 'OK'
                }).then(function() {
                    window.location.href = base_url + 'admin_root/inventory';
                });
            } else if (processedRows === totalRows && errorCount > 0) {
                toastr["warning"]("Warning", successCount + ' updated, ' + errorCount + ' failed. Please check errors.');
            }
        }
    }

    // Prevent form submit on Enter key in search
    $(document).on('keypress', '#product_search', function(e) {
        if (e.keyCode === 13) {
            e.preventDefault();
            return false;
        }
    });
</script>
