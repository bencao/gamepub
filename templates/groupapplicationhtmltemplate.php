<?php

if (!defined('SHAISHAI')) {
    exit(1);
}



class GroupapplicationHTMLTemplate extends GroupdesignHTMLTemplate
{
    function title()
    {
        return '' . GROUP_NAME() . '待处理请求';
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
		$total = $this->args['total'];
		if ($total > 0) {
			$this->elementStart('div', array('id' => 'totalitem'));
			$this->raw('共有<span>' . $total . '</span>个待处理加入请求');
			$this->elementEnd('div');
		}
		
		$applicants = $this->args['subs'];
		if ($applicants && $applicants->N > 0) {
	    	$subscribers_list = new GroupApplicationList($this, $applicants, $this->cur_group, $this->cur_user);
	        $subscribers_list->show();
	        $this->numpagination($total, 'groupapplication', array('id' => $this->cur_group->id), 
				array(), PROFILES_PER_PAGE);
		} else {
			$this->showEmptyList();
		}
	}

    function showEmptyList()
    {
        $emptymsg = array();
        $emptymsg['p'] = '所有的加入申请已被处理， 暂时没有新人申请加入！';
        $this->tu->showEmptyListBlock($emptymsg);
    }
    
    function showScripts() {
		parent::showScripts();
		
		$this->script('js/lshai_groupapplication.js');
	}
    
}

class GroupApplicationList extends ProfileList
{
    var $group = null;

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
            $cnt++;
            
            if($cnt > PROFILES_PER_PAGE) {
                break;
            }
            $profile = Profile::staticGet($this->profiles->inviteeid);
            
            $this->out->elementStart('li', array('class' => 'user', 'pid' => $profile->id));
            $this->showAvatar($profile);
            $this->showNickname($profile);
            $this->showInfos($profile);
            $this->showMessage($this->profiles->message);
            $this->out->elementStart('div', 'op');
            $this->showOperations($profile);
            $this->out->elementEnd('div');
            $this->out->elementEnd('li');
        }
        $this->profiles->free();
        
        $this->out->elementEnd('ol');
        
        return $cnt;
    }
    
    function showMessage($msg)
    {
    	$this->out->elementStart('p', 'msg rounded5');
    	$this->out->element('span', 'app', $msg);
    	$this->out->element('span', 'pointer');
    	$this->out->elementEnd('p');
    }

    function showOperations($profile) 
    {    
        $this->_showApproveForm($this->group->id, $profile->id);
        $this->_showRejectForm($this->group->id, $profile->id);
    }
    
    function _showApproveForm($groupid, $profileid) 
    {
    	$this->out->tu->startFormBlock(array('method' => 'post',
                                           			'class' => 'form_application',
                                           			'action' => common_local_url('groupapprove', array('id' => $groupid))),
    											'批准申请');
		$this->out->hidden('profileid', $profileid);
		$this->out->element('input', array('class'=>'submit button60 orange60',
            		                        'name'=>'approvejoin', 'type'=>'submit',
    										'value' => '通过'));
		$this->out->tu->endFormBlock();
    }
    
	function _showRejectForm($groupid, $profileid) 
    {
    	$this->out->tu->startFormBlock(array('method' => 'post',
                                           			'class' => 'form_application',
                                           			'action' => common_local_url('groupreject', array('id' => $groupid))),
    											'拒绝申请');
		$this->out->hidden('profileid', $profileid);
		$this->out->element('input', array('class'=>'submit button60 silver60',
            		                        'name'=>'rejectjoin', 'type'=>'submit',
    										'value' => '拒绝'));
		$this->out->tu->endFormBlock();
    }
}
