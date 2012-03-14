$(document).ready(function(){	
	$("a.group_post_del").click(function() {
		if(confirm('您确定要删除公告吗?')){
			ajaxDelPost($(this).attr('href'));
		}
		return false;
	});

	$("a.deletegroup").click(function() {
		if (!confirm("您是这个" + $("a.deletegroup").text().substr(2,2) + "的拥有者，确定删除它吗？")){
			return false;
		}
		ajaxDeleteGroup($(this).attr('href'));
		return false;
	});
	
	$("a.leavinggroup").click(function() {
    	if (!confirm("您确认从这个" + $("a.leavinggroup").text().substr(2,2) + "退出吗？")){
			return false;
		}
		ajaxLeaveGroup($(this).attr('href'));
		return false;
	});
	
	function ajaxDeleteGroup(url) {
		$.ajax({
			type : 'POST',
			url : url,
			data : {token : $('body').attr('token'), ajax : '1'},
			dataType: 'json',
			success: function(json) {
				fadeSuccess("删除成功");
	        	window.location.href=json.tourl;
			},
			error: function(xml) {
				 var rtext = $("div.error", xml.responseText).html();
				 alertFail(rtext);
			}
		});
	}
	
	function ajaxLeaveGroup(url) {
		$.ajax({
			type : 'POST',
			url : url,
			data : {token : $('body').attr('token'), ajax : '1'},
			dataType: 'json',
			success: function(json) {
				fadeSuccess("退出成功");
	        	window.location.href=json.tourl;
			},
			error: function(xml) {
				 var rtext = $("div.error", xml.responseText).html();
				 alertFail(rtext);
			}
		});
	}
	
	function ajaxDelPost(url){
		$.ajax({
			type : 'POST',
			url : url,
			data : {token : $('body').attr('token'), ajax : '1'},
			dataType: 'json',
			success: function(json) {
				$('#bubbles').remove();
				fadeSuccess("公告删除成功");
			},
			error: function(xml) {
				 var rtext = $("div.error", xml.responseText).html();
				 alertFail(rtext);
			}
		});
	}
	
	$('#disply_btn').click(function(){
		$('#detail_info').slideToggle("slow");
		$('#disply_btn').hide();
		return false;
	});
	$('#undisply_btn').click(function(){
		$('#detail_info').slideToggle("slow");
		$('#disply_btn').show();
		return false;
	});
});