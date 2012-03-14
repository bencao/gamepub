
/** Init for Farbtastic library and page setup
 *
 * @package   LShai
 */
$(document).ready(function() {
	window.$wrap = $('#wrap');
	window.$wsplit = $('#widgets div.split');
	window.$wsubinfo = $('#widgets div.sub_info a');
	window.$wnavli = $('#w_nav li');
	window.$wgrid6 = $('#widgets dl.grid-6 dd p.op');
	window.$wgida = $('#widgets div.group_info div.avatar');
	window.$wgidta = $('#widgets div.group_info dl.detail dt a');
	window.$wgidt = $('#widgets div.group_info dl.detail dt');
	function UpdateColors(S) {
        C = $(S).val();
        switch (parseInt(S.id.slice(-1))) {
            case 1: default:
                $('body').css({backgroundColor : C});
                break;
            case 2:
                $('#notices, #owner_summary').css({color : C});
                break;
            case 3:
                var sepcolor = getCompositeColor(C, '#FFFFFF', 0.13);
                var navbgcolor = getCompositeColor(C, '#000000', 0.1);
                var grouplightcolor = getCompositeColor(C, '#FFFFFF', 0.284);
                var groupheavycolor = getCompositeColor(C, '#000000', 0.44);
                var groupmediumcolor = getCompositeColor(C, '#000000', 0.22);
                
                $wrap.css({backgroundColor : C});
                $wsplit.css({borderTopColor : sepcolor});
                $wsubinfo.css({borderLeftColor : sepcolor});
                $wnavli.css({borderBottomColor : sepcolor, backgroundColor : navbgcolor});
                $wgrid6.css({color : sepcolor});
                $wgida.css({borderColor : grouplightcolor});
                $wgidta.css({borderTopColor : grouplightcolor, borderBottomColor : groupheavycolor});
                $wgidt.css({borderColor : groupmediumcolor});
                
                break;
            case 4:
                $('#widgets').css({color : C});
                break;
            case 5:
                $('#owner_summary a, #notices a, #widgets div.sub_info a, #widgets dl.grid-6 a').css({color : C});
                break;
        }
        $(document).data('colorchanged', '1');
    }
	
	function ClearColors(S) {
		C = $(S).val();
        switch (parseInt(S.id.slice(-1))) {
            case 1: default:
                $('body').removeAttr('style');
                break;
            case 2:
                $('#notices, #owner_summary').removeAttr('style');
                break;
            case 3:
                $wrap.removeAttr('style');
                $wsplit.removeAttr('style');
                $wsubinfo.removeAttr('style');
                $wnavli.removeAttr('style');
                $wgrid6.removeAttr('style');
                $wgida.removeAttr('style');
                $wgidta.removeAttr('style');
                $wgidt.removeAttr('style');
                break;
            case 4:
                $('#widgets').removeAttr('style');
                break;
            case 5:
                $('#owner_summary a, #notices a, #widgets div.sub_info a, #widgets dl.grid-6 a').removeAttr('style');
                break;
        }
        $(document).data('colorchanged', '0');
	}

    function UpdateFarbtastic(e) {
        f.linked = e;
        f.setColor(e.value);
    }

    function UpdateSwatch(e) {
        $(e).css({"background-color": e.value,
                  "color": f.hsl[2] > 0.5 ? "#000": "#fff"});
    }

    function SynchColors(e) {
        var S = f.linked;
        var C = f.color;

        if (S && S.value && S.value != C) {
            S.value = C.toUpperCase();
            UpdateSwatch(S);
            UpdateColors(S);
        }
    }

    function InitFarbtastic() {
	    f = $.farbtastic('#color-picker', SynchColors);
        $('.swatch').each(SynchColors)
            .blur(function() {
                tv = $(this).val();
                $(this).val(tv.toUpperCase());
                (tv.length == 4) ? ((tv[0] == '#') ? $(this).val('#'+tv[1]+tv[1]+tv[2]+tv[2]+tv[3]+tv[3]) : '') : '';
             })
            .focus(function() {
                $('#color-picker').show();
                UpdateFarbtastic(this);
            })
            .change(function() {
                UpdateFarbtastic(this);
                UpdateSwatch(this);
                UpdateColors(this);
            }).change();
        hasStyle = true;
    }

    var f, swatches, hasStyle;
    
    function ClearFarbtastic() {
    	if (hasStyle) {
    		 $('.swatch').each(function() {
    			 ClearColors(this);
    		 });
    		 hasStyle = false;
    	}
    }
    
    $('#form_settings_design').bind('reset', function(){
        setTimeout(function(){
            swatches.each(function(){UpdateColors(this);});
            $('#color-picker').remove();
            swatches.unbind();
            InitFarbtastic();
        },10);
    });
    
    $('#bgl').click(function() {
    	$('body').css({backgroundPosition : 'left top'});
    	$(document).data('bgchanged', '1');
    });
    
    $('#bgr').click(function() {
    	$('body').css({backgroundPosition : 'right top'});
    	$(document).data('bgchanged', '1');
    });
    
    $('#bgc').click(function() {
    	$('body').css({backgroundPosition : 'center top'});
    	$(document).data('bgchanged', '1');
    });
    
    $('#bgf').click(function() {
    	if (this.checked) {
    		$('body').css({backgroundRepeat : 'repeat'});
    	} else {
    		$('body').css({backgroundRepeat : 'no-repeat'});
    	}
    	$(document).data('bgchanged', '1');
    });
    
    $('#bgfix').click(function() {
    	if (this.checked) {
    		$('body').css({backgroundAttachment : 'fixed'});
    	} else {
    		$('body').css({backgroundAttachment : 'scroll'});
    	}
    	$(document).data('bgchanged', '1');
    });
    
    $('a.do_not_show_bg').click(function() {
    	$('a.show_bg').removeClass('active');
    	$(this).addClass('active');
    	$('body').css({backgroundImage : 'none'});
    	$("#showimage").val('0');
    	$(document).data('bgchanged', '1');
    	return false;
    });
    
    $('a.show_bg').click(function() {
    	$('a.do_not_show_bg').removeClass('active');
    	$(this).addClass('active');
    	$('body').css({backgroundImage : $('body').data('old_bg')});
    	$("#showimage").val('1');
    	$(document).data('bgchanged', '1');
    	return false;
    });
    
    updateRadioCheckbox();
    
    $('#settings_design_reset').click(function() {
    	$('.swatch').change();
    	$(document).data('colorchanged', '0');
    });
    
    handleUpload();
    
    $(document).data('colorchanged', '0');
    $(document).data('bgchanged', '0');
    
    $('input.submit').click(function() {
    	if ($("#design_name").val() == '') {
    		alert('您还没有给皮肤取个好听的名字呢');
    		$('#design_name').focus();
    		return false;
    	}
    	$(document).data('colorchanged', '0');
        $(document).data('bgchanged', '0');
    });
    
    window.onbeforeunload = function(event){
    	if ($(document).data('colorchanged') == '1'
    		|| $(document).data('bgchanged') == '1') {
	        event = event || window.event;
	        event.returnValue = '您自定的皮肤尚未保存';
    	}
    }
    
    $('form.self_design_settings').ajaxForm({
    	dataType: 'json',
    	success: function(json) {
    		if (json.result == 'true') {
//    			$('head').append('<link rel="stylesheet" type="text/css" href="' + json.url + '" media="screen, projection, tv"/>');
    			alertSuccess('已将选中皮肤设置为当前皮肤。', '操作成功', function() {
    				window.location = json.url;
    			});
    		} else {
    			alertFail('应用皮肤失败');
    			window.applyingskin = false;
    		}
    	}
    }).each(addAjaxHidden);
    
    $('#self_designs a.apply, #template_designs a.apply').click(function() {
    	if (window.applyingskin
    			|| $(this).parents('li').hasClass('active')) {
    		return false;
    	}

		window.applyingskin = true;
		$(this).parents('form').submit();
		return false;
    });
    
    $('#self_designs a.delete').click(function() {
    	if (window.deletingskin) {
    		return false;
    	}
    	
    	if (confirm('您确定要删除该皮肤么？')) {
    		window.deletingskin = true;
    		$.ajax({
    			dataType : 'json',
    			type : 'POST',
    			url : $(this).attr('href'),
    			data : {ajax : '1', design_id : $(this).parents('li').attr('dsid'), token : $('body').attr('token'), del : '1'},
    			success : function(json) {
    				if (json.result == 'true') {
    					$('#self_designs li[dsid="' + json.dsid + '"]').fadeOut('slow', function() {
    						$(this).remove();
    					});
    					fadeSuccess('皮肤删除成功');
    				} else {
    					alertFail(json.msg);
    				}
    				window.deletingskin = false;
    			}
    		});
    	}
    	
    	return false;
    });
    
    $('#settings_design_color ul.nav_pane a').click(function() {
    	$('#settings_design_color ul.nav_pane li').removeAttr('style');
    	$(this).parent().css({backgroundColor: '#b8510c'});
    	var colorvalue = $(this).attr('colorvalue');
    	$('#navcolor').attr('value', colorvalue);
    	$('head').append('<style>'
    			+ '#w_nav li.active span{background-image:url(/theme/default/i/b/' + colorvalue + '.png);}'
    			+ '#w_nav li.on span{background-image:url(/theme/default/i/b/' + colorvalue + '_on.png);}'
    			+ '</style>');
    	return false;
    });
    
    $('#settings_self_design').ajaxForm({
    	dataType: 'json',
    	success: function(json) {
    		if (json.result == 'true') {
    			alertSuccess(json.msg, '操作成功', function() {
    				window.location = json.url;
    			});
    		} else {
    			alertFail('保存皮肤失败');
    		}
    	}
    }).each(addAjaxHidden);
    
    // collapse
    $('#settings_self_design dl dt a').click(function() {
    	var dd = $('#settings_self_design dl dd');
    	if (! dd.is(':visible')) {
    		$('#self_designs dd, , #template_designs dd').hide(0, function() {
    			dd.fadeIn('slow');
    		});
    		InitFarbtastic();
    	}
    	
    	return false;
    });
    $('#self_designs dt a').click(function() {
    	var dd = $('#self_designs dd');
    	if (! dd.is(':visible')) {
    		$('#settings_self_design dl dd, #template_designs dd').hide(0, function() {
    			ClearFarbtastic();
    			dd.fadeIn('slow');
    		});
    	}
    	return false;
    });
    
    $('#template_designs dt a').click(function() {
    	var dd = $('#template_designs dd');
    	if (! dd.is(':visible')) {
    		$('#self_designs dd, #settings_self_design dl dd').hide(0, function() {
    			ClearFarbtastic();
    			dd.fadeIn('slow');
    		});
    	}
    	return false;
    });
    
    $('#theme_roller div.theme_title a.min').click(function() {
    	if ($(this).hasClass('min')) {
    		$('#theme_roller div.theme_body').hide(0);
    		$(this).removeClass('min').addClass('max').text('还原');
    	} else {
    		$('#theme_roller div.theme_body').show(0);
    		$(this).removeClass('max').addClass('min').text('最小化');
    	}
    	return false;
    });
    
    $('#theme_roller div.theme_title a.close').click(function() {
    	$('#theme_roller').remove();
    	return false;
    });
    
    $('#theme_roller').draggable({ handle : 'div.theme_title'}).bgiframe();
    
});

