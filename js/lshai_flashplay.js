$(document).ready(function() {
	$('dl.info a.toop').mouseover(function() {
		$('dl.info').hide();
		$('dl.op').show();
	});
	
	$('dl.op a.toinfo').mouseover(function() {
		$('dl.op').hide();
		$('dl.info').show();
	});
	
	$('div.player ul')
		.find('a.reset').click(function() {
			location.href = document.URL;
			return false;
		}).end()
		.find('a.todiscuss').click(function() {
			$('form.mydiscussion textarea').focus();
			return false;
		}).end()
		.find('a.recommend').click(sentRecommondation).end()
		.find('a.fullscreen').click(function() {
			var w = window.open('/flash/fullscreen?fid=' + $('#mini').attr('fid'), 'flashplay', 'fullscreen=1,menubar=0,toolbar=0,directories=0,location=0,status=0,scrollbars=0');
			w.focus();
			return false;
		});
	
	// 点击复制
	ZeroClipboard.setMoviePath( '/js/ZeroClipboard.swf' );
	var clip = new ZeroClipboard.Client();
	clip.setHandCursor( true );
	clip.addEventListener('mouseOver', function() {
		$(document).data('clip').setText(window.location.href);
	});
	clip.addEventListener('complete', function() {
		fadeSuccess('已成功复制');
	});
	clip.glue('copyurl');//用id名
	$(document).data('clip', clip);
	
	function sentRecommondation() {
	  var html = '<div class="dialog_body">' +
				  '<form class="suggestto" action="' + $(this).attr('href') + '" method="post">' +
				  '<fieldset><legend>推荐</legend><p>推荐将作为一条新消息发给关注您的人，跟他们说说您的推荐理由吧</p>'+
				  '<div class="simple_form"><span>您还可以输入 <em class="re_count">280</em>个字</span>'+
				  '<textarea name="status_textarea">有一个叫做' + $('dl.info h2').text() + '的小游戏(' + window.location.href + ')很有趣，快来看看吧</textarea></div>'+
				  '<div class="op"><input type="submit" value="确定" class="confirm button60"></input>' +
				  '<a class="cancel button60" href="#">取消</a></div>'+
				  '<input type="hidden" value="' + $('body').attr('token') + '" name="token"></input>' +
				  '</fieldset></form></div>';
	  
	  var arrPageSizes = getPageSize();
	  var top = arrPageSizes[3]/3;
	  var left = arrPageSizes[2]/3;
	  
	  $('div.player embed, div.player object').css({width: '0'});
	  var dlg = $(html).dialog({width : 396, height : 240, draggable : true, resizable : false,
		    title: "把小游戏推荐给朋友", position : [left, top],
		  	close: function(event, ui) {$('div.player embed, div.player object').removeAttr('style');$(this).dialog('destroy').remove();return false;}});
	  
	  $('a.cancel', dlg).click(function() {
		$(this).parents('div.dialog_body').dialog('close');
		return false;
	  });
		
	  $("form", dlg).ajaxForm({
		  dataType : 'json',
		  beforeSubmit : function(formData, jqForm, options) {
		  	var textLength = $("textarea", jqForm).val().length;
		  	if (textLength == 0) {
				jqForm.addClass("warning");
				alertFail("请输入您想对好友说的话");
				return false;
			} else if (textLength > 280) {
				alertFail('推荐信息太长，最长为280字');
				return false;
			}
			jqForm.addClass("processing");
			$("input.confirm", jqForm).attr("disabled", "disabled").addClass("disabled");
			return true;
	  	  },
	  	  success: function(json) {
			$("div.dialog_body").dialog('close');
			fadeSuccess('推荐发送成功');
		  }  
	  }).each(addAjaxHidden);
		
	  $("form textarea", dlg)
		.bind("keyup paste", function() {
			counterArray(this, $("span em", $(this).parent()), 8);
		})
		.bind("keydown", textareaEventHandler).focus().keyup();
	  return false;
	}
	
	$("form.subscribe").ajaxForm({
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
			$("form.subscribe").replaceWith('<a class="subscribed" title="已关注"></a>');
			fadeSuccess('已关注');
		}
	}).each(addAjaxHidden);
});

