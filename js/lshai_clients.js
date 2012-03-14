$().ready(function(){
	$('a.clientdown').click(function() {
		$.ajax({
			url : '/ajax/increaseclientdown',
			dataType : 'json',
			data : {c : $(this).attr('c'), url: $(this).attr('href'), token : $('body').attr('token')},
			success : function(json) {
				if (json.result == 'true') {
					window.location.href = json.url;
				}
			}
		});
		return false;
	});
});