function getCompositeColor(bgcolor, fecolor, opacity) {
	var bred = parseInt(bgcolor.substr(1, 2), 16);
	var bgreen = parseInt(bgcolor.substr(3, 2), 16);
	var bblue = parseInt(bgcolor.substr(5, 2), 16);
	
	var fred = parseInt(fecolor.substr(1, 2), 16);
	var fgreen = parseInt(fecolor.substr(3, 2), 16);
	var fblue = parseInt(fecolor.substr(5, 2), 16);
	
	var nred = bred * (1 - opacity) + fred * opacity;
	var ngreen = bgreen * (1 - opacity) + fgreen * opacity;
	var nblue = bblue * (1 - opacity) + fblue * opacity;
	
	return '#' + parseInt(nred).toString(16) + parseInt(ngreen).toString(16) + parseInt(nblue).toString(16);
}

function updateRadioCheckbox() {
	var old = $('body').css('backgroundImage');
	$('body').data('old_bg', old);
	if (old != 'none') {
		$('a.show_bg').addClass('active');
		$("#showimage").val('1');
	} else {
		$('a.do_not_show_bg').addClass('active');
		$("#showimage").val('0');
	}
	if ($('body').css('backgroundRepeat') == 'repeat') {
		$('#bgf').get(0).checked = true;
	}
	if ($('body').css('backgroundAttachment') == 'fixed') {
		$('#bgfix').get(0).checked = true;
	}
	
	var align = $('body').css('backgroundPosition');
	if (align == '50% 0%') {
		$('#bgc').get(0).checked = true;
	} else if (align == '100% 0%') {
		$('#bgr').get(0).checked = true;
	} else {
		$('#bgl').get(0).checked = true;
	}
}

