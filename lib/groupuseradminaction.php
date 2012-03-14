<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Base class for administrating group user actions
 *
 * @category Action
 *
 */
class GroupUserAdminAction extends ShaiAction 
{
    var $cur_group = null;
    var $op_user = null;
    
    function isReadOnly($args)
    {
        return false;
    }
    
    function prepare($args) {
    	if (! parent::prepare($args)) {return false;}
    	
    	if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        	$this->clientError('只接受POST请求');
        	return false;
        }
    	
        $id = $this->trimmed('id');
        if (! ($id && $this->cur_group = User_group::staticGet('id', $id))) {
        	$this->clientError('指定的' . GROUP_NAME() . '不存在', 404);
	        return false;
        } 
        
        if(!$this->cur_group->validity){        	
            $this->clientError('指定的' . GROUP_NAME() . '处于无效状态', 404);
            return false;
        }
            
        $pid = $this->trimmed('profileid');
        if (! ($pid && $this->op_user = User::staticGet('id', $pid))) {
        	$this->clientError('该用户不存在。', 404);
        	return false;
        } 
        
        $is_group_admin = $this->cur_user && $this->cur_group->hasAdmin($this->cur_user);
        if (! $is_group_admin) {
            $this->clientError('只有该' . GROUP_NAME(). '管理员才有权限进行此操作！', 403);
            return false;
        }
    	
    	return true;
    }
     
    function handle($args)
    {
        parent::handle($args);
        $this->addPassVariable('cur_group', $this->cur_group);
    }
}
