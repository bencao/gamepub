$(document).ready(function(){
	$('ul.hot-switch a').click(function() {
		if ($(this).hasClass('active')) {
			return false;
		}
		$.ajax({
			url : $(this).attr('href'),
			dataType : 'json',
			success : function (json) {
				if (json.result == 'true') {
					if ($('#notices').length > 0) {
						$('#notices').replaceWith(json.notices);
					} else {
						$('ul.hot-switch').after(json.notices);
					}
					$('#notices li').each(function() {
						$(this).mouseover(function() {
							if (! $(this).hasClass('op_added')) {
								$(this).addClass('op_added');
								addNoticeOperation(this);
							}
						});
					});
					$('ul.hot-switch a').removeClass('active').each(function() {
						if ($(this).attr('which') == json.which) {
							$(this).addClass('active');
						}
						if (json.which == 'latest') {
							$('div.hot-switch-wrap a.refresh').show(0).each(function() {
								if (! $(this).hasClass('op_added')) {
									$(this).addClass('op_added').click(function() {
										$.ajax({
											url : $(this).attr('href'),
											dataType : 'json',
											success : function (json) {
												if (json.result == 'true') {
													if ($('#notices').length > 0) {
														$('#notices').replaceWith(json.notices);
													} else {
														$('ul.hot-switch').after(json.notices);
													}
													$('#notices li').each(function() {
														$(this).mouseover(function() {
															if (! $(this).hasClass('op_added')) {
																$(this).addClass('op_added');
																addNoticeOperation(this);
															}
														});
													});
													
													if ($('#notice_more').length > 0) {
														if (json.pg) {
															$('#notice_more').replaceWith(json.pg);
														} else {
															$('#notice_more').remove();
														}
													} else {
														if (json.pg) {
															$('#notices').after(json.pg);
														}
													}
													
													$("#notice_more").unbind('click').click(nextPageNotices);
													
													if ($.browser.msie && parseInt($.browser.version) <= 7 ) {
														iePatch();
													}
												}
											}
										});
										return false;
									});
								}
							});
						} else {
							$('div.hot-switch-wrap a.refresh').hide(0);
						}
					});
					
					if ($('#notice_more').length > 0) {
						if (json.pg) {
							$('#notice_more').replaceWith(json.pg);
						} else {
							$('#notice_more').remove();
						}
					} else {
						if (json.pg) {
							$('#notices').after(json.pg);
						}
					}
					
					$("#notice_more").unbind('click').click(nextPageNotices);
					
					if ($.browser.msie && parseInt($.browser.version) <= 7 ) {
						iePatch();
					}
				}
			}
		});
		return false;
	});
	
	$('dl.sugg').find('a.rollleft').click(function() {
		if (window.rolling) {
			return false;
		}
		var $ul = $(this).parent().find('ul');
		var left = $ul.css('left');
		if (left != '0px') {
			window.rolling = true;
			$ul.animate({left : '+=157'}, 1000, function() {
				window.rolling = false;
			});
		}
		return false;
	}).end().find('a.rollright').click(function() {
		if (window.rolling) {
			return false;
		}
		var $ul = $(this).parent().find('ul');
		var left = $ul.css('left');
		if (left != '-1099px') {
			window.rolling = true;
			$ul.animate({left : '-=157'}, 1000, function() {
				window.rolling = false;
			});
		}
		return false;
	});
	
	window.getTopicNotice = function() {
		$.ajax({
			url : '/ajax/publictopicnotice',
			dataType : 'json',
			data : {q : $('a.say').attr('topic')},
			success : function(json) {
				if (json != null) {
					$('div.related_notice').fadeOut('slow', function() {
						$(this).html(json.html).each(function() {
							$("form.subscribe", this).ajaxForm({ 
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
									var subinfo =  	$("div.sub_info", $my.widgets);
									if (subinfo.hasClass('isown')) {
							  	   		$("a.subscription span", subinfo).text(parseInt($("a.subscription span", subinfo).text()) + 1);
									} else {
										$("a.subscriber span", subinfo).text(parseInt($("a.subscriber span", subinfo).text()) + 1);
									}
							  	   	
									$("dl.people dd li[pid='" + json.pid + "']").find("form.subscribe").remove().end().append('<div class="subscribed" title="已关注"></div>');
									$("ol.users li[pid='" + json.pid + "']").find("form.subscribe").remove().end().find("div.op").prepend('<div class="done">已关注</div>');
									$("div.card div.op").find("form.subscribe").replaceWith('<div class="subscribed">关注中</div>');
									
							  	   	var container = $("#users li[pid='" + json.pid + "'], #owner_summary");
									$("form.subscribe", container).remove();
									
									$("#users li[pid='" + json.pid + "'] div.op").prepend('<div class="done">已关注</div>');
									$("#owner_summary").append('<div class="subscribed" title="已关注"></div>');
									$("div.op ul.more", container).prepend('<li><a class="unsubscribe" url="' + json.action + '" token="' + $('body').attr('token') + '" to="' 
											+ json.pid + '" href="#">取消关注</a></li>')
									
									$("a.unsubscribe", container).click(function() { 
										ajaxUnsubscribe($(this).attr('url'), $(this).attr('to'));
										$(this).parents('.more').hide();
										return false; 
									});
									
									ajaxTagOther(json.tagotherurl, json.pid, '1');
								}
							}).each(addAjaxHidden);
						});					
					}).fadeIn('slow');
				}
			}
		});
	}
	window.getTopicNotice();
	
	window.topicnoticehandle = setInterval("window.getTopicNotice()", 10000);
	
//	$('div.related_notice').mouseover(function() {
//		clearInterval(window.topicnoticehandle);
//	}).mouseout(function() {
//		window.topicnoticehandle = setInterval("window.getTopicNotice()", 5000);
//	});
	
	function ajaxTagOther(url, to, issub) {
		$.ajax({
			type : 'GET',
			url : url,
			data : {to : to, ajax : '1', issub : issub},
			dataType: 'json',
			success: function(json) {
				var dialogContent = json.html;
				var arrPageSizes = getPageSize();
				var top = arrPageSizes[3]/3;
				var left = arrPageSizes[2]/3;
				$('div.player embed').css({width: '0'});
				$(dialogContent).dialog({title : "修改用户分组", width: 397, position : [left, top], draggable : true, resizable : false,
					close: function(event, ui) {$('div.player embed').removeAttr('style');$(this).dialog('destroy').remove();return false;}});
				
				$('a.create').click(function() {
					$(this).hide();
					$('div.create_new').show();
					return false;
				});
				$('div.create_new a.tocreate').click(function() {
					var self = $(this);
					var parent = self.parent();
					$.ajax({
						type : 'POST',
						url : self.attr('url'),
						dataType : 'json',
						data : {tname : $('input[name="tname"]', parent).val(), token : $('body').attr('token'), create : '1'},
						success : function(json) {
							if (json.result == 'true') {
								$('ul.checkboxes').append('<li><input type="checkbox" class="checkbox" id="newtag' + json.tid + '" value="' + json.tid + '" checked="checked" /><label for="newtag' + json.tid + '">' + json.tag + '</label></li>');
								$('div.create_new').hide();
								$('a.create').show();
							} else {
								alertFail(json.msg);
							}
						}
					});
					return false;
				});
				$('div.create_new a.tocancel').click(function() {
					$('div.create_new').hide();
					$('a.create').show();
					return false;
				});
				
				$('div.dialog_body a.cancel').click(function() {
					var dialog_body = $(this).parents('div.dialog_body');
					if ($('input[name="issub"]', dialog_body).val() == '1') {fadeSuccess('关注成功');}
					dialog_body.dialog('close');
					return false;
				});
				
				$('form.tagother').ajaxForm({
					dataType : 'json',
					beforeSubmit: function(formData, jqForm, options) {
						if (jqForm.hasClass('processing')) {
							return false;
						}
						jqForm.addClass("processing");
						$("input.confirm", jqForm).attr("disabled", "disabled").addClass("disabled");
						return true;
					},
					success : function(json) {
						if (json.result == 'true') {
							$('#users li[pid="' + json.to + '"] p.pgroup').text('所属分组: ' + json.tags);
							
							var form = $("#tagotherfor" + json.to);
							form.removeClass("processing").parents('div.dialog_body').dialog('close');
							
							fadeSuccess(json.msg);
						} else {
							alertFail('修改分组的时候出错了');
						}
					}
				});
			}
		});	
	}
	
});