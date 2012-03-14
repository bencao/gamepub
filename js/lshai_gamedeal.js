$(document).ready(function(){
	$('#deals li')
		.find("div.pic a").lightBox({
			imageLoading: '/theme/default/images/lightbox/loading.gif',
			imageBtnClose: '/theme/default/images/lightbox/close.gif',
			imageBtnPrev: '/theme/default/images/lightbox/prev.gif',
			imageBtnNext: '/theme/default/images/lightbox/next.gif',
			imageBlank : '/theme/default/images/lightbox/blank.gif'
		}).end()
		.find("a.close_deal").click(function() {
			if(confirm('确定关闭该交易?')) {
				$.ajax({
					dataType : 'json',
					url : '/ajax/gamedealclose',
					data : {deal_id : $(this).attr('deal_id')},
					success : function(json) {
						if (json.result == 'true') {
							$('#deals li[did="' + json.deal_id + '"] div.status').text('已关闭');
							fadeSuccess("交易已关闭");
						}
					}
				});
			  }
			return false;
		}).end()
		.find("a.totalk").click(function (){
			var html =  '<div class="dialog_body">'+
			  '<form class="msg" action="' + $(this).attr('href') + '" method="post">' +
			  '<fieldset><div class="simple_form"><span>您还可以输入 <em>280</em>个字</span>'+
			  '<textarea name="status_textarea"></textarea></div>'+
			  '<div class="op"><input type="submit" value="发送" class="confirm button60"></input>'+
	          '<a class="cancel button60" href="#">取消</a></div>'+
			  '<input type="hidden" value="' + $('body').attr('token') + '" name="token"></input>'+
			  '<input type="hidden" value="' + $(this).attr('to') + '" name="to"></input>'+
			  '</fieldset></form></div>';
			 
			var arrPageSizes = getPageSize();
			var top = arrPageSizes[3]/3;
			var left = arrPageSizes[2]/3;
			  
			var dlg = $(html).dialog({width : 396, height : 190, draggable : true, resizable : false,
				    title: "对" + $(this).attr('nickname') + "说:", position : [left, top],
				  	close: function(event, ui) {$(this).dialog('destroy').remove();return false;}});
			  
			  $('a.cancel', dlg).click(function() {
				$(this).parents('div.dialog_body').dialog('close');
				return false;
			  });
			  $("form", dlg).ajaxForm({
				  dataType : 'json',
					beforeSubmit : function(formData, jqForm, options) {
						if ($("textarea", jqForm).val().length == 0) {
							alertFail("请输入您想对好友说的悄悄话");
							return false;
						} else if ($("textarea", jqForm).val().length > 280) {
							alertFail("您输入的字数超过了280个字");
							return false;
						} 
						if (jqForm.hasClass('processing')) {
							return false;
						}
						jqForm.addClass("processing");
						$("input.confirm", jqForm).attr("disabled", "disabled").addClass("disabled");
						return true;
					},
					success: function(json) {
						if (json.html) {
							$("div.dialog_body").dialog('close');
							fadeSuccess('悄悄话发送成功');
						}
					}
			  }).each(addAjaxHidden);
			  $("form textarea", dlg)
				.bind("keyup paste", function() {
					counterArray(this, $("span em", $(this).parent()), 6);
				})
				.bind("keydown", textareaEventHandler).focus();
			  $(this).parents('.more').hide();
			  return false;
		});

	$('form select.choosegame').change(updateGame);
	
	function updateGame() {
		$.ajax({
			dataType : 'json',
			url : '/ajax/getdtbygame',
			data : {gid : $(this).val()},
			success : function(json) {
				var html = '<option value="">全部分类</option>';
				for (var i = 0; i < json.length; i ++) {
					html += '<option value="' + json[i].id + '">' + json[i].name + '</option>';
				}
				$('form select.choosecategory').html(html);
			}
		});
	}
	
	$("div.dealnav ul a").click(function() {
		$('#dealoption input[name="deal_tag"]').val($(this).attr('type')).parents('form').submit();
		return false;
	});
	
	$('#dealoption')
		.find('span.disp a').click(function() {
			$(this).parents('form')
				.find('input[name="deal_per_page"]').val($(this).attr('pagesize')).end()
				.find('input[name="page"]').val('1').end().submit();
			return false;
		});
	
	$("#pagination a").click(function() {
		$("form.dealoption").find('input[name="page"]').val($(this).attr('page')).end().submit();
		return false;
	});
	
	$('div.dealnav a.tosell').click(function() {
		 if (window.selling) {
			  return false;
		  }
		  window.selling = true;
		  
		  var sellinputer =
			  '<div class="dialog_body"><form class="tosell" action="' + $(this).attr('href') + '" method="post">' +
			  '<fieldset><legend>发布出售信息</legend>' + 
			  '<p style="width:auto;">' + 
			  '<select class="choosegame">' + $('#dealsearch select.choosegame').html() + '</select>' +
			  '<select class="choosecategory" name="deal_tag">' + $('#dealsearch select.choosecategory').html() + '</select>' +
			  '<select class="choosebigzone">' + $('#dealsearch select.choosebigzone').html() + '</select>' +
			  '<select class="chooseserver" name="game_server">' + $('#dealsearch select.chooseserver').html() + '</select>' +
			  '</p><dl class="inputs clearfix">'+
//			  '<dt><label>商品标题：</label></dt><dd><input class="text300" type="text" name="deal_title"></input></dd>'+
//			  '<dt><label>商品链接：</label></dt><dd><input class="text300" type="text" name="deal_link"></input></dd>'+
			  '<dt><label>商品价格：</label></dt><dd><input class="text74" type="text" name="price"></input>元</dd>'+
			  '<dt><lable>过期时间：</lable></dt><dd><select class="expiretime" name="expire_time">'+
			  '<option value="7">一周</option><option value="15">半个月</option><option value="30">一个月</option></select>'+
			  '<dt><label>商品描述：</label></dt><dd><textarea class="textarea376" type="textarea" name="description"></textarea></dd>'+ 
			  '<dt><label>插入图片：</label></dt><dd><input type="file" name="picfile" id="picfile"></input>'+
			  '<div id="fileQueue"></div></dd></dl>'+
			  '<p class="msg" style="display:none;"></p>'+
			  '<input type="hidden" value="' + $('body').attr('token') + '" name="token"></input>'+
			  '<input type="hidden" value="' + $(this).attr('uid') + '" name="uid"></input>'+
			  '<input type="hidden" value="" name="picfilename" id="picfilename"></input>'+
			  '<div class="op"><input class="submit orange94 button94 aligncenter" type="submit" value="发布商品"></input></div></fieldset></form></div>';
		  
		  var arrPageSizes = getPageSize();
		  var top = arrPageSizes[3]/3;
		  var left = arrPageSizes[2]/3;
		  var height = 370;
		  var selldlg = $(sellinputer).dialog({width : 500, height : height, title: "出售商品", position: [left, top], draggable : true, resizable : false, 
			  	close: function(event, ui) {$(this).dialog('destroy').remove();window.selling = false;return false;}});
		  
		  $("form.tosell").ajaxForm({
			 dataType : 'json',
				data : {ajax : 1},
				beforeSubmit: function(formData, jqForm, options) {
					if (jqForm.hasClass('processing')) {
						return false;
					}
					jqForm.addClass("processing");
					$("input[type=submit]", jqForm).attr("disabled", "disabled").addClass("disabled");
					$('form.tosell p.msg').hide();
					return true;
				},
				error : function() {
					alertFail('发送出售信息错误，请您再试一次!');
					$('form.tosell').removeClass('processing')
						.find("input[type=submit]").removeAttr("disabled").removeClass("disabled");
					window.selling = false;
				},
				success: function(json) {
					var $dialog = $("div.dialog_body");
					if (json.result == 'true') {
						$dialog.dialog('close');
						window.selling = false;
						fadeSuccess("已添加商品", '提示', function() {
							Helper.refresh();
						});
					} else {
						$('form.tosell p.msg').text(json.msg).show();
					}
					$('form.tosell').removeClass('processing')
						.find("input[type=submit]").removeAttr("disabled").removeClass("disabled");
				}
		 }).each(addAjaxHidden);
		  
		 selldlg
			.find('select.choosegame').change(function() {
				updateTosellGame($(this).val());
			}).end()
			.find('select.choosebigzone').change(function() {
				updateTosellBigzone($(this).val());
			});
		
		function updateTosellBigzone(bzid) {
			$.ajax({
				type: "get",
				url: '/ajax/getserversbybzid', 
			    data: {bid : bzid}, 
				dataType: "json",
				success: function(json) {
					var html = '<option value="">选择服务器</option>';
					for (var i = 0; i < json.length; i ++) {
						html += '<option value="' + json[i].id + '" class="exist">' + json[i].name + '</option>';
					}
					$('form.tosell select.chooseserver').html(html);
			    }
			});
		}
		
		function updateTosellGame(gid) {
			$("select.chooseserver").html('<option value="">先选择大区</option>');
			$.ajax({
				dataType : 'json',
				url : '/ajax/getdtbygame',
				data : {gid : gid},
				success : function(json) {
					var html = '<option value="">全部分类</option>';
					for (var i = 0; i < json.length; i ++) {
						html += '<option value="' + json[i].id + '">' + json[i].name + '</option>';
					}
					$('form.tosell select.choosecategory').html(html);
				}
			});
			$.ajax({
				dataType : 'json',
				url : '/ajax/getbzsbygame',
				data : {gid : gid},
				success : function(json) {
					var html = '<option value="">选择大区</option>';
					for (var i = 0; i < json.length; i ++) {
						html += '<option value="' + json[i].id + '">' + json[i].name + '</option>';
					}
					$('form.tosell select.choosebigzone').html(html);
				}
			});
		}
		  
		 $("#picfile").uploadify({
			'uploader'       : '/js/uploadify.swf',
			'script'         : '/ajax/uploadfile?uid=' + $("form.tosell").find('input[name="uid"]').val(),
			'cancelImg'      : '/theme/default/images/uploadify/cancel.png',
			'buttonImg'      : '/theme/default/images/uploadify/upload.jpg',
			'width'          : 60,
			'height'         : 23,
			'folder'         : '/file/tmp',
			'queueID'        : 'fileQueue',
			'auto'           : true,
			'multi'          : false,
			'buttonText'     : 'Upload',
			'fileDesc'       : 'JPG/PNG/GIF文件',
			'fileExt'        : '*.jpg;*.png;*.jpeg;*.gif',
			'sizeLimit'      : '1048576',
			'onError'        : function(event, queueID, fileObj, errorObj) {
				if (errorObj.type == 'File Size') {
					alert('您选择的文件太大，不能超过1MB');
				};
				return false;
			},
			'onComplete'     : function(event, queueID, fileObj, response, data) {
				$("#picfilename").val(response);
				$('#picfileUploader').remove();
				$('#picfile').replaceWith('<span>已上传</span>');
			}
		});
		return false;
	});
});