<script>
    $(document).ready(function() {
        // Categories DataTable
        $("#categories_table").DataTable({
            "processing": true,
            "ajax": base_url + 'admin_root/products/categories_data_ajax'
        }), $(".dataTables_length select").addClass("form-select form-select-sm");

        // Products DataTable
        $("#products_table").DataTable({
            "processing": true,
            "ajax": base_url + 'admin_root/products/products_data_ajax'
        }), $(".dataTables_length select").addClass("form-select form-select-sm");
    });

    // Change Category Status
    function change_status_category(cat_id, status) {
        var msg = '';
        var btn_text = '';
        if (status == 1) {
            msg = 'Are you sure you want to activate this Category?';
            btn_text = 'Yes, activate';
        } else if (status == 2) {
            msg = 'Are you sure you want to deactivate this Category?';
            btn_text = 'Yes, deactivate';
        } else if (status == 3) {
            msg = 'Are you sure you want to delete this Category?';
            btn_text = 'Yes, delete';
        }

        Swal.fire({
            title: "Are you sure?",
            text: msg,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#34c38f",
            cancelButtonColor: "#f46a6a",
            confirmButtonText: btn_text
        }).then(function(t) {
            if (t.value) {
                var dataString = "id=" + cat_id + "&status=" + status;
                $.ajax({
                    url: base_url + 'admin_root/products/change_category_status',
                    type: "POST",
                    data: dataString,
                    success: function(data) {
                        var obj = JSON.parse(data);
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
            }
        });
    }

    // Change Product Status
    function change_status_product(product_id, status) {
        var msg = '';
        var btn_text = '';
        if (status == 1) {
            msg = 'Are you sure you want to activate this Product?';
            btn_text = 'Yes, activate';
        } else if (status == 2) {
            msg = 'Are you sure you want to deactivate this Product?';
            btn_text = 'Yes, deactivate';
        } else if (status == 3) {
            msg = 'Are you sure you want to delete this Product?';
            btn_text = 'Yes, delete';
        }

        Swal.fire({
            title: "Are you sure?",
            text: msg,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#34c38f",
            cancelButtonColor: "#f46a6a",
            confirmButtonText: btn_text
        }).then(function(t) {
            if (t.value) {
                var dataString = "id=" + product_id + "&status=" + status;
                $.ajax({
                    url: base_url + 'admin_root/products/change_product_status',
                    type: "POST",
                    data: dataString,
                    success: function(data) {
                        var obj = JSON.parse(data);
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
            }
        });
    }
</script>
