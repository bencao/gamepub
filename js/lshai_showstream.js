$(document).ready(function(){
	if ($('#get_copy').length > 0) {
		//==================== Clip Board ==================== //
		ZeroClipboard.setMoviePath( '/js/ZeroClipboard.swf' );
		var clip = new ZeroClipboard.Client();
		clip.setHandCursor( true );
		clip.addEventListener('mouseOver', function() {
			$(document).data('clip').setText($("#profile_url").attr('href'));
		});
		clip.addEventListener('complete', function() {
			fadeSuccess('已成功复制');
		});
		clip.glue('get_copy');//用id名
		$(document).data('clip', clip);
		//==================== Clip Board ==================== //
	}
	
	$('a.show_more').click(function() {
		var p = $(this).parents('p');
		$('span.short', p).hide();
		$('span.full', p).show('slide', {direction: 'up'}, 100);
	});
	$('a.hide_more').click(function() {
		var p = $(this).parents('p');
		$('span.full', p).hide('slide', {direction: 'up'});
		$('span.short', p).show('slide', {direction: 'down'}, 50);
	});
	
	//推荐
    $("#owner_summary a.suggest").click(sentRecommondation);
	
	function sentRecommondation() {
	  var html = '<div class="dialog_body">' +
				  '<form class="suggestto" action="' + $(this).attr('href') + '" method="post">' +
				  '<fieldset><legend>推荐</legend><p>推荐将作为一条新消息发给关注您的人，跟他们说说您的推荐理由吧</p>'+
				  '<div class="simple_form"><span>您还可以输入 <em class="re_count">280</em>个字</span>'+
				  '<textarea name="status_textarea">' + $(this).attr('nickname') + ' 的空间(' + $(this).attr('link') + ')很有趣，快来看看吧</textarea></div>'+
				  '<div class="op"><input type="submit" value="确定" class="confirm button60"></input>' +
				  '<a class="cancel button60" href="#">取消</a></div>'+
				  '<input type="hidden" value="' + $('body').attr('token') + '" name="token"></input>'+
				  '<input type="hidden" value="' + $(this).attr('who') + '" name="suggestwho"></input>'+
				  '</fieldset></form></div>';
	  
	  var arrPageSizes = getPageSize();
	  var top = arrPageSizes[3]/3;
	  var left = arrPageSizes[2]/3;
	  
	  $('div.player embed, div.player object').css({width: '0'});
	  var dlg = $(html).dialog({width : 396, height : 240, draggable : true, resizable : false,
		    title: "把" + $(this).attr('nickname') + "推荐给朋友", position : [left, top],
		  	close: function(event, ui) {$('div.player embed, div.player object').removeAttr('style');$(this).dialog('destroy').remove();return false;}});
	  
	  $('a.cancel', dlg).click(function() {
		$(this).parents('div.dialog_body').dialog('close');
		return false;
	  });
		
	  $("form", dlg).ajaxForm(suggestOptions).each(addAjaxHidden);
		
	  $("form textarea", dlg)
		.bind("keyup paste", function() {
			counterArray(this, $("span em", $(this).parent()), 8);
		})
		.bind("keydown", textareaEventHandler).focus().keyup();
	  return false;
	}
	
	var suggestOptions = {
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
//		error : function(xml) {
//			alertFail('推荐出错，请稍后再试');
//		},
		success: function(json) {
			$("div.dialog_body").dialog('close');
			fadeSuccess('推荐发送成功');
		}
	};
	
});
