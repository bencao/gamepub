<?php


if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Approve a user to join in the group
 *
 * @category Group
 */
class GroupapproveAction extends GroupUserAdminAction
{
    function prepare($args)
    {
        if (! parent::prepare($args)) {return false;}
        
        if(!$this->cur_group->isadvanced && $this->cur_group->memberCount()>99){
        	$this->clientError('对不起，这个' . GROUP_NAME() . '是普通' . GROUP_NAME() . '，它已经达到人数上限，无法再加入新成员。');
            return false;
        }

        return true;
    }

    
    function handle($args)
    {
        parent::handle($args);
        
        if (!Group_application::deleteApply($this->cur_group->id, $this->op_user->id)) {
        	$this->serverError('清除用户申请记录失败！');
        	return false;
        }
    
        if ($this->cur_group->hasBlocked($this->op_user)) {
            $this->clientError(sprintf('用户 %s 已经被这个' . GROUP_NAME() . '的管理员屏蔽。', $this->op_user->nickname));
            return false;
        }
        
        if (!$this->cur_group->hasMember($this->op_user)) {
	        $result = $this->cur_group->addMember($this->op_user);
	        if (!$result) {
	            $this->serverError(sprintf('用户 %s 加入这个' . GROUP_NAME() . '失败！', $this->op_user->nickname));
	            return false;
	        }
        }
        
        // send a system message to the user who is approved
     	$content = '您加入' . GROUP_NAME() . ' '. $this->cur_group->nickname . ' 的申请已经通过了管理员的审核，快去' . GROUP_NAME() . '里看看成员们的最新动态吧！';
    	$rendered = '您加入' . GROUP_NAME() . ' '. common_group_linker($this->cur_group). ' 的申请已经通过了管理员的审核，快去' . GROUP_NAME() . '里看看成员们的最新动态吧！';
    	System_message::saveNew(array($this->op_user->id), $content, $rendered, 1);
		
        if ($this->boolean('ajax')) {
        	$this->showJsonResult(array('result'=>'successful', 
        		'pid' => $this->op_user->id));
        } else {
			common_redirect(common_path('group/' . $this->cur_group->id . '/application'), 
				303);
        }
    }
}