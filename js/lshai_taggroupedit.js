$(window).load(function() {	
	$("#sub_op a.edit").click(function() {
		if (window.renamingtag) {
			return false;
		}
		window.renamingtag = true;
		
		var dialogBody = '<div class="dialog_body" title="重命名分组">' 
			+ '<p>请输入分组的新名字，长度在1~8个字之间:</p>'
			+ '<div class="create_new"><input type="text" class="text200" name="tname" />' 
			+ '<a href="' + $(this).attr('href') + '" token="' + $('body').attr('token') + '" tid="' + $(this).attr('tid') + '" class="toedit">确定</a>' 
			+ '<a href="#" class="tocancel">取消</a></div></div>';
	
		var arrPageSizes = getPageSize();
		var top = arrPageSizes[3]/3;
		var left = arrPageSizes[2]/3;
		  
		var ctDlg = $(dialogBody).dialog({width:396, position: [left, top], draggable : true, resizable : false,
			close: function(event, ui) {$(this).dialog('destroy').remove();window.renamingtag = false;return false;}});
		$("a.toedit", ctDlg).click(function() {
			var self = $(this);
			var parent = self.parent();
			$.ajax({
				type : 'POST',
				url : self.attr('href'),
				dataType : 'json',
				data : {tname : $('input[name="tname"]', parent).val(), tid : self.attr('tid'), token : $('body').attr('token'), edit : '1'},
				success : function(json) {
					if (json.result == 'true') {
						$('div.dialog_body').dialog('close');
						alertSuccess('重命名分组成功', '操作成功', function() {
							window.location = $("#w_nav li:first a").attr('href') + '?gtag=' + json.tag;
						});
					} else {
						alertFail(json.msg);
					}
				}
					
			});
			return false;
		});
		$("a.tocancel", ctDlg).click(function() {
			$(this).parents('div.dialog_body').dialog('close');
			return false;
		});
		$('input.text200', ctDlg).focus();
		return false;
	});
	
	$("#sub_op a.add").click(function() {
		if (window.addingtag) {
			return false;
		}
		window.addingtag = true;
		$.ajax({
			type : 'GET',
			url : '/ajax/getsubscription',
			dataType : 'json',
			data : {tname : $(this).attr('gtag'), tid : $(this).attr('tid')},
			error : function() {
				alert('error');
			},
			success : function(json) {
				var dialogBody = '<div class="dialog_body" title="添加关注的人至本组">'
					+ '<form action="/ajax/edittaggroup" method="post" class="addall"><fieldset><legend>添加关注的人至本分组</legend><div class="ul_wrap"><ul>'; 
				for (var i = 0; i < json.item.length; i ++) {
					dialogBody += '<li><div class="avatar"><a href="#"><img src="' + json.item[i].avatar + '" alt="' + json.item[i].nickname + '"/></a></div><input type="checkbox" class="checkbox" name="sus[]" value="' + json.item[i].uid + '" /><a href="#" class="nickname">' + json.item[i].nickname + '</a></li>';
				}
				dialogBody += '</ul></div><div class="op"><input class="confirm button60" type="submit" value="确定" /><a href="#" class="cancel button60">取消</a></div>';
				dialogBody += '<input type="hidden" name="addall" value="t"/><input type="hidden" name="token" value="' + $('body').attr('token') 
					+ '" /><input type="hidden" name="tid" value="' + json.tid + '" /></fieldset></form></div>';
				
				var arrPageSizes = getPageSize();
				var top = arrPageSizes[3]/3;
				var left = arrPageSizes[2]/3;
				  
				var ctDlg = $(dialogBody).dialog({width:434, position: [left, top], draggable : true, resizable : false,
					close: function(event, ui) {$(this).dialog('destroy').remove();window.addingtag = false;return false;}});
				
				$("a.cancel", ctDlg).click(function() {
					$(this).parents('div.dialog_body').dialog('close');
					return false;
				});
				
				$('div.avatar a, a.nickname', ctDlg).click(function() {
					var $pli = $(this).parents('li');
					var checkbox = $pli.find('input.checkbox').get(0);
					if (checkbox.checked) {
						checkbox.checked = false;
						$pli.removeClass('active');
					} else {
						checkbox.checked = true;
						$pli.addClass('active');
					}
					return false;
				});
				
				$('input.checkbox', ctDlg).click(function() {
					var $pli = $(this).parents('li');
					if (this.checked) {
						$pli.addClass('active');
					} else {
						$pli.removeClass('active');
					}
				});
				
				$('form', ctDlg).ajaxForm({
					dataType : 'json',
					error : function() {
						alertFail('添加成员至分组时出错，请稍候再试');
						window.addingtag = false;
					},
					success : function(json) {
						if (json.result == 'true') {
							alertSuccess('添加成员至分组成功', '操作成功', function() {
								window.location = $("#w_nav li:first a").attr('href') + '?gtag=' + json.tag;
							});
						} else {
							alertFail('添加成员至分组的时候出错了');
						}
						window.addingtag = false;
					}
				}).each(addAjaxHidden);
			}
		});
		return false;
	});
	
	$("#sub_op a.delete").click(function() {
		if (window.deletingtag) {
			return false;
		}
		window.deletingtag = true;
		
		if (confirm("确定删除该分组?")) {
			var self = $(this);
			var parent = self.parent();
			
			$.ajax({
				type : 'POST',
				url : self.attr('href'),
				dataType : 'json',
				data : {tid : self.attr('tid'), token : $('body').attr('token'), del : '1'},
				success : function(json) {
					if (json.result == 'true') {
						alertSuccess('删除分组成功', '操作成功', function() {
							window.location = $("#w_nav li:first a").attr('href');
						});
					} else {
						alertFail('删除分组的时候出错了');
					}
					window.deletingtag = false;
				}
			});
		} else {
			window.deletingtag = false;
		}
		return false;
	});
	
	$("#w_nav_create a").click(function () {
		if (window.creatingtag) {
			return false;
		}
		window.creatingtag = true;
		
		var dialogBody = '<div class="dialog_body" title="创建分组">' 
				+ '<p>请输入新分组的名字，长度在1~8个字之间:</p>'
				+ '<div class="create_new"><input type="text" class="text200" name="tname" />' 
				+ '<a href="' + $(this).attr('href') + '" token="' + $('body').attr('token') + '" class="tocreate">创建</a>' 
				+ '<a href="#" class="tocancel">取消</a></div></div>';
		
		var arrPageSizes = getPageSize();
		var top = arrPageSizes[3]/3;
		var left = arrPageSizes[2]/3;
		  
		var ctDlg = $(dialogBody).dialog({width:396, position: [left, top], draggable : true, resizable : false,
			close: function(event, ui) {$(this).dialog('destroy').remove();window.creatingtag = false;return false;}});
		$("a.tocreate", ctDlg).click(function() {
			var self = $(this);
			var parent = self.parent();
			$.ajax({
				type : 'POST',
				url : self.attr('href'),
				dataType : 'json',
				data : {tname : $('input[name="tname"]', parent).val(), token : $('body').attr('token'), create : '1'},
				success : function(json) {
					if (json.result == 'true') {
						var location = $("#w_nav li:first a").attr('href');
						$("#w_nav").append('<li><span></span><a href="' + location + '?gtag=' + json.tag + '">' + json.tag + '</a></li>');
						if ($.browser.msie && parseInt($.browser.version) <= 6) {
							DD_belatedPNG.fixPng($('#w_nav li:last').get('0'));
						}
						effectsForNavHovers();
						$("div.dialog_body").dialog('close');
						fadeSuccess('添加分组成功');
					} else {
						alertFail(json.msg);
					}
				}
					
			});
			return false;
		});
		$("a.tocancel", ctDlg).click(function() {
			$(this).parents('div.dialog_body').dialog('close');
			return false;
		});
		$('input.text200', ctDlg).focus();
		return false;
	});
});