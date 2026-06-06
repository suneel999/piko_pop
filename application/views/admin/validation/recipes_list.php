<script>
    $(document).ready(function() {
        $('#recipes_table').DataTable({
            "order": [[0, "desc"]]
        });
    });

    function delete_recipe(rid) {
        Swal.fire({
            title: "Delete Recipe?",
            text: "This recipe will be deleted permanently.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#f46a6a",
            cancelButtonColor: "#34c38f",
            confirmButtonText: "Yes, delete it!"
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: base_url + 'admin_root/recipes/delete_recipe',
                    type: "POST",
                    data: { id: rid },
                    success: function(result) {
                        var obj = JSON.parse(result);
                        if (obj.status == 200) {
                            toastr["success"]("Success", obj.message);
                            setTimeout(function() { window.location.reload(); }, 1500);
                        } else {
                            toastr["error"]("Error", obj.message);
                        }
                    }
                });
            }
        });
    }

    function change_status(rid, status) {
        $.ajax({
            url: base_url + 'admin_root/recipes/change_status',
            type: "POST",
            data: { id: rid, status: status },
            success: function(result) {
                var obj = JSON.parse(result);
                if (obj.status == 200) {
                    toastr["success"]("Success", obj.message);
                    setTimeout(function() { window.location.reload(); }, 1500);
                } else {
                    toastr["error"]("Error", obj.message);
                }
            }
        });
    }
</script>
