var newMessageOptions = {
	dataType: 'json',
	beforeSubmit: function(formData, jqForm, options) {
		if ($("textarea", jqForm).val().length == 0) {
			jqForm.addClass("warning");
			alertFail("请输入您想说的话");
			return false;
		}
		if($("#replyto option:selected").val() == "tip") {
			alertFail("您还没有选择收件人");
			return false;
		}
		if (jqForm.hasClass('processing')) {
			return false;
		}
		jqForm.addClass("processing");
		$("input.confirm", jqForm).attr("disabled", "disabled").addClass("disabled");
		return true;
	},
	success : function(json) {
		//如果是发件箱, 则插入一条
		if($("#thirdary_nav li.active a").text() == '发件箱') {
	    	$my.notices.prepend(json.html);
    		$(".message a.mesdelete", $my.notices).click(deleteMessage);
		}
		
		$('div.dialog_body').dialog('close');
		fadeSuccess("发送成功");
	}
};

function deleteMessage() {
	if (!confirm("确认删除此悄悄话?")){ 
		return false;
	}
	
	$.ajax({
		  type: "post",
		  url: $(this).attr("href"),
		  dataType: "json",
		  data: {nid: $(this).attr('nid'), ajax: 1, token: $('body').attr('token')}, 
		  success: function(json){
			  if (json.result == 'true') {
				  var noticeid = json.deleted;
				  if($("#message-"+noticeid).length > 0) {
					  $("#message-"+noticeid).fadeOut("slow");
					  $("#message-"+noticeid).remove();
				  }
				  fadeSuccess("删除成功");
			  } else {
				  alertFail(json.msg);
			  }
		  }
		});
	return false;
}

function deleteAllMessage() {
	if (!confirm("确认删除所有?")){ //jConfirm
		return false;
	}
	
	var type = $(this).attr('type');
	//为空
	if(!$(".message", $my.notices).length || $(".guide").length>0) {
		if(type == 'inbox') {
			alertFail("您的收件箱没有消息");
		} else {
			alertFail("您的发件箱没有消息");
		}
		return false;
	}

	$.ajax({
		  type: "post",
		  url: $(this).attr("href"),
		  dataType: "json",
		  data: {ajax: 1, token: $('body').attr('token'), delall : 1}, 
		  error: function (xhr, textStatus, errorThrown) {	
			  alertFail("对不起, 在删除中中遇到一些问题, 请稍后再试");
		  },
		  success: function(json){
			  if (json.result == 'true') {
				  $my.notices.fadeOut(2500).html("");
				  $("div.empty").fadeOut(3000).remove();
				  $('#pagination').remove();
				  fadeSuccess("删除成功");
			  }
		  }
		});
	return false;
}

function showDialog(html){
	//alert(html);
	var arrPageSizes = getPageSize();
	var top = arrPageSizes[3]/3;
	var left = arrPageSizes[2]/3;
	
	var height = 220 + $(html).find('p').text().length/28*18;
	var msgdlg = $(html).dialog({width:396, height:height, position : [left, top], draggable : true, resizable : false,
		close: function(event, ui) {
			$(this).dialog('destroy').remove();
			window.sending = false;
			return false;
		}
	});

	$("form", msgdlg).ajaxForm(newMessageOptions).each(addAjaxHidden);
	
	$("form textarea", msgdlg)
	  	.bind("keyup paste", function() {
	  		counterArray(this, $("span em", $(this).parent()), 1);
	  	})
	  	.bind("keydown", textareaEventHandler).focus();
	
	$('a.cancel', msgdlg).click(function() {
		$(this).parents('div.dialog_body').dialog('close');
		return false;
	});
}

function sendMessage(){
	if(window.sending){
		return false;
	}
	window.sending = true;
	$.ajax({
		type:"GET",
		dataType:"json",
		url:'/ajax/getsubscribe',
		cache: false,
		error:function(xmlobject,textStatus){
			alertFail("发生错误，请重试！");
		},
		success: function(json) {
			if (json.result == 'true') {		
				var html = '<div class="dialog_body" title="发私信"><form method="post" action="/message/new" id="message_form1"><fieldset>' 
						+ '<legend>发私信</legend><div class="replyto"><label for="replyto">回复:</label>' 
						+ '<select id="replyto" name="to"><option value="tip">选择关注您的游友</option>';
				for (var i = 0; i < json.subs.length; i ++) {
					html += '<option value="' + json.subs[i].id + '">' + json.subs[i].nickname + '</option>';
				}
				html += '</select>' 
					+ '</div><div class="simple_form"><span><em id="message_text-count">280</em>字剩余</span><textarea cols="45" rows="3" style="overflow-y: auto;" id="message_data-text" name="status_textarea"></textarea></div><div class="op"><input type="submit" id="message_action_submit" class="confirm button60" name="status_submit" value="发送" title="发送" /><a href="#" class="cancel button60">取消</a></div>' 
					+ '<input type="hidden" name="token" value="' + $('body').attr('token') + '" /></fieldset></form></div>';
				
				showDialog(html);
			} else {
				alertFail(json.msg);
				window.sending = false;
			}	
		}
	});
	return false;
};

function replyMessage(){
	if(window.sending){
		return false;
	}
	window.sending = true;
	var text = $(this).attr('nickname') + "说：" + $(this).parents('.message').find('.content').text();
	
	var html = '<div class="dialog_body" title="回复' + $(this).attr('nickname') + '"><form method="post" action="/message/new" id="message_form1"><fieldset>' 
		+ '<legend>发私信</legend>'
		+ '<p>' + text + '</p>'
		+ '<div class="simple_form"><span><em id="message_text-count">280</em>字剩余</span><textarea cols="45" rows="3" style="overflow-y: auto;" id="message_data-text" name="status_textarea"></textarea></div><div class="op"><input type="submit" id="message_action_submit" class="confirm button60" name="status_submit" value="发送" title="发送" /><a href="#" class="cancel button60">取消</a></div>' 
		+ '<input type="hidden" name="token" value="' + $('body').attr('token') + '" />' 
		+ '<input type="hidden" name="to" value="' + $(this).attr('nid') + '" />'
		+ '</fieldset></form></div>';

	showDialog(html);
	return false;
}

$(window).load(function(){
	$(".message a.mesdelete", $my.notices).click(deleteMessage);
	$(".message a.mesreply", $my.notices).click(replyMessage);
	$("#sub_op a.delete").click(deleteAllMessage);
	$('#sendmessage').click(sendMessage);
});