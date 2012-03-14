<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Invite people to a group
 *
 * This is the form for inviting people to group
 *
 * @category Group
 */

class GroupinviteAction extends GroupUserAdminAction
{
	
    function handle($args)
    {
        parent::handle($args);
        
        $this->inviteHandle();
    }
    
    //Subclass to override
    function inviteHandle()
    {
    	$this->trySendInvite($this->op_user->id);
        
        if ($this->boolean('ajax')) {
            $this->showJsonResult(array('result'=>'successful', 'pid'=>$this->op_user->id));
        }else {
            common_redirect(common_path('group/' . $this->cur_group->id . '/invitation'), 
            				303);
        }
    }
    
    function trySendInvite($profile_id)
    {
    	if (Group_invitation::isInvitSend($this->cur_group->id, $profile_id)) {
            return false;
        }
    	
    	$inv_code = Group_invitation::saveNew($this->cur_group->id, $profile_id);
        if (!$inv_code) {
            $this->serverError('邀请用户加入' . GROUP_NAME() . '失败！');
            return false;
        }
		        
        // send a system message to the user who is invited
        $sysmsg_id =  System_message::create_guid();
        $contentFormat = '' . GROUP_NAME() . ' %s 邀请您加入，您可以点击' . GROUP_NAME() . '名字查看该' . GROUP_NAME() . '详情，然后选择 %s 或者 %s';
        $content = sprintf($contentFormat, 
        					$this->cur_group->nickname, 
        					'接受', 
        					'拒绝');
        $rendered = sprintf($contentFormat, 
        					common_group_linker($this->cur_group), 
        					GroupinviteAction::groupinv_acc_link($inv_code, $sysmsg_id, $this->cur_group->id, $profile_id),
        					GroupinviteAction::groupinv_rej_link($inv_code, $sysmsg_id, $this->cur_group->id, $profile_id));
                         
        System_message::saveNew($profile_id, $content, $rendered, 4, $sysmsg_id);
    }
    
	static function groupinv_acc_link($inv_code, $sysmsg_id, $group_id, $profile_id)
	{
		$getLink = htmlspecialchars(common_local_url('groupacceptinvitation', 
	                       array('id' => $group_id), 
	                       array('accepterid' => $profile_id, 'code' => $inv_code, 'accinvitationid' => $sysmsg_id)));
	    return '<a href="'. $getLink. '"><span>接受</span></a>';
	}
	
	static function groupinv_rej_link($inv_code, $sysmsg_id, $group_id, $profile_id)
	{
		$getLink = htmlspecialchars(common_local_url('grouprejectinvitation', 
	                       array('id' => $group_id), 
	                       array('accepterid' => $profile_id, 'code'=> $inv_code, 'rejinvitationid' => $sysmsg_id)));
	    return '<a href="'. $getLink. '"><span>拒绝</span></a>';
	}
}

