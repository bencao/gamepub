$(document).ready(function() {
	$("#resendmail").click(function() {
		if (window.sending) {
			return false;
		}
		window.sending = true;
		showLoading();
		$.ajax({
		    type: "post",
			url: $(this).attr('href'),
			data: {ajax: 1, token : $('body').attr('token')}, 
			dataType: "json",
			error: function (XMLHttpRequest, textStatus, errorThrown) {
				alertFail('重发邮件时发生错误，请过段时间再试');
				window.sending = false;
				$.unblockUI();
			},
			success: function(json){
				if (json.result == 'true') {
					alertSuccess('重发成功， 请前往收取');
				} else {
					alertFail(json.msg);
				}
				window.sending = false;
			}
		});
		return false;
	});
	
	$("#sendnew").click(function() {
		if (window.sending) {
			return false;
		}
		var newmail = $('#newmail').val();
		if (! newmail) {
			alertFail('请输入新邮件地址');
			return false;
		}
		
		var pattern=/^([a-zA-Z0-9_.-])+@([a-zA-Z0-9_.-])+\.([a-zA-Z])+([a-zA-Z])+/;
		if (! pattern.test(newmail)) {
			alertFail('请输入有效的邮件地址');
			return false;
		}
		
		window.sending = true;
		showLoading();
		$.ajax({
		    type: "post",
			url: $(this).attr('href'),
			data: {ajax: 1, token : $('body').attr('token'), newmail : newmail}, 
			dataType: "json",
			error: function (XMLHttpRequest, textStatus, errorThrown) {
				alertFail('重发邮件时发生错误，请过段时间再试');
				window.sending = false;
				$.unblockUI();
			},
			success: function(json){
				if (json.result == 'true') {
					alertSuccess('重发成功， 请前往收取');
				} else {
					alertFail(json.msg);
				}
				window.sending = false;
			}
		});
		return false;
	});
});