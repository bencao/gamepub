<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Update register code for join a group
 *
 * @category Group
 * @package  ShaiShai
 */

class GroupupdatecodeAction extends GroupAdminAction
{

    function handle($args)
    {
        parent::handle($args);
        
        $ivcode = Group_ivcode::staticGet('groupid', $this->cur_group->id);
        if (strtotime($ivcode->modified)>strtotime("-1 day")) {
        	$this->showForm('邀请码的更新太过频繁，一天只能更新一次。');
            return;
        }
        $orig = clone($ivcode);
        $ivcode->code = common_confirmation_code(64);
        
        $result = $ivcode->update($orig);
        if (!$result) {
        	common_log_db_error($ivcode, 'UPDATE', __FILE__);
        	$this->serverError('入' . GROUP_NAME() . '码更新失败');
        }
        $this->showForm('邀请码更新成功，请拷贝新生成的邀请链接。', true);
    }
    
    function showForm($msg=null, $success=null)
    {
        common_ensure_session();
        $_SESSION['upcodemsg'] = $msg;
        if ($success){
            $_SESSION['updatecodesuc'] = $success;
        }
	    common_redirect(common_path('group/' . $this->cur_group->id . '/invitation'));
    }
}