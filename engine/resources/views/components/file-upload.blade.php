<div class="form-group">
    <label class="mb-1">{{ $label }}</label>
    <div class="clearfix pb-2"></div>

    <!-- Preview Image -->
    <img id="{{ $id }}Preview" alt="{{ $label }}" width="100" height="100" style="display:none;" />

    <!-- Download link for non-image files -->
    <a id="{{ $id }}Download" href="#" style="display:none;" download>Download File</a>

    <!-- File Input -->
    <input type="file" name="{{ $name }}" class="form-control"
        onchange="
            var file = this.files[0];
            var img = document.getElementById('{{ $id }}Preview');
            var downloadBtn = document.getElementById('{{ $id }}Download');

            if (file) {
                if (file.type.startsWith('image/')) {
                    img.style.display = 'block';
                    img.src = window.URL.createObjectURL(file);

                    downloadBtn.style.display = 'none';
                    downloadBtn.href = '';
                } else {
                    img.style.display = 'none';
                    img.src = '';

                    downloadBtn.style.display = 'block';
                    downloadBtn.href = window.URL.createObjectURL(file);
                    downloadBtn.innerHTML = 'Download ' + file.name;
                }
            } else {
                img.style.display = 'none';
                img.src = '';
                downloadBtn.style.display = 'none';
                downloadBtn.href = '';
            }
        "
    >
</div>
