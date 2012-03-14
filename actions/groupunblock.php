<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Unblock a user from a group
 *
 * @category Action
 * @package  ShaiShai
 */
class GroupunblockAction extends GroupUserAdminAction
{
	
    function prepare($args)
    {
        if (! parent::prepare($args)) {return false;}
        
        if (!$this->cur_group->hasBlocked($this->op_user)) {
            $this->clientError('此用户不在被屏蔽名单中。');
            return false;
        }
        return true;
    }

    function handle($args)
    {
        parent::handle($args);

        $result = Group_block::unblockUser($this->cur_group, $this->op_user);

        if (!$result) {
            $this->serverError('取消屏蔽时发生错误！');
            return false;
        }
        
    	if ($this->boolean('ajax')) {
            $this->showJsonResult(array('result'=>'successful', 'pid' => $this->op_user->id));
        } else {
            common_redirect(common_path('group/' . $this->cur_group->id . '/blacklist'), 303);
        }
    }
}

