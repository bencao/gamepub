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
	
	$('dl.videos').find('a.prev').click(dlPrevClick)
			.end().find('a.next').click(dlNextClick);
	
	
	$('dl.photos').find('a.prev').click(dlPrevClick)
			.end().find('a.next').click(dlNextClick)
			.end().find('div.smallpicture > a').lightBox({
		imageLoading: '/theme/default/images/lightbox/loading.gif',
		imageBtnClose: '/theme/default/images/lightbox/close.gif',
		imageBtnPrev: '/theme/default/images/lightbox/prev.gif',
		imageBtnNext: '/theme/default/images/lightbox/next.gif',
		imageBlank : '/theme/default/images/lightbox/blank.gif'
	});

	
	$('dl.musics').find('a.prev').click(dlPrevClick)
			.end().find('a.next').click(dlNextClick)
			.end().find('div.music_message > a').click(playMusic);


	function dlPrevClick() {
		if (window.rolling) {
			return false;
		}
		var $ol = $(this).parent().find('ol');
		var left = $ol.css('left');
		if (left != '' && left != '0px') {
			window.rolling = true;
			$ol.animate({left : '+=152'}, 1000, function() {
				window.rolling = false;
			});
		}
		return false;
	}
	
	function dlNextClick() {
		if (window.rolling) {
			return false;
		}
		var $ol = $(this).parent().find('ol');
		var left = $ol.css('left');
		var liCount = $ol.children('li').size();
		if (liCount > 3 && left != (-152 * (liCount - 3) + 'px')) {
			window.rolling = true;
			$ol.animate({left : '-=152'}, 1000, function() {
				window.rolling = false;
			});
		}
		return false;
	}

	$('dl.news').find('a.prev').click(function() {
		if (window.rolling) {
			return false;
		}
		var $ul = $(this).parent().parent().find('ul');
		var top = $ul.css('top');
		if (top != '0px') {
			window.rolling = true;
			$ul.animate({top : '+=145'}, 1000, function() {
				window.rolling = false;
			});
		}
		return false;
	}).end().find('a.next').click(function() {
		if (window.rolling) {
			return false;
		}
		var $ul = $(this).parent().parent().find('ul');
		var top = $ul.css('top');
		if (top != '-145px') {
			window.rolling = true;
			$ul.animate({top : '-=145'}, 1000, function() {
				window.rolling = false;
			});
		}
		return false;
	});
	
	function showPhotoDisList() {
		alert('test');
		$(this).attr('href', $(this).parents('.image_message').find('img.bigimagebtn').attr('src'));
		$(this).lightBox();
	}
	
	function playMusic() {
		var w = window.open('/mplayer/show?nid=' + $(this).parents('.noticeitem').attr('nid'), 'mplayer', "width=730,height=576,top=100px,left=220px,scrollbars=no,resizable=no");
		w.focus();
		return false;
	}
	
});