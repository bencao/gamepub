$(document).ready(function() {
	$('#missions li a.able').live('click', function() {
		var self = $(this);
		var html = '<div class="smission">' 
			+ '<h2>' + self.find('strong').text() + '</h2>'
			+ '<h3>任务描述：</h3>'
			+ '<p>' + self.attr('desc') + '</p>'
			+ '<h3>限制条件：</h3>'
			+ '<p>' + self.attr('lim') + '</p>'
			+ '<h3>任务奖励：</h3>'
			+ '<p>' + self.attr('award') + '</p>'
			+ '<a href="#" class="button76 orange76 accept" cz="' + self.attr('cz') + '">接受任务</a>'
			+ '<a href="#" class="close"></a>'
			+ '</div>';
		$.unblockUI({ 
            onUnblock: function(){
				$.blockUI({
					css : {border : 0, background: 'none', top : '20%', left : '40%'},
					message : html,
					fadeIn : 0
				});
				
			}
        });
		return false;
	});
	
	$('#missions li a.fin').live('click', function() {
		var self = $(this);
		var html = '<div class="smission">' 
			+ '<h2>' + self.find('strong').text() + '(已完成)</h2>'
			+ '<h3>任务描述：</h3>'
			+ '<p>' + self.attr('desc') + '</p>'
			+ '<h3>限制条件：</h3>'
			+ '<p>' + self.attr('lim') + '</p>'
			+ '<h3>任务奖励：</h3>'
			+ '<p>' + self.attr('award') + '</p>'
			+ '<a href="#" class="close"></a>'
			+ '</div>';
		$.unblockUI({ 
            onUnblock: function(){
				$.blockUI({
					css: {border : 0, background: 'none', top : '20%', left : '40%'},
					message : html,
					fadeIn : 0
				});
				
			}
        });
		return false;
	});
	
	$('#missions li a.ing').live('click', function() {
		var self = $(this);
		$.ajax({
			url : '/ajax/awardmission',
			dataType : 'json',
			type : 'POST',
			data : {cz : self.attr('cz'), token : $('body').attr('token')},
			success : function(json) {
				if (json.result == 'true') {
					var html = '<div class="smission">' 
						+ '<h2>' + self.find('strong').text() + '</h2>'
						+ '<h3>任务奖励：</h3>'
						+ '<p>' + json.msg + '</p>'
						+ '<a href="#" class="button76 green76 confirm" cz="' + self.attr('cz') + '">确定</a>'
						+ '<a href="#" class="closing" cz="' + self.attr('cz') + '"></a>'
						+ '</div>';
					
				} else {
					var html = '<div class="smission">' 
						+ '<h2>' + self.find('strong').text() + '(尚未完成)</h2>'
						+ '<h3>任务描述：</h3>'
						+ '<p>' + self.attr('desc') + '</p>'
						+ '<h3>限制条件：</h3>'
						+ '<p>' + self.attr('lim') + '</p>'
						+ '<h3>任务奖励：</h3>'
						+ '<p>' + self.attr('award') + '</p>'
						+ '<a href="#" class="close"></a>'
						+ '</div>';
				}
				$.unblockUI({ 
		            onUnblock: function(){
						$.blockUI({
							css: {border : 0, background: 'none', top : '20%', left : '40%'},
							message : html,
							fadeIn : 0
						});
						
					}
		        });
			}
		});
		
		return false;
	});
	
	$('#missions a.prev, #missions a.next').live('click', function() {
		$.ajax({
			url : $(this).attr('href'),
			data : {ajax : '1'},
			dataType : 'json',
			success : function(json) {
				if (json.result == 'true') {
					$('#missions').html(json.html);
				} else {
					alertFail('获取任务信息时发生错误');
				}
			}
		});
		return false;
	});
	
	$('div.smission a.closing, div.smission a.confirm').live('click', function() {
		$('#missions a[cz="' + $(this).attr('cz') + '"]').removeClass('ing').addClass('fin').find('span.status').text('(已完成)');
		$.unblockUI({ 
            onUnblock: function(){ 
				$('div.smission').remove();
				$.blockUI({
					css: {border : 0, background: 'none', top : '10%', left : '30%'},
			        message:  $('#missions'),
			        fadeIn : 0
			    });
			} 
        });
		return false;
	});
	
	$('div.smission a.close').live('click', function() {
		$.unblockUI({ 
            onUnblock: function(){ 
				$('div.smission').remove();
				$.blockUI({
					css: {border : 0, background: 'none', top : '10%', left : '30%'},
			        message:  $('#missions'),
			        fadeIn : 0
			    });
			} 
        });
		return false;
	});
	
	$('div.smission a.accept').live('click', function() {
		$.ajax({
			url : '/ajax/startmission',
			type: 'POST',
			data: {cz : $(this).attr('cz'), token : $('body').attr('token')},
			dataType : 'json',
			success: function(json) {
				if (json.result == 'true') {
					if (json.url != '#') {
						alertSuccess('您准备好开始任务了吗？一会儿我们将为您打开任务页面，任务结束条件是：' + json.fmsg + '，请完成任务。', '开始任务', function() {
							window.location = json.url;
						});
					} else {
						alertSuccess('您准备好开始任务了吗？如果不了解怎么操作，可以再次打开任务窗口查看说明哦。', '开始任务', function() {
							$.unblockUI({ 
					            onUnblock: function(){ $('#missions').remove(); } 
					        });
						});
					}
				} else {
					alertFail(json.msg);
				}
				window.accepting = false;
			}
		});
		return false;
	});
	
	$('#missions a.close').live('click', function() {
		$.unblockUI({ 
            onUnblock: function(){ $('#missions').remove(); } 
        });
		return false;
	});
	
	$('div.mymissions > a').click(function() {
		$('div.mymissions div.pop a').click();
		$.ajax({
			url : $(this).attr('href'),
			data : {ajax : '1'},
			dataType : 'json',
			success : function(json) {
				if (json.result == 'true') {
					$('body').append('<div id="missions" style="display:none;">' + json.html + '</div>');
					$.blockUI({
						css: {border : '0', background: 'none', top : '10%', left : '30%'},
				        message: $('#missions')
				    });
				} else {
					alertFail('获取任务信息时发生错误');
				}
			}
		});
		return false;
	});
	
	$('div.mymissions div.pop a').click(function() {
		$(this).parents('div.pop').remove();
		$.ajax({
			url : '/ajax/ignoremissionnote',
			type : 'post',
			data : {token : $('body').attr('token')}
		});
		return false;
	});
	
	if ($.browser.msie && parseInt($.browser.version) <= 6 && $('div.mymissions div.pop').size() > 0) {
		$('div.mymissions div.pop div.popbg').each(function() {
			DD_belatedPNG.fixPng(this);
		});
	}
});