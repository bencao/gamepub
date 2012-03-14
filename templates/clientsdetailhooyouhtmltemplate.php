<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class ClientsdetailhooyouHTMLTemplate extends ClientsHTMLTemplate
{
	function othertitle() {
		return '来体验' . common_config('site', 'name') . '的更多玩法吧';
	}
	
	function title()
    {
    	return '呼游，连接您和其他游戏朋友的桥梁';
    }
    
	function metaKeywords() {
		return '呼游、GamePub桌面版、游戏截图工具、游戏即时分享工具';
	}
	
	function metaDescription() {
		return '呼游是GamePub的桌面版本，让您能够即时地收到朋友的分享，立刻对TA的消息进行回复和评论。除此之外，呼游还提供了极方便的一键截图功能，让您轻松记录和分享游戏中的难忘瞬间。';
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
		$this->element('dt', null, '呼游(' . common_config('site', 'name') . '桌面版)');
		$this->elementStart('dd');
		$this->raw('1. 第一时间收到您关注的人发的最新消息<br />' . '2. 实时接收和回复' . GROUP_NAME() . '消息<br />3. 支持游戏中一键把游戏窗口截图分享到' . common_config('site', 'name'));
		$this->elementEnd('dd');
		$this->elementEnd('dl');
		$this->elementStart('div', 'detail');
		$this->elementStart('p');
		$this->element('strong', null, '当前版本：');
		$this->text('HooYou 2010 V1.2');
		$this->elementEnd('p');
		$this->elementStart('p');
		$this->element('strong', null, '操作系统：');
		$this->text('WinXP， Vista，Win7');
		$this->elementEnd('p');
		$this->elementStart('p');
		$this->element('strong', null, '下载次数：');
		$this->text($this->trimmed('download_times'));
		$this->elementEnd('p');
		$this->elementStart('p', 'button');
		$this->element('a', array('class' => 'button99 green99 clientdown', 'href' => common_path('downloads/HooYou_v1.2_Setup.exe'), 'c' => 'hooyou'), '下载');
		$this->elementEnd('p');
		$this->elementEnd('div');
		$this->elementEnd('div');
	}
}