$(document).ready(function(){
	$("#feedback").validate({
		onkeyup: false ,
		submitHandler: function(form) {
			showLoading();
			form.submit();
		},
		highlight: function(error, element) {
			// do nothing
		},
		errorElement : 'span',
		errorClass : 'error',
		errorPlacement: function(error, element) {
		    $(element).parent().append(error);
		},
		rules: {
			type: {
				required: true
			},
			email: {
				email: true,
				maxlength : 100
			},
			description: {
				required: true
			}
		},
		messages: {
			type: {
				required: "请选择问题类型"
			},
			email: {
				email: "请输入正确的邮件地址",
				maxlength : '邮件太长了，最长100字'
			},
			description: {
				required: "请填写问题描述"
			}
		}
	});
});