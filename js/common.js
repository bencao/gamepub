$.ajaxSetup({
	type : 'get',
	data : {ajax : '1'},
	error : ajaxErrorHandler
});
function textareaEventHandler(event) {
	if (event.ctrlKey && event.which == 13) {
		$(this).parents("form").submit(); 
		event.preventDefault();
		event.stopPropagation();
	}
	if (event.which == 27) {
		$(this).blur();
	}
}

function ajaxErrorHandler(request, status, errorThrown) {
	if (status == 'error') {
		var errorObj = jQuery.parseJSON(request.responseText);
		alertFail(errorObj.error);
	} else if (status == 'parsererror') {
//		alertFail('网站出现问题，请描述您遇到的情况，将反馈发给小酒保，我们将即刻改进，谢谢！');
	} else if (status == 'timeout') {
		alertFail('请求超时，可能是服务器压力过大，请几分钟后再试。');
	}
}

function processNotice() {
	var $textArea = $my.notice_textarea;
	if ($textArea.length) {
		$textArea
			.unbind("keyup paste").bind("keyup paste", function(){counterArray($textArea, $my.notice_text_count, 0)})
			.unbind("keydown").bind("keydown", textareaEventHandler).keyup().focus().each(function() {
				selectEnd(this);
			});
	}
}

function insertTopic() {
	var $textArea = $my.notice_textarea;
	var value = $textArea.val();
	var $first_tag = $(this).closest('li.first_tag');
	var tag = $first_tag.attr('tag');
	var $notice_form = $my.notice_form;
	
	if($first_tag.hasClass('added') && value.match('[' + tag + ']')) {
		if (!confirm("您已经选择了此目录下的话题, 需要替换原有话题吗?")){ 
			return false;
		} else {			
			value = value.replace('[' + tag +']', '[' + $(this).text() +']');	
			$textArea.val(value.trim());
		}
	} else if($first_tag.hasClass('added')) { 
		$textArea.insertAtCaret('[' + $(this).text()+ ']' );
	} else {
		$textArea.insertAtCaret('[' + $(this).text()+ ']' );
		$first_tag.addClass('added');
	}	
	
	$("li.first_tag", $notice_form).removeClass("on").removeClass("active");
	$("li.self_define", $notice_form).removeClass("on").removeClass("active");
	$first_tag.addClass("active").attr('tag', $(this).text()).children('ul').hide();
	
	counterArray($textArea, $my.notice_text_count, 0);
	return false;
}

function insertTopicDefine() {
	var $textArea = $my.notice_textarea;
	var value = $textArea.val();		
	var tagvalue = '[' + $(this).text() + ']';	//[自定义]
	var $notice_form = $my.notice_form;
	var $self_define = $(this).closest('li.self_define');
	
	$textArea.insertAtCaret(tagvalue);
	counterArray($textArea, $my.notice_text_count, 0);
	
	$("li.first_tag", $notice_form).removeClass("on").removeClass("active");
	$("li.self_define", $notice_form).addClass("active");
	
	if($textArea.val().match(tagvalue)) {
		$textArea.selectContent($(this).text());
	}
	
	return false;
}

function noticeDelete() {
	if (window.deletingnotice) {
		return false;
	}
	window.deletingnotice = true;
	
	if (confirm("确认删除此消息吗?")){ 
		$.ajax({
		  type: "post",
		  url: $(this).attr("href"),
		  dataType: "json",
		  data: {nid: $(this).attr('nid'), ajax: 1, 
				 token: $('body').attr('token')
		  }, 
		  success: function(json){
			if (json.result == 'true') {
				var $notice = $("#notice-" + json.deleted);
				if($notice.length > 0) {
					$notice.fadeOut("slow").remove();
				}
				fadeSuccess("删除成功");
			} else {
				if (json.msg) {
					alertFail(json.msg);
				} else {
					alertFail('删除失败');
				}
			}
			window.deletingnotice = false;
		  }
		});
	} else {
		window.deletingnotice = false;
	}
	return false;
}

var favoptions = { 
	dataType: 'json',
	beforeSubmit: function(arr, jqForm) {
		var name = $('#favorgroup').val();
		if(/^(　|\s)*$/.test(name) == true ){
			alertFail('请选择或填写收藏夹名称');
			return false;
		} else if(name.length > 6){
			alertFail('收藏夹名称请控制在6个字以内');
			return false;
		}
		if (jqForm.hasClass('processing')) {
			return false;
		}
		jqForm.addClass("processing");
		$("input.confirm", jqForm).attr("disabled", "disabled").addClass("disabled");
	},
	success: function(json) { 
		var new_link = $(json.html).get(0);
		var dis = new_link.id;
		var fav = dis.replace('disfavor', 'favor');
		$('#'+fav).replaceWith(new_link);
				
		$('a', $('#'+dis)).click(noticeDisfavor);

		$("div.dialog_body").dialog('close');
		fadeSuccess("收藏成功");
	},
	complete : function(r, s) {
		$('#favorgroup').removeClass('processing');
		$("div.dialog_body input.confirm").removeAttr('disabled').removeClass('disabled');
	}
};

function noticeFavor() {
	if (window.favoring) {
		return false;
	}
	window.favoring = true;
	$.ajax({
	  type: "GET",
	  url: $(this).attr('href'),
	  data: {nid: $(this).attr('nid'), token: $('body').attr('token'), ajax : 1},
	  dataType: "json",
	  success: function(json){
		var dialogContent = $(json.html).get(0);
		
		var arrPageSizes = getPageSize();
		var top = arrPageSizes[3]/3;
		var left = arrPageSizes[2]/3;
		
		var favdlg = $(dialogContent);
		var height = 170 + favdlg.find('p').text().length/28*18;
		
		$('div.player embed, div.player object').css({width: '0'});
		favdlg.dialog({width : 397, height: height, title: '收藏消息', position : [left, top], draggable : true, resizable : false,
			close: function(event, ui) {$('div.player embed, div.player object').removeAttr('style');$(this).dialog('destroy').remove();window.favoring = false;return false;}});
		
		$('a.cancel', favdlg).click(function() {
			$(this).parents('div.dialog_body').dialog('close');
			return false;
		});
		
		$("form", favdlg).ajaxForm(favoptions).each(addAjaxHidden);	
		
		$("a.show_dropdown", favdlg).click(function(){
			$('#favorselect', $(this).parent()).toggle();
			return false;
		});
		
		$("#favorselect a", favdlg).click(function(){
			var value = $(this).attr('fid');
			var txt = $(this).text();
			if(value == 'sep' || value == 'new') {
				txt = "";
			}
		   $('#favorgroup').val(txt).focus();
		   $('#favorselect').hide();
		   return false;
		});
		
	  }
	});
	return false;
}

function noticeDisfavor() {
	if (window.disfavoring) {
		return false;
	}
	window.disfavoring = true;
	
	if(confirm('确定取消收藏?')) {
		$.ajax({
		  type: "post",
		  url: $(this).attr('href'),
		  data: {nid: $(this).attr('nid'), ajax: 1, token : $('body').attr('token')}, 
		  dataType: "json",
		  success: function(json){
			var new_link = $(json.html).get(0);
			var fav = new_link.id;			
			var noticeid = fav.substring("notice_favor-".length);
			var $notice = $("#notice-"+noticeid);
			
			if((document.URL).search(/\/[a-zA-Z0-9]+\/favorites/i) != -1) { 
				if($notice.length > 0) {
					$notice.fadeOut("slow").remove();
				}
			} else {				
				var dis = fav.replace('favor', 'disfavor');
				$('#'+dis).replaceWith(new_link);
				$('a', $('#'+fav)).click(noticeFavor);
			}
			
			fadeSuccess("取消收藏成功");
			window.disfavoring = false;
		  }
		});
	}
	return false;
}

function addAjaxHidden() {
	$('fieldset', this).append('<input type="hidden" name="ajax" value="1" />');
}

function validateInput() {
	var content = $my.notice_textarea.get(0).value.trim();
	var $notice_form = $my.notice_form;
	var notice_form2 = document.getElementById("notice_data-text");
	if (content == "") {
		$notice_form.addClass("warning");
		alertFail("请输入您想说的话");
		selectEnd(notice_form2);
		return false;
	} else if(content.length > 280) {
		$notice_form.addClass("warning");
		selectEnd(notice_form2);
		alertFail("您输入的字数超过了280，请精简");
		return false;
	} else if(content.match(/\[自定义\]/)) {
		$notice_form.addClass("warning");
		selectEnd(notice_form2);
		alertFail("请定义您的标签");
		return false;
	}
	
	if($('li.insert_video div.insert', $notice_form).is(":visible")) {
		alertFail("请添加您的视频链接，如果不想添加视频，可关闭此窗口");		
		return false;
	} else if($('li.insert_music div.insert', $notice_form).is(":visible")) {
		alertFail("请添加您的音乐链接，如果不想添加音乐，可关闭此窗口");
		return false;
	} else if($('li.insert_emotion #emotions', $notice_form).is(":visible")) {
		alertFail("请选择您需要添加的表情，如果不想添加表情，可关闭此窗口");
		return false;
	}
	
	if(content.search(/[\[【][^\]]+[\]】]/g) == -1) { ///\[[^\]]+\]/g
		window.alerttag = true;
	} else {
		content = content.replace(/[\[【][^\]]+[\]】]/g, '');
		if(content.trim() == "") {
			$notice_form.addClass("warning");
			alertFail("您需要对此标签添加一些内容");
			selectEnd(notice_form2);
			return false;
		}
	}
	
	return true;
}

