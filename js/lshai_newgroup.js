$(document).ready(function(){
//	$('#location_game').citySelect();
	$('#cates').cateSelect();
//	$('#location_life').citySelect1();

	$('input.radio').click(function(){
		if($(this).val() == '1'){
			document.getElementById('radio_game').checked = true;
			$('#form_lifegroup_add').hide();
			$('#form_gamegroup_add').show();
		}else{
			document.getElementById('radio_life').checked = true;
			$('#form_gamegroup_add').hide();
			$('#form_lifegroup_add').show();
		}
	});
	
	validateForm();

	$("#form_gamegroup_add dl dd input[type='text']").focus(function(){
		var par = $(this).parents("dd");
		if ($('label', par).size() == 0) {
			par.append('<label htmlfor="' + $(this).attr('id') + '" generated="true">' + $(this).attr('tip') + '</label>');
		} else {
		}
	}).blur(function() {
	});
	
	$("#form_lifegroup_add dl dd input[type='text']").focus(function(){
		var par = $(this).parents("dd");
		if ($('label', par).size() == 0) {
			par.append('<label htmlfor="' + $(this).attr('id') + '" generated="true">' + $(this).attr('tip') + '</label>');
		} else {
		}
	}).blur(function() {
	});
	
	$("#creator_one").click(function(){
		ajaxCreator($(this).attr('id'), $(this).attr('id'), 1);
	}).blur(function() {
	});
	
});

