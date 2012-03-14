<?php
/**
 * ShaiShai, the distributed microblogging tool
 *
 * Apply joining a group
 *
 * PHP version 5
 * @category  Group
 * @package   ShaiShai
 * @author    Andray
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Apply joining a group
 *
 * This is the action for applying to join a group.
 * for users.
 *
 * @category Group
 * @package  ShaiShai
 */

class GroupapplyjoinAction extends GroupdesignAction
{
    var $message = null;


    function prepare($args)
    {
        if (! parent::prepare($args)) {return false;}
        
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        	$this->clientError('只接受POST请求');
        	return false;
        }
        
        if ($this->cur_group->hasApplicationFor($this->cur_user)) {
        	$this->clientError("您已经发送过申请，管理员正在申核，请不要重复提交。");
            return false;
        }
        
        if (! $this->cur_group->isadvanced 
        	&& $this->cur_group->memberCount() > 99) {
        	$this->clientError('对不起，这个' . GROUP_NAME() . '是普通' . GROUP_NAME() . '，它已经达到人数上限，无法进入。', 403);
            return false;
        }

        if ($this->cur_group->hasMember($this->cur_user)) {
            $this->clientError('您已经是此' . GROUP_NAME() . '的成员。', 403);
            return false;
        }

        if ($this->cur_group->hasBlocked($this->cur_user)) {
            $this->clientError('您已经被这个' . GROUP_NAME() . '的管理员屏蔽。', 403);
            return false;
        }
        
        if ($this->cur_group->closed) {
            $this->clientError('此' . GROUP_NAME() . '入口已被管理员关闭。', 403);
            return false;
        }
        
        $this->message = $this->trimmed('message');

        return true;
    }


    function handle($args)
    {
        parent::handle($args);

        $groupOwner = User::staticGet('id', $this->cur_group->ownerid);
        
        // If the current user hasn't subscribe the groupowner, subscribe him
        if (! $this->cur_user->isSubscribed($groupOwner)) {
        	if ($groupOwner->hasBlocked($this->cur_user)) {
        		$this->clientError('对不起，您被该' . GROUP_NAME() . '主列入黑名单，不能加入此' . GROUP_NAME() . '。', 403);
		            return;
        	} else {
		        $result = Subscription::subscribeTo($this->cur_user, $groupOwner);
		        if($result != true) {
		            $this->clientError('对不起，您未能成功关注' . GROUP_NAME() . '主，加入' . GROUP_NAME() . '失败。', 403);
		            return;
		        }
	        }
        }

        if (! $this->cur_group->saveNewApplication($this->cur_user, $this->message)) {
            $this->serverError('因技术原因，无法加入' . GROUP_NAME() . '， 请稍候再试。');
        }
        
        $this->showJsonResult(array('result'=>'successful', 'groupid' => $this->cur_group->id));
    }
}