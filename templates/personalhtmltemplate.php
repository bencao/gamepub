<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class PersonalHTMLTemplate extends OwnerdesignHTMLTemplate
{
	var $page_owner;
	var $cur_user;
	var $navs;
	
    function title()
    {
        return $this->page_owner->uname . " 的个人空间";
    }
    
    function showNoticeForm() {
    	$nform = new ShaiNoticeForm($this);
    	$nform->show();
    }
    
    function showContentInfo() {    	
    }
    
    function showNoticeList($notice, $class) {
    	$nl = new PersonalNoticeList($notice, $class);
        $cnt = $nl->show();
        return $cnt;
    }
    
    function showContent() {
        if($this->cur_user->id == $this->page_owner->id) { 
    		$this->showNoticeForm();
        }
        
    	$this->showContentInfo();    
    }
    
    function showRightSection($templateutil, $page_owner_profile) {
    	 //全局变量, 可以随时添加
    	 // 滑动导航仅在个人首页显示
//    	$this->navs = new NavList_Home($page_owner_profile);
//    	
//    	//$templateutil->showMainPanelNavigationBlock($this->navs->lists(), $this->trimmed('action'));
//    	$templateutil->showNavigationWidget($this->navs->lists(), $this->trimmed('action'));
    }
    
    function showSecondaryPanel($page_owner_profile) {
    	$this->tu->showSecondaryPanelNavigationBlock($page_owner_profile);
    }
    
    function showRightsidebar() {
    	$page_owner_profile = $this->page_owner->getProfile();
    	
    	$this->tu->showOwnerInfoWidget($page_owner_profile);
    	$this->tu->showSubInfoWidget($page_owner_profile, $this->is_own);
    	$this->tu->showToolbarWidget($page_owner_profile);
    	$this->showRightSection($this->tu, $page_owner_profile);
    	$this->tu->showGroupsWidget($page_owner_profile, 6);
    	$subscriptions = $page_owner_profile->getSubscriptions(0, 18);
    	$this->tu->showUserListWidget($subscriptions, '我关注的人');
    	$this->tu->showInviteWidget();
    	
    	$this->tu->showTagcloudWidget();
    }
    
	function showEmptyList()
    {
    	if($this->cur_user->id != $this->page_owner->id) { 
        	$message = sprintf('这是  %s 和好友的时间表 , 但是还没人发消息.', 
        			$this->page_owner->uname) . ' ';
    	} else {
        	$message = sprintf('这是 您和好友的时间表 , 但是还没人发消息.', 
        			$this->page_owner->uname) . ' ';    		
    	}
        
        $this->elementStart('div', 'b_ph guide');
        $this->raw(common_markup_to_html($message));
        $this->elementEnd('div');
    }
    
    function show($args = array()) {
//    	$this->cur_user = common_current_user();
    	$this->page_owner = $args['user'];
//    	$this->tu = new TemplateUtil($this);
    	parent::show($args);	
    }
    
    function showScripts() {
    	parent::showScripts();
    	$this->script('js/lshai_tagcloud_min.js');
    	//$this->script('js/lshai_group_link.js');
    }
    
}

class PersonalNoticeList extends NoticeList
{
    function newListItem($notice)
    {
        return new PersonalNoticeListItem($notice, $this->out);
    }
}

class PersonalNoticeListItem extends NoticeListItem
{
    function showNoticeInfo()
    {
    	$this->out->elementStart('div', array('class' => 'content'));
    	if($this->user->game_id != $this->profileUser->game_id) {
			$game = Game::staticGet('id', $this->profileUser->game_id); 
			$text = '[<span class="tag"><a rel="tag" target="_blank" href="' . 
	    				common_local_url('game', array('gameid' => $game->id)) .
	    				'">' . $game->name . '</a></span>]';
	    	$this->out->raw($text);			
		}    				
    	//$this->out->raw($text);
    	$this->out->raw($this->notice->rendered);
    	$this->out->elementEnd('div');
    }
}
?>