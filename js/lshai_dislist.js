$(document).ready(function(){
	$('form.mydiscussion').ajaxForm(newDissDetailOptions).each(addAjaxHidden)
		.find('textarea').bind("keydown", textareaEventHandler).end()
		.find('a.emotion').click(function(event) {
			newEmotionDialog('dislist_emotion', $(this).parents('form').find('textarea'), {left : event.pageX, top : event.pageY});
			return false;
		});
	
	$('dl.discussions li.notice a.toggle').click(function(){ 			    		
		 $('form.mydiscussion textarea').val('@' + $(this).attr('toreply') + ' ');
		 $('form.mydiscussion textarea').focus();
		 return false;
	 });
	
	 $('dl.discussions li.notice a.delete').click(deleteDetailDiscuss);
	 
	 addNoticeOperation($('div.discussion_detail div.notice'));
});

function deleteDetailDiscuss() {
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
		  alertFail("对不起, 在删除中中遇到一些问题, 请稍后再试");
		  window.deletingdiscuss = false;
	  },
	  success: function(json){
		  if (json.result == 'success') {
			  var parentOl = $('dl.discussions li[did="' + json.did + '"]').parent();
			  var span = $('dl.discussions dt span');
			  span.text(parseInt(span.text()) - 1);
			  
			  $('li.notice[did="' + json.did + '"]').fadeOut("slow", function() {
				  var pol = $(this).parents('ul');
				  $(this).remove();
			  });
			  
			  fadeSuccess("删除评论成功");
		  } else {
			  alertFail('删除评论失败');
		  }
		  window.deletingdiscuss = false;
	  }
	});
	return false;
}

var newDissDetailOptions = {
	dataType: 'json',
	beforeSubmit: function(formData, jqForm, options) { 
		if ($('textarea', jqForm).val().length == 0) {
			alertFail("请输入您想说的话", '提示', function() {
				$('textarea', jqForm).focus();
			});
			return false;
		}
		jqForm.addClass("processing");
		$('input.submit', jqForm).attr("disabled", "disabled").addClass("disabled");
		return true;
	},
	success: function(json) {
		var disnotice = $(json.html).get(0);
		var did = $(disnotice).hide().attr('did');
		var parentUl = $('dl.discussions dd > ul');

    	$(parentUl).prepend(disnotice);
		
    	$('li[did="' + did + '"] a.toggle', parentUl).click(function() {
    		$('form.mydiscussion textarea',dis).val('@' + $(this).attr('toreply') + ' ');
    		$('form.mydiscussion textarea',dis).focus();
    		return false;
    	});
    	$('li[did="' + did + '"] a.delete', parentUl).click(deleteDetailDiscuss);
    	$('li[did="' + did + '"]', parentUl).slideDown('slow');
    	
		$('form.mydiscussion').removeClass("processing");
		$('form.mydiscussion input.submit').removeAttr("disabled").removeClass("disabled");
		$('form.mydiscussion textarea').val("").focus();
		$('dl.discussions dt span').text(parseInt($('dl.discussions dt span').text()) + 1);
		
		updateTimeAgo();
	}
}