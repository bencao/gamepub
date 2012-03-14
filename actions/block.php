<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Block a user action class.
 *
 * @category Action
 * @package  LShai
 */
class BlockAction extends ShaiAction
{
	var $userBeingBlock;
	
    function prepare($args)
    {
        if (! parent::prepare($args)) {return false;}
        
    	if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        	$this->clientError('只接受POST请求');
        	return false;
        }
        
        $id = $this->trimmed('to');
        if (! $id) {
            $this->clientError('未指定屏蔽的用户');
            return false;
        }
        
   	 	if ($this->cur_user->id == $id) {
        	$this->clientError('不能屏蔽自己');
        	return false;
        }
        
        $this->userBeingBlock = User::staticGet('id', $id);
        
        if (! $this->userBeingBlock) {
            $this->clientError('目标用户不存在。');
            return false;
        }
        
    	if ($this->cur_user->hasBlocked($this->userBeingBlock)) {
            $this->clientError('您已经黑了这个用户。');
            return;
        }
        
        return true;
    }
    
    function handle($args)
    {
        parent::handle($args);
        
        if (! $this->cur_user->block($this->userBeingBlock)) {
            $this->serverError('保存黑名单时出错。');
            return;
        }
        
        if ($this->boolean('ajax')) {
        	$this->showJsonResult(array('pid' => $this->userBeingBlock->id,
        			'action' => common_path('main/unblock')));
        } else {
	        common_redirect(common_path($this->cur_user->uname . '/subscribers'), 303);
        }
    }
}

