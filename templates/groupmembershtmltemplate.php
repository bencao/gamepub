<?php

if (!defined('SHAISHAI')) {
	exit(1);
}

class GroupmembersHTMLTemplate extends GroupdesignHTMLTemplate
{
	function title()
	{
		return '' . GROUP_NAME() .  '成员列表';
	}

	function getPage()
	{
		//右边导航的位置,从0开始: 公会主页, 公会成员, 编辑公会
		return 1;
	}
	
	function showContent()
	{
		$this->tu->showTitleBlock('' . GROUP_NAME() . '成员', 'groups');
		
		$navs = new NavList_GroupMembers($this->cur_group, $this->is_group_admin);
        $this->tu->showTabNav($navs->lists(), $this->trimmed('action'));
		 
		$this->showTabContent();
	}
	
	function showTabContent()
	{
        if(!$this->cur_group->hasMember($this->cur_user) && $this->cur_group->grouptype) {
            $this->text('此' . GROUP_NAME() . '是私有' . GROUP_NAME() . '，您尚未加入，不能查看成员列表', 404);
            return;
        } else {	
			$total = $this->cur_group->memberCount();
			if ($total > 0) {
				$this->elementStart('div', array('id' => 'totalitem'));
				$this->raw('本' . GROUP_NAME() . '共有<span>' . $total . '</span>名成员');
				$this->elementEnd('div');
			}
			
			$offset = ($this->cur_page-1) * PROFILES_PER_PAGE;
			$limit =  PROFILES_PER_PAGE;
			$members = $this->cur_group->getMembers($offset, $limit);
	
			if ($members && $members->N > 0) {
				$member_list = new GroupMemberList($this, $members, $this->cur_group, $this->cur_user);
				$member_list->show();
			} else {
				$this->showEmptyList();
			}
	
			$members->free();
			
			$this->numpagination($total, 'groupmembers', array('id' => $this->cur_group->id), 
					array(), PROFILES_PER_PAGE);
        }
	}

	function showEmptyList()
	{
		$emptymsg = array();
		$emptymsg['p'] = '这是' . $this->cur_group->uname . '的成员列表 ，但是还没人加入本' . GROUP_NAME() . '。';
		$this->tu->showEmptyListBlock($emptymsg);
	}

	function showScripts() {
		parent::showScripts();
		$this->script('js/lshai_groupmembers.js');
		$this->script('js/lshai_relation.js');
	}

}

class GroupMemberList extends ProfileList
{
    var $cur_group = null;
    var $is_group_admin = false;
    var $is_group_owner = false;

    function __construct($out, $profiles, $cur_group, $cur_user)
    {
        $this->cur_group = $cur_group;
        $this->cur_user = $cur_user;
        $this->is_group_admin = $this->cur_group->hasAdmin($this->cur_user);
        $this->is_group_owner = $this->cur_group->isOwnedBy($this->cur_user);
        parent::__construct($out, $profiles,  $cur_group->getOwner(), $this->cur_user);
    }
    
    //管理员有屏蔽普通用户的权限, 公会拥有者有加/删管理员和屏蔽普通用户和管理员的权限
    function showOperations($profile) {
    	if (! $this->cur_user || $profile->id == $this->cur_user->id){
    		return;
    	}
    	
    	$isSubscribed = $this->cur_user->isSubscribed($profile);
    	$isBeingSubscribed = $profile->getUser()->isSubscribed($this->cur_user);
    	
    	if (! $isSubscribed) {
    		$this->_showSubscribe($profile);
    	}else{
    		$this->_showSubscribed($profile);
    	}
    	
    	$this->_startMoreButton($profile);
    	
    	if ($isSubscribed) {
	    	$this->out->elementStart('li');
	    	$this->out->element('a', array('class' => 'unsubscribe', 'title' => '取消关注', 'href' => '#', 'to' => $profile->id, 'url' => common_local_url('unsubscribe')), '取消关注');
	    	$this->out->elementEnd('li');
    	}
    	
    	if ($isBeingSubscribed) {
	    	$this->out->elementStart('li');
	    	$this->out->element('a', array('class' => 'msg', 'title' => '悄悄话', 'href' => common_local_url('newmessage'), 'to' => $profile->id, 'nickname' => $profile->nickname), '悄悄话');
	    	$this->out->elementEnd('li');
    	}
    	
    	$this->out->elementStart('li');
    	$this->out->element('a', array('class' => 'at', 'title' => '对TA说', 'href' => common_local_url('newnotice', array('replyto' => $profile->uname)), 'to' => $profile->id, 'nickname' => $profile->nickname), '对TA说');
    	$this->out->elementEnd('li');
    	
    	$profile_is_admin = $this->cur_group->hasAdmin($profile);
    	
    	if($this->is_group_owner || 
    			($this->is_group_admin && !$profile_is_admin)){	
            
	    	$this->out->elementStart('li');
	    	$this->out->element('a', array('class' => 'groupblock', 'title' => '屏蔽用户', 'href' => common_local_url('groupblock', array('id' => $this->cur_group->id)), 'groupid' => $this->cur_group->id, 'profileid' => $profile->id, 
	    		'next_url' => common_local_url('groupmembers', array('id' => $this->cur_group->id))), '屏蔽用户');
	    	$this->out->elementEnd('li');
    	}
    	
    	if($this->is_group_owner){    		
	    	$this->out->elementStart('li');
	    	if (!$profile_is_admin) {
		    	$this->out->element('a', array('class' => 'makeadmin', 'title' => '加管理员', 'href' => common_local_url('groupmakeadmin', array('id' => $this->cur_group->id)), 'groupid' => $this->cur_group->id, 'profileid' => $profile->id), '加管理员');
	        } else {
		    	$this->out->element('a', array('class' => 'canceladmin', 'title' => '删管理员', 'href' => common_local_url('groupcanceladmin', array('id' => $this->cur_group->id)), 'groupid' => $this->cur_group->id, 'profileid' => $profile->id), '删管理员');
	        }
	    	$this->out->elementEnd('li');
    	}
    	
    	$this->_endMoreButton($profile);
    }
}
