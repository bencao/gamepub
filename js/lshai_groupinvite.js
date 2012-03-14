$(document).ready(function(){	
	//==================== Clip Board ==================== //
	ZeroClipboard.setMoviePath( '/js/ZeroClipboard.swf' );
	var clip = new ZeroClipboard.Client();
	clip.setHandCursor( true );
	clip.addEventListener('mouseOver', function() {
		$(document).data('clip').setText($("#ivlink").val());
	});
	clip.addEventListener('complete', function() {
		fadeSuccess('已成功复制');
	});
	clip.glue('ivbtn');
	$(document).data('clip', clip);
	//==================== Clip board ==================== //
	
	function ajaxBatchInvite(url, userid, ids) {
		$.ajax({
			type : 'POST',
			url : url,
			data : {token : $('body').attr('token'), profileid: userid, batchid: ids, ajax : '1'},
			dataType: 'json',
			success: function(json) {
				$('#users li div.op').html('<div class="done">已发送</div>');
				$('a.batchinvite').replaceWith('<div>本页好友已邀请<div>');
				fadeSuccess('邀请成功');
			},
			error: function() {
				 alertFail('发生未知错误，发送邀请失败！');
			}
		});
	}

	var groupInviteoptions = {
		dataType: 'json',
		data : {ajax : '1'},
		success: function(json) {
			$('#users li.user[pid="' + json.pid + '"] div.op').html('<div class="done">已发送</div>');
			fadeSuccess('邀请成功');
		},
        error: function() {
			alertFail('发生未知错误，发送邀请失败！');
		}
	};
	
	
	if($('#batchinvite_ids').val().length == 0){
		$('a.batchinvite').replaceWith('<div>本页好友已邀请<div>');
	} else {
		$('a.batchinvite').click(function(){
			ajaxBatchInvite($(this).attr('href'), $(this).attr('userid'), $('#batchinvite_ids').val());
			return false;
		});
	}
	$("form.form_group_invite").ajaxForm(groupInviteoptions).each(addAjaxHidden);
});