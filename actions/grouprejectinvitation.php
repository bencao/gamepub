<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * User reject to join a group
 *
 * @category Group
 * 
 */

class GrouprejectinvitationAction extends ShaiAction
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
        
        return true;
    }

  
    function handle($args)
    {
        parent::handle($args);
        
    	//If it is a group to be audited and be refused,
        //destroy this group and send system message to other creators
        if ($this->cur_group->validity == 0) {
	        $result = $this->cur_group->destroy();
	    	if (!$result) {
	            $this->serverError('无法撤销' . GROUP_NAME()  . '！');
	            return;
	        }
	        
	        $content = '用户 ' . $this->cur_user->nickname . ' 拒绝了共同创建游戏'. GROUP_NAME() .' ' . $this->cur_group->nickname . '，该' . GROUP_NAME() . '被撤销。';
			$rendered = '用户 ' . common_user_linker($this->cur_user->id) . ' 拒绝了共同创建游戏'. GROUP_NAME() .' ' . $this->cur_group->nickname . '，该' . GROUP_NAME() . '被撤销。';
        	System_message::saveNew($this->cur_group->ownerid, $content, $rendered, 1);
        }
        
        //Update invitation system message
        $invitation = System_message::staticGet('id', $this->trimmed('rejinvitationid'));
        $orig = clone($invitation);
        $invitation->content = '您拒绝了邀请，未加入' . GROUP_NAME() . ' ' . $this->cur_group->nickname;
        $invitation->rendered = '您拒绝了邀请，未加入' . GROUP_NAME() . ' ' . $this->cur_group->nickname;
        $invitation->message_type = 1;
        $result = $invitation->update($orig);
        if (!$result) {
        	common_log_db_error($invitation, 'UPDATE', __FILE__);
	        return false;
        }
        
        //Delete group invitation
        $result = $this->cur_group_inv->delete();
        if (!$result) {
            common_log_db_error($this->cur_group_inv, 'DELETE', __FILE__);
            $this->serverError('无法删除邀请记录。');
            return;
        }

        common_redirect(common_path('main/sysmessage'));
    }
}