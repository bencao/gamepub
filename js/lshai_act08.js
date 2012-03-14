$(document).ready(function() {
	$('form.mycomment').ajaxForm({
		dataType : 'json',
		beforeSubmit : function(data, jForm, option) {
			if (window.issending) {
				alertFail('发送中，请稍候');
				return false;
			}
			if (jForm.find('input[name="nn"]').val().trim() == '') {
				alertFail('请输入您的名字', '提示', function() {
					jForm.find('input[name="nn"]').focus();
				});
				return false;
			}
			if (jForm.find('textarea').val().trim() == '') {
				alertFail('请输入评论内容', '提示', function() {
					jForm.find('textarea').focus();
				});
				return false;
			}
			window.issending = true;
			return true;
		},
		success : function(json) {
			if (json.result == 'false') {
				alertFail(json.msg);
			} else {
				$('form.mycomment').find('textarea').val('');
				$('ol.sounds').prepend('<li><p class="info"><strong>' + json.nn + '</strong><span class="time">就在刚才</span></p>' 
						+ '<p class="msg">' + json.nc + '</p></li>');
			}
			window.issending = false;
		}
	});
	
	$('a.dojoin').click(function() {
		if (confirm('您确认参与今日的《游戏人的一天》记录活动吗？')) {
			$.ajax({
				type : 'post',
				url : $(this).attr('href'),
				data : {token : $('body').attr('token')},
				dataType : 'json',
				success : function(json) {
					if (json.result == 'true') {
						fadeSuccess('报名成功');
					} else {
						alertFail(json.msg);
					}
				}
			});
		}
		return false;
	});
	
	function getTimeline(url, who) {
		$.ajax({
			url : url,
			dataType : 'json',
			data : {who : who},
			success : function(json) {
				if (json.result == 'true') {
					$('#notices').replaceWith(json.html);
					$('#notices li').each(function() {
						$(this).mouseover(function() {
							if (! $(this).hasClass('op_added')) {
								$(this).addClass('op_added');
								addNoticeOperation(this);
							}
						});
					});
				}
			}
		});
	}
	$('p.swit').find('a.star').click(function() {
		if (! $(this).hasClass('active')) {
			getTimeline($(this).attr('href'), 'star');
			$(this).addClass('active').next().removeClass('active');
		}
		return false;
	}).end().find('a.other').click(function() {
		if (! $(this).hasClass('active')) {
			getTimeline($(this).attr('href'), 'other');
			$(this).addClass('active').prev().removeClass('active');
		}
		return false;
	});
	
	if ($.browser.msie && parseInt($.browser.version) <= 6 ) {
		$('#awrap #notices li.notice div.avatar').each(function() {
			DD_belatedPNG.fixPng(this);
		});
	}
});