<!--begin::Footer-->
<div id="kt_app_footer" class="app-footer">
	<!--begin::Footer container-->
	<div class="app-container container-xxl d-flex flex-column flex-md-row flex-center flex-md-stack py-3">
		<!--begin::Copyright-->
		<div class="text-gray-900 order-2 order-md-1">
			<span class="text-muted fw-semibold me-1">{{ date('Y') }}&copy;</span>
			<a href="https://wa.me/+919540329321" target="_blank" class="text-gray-800 text-hover-primary">ipulse web solution</a>
		</div>
		<!--end::Copyright-->

		<!--end::Menu-->
	</div>
	<!--end::Footer container-->
</div>
<!--end::Footer-->

<!-- jQuery (required for Trumbowyg) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Trumbowyg JS -->
<script src="https://cdn.jsdelivr.net/npm/trumbowyg@2.28.0/dist/trumbowyg.min.js"></script>

<!-- Optional plugins -->
<script src="https://cdn.jsdelivr.net/npm/trumbowyg@2.28.0/dist/plugins/upload/trumbowyg.upload.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/trumbowyg@2.28.0/dist/plugins/base64/trumbowyg.base64.min.js"></script>

<!-- Your textarea -->
<textarea id="editor"></textarea>

<script>
    $(document).ready(function () {
        $('#editor').trumbowyg({
            btns: [
                ['undo', 'redo'], // undo/redo
                ['formatting'], // paragraph, headings
                ['strong', 'em', 'del'], // bold, italic, strikethrough
                ['superscript', 'subscript'],
                ['link'],
                ['insertImage'],
                ['unorderedList', 'orderedList'],
                ['horizontalRule'],
                ['removeformat'],
                ['viewHTML'] // code view
            ],
            autogrow: true
        });
    });
</script>
