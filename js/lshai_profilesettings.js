$(document).ready(function(){
	$('#location').citySelect();
	
	$('#homepage').bind('focus', function() {
		if (! $(this).val().match(/^http:\/\/.*$/)) {
			$(this).val('http://' + $(this).val());
		}
	}).blur(function() {
		if ($(this).val().trim() == 'http://') {
			$(this).val('');
			$.data(this.form, 'validator').element($(this));
		}
	});
	showBirthday();
	
	if ($('#school').size() > 0) {
		$('#school').schoolSelect();
	}
	if ($('#occupation').size() > 0) {
		$('#occupation').installOccupationSelect();
	}
	validateForm();
	
});

function showBirthday() {
	$("#birthday").datepicker($.datepicker.regional['zh-CN']);
}

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
	}, "请确保输入的值在2-12个字节之间(一个中文字算2个字节)");
	
	$("#form_settings_profile").validate({
		onkeyup: false ,
		submitHandler: function(form) {
			showLoading();
			form.submit();
		},
		highlight: function(error, element) {
			// do nothing
		},
		errorElement : 'label',
		errorClass : 'error',
		errorPlacement: function(error, element) {
			$(element).parent().append(error);
		},
		rules: {
			nickname: {
				required: true,
				byteRangeLength: [2, 12]
			},
			homepage: {
				url: true
			},
			bio: {
				minlength: 10,
				maxlength: 280
			}
		},
		messages: {
			nickname: {
				required: "请输入名字",
				byteRangeLength: "名字长度应在2-12个字符之间, 中文字算两个字符"
			},
			homepage: {
				url: "请输入有效的URL"
			},
			bio : {
				minlength: "不能少于10字",
				maxlength: "不能超过280个字"
			}
		}
	});
}