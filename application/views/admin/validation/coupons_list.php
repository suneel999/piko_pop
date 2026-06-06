<script>
    $(document).ready(function() {
        $('#coupons_table').DataTable({
            "order": [[0, "desc"]]
        });
    });

    function delete_coupon(cid) {
        Swal.fire({
            title: "Delete Coupon?",
            text: "This coupon will be deleted permanently.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#f46a6a",
            cancelButtonColor: "#34c38f",
            confirmButtonText: "Yes, delete it!"
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: base_url + 'admin_root/coupons/delete_coupon',
                    type: "POST",
                    data: {
                        id: cid
                    },
                    success: function(result) {
                        var obj = JSON.parse(result);
                        if (obj.status == 200) {
                            toastr["success"]("Success", obj.message);
                            setTimeout(function() {
                                window.location.reload();
                            }, 1500);
                        } else {
                            toastr["error"]("Error", obj.message);
                        }
                    }
                });
            }
        });
    }

    function change_status(cid, status) {
        $.ajax({
            url: base_url + 'admin_root/coupons/change_status',
            type: "POST",
            data: {
                id: cid,
                status: status
            },
            success: function(result) {
                var obj = JSON.parse(result);
                if (obj.status == 200) {
                    toastr["success"]("Success", obj.message);
                    setTimeout(function() {
                        window.location.reload();
                    }, 1500);
                } else {
                    toastr["error"]("Error", obj.message);
                }
            }
        });
    }
</script>
