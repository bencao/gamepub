$(document).ready(function(){
	if ($('body').attr('anonymous') == '0') {
		$('a.say').click(noticeSay);
	}
});

function noticeSay() {

	  if (window.replying) {
		  return false;
	  }
	  window.replying = true;
	  
	 
	  var content = $(this).attr('topic');
	  
	  // 每28个字换行，每行17px
	  var height = 190 + content.length/28*17;
	  
	  var replyinputer =
			  '<div class="dialog_body"><form class="reply" action="' + $(this).attr('href') + '" method="post">' +
			  '<fieldset><legend>我也说两句</legend>' + 
			  '<div class="simple_form"><span>您还可以输入<em>280</em>个字</span>'+
			  '<textarea id="notice_data-text" name="status_textarea" style="overflow-y: auto;" rows="3" cols="45">'+'['+content+'] '+'</textarea></div>';
	  replyinputer += '<div class="op"><input class="confirm button60" type="submit" value="确定" name="status_submit" id="notice_action-submit2"></input>' +
	  		  '<a class="cancel button60" href="#">取消</a></div>'+
	  		  '<input type="hidden" value="1" name="replyinbox"></input>'+
			  '<input type="hidden" value="' + $('body').attr('token') + '" name="token"></input>';
	  
	  replyinputer += '<input type="hidden" value="1" name="newreply"></input>'+
		'</fieldset></form></div>';
	  
	  var arrPageSizes = getPageSize();
	  var top = arrPageSizes[3]/3;
	  var left = arrPageSizes[2]/3;
		
	  $('div.player embed').css({width: '0'});
	  var replydlg = $(replyinputer).dialog({width : 396, height : height, title: "我来说两句", position: [left, top], draggable : true, resizable : false,
		  	close: function(event, ui) {$('div.player embed').removeAttr('style');$(this).dialog('destroy').remove();window.replying = false;return false;}});
	  
	  $('a.cancel', replydlg).click(function() {
			$(this).parents('div.dialog_body').dialog('close');
			return false;
	  });
	  
	  selectEnd(document.getElementById("notice_data-text"));
	  $("form", replydlg).ajaxForm(newReplyOptions).each(addAjaxHidden);
	  
	  $("form textarea", replydlg)
		.bind("keyup paste", function() {
			counterArray(this, $("span em", $(this).parent()), 2);
		})
		.bind("keydown", function(event) {
			if (event.ctrlKey && event.keyCode == 13) {
				$(this).parents("form").submit(); 
				event.preventDefault();
				event.stopPropagation();
			}
		}).focus();
	  return false;
	}