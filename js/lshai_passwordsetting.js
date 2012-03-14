$(document).ready(function(){
	validateForm();
	$("#form_password_recover").submit(showLoading);
});

function validateForm() {
	$("#form_password").validate({
		errorPlacement: function(error, element) {
			$(element).parent().append(error);
		},
		errorElement : 'label',
		errorClass : 'error',
		rules: {
			oldpassword: {
				required: true,
				minlength: 6
			},
			newpassword: {
				required: true,
				minlength: 6
			},
			confirm: {
				required: true,
				minlength: 6,
				equalTo: "#newpassword"
			}
		},
		messages: {
			
			oldpassword: {
				required: "请输入原密码",
				minlength: "密码不能少于6个字符"
			},
			newpassword: {
				required: "请输入新密码",
				minlength: "密码不能少于6个字符"
			},
			confirm: {
				required: "请输入确认密码",
				minlength: "密码不能少于6个字符",
				equalTo: "与上面的密码不一致"
			}
		}

	});
}