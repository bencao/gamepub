<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * User accept to join a group
 *
 * @category Group
 * 
 */
class GroupacceptinvitationAction extends ShaiAction
{
	var $cur_group_inv = null; 
    var $cur_group = null;   
    
    
    function prepare($args)
    {
        if (! parent::prepare($args)) {return false;}

        $id = $this->trimmed('id');
        if (! ($id && $this->cur_group = User_group::staticGet('id', $id))) {
        	$this->clientError('指定的' . GROUP_NAME() . '不存在', 404);
	        return false;
        } 
            
        $pid = $this->trimmed('accepterid');
    	if ($pid != $this->cur_user->id) {
        	$this->clientError('用户不匹配', 403);
            return false;
        }

        $code = $this->trimmed('code');
        if (! ($code && $this->cur_group_inv = Group_invitation::staticGet('code', $code))) {
            $this->clientError('验证码不存在。', 404);
            return false;
        }

        if ($this->cur_group_inv->groupid != $this->cur_group->id) {
        	$this->clientError('' . GROUP_NAME() . '不匹配。', 403);
            return;
        }
        
        if(!$this->cur_group->isadvanced && $this->cur_group->memberCount()>99){
        	$this->clientError('对不起，这个' . GROUP_NAME() . '是普通' . GROUP_NAME() . '，它已经达到人数上限，无法再加入新成员。');
            return false;
        }

        if ($this->cur_group->hasBlocked($this->cur_user)) {
            $this->clientError('您已经被这个' . GROUP_NAME() . '的管理员屏蔽。');
            return false;
        }
        
        return true;
    }
    

    function handle($args)
    {
        parent::handle($args);
        
        //Add member to group
        if (!$this->cur_group->hasMember($this->cur_user)) {
        	$result = $this->cur_group->addMember($this->cur_user, $this->cur_group->validity == 0);
	        if (!$result) {
	            $this->serverError('您加入这个' . GROUP_NAME() . '失败！');
	            return false;
	        }
        }
        
        //If it is a group to be audited and be accepted,
        //send system message to game group owner
        if($this->cur_group->validity == 0 && $this->cur_group->memberCount() >= 2)
        {
        	$result = $this->cur_group->setValidity(1);    
	        if (!$result) {
	            $this->serverError('游戏' . GROUP_NAME() . '创建失败！');
	            return false;
	        }
        	$this->cur_group->blowGameGroupsCache(); 
	        $content = '您建立的游戏' . GROUP_NAME() . ' ' . $this->cur_group->nickname . ' 已被所有共创人通过，' . GROUP_NAME() . '已被激活。';
	        $render = '您建立的游戏' . GROUP_NAME() . ' ' . common_group_linker($this->cur_group) . ' 已被所有共创人通过，' . GROUP_NAME() . '已被激活。';;
	        $result = System_message::saveNew($this->cur_group->ownerid, $content, $render, 1);
        }
                
        //Update invitation system message
        $inv_sys_msg = System_message::staticGet('id', $this->trimmed('accinvitationid'));
        $orig = clone($inv_sys_msg);
        $inv_sys_msg->content = '您接受了邀请，并加入了' . GROUP_NAME() . ' ' . $this->cur_group->nickname;
        $inv_sys_msg->rendered = '您接受了邀请，并加入了' . GROUP_NAME() . ' ' . common_group_linker($this->cur_group);
        $inv_sys_msg->message_type = 1;
        $result = $inv_sys_msg->update($orig);
        if (!$result) {
        	common_log_db_error($inv_sys_msg, 'UPDATE', __FILE__);
	        return false;
        }
            
    	//Delete group invitation
        $result = $this->cur_group_inv->delete();
        if (!$result) {
            common_log_db_error($this->cur_group_inv, 'DELETE', __FILE__);
            $this->serverError('无法删除邀请记录！');
            return;
        }
        
        if($this->cur_group->validity == 0){
			common_redirect(common_path('main/sysmessage'));
        } else{
        	common_redirect(common_path('group/' . $this->cur_group->id));
        }
    }
}