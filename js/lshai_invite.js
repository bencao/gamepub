$(document).ready(function() {
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
	//==================== Clip Board ==================== //
	
	$("#form_qq_invite").submit(function() {
		var un = $("input[name='username']", this).val();
		if (! un.match(/^\d+$/)) {
			alertFail('请输入有效的QQ号');
			return false;
		}
		var pw = $("input[name='password']", this).val();
		if (! pw.match(/^\S+$/)) {
			alertFail('请输入QQ密码');
			return false;
		}
	});
	$("#form_google_invite").submit(function() {
		var mail = $("input[name='usermail']", this).val();
		if (! mail.match(/^.+@gmail\.com$/i)) {
			alertFail('请输入有效的google邮箱');
			return false;
		}
	});
	$("#form_other_invite").submit(function() {
		var un = $("input[name='username']", this).val();
		if (! un.match(/^.+@.+\.[^@]+$/i)) {
			alertFail('请输入有效的邮箱');
			return false;
		}
		var pw = $("input[name='password']", this).val();
		if (! pw.match(/^\S+$/i)) {
			alertFail('请输入邮箱密码');
			return false;
		}
	});
	
	var hideAll = function() {
		$("div.email_iv form").hide();
		$("div.email_iv ul.iv_tab a").removeClass('active');
	};
	$("li.tab_qq a").click(function() {
		hideAll();
		if ($.browser.msie) {
			$("#form_qq_invite").show();
		} else {
			$("#form_qq_invite").fadeIn('slow');
		}
		$(this).addClass('active');
		return false;
	});
	$("li.tab_msn a").click(function() {
		hideAll();
		if ($.browser.msie) {
			$("#form_live_invite").show();
		} else {
			$("#form_live_invite").fadeIn('slow');
		}
		$(this).addClass('active');
		return false;
	});
	$("li.tab_gmail a").click(function() {
		hideAll();
		if ($.browser.msie) {
			$("#form_google_invite").show();
		} else {
			$("#form_google_invite").fadeIn('slow');
		}
		$(this).addClass('active');
		return false;
	});
	$("li.tab_yahoo a").click(function() {
		hideAll();
		if ($.browser.msie) {
			$("#form_yahoo_invite").show();
		} else {
			$("#form_yahoo_invite").fadeIn('slow');
		}
		$(this).addClass('active');
		return false;
	});
	$("li.tab_other a").click(function() {
		hideAll();
		if ($.browser.msie) {
			$("#form_other_invite").show();
		} else {
			$("#form_other_invite").fadeIn('slow');
		}
		$(this).addClass('active');
		return false;
	});
});
