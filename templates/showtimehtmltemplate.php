<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class ShowtimeHTMLTemplate extends BasicHTMLTemplate
{
	
	function title()
    {
        return common_config('site', 'name') . '介绍视频';
    }
    
	function metaKeywords() {
		return 'GamePub，游戏酒馆，玩家社区，公会，部落，家族，记录旅程，游戏截图，一键上传';
	}
	
	function metaDescription() {
		return '游戏酒馆是一个可以使您与游戏朋友的沟通交流变得更开放，更有趣的社区。在这里寻找同服务器、同游戏玩家，了解他们结识他们，您会感觉到朋友一下变多了。';
	}
	
	function showCore() {
		$this->elementStart('div', 'stwrap');
		$this->element('h2', null, '欢迎您来到GamePub');
		$this->element('h3', null, '这里是网游玩家的专属社区，游戏人在这里记录游戏中的点点滴滴，分享精彩与快乐。让我们享受游戏，享受生活。以下是我们的第一个宣传片：分享游戏中的精彩瞬间，记录你的游戏历程。');
		$this->elementStart('div', 'player');
		//$this->raw('<embed src="http://player.youku.com/player.php/sid/XMTczODgyNzI0/v.swf" quality="high" width="529" height="425" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" flashvars="isAutoPlay=true"></embed>');
		$this->raw('<embed type="application/x-shockwave-flash" src="http://static.youku.com/v1.0.0122/v/swf/qplayer.swf" width="100%" height="100%" id="movie_player" name="movie_player" bgcolor="#FFFFFF" quality="high" allowfullscreen="true" allowscriptaccess="always" flashvars="ShowId=0&amp;Cp=0&amp;Tid=0&amp;VideoIDS=XMTk4NDUwODQ0&amp;isAutoPlay=true&amp;Version=/v1.0.0601&amp;winType=interior&amp;iku_num=2" pluginspage="http://www.macromedia.com/go/getflashplayer"></embed>');
		$this->elementEnd('div');
		$this->elementEnd('div');
	}
	
	function showUAStylesheets () {
		if (Event::handle('StartShowUAStyles', array($this))) {
			$this->comment('[if lt IE 8]><link href="'.theme_path('css/ie.css', 'default').'?'.SHAISHAI_VERSION.'" rel="stylesheet" type="text/css" />'
				.'<![endif]');
			$this->comment('[if IE 6]><link href="'.theme_path('css/ie6.css', 'default').'?'.SHAISHAI_VERSION.'" rel="stylesheet" type="text/css" />'
				.'<![endif]');
			Event::handle('EndShowUAStyles', array($this));
		}
	}
	
	function showFooter() {}
	
	function showShaishaiStylesheets() {
		parent::showShaishaiStylesheets();
		$this->cssLink('css/showtime.css', 'default');
	}
}