var newNoticeOptions = { 
	dataType: 'json',
	beforeSubmit: function(formData, jqForm, options) {
		if(!validateInput()) {
			return false;
		}
		if ($my.notice_form.hasClass("processing")) {
			alertFail('消息正在发送中， 请稍候');
			return false;
		}
		$my.notice_form.addClass("processing").find('input[type="submit"]').attr("disabled", "disabled").addClass("disabled");
		return true;
	},
	complete : function(r, s) {
		$my.notice_form.removeClass("processing")
			.find('input[type="submit"]').removeAttr("disabled").removeClass("disabled");
	},
	success: function(json) {
		$my.notices.find('.guide').remove().end()
			.prepend(json.result).find('li.notice:first').css({display:'none'}).fadeIn('slow').each(function() {
				$(this).mouseover(function() {
					if (! $(this).hasClass('op_added')) {
						$(this).addClass('op_added');
						addNoticeOperation(this);
					}
				});
			});
			
		$my.notice_form
			.find('li.insert_video a.video').attr('title', '插入视频链接').text('插入视频').css('color', '#FFFFFF').end()
			.find('li.insert_music a.music').attr('title', '插入音乐链接').text('插入音乐').css('color', '#FFFFFF').end()
			.find('li.insert_picture a.picture').attr('title', '插入本地图片').text('插入图片').css('color', '#FFFFFF').end()
			.find('#noticefilename, input[name="video"], input[name="audio"], input[name="photo"], textarea').val('').end()
			.find('li.first_tag, li.self_define').removeClass("on").removeClass("active").removeClass('added');
		
		var $notice_num_span = $('#widgets a.moment span');
		$notice_num_span.text(parseInt($notice_num_span.text())+1);
		
		fadeSuccess("发送成功");
//		if (window.alerttag) {
//			window.alerttag = false;
//			alertFail("发送成功。因为没有加入话题，这条消息并未给您增加财富，下次记得加哦！")
//		} else {
//			fadeSuccess("发送成功");
//		}
		processNotice();
	}
};

function noticeReply() {
  if (window.replying) {
	  return false;
  }
  window.replying = true;
  var id, nickname, uname, content, mode, mode_identifier;
  id = $(this).attr('nid');
  var $video = $("#videoplayer"); 
  var $discussion_detail = $("#contents div.discussion_detail");
  //需要在视频播放页面, 相关会话页面(怎么加比较好?), 评论页面添加mode, mode_identifier
  if($video.length > 0) {
	  nickname = $('input.name', $video).val(); 
	  uname = $('input.uname', $video).val();
	  content = $('dt.title span', $video).text().specialCharToEntity();
	  mode = $("input[name=mode]", $video).val();
	  mode_identifier = $("input[name=mode_identifier]", $video).val();
  } else {
	  var noticeParent = $('#notice-' + id);
	  //在评论页面没有这个
	  nickname = $('.name', noticeParent).text(); 
	  uname = $('input.uname', noticeParent).val();
	  content = $('div.content', noticeParent).children('span').text().specialCharToEntity();
	  if($discussion_detail.length > 0) {
		  mode = $("input[name=mode]", $discussion_detail).val();
		  mode_identifier = $("input[name=mode_identifier]", $discussion_detail).val();
	  } else {
		  mode = $("input[name=mode]", $my.notice_form).val();
		  mode_identifier = $("input[name=mode_identifier]", $my.notice_form).val();
	  }
  }

  // 每28个字换行，每行17px
  var height = 205 + content.length/28*17;
  
  var replyinputer =
		  '<div class="dialog_body"><form class="reply" action="' + $(this).attr('href') + '" method="post">' +
		  '<fieldset><legend>回复</legend>' + '<p>' +content + '</p>' + 
		  '<div class="simple_form"><a href="#" class="emotion">插入表情</a><span>您还可以输入<em>280</em>个字</span>'+
		  '<textarea name="status_textarea" style="overflow-y: auto;" rows="3" cols="45"></textarea></div>';
  if (mode != 'group') {
	  replyinputer += '<div class="option"><input class="checkbox" id="addInbox" type="checkbox" name="replyinbox" value="1"></input>'+
	      '<label for="addInbox">作为一条新消息</label></div>';
	  height += 24;
  }
  replyinputer += '<div class="op"><input class="confirm button60" type="submit" value="回复" name="status_submit" id="notice_action-submit2"></input>' +
  		  '<a class="cancel button60" href="#">取消</a></div>'+
		  '<input type="hidden" value="' + $('body').attr('token') + '" name="token"></input>';
  if (mode) {
	 replyinputer += '<input type="hidden" name="mode" value="' + mode + '"></input> ' + 
		  '<input type="hidden" name="mode_identifier" value="' + mode_identifier + '"></input>';
  }
  replyinputer += '<input type="hidden" value="' + id + '" name="inreplyto"></input>' +
  	'<input type="hidden" value="1" name="newreply"></input>'+
	'</fieldset></form></div>';
   
  var arrPageSizes = getPageSize();
  var top = arrPageSizes[3]/3;
  var left = arrPageSizes[2]/3;
  $('div.player embed, div.player object').css({width: '0'});
  var replydlg = $(replyinputer).dialog({width : 396, height : height, title: "回复" + nickname, position: [left, top], draggable : true, resizable : false, 
	  	close: function(event, ui) {$('div.player embed, div.player object').removeAttr('style');$(this).dialog('destroy').remove();window.replying = false;return false;}});
  
  $('a.emotion', replydlg).click(function(event) {
	  newEmotionDialog('reply_emotion' + id, $(this).parents('form').find('textarea'), {left : event.pageX, top : event.pageY});
	  return false;
  });
  $('a.cancel', replydlg).click(function() {
		$(this).parents('div.dialog_body').dialog('close');
		return false;
  });
  $("form", replydlg).ajaxForm(newReplyOptions).each(addAjaxHidden);
  
  $("form textarea", replydlg)
	.bind("keyup paste", function() {
		counterArray(this, $("span em", $(this).parent()), 2);
	})
	.bind("keydown", textareaEventHandler).focus();
  
  return false;
}

