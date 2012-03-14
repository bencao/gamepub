<?php

if (!defined('SHAISHAI')) {
    exit(1);
}


class GroupinvitationHTMLTemplate extends GroupdesignHTMLTemplate
{
    function title()
    {
        return '邀请好友加入' . GROUP_NAME() . '';
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
		$this->element('input', array('name' => 'newlifegroupok',
                                      'type' => 'hidden',
                                      'id' => 'newlifegroupok',
                                      'value' => $this->trimmed('newgroupok')));
		
		// handle the msg from updategroupcode action
    	if(array_key_exists('upcodemsg', $_SESSION)) {
        	if (array_key_exists('updatecodesuc', $_SESSION)) {
        		$this->element('div', 'success', $_SESSION['upcodemsg']);
        		unset($_SESSION['updatecodesuc']);
        	}else {
    		    $this->element('div', 'error', $_SESSION['upcodemsg']);
        	}
        	unset($_SESSION['upcodemsg']);
    	}
    	
		$this->elementStart('div', 'invite');
		$this->element('h3', null, '发送邀请链接给朋友');
		$this->elementStart('p');
		$this->text('复制下面的链接，用QQ,MSN发送邀请链接');
		$this->element('a', array('class' => 'help', 'href' => common_local_url('doc', array('type' => 'help', 'title' => 'invite')), 'target' => '_blank'), '邀请奖励');
		$this->elementEnd('p');
		$this->elementStart('p', 'clearfix');
		$this->element('input', array('id'=>'ivlink', 'class' => 'text', 'type' => 'text', 'readonly' => 'readonly',
			'value'=>common_local_url('register', null, array('givid' => $this->cur_group->getGroupIvid()))));
		$this->element('a', array('id'=>'ivbtn', 'class' => 'copy button76 green76', 'href' => '#'), '复制链接');
		$this->element('a', array('class' => 'refresh', 'href'=>common_local_url('groupupdatecode', array('id'=>$this->cur_group->id))), '更新邀请码');
		$this->elementEnd('p');
		$this->elementEnd('div');

    	$offset = ($this->cur_page-1) * PROFILES_PER_PAGE;
        $limit =  PROFILES_PER_PAGE;
        
        $subscribers = $this->cur_user->getSubscribers($offset, $limit);
        
        if ($subscribers && $subscribers->N > 0) {
        	$this->element('a', 
        					array('class' => 'batchinvite', 
        							'userid' => $this->cur_user->id,
        							'href' => common_local_url('groupbatchinvite', array('id'=>$this->cur_group->id))),
        					'邀请本页好友');
            $subscribers_list = new GroupInviteList($this, $subscribers, $this->cur_group, $this->cur_user);
            $subscribers_list->show();
        } else {
        	$this->showEmptyList();
        }

        $subscribers->free();
        
        $total = $this->cur_user->getProfile()->subscriberCount();

        $this->numpagination($total, 'groupinvitation', array('id' => $this->cur_group->id), 
				array(), PROFILES_PER_PAGE);
	}
    
    function showEmptyList()
    {
        $emptymsg = array();
        $emptymsg['p'] = '您没有关注者，或此页的所有关注者都已加入' . GROUP_NAME() . '。';
        $this->tu->showEmptyListBlock($emptymsg);
    }
    
    function showScripts() {
		parent::showScripts();

		$this->script('js/lshai_groupinvite.js');
		$this->script('js/ZeroClipboard.js');
		$this->script('js/lshai_newgroupok.js');
	}
	

	function showShaishaiStylesheets() {
          if (Event::handle('StartShowShaishaiStyles', array($this))) {
            	$this->cssLink('css/jqueryui/jui.css', 'default', 'screen, projection, tv');
                $this->cssLink('css/base.css','default','screen, projection');
                $this->cssLink('css/public.css','default','screen, projection');
                $this->cssLink('css/settings.css','default','screen, projection');
                Event::handle('EndShowShaishaiStyles', array($this));
          }
    }
}


class GroupInviteList extends ProfileList
{
	var $group = null;
	var $batchid = "";


	function __construct($out, $profiles, $group, $cur_user)
    {
    	parent::__construct($out, $profiles, $group->getOwner(), $cur_user);
    	$this->group = $group;
    }
    
    function show()
    {
    	$this->out->elementStart('ol', array('id' => 'users'));
        $cnt = 0;
        while ($this->profiles->fetch()) {
            
            if($cnt > PROFILES_PER_PAGE) {
                break;
            }
            if ($this->group->isOwnedBy($this->profiles)) {
            	// ignore ownerself
            	continue;
            }
            if ($this->group->hasMember($this->profiles)) {
            	// ignore group member
            	continue;
            }
            if ($this->group->hasBlocked($this->profiles)) {
            	// ignore group blocked
            	continue;
            }
            $cnt++;
            
            $this->out->elementStart('li', array('class' => 'user', 'pid' => $this->profiles->id));
            $this->showAvatar($this->profiles);
            $this->showNickname($this->profiles);
            $this->showInfos($this->profiles);
            $this->out->elementStart('div', 'op');
            $this->showOperations($this->profiles);
            $this->out->elementEnd('div');
            $this->out->elementEnd('li');
        }
        $this->out->elementEnd('ol');
        
        $this->out->element('input', array('type' => 'hidden', 'id' => 'batchinvite_ids',
        							 'name' => 'batchid', 'value' => $this->batchid));
        
        return $cnt;
    }

    function showOperations($profile) {    	
	    if(Group_invitation::isInvitSend($this->group->id, $profile->id)){
	    	$this->out->element('div', 'done', '已发送');
	    }else{
	    	if(strlen($this->batchid)>0)
	    		$this->batchid .= '-';
	    	$this->batchid .= $profile->id;
	    	$this->_showInvitegroupForm($this->group->id, $profile->id);
	    }
    }
    
    function _showInvitegroupForm($groupid, $profileid)
    {
    	$this->out->tu->startFormBlock(array('method' => 'post',
                                           			'class' => 'form_group_invite',
                                           			'action' => common_local_url('groupinvite', array('id' => $groupid))),
    											'发送' . GROUP_NAME(). '邀请');
		$this->out->hidden('profileid', $profileid);
    	$this->out->element('input', array('class'=>'submit button76 orange76',
            		                       'name'=>'invitebutton', 'type'=>'submit',
            		                       'value'=>'发送邀请'));
		$this->out->tu->endFormBlock();
    }
    
}