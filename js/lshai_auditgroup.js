$(document).ready(function(){
	$('a.cancelaudit').click(function(){
		var group_alias = $('dl.grid-3:eq(0) dt').text().replace(/(\s*$)/g, "");
		group_alias = group_alias.substr(group_alias.length - 2, 2);
		var gid = $(this).attr('gid');
		
		if (!confirm("您确定要撤销该" + group_alias + "吗？")){
			return false;
		}
		
		$.ajax({
			type : 'POST',
			url : $(this).attr('href'),
			data : {ajax : '1', token : $('body').attr('token'), id : gid},
			dataType: 'json',
			success: function(json) {
				if (json.result == 'true') {
					var $group = $('li#group-' + gid);
					if($group.length > 0) {
						var group_num = $('dl.grid-3:eq(0) li').length;
						if (group_num == 1) {
							$('dl.grid-3:eq(0) dd').replaceWith('<dd><div class="instruction guide"><p>您没有待确认的' + group_alias + '。</p></div></dd>');
						} else {
							$group.fadeOut("slow").remove();
						}
					}
					fadeSuccess("撤销" + group_alias + "成功");
				} else {
					alertFail("撤销" + group_alias + "失败!");
				}
			},
			error: function(xml) {
				 var rtext = $("div.error", xml.responseText).html();
				 alertFail(rtext);
			}
		});
		return false;
	});
});