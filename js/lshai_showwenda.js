$(document).ready(function() {
	$('a.toanswer').click(function() {
		var $myanswer = $('dl.myanswer');
		if (! $myanswer.is(':visible')) {
			$myanswer.show('slow', function() {
				if (! $(this).hasClass('op_added')) {
					$(this).addClass('op_added').find('form').ajaxForm({
						dataType : 'json',
						success : function(json) {
							if (json.result == 'true') {
								$('dl.answers dt span').text(parseInt($('dl.answers dt span').text()) + 1);
								$('dl.answers dd > ul').append(json.html);
								$('dl.answers dd > ul > li:last').each(addLiOperations);
								$('dl.myanswer').hide('fast');
							} else {
								alertFail(json.msg);
							}
						}
					}).find('textarea').unbind("keydown").bind("keydown", textareaEventHandler).keyup().focus().each(function() {
						selectEnd(this);
					});
				}
			});
		} else {
			$myanswer.find('textarea').focus();
		}
		return false;
	});
	
	var addLiOperations = function() {
		$(this).find('ul.op')
		.find('li.bestanswer a').click(function() {
			if (confirm('设置此问题为最佳答案后，问题将关闭，是否继续？')) {
				$.ajax({
					url : $(this).attr('href'),
					dataType : 'json',
					type : 'post',
					data : {token : $('body').attr('token'), aid : $(this).attr('aid')},
					success : function(json) {
						if (json.result == 'true') {
							Helper.refresh();
						} else {
							alertFail(json.msg);
						}
					}
				});
			}
			return false;
		}).end()
		.find('li.modifyanswer a').click(function() {
			if (window.answermodifying) {
				return false;
			}
			window.answermodifying = true;
			
			var oldcontent = $(this).parents('li.notice').children('div.content').text();
			var html = '<div class="dialog_body"><form class="reply" action="' + $(this).attr('href') + '" method="post">' 
			  + '<fieldset><legend>回复</legend>'
			  + '<div class="simple_form"><span>您还可以输入<em>280</em>个字</span>'
			  + '<textarea name="content" style="overflow-y: auto;" rows="3" cols="45">' + oldcontent + '</textarea></div>'
			  + '<div class="option"><input class="checkbox" id="addInbox" type="checkbox" name="replyinbox" value="1"></input>'
			  + '<label for="addInbox">作为一条新消息</label></div>'
			  + '<div class="op"><input class="confirm button60" type="submit" value="确定"></input>' 
			  + '<a class="cancel button60" href="#">取消</a></div>'
			  + '<input type="hidden" value="' + $(this).attr('aid') + '" name="aid"></input>'
			  + '<input type="hidden" value="' + $('body').attr('token') + '" name="token"></input>'
			  + '</fieldset></form></div>';
			
			var arrPageSizes = getPageSize();
			var top = arrPageSizes[3]/3;
			var left = arrPageSizes[2]/3;
			var modifydlg = $(html).dialog({width : 396, height : 220, title: "修改回复", position: [left, top], draggable : true, resizable : false, 
				  	close: function(event, ui) {$(this).dialog('destroy').remove();window.answermodifying = false;return false;}});
			$('a.cancel', modifydlg).click(function() {
				$(this).parents('div.dialog_body').dialog('close');
				return false;
			});
			$("form", modifydlg).ajaxForm({
				dataType : 'json',
				success : function(json) {
					if (json.result == 'true') {
						$('dl.answers li[aid="' + json.aid + '"]').find('div.content').text(json.newcontent);
						$('div.dialog_body').dialog('close');
						alertSuccess('修改成功');
					} else {
						alertFail(json.msg);
					}
				}
			}).each(addAjaxHidden);
			
			$("form textarea", modifydlg)
				.bind("keyup paste", function() {
					counterArray(this, $("span em", $(this).parent()), 2);
				})
				.bind("keydown", textareaEventHandler).focus();
			  
			return false;
		}).end()
		.find('li.usefulanswer a').click(function() {
			return false;
		});
	};
	
	$('dl.answers > dd > ul > li').each(addLiOperations);
	
});