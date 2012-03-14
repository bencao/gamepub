$(document).ready(function(){
	if($('input#cates')) {
		$('input#cates').cateSelect();
	}
//	if($('#location_life')){
//		$('#location_life').citySelect();
//	}
//	if($('#location_game')){
//		$('#location_game').citySelect1();
//	}
	validateForm();
	
});

function validateForm() {
	jQuery.validator.addMethod("byteRangeLength", function(value, element,
			param) {
		var length = value.length;
		for ( var i = 0; i < value.length; i++) {
			if (value.charCodeAt(i) > 127) {
				length +=2;
			}
		}
		return this.optional(element)
				|| (length >= param[0] && length <= param[1]);
	}, "请确保输入的值在规定的长度范围之内");
	
	// 字符验证
	jQuery.validator.addMethod("groupName", function(value, element) {
		return this.optional(element) || /^[A-Za-z0-9\u4E00-\u9FA5]*$/.test(value);
		}, "群组名字只能包含大小写英文字母，数字和汉字");
	
	jQuery.validator.addMethod("audioUrl", function(value, element) {
		return this.optional(element) || value.search(/^https?:\/\/(.*)\.mp3$/i)!=-1;
		}, "你输入的音乐地址不正确");
	
	if($("#form_gamegroup_add")){
	$("#form_gamegroup_add").validate({
		onkeyup: false ,
		errorElement : 'label',
		errorClass : 'error',
		errorPlacement: function(error, element) {
			$(element).parent().append(error);
		},
		rules: {
			uname: {
				required: true,
				groupName: true,
				byteRangeLength: [2, 18]
			},
			nickname: {
				required: true,
				byteRangeLength: [2, 24]
			},
			location: {
				byteRangeLength: [2, 100]
			},
			description: {
				required: true,
				byteRangeLength: [2, 150]
			},
			backmusic: {
				audioUrl: true
			}
		},
		messages: {
			uname: {
				required: "请输入群组名",
				byteRangeLength: "名字不超过6个中文字或18个英文字符",
				groupName: "群组名字只能包含大小写英文字母，数字和汉字"
			},
			nickname: {
				required: "请输入群组全名",
				byteRangeLength: "不超过8个中文字或24个英文字符"
			},
			location: {
				byteRangeLength: "地址不超过33个中文字或100个英文字符"
			},
			description: {
				required: "请输入群组简介",
				byteRangeLength: "简介不超过50个中文字或150个英文字符"
			},
			backmusic: {
				audioUrl: "背景音乐的地址不正确"
			}
		}

	});}
	
	if($("#form_lifegroup_add")){
	$("#form_lifegroup_add").validate({
		onkeyup: false ,
		errorElement : 'label',
		errorClass : 'error',
		errorPlacement: function(error, element) {
			$(element).parent().append(error);
		},
		rules: {
			uname: {
				required: true,
				groupName: true,
				byteRangeLength: [2, 18]
			},
			nickname: {
				required: true,
				byteRangeLength: [2, 24]
			},
			cates: {
				required: true
			},
			location: {
				byteRangeLength: [2, 100]
			},
			description: {
				required: true,
				byteRangeLength: [2, 150]
			}
		},
		messages: {
			uname: {
				required: "请输入群组名",
				byteRangeLength: "名字不超过6个中文字或18个英文字符",
				groupName: "群组名字只能包含大小写英文字母，数字和汉字"
			},
			nickname: {
				required: "请输入群组全名",
				byteRangeLength: "不超过8个中文字或24个英文字符"
			},
			cates: {
				required: "请选择目录"
			},
			location: {
				byteRangeLength: "地址不超过33个中文字或100个英文字符"
			},
			description: {
				required: "请输入群组简介",
				byteRangeLength: "简介不超过50个中文字或150个英文字符"
			}
		}

	});
	}
}