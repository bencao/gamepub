<?php


if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Cancel another user from an admin of a group
 *
 * @category Group
 * @package  ShaiShai
 * @author   Andray
 */
class GroupcanceladminAction extends GroupUserAdminAction
{

    function prepare($args)
    {
        if (! parent::prepare($args)) {return false;}
        
        if (! $this->cur_group->isOwnedBy($this->cur_user)) {
            $this->clientError('您不是该' . GROUP_NAME() . '的创建者，没有权限设置取消一个管理员。', 403);
            return false;
        }
        
        return true;
    }

    function handle($args)
    {
        parent::handle($args);

        if (! $this->cur_group->toggleAdmin($this->op_user, false)) {
        	$this->serverError('取消管理员时发生错误，请稍后再试！');
        	return false;
        }
        
    	if ($this->boolean('ajax')) {
    		$this->showJsonResult(array('result'=>'successful', 
    			'pid' => $this->op_user->id, 
    			'groupid' => $this->cur_group->id,
            	'action' => common_path('group/' . $this->cur_group->id . '/makeadmin')));
        } else {
            common_redirect(common_path('group/' . $this->cur_group->id . '/members'),
                            303);
        }
    }
}
