$(document).ready(function(){
	var applyOptions = { 
		dataType: 'json',
		error : function() {
			alertFail('发生未知错误，处理加入请求失败！');
		},
		success: function(json) {
			$('#users li.user[pid="' + json.pid + '"]').remove();
			var span = $('#totalitem span');
			if (span != null) {
				span.text(parseInt(span.text()) - 1);
			}
			fadeSuccess("处理加入请求成功");
		}
	};
	
	$("form.form_application").ajaxForm(applyOptions).each(addAjaxHidden);
});