function handleUpload() {
	var uploadifyErrorHandler = function(event, queueID, fileObj, errorObj) {
		if (errorObj.type == 'File Size') {
			alert('您选择的文件太大，不能超过1MB');
			$("#bgfile").parents("td").next().html('<label>文件太大，不能超过1MB</label>');
		};
		return false;
	};
	
	var uploadifyCompleteHandler = function(event, queueID, fileObj, response, data) {
//		$("#bgfile").parents("td").next().html('<label>背景上传成功</label>');
		$("#backgroundimage").val(response);
		$('body').css({backgroundImage : 'url(' + response + ')', backgroundPosition : 'center top'});
		$('#bgc').get(0).checked = true;
		$('a.do_not_show_bg').removeClass('active');
		$('a.show_bg').addClass('active');
		$('body').data('old_bg', $('body').css('backgroundImage'));
		$(document).data('bgchanged', '1');
	};
	
	$("#bgfile").uploadify({
		'uploader'       : '/js/uploadify.swf',
		'script'         : '/ajax/uploadfile?uid=' + $("#fileQueue").attr('uid'),
		'cancelImg'      : '/theme/default/images/uploadify/cancel.png',
		'buttonImg'      : '/theme/default/images/uploadify/upload.png',
		'width'          : 90,
		'height'         : 24,
		'folder'         : '/file/tmp',
		'queueID'        : 'fileQueue',
		'auto'           : true,
		'multi'          : false,
		'buttonText'     : ' ',
		'fileDesc'       : 'JPG/PNG/GIF文件',
		'fileExt'        : '*.jpg;*.png;*.gif',
		'sizeLimit'      : '1048576',
		'onError'        : uploadifyErrorHandler,
		'onComplete'     : uploadifyCompleteHandler
	});
}
