<script type="text/javascript">
jQuery(document).ready(function($)
{

	$('.pdf-element-link').on('click', function() {
	    var cursorPos = $('textarea[name="pdf_template"]').prop('selectionStart');
	    var v = $('textarea[name="pdf_template"]').val();


	    var textBefore = v.substring(0,  cursorPos);
	    var textAfter  = v.substring(cursorPos, v.length);

	    $('textarea[name="pdf_template"]').val(textBefore + 'maranza' + textAfter);
	});




	// $('body').on('click', '.pdf-element-link', function(e)
	// {
	// 	e.preventDefault();

	// 	alert('asd');
	// })
});
</script>


<script>


window.onload = function () {
    var editor = CodeMirror.fromTextArea($("textarea[name='pdf_template']")[0], {
        lineNumbers: true,
        lineWrapping: true,
    });
};

</script>



<div class="ib-filecabinets">
	@include('filecabinet::filecabinetTemplates._categories', ['categoryTree' => $categoryTree])
</div>



