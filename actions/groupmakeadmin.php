<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Make another user an admin of a group
 *
 * @category Group
 * @package  ShaiShai
 */

class GroupmakeadminAction extends GroupUserAdminAction
{
    function prepare($args)
    {
        if (! parent::prepare($args)) {return false;}
        
        if (! $this->cur_group->isOwnedBy($this->cur_user)) {
            $this->clientError('您不是该' . GROUP_NAME() . '的创建者，没有权限设置另外一个管理员。', 403);
            return false;
        }
        
        $adminNum = Group_member::getAdminNum($this->cur_group->id);
        if ($adminNum>=5) {
        	$this->clientError('一个' . GROUP_NAME() . '最多可以设置5个管理员。');
            return false;
        }

        return true;
    }

    function handle($args)
    {
        parent::handle($args);

        if (! $this->cur_group->toggleAdmin($this->op_user, true)) {
        	$this->serverError('增加管理员时发生错误，请稍后再试！');
        	return false;
        }
        
        // Send a system message to the user who just become admin
        $content = '您已经被设置为' . GROUP_NAME() . ' ' . $this->cur_group->nickname . ' 的管理员，您已拥有编辑' . GROUP_NAME() . '信息、管理' . GROUP_NAME() . '成员等权限。';
    	$rendered = '您已经被设置为' . GROUP_NAME() . ' ' . common_group_linker($this->cur_group). ' 的管理员，您已拥有编辑' . GROUP_NAME() . '信息、管理' . GROUP_NAME() . '成员等权限。';
    	System_message::saveNew(array($this->op_user->id), $content, $rendered, 1);
        
        if ($this->boolean('ajax')) {
        	$this->showJsonResult(array('result'=>'successful', 
        		'pid' => $this->op_user->id, 
        		'groupid' => $this->cur_group->id,
            	'action' => common_path('group/' . $this->cur_group->id . '/canceladmin')));
        }else {
	        common_redirect(common_path('group/' . $this->cur_group->id . '/members'),
                            303);
        }
    }
}