var newReplyOptions = {
	dataType: 'json',
	beforeSubmit: function(formData, jqForm, options) { 
		if ($("textarea", jqForm).val().length == 0) {
			jqForm.addClass("warning");
			alertFail("请输入您想说的话");
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
		var $dialog = $("div.dialog_body");
		//对一些页面, 不用添加上去 replies, favorites 及不作为一条新分享
    	var url = document.URL;
    	if($("#owner_summary").length > 0 || 
    			url.search(/gamepub\.cn\/[a-zA-Z0-9_]+\/(replies|favorites|checkfavorites|showall|showreplies)/i) != -1 ||
    			url.search(/gamepub\.cn\/(conversation|public|hottopics|hotnotice|experiences|requestforhelp|rank)/i) != -1 ||
    			json.replyinbox == 'true') {
    		$dialog.dialog('close');
    		fadeSuccess("发送成功");
    	} else {
	    	var div = $(json.result).get(0);
	    	var $notices = $my.notices;
	    	if ($("#"+div.id).length == 0) {
		    	if ($(".guide", $notices).length > 0) {
		    		$(".guide", $notices).remove();
		    	}
		    	
		    	$notices.prepend(json.result);
    			
    			$('#'+div.id).css({display:'none'}).fadeIn(2500).each(function() {
	    			addNoticeOperation(this);
	    		});
    			
	    		$dialog.dialog('close');
	    		fadeSuccess("发送成功");
		    }
    	}
		
		if ($my.waiter.size() > 0) {
			$my.waiter.find("div.sendfaq").remove();
		}
	}
};

function noticeOriginRetweet() { //弹出原文转发对话框

  if (window.originretweeting) {
	  return false;
  }
  window.originretweeting = true;
  
  var noticeParent = $(this).parents(".notice");
  var id = $('blockquote', noticeParent).attr('nid');
  
  var nickname = $(this).attr('nickname');
  var content = $('blockquote div.c span', noticeParent).text().specialCharToEntity();//获取必须的一些值
  var pageentry = '';
  var mode = $("input[name=mode]", $my.notice_form).val();
  var mode_identifier = $("input[name=mode_identifier]", $my.notice_form).val();
  
  pageentry +=
		  '<div class="dialog_body"><form action="' + $(this).attr('href') + '" method="post">' +
		  '<fieldset><legend>原文转载</legend><p>转：';
  pageentry += content;//获取必须的一些值; 
  pageentry +=
		  '</p><div class="simple_form"><a href="#" class="emotion">插入表情</a><span>您还可以输入<em>280</em>个字</span>' +
		  '<textarea name="status_textarea" style="overflow-y: auto;" rows="3" cols="45">' +
		  '</textarea></div><div class="option"><input id="discusscurrent" type="checkbox" name="discusscurrent" class="checkbox" value="1"></input>'+
	      '<label for="discusscurrent">同时作为给' + nickname + '的评论发布</label>';//当前消息用户
  
  pageentry +=
		  '</div><div class="op"><input class="confirm button60" type="submit" value="转载" name="status_submit"></input>'+
		  '<a class="cancel button60" href="#">取消</a></div>' +
		  '<input type="hidden" value="' + $('body').attr('token') + '" name="token"></input>'+
		  '<input type="hidden" value="' + id + '" name="inretweetfrom"></input>' +
		  '<input type="hidden" value="' + nickname + '"  name="retweetfrom"></input>';
  
  pageentry += '</fieldset></form></div>';
	
  //每28个字换行，每行17px
  var height = 235 + content.length/28*17;

  var arrPageSizes = getPageSize();
  var top = arrPageSizes[3]/3;
  var left = arrPageSizes[2]/3;
  
  $('div.player embed, div.player object').css({width: '0'});
  var retweetdlg = $(pageentry).dialog({width : 396, height : height, title: "转载" + nickname, position: [left, top], draggable : true, resizable : false,
	  	close: function(event, ui) {$('div.player embed, div.player object').removeAttr('style');$(this).dialog('destroy').remove();window.originretweeting = false;return false;} });
  
  $('a.emotion', retweetdlg).click(function(event) {
	  newEmotionDialog('retweet_emotion' + id, $(this).parents('form').find('textarea'), {left : event.pageX, top : event.pageY});
	  return false;
  });
  $('a.cancel', retweetdlg).click(function() {
		$(this).parents('div.dialog_body').dialog('close');
		return false;
  });
  
  $("form", retweetdlg).ajaxForm(newRetweetOptions).each(addAjaxHidden);
  
  $("form textarea", retweetdlg)
  	.bind("keyup paste", function() {
  		counterArray(this, $("span em", $(this).parent()), 3);
  	})
  	.bind("keydown", textareaEventHandler).focus();
  
  return false;	
}

function noticeRetweet() { //弹出转发对话框
  if (window.retweeting) {
	  return false;
  }
  window.retweeting = true;
  
  var id = $(this).attr('nid');
  var rootId = $(this).attr('oid');
  var $video = $("#videoplayer"); 
  var nickname = $(this).attr('nickname');
  var noticeParent;
  var content;
  
  //在视频页没有原文转载
  if($video.length > 0) {
	  content = $('dt.title span', $video).text().specialCharToEntity();
  } else {
	  noticeParent = $(this).parents(".notice");
	  content = $('div.content', noticeParent).children('span').text().specialCharToEntity();
  }
  
  //每28个字换行，每行17px
  var height = 235;
  
  var pageentry = '';
  
  pageentry += '<div class="dialog_body"><form action="' + $(this).attr('href') + '" method="post">' +
		  '<fieldset><legend>转载</legend><p>转：';
  if (rootId) {
	  var quote = $('blockquote div.c', noticeParent).children('span').text().specialCharToEntity();
	  pageentry += quote;//获取必须的一些值; 
	  height += quote.length/28*17;
  } else {
	  pageentry += content;
	  height += content.length/28*17;
  }
  pageentry +=
		  '</p><div class="simple_form"><a href="#" class="emotion">插入表情</a><span>您还可以输入<em>280</em>个字</span>'+
		  '<textarea name="status_textarea" style="overflow-y: auto;" rows="3" cols="45" id="status_textarea_retweet">';
  if (rootId) {
	  pageentry +=' //@'+nickname+':'+content;
  }
  pageentry +=
		  '</textarea></div><div class="option"><input id="discusscurrent" type="checkbox" name="discusscurrent" class="checkbox" value="1"></input>'+
	      '<label for="discusscurrent">同时作为给' + nickname + '的评论发布</label>';//当前消息用户
  //被删除的消息不能转载及评论
  if (rootId && $('blockquote a.origindiscuss', noticeParent).length > 0) {
	  pageentry +='<br /><input id="discussoriginal" type="checkbox" name="discussoriginal" class="checkbox" value="1"></input>'+
	      '<label for="discussoriginal">同时作为给原作者' +
	      $('blockquote h4', noticeParent).text() + '的评论发布</label>';//根消息用户
	  
	  height += 24;
  }
  
  pageentry +=
		  '</div><div class="op"><input class="confirm button60" type="submit" value="转载" name="status_submit"></input>'+
		  '<a class="cancel button60" href="#">取消</a></div>' +
		  '<input type="hidden" value="' + $('body').attr('token') + '" name="token"></input>'+
		  '<input type="hidden" value="' + id + '" name="inretweetfrom"></input>' +
		  '<input type="hidden" value="' + nickname + '"  name="retweetfrom"></input>';
  if (rootId) {
	  pageentry += '<input type="hidden" value="' + rootId + '" name="orinretweetfrom"></input>';
  }
  pageentry += '</fieldset></form></div>';

  var arrPageSizes = getPageSize();
  var top = arrPageSizes[3]/3;
  var left = arrPageSizes[2]/3;
  
  $('div.player embed, div.player object').css({width: '0'});
  var retweetdlg = $(pageentry).dialog({width : 396, height : height, title: "转载" + nickname, position: [left, top], draggable : true, resizable : false,
	  	close: function(event, ui) {$('div.player embed, div.player object').removeAttr('style');$(this).dialog('destroy').remove();window.retweeting = false;return false;} });
  
  $('a.emotion', retweetdlg).click(function(event) {
	  newEmotionDialog('retweet_emotion' + id, $(this).parents('form').find('textarea'), {left : event.pageX, top : event.pageY});
	  return false;
  });
  $('a.cancel', retweetdlg).click(function() {
		$(this).parents('div.dialog_body').dialog('close');
		return false;
  });
  
  $("form", retweetdlg).ajaxForm(newRetweetOptions).each(addAjaxHidden);
  
  $("form textarea", retweetdlg)
  	.bind("keyup paste", function() {
  		counterArray(this, $("span em", $(this).parent()), 4);
  	})
  	.bind("keydown", textareaEventHandler).focus();
  
  selectStart(document.getElementById("status_textarea_retweet"));
  return false;	
}

/*
 0. 发消息这块
 1. 发悄悄话
 2. 回复
 3. 原始转载
 4. 直接转载
 5. 他人-对他说
 6. 他人-悄悄话
 7. 相关会话
 8. 推荐
 9. 群组申请
 */

var counterBlackoutArray = new Array(false, false, false, false, false, false, false, false, false, false);
var nfOverflowArray = new Array(false, false, false, false, false, false, false, false, false, false);
function counterArray(textarea, counter, idx, maxLength){
	if (! maxLength) {
		maxLength = 280;
	}
	var currentLength = $(textarea).val().length;;
	var remaining = maxLength - currentLength;

	if (remaining.toString() != counter.text()) {
		if (!counterBlackoutArray[idx] || remaining === 0) {
			if (! nfOverflowArray[idx] && remaining < 0) {
				nfOverflow = true;
				counter.text(remaining);
			} else if (nfOverflowArray[idx] && remaining > 0) {
				nfOverflowArray[idx] = false;
			} else {
				counter.text(remaining);
			}
			// Skip updates for the next 500ms.
            // On slower hardware, updating on every keypress is unpleasant.
            if (!counterBlackoutArray[idx]) {
            	counterBlackoutArray[idx] = true;
            	setTimeoutCounter(clearCounterBlackoutArray, 500, textarea, counter, idx, maxLength);
            }
		}
	 }
}

var __sto = setTimeout;
var setTimeoutCounter = function(callback,timeout)
{
    var args = Array.prototype.slice.call(arguments,2);
    var _cb = function()
    {
        callback.apply(null,args);
    }
    
    __sto(_cb,timeout);
}

function clearCounterBlackoutArray(textarea, counter, idx, maxLength) {
    // Allow keyup events to poke the counter again
	counterBlackoutArray[idx] = false;
    // Check if the string changed since we last looked
	counterArray(textarea, counter, idx, maxLength);
}

var newRetweetOptions = { 
	dataType: 'json',
	beforeSubmit: function(formData, jqForm, options) {
		if ($("textarea", jqForm).val().length == 0) {
			jqForm.addClass("warning");
			alertFail("请输入您想说的话");
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
		var $dialog = $("div.dialog_body");
    	var url = document.URL;	  
    	if($("#owner_summary").length > 0 || 
    		url.search(/gamepub\.cn\/[a-zA-Z0-9]+\/(replies|favorites|checkfavorites|showall|showreplies)/i) != -1||
    		url.search(/gamepub\.cn\/(conversation|public|hottopics|hotnotice|experiences|requestforhelp|rank)/i) != -1) {
    		$dialog.dialog('close');
    		fadeSuccess("转载成功");
    	} else {
    		var div = $(json.html).get(0);
	    	var $notices = $my.notices;
	    	if ($("#"+div.id).length == 0) {
    			var nid = json.nid;
    			var add_discuss = json.add_discuss;
    			var add_origin_discuss = json.add_origin_discuss;
    			var oid = json.oid;
    			
    			var noticeParent = $('#notice-' + nid);	    				    			
    			var span = $('div.bar ul.op li.retweet span', noticeParent);
    			if (span.size() == 0) {
    				$('div.bar ul.op li.retweet a', noticeParent).append('(<span>1</span>)');
    			} else {
    				span.text(parseInt(span.text()) + 1);
    			}
    			if (add_discuss == 'true') {
    				var dspan = $('div.bar ul.op li.discuss span', noticeParent);
    				if (dspan.size() == 0) {
    					$('div.bar ul.op li.discuss a', noticeParent).append('(<span>1</span>)');
    				} else {
    					dspan.text(parseInt(dspan.text()) + 1);
    				}
    			}
    			
    			$('li.notice blockquote[nid="' + nid + '"] a.originretweet em').each(function(){
    				$(this).text(parseInt($(this).text()) + 1);
    			});
    			if (add_discuss == 'true') {
    				$('li.notice blockquote[nid="' + nid + '"] a.origindiscuss em').each(function(){
	    				$(this).text(parseInt($(this).text()) + 1);
	    			});
    			}
    			if (add_origin_discuss == 'true' && oid) {
    				$('li.notice blockquote[nid="' + oid + '"] a.origindiscuss em').each(function(){
	    				$(this).text(parseInt($(this).text()) + 1);
	    			});
    			}
    			$('a[nid="' + nid + '"] em').each(function(){
    				$(this).text(parseInt($(this).text()) + 1);
    			});
    			
		    	if ($(".guide", $notices).length > 0) {
		    		$(".guide", $notices).remove();
		    	}
		    	
	    		$notices.prepend(json.html);
    			
    			$('#'+div.id).css({display:'none'}).fadeIn(2500).each(function() {
    				$(this).mouseover(function() {
    					if (! $(this).hasClass('op_added')) {
    						$(this).addClass('op_added');
    						addNoticeOperation(this);
    					}
    				});
	    		});
    			
	    		$dialog.dialog('close');
	    		fadeSuccess("转载成功");
		    }
		}
		$("form", $dialog).removeClass("processing");
		$("form input.confirm", $dialog).removeAttr("disabled").removeClass("disabled");
	}
};

function noticeDiscuss() {
	if (window.discussing) {
		return false;
	}
	window.discussing = true;
	var nid = $(this).attr('nid');
	var parent = $(this).parents("li.notice");
	parent.toggleClass('discuss_on');
	if (parent.hasClass('discuss_on')) {
		$.ajax({
		  type: "GET",
		  url: $(this).attr('href'),
		  data: {nid: $(this).attr('nid'), token: $('body').attr('token'), ajax: 1},
		  dataType: 'json',
		  error: function (XMLHttpRequest, textStatus, errorThrown) {
			  alertFail('获取评论信息时出错，请稍后再试');
			  window.discussing = false;
		  },
		  success: function(json){
			var discusslist = $(json.html).get(0);
			$(discusslist).hide();
			var noticeParent = $('#notice-' + $(discusslist).attr('nid'));
			noticeParent.append(discusslist);
			var $discussions = $('ol.discussions', noticeParent);
			$discussions.show();
			$('li.create form textarea', $discussions)
				.unbind("keydown").bind("keydown", textareaEventHandler).focus();
			$('li a.toggle', $discussions).click(toggleDiscuss);
			$('li a.delete', $discussions).click(deleteDiscuss);
			$('li form', $discussions).ajaxForm(newDissOptions).each(addAjaxHidden);
			$('li:first', $discussions).css({border:'0'});
			$('li.create form a.emotion', $discussions).click(function(event) {
				newEmotionDialog('discuss_emotion_' + $(this).parents('li.notice').attr('nid'), $(this).parents('form').find('textarea'), {left : event.pageX, top : event.pageY});
				return false;
			});
			
			window.discussing = false;
		  }
		});
	} else {
		$('.discussions', parent).hide().remove();
		window.discussing = false;
	}
	return false;
}

function newEmotionDialog(id, $textarea, pos) {
	if ($('#' + id).length > 0) {
		return false;
	} else {
		$('body').append('<div class="insertemotion rounded5" id="' + id + '">' + getEmotionContent() + '<span class="intro">请选择表情</span><a class="close" href="#">X</a><span class="pointer"></span></div>')
			.find('td > a').click(function() {
				$textarea.insertAtCaret(':' + $(this).attr('title') + ':');
				counterArray($textarea, $my.notice_text_count, 0);
				$(this).parents("div.insertemotion").remove();
				return false;
			}).end().find('a.close').click(function() {
				$(this).parent().remove();
				return false;
			});
		$('#' + id).css({left : (pos.left - 25) + 'px', top : (pos.top + 20) + 'px'});
	}
}

function toggleDiscuss() {
	var dis = $(this).parents('.discussions');
	$('.create form textarea',dis).val('@' + $(this).attr('toreply') + ' ');//.focus();
	var nid = $(this).parent().attr('nid');
	selectEnd(document.getElementById("status_textarea_discuss_"+nid));
	return false;
}

var newDissOptions = {
	dataType: 'json',
	beforeSubmit: function(formData, jqForm, options) { 
		if ($('textarea', jqForm).val().length == 0) {
			alertFail("请输入您想说的话");
			return false;
		} else if ($('textarea', jqForm).val().length > 280) {
			alertFail("您输入的评论超过了280个字，评论信息请控制在280字以内");
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
		var disnotice = $(json.html).get(0);
		var did = $(disnotice).hide().attr('did');
		var parentOl = $('ol.discussions[nid="' + $(disnotice).attr('nid') + '"]');

    	$('li.create', parentOl).after(disnotice);
		
    	$('li[did="' + did + '"] a.toggle', parentOl).click(toggleDiscuss);
    	$('li[did="' + did + '"] a.delete', parentOl).click(deleteDiscuss);
    	$('li[did="' + did + '"]', parentOl).fadeIn('slow');
    	
		$('li.create form', parentOl).removeClass("processing");
		$('li.create form input.submit', parentOl).removeAttr("disabled").removeClass("disabled");
		$('li.create form textarea', parentOl).val("").focus();
		
		var span = $('div.bar ul.op li.discuss span', $(parentOl).parent());
		if (span.size() == 0) {
			$('div.bar ul.op li.discuss a', $(parentOl).parent()).append('(<span>1</span>)');
			$('li:first', parentOl).css({border:'0'});
			$('li.create', parentOl).removeAttr('style');
		} else {
			span.text(parseInt(span.text()) + 1);
		}
		$("ol.discussions li.create form").removeClass("processing");
		$("ol.discussions li.create input.submit").removeAttr("disabled").removeClass("disabled");
	}
};

function deleteDiscuss() {
	if (window.deletingdiscuss) {
		return false;
	}
	window.deletingdiscuss;
	
	if (!confirm("确认删除此评论吗?")){ 
		return false;
	}
	
	$.ajax({
	  type: "post",
	  url: $(this).attr('href'),
	  dataType: 'json',
	  data: {ajax:1,token: $('body').attr('token')}, 
	  error: function (xhr, textStatus, errorThrown) {	
		  alertFail("对不起，在删除时遇到问题，请稍后再试");
		  window.deletingdiscuss = false;
	  },
	  success: function(json){
		  if (json.result == 'success') {
			  var parentOl = $('ol.discussions li[did="' + json.did + '"]').parent();
			  var span = $('div.bar ul.op li.discuss span', $(parentOl).parent());
			  if (parseInt(span.text()) == 1) {
				  span.parent().text('评论');
			  } else {
				  span.text(parseInt(span.text()) - 1);
			  }
			  
			  $('li.notice[did="' + json.did + '"], ol.discussions li[did="' + json.did + '"]').fadeOut("slow", function() {
				  var pol = $(this).parents('ol.discussions');
				  $(this).remove();
				  $('li:first', pol).css({border:'0'});
			  });
		  } else {
			  alertFail('删除评论失败');
		  }
		  window.deletingdiscuss = false;
	  }
	});
	return false;
}


function nextPageNotices() {
	var url = $(this).attr('href'); //获取属性值
	if ($(this).hasClass('processing')) {
		return false;
	}
	
	//$my.notice_more, 动态生成, 需要及时改变
	$(this).addClass('processing');
	
	$.ajax({
		  type: "get",
		  url: url,
		  data: {ajax: 1}, 
		  dataType: 'json',
		  success: function(json){
			
			if (json.result == 'true') {
				var noticelist = $(json.notices).get(0);			
				var timeline = $('#notices');		
				
				$(noticelist).find(".notice").each(function(){ //若原列表中已有则从xml中删除notice
					if($(timeline).find("#"+this.id).length){
						$(this).remove();
					}
				});
				
				var notices=$(noticelist).find(".notice");
				$(timeline).append(notices); //加到里面
				
				var $notice_more = $("#notice_more");
				
				if (json.pg) {
					$notice_more.replaceWith(json.pg);
					$("#notice_more").click(nextPageNotices);
				} else {
					$notice_more.remove();
				}
				$(notices).each(function() {
					$(this).mouseover(function() {
						if (! $(this).hasClass('op_added')) {
							$(this).addClass('op_added');
							addNoticeOperation(this);
						}
					});
				});
				if ($.browser.msie && parseInt($.browser.version) <= 7 ) {
					zi=999;
					$('#notices li.notice').each(function() {
					  this.style.zIndex = zi --;
					});
				}
			}
			$("#notice_more").removeClass("processing");
		  }
	});
	return false;
}
			
function attachVideoHandler () {
	closeAllAttachDialog();
	if(!validateAttach()) {
		return false;
	} 
	$(this).closest('li').find('div').show().bgiframe().find('input.text').focus();
	return false;
}

function attachPhotoHandler () {
	closeAllAttachDialog();
	if(!validateAttach()) {
		return false;
	}
	if (! $('#notice_file').hasClass('op_added')) {
		$("#notice_file").addClass('op_added').uploadify({
			'uploader'       : '/js/uploadify.swf',
			'script'         : '/ajax/uploadfile?uid=' + $("#noticeFileQueue").attr('uid'),
			'cancelImg'      : '/theme/default/images/uploadify/cancel.png',
			'buttonImg'      : '/theme/default/images/uploadify/upload.jpg',
			'width'          : 60,
			'height'         : 23,
			'folder'         : '/file/tmp',
			'queueID'        : 'noticeFileQueue',
			'auto'           : true,
			'multi'          : false,
			'buttonText'     : 'Upload',
			'fileDesc'       : 'JPG/PNG/GIF文件',
			'fileExt'        : '*.jpg;*.png;*.jpeg;*.gif',
			'sizeLimit'      : '1048576',
			'onError'        : uploadifyErrorHandlerForNotice,
			'onComplete'     : uploadifyCompleteHandlerForNotice
		});
	}
	$(this).closest('li').find('div').show().bgiframe().find('a.close').unbind('click').click(function() {
		var $this = $(this);
		$('#notice_file').uploadifyClearQueue();
		$this.parents("div.insert").hide("slow")
				.find('input.text').val("");
		return false;
	});
	
	return false;
}

var uploadifyErrorHandlerForNotice = function(event, queueID, fileObj, errorObj) {
	if (errorObj.type == 'File Size') {
		alert('您选择的文件太大，不能超过1MB');
	};
	return false;
};

var uploadifyCompleteHandlerForNotice = function(event, queueID, fileObj, response, data) {
	$("#noticefilename").val(response);
	$("li.insert_picture a.picture", $my.notice_form).attr('title', '图片已传').text('图片已传').css('color', '#F7CB79');
	$("li.insert_picture div.insert", $my.notice_form).hide("slow"); 
	$my.notice_textarea.focus();
};

function closeAllAttachDialog() {
	$("div.insert", $my.notice_form).each(function() {
		if ($(this).is(":visible")) {
			$(this).hide();
		}
		$errorField = $('p.error', this);
		if ($errorField != null) {
			$errorField.hide();
		}
	});
}

function validateAttach() {
	var $notice_form = $my.notice_form;
	if($("#noticefilename").val().length > 0) {
		if(!confirm('一条消息中只能含有一个图片/音乐/视频。 继续插入，之前的图片会被清除，是否继续？')) {
			return false;
		} else {
			$("#noticefilename").val("");
			$("li.insert_picture a.picture", $notice_form).attr('title', '插入本地图片').text('插入图片').css('color', '#FFFFFF');
		}
	}
	var value = $("li.insert_music input.text", $notice_form).val().trim();
	var $textArea = $my.notice_textarea;
	var content = $textArea.val().trim();
	if(value != 'http://' && value.length > 0) {
		if(!confirm('一条消息中只能含有一个图片/音乐/视频。 继续插入，之前的音乐会被清除，是否继续？')) {
			return false;
		} else {
			$("li.insert_music input.text", $notice_form).val("");
			$("li.insert_music a.music", $notice_form).attr('title', '插入音乐链接').text('插入音乐').css('color', '#FFFFFF');
		}
	}
	value = $("li.insert_video input.text", $notice_form).val().trim();
	if(value != 'http://' && value.length > 0) {
		if(!confirm('一条消息中只能含有一个图片/音乐/视频。 继续插入，之前的视频会被清除，是否继续？')) {
			return false;
		} else {
			$("li.insert_video input.text", $notice_form).val("");
			$("li.insert_video a.video", $notice_form).attr('title', '插入视频链接').text('插入视频').css('color', '#FFFFFF');
		}
	}
	return true;
}

function closeAttachDialog() {
	$(this).closest("div.insert").hide("slow").find('input.text').val("");
	return false;
}

function insertDirectLink() {
	var attachDlg = $(this).closest("div.insert");
	var value = attachDlg.find('input.text').val();
	
	var $textArea = $my.notice_textarea;
	var text = $textArea.val();		
	$textArea.val(text + value + ' ').keyup();
	selectEnd(document.getElementById("notice_data-text"));
	processNotice();
	
	attachDlg.hide("slow").find('input.text').val("");
	return false;
}

function addVideoCompleted() {
	$("li.insert_video a.video", $my.notice_form).attr('title', '视频已插入').text('视频已加').css('color', '#F7CB79');
	$("li.insert_video div.insert", $my.notice_form).hide("slow"); 
	$my.notice_textarea.focus();
} 

function addVideoUrl() {
	var inputField = $(this).closest('p').find('input.text');
	var errorField = $(this).closest('p').next('p.error');
	var value = inputField.val().trim();
			
	if( value == "" || value == "http://") {
		inputField.focus();
		return false;
	} else if(! /^http:\/\//i.test(value) &&
			! /^http:\/\/v\.youku\.com\/v_show\/id_(.{13})\.html/i.test(value) &&
			! /^http:\/\/v\.youku\.com\/v_playlist\/f(\d+)o(\d+)p(\d+)\.html/i.test(value) &&
			! /^http:\/\/www\.tudou\.com\/programs\/view\/(.{11})/i.test(value) &&
			! /^http:\/\/www\.tudou\.com\/playlist\/playindex.do\?lid=(\d+)(?:&iid=(\d+))?/i.test(value) &&
			! /^http:\/\/www\.tudou\.com\/playlist\/p\/a(\d+)\.html(?:\?iid=(\d+))?/i.test(value) &&
			! /^http:\/\/v\.ku6\.com\/show\/(.{16})\.html/i.test(value) &&
			! /^http:\/\/v\.ku6\.com\/special\/show_(\d+)\/(.{16})\.html/i.test(value) &&
			! /^http:\/\/v\.ku6\.com\/special\/index_(\d+)\.html/i.test(value) &&
			! /^http:\/\/cgi\.video\.qq\.com\/v1\/videopl\?v=(.{11})/i.test(value) &&
			! /^http:\/\/cgi\.video\.qq\.com\/v1\/videopl\/vbar\?g=(.*)/i.test(value) &&
			! /^http:\/\/video\.sina\.com\.cn.*\/(\d+)-(\d+)\.html$/i.test(value) &&
			! /^http:\/\/video\.sina\.com\.cn.*\/\d+\.html$/i.test(value) &&
			! /^http:\/\/www\.aipai\.com\/[a-z][0-9]\/.*\.html/i.test(value) &&
			! /^http:\/\/www\.56\.com\/u\d{2}\/v_(.{11})\.html/i.test(value) &&
			! /^http:\/\/www\.56\.com\/w\d{2}\/play_album-aid-(\d*)_vid-(.{11})\.html/i.test(value) &&
			! /^http:\/\/v\.game\.sohu\.com\/v\/\d+\/\d+\/\d+\/.{8}/i.test(value) &&
			! /^http:\/\/v\.game\.sohu\.com\/b\/(\d+)\/a_\d+_(.{8})/i.test(value) &&
			! /^http:\/\/.*\.joy\.cn\/video\/(\d+).htm/i.test(value) &&
			! /^http:\/\/.*\.joy\.cn\/Album\/\d+\/\d+\/\d+\/(\d+).htm/i.test(value) ) {
		errorField.show();
		return false;
	}
	
	addVideoCompleted();
	
	return false;
}

function attachAudioHandler () {
	closeAllAttachDialog();
	if(!validateAttach()) {
		return false;
	} 
	$(this).closest('li').find('div').show().bgiframe().find('input.text').focus();	
	return false;
}

function addAudioCompleted() {
	$("li.insert_music a.music", $my.notice_form).attr('title', '音乐已插入').text('音乐已加').css('color', '#F7CB79');
	$("li.insert_music div.insert", $my.notice_form).hide("slow"); 
	$my.notice_textarea.focus();
} 

function addAudioUrl() {
	var inputField = $(this).closest('p').find('input.text');
	var errorField = $(this).closest('p').next('p.error');
	var value = inputField.val().trim();
	
	if( value == "" || value == "http://") {
		inputField.focus();
		return false;
	} else if(value.search(/^https?:\/\/(.*)\.mp3$/i) == -1) {
		errorField.show();
		return false;
	}
	
	addAudioCompleted();
	return false;
}

function getEmotionContent() {
	var emotions = [
			         {src : '/theme/default/images/emotions/01.gif', text : '鼓掌'}, 
			         {src : '/theme/default/images/emotions/02.gif', text : '哭'},
			         {src : '/theme/default/images/emotions/03.gif', text : '怒'},
			         {src : '/theme/default/images/emotions/04.gif', text : '微笑'},
			         {src : '/theme/default/images/emotions/05.gif', text : '汗'},
			         {src : '/theme/default/images/emotions/06.gif', text : '再见'},
			         {src : '/theme/default/images/emotions/07.gif', text : '酷'},
			         {src : '/theme/default/images/emotions/08.gif', text : '惊讶'},
			         {src : '/theme/default/images/emotions/09.gif', text : '口哨'},
			         {src : '/theme/default/images/emotions/10.gif', text : '拥抱'},
			         {src : '/theme/default/images/emotions/11.gif', text : '傻笑'},
			         {src : '/theme/default/images/emotions/12.gif', text : '惊喜'},
			         {src : '/theme/default/images/emotions/13.gif', text : '疯了'},
			         {src : '/theme/default/images/emotions/14.gif', text : '忧伤'},
			         {src : '/theme/default/images/emotions/15.gif', text : '胜利'},   
			         {src : '/theme/default/images/emotions/17.gif', text : '大笑'},
			         {src : '/theme/default/images/emotions/18.gif', text : '发呆'},
			         {src : '/theme/default/images/emotions/19.gif', text : '切'},
			         {src : '/theme/default/images/emotions/20.gif', text : '讨厌'},
			         {src : '/theme/default/images/emotions/22.gif', text : '难过'},
			         {src : '/theme/default/images/emotions/23.gif', text : '生气'},
			         {src : '/theme/default/images/emotions/27.gif', text : '色'},
			         {src : '/theme/default/images/emotions/24.gif', text : '郁闷'},
			         {src : '/theme/default/images/emotions/25.gif', text : '鄙视'},
			         {src : '/theme/default/images/emotions/26.gif', text : '闭嘴'},
			         {src : '/theme/default/images/emotions/21.gif', text : '功夫'},
			         {src : '/theme/default/images/emotions/16.gif', text : '喜欢'}
			         
			     ];
	
	var pannel = '<table cellspacing="1" border="1"><tbody>';
	var inTr = false;
	var idx = 0;
	for (var i = 0, l = emotions.length; i < l; i ++) {
		if (idx % 5 == 0) {
			pannel += '<tr>';
			inTr = true;
		}
		pannel += '<td><a href="#" title="' + emotions[i].text + '"><img src="' + emotions[i].src + '" title="' + emotions[i].text + '" /></a></td>';
		if (idx % 5 == 4) {
			pannel += '</tr>';
			inTr = false;
		}
		idx ++;
	}
	while (idx % 5 > 0 && idx % 5 <= 4) {
		pannel += '<td></td>';
		if (idx % 5 == 4) {
			pannel += '</tr>';
			inTr = false;
		}
		idx ++;
	}
	pannel += '</tbody></table>';
	
	return pannel;
}

function addEmotion() {	
	var textarea = $my.notice_textarea.val($my.notice_textarea.val() + ':' + $(this).attr('title') + ':').focus().keyup().get(0);
	if (textarea.setSelectionRange) {
		var len = $(textarea).val().length * 2;
    	textarea.setSelectionRange(len, len);
    } else {
    	$(textarea).val($(textarea).val());
    }
	selectEnd(document.getElementById("notice_data-text"));
	$(this).closest('div').hide("slow");
	processNotice();	

	return false;
}

var showLoading = function() {
	$.blockUI({ 
		message: '<h1 class="blockui_loading">处理中，请稍候...</h1>',
		css : {width : '396px',border : '1px solid #ccc', outline : '1px solid #fff'}
	});
};

var showLogin = function() {
	
	$('div.player embed, div.player object').css({width: '0'});
	
	$.blockUI({
		css : {width : '396px',border : '1px solid #ccc', outline : '1px solid #fff'},
		message: '<div id="popup_title">请登录GamePub<a href="#">X</a></div><div class="dialog_body"><form class="login" id="login" action="/main/login" method="post"><fieldset><legend>登录</legend><p><label class="label60">账号</label><input name="uname" type="text" class="text200" /></p><p><label class="label60">密码</label><input name="password" type="password" class="text200" /><a href="/main/recoverpassword" target="_blank" class="forget">忘记密码</a></p><p class="rem"><input type="checkbox" class="checkbox" name="rememberme" id="rememberme" /><label for="rememberme">两周之内自动登录</label></p><p class="msg" style="display:none;"></p><div class="op"><input type="submit" class="confirm button60" value="登录" /><a href="#" class="canc">暂不登录</a></div><p class="reg">还没有帐号？<a href="/register" target="_blank">立即注册</a></p></fieldset></form></div>',
		focusInput : true
	});
	$('#popup_title a').click(function() {
		$.unblockUI({ 
            onUnblock: function(){ $('div.player embed, div.player object').removeAttr('style'); } 
        });
		return false;
	});
	$('#login').ajaxForm({
		dataType : 'json',
		beforeSubmit: function(formData, jqForm, options) {
			if (jqForm.hasClass('processing')) {
				return false;
			}
			jqForm.addClass("processing");
			$("input[type=submit]", jqForm).attr("disabled", "disabled").addClass("disabled");
			$('#login p.msg').hide();
		},
		error : function() {
			alertFail('您已经登录了!');
			$('#login').removeClass('processing')
				.find("input[type=submit]").removeAttr("disabled").removeClass("disabled");
			window.location = window.location;
		},
		success: function(json) {
			if (json.result == 'true') {
				$.unblockUI({
					onUnblock: function(){
						$('div.player embed, div.player object').removeAttr('style');
						Helper.refresh();
					}
				});
			} else {
				$('#login p.msg').text(json.msg).show();
			}
			$('#login').removeClass('processing')
				.find("input[type=submit]").removeAttr("disabled").removeClass("disabled");
		}
	}).each(addAjaxHidden).find('a.canc').click(function() {
		$.unblockUI({ 
            onUnblock: function(){ $('div.player embed, div.player object').removeAttr('style'); } 
        });
		return false;
	}).end().find('input[name="password"]').bind('keyup', function(event) {
		if (event.keyCode == 13) {
			$(this).parents('form').submit();
		}
	});
	return false;
};

function playMusic() {
	if ($('body').attr('anonymous') == '1') {
		return showLogin();
	}
	var w = window.open('/mplayer/show?nid=' + $(this).parents('.notice').attr('nid'), 'mplayer', "width=730,height=576,top=100px,left=220px,scrollbars=no,resizable=no");
	w.focus();
	return false;
}

function addNoticeOperation(element) {
	$(element)
		.find('div.bar ul.op')
			.find('li.delete > a').click(noticeDelete).end()
			.find('li.favor > a').click(noticeFavor).end()
			.find('li.disfavor > a').click(noticeDisfavor).end()
			.find('li.reply > a').click(noticeReply).end()
			.find('li.discuss > a').click(noticeDiscuss).end()
			.find('li.retweet > a').click(noticeRetweet).end()
		.end()
		.find('blockquote a.originretweet').click(noticeOriginRetweet).end()
		.find('div.music_message > a').click(playMusic).end()
		.find('div.image_message').each(function() {
			imageDisplay(this);
		}).end()
		.find('a.trylogin').unbind('click').click(showLogin);
}

//add by Xiangyun
function imageDisplay(e){
	$(e).find('div.smallpicture a.smallimagebtn').click(function() {
			$(this).parent().hide(0).next().slideDown(1000);//show(100);
			return false;
		}).end()
		.find('div.bigpicture')
			.find('a.bigimagebtn,a.pickpicture').click(function() {
				$(this).parents('div.bigpicture').slideUp(500).prev().show(500);//slideUp(1000);//
				return false;
			}).end()
			.find('div.btnbanel a.rightrotate').click(function() {
				var $bp = $(this).parents('div.bigpicture');
				$bp.find('.bigimage').rotateRight($bp.find('div.wrappicture'));
				return false;
			}).end()
			.find('div.btnbanel a.leftrotate').click(function() {
				var $bp = $(this).parents('div.bigpicture');
				$bp.find('.bigimage').rotateLeft($bp.find('div.wrappicture'));
				return false;
			});
}

function _toggleOnDocument(handle, effect) {
	if ($(document).data('toggle')) {
		if ($(document).data('toggle').css('display') != 'block') {
			$(document).unbind('click').removeData('toggle');
		}
	} else {
		$(document).data('toggle', handle).bind('click', function(event) {
			var handle = $(this).data('toggle');
			if (handle.css('display') == 'block') {
				if (effect == 'fold') {
					handle.hide('fold', {}, 500);
				} else {
					handle.hide('slide', {direction: 'up'}, 500);
				}
				$('div.player embed, div.player object').removeAttr('style');
			}
			$(this).unbind('click').removeData('toggle');
		});
	}
}

function iePatch() {
//	$('ul.op li').each(function() {
//		$(this).html($(this).html().trim());
//	});
	var zi = 30;
	$('#users li.user').each(function() {
	  this.style.zIndex = zi --;
	});
	zi=999;
	$('#notices li.notice').each(function() {
	  this.style.zIndex = zi --;
	});
	zi=30;
	$('#w_nav li').each(function() {
	  this.style.zIndex = zi --;
	});
	zi=30;
	$('#main_nav li').each(function() {
		this.style.zIndex = zi --;
	});
}

function ie6Patch() {
	var selectors = [];
	var $widgets = $("#widgets");
	if ($widgets.size() > 0) {
		selectors.push('#contents div.gmusic a');
		selectors.push('#contents div.discussion_detail dl.discussions > dt, dl.answers > dt');
		selectors.push('#w_nav li span, #widgets dl.invite a');
		selectors.push('#widgets div.toolbox span em');
		selectors.push('#widgets div.group_info dl.detail > dt span');
		selectors.push('#widgets dl.grid-1 dt a,#widgets dl.grid-6 dt a,#widgets dl.faq dt a, #widgets dl.videos dt a, #widgets dl.videos dd li a');
		selectors.push('#widgets div.group_info div.profile, #widgets div.activity');
		selectors.push('#widgets dl.grid-1 dt, #widgets dl.grid-6 dt, #widgets dl.faq dt, #widgets dl.videos dt');
		$widgets.append('<div class="split"></div><dl class="widget intro"><dt>小提示</dt><dd>您还在使用IE6浏览器，该浏览器被发现存在重大安全隐患。建议使用<a target="_blank" href="http://ie.sogou.com/" title="去搜狗浏览器网站了解详情">搜狗浏览器</a>，上网更加安全快速，浏览本站也可以达到更好的显示效果。</dd></dl>');
	}
	selectors.push('.notice div.video_message em, #footer > span.lc, #footer > span.rc, body > div.anonymous, div.growlUI, div.gamesign img');
	
	if ($("#register_wrap").size() > 0) {
		selectors.push('#register_wrap div.greet p,#register_wrap div.avatar');
		selectors.push('#register_head, #register_foot, #register_wrap');
	}
	
	if ($("#public_widgets").size() > 0) {
		selectors.push('#public_widgets dl.search dd input.submit');
	}
	
	if ($("#public_contents dl.recommendpeople").size() > 0) {
		selectors.push('#public_contents dl.recommendpeople dd div.avatar');
	}
	
	if ($('#appbg').size() > 0) {
		selectors.push('#appbg');
	}
	
	if ($('#waiter').size() > 0) {
		selectors.push('#waiter a.standby');
	}
	
	$(selectors.join(',')).each(function() {
		DD_belatedPNG.fixPng(this);
	});
	
	$('div.video_message a').hover(function(){
		$('em', this).css({backgroundPosition: '-610px -45px'});
	}, function() {
		$('em', this).css({backgroundPosition: '-494px -45px'});
	});
	
	window.floatbar = document.getElementById("floatbar");
	window.waiter = document.getElementById("waiter");
	window.onscroll = function() {
		if (window.floatbar) {
			if (window.scrolling) {
				return;
			}
			window.scrolling = true;
			window.floatbar.style.display = 'none';
			setTimeout(function() {
				window.floatbar.className = window.floatbar.className;
				window.floatbar.style.display = 'block';
				window.scrolling = false;
			}, 1500);
		}
		if (window.waiter) {
			if (window.wscrolling) {
				return;
			}
			window.wscrolling = true;
			window.waiter.style.display = 'none';
			setTimeout(function() {
				window.waiter.className = window.waiter.className;
				window.waiter.style.display = 'block';
				window.wscrolling = false;
			}, 1500);
		}
	};
}

var Effect = {
	sideNav : function(element) {
		$("#w_nav li").hover(
			function() {
				var b = $(this).hasClass('active');
				if (! b) {
					$(this).addClass("on");
					if ($.browser.msie && /MSIE 6.0/.test(navigator.userAgent)) {
						var $span = $('span', this);
						if (! $span.data('bg')) {
							$span.data('bg', $span.css('backgroundImage'));
						}
						DD_belatedPNG.fixPng(
							$span.css({backgroundImage: $span.data('bg')}).get(0));
					}
				}
			},
			function() {
				var b = $(this).hasClass('active');
				if (! b) {
					$(this).removeClass("on");
					if ($.browser.msie && /MSIE 6.0/.test(navigator.userAgent)) {
						$('span', this).css({backgroundImage: 'none'});
					}
				}
			}
		);
	},
	topNav : function(element) {
		$("#main_nav > li").hover(
			function() {
				$('div.player embed, div.player object').css({width: '0px'});
				$('ul', this).show();
			},
			function() {
				$('div.player embed, div.player object').removeAttr('style');
				$('ul', this).hide();
			}
		);
	},
	button : function() {
		$(".button60, .button76, .button94, .button99, .button99")
			.live('mousedown', function() {
				$(this).css({textIndent: '1px'});
			}).live('mouseup', function() {
				$(this).css({textIndent: '0'});
			});
		$('a,button,input[type="button"],input[type="submit"]')
			.live('focus', function(){if(this.blur){this.blur();}});
	},
	widgets : function() {
		//收起展开
	    $('a.unfold, a.fold', $my.widgets).click(function() {
	    	$(this).closest('dl').find('dd').slideToggle("slow");
	    	if($(this).hasClass('unfold')) {
	    		$(this).removeClass('unfold').addClass('fold');	
	    	} else {
	    		$(this).removeClass('fold').addClass('unfold');
	    	}
	    	return false;
	    });
	},
	floatbar : function() {
		$('#floatbar a.toggle, #floatbar a.toexpand').click(function() {
			var expanded = $(".expanded", $my.floatbar).is(":visible");
			if (expanded) {
				$(".expanded", $my.floatbar).hide(0, function() {
					$(".folded", $my.floatbar).fadeIn();
				});
			} else {
				$(".folded", $my.floatbar).hide(0, function() {
					$(".expanded", $my.floatbar).fadeIn();
				});
			}
			$.ajax({
				  type: "post",
				  url: '/ajax/handlefloatbar',
				  data: {token: $('body').attr('token'), close: expanded}
			});
			return false;
		});
	},
	more : function() {
		$('div.op a.toggle').click(function() {
			var $embed = $('div.player embed, div.player object');
			if ($embed.attr('style')) {
				$embed.removeAttr('style');
			} else {
				$embed.css({width: '0'});
			}
			var handle = $('.more', $(this).parent()).toggle('slide', {direction: 'up'}, 500);
			_toggleOnDocument(handle);
			return false;
		});
	}
}

var Interval = {
	fetchRemind : function () {
		if ($my.floatbar && $my.floatbar.length > 0 && $(".expanded", $my.floatbar).is(":visible")){
			$.ajax({
				type: "get",		  
				url: '/ajax/getunreadinfo',
				data: {latest_timestamp : $my.floatbar.attr('sync')}, 
				dataType: "json",
				success: function(json){
					$my.floatbar.attr('sync', json.timestamp);
					if (json.rcnt > 0) {
						if ($my.floatbar.find('li.remind a.btn em').length > 0) {
							var newcount = parseInt($my.floatbar.find('li.remind a.btn em').text()) + parseInt(json.rcnt);
							$my.floatbar.find('li.remind a.btn em').html(newcount);
						} else {
							$my.floatbar.find('li.remind a.btn').append('<em class="cnt">' + json.rcnt + '</em>');
						}
					}
					
					if (json.remind) {
						if ($my.floatbar.find('li.remind dl.mbox').length > 0) {
							$my.floatbar.find('li.remind dl.mbox ul li.seemore').before($(json.remind).find('ul').html());
						} else {
							$my.floatbar.find('li.remind').append(json.remind);
						}
					}
					
					if (json.group) {
						if ($my.floatbar.find('li.group dl.mbox').length > 0) {
							$my.floatbar.find('li.group dl.mbox').replaceWith(json.group);
						} else {
							$my.floatbar.find('li.group').append(json.group);
						}
						$my.floatbar.find('li.group a.btn').addClass('flick');
					} else {
						$my.floatbar.find('li.group dl.mbox').remove();
						$my.floatbar.find('li.group a.btn').removeClass('flick');
					}
					
					// alert sound
					if (json.rcnt > 0 
						|| json.group) {
						if ($('#alertsound').length == 0) {
							$('body').append('<div id="alertsound" style="display:none;"></div>');
							swfobject.embedSWF('/js/player.swf', 'alertsound', '0', '0', '9', '/js/expressInstall.swf', {
								file : '/theme/default/alert.mp3',
								icons : 'false',
								autostart : 'true'
							}, {}, {
								id : 'alertsound'
							});
						} else {
							document.getElementById("alertsound").sendEvent("PLAY", "true");
						}
					}	
				}
			});
		}
	},
	reportStatus : function() {
		if ($('body').attr('anonymous') == 0) {
			$.ajax({
				type: "post",
				url: '/main/reportstatus',
				data: {token: $('body').attr('token')}
			});
		}
	}
}

var Helper = {
	// keyboard short cuts
	globalShortCut : function(event) {
		var nodeName = event.target.nodeName.toUpperCase();
		if (! event.ctrlKey && (nodeName == 'HTML' || nodeName == 'BODY')) {
			switch (event.which) {
				case 72 : window.location.href = '/home';break;
				case 71 : window.location.href = '/groups';break;
				case 90 : window.location.href = '/public';break;
				case 77 : window.location.href = '/clients';break;
				case 83 : window.location.href = '/settings/profile';break;
				case 73 : window.location.href = '/main/invite';break;
				case 81 : window.location.href = '/main/logout';break;
				case 67 : if ($my.notice_form) {$my.notice_form.find('textarea').focus();event.preventDefault();}break;
				case 191 : if ($my.floatbar) {$my.floatbar.find('input[name="q"]').focus();event.preventDefault();}break;
			}
		}
	},
	refresh : function() {
		var sharpIndex = window.location.href.lastIndexOf('#');
		window.location.href = (sharpIndex == -1 ? window.location.href : window.location.href.substr(0, sharpIndex));
		setInterval(function() {
			window.location.href = (sharpIndex == -1 ? window.location.href : window.location.href.substr(0, sharpIndex));
		}, 3000);
	}
}

$(document).ready(function(){
	// put patch on top, can accelerate ie6's render speed
	if ($.browser.msie && parseInt($.browser.version) <= 6 ) {
		ie6Patch();
	}
	
	if ($.browser.msie && parseInt($.browser.version) <= 7 ) {
		iePatch();
	}
	
	window.$my = {
		notice_form: $("#notice_form"),
		notice_textarea: $("#notice_data-text"),
		notice_text_count: $("#notice_text-count"),
		notices: $("#notices"),
		floatbar: $("#floatbar"),
		widgets: $('#widgets'),
		waiter : $('#waiter')
	};
	
	if ($my.notice_form.length > 0) {
		 processNotice();
		 
		 $my.notice_form
		 	.find('dl.topic > dd > ul')
		 		.children('li.first_tag').hover(
			 		function() {
						if (! $(this).hasClass('op_added')) {
							$(this).addClass('op_added').find('li > a').click(insertTopic);
						}
						$(this).addClass("on").children('ul').show();
					},
					function() {
						$(this).removeClass("on").children('ul').hide();
					}
				).parent()
				.children('li.self_define').hover(
					function() {
						if (! $(this).hasClass('op_added')) {
							$(this).addClass('op_added').children('a').click(insertTopicDefine);
						}
						$(this).addClass('on');
					}, 
					function() {
						$(this).removeClass('on');	
					}
				);
		 $my.notice_form
		 	.find('div.form ul').mouseover(function() {
				if (! $(this).hasClass('op_added')) {
					$(this).addClass('op_added')
						.find('a.picture').click(attachPhotoHandler).end()
					 	.find('a.music').click(attachAudioHandler).end()
					 	.find('a.video').click(attachVideoHandler).end()
					 	.find('li.insert_music a.addlink').click(addAudioUrl).end()
					 	.find('li.insert_video a.addlink').click(addVideoUrl).end()
					 	.find('li.insert_emotion a.emotion').click(function(event) {
					 		closeAllAttachDialog();
					 		newEmotionDialog('notice_emotion', $(this).parents('form').find('textarea'), {left : event.pageX, top : event.pageY});
							return false;
					 	}).end()
					 	.find('input[name="video"], input[name="audio"]').Watermark('http://').end()
					 	.find('a.close').click(closeAttachDialog).end()
					 	.find('a.insert_direct').click(insertDirectLink);
				}
			}).end()
		 	.ajaxForm(newNoticeOptions).each(addAjaxHidden);
	 }
	
	$("#notice_filter select").change(function() {
		window.location.href = $("option:selected", this).val();	
	});
	
	var $notice_filter_new = $('#notice_filter_new');
	
	if ($notice_filter_new) {
		$('a.toggle', $notice_filter_new).click(function() {
			var $form = $(this).parent().prev();
			if ($form.is(':visible')) {
				$form.hide();
			} else {
				$form.show();
			}
			return false;
		});
		
		$('form a.close', $notice_filter_new).click(function() {
			$(this).parents('form').hide();
			return false;
		});
	}
	
	$('#notices li.notice, #videoplayer').each(function() {
		// 延迟加载
		$(this).mouseover(function() {
			if (! $(this).hasClass('op_added')) {
				$(this).addClass('op_added');
				addNoticeOperation(this);
			}
		});
	});
    
    //查看更多
	$("#notice_more").click(nextPageNotices);
    
    $("a.trylogin").click(showLogin);
    
	Effect.sideNav();
	Effect.topNav();
	Effect.button();
	Effect.widgets();
	Effect.more();
	Effect.floatbar();
	
	if ($my.floatbar.length > 0) {
		$my.floatbar
			.find('li.group, li.remind').hover(
				function() {
					$dl = $(this).children('dl');
					if ($dl.size() > 0) {
						$(this).children('a').addClass('on');
						$dl.show();
					}
				},
				function() {
					$dl = $(this).children('dl');
					if ($dl.size() > 0) {
						$(this).children('a').removeClass('on');
						$dl.hide();
					}
				}
			).end()
			.find('li.remind li.seemore a').live('click', function() {
				$.ajax({
					type: "post",
					url: '/ajax/ignorereminds',
					data: {token: $('body').attr('token')},
					dataType: "json",
					success: function(json){
						if (json.result == 'true') {
							$my.floatbar.find('li.remind')
								.find('dl.mbox').remove().end()
								.find('a.btn').removeClass('on').find('em').remove();
						}
					}
				});
				return false;
			}).end()
			.find('li.group li.seemore a').live('click', function() {
				$.ajax({
					type: "post",
					url: '/ajax/ignoregroupreminds',
					data: {token: $('body').attr('token')},
					dataType: "json",
					success: function(json){
						if (json.result == 'true') {
							$my.floatbar.find('li.group')
								.find('dl.mbox').remove().end()
								.find('a.btn').removeClass('on flick').find('em').remove();
						}
					}
				});
				return false;
			});
		
		$my.floatbar.find('form.search').submit(function() {
			if ($('input.text', this).val() == '输入搜索内容') {
				$('input.text', this).focus();
				return false;
			}
		}).find('input.text').Watermark('输入搜索内容', '#bdbdbd').bind('keydown', textareaEventHandler);
		
		Interval.fetchRemind();
	}
	
	if ($my.waiter.length > 0) {
		$my.waiter.find('a').click(function() {
			$my.waiter.append('<div class="sendfaq rounded8l"><h3>您好，我是GamePub小酒保！</h3><div class="hello"><div class="avatar"><a href="/gamepub"><img src="/images/welcomeAnimal.png" alt="小酒保" width="48" height="48"/></a></div><p>不知客官您遇到什么问题？无论是功能方面的疑问，或者是活动相关的问题，我都将尽我所能为您解答！</p></div><form action="/notice/new?replyto=gamepub" method="post"><fieldset><legend>给酒保发消息</legend><span>您还可以输入<em>280</em>个字</span><textarea name="status_textarea" style="overflow-y: auto;" rows="3" cols="45"></textarea><div class="op"><input class="confirm silver76 button76" type="submit" value="提问" name="status_submit" /><a class="cancel silver76 button76" href="#">先下去候着</a></div><input type="hidden" value="1" name="newreply" /><input type="hidden" name="token" value="' + $('body').attr('token') + '" /></fieldset></form></div>');
			
			$("form", $my.waiter).ajaxForm(newReplyOptions).each(addAjaxHidden);
			  
			$("form textarea", $my.waiter)
				.bind("keyup paste", function() {
					counterArray(this, $("span em", $(this).parent()), 2);
				})
				.bind("keydown", textareaEventHandler).focus();
			$my.waiter.find("a.cancel").click(function() {
				$(this).parents("div.sendfaq").hide("slide", {direction : "right"}, 1000, function() {
					$(this).remove();
				})
				return false;
			});
			return false;
		});
	}
	
	setInterval(Interval.fetchRemind, 60000);
	setInterval(Interval.reportStatus, 300000);
	
	$(document).bind('keydown', Helper.globalShortCut);
});
