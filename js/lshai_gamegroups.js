function successFilterGameGroups(json){
	if (json.result == 'true') {
		$('dl.grid-3:eq(1) dd').html(json.groups);
		$("a.page").click(ajaxPage);
	}
}

function ajaxPage() {
	$.ajax({
		url : $(this).attr('href'),
		dataType : 'json',
		data : {sameserver : $('input#sameserver').attr('checked')?1:0},
		success : successFilterGameGroups
	});
	
	return false;
}

$(document).ready(function(){
	$("input#sameserver").click(function(){
		$.ajax({
			url : '/ajax/filtergamegroups',
			data : {sameserver : $('input#sameserver').attr('checked')?1:0},
			dataType : 'json',
			success : successFilterGameGroups
		});
	});
	$("a.page").click(ajaxPage);
});