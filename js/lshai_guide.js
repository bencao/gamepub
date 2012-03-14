$.fn.getPosition = function() {
	var i = $(this);
	var top = 0; var left =0;var x=0; //x is just for avoiding infinite loops
	var oParent = i.get(0);
	while (oParent  && x++ <100) {
		if ($.browser.safari && oParent.nodeName == 'body') {
			continue;
		}
		top  += oParent.offsetTop  - oParent.scrollTop;
		left += oParent.offsetLeft - oParent.scrollLeft;
		oParent = oParent.offsetParent;
	}
	//top -= document.documentElement.scrollTop;
	//left -= document.documentElement.scrollLeft;
	top += i.height();
	
	return {top: top, left : left};
};

$(document).ready(function(){
	var prepareSteps = function() {
		window.gs = {};
		window.gs.create = [];
		window.gs.destory = [];
		window.gs.maxStep = 5;
		window.gs.zindex = $.browser.msie ? ($.blockUI.defaults.baseZ + 2) : $.blockUI.defaults.baseZ + 1; 
		window.gs.create[1] = function() {
			$('#gstep1').show();
		};
		window.gs.destory[1] = function() {
			$('#gsteps').hide(0, function() {
				$(this).fadeIn(1000);
			});
			$('#gstep1').hide();
		};
		window.gs.create[2] = function() {
			$('#gstep2').show();
			
			// zIndex : window.gs.zindex, 
			var pos = $("#notice_form").css({position : 'relative', backgroundColor:'#fff', border : '2px solid #e66510', padding: '0 11px', backgroundPosition: 'top -2px'}).getPosition();
			if ($.browser.msie && parseInt($.browser.version) <= 7) {
				pos.top += $(window).scrollTop();
			}
			$(".blockPage").css({top : pos.top + 55, left : pos.left + 180});
		};
		window.gs.destory[2] = function() {
			$('#gsteps').hide(0, function() {
				$(this).fadeIn(1000);
			});
			$('#gstep2').hide();
			$("#notice_form").removeAttr('style');
		};
		window.gs.create[3] = function() {
			$('#gstep3').show();
			
			var pos = $("#notice_filter_new").getPosition();
			// zIndex : window.gs.zindex, 
			$("#notice_filter_new").parent().css({position : 'relative', backgroundColor:'#fff', border : '2px solid #e66510', margin: '0 -2px'});
			
			if ($.browser.msie && parseInt($.browser.version) <= 7) {
				pos.top += $(window).scrollTop();
			}
			
			$(".blockPage").css({top : pos.top - 270, left : pos.left + 200});
		};
		window.gs.destory[3] = function() {
			$('#gsteps').hide(0, function() {
				$(this).fadeIn(1000);
			});
			$('#gstep3').hide();
			$("#notice_filter_new").parent().removeAttr('style');
		};
		window.gs.create[4] = function() {
			$('#gstep4').show();
			
			var pos = $("#w_nav").getPosition();
			
			// zIndex : window.gs.zindex,
			$('#w_nav').parent().css({position : 'relative', backgroundColor:'#333', border : '2px solid #e66510', margin:'0 -11px', padding: '0 9px'});
			
			if ($.browser.msie && parseInt($.browser.version) <= 7) {
				pos.top += $(window).scrollTop();
			}
			
			$(".blockPage").css({top : pos.top - 200, left : pos.left - 400});
		};
		window.gs.destory[4] = function() {
			$('#gsteps').hide(0, function() {
				$(this).fadeIn(1000);
			});
			$('#gstep4').hide();
			$('#w_nav').parent().removeAttr('style');
		};
		window.gs.create[5] = function() {
			$('#gstep5').show();
			
			//  zIndex : window.gs.zindex,
			var pos = $("#main_nav").css({border : '2px solid #e66510' }).getPosition();
			
			if ($.browser.msie && parseInt($.browser.version) <= 7) {
				pos.top += $(window).scrollTop();
			}
			
			$(".blockPage").css({top : pos.top + 55, left : pos.left - 120});
		};
		window.gs.destory[5] = function() {
			$('#gsteps').hide(0, function() {
				$(this).fadeIn(1000);
			});
			$('#gstep5').hide();
			$("#main_nav").removeAttr('style');
		};
	};
	
	var gotoStep = function(curStep, destStep) {
		if (window.gs) {
			if (window.gs.destory[curStep]) {
				window.gs.destory[curStep]();
			}
			if (curStep == 0) {
				$.blockUI({
			        message: $('#gsteps'), 
			        css: {
			            width : '350px',
			            border : '0',
			            cursor : 'default',
			            backgroundColor : 'transparent',
			            position : 'absolute'
			        },
			        overlayCSS:  {cursor:'default'}
			    });
			}
			if (destStep > window.gs.maxStep) {
				$.unblockUI();
				return true;
			}
			window.gs.create[destStep]();
			return false;
		} else {
			$.unblockUI();
			return false;
		}
	};
	
	var prepareStepOperation = function() {
		$("#gsteps .next").click(function() {
			var curstep = parseInt($(this).attr('step'));
			return gotoStep(curstep, curstep + 1);
		});
		$("#gsteps .prev").click(function() {
			var curstep = parseInt($(this).attr('step'));
			return gotoStep(curstep, curstep - 1);
		});
	};
	
	prepareSteps();
	prepareStepOperation();
	gotoStep(0, 1);
});