function ajaxCreator(id, tid, page) {
	$.ajax({
		type : 'GET',
		url : '/ajax/getgroupsubscribe',
		dataType : 'json',
		data : {tname : id, tid : tid, total : -1, page : page, search : ''},
		error : function() {
			alert('error');
		},
		success : function(json) {
			var dialogBody = '<div id="creator_choose" class="dialog_body" title="选择关注我的人作为共创者">'
				+ '<form action="#" method="post" class="addall"><fieldset><legend>选择关注我的人作为共创者</legend><div class="ul_wrap"><ul class="clearfix">'; 
			for (var i = 0; i < json.item.length; i ++) {
				dialogBody += '<li><input type="hidden" class="hidden" name="uname'+i+'" value="'+json.item[i].uname+'" /> ' 
				 + '<div class="avatar"><a href="#"><img src="' + json.item[i].avatar + '" title="' + json.item[i].uname + '"/></a></div>'
				 + '<a href="#" class="nickname" title="' + json.item[i].uname + '">' + json.item[i].nickname + '</a></li>';
			}
			dialogBody += '</ul></div><div class="aop clearfix">';
			dialogBody += '<a pg="'+ json.next +'" href="#" class="pagechg nextpage button60">下一页</a>';
			dialogBody += '<a pg="' + json.preview + '" href="#" class="pagechg previewpage button60">上一页</a>';
			dialogBody += '</div><div class="create_new"><input name="search" type="text" class="text200" /><input name="search_p" type="hidden" /><a href="#" class="postbtn search">搜索</a>';
			dialogBody += '</div><div class="op"><a href="#" class="cancel button60">取消</a></div></fieldset></form></div>';
			
			var arrPageSizes = getPageSize();
			var top = arrPageSizes[3]/3;
			var left = arrPageSizes[2]/3;
			  
			var ctDlg = $(dialogBody).dialog({width:434, position: [left, top], 
				close: function(event, ui) {$(this).dialog('destroy').remove();window.addingtag = false;return false;}});
			
			$("a.cancel", ctDlg).click(function() {
				$(this).parents('div.dialog_body').dialog('close');
				return false;
			});
			
			$("input", ctDlg).keypress(function(e){
				var keyCode = e.keyCode ? e.keyCode : e.which ? e.which : e.charCode;
				if (keyCode == 13){
					return false;
				}
			});
			
			$('div.avatar a, a.nickname', ctDlg).click(function() {
				var $pli = $(this).parents('li');
				var hiddenele = $pli.find('input.hidden');
				$('#'+json.tid).val(hiddenele.val());
				$('div.dialog_body').dialog('close');
				return false;
			});
			
			if(json.preview<=0) $('a.previewpage', ctDlg).hide();
			if(json.next<=0) $('a.nextpage', ctDlg).hide();
			$('input[name="search"]', ctDlg).val(json.search);
			$('input[name="search_p"]', ctDlg).val(json.search);
			
			$("a.pagechg", ctDlg).click(function() {
				var page = $(this).attr('pg');
				$.ajax({
					type : 'GET',
					url : '/ajax/getgroupsubscribe',
					dataType : 'json',
					data : {tname : id, tid : tid, page : page, total : json.total, search : $('input[name="search_p"]', ctDlg).val()},
					error : function() {
						alert('error');
					},
					success : function(json) {
						var ul = $('#creator_choose ul.clearfix');
						var html = '';
						for (var i = 0; i < json.item.length; i ++) {
							html += '<li><input type="hidden" class="hidden" name="uname'+i+'" value="'+json.item[i].uname+'" /> ' 
						 		+ '<div class="avatar"><a href="#"><img src="' + json.item[i].avatar + '" title="' + json.item[i].uname + '"/></a></div>'
						 		+ '<a href="#" class="nickname" title="' + json.item[i].uname + '">' + json.item[i].nickname + '</a></li>';
						}
						ul.html(html);
						if(json.next>0){
							$('#creator_choose a.nextpage').attr('pg', json.next).show();
						}else{
							$('#creator_choose a.nextpage').hide();
						}
						if(json.preview>0){
							$('#creator_choose a.previewpage').attr('pg', json.preview).show();
						}else{
							$('#creator_choose a.previewpage').hide();
						}
						$('#creator_choose input[name="search"]').val(json.search);
						$('#creator_choose div.avatar a, a.nickname').click(function() {
							var $pli = $(this).parents('li');
							var hiddenele = $pli.find('input.hidden');
							$('#'+json.tid).val(hiddenele.val());
							$('div.dialog_body').dialog('close');
							return false;
						});
					}
				});
			});
			
			$("a.postbtn", ctDlg).click(function() {
				var search = $('input[name="search"]', ctDlg).val();
				$.ajax({
					type : 'GET',
					url : '/ajax/getgroupsubscribe',
					dataType : 'json',
					data : {tname : id, tid : tid, page : 1, total : -1, search : search},
					error : function() {
						alert('error');
					},
					success : function(json) {
						var ul = $('#creator_choose ul.clearfix');
						var html = '';
						for (var i = 0; i < json.item.length; i ++) {
							html += '<li><input type="hidden" class="hidden" name="uname'+i+'" value="'+json.item[i].uname+'" /> ' 
				 				+ '<div class="avatar"><a href="#"><img src="' + json.item[i].avatar + '" title="' + json.item[i].uname + '"/></a></div>'
				 				+ '<a href="#" class="nickname" title="' + json.item[i].uname + '">' + json.item[i].nickname + '</a></li>';
						}
						ul.html(html);
						if(json.next>0){
							$('#creator_choose a.nextpage').attr('id', json.next).show();
						}else{
							$('#creator_choose a.nextpage').hide();
						}
						if(json.preview>0){
							$('#creator_choose a.previewpage').attr('id', json.preview).show();
						}else{
							$('#creator_choose a.previewpage').hide();
						}
						$('#creator_choose input[name="search"]').val(json.search);
						$('#creator_choose input[name="search_p"]').val(json.search);
						$('#creator_choose div.avatar a, a.nickname').click(function() {
							var $pli = $(this).parents('li');
							var hiddenele = $pli.find('input.hidden');
							$('#'+json.tid).val(hiddenele.val());
							$('div.dialog_body').dialog('close');
							return false;
						});
					}
				});
			});
			
		}
	});
}

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
	

	jQuery.validator.addMethod("userName", function(value, element) {
		return this.optional(element) || /^[A-Za-z0-9\u4E00-\u9FA5]*$/.test(value);
		}, "共创人只能包含大小写英文字母，数字和汉字");
	
	$("#form_gamegroup_add").validate({
		onkeyup: false ,
		success: function(label) {
			label.removeClass('error').addClass('success').html('&#160;');
		},
		errorElement : 'label',
		errorClass : 'error',
		errorPlacement: function(error, element) {
			var par = $(element).parents("dd");
			if ($('label', par).size() > 0) {
				$('label', par).remove();
			}
			$(element).parent().append(error);
		},
		rules: {
			creator_one: {
				required: true,
				userName: true,
				minlength: 5,
				maxlength: 12,
				remote: "/ajax/ifnotexistuser"
			},
//			creator_two: {
//				required: true,
//				userName: true,
//				minlength: 5,
//				maxlength: 12,
//				remote: "/ajax/ifnotexistuser"
//			},
//			creator_three: {
//				required: true,
//				userName: true,
//				minlength: 5,
//				maxlength: 12,
//				remote: "/ajax/ifnotexistuser"
//			},
//			creator_four: {
//				required: true,
//				userName: true,
//				minlength: 5,
//				maxlength: 12,
//				remote: "/ajax/ifnotexistuser"
//			},
			uname: {
				required: true,
				groupName: true,
				minlength: 2,
				maxlength: 18,
				remote: "/ajax/ifexistgroup"
			},
			nickname: {
				required: true,
				minlength: 2,
				maxlength: 24
			},
			location: {
				minlength: 2,
				maxlength: 100
			},
			description: {
				required: true,
				minlength: 2,
				maxlength: 150
			}
		},
		messages: {
			creator_one: {
				required: "请输入共创者用户名",
				minlength: "创建者名字不少于5个字符",
				maxlength: "创建者名字不超过4个中文字或12个英文字符",
				remote : "此用户名不存在"
			},
//			creator_two: {
//				required: "请输入共创者用户名",
//				minlength: "创建者名字不少于5个字符",
//				maxlength: "创建者名字不超过4个中文字或12个英文字符",
//				remote : "此用户名不存在"
//			},
//			creator_three: {
//				required: "请输入共创者用户名",
//				minlength: "创建者名字不少于5个字符",
//				maxlength: "创建者名字不超过4个中文字或12个英文字符",
//				remote : "此用户名不存在"
//			},
//			creator_four: {
//				required: "请输入共创者用户名",
//				minlength: "创建者名字不少于5个字符",
//				maxlength: "创建者名字不超过4个中文字或12个英文字符",
//				remote : "此用户名不存在"
//			},
			uname: {
				required: "请输入群组名",
				minlength: "名字不少于2个字符",
				maxlength: "名字不超过18个英文字符",
				groupName: "群组名字只能包含大小写英文字母，数字和汉字",
				remote : "此群组名已被使用"
			},
			nickname: {
				required: "请输入群组全名",
				minlength: "全名不少于2个字符",
				maxlength: "不超过8个中文字或24个英文字符"
			},
			location: {
				minlength: "地址不少于2个字符",
				maxlength: "地址不超过50个中文字或100个英文字符"
			},
			description: {
				required: "请输入群组简介",
				minlength: "简介不少于2个字符",
				maxlength: "简介不超过50个中文字或150个英文字符"
			}
		}

	});
	

	$("#form_lifegroup_add").validate({
		onkeyup: false ,
		success: function(label) {
			label.removeClass('error').addClass('success').html('&#160;');
		},
		errorElement : 'label',
		errorClass : 'error',
		errorPlacement: function(error, element) {
			var par = $(element).parents("dd");
			if ($('label', par).size() > 0) {
				$('label', par).remove();
			}
			$(element).parent().append(error);
		},
		rules: {
			uname: {
				required: true,
				groupName: true,
				minlength: 2,
				maxlength: 18,
				remote: "/ajax/ifexistgroup"
			},
			nickname: {
				required: true,
				minlength: 2,
				maxlength: 24
			},
			cates: {
				required: true
			},
			location: {
				minlength: 2,
				maxlength: 100
			},
			description: {
				required: true,
				minlength: 2,
				maxlength: 150
			}
		},
		messages: {
			uname: {
				required: "请输入群组名",
				minlength: "名字不少于2个字符",
				maxlength: "名字不超过18个英文字符",
				groupName: "群组名字只能包含大小写英文字母，数字和汉字",
				remote : "此群组名已被使用"
			},
			nickname: {
				required: "请输入群组全名",
				minlength: "全名不少于2个字符",
				maxlength: "不超过8个中文字或24个英文字符"
			},
			cates: {
				required: "请选择目录"
			},
			location: {
				minlength: "地址不少于2个字符",
				maxlength: "地址不超过33个中文字或100个英文字符"
			},
			description: {
				required: "请输入群组简介",
				minlength: "简介不少于2个字符",
				maxlength: "简介不超过50个中文字或150个英文字符"
			}
		}

	});
}