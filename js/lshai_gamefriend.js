$(document).ready(function(){
	$("#pagination a").click(function() {
		$("form.mfsearch").find('input[name="page"]').val($(this).attr('page')).end().submit();
		return false;
	});
});