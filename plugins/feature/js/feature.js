$(document).ready(function() {
	$('#feature_new a.close').click(function() {
		$.ajax({
			url : '/ajax/ignorenote',
			type : 'POST',
			data : {cz : $('#feature_new').attr('cz'), token : $('body').attr('token')}
		});
		$('#feature_new').remove();
		return false;
	});
	$('#feature_new a.follow').click(function() {
		$.ajax({
			url : '/ajax/ignorenote',
			type : 'POST',
			data : {cz : $('#feature_new').attr('cz'), token : $('body').attr('token')}
		});
		$('#feature_new').remove();
		return true;
	});
});