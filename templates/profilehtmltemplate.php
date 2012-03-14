<?php
/**
 * Shaishai, the distributed microblog
 *
 * Common parent of visitor actions
 *
 * PHP version 5
 *
 * @category  Login
 * @package   Shaishai
 * @author    Andray Ma <andray09@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

if (!defined('SHAISHAI')) {
	exit(1);
}

/**
 * Common parent of visitor actions
 *
 * @category Personal
 * @package  Shaishai
 * @author    Andray Ma <andray09@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */
class ProfileHTMLTemplate extends OwnerdesignHTMLTemplate
{
	
	var $page_owner;
	
    function show($args = array()) {
    	$this->page_owner = $args['owner'];
    	parent::show($args);
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
    		$this->raw('这是<strong>' . $this->page_owner->nickname . '</strong>的个人主页，想随时了解到' . Profile::displayName($this->page_owner->getProfile()->sex, false) . '的最新动态吗？');
    		$this->elementEnd('h2');
    		$this->element('p', null, common_config('site', 'name') . '是为网游玩家量身打造的，是国内第一家以用户为中心的网游互动社区，是现在最酷最火的玩家互动平台。');
    		$this->element('p', null, '现在注册' . common_config('site', 'name') . '，与游友们分享游戏的点滴和快乐。');
    		$this->element('a', array('href' => common_path('register?ivid=' . $this->page_owner->id), 'class' => 'toreg', 'rel' => 'nofollow', 'rel' => 'nofollow'), '');
    		$this->elementStart('p', 'tologin');
    		$this->text('已有' . common_config('site', 'name') . '账号？请');
    		$this->element('a', array('href' => common_path(''), 'class' => 'trylogin'), '登录');
    		$this->elementEnd('p');
    		$this->elementEnd('div');
    	}
    }
    
 	function showBody(){
        $this->elementStart('body', array('token' => common_session_token(), 'anonymous' => $this->cur_user ? '0' : '1'));
        if (Event::handle('StartShowHeader', array($this))) {
            $this->showHeader();
            Event::handle('EndShowHeader', array($this));
        }
		$this->elementStart('div', array('id' => 'wrap', 'class' => 'rounded5 clearfix'));
        $this->showCore();
        $this->elementEnd('div');
        
        if (Event::handle('StartShowFooter', array($this))) {
            $this->showFooter();
            Event::handle('EndShowFooter', array($this));
        }
        $this->showFloatbar();
        
        $this->showWaiter();
        
        $this->elementEnd('body');
    }
    
	function showCore() {
		$this->elementStart('div', array('id' => 'contents', 'class' => 'rounded5l'));
		$this->showContent();
		
		$this->elementEnd('div');
    	$this->elementStart('div', array('id' => 'widgets'));
		$this->showRightsidebar();
		$this->elementEnd('div');
	}
	
    // show the navigation of visitor's view
    // it can be overwritten when there is exception
	function showRightSection($page_owner_profile) {    	
		if($this->is_own) {
        	$navs = new NavList_MyNotices($page_owner_profile);
    	    $this->tu->showNavigationWidget($navs->lists(), $this->trimmed('action'));
        } else {
            $navs = new NavList_Visitor($page_owner_profile, $this->is_own);
    	    $this->tu->showNavigationWidget($navs->lists(), $this->trimmed('action'), ! $this->cur_user);
        }
        
    }
	
	function showRightsidebar() {
		
    	$page_owner_profile = $this->args['profile'];
    	
		$this->tu->showProfileDetailWidget($page_owner_profile);
        
    	$this->tu->showSubInfoWidget($page_owner_profile, $this->is_own, ! $this->cur_user);
    	
    	$this->showRightSection($page_owner_profile);

    	$this->tu->showGroupsWidget($page_owner_profile, 6, $this->is_own, ! $this->cur_user);
    	$subscriptions = $page_owner_profile->getSubscriptions(0, 18);
    	$this->tu->showUserListWidget($subscriptions, Profile::displayName($page_owner_profile->sex, $this->is_own) . '关注的人');
    	if ($this->cur_user) {
    		$this->tu->showInviteWidget();
    	}
    }
    
    function showContent() {
    	$this->tu->showUserSummaryBlock($this->args['profile'], $this->cur_user, $this->page_owner);
    	//$this->showIllegal();
    }
	
	function showIllegal() {
		$this->elementStart('div', array('id'=>'illegalreport', 'title'=>'非法举报', 'style'=>'display:none'));
		
		$this->elementStart('dl', 'b_el');
		$this->element('dt', null, '您将要举报用户：'. $this->page_owner->nickname);
		$this->elementEnd('dl');
		
		$this->elementStart('dl', 'b_el');
		$this->element('dt', null, '您的举报将被严格保密，我们将认真阅读您的举报信息并适当处理。');
		$this->elementEnd('dl');
		
		$this->elementStart('div', 'b_cbf');
		$form = new IllegalReportForm($this, 1, $this->args['profile']->id);
        $form->show();
        $this->elementEnd('div');
        
        $this->elementEnd('div');
	}
	
    function showScripts() {
		parent::showScripts();
    	$this->script('js/lshai_relation.js');
    	$this->script('js/lshai_showstream.js');
    	$this->script('js/ZeroClipboard.js');
	}
	
}