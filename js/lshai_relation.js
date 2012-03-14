$(document).ready(function() {	
	var subscribeOptions = { 
		dataType: 'json',
		beforeSubmit: function(formData, jqForm, options) {
			if (jqForm.hasClass('processing')) {
				return false;
			}
			jqForm.addClass("processing");
			$("input[type=submit]", jqForm).attr("disabled", "disabled").addClass("disabled");
		},
		error : function() {
			alertFail('发生未知错误，关注失败');
		},
		success: function(json) {
			var subinfo =  	$("div.sub_info", $my.widgets);
			if (subinfo && subinfo.hasClass('isown')) {
	  	   		$("a.subscription span", subinfo).text(parseInt($("a.subscription span", subinfo).text()) + 1);
			} else {
				$("a.subscriber span", subinfo).text(parseInt($("a.subscriber span", subinfo).text()) + 1);
			}
			
	  	   	var container = $("#owner_summary, li[pid='" + json.pid + "'], div.card div.op");
			$("form.subscribe", container).replaceWith('<div class="subscribed">已关注</div>');
			
			$("div.op ul.more", container).prepend('<li><a class="unsubscribe" url="' + json.action + '" token="' + $('body').attr('token') + '" to="' 
					+ json.pid + '" href="#">取消关注</a></li>')
			
			$("a.unsubscribe", container).click(function() { 
				ajaxUnsubscribe($(this).attr('url'), $(this).attr('to'));
				$(this).parents('.more').hide();
				return false; 
			});
			
			ajaxTagOther(json.tagotherurl, json.pid, '1');
		}
	};
	
	function ajaxUnsubscribe(url, to) {
		$.ajax({
			type : 'POST',
			url : url,
			data : {to : to, token : $('body').attr('token'), ajax : '1'},
			dataType: 'json',
			success: function(json) {
				var subinfo =  	$("div.sub_info", $my.widgets);
				if (subinfo.hasClass('isown')) {
		  	   		$("a.subscription span", subinfo).text(parseInt($("a.subscription span", subinfo).text()) - 1);
				} else {
					$("a.subscriber span", subinfo).text(parseInt($("a.subscriber span", subinfo).text()) - 1);
				}
				
				var container = $("#users li[pid='" + json.pid + "'], #owner_summary");
				$("a.unsubscribe", container).parent().remove();
				$("div.op div.done, div.subscribed", container).remove();
				$("#owner_summary").prepend('<form class="subscribe" action="' + json.action + '" method="post"><fieldset><legend>关注</legend>' 
						+ '<input type="hidden" name="token" value="' + $('body').attr('token') + '" /><input type="hidden" name="subscribeto" value="' 
						+ json.pid + '" /><input type="submit" value="关注" class="submit button94 orange94" /></fieldset></form>');

				$("#users li[pid='" + json.pid + "'] div.op").prepend('<form class="subscribe" action="' + json.action + '" method="post"><fieldset><legend>关注</legend>' 
						+ '<input type="hidden" name="token" value="' + $('body').attr('token') + '" /><input type="hidden" name="subscribeto" value="' 
						+ json.pid + '" /><input type="submit" value="关注" class="submit button76 orange76" /></fieldset></form>');
				
				$("form.subscribe", container).ajaxForm(subscribeOptions).each(addAjaxHidden);
				
				fadeSuccess("取消关注成功");
			}
		});
	}
	
	function ajaxBlock(url, to) {
		$.ajax({
			type : 'POST',
			url : url,
			data : {to : to, token : $('body').attr('token'), ajax : '1'},
			dataType: 'json',
			success: function(json) {
				var op = $("#users li[pid='" + json.pid + "'] div.op, #owner_summary div.op");
				$("a.block", op).parent().html('<a class="unblock" url="' + json.action + '" token="' + $('body').attr('token') + '" to="' 
						+ json.pid + '" href="#">取消黑名</a>');
				
				$("a.unblock", op).click(function() {
					ajaxUnblock($(this).attr('url'), $(this).attr('to'), $('body').attr('token'));
					$(this).parents('.more').hide();
					return false;
				});
				
				fadeSuccess("添加黑名单成功");
			}
		});
	}
	
	function ajaxUnblock(url, to) {
		$.ajax({
			type : 'POST',
			url : url,
			data : {to : to, token : $('body').attr('token'), ajax : '1'},
			dataType: 'json',
			success: function(json) {
				var op = $("#users li[pid='" + json.pid + "'] div.op, #owner_summary div.op");
				$("a.unblock", op).parent().html('<a class="block" url="' + json.action + '" token="' + $('body').attr('token') + '" to="' 
						+ json.pid + '" href="#">黑名单</a>')
				
				$("a.block", op).click(function() {
					ajaxBlock($(this).attr('url'), $(this).attr('to'), $('body').attr('token'));
					$(this).parents('.more').hide();
					return false;
				});
				
				fadeSuccess("取消黑名单成功");
			}
		});
	}
	
	
	$("form.subscribe").ajaxForm(subscribeOptions).each(addAjaxHidden);
	$("a.unsubscribe").click(function() {
		ajaxUnsubscribe($(this).attr('url'), $(this).attr('to'));
		$(this).parents('.more').hide();
		$('div.player embed').removeAttr('style');
		return false;
	});
	$("a.block").click(function() {
		ajaxBlock($(this).attr('url'), $(this).attr('to'));
		$(this).parents('.more').hide();
		$('div.player embed').removeAttr('style');
		return false;
	});
	$("a.unblock").click(function() {
		ajaxUnblock($(this).attr('url'), $(this).attr('to'));
		$(this).parents('.more').hide();
		$('div.player embed').removeAttr('style');
		return false;
	});
	
	//悄悄话
	$("a.msg").click(function (){
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
		  
		$('div.player embed').css({width: '0'});
		  var dlg = $(html).dialog({width : 396, height : 190, draggable : true, resizable : false,
			    title: "对" + $(this).attr('nickname') + "说:", position : [left, top],
			  	close: function(event, ui) {$('div.player embed').removeAttr('style');$(this).dialog('destroy').remove();return false;}});
		  
		  $('a.cancel', dlg).click(function() {
			$(this).parents('div.dialog_body').dialog('close');
			return false;
		  });
		  $("form", dlg).ajaxForm(messageOptions).each(addAjaxHidden);
		  $("form textarea", dlg)
			.bind("keyup paste", function() {
				counterArray(this, $("span em", $(this).parent()), 6);
			})
			.bind("keydown", textareaEventHandler).focus();
		  $(this).parents('.more').hide();
		  return false;
	});
	
	var messageOptions = {
		dataType : 'json',
		beforeSubmit : function(formData, jqForm, options) {
			if ($("textarea", jqForm).val().length == 0) {
				jqForm.addClass("warning");
				alertFail("请输入您想对好友说的悄悄话");
				return false;
			} else if ($("textarea", jqForm).val().length > 280) {
				jqForm.addClass("warning");
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
	};
	
	//对他说
    $("a.at").click(function() {
  	  	var div = '<div class="dialog_body">'+
  				  '<form class="atta" action="' + $(this).attr('href') + '" method="post">' +
  				  '<fieldset><div class="simple_form"><a href="#" class="emotion">插入表情</a><span>您还可以输入 <em>280</em>个字</span>'+
  				  '<textarea name="status_textarea"></textarea></div>'+
  				  '<div class="option"><input type="checkbox" class="checkbox" name="replyinbox" value="1" id="addInbox" />'+
  				  '<label for="addInbox">作为一条新消息</label></div>'+
  				  '<div class="op"><input type="submit" value="发送" class="confirm button60"></input>'+
  		          '<a class="cancel button60" href="#">取消</a></div>'+
  				  '<input type="hidden" value="' + $('body').attr('token') + '" name="token"></input>'+
  				  '<input type="hidden" value="1" name="newreply"></input>'+
  				  '</fieldset></form></div>';
  		      
	  	  var arrPageSizes = getPageSize();
		  var top = arrPageSizes[3]/3;
		  var left = arrPageSizes[2]/3;
		  
		  $('div.player embed').css({width: '0'});
	  	  var replydlg = $(div).dialog({width : 396, height : 210, draggable : true, resizable : false,
	  		  title: "@" + $(this).attr('nickname'), position: [left, top], 
	  		  close: function(event, ui) {$('div.player embed').removeAttr('style');$(this).dialog('destroy').remove();return false;} });
	  	  
	  	  //处理消息, 及发送消息两块, reply及retweet都可以处理
	  	  $('a.emotion', replydlg).click(function(event) {
	  		  newEmotionDialog('retweet_emotion', $(this).parents('form').find('textarea'), {left : event.pageX, top : event.pageY});
	  		  return false;
	  	  });
	  	  $('a.cancel', replydlg).click(function() {
	  			$(this).parents('div.dialog_body').dialog('close');
	  			return false;
	  	  });
	
	  	  $("form", replydlg).ajaxForm(newReplyOptions).each(addAjaxHidden);
	  	  
	  	  $("form textarea", replydlg)
	  		.bind("keyup paste", function() {
	  			counterArray(this, $("span em", $(this).parent()), 5);
	  		})
	  		.bind("keydown", textareaEventHandler).focus();
	  	  
	  	$(this).parents('.more').hide();
	  	return false;
    });
	
	$("a.illegal, a.report").click(function() {
		var html = '<div class="dialog_body" title="非法举报">'
			+ '<form id="illegalreport"  action="' + $(this).attr('url') + '" method="post"><fieldset><legend>举报</legend>' 
			+ '<dl class="inputs clearfix"><dt>举报原因:</dt><dd>' 
			+ '<select name="reason" id="reason" style="padding-top:2px;"><option value="">请选择</option><option value="1">内容反动</option>' 
			+ '<option value="2">内容色情</option><option value="3">骚扰诈骗</option><option value="4">张贴广告</option>' 
			+ '<option value="5">滥发垃圾信息</option></select></dd><dt style="clear:both;">附加说明:</dt>' 
			+ '<dd><textarea name="description" class="textarea280"></textarea></dd></dl>' 
			+ '<input type="hidden" name="illtype" value="1" /><input type="hidden" name="targetid" value="' + $(this).attr('to') + '" />' 
			+ '<input type="hidden" name="from_url" value="' + window.location.href + '" />'
			+ '<div class="op"><input type="submit" value="确定" class="confirm button60" />'
			+ '<a class="cancel button60" href="#">取消</a>'
			+ '<input type="hidden" value="' + $('body').attr('token') + '" name="token" />'
			+ '</div></fieldset></form></div>';
		var arrPageSizes = getPageSize();
		var top = arrPageSizes[3]/3;
		var left = arrPageSizes[2]/3;
		  
		$('div.player embed').css({width: '0'});
		var illegaldlg =$(html).dialog({width : 397, height : 190, position : [left, top], draggable : true, resizable : false,
			close: function(event, ui) {$('div.player embed').removeAttr('style');$(this).dialog('destroy').remove();return false;}});
		$("a.cancel", illegaldlg).click( function() {
		  	$(this).parents('div.dialog_body').dialog('close');
		  	return false;
		});
		$("form", illegaldlg).ajaxForm(ajaxIllegal).each(addAjaxHidden);
		
		$(this).parents('.more').hide();
		return false;
	});
	
	var ajaxIllegal = { 
			dataType: 'json',
			beforeSubmit: function(formData, jqForm, options) { 
				if ($("select", jqForm).val().length == 0) {
					jqForm.addClass("warning");
					alertFail("请选择举报原因");
					return false;
				}
				if ($("textarea", jqForm).val().length == 0) {
					jqForm.addClass("warning");
					alertFail("请填写描述");
					return false;
				}
				if (jqForm.hasClass('processing')) {
					return false;
				}
				jqForm.addClass("processing");
				$("input.confirm", jqForm).attr("disabled", "disabled").addClass("disabled");
				return true;
			},
			timeout: '60000',
			error: function (xhr, textStatus, errorThrown) {	
				$("#illegalreport").removeClass("processing");
				if (textStatus == "timeout") {
					alertFail("对不起， 举报未能成功， 请稍后再试");
				}
			},
			success: function(json) {
				if(json.result=="successful"){
				    fadeSuccess("举报提交成功，谢谢！");
				}
				$("div.dialog_body").dialog('close');
			}
	};

	function ajaxTagOther(url, to, issub) {
		$.ajax({
			type : 'GET',
			url : url,
			data : {to : to, ajax : '1', issub : issub},
			dataType: 'json',
			success: function(json) {
				var dialogContent = json.html;
				var arrPageSizes = getPageSize();
				var top = arrPageSizes[3]/3;
				var left = arrPageSizes[2]/3;
				$('div.player embed').css({width: '0'});
				$(dialogContent).dialog({title : "修改用户分组", width: 397, position : [left, top], draggable : true, resizable : false,
					close: function(event, ui) {$('div.player embed').removeAttr('style');$(this).dialog('destroy').remove();return false;}});
				
				$('a.create').click(function() {
					$(this).hide();
					$('div.create_new').show();
					return false;
				});
				$('div.create_new a.tocreate').click(function() {
					var self = $(this);
					var parent = self.parent();
					$.ajax({
						type : 'POST',
						url : self.attr('url'),
						dataType : 'json',
						data : {tname : $('input[name="tname"]', parent).val(), token : $('body').attr('token'), create : '1'},
						success : function(json) {
							if (json.result == 'true') {
								$('ul.checkboxes').append('<li><input type="checkbox" class="checkbox" id="newtag' + json.tid + '" value="' + json.tid + '" checked="checked" /><label for="newtag' + json.tid + '">' + json.tag + '</label></li>');
								$('div.create_new').hide();
								$('a.create').show();
							} else {
								alertFail(json.msg);
							}
						}
					});
					return false;
				});
				$('div.create_new a.tocancel').click(function() {
					$('div.create_new').hide();
					$('a.create').show();
					return false;
				});
				
				$('div.dialog_body a.cancel').click(function() {
					var dialog_body = $(this).parents('div.dialog_body');
					if ($('input[name="issub"]', dialog_body).val() == '1') {fadeSuccess('关注成功');}
					dialog_body.dialog('close');
					return false;
				});
				
				$('form.tagother').ajaxForm({
					dataType : 'json',
					beforeSubmit: function(formData, jqForm, options) {
						if (jqForm.hasClass('processing')) {
							return false;
						}
						jqForm.addClass("processing");
						$("input.confirm", jqForm).attr("disabled", "disabled").addClass("disabled");
						return true;
					},
					success : function(json) {
						if (json.result == 'true') {
							$('#users li[pid="' + json.to + '"] p.pgroup').text('所属分组: ' + json.tags);
							
							var form = $("#tagotherfor" + json.to);
							form.removeClass("processing").parents('div.dialog_body').dialog('close');
							
							fadeSuccess(json.msg);
						} else {
							alertFail('修改分组的时候出错了');
						}
					}
				});
			}
		});	
	}
	
	$("a.tagother").click(function() {
		// 获取已有分组
		ajaxTagOther($(this).attr('url'), $(this).attr('to'), '0');
		$(this).parents('.more').hide();
		return false;
	});
});
