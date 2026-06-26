<div class="modal fade" id="filePreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filePreviewTitle">Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center" id="filePreviewBody"></div>
        </div>
    </div>
</div>

<script>
    function previewFile(url, name) {
        var body = document.getElementById('filePreviewBody');
        var isImage = /\.(jpe?g|png|gif|webp|bmp|svg)$/i.test(url);

        document.getElementById('filePreviewTitle').textContent = name || 'Preview';

        if (isImage) {
            var img = document.createElement('img');
            img.src = url;
            img.className = 'img-fluid rounded';
            body.replaceChildren(img);
        } else {
            var frame = document.createElement('iframe');
            frame.src = url;
            frame.style.width = '100%';
            frame.style.height = '70vh';
            frame.style.border = '0';
            body.replaceChildren(frame);
        }

        new bootstrap.Modal(document.getElementById('filePreviewModal')).show();
    }

    document.addEventListener('click', function(e) {
        var trigger = e.target.closest('.preview-trigger');
        if (!trigger) {
            return;
        }
        e.preventDefault();
        previewFile(trigger.dataset.url, trigger.dataset.name);
    });
</script>
