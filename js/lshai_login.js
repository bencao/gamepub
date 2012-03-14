$(document).ready(function() {
	$('#q').Watermark("搜索内容", "#999").parents('form').submit(function() {
		if ($('input.text', this).val() == '搜索内容') {
			$('input.text', this).focus();
			return false;
		}
	});
	$('#uname').focus()
	
	// detect if there was an error and show it
	var login_error = $("input[name='login_error']");
	if (login_error.length > 0) {
		var msg = $("input[name='login_error']").attr('value');
		alertFail(msg);
	}
	
	$('a.sw-login').click(function() {
		if (! $('#sidebar .login').is(":visible")) {
			$('#sidebar .register').hide('slide', {direction : 'up'}, 500, function() {
				$('#sidebar .login').show('slide', {direction : 'up'}, 500, function() {
					$('#uname').focus();
				});
			});
		}
		return false;
	});
	
	$('a.sw-reg').click(function() {
		if (! $('#sidebar .register').is(":visible")) {
			$('#sidebar .login').hide('slide', {direction : 'up'}, 500, function() {
				$('#sidebar .register').show('slide', {direction : 'up'}, 500);
			});
		}
		return false;
	});
	
	$("form.login").submit(function() {
		var nn = $("input[name='uname']", this).val();
		if (nn.length == 0) {
			alertFail('请输入用户名或邮箱');
			return false;
		}
		var pw = $("input[name='password']", this).val();
		if (pw.length == 0) {
			alertFail('请输入密码');
			return false;
		}
		$.blockUI({ 
			message: '<h1 class="blockui_loading">处理中，请稍候...</h1>', 
			overlayCSS:  { 
		        backgroundColor: '#fff', 
		        opacity: 0.15 
		    },
		    css: { 
		        padding:        0, 
		        margin:         0, 
		        width:          '30%', 
		        top:            '40%', 
		        left:           '35%', 
		        textAlign:      'center', 
		        color:          '#666', 
		        border:         '0', 
		        backgroundColor: 'transparent', 
		        cursor:         'wait' 
		    },
		    fadeIn:  0,
		    fadeOut:  0
		});
	});
	
	var timer = 2000;
	window.rollinghandle = setInterval("window.rolling()", 2.5 * timer);
	
	$('#talking').mouseover(function() {
		clearInterval(window.rollinghandle);
	}).mouseout(function() {
		window.rollinghandle = setInterval("window.rolling()", 2.5 * timer);
	});
	
	window.rolling = function() {
		$('#talking li:last').remove().prependTo('#talking');
		$('#talking li:first').each(function() {
				var height = $(this).height();
				$(this).css({"height" : height + "px"})
					.parent().css({"top" : "-" + height + "px"});
			}).find("div.content").hide();
		$('#talking').animate({"top" : "0px"}, timer/2, function() {
				$(this).find("li:first div.content").fadeIn(timer);
			});
	};
	
	window.rolling();
});