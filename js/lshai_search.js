$(document).ready(function(){
	$("a.toggle_advance").click(function() {
		var form = $(this).parents("form.search");
		$("p.options", form).fadeOut('slow');
		$("div.more", form).fadeIn('slow');
		$("input.submit", form).attr('name', 'advance');
		return false;
	});
	$("a.toggle_back").click(function() {
		var form = $(this).parents("form.search");
		$("div.more", form).fadeOut('slow');
		$("p.options", form).fadeIn('slow');
		$("input.submit", form).attr('name', 'normal');
		return false;
	});
	$('form.search, dl.search').submit(function() {
		var qVal = $('input[name="q"]', this).val().trim();
		if (qVal == '') {
			$('input[name="q"]', this).focus();
			return false;
		}
		return true;
	});
});