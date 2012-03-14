<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class PublicbaseHTMLTemplate extends BasicHTMLTemplate
{		
	function showShaishaiStylesheets() {
		parent::showShaishaiStylesheets();
      	$this->cssLink('css/public.css','default','screen, projection');
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
    		$this->elementStart('div', array('id' => 'header_anonymous_public', 'class' => 'anonymous'));
    		$this->element('a', array('href' => common_path(''), 'title' => '回到登录页面', 'class' => 'lg'));
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
    
    function showBody()
    { 
        $this->elementStart('body', array('token' => common_session_token(), 'anonymous' => $this->cur_user ? '0' : '1'));
        if (Event::handle('StartShowHeader', array($this))) {
            $this->showHeader();
            Event::handle('EndShowHeader', array($this));
        }
        
        $this->elementStart('div', array('id' => 'public_outter_wrap', 'class' => 'clearfix'));
        $this->element('div', array('id' => 'appbg'));
		$this->elementStart('div', array('id' => 'public_wrap', 'class' => 'clearfix'));
        $this->showCore();
        $this->elementEnd('div');
        $this->elementEnd('div');
        
        if (Event::handle('StartShowFooter', array($this))) {
            $this->showFooter();
            Event::handle('EndShowFooter', array($this));
        }        
        $this->showFloatBar();
        
        $this->showWaiter();
        
        $this->elementEnd('body');
    }
    
	function showFooter()
    {
        $this->elementStart('div', array('id' => 'footer', 'class' => 'public'));
        $this->showFooterSpans();
        $this->showSecondaryNav();
        $this->elementEnd('div');
    }
    
	function showLeftNav() {
		$this->elementStart('div', array('id' =>'public_nav'));
    	$this->elementStart('h2');
    	$this->elementStart('span');
    	$this->element('em', 'all', '酒馆地带');
    	$this->elementEnd('span');
    	$this->elementEnd('h2');
    	
    	$classMap = array('public' => 'index', 
    							  'wenda' => 'requestforhelp', 
    							  'flashgame' => 'minigames',
    							  'halloffame' => 'famouspeople', 
    							  'rank' => 'rank', 
//    							  'hottopics' => 'hottopics',
    							  'hotnotice' => 'hotnotices',
//    							  'funnypeople' => 'funnypeople',
    							  'citypeople' => 'citypeople');
									
    	$publiclist = new NavList_Public();
    	
    	$this->tu->showLeftNav($publiclist->lists(), $classMap, $this->trimmed('action'), ! $this->cur_user);
    	
    	$this->elementEnd('div');	
	}
}