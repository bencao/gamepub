<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Leave a group
 *
 * This is the action for leaving a group. It works more or less like the subscribe action
 * If user is the owner of group, this operation results in group delete
 * for users.
 *
 * @category Group
 * @package  ShaiShai
 */

class GroupleaveAction extends GroupDesignAction
{
    var $tourl = null;

    function prepare($args)
    {
        if (! parent::prepare($args)) {return false;}

        if (!$this->cur_group->hasMember($this->cur_user)) {
            $this->clientError('您不是这个' . GROUP_NAME() . '的成员。', 403);
            return false;
        }
        
        if ($this->cur_group->isOwnedBy($this->cur_user) && $this->cur_group->memberCount()>1) {
        	$this->clientError('' . GROUP_NAME() . '内还有成员，不能退出' . GROUP_NAME() . '；请先删除(屏蔽)所有其它成员！', 403);
        	return false;
        }

        return true;
    }

    function handle($args)
    {
        parent::handle($args);

        $member = new Group_member();

        $member->group_id   = $this->cur_group->id;
        $member->user_id = $this->cur_user->id;

        if (!$member->find(true)) {
            $this->serverError('找不到成员的记录。');
            return;
        }
        
        if ($this->cur_group->isOwnedBy($this->cur_user) && $this->cur_group->memberCount()==1) {
        	$result = $this->cur_group->destroy();
        	$tourl = common_path('groups');
        }else {
            $result = $member->delete();
        	$tourl = common_path('group/' . $this->cur_group->id);
        }

        if (!$result) {
            common_log_db_error($member, 'DELETE', __FILE__);
            $this->serverError('用户不能离开' . GROUP_NAME());
        }
        if ($this->boolean('ajax')) {
        	$this->showJsonResult(array('qualified'=>true, 'tourl' => $tourl));
		} else {
			common_redirect($tourl, 303);
		}
    }
}
