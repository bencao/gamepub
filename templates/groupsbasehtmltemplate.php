<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class GroupsbaseHTMLTemplate extends BasicHTMLTemplate
{	
	function showFooter()
    {
        $this->elementStart('div', array('id' => 'footer', 'class' => 'public'));
        $this->showFooterSpans();
        $this->showSecondaryNav();
        $this->elementEnd('div');
    }
	
	function showCore() {
		$this->showLeftNav();
		
		$this->elementStart('div', array('id' => 'public_contents', 'style' => 'width: 750px;'));
		$this->showContent();
		$this->elementEnd('div');
	}
	
	function showContent() {
		
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
    		$this->element('a', array('href' => common_local_url('register'), 'class' => 'toreg', 'rel' => 'nofollow'), '');
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
	
	function showLeftNav() {
		$this->elementStart('div', array('id' => 'public_nav'));
		$this->elementStart('h2');
		$this->elementStart('span');
		$this->element('em', array('class' => 'group'),  GROUP_NAME() . '地带');
		$this->elementEnd('span');
		$this->elementEnd('h2');
									
		$classMap = array('groups' => 'my', 
						  'gamegroups' => 'game',
						  'lifegroups' => 'life',
						  'auditgroups' => 'app');
		
    	$grouplist = new NavList_Group();
    	
    	$this->tu->showLeftNav($grouplist->lists(), $classMap, $this->trimmed('action'), ! $this->cur_user);
    	
    	$this->elementEnd('div');
	}
	
	function showShaishaiStylesheets() {
        parent::showShaishaiStylesheets();
    	$this->cssLink('css/public.css','default','screen, projection');
    }
    
	function showGroupSearchForm() {
		$this->tu->startFormBlock(array('action' => common_path('search/group'),
    								'class' => 'clearfix', 'id' => 'search_form'), '搜索' . GROUP_NAME());
    	
    	$this->element('label', array('for' => 'q'),'搜索' . GROUP_NAME());

		$this->element('input', array('id' => 'q',
                                           'class' => 'text200',
                                           'name' => 'q',
                                           'type' => 'text'));
		
		$this->element('input', array('class' => 'submit button60 green60',
                                           'type' => 'submit',
                                           'value' => '搜索'));

		$this->element('span', null, '输入名字、游戏、服务器、聚集地等信息进行搜索');
    	
    	$this->tu->endFormBlock();    	
    }
}
?>