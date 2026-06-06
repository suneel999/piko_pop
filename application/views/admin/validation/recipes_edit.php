<script>
    $(document).ready(function() {
        var $picker = $('#product_picker').select2({
            placeholder: 'Search and select products...',
            width: '100%',
            closeOnSelect: false,
        });

        $picker.on('change', function() {
            renderSelectedProducts($(this).val() || []);
        });

        $('#youtube_url').on('input change', function() {
            var id = extractYouTubeId($(this).val());
            if (id) $('#youtube_thumb_img').attr('src', 'https://img.youtube.com/vi/' + id + '/hqdefault.jpg');
        });

        $('#thumbnail').on('change', function(e) {
            var file = e.target.files[0];
            if (!file) { $('#thumbnail_preview').hide(); return; }
            var reader = new FileReader();
            reader.onload = function(ev) {
                $('#thumbnail_preview_img').attr('src', ev.target.result);
                $('#thumbnail_preview').show();
            };
            reader.readAsDataURL(file);
        });

        $('#edit_recipe').on('submit', function(e) {
            e.preventDefault();

            var title = $('#title').val().trim();
            var ytUrl = $('#youtube_url').val().trim();
            if (!title) { toastr.error('Please enter a title.'); return; }
            if (!extractYouTubeId(ytUrl)) { toastr.error('Please enter a valid YouTube URL.'); return; }

            var formData = new FormData(this);
            var picked = $('#product_picker').val() || [];
            picked.forEach(function(pid) {
                formData.append('product_ids[]', pid);
                var qty = $('input[name="product_quantities[' + pid + ']"]').val() || 1;
                formData.append('product_quantities[' + pid + ']', qty);
            });

            $('#save_btn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Saving...');

            $.ajax({
                url: base_url + 'admin_root/recipes/update_recipe',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(result) {
                    var obj = typeof result === 'string' ? JSON.parse(result) : result;
                    if (obj.status == 200) {
                        toastr.success(obj.message);
                        setTimeout(function() { window.location.href = base_url + 'admin_root/recipes'; }, 1200);
                    } else {
                        toastr.error(obj.message || 'Something went wrong.');
                        $('#save_btn').prop('disabled', false).html('<i class="mdi mdi-content-save me-1"></i> Update Recipe');
                    }
                },
                error: function() {
                    toastr.error('Request failed.');
                    $('#save_btn').prop('disabled', false).html('<i class="mdi mdi-content-save me-1"></i> Update Recipe');
                }
            });
        });
    });

    function renderSelectedProducts(ids) {
        var $body = $('#selected_products_body');
        var existingQtys = {};
        $body.find('tr').each(function() {
            var pid = $(this).data('pid');
            existingQtys[pid] = $(this).find('input[type="number"]').val();
        });
        $body.empty();
        if (!ids || ids.length === 0) {
            $('#selected_products_table').hide();
            $('#no_products_msg').show();
            return;
        }
        $('#no_products_msg').hide();
        $('#selected_products_table').show();
        ids.forEach(function(pid) {
            var name = $('#product_picker option[value="' + pid + '"]').text();
            var qty = existingQtys[pid] || 1;
            $body.append(
                '<tr data-pid="' + pid + '">' +
                    '<td>' + name + '</td>' +
                    '<td><input type="number" min="1" class="form-control form-control-sm" name="product_quantities[' + pid + ']" value="' + qty + '"></td>' +
                    '<td><button type="button" class="btn btn-sm btn-soft-danger remove-product-row" data-pid="' + pid + '"><i class="mdi mdi-close"></i></button></td>' +
                '</tr>'
            );
        });
    }

    $(document).on('click', '.remove-product-row', function() {
        var pid = $(this).data('pid');
        var current = $('#product_picker').val() || [];
        current = current.filter(function(x) { return x != pid; });
        $('#product_picker').val(current).trigger('change');
    });

    function extractYouTubeId(url) {
        if (!url) return '';
        var patterns = [
            /youtube\.com\/watch\?v=([A-Za-z0-9_-]{6,})/i,
            /youtu\.be\/([A-Za-z0-9_-]{6,})/i,
            /youtube\.com\/embed\/([A-Za-z0-9_-]{6,})/i,
            /youtube\.com\/shorts\/([A-Za-z0-9_-]{6,})/i
        ];
        for (var i = 0; i < patterns.length; i++) {
            var m = url.match(patterns[i]);
            if (m) return m[1];
        }
        return '';
    }
</script>
