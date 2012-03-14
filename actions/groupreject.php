<?php


if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Reject user from joining a group
 *
 * This is the action for approve application.
 * for users.
 *
 * @category Group
 * @package  ShaiShai
 */
class GrouprejectAction extends GroupUserAdminAction
{

    function handle($args)
    {
        parent::handle($args);
        
        if (!Group_application::deleteApply($this->cur_group->id, $this->op_user->id)) {
        	$this->serverError('清除用户申请记录失败。');
        	return false;
        }
        
        if (!$this->cur_group->hasMember($this->op_user)) {
	        $content = '您加入' . GROUP_NAME() .  ' ' . $this->cur_group->nickname. ' 的申请被管理员拒绝了。';
	    	// render the message with link to group detail
	    	$rendered = '您加入' . GROUP_NAME() . ' '. common_group_linker($this->cur_group). ' 的申请被管理员拒绝了。';
	    	// We put a group notice into sysmessage
	        $result = System_message::saveNew(array($this->op_user->id), $content, $rendered, 1);
	        if (!$result) {
	            $this->serverError('系统消息通知用户审批结果失败');
	            return false;
	        }
        } else {
        	$this->clientError(sprintf('用户 %s 已经是此' . GROUP_NAME() . '的成员。作为此' . GROUP_NAME() . '管理员，如果您想让他离开，可以在成员列表里屏蔽他。', $this->op_user->nickname));
            return false;
        }
        
        if ($this->boolean('ajax')) {
        	$this->showJsonResult(array('result'=>'successful', 
        		'pid' => $this->op_user->id));
        } else {
			common_redirect(common_path('group/' . $this->cur_group->id . '/application'), 
				303);
        }
    }
}