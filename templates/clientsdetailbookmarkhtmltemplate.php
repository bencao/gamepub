<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class ClientsdetailbookmarkHTMLTemplate extends ClientsHTMLTemplate
{
	function othertitle() {
		return '来体验' . common_config('site', 'name') . '的更多玩法吧';
	}
	
	function title()
    {
    	return 'GamePub Bookmark，分享精彩到游戏酒馆';
    }
    
	function metaKeywords() {
		return 'GamePub Bookmark，一键分享工具， 书签';
	}
	
	function metaDescription() {
		return 'GamePub Bookmark是GamePub团队开发的浏览器书签，方便用户将有趣的网页直接分享到GamePub。';
	}
	
	function showContent() {
		$this->showNav();
		$this->showBigBanner();
		$this->showDetail();
	}
	
	function showBigBanner() {
		$this->elementStart('div', 'client_banner');
		$this->element('img', array('src' => common_path('theme/default/i/pic_clientdetail.png')));
		$this->elementEnd('div');
	}
	
	function showDetail() {
		$this->elementStart('div', 'client_intro clearfix');
		$this->elementStart('dl', 'brief');
		$this->element('dt', null, 'GamePub Bookmark(外站快速分享书签)');
		$this->elementStart('dd');
		$this->raw('1.点击收藏夹的书签，快速分享当前网页到' . common_config('site', 'name') . '<br />2.只对' . common_config('site', 'name') . '站外的网页有效');
		$this->elementEnd('dd');
		$this->elementEnd('dl');
		$this->elementStart('div', 'detail');
		$this->elementStart('p');
		$this->element('strong', null, '浏览器：');
		$this->text('IE, Firefox, Chrome, Opera');
		$this->element('strong', null, '使用方法： ');
		if(stripos($_SERVER["HTTP_USER_AGENT"], "msie") || stripos($_SERVER["HTTP_USER_AGENT"], "opera")) {
			$btnstyle = 'font-size:12px;';
			$this->text('用鼠标右键点击下面的按钮，并选择“添加到收藏夹”即可创建快捷方式。');
		} else {
			$btnstyle = 'font-size:12px;cursor:move;';
			$this->text('用鼠标拖动下面的按钮到浏览器书签栏即可创建快捷方式。');
		}
		$this->elementEnd('p');
		$this->elementStart('p', 'button');
		$this->element('a', 
						array('class' => 'button99 green99 clientdown', 'style' => $btnstyle,
								'href' => 'javascript:(function(){if(location.href.indexOf("' . common_path('') . '")!=-1)return;var%20link="' . common_path('shareoutlink') . '?title="+encodeURIComponent(document.title)+"&url="+encodeURIComponent(location.href)+"&source=bookmark";if(/msie/i.test(navigator.userAgent))window.location.href=link;else%20a();function%20a(){window.open(link,"_blank","width=600,height=500");}})()'),
						'分享到游戏酒馆');
		$this->elementEnd('p');
		$this->elementEnd('div');
		$this->elementEnd('div');
	}
}