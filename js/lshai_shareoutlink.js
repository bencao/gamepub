$(document).ready(function(){
	
	// add by cao ie6 png fix
	if ($.browser.msie && parseInt($.browser.version) <= 6 ) {
		$('#share_wrap a.reg').each(function() {
			DD_belatedPNG.fixPng(this);
		});
	}
	
	var options = {  
		datatype: 'json',   
		beforeSubmit: function(formData, jqForm, options) {
			if (jqForm.hasClass('processing')) {
				return false;
			}
			jqForm.addClass("processing");
			$("#notice_action-submit", jqForm).attr("disabled", "disabled").addClass("disabled");
			$('#notice_form p.msg').hide();
		},
		error : function() {
			alertFail('对不起，出错了!');
			$('#notice_form').removeClass('processing').find("#notice_action-submit").removeAttr("disabled").removeClass("disabled");
		},
		success: function(json) {
			if (json.result == 'true') {
				//window.location.href = "/home";
				$.unblockUI({
					onUnblock: function(){
						$('#share_wrap a.reg').hide();
						$('div.player embed').removeAttr('style');
						$('#notice_form').replaceWith(json.msg);
						setInterval(countdown, 1000);
					}
				});
			} else if (json.result == 'false'){
				alertFail(json.msg);
			} 
			$('#notice_form').removeClass('processing').find("#notice_action-submit").removeAttr("disabled").removeClass("disabled");
		}
	};
	$('#notice_form').ajaxForm(options); 

	var isAddPic = true;
	window.$my = {
		notice_form: $("#notice_form"),
		notice_textarea: $("#notice_data-text"),
		notice_text_count: $("#notice_text-count"),
		notice_submit: $("#notice_action-submit")
	};

	if ($("#notice_form").length > 0) {
		 processNotice();
	}

	$("#password").keypress(function(event){
		
		if(event.keyCode==13)
		{
			$my.notice_submit.click();
		}
	});

	$("#uname").keypress(function(event){
		
		if(event.keyCode==13)
		{
			$my.notice_submit.click();
		}
	});
	$("#notice_action-submit").click(function(){
		if ($("#uname").val() == ""){
			alertFail("用户名不能为空！","提示",function(){
				$("#uname").focus();
			});
			return;
		}else if($("#password").val() == ""){
			alertFail("密码不能为空！","提示",function(){
				$("#password").focus();
			});
			return;
		}else if ($("#notice_text-count").html() == 280) {
			alertFail("请输入您想说的话","提示",function(){
				$("#notice_data-text").focus();
			});
			return ;
		}else if($("#notice_text-count").html() < 0) {
			alertFail("您输入的字数超过了280，请精简","提示",function(){
				$("#notice_data-text").focus();
			});
			return;
		}else{
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
			        top:            '38%', 
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
			$my.notice_form.submit();
		}
		return;
	});

	if ($("#img0")[0] != null)
		$("#noticefilename").val($("#img0")[0].src);

	$("img").click(function(){
		$("#noticefilename").val(this.src);
	}); 

	$("li").click(function(){
		var c_index = $("#curindex").val();
		var p_index = (c_index - c_index %3)/3+1;

		var nname = "#"+c_index ;
		$(nname).removeClass();
		$(nname).addClass("page"+p_index );

		nname = "#"+this.id;
		$(nname).removeClass();
		$(nname).addClass("page"+p_index+" active");
		$("#curindex").val(this.id);
	});

	$("a.cancel").click(function(){

		var c_index = $("#curindex").val();
		var p_index = (c_index  - c_index%3 )/3 + 1;
		
		if (isAddPic == true)
		{
			$("#counttext" ).hide();
			$("#prevbutton" ).hide();
			$("#nextbutton" ).hide();
			$('li.page'+p_index ).hide();
			$("#selecttext").html("不附加图片");
			$("#cancelbutton").html("附加图片");
		
			$("#noticefilename").val("");
			isAddPic = false;
		}else{
			$("#counttext" ).show();
			$("#prevbutton" ).show();
			$("#nextbutton" ).show();
			$('li.page'+p_index ).show();
			$("#selecttext" ).html("请选择一张图片作为图片附件");
			$("#cancelbutton" ).html("不想附加图片");
			if ($("#img"+c_index).size() > 0) {
				$("#noticefilename").val($("#img"+c_index )[0].src);
			}
			isAddPic = true;
		}
	});

	$("a.next").click(function(){
		var all_index = $("#picnum").val();
		var c_index = $("#curindex").val();
		var p_index = (c_index  - c_index%3 )/3 + 1;

		var np_index = p_index+1;
		if (np_index > (all_index - all_index %3)/3 + 1)
		{
			np_index = 1;
		}
		if (np_index == p_index)
			return;

		$('li.page'+p_index ).hide(0, function() {
    		      $('li.page'+np_index).show();
    		 });


		var nname = "#"+c_index;
		$(nname).removeClass();
		$(nname).addClass("page"+p_index);

		c_index = np_index*3 - 3;
		$("#curindex").val(c_index );
		nname = "#"+c_index;
		$(nname).removeClass();
		$(nname).addClass("page"+np_index+" active");

		$("#noticefilename").val($("#img"+c_index )[0].src);
	});

	$("a.prev").click(function(){

		var all_index = $("#picnum").val();
		var c_index = $("#curindex").val();
		var p_index = ($("#curindex").val() - c_index%3 )/3 + 1;
	
		var pp_index = p_index-1;
		if (pp_index <= 0)
		{
			pp_index = (all_index - all_index %3)/3 + 1;
		}
		if (pp_index == p_index)
			return;

		$('li.page'+p_index ).hide(0, function() {
    		      $('li.page'+pp_index ).show();
    		 });


		var nname = "#"+c_index;
		$(nname).removeClass();
		$(nname).addClass("page"+p_index);

		c_index = pp_index*3 - 3;
		$("#curindex").val(c_index);
		nname = "#"+c_index;
		$(nname).removeClass();
		$(nname).addClass("page"+pp_index+" active");

		$("#noticefilename").val($("#img"+c_index )[0].src);
	});

	var left = 5;
	function countdown()
	{
		var time = $('#timeout');
		left--;
		time.text(left);

		if(left == 0)
		{
		   window.opener=null;
		   window.close();
		}
	}
});