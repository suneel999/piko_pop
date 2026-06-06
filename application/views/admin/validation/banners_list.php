<script>
    $(document).ready(function() {
        $('#banners_table').DataTable({
            "order": [[0, "desc"]],
            "ajax": {
                url: base_url + 'admin_root/banners/banners_data_ajax',
                type: 'POST',
                dataSrc: function(json) {
                    if (json.status == 200) {
                        return json.data;
                    } else {
                        return [];
                    }
                }
            }
        });
    });

    function delete_banner(bid) {
        Swal.fire({
            title: "Delete Banner?",
            text: "This banner will be deleted permanently.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#f46a6a",
            cancelButtonColor: "#34c38f",
            confirmButtonText: "Yes, delete it!"
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: base_url + 'admin_root/banners/delete_banner',
                    type: "POST",
                    data: {
                        id: bid
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

    function change_status(bid, status) {
        $.ajax({
            url: base_url + 'admin_root/banners/change_status',
            type: "POST",
            data: {
                id: bid,
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
