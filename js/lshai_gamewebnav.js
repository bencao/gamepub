var newGameWeb = {
	dataType: 'json',
	beforeSubmit: function(formData, jqForm, options) {
		//validate input
		var warningText = "";
		
		if ($('#txt_webname').val().length == 0) {
			alertFail("请填写网站名称");
			return false;
		}
		if ($('#txt_website').val().length == 0) {
			alertFail("请填写网站地址");
			return false;
		}
		var detailLen = $('#txt_webdetail').val().length;
		if (detailLen == 0) {
			alertFail("请填写网站描述");
			return false;
		} else if(detailLen > 280) {
			alertFail("网站描述不能多于280个字符");
			return false;
		}
		
		if (jqForm.hasClass('processing')) {
			return false;
		}
		jqForm.addClass("processing");
		$("input.button94", jqForm).attr("disabled", "disabled").addClass("disabled");
		return true;
	},
	success : function(json) {
		if(json.result == 'true') {
			$('div.dialog_body').dialog('close');
			fadeSuccess("提交成功");
		} else {
			$("div.dialog_body form").removeClass("processing");
			$("div.dialog_body input.button94").removeAttr('disabled').removeClass('disabled');
			alertFail(json.result);
		}
	}
};

function showDialog(html){
	var arrPageSizes = getPageSize();
	var top = arrPageSizes[3]/3;
	var left = arrPageSizes[2]/3;
	
	var msgdlg = $(html).dialog({width:450, height:230, position : [left, top], draggable : true, resizable : false,
		close: function(event, ui) {
			$(this).dialog('destroy').remove();
			window.sending = false;
			return false;
		}
	});

	$("form", msgdlg).ajaxForm(newGameWeb).each(addAjaxHidden);
}

function applyGameWeb(){
	if(window.sending){
		return false;
	}
	window.sending = true;
	
	var html = '<div class="dialog_body" title=' + $(this).attr('gname') + '站点申请>'
		+ '<form method="post" action="' + $(this).attr('href') + '"><fieldset>'
		+ '<legend>站点申请</legend>'
		+ '<dl class="inputs clearfix">'
		+ '<dt><label for="txt_webname">网站名称：</label></dt><dd><input name="webname" id="txt_webname" class="text300" maxlength=80/></dd>'
		+ '<dt><label for="txt_website">网站地址：</label></dt><dd><input name="website" id="txt_website" class="text300" maxlength=160/></dd>'
		+ '<dt><label for="txt_webdetail">网站描述：</label></dt><dd><textarea name="webdetail" id="txt_webdetail" class="textarea376" /></dd>'
		+ '<div class="op"><input type="submit" class="orange94 button94 aligncenter" name="websubmit" value="提交申请"/></div>' 
		+ '<input type="hidden" name="token" value="' + $('body').attr('token') + '" />' 
		+ '</fieldset></form></div>';
	showDialog(html);
	return false;
}

function increaseClick(){
	$.ajax({
		url : '/ajax/increasegamewebclick',
		dataType : 'json',
		data : {gwid : $(this).attr('gwid')}
	});
	//whether succeed or not, return true to link to web site
	return true;
}


$(document).ready(function(){
	$('a.webnav').click(increaseClick);
	$('a.webapply').click(applyGameWeb);
});