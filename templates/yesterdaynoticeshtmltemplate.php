<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class YesterdaynoticesHTMLTemplate extends BasicHTMLTemplate
{
	function title()
    {
    	return 'GamePub最近更新的500条消息';
    }
    
	function metaKeywords() {
		return 'GamePub，游戏酒馆，玩家社区，公会，部落，家族，记录旅程，游戏截图，一键上传';
	}
	
	function metaDescription() {
		return '游戏酒馆是一个可以使您与游戏朋友的沟通交流变得更开放，更有趣的社区。在这里寻找同服务器、同游戏玩家，了解他们结识他们，您会感觉到朋友一下变多了。';
	}
	
    function show($args) {
    	$this->notice = $args['notice'];
    	parent::show($args);
    }
    
	function showBody()
    {
        $this->elementStart('body', array('token' => common_session_token(), 'anonymous' => $this->cur_user ? '0' : '1'));
        
//        if (Event::handle('StartShowHeader', array($this))) {
//            $this->showHeader();
//            Event::handle('EndShowHeader', array($this));
//        }

        $this->elementStart('div', array('id' => 'wrap', 'style' => 'background:#fff;padding:50px 20px;'));
        $this->showCore();
        $this->elementEnd('div');
        
        $this->elementEnd('body');
    }
    
    function showCore() {
   		$nl = new NoLimitNoticeList($this->notice, $this);
   		$nl->show();
    }
    
//    function showShaishaiStylesheets() {
//    	parent::showShaishaiStylesheets();
//    	$this->elementStart('style');
//    	$this->raw('body{background:#000 url(/images/wow.jpg) center top;height:1200px;}a.enter{width:616px;height:57px;display:block;margin:910px auto 0;text-indent:-999px;}');
//    	$this->elementEnd('style');
//    }
    
    function showUAStylesheets () {
    	
    }
}

class NoLimitNoticeList extends NoticeList {
	function show()
    {
    	$this->out->elementStart('ol', array('id' => 'notices'));
        $cnt = 0;

        while ($this->notice != null 
        	&& $this->notice->fetch()) {
            $cnt++;
                        
            $item = $this->newListItem($this->notice);
            $item->show();
        }
        
        if (0 == $cnt) {
            $this->out->showEmptyList();
        }
        
        $this->out->elementEnd('ol');
        
        return $cnt;
    }
}