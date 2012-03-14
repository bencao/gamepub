$(document).ready(function(){

	$("a.groupblock").click(function() {
		ajaxBlock($(this).attr('href'), $(this).attr('groupid'), $(this).attr('profileid'), $(this).attr('next_url'));
		return false;
	});
	
	$("a.makeadmin").click(function() {
		ajaxMakeAdmin($(this).attr('href'), $(this).attr('groupid'), $(this).attr('profileid'));
		return false;
	});
	
	$("a.canceladmin").click(function() {
		ajaxCancelAdmin($(this).attr('href'), $(this).attr('groupid'), $(this).attr('profileid'));
		return false;
	});
	
	function ajaxBlock(url, groupid, profileid, next_url) {
		$.ajax({
			type : 'POST',
			url : url,
			data : {id : groupid, profileid : profileid, next_url : next_url, token : $('body').attr('token'), ajax : '1'},
			dataType: 'json',
			success: function(json) {
				$('#users li[pid="'+json.pid +'"]').remove();
				var span = $('#totalitem span');
				if (span != null) {
					span.text(parseInt(span.text()) - 1);
				}
				fadeSuccess("屏蔽用户成功");
			},
			error: function(xml) {
				 var rtext = $("div.error", xml.responseText).html();
				 alertFail(rtext);
			}
		});
	}
	
	function ajaxMakeAdmin(url, groupid, profileid) {
		$.ajax({
			type : 'POST',
			url : url,
			data : {id : groupid, profileid : profileid, token : $('body').attr('token'), ajax : '1'},
			dataType: 'json',
			success: function(json) {
				var op = $("#users li[pid='" + json.pid + "'] div.op");
				$("a.makeadmin", op).parent().html('<a class="canceladmin" href="' + json.action + '" profileid="'+ json.pid +'" token="' + 
						$('body').attr('token') + '" groupid="' + json.groupid + '">删管理员</a>');
				
				$("a.canceladmin", op).click(function() {ajaxCancelAdmin($(this).attr('href'), $(this).attr('groupid'), $(this).attr('profileid'));return false;});
				fadeSuccess("加管理员成功");
			},
			error: function(xml) {
				 var rtext = $("div.error", xml.responseText).html();
				 alertFail(rtext);
			}
		});
	}
	
	function ajaxCancelAdmin(url, groupid, profileid) {
		$.ajax({
			type : 'POST',
			url : url,
			data : {id : groupid, profileid : profileid, token : $('body').attr('token'), ajax : '1'},
			dataType: 'json',
			success: function(json) {
				var op = $("#users li[pid='" + json.pid + "'] div.op");
				$("a.canceladmin", op).parent().html('<a class="makeadmin" href="' + json.action + '" profileid="'+ json.pid +'" token="' + 
						$('body').attr('token') + '" groupid="' + json.groupid + '">加管理员</a>');
				
				$("a.makeadmin", op).click(function() {ajaxMakeAdmin($(this).attr('href'), $(this).attr('groupid'), $(this).attr('profileid'));return false;});
				fadeSuccess("删管理员成功");
			},
			error: function(xml) {
				 var rtext = $("div.error", xml.responseText).html();
				 alertFail(rtext);
			}
		});
	}
	
	var unblockOptions = { 
			dataType: 'json',
			error : function() {
				alertFail('发生未知错误，取消屏蔽失败！');
			},
			success: function(json) {
				$('#users li.user[pid="' + json.pid + '"]').remove();
				var span = $('#totalitem span');
				if (span != null) {
					span.text(parseInt(span.text()) - 1);
				}
				fadeSuccess("取消屏蔽成功");
			}
	};
	
	$("form.form_group_unblock").ajaxForm(unblockOptions).each(addAjaxHidden);
});