<?php


if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Block a user from a group
 * @category Group
 * @package  ShaiShai
 */
class GroupblockAction extends GroupUserAdminAction
{
    function prepare($args)
    {
        if (! parent::prepare($args)) {return false;}
        
        if(!$this->cur_group->isOwnedBy($this->cur_user) && $this->cur_group->hasAdmin($this->op_user)){
        	$this->clientError('您不是该' . GROUP_NAME() . '的创建者，没有权限屏蔽管理员。', 403);
            return false;
        }
        
        if ($this->cur_group->hasBlocked($this->op_user)) {
            $this->clientError('此用户已经被屏蔽。');
            return false;
        }
        
        return true;
    }


    function handle($args)
    {
        parent::handle($args);
        
        $result = Group_block::blockUser($this->cur_group, $this->op_user, $this->cur_user);

        if (!$result) {
            $this->serverError("屏蔽用户时发生错误！");
            return false;
        }
        
        if ($this->boolean('ajax')) {
            $this->showJsonResult(array('result'=>'successful', 'pid' => $this->op_user->id));
        } else {
            common_redirect(common_path('group/' . $this->cur_group->id . '/members'), 303);
        }
    }
}

