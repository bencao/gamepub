$(document).ready(function(){
	
	validateForm();
	
	$("#register dl dd input[type='text'], #register dl dd input[type='password']").focus(function(){
		var par = $(this).parents("dd");
		if ($('span', par).size() == 0) {
			par.append('<span htmlfor="' + $(this).attr('id') + '" generated="true">' + $(this).attr('tip') + '</span>');
		} else {
		}
	}).blur(function() {
	});
	
	$("#reloadimg").click(function(){$("#vefimg").attr("src",
			"/ajax/randverifypic?timestamp=" + (new Date()).valueOf());
			return false;
	});
});

function validateForm() {
	
	jQuery.validator.addMethod("byteRangeLength", function(value, element,
			param) {
		var length = value.length;
		for ( var i = 0; i < value.length; i++) {
			if (value.charCodeAt(i) > 127) {
				length++;
			}
		}
		return this.optional(element)
				|| (length >= param[0] && length <= param[1]);
	}, "请确保在2-12个字符(中文算2个字节)");
	
	// 字符验证   
	jQuery.validator.addMethod("userName", function(value, element) {   
	  return this.optional(element) || /^[a-z][a-z0-9_]*$/.test(value);   
	}, "以小写英文字母开头，由字母和数字组成");
	
	jQuery.validator.addMethod("nickName", function(value, element) {
		  return this.optional(element) || ! ((/^[0-9]+$/.test(value)) || (/^[asdf]+[0-9]*$/.test(value)));
		}, "您不可以使用该昵称");
	
	$("#register").validate({
		onkeyup: false ,
		submitHandler: function(form) {
			if (document.getElementById("agreelicense").checked) {
				showLoading();
				form.submit();
			} else {
				alertFail('请阅读《服务协议》，您需要同意该协议才能注册');
				return false;
			}
		},
		success: function(label) {
			label.removeClass('error').addClass('success').html('&#160;');
		},
		highlight: function(error, element) {
			// do nothing
		},
		errorElement : 'span',
		errorClass : 'error',
		errorPlacement: function(error, element) {
			var par = $(element).parents("dd");
			if ($('span', par).size() > 0) {
				$('span', par).remove();
			}
			par.append(error);
		},
		rules: {
			uname: {
				required: true,
				minlength: 5,
				maxlength: 20,
				userName: true,
				remote: "/ajax/ifexistuser"
			},
			nickname: {
				required: true,
				nickName : true,
				byteRangeLength: [2, 12]
			},
			password: {
				required: true,
				minlength: 6
			},
			confirm: {
				required: true,
				minlength: 6,
				equalTo: "#password"
			},
			email: {
				required: true,
				email: true
			},
			game_server: {
				required: true
			},
			sex: {
				required: true
			},
			recruit: {
				digits: true,
				rangelength : [11, 11]
			},
			reg_rand: {
				required: true,
				digits: true,
				rangelength : [5, 5]
			}
		},
		messages: {
			uname: {
				required: "请输入用户名",
				minlength: "用户名不能少于5个字符",
				maxlength: "用户名不能多于20个字符",
				remote: "用户名已存在"
			},
			nickname: {
				required: "请输入您的名字",
				byteRangeLength: "名字由2-12个字符组成(汉字算两个字符)"
			},
			password: {
				required: "请输入密码",
				minlength: "密码不能少于6个字符"
			},
			confirm: {
				required: "请输入确认密码",
				minlength: "密码不能少于6个字符",
				equalTo: "与上面的密码不一致"
			},
			email: {
				required: "请输入邮件地址",
				email: "请输入有效的邮件地址"
			},
			game_server: {
				required: "请选择您正在玩的游戏"
			},
			sex: {
				required: "请选择您的性别"
			},
			recruit: {
				digits: "新手卡号必须为数字",
				rangelength: "新手卡号必须为11位长"
			},
			reg_rand: {
				required: "请输入验证码",
				digits: "验证码必须为数字",
				rangelength: "验证码必须为5位长"
			}
		}
	});
}