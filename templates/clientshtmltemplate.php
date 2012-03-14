<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class ClientsHTMLTemplate extends OtherHTMLTemplate
{
	function othertitle() {
		return '来体验' . common_config('site', 'name') . '的更多玩法吧';
	}
	
	function title() {
		return common_config('site', 'name') . '的更多玩法';
	}
	
	function showHeader()
    {
    	if ($this->cur_user) {
	        $this->elementStart('div', array('id' => 'header_outter_wrap'));
	        $this->elementStart('div', array('id' => 'header_inner_wrap'));
	        $this->elementStart('div', array('id' => 'header', 'class' => 'clearfix'));
	        $this->showLogo();
	        $this->showTopMenu();
	        $this->elementEnd('div');
	        $this->elementEnd('div');
	        $this->elementEnd('div');
    	} else {
    		$this->elementStart('div', array('id' => 'header_anonymous', 'class' => 'anonymous'));
    		$this->element('a', array('href' => common_path(''), 'title' => '回到登录页面', 'class' => 'lg'));
    		$this->elementStart('h2');
    		$this->raw('想迅速成为游戏达人？想更方便的和游戏好友沟通交流？');
    		$this->elementEnd('h2');
    		$this->element('p', null, common_config('site', 'name') . '是为网游玩家量身打造的，是国内第一家以用户为中心的网游互动社区，是现在最酷最火的玩家互动平台。');
    		$this->element('p', null, '现在注册' . common_config('site', 'name') . '，与游友们分享游戏的点滴和快乐。');
    		$this->element('a', array('href' => common_path('register'), 'class' => 'toreg', 'rel' => 'nofollow'), '');
    		$this->elementStart('p', 'tologin');
    		$this->text('已有' . common_config('site', 'name') . '账号？请');
    		$this->element('a', array('href' => common_path(''), 'class' => 'trylogin'), '登录');
    		$this->elementEnd('p');
    		$this->elementEnd('div');
    	}
    }
	
	function showContent() {
		$this->showBanner();
		$this->showNav();
		$this->showClients();
	}
	
	function showBanner() {
		$this->elementStart('div', 'banner');
		$this->element('img', array('src' => common_path('theme/default/i/pic_clientbanner.jpg')));
		$this->elementEnd('div');
	}
	
	function showNav() {
		$this->elementStart('ul', 'nav clearfix');
		//All
		$curClient = $this->trimmed('client');
		if (empty($curClient)) {
			$this->elementStart('li', 'active');
		} else {
			$this->elementStart('li');
		}
		$this->element('a', array('href' => common_path('clients')), '全部');
		$this->elementEnd('li');
		//HooYou
		if ($curClient == 'hooyou') {
			$this->elementStart('li', 'active');
		} else {
			$this->elementStart('li');
		}
		$this->element('a', array('href' => common_path('clients/hooyou')), '呼游');
		$this->elementEnd('li');
		//Bookmark
		if ($curClient == 'bookmark') {
			$this->elementStart('li', 'active');
		} else {
			$this->elementStart('li');
		}
		$this->element('a', array('href' => common_path('clients/bookmark')), '分享书签');
		$this->elementEnd('li');
		//FxExt
		if ($curClient == 'fxext') {
			$this->elementStart('li', 'active');
		} else {
			$this->elementStart('li');
		}
		$this->element('a', array('href' => common_path('clients/fxext')), 'Firefox扩展');
		$this->elementEnd('li');
		//ChromeExt
		if ($curClient == 'chromeext') {
			$this->elementStart('li', 'active');
		} else {
			$this->elementStart('li');
		}
		$this->element('a', array('href' => common_path('clients/chromeext')), 'Chrome扩展');
		$this->elementEnd('li');
		//Feed
		$this->elementStart('li');
		$this->element('a', array('href' => common_path('settings/feed')), '从自己的博客导入');
		$this->elementEnd('li');
		
		$this->elementEnd('ul');
	}
	
	function showClients() {
		$this->elementStart('ul', 'clients');
		//HooYou
		$this->elementStart('li');
		$this->elementStart('div', 'image');
		$this->element('img', array('src' => common_path('theme/default/i/pic_clients_hooyou.jpg')));
		$this->elementEnd('div');
		$this->elementStart('p', 'name');
		$this->element('a', array('href' => common_path('clients/hooyou')), '呼游(' . common_config('site', 'name') . '桌面版)');
		$this->elementEnd('p');
		$this->element('p', null, '简约而不简单，不仅可以第一时间收到新消息，而且还支持强大的一键截屏上传功能。');
		$this->elementStart('p', 'op');
		$this->element('a', array('class' => 'download button60 orange60 clientdown', 'href' => common_path('downloads/HooYou_v1.2_Setup.exe'), 'c' => 'hooyou'), '下载');
		$this->element('a', array('href' => common_path('clients/hooyou')), '查看详情');
		$this->elementEnd('p');
		$this->elementEnd('li');
		//Bookmark
		$this->elementStart('li');
		$this->elementStart('div', 'image');
		$this->element('img', array('src' => common_path('theme/default/i/pic_clients_hooyou.jpg')));
		$this->elementEnd('div');
		$this->elementStart('p', 'name');
		$this->element('a', array('href' => common_path('clients/bookmark')), '分享书签');
		$this->elementEnd('p');
		$this->element('p', null, '快速分享当前页面，支持各种浏览器！请拖拽下面的按钮至浏览器收藏夹。');
		$this->elementStart('p', 'op');
		$this->element('a', array('class' => 'download button60 orange60 clientdown', 'href' => 'javascript:(function(){if(location.href.indexOf("' . common_path('') . '")!=-1)return;var%20link="' . common_path('shareoutlink') . '?title="+encodeURIComponent(document.title)+"&url="+encodeURIComponent(location.href)+"&source=bookmark";if(/msie/i.test(navigator.userAgent))window.location.href=link;else%20a();function%20a(){window.open(link,"_blank","width=600,height=500");}})()', 'c' => 'bookmark'), '分享到GamePub');
		$this->element('a', array('href' => common_path('clients/bookmark')), '查看详情');
		$this->elementEnd('p');
		$this->elementEnd('li');
		//FxExt
		$this->elementStart('li');
		$this->elementStart('div', 'image');
		$this->element('img', array('src' => common_path('theme/default/i/pic_clients_hooyou.jpg')));
		$this->elementEnd('div');
		$this->elementStart('p', 'name');
		$this->element('a', array('href' => common_path('clients/fxext')), 'Firefox浏览器扩展');
		$this->elementEnd('p');
		$this->element('p', null, 'Firefox用户的贴心帮手，一键分享精彩页面。');
		$this->elementStart('p', 'op');
		$this->element('a', array('class' => 'download button60 orange60 clientdown', 'href' => 'https://addons.mozilla.org/zh-CN/firefox/addon/xgamepub/', 'c' => 'fxext'), '下载');
		$this->element('a', array('href' => common_path('clients/fxext')), '查看详情');
		$this->elementEnd('p');
		$this->elementEnd('li');
		//ChromeExt
		$this->elementStart('li');
		$this->elementStart('div', 'image');
		$this->element('img', array('src' => common_path('theme/default/i/pic_clients_hooyou.jpg')));
		$this->elementEnd('div');
		$this->elementStart('p', 'name');
		$this->element('a', array('href' => common_path('clients/chromeext')), 'Chrome浏览器扩展');
		$this->elementEnd('p');
		$this->element('p', null, 'Chrome用户的贴心帮手，一键分享精彩页面。');
		$this->elementStart('p', 'op');
		$this->element('a', array('class' => 'download button60 orange60 clientdown', 'href' => 'https://chrome.google.com/extensions/detail/iieiiledbklfbobfeljhanjlpdoincgm?hl=zh-cn', 'c' => 'chromeext'), '下载');
		$this->element('a', array('href' => common_path('clients/chromeext')), '查看详情');
		$this->elementEnd('p');
		$this->elementEnd('li');
		
		$this->elementEnd('ul');
	}
	
	function showRightsidebar() {
		$this->elementStart('dl', 'widget intro');
		$this->element('dt', null, common_config('site', 'name') . '客户端说明');
		$this->elementStart('dd');
		$this->elementStart('ul');
		$this->elementStart('li');
		$this->text(common_config('site', 'name') . '是一个开放的娱乐平台，所有客户端都是基于' . common_config('site', 'name') . ' API');
//		$this->element('a', array('href' => '#'), common_config('site', 'name') . ' API');
		$this->text('开发的');
		$this->elementEnd('li');
		$this->elementStart('li');
		$this->text('如果您对客户端开发感兴趣，可以加入' . common_config('site', 'name') . '开发者社区');
//		$this->element('a', array('href' => '#'), common_config('site', 'name') . '开发者社区');
		$this->text('，了解更多开发细节。');
		$this->elementEnd('li');
		$this->elementEnd('ul');
		$this->elementEnd('dd');
		$this->elementEnd('dl');
		
		$this->elementStart('dl', 'widget dashboard');
		$this->element('dt', null, '公告板');
		$this->elementStart('dd');
		$this->elementStart('ul');
		//Bookmark
		$this->elementStart('li');
		$this->text('现在可以通过');
		$this->element('a', array('href' => common_path('clients/bookmark')), '分享书签');
		$this->text('来分享喜欢的网站！');
		$this->elementEnd('li');
		//FxExt
		$this->elementStart('li');
		$this->text('Firefox扩展');
		$this->element('a', array('href' => common_path('clients/fxext')), 'XGamePub');
		$this->text('，快速分享工具！');
		$this->elementEnd('li');
		//ChromeExt
		$this->elementStart('li');
		$this->text('新发布Chrome浏览器扩展');
		$this->element('a', array('href' => common_path('clients/chromeext')), 'GamePub Button');
		$this->text('，可快速分享网页！');
		$this->elementEnd('li');
		//HooYou
		$this->elementStart('li');
		$this->text('号外！官方桌面端 - ');
		$this->element('a', array('href' => common_path('clients/hooyou')), '呼游 2010');
		$this->text('推出1.2版本！');
		$this->elementEnd('li');
		
		$this->elementEnd('ul');
		$this->elementEnd('dd');
		$this->elementEnd('dl');
		
		$this->showFeedbackWidget();
	}
	
	function showFeedbackWidget() {
    	$this->elementStart('dl', 'widget feedback');
    	$this->element('dt', null, '其他问题');
    	$this->elementStart('dd');
    	$this->text('如果您有问题或想要平台为您提供您想要的新功能，请点此');
    	$this->element('a', array('class' => 'button76 green76', 'href' => common_path('main/userfeedback'), 'target' => '_blank'), '反馈');
    	$this->elementEnd('dd');
    	$this->elementEnd('dl');
    }
	
	function showShaishaiScripts() {
		parent::showShaishaiScripts();
		$this->script('js/lshai_clients.js');
	}
}