<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class GroupblacklistHTMLTemplate extends GroupdesignHTMLTemplate
{
	function title()
    {
        return sprintf('被%s' . GROUP_NAME() . '屏蔽的游友',
                           $this->cur_group->nickname);
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
			$this->raw('共有<span>' . $total . '</span>个用户被屏蔽');
			$this->elementEnd('div');
		}

    	$blocked = $this->args['blocked'];
        if ($blocked && $blocked->N > 0) {
            $blocked_list = new GroupBlockList($this, $blocked, $this->cur_group, $this->cur_user);
            $blocked_list->show();
            $this->numpagination($total, 'groupblacklist', array('id' => $this->cur_group->id), 
				array(), PROFILES_PER_PAGE);
        } else {
        	$this->showEmptyList();
        }
	}
    
    function showEmptyList()
    {
        $emptymsg = array();
        $emptymsg['p'] = '如果本' . GROUP_NAME() . '受到骚扰，作为管理员，您可以将骚扰人屏蔽，他将被从此' . GROUP_NAME() . '中删除，以后也不能再加入本' . GROUP_NAME() . '。';
        $this->tu->showEmptyListBlock($emptymsg);
    }
    
    function showScripts() {
		parent::showScripts();
		
		$this->script('js/lshai_groupmembers.js');
	}
}

class GroupBlockList extends ProfileList
{
    var $group = null;

    function __construct($out, $profiles, $group, $cur_user)
    {
    	parent::__construct($out, $profiles, $group->getOwner(), $cur_user);

    	$this->group = $group;
    }

    function showOperations($profile) {    
    	$this->_showUnblockForm($this->group->id, $profile->id);
    }
    
    function _showUnblockForm($groupid, $profileid)
    {
    	$this->out->tu->startFormBlock(array('method' => 'post',
                                           			'class' => 'form_group_unblock',
                                           			'action' => common_local_url('groupunblock', array('id' => $groupid))),
    											'取消屏蔽');
		$this->out->hidden('profileid', $profileid);
		$this->out->element('input', array('class'=>'submit button60 orange60',
            		                        'name'=>'unblock', 'type'=>'submit',
    										'value' => '取消屏蔽'));
		$this->out->tu->endFormBlock();
    }
}