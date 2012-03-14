<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class ClientsdetailfxextHTMLTemplate extends ClientsHTMLTemplate
{
	function othertitle() {
		return '来体验' . common_config('site', 'name') . '的更多玩法吧';
	}
	
	function title()
    {
    	return 'XGamePub，Firefox用户的一键分享工具';
    }
    
	function metaKeywords() {
		return 'XGamePub，一键分享工具';
	}
	
	function metaDescription() {
		return 'XGamePub是GamePub团队开发的Firefox扩展，方便Firefox浏览器用户将有趣的网页直接分享到GamePub的一个小工具。';
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
		$this->element('dt', null, 'XGamePub(Firefox浏览器扩展)');
		$this->elementStart('dd');
		$this->raw('随时随地一键把Firefox的当前页面分享到' . common_config('site', 'name'));
		$this->elementEnd('dd');
		$this->elementEnd('dl');
		$this->elementStart('div', 'detail');
		$this->elementStart('p');
		$this->element('strong', null, '当前版本：');
		$this->text('XGamePub 1.0');
		$this->elementEnd('p');
		$this->elementStart('p');
		$this->element('strong', null, '浏览器：');
		$this->text('Firefox');
		$this->elementEnd('p');
		$this->elementStart('p');
		$this->element('strong', null, '下载次数：');
		$this->text($this->trimmed('download_times'));
		$this->elementEnd('p');
		$this->elementStart('p', 'button');
		$this->element('a', array('class' => 'button99 green99 clientdown', 'href' => 'https://addons.mozilla.org/zh-CN/firefox/addon/xgamepub/', 'c' => 'fxext'), '下载');
		$this->elementEnd('p');
		$this->elementEnd('div');
		$this->elementEnd('div');
	}
}