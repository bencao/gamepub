$(document).ready(function(){
	popupIllegalReport();
	$("form#form_illegal_report").ajaxForm(illreport).each(addAjaxHidden);
});

//var illegalreportoptions = {
//		dataType: 'json',
//		success: function(jaser) {
//	        var url = $('#next_url').attr('value');
//	        fadeSuccess("举报信息已成功发送，3秒后自动返回。");
//	        $(this).oneTime(1000, function() {
//	        	window.location = url;
//	          });
//	    }
//};

function popupIllegalReport() {
//	$.ui.dialog.defaults.bgiframe = true;
	$('#illegalreport').dialog({ autoOpen: false, height: 350, width: 530, draggable : true, resizable : false});
	$('#get_illreport').click(
			function() {
				$('#illegalreport').dialog('open');
			}
	);
	$('#cancel_report').click(
			function() {
				$('#illegalreport').dialog('close');
			}
    );
}

var illreport = {
		dataType: 'json',
		beforeSubmit: function(xml, jqForm) {
			var reason = $('form.form_illegal_report #reason').val();
			var description = $('form.form_illegal_report #description').val();
		   if(reason == 0 ){
			   $('#reasonTip').show();
			   return false;
		   }
		   if (/^(　|\s)*$/.test(description) == true || description.length>255) {
			   $('#desTip').show();
			   return false;
		   }
		},
		error: function (XMLHttpRequest, textStatus, errorThrown) {
			alertFail('非法举报时发生错误，请过段时间再试');
		},
		success: function(jaser) {
			$('#illegalreport').dialog('close');
			fadeSuccess("举报提交成功！我们会尽快审核，确认后会给您一定财富奖励，谢谢！");
		}
};