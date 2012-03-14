$(document).ready(function(){
	$('div.group_join a.applied').click(function() {return false;});
	
	$('div.group_join a.apply').click(apply);
	$('div.op a.group_apply').click(apply);
	
	function apply() 
	{
		var html = '<div class="dialog_body" title="' + $(this).text() + '">'+
		  '<form action="' + $(this).attr('href') + '" method="post">' +
		  '<p>申请加入将自动关注创建人</p>' +
		  '<fieldset><div class="simple_form"><span>您还可以输入 <em id="message_data-count">30</em>个字</span>'+
		  '<textarea name="message"></textarea></div>'+
		  '<div class="op"><input type="submit" value="确定" class="confirm button60"></input>'+
	      '<a class="cancel button60" href="#">取消</a></div>'+
		  '<input type="hidden" value="' + $('body').attr('token') + '" name="token"></input>'+
		  '</fieldset></form></div>';
	
		var arrPageSizes = getPageSize();
		var top = arrPageSizes[3]/3;
		var left = arrPageSizes[2]/3;
		  
		var dlg = $(html).dialog({width : 396, height : 210, 
			  position : [left, top], draggable : true, resizable : false,
			  close: function(event, ui) {$(this).dialog('destroy').remove();return false;}});
		  
		$('a.cancel', dlg).click(function() {
			$(this).parents('div.dialog_body').dialog('close');
			return false;
		});
		$("form", dlg).ajaxForm(sendApplyoptionsbtn).each(addAjaxHidden);
		$("form textarea", dlg)
			.bind("keyup paste", function() {
				counterArray(this, $("span em", $(this).parent()), 9, 30);
			})
			.bind("keydown", textareaEventHandler).focus();
		return false;
	};

	var sendApplyoptionsbtn = {
		dataType: 'json',
		beforeSubmit: function(xml, jqForm) {
		    if ($("textarea", jqForm).val().length > 30) {
		        alertFail('您输入的验证消息超过了30个字');
				return false;
		    }
		},
		success: function(json) {
			$('div.dialog_body').dialog('close');
			$('div.group_join').html('<a href="#" class="button94 green94 applied">已申请</a>您已经申请，请耐心等待管理员的审核！').find("a").click(function() {
				return false;
			});
			$('div.join').html('<a href="#" class="applied">您已申请加入</a>').find("a").click(function() {
				return false;
			});
			
			$('li#group-' + json.groupid + ' div.op').html('<div class="done">已申请</div>');
			
			fadeSuccess("请求成功发送，请等待管理员审核", 3000);
		},
        error: function(xml) {
	         var rtext = $("div.error", xml.responseText).html();
			 alertFail(rtext);
		 }
	};
});