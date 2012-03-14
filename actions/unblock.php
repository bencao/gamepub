<?php
/**
 * Unblock a user action class.
 *
 * PHP version 5
 *
 * @category Action
 * @package  LShai
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Unblock a user action class.
 *
 * @category Action
 * @package  LShai
 */
class UnblockAction extends ShaiAction
{
    var $userBeingUnblock = null;

    /**
     * Take arguments for running
     *
     * @param array $args $_REQUEST args
     *
     * @return boolean success flag
     */
    function prepare($args)
    {
        if (! parent::prepare($args)) {return false;}

        $id = $this->trimmed('to');
        if (!$id) {
            $this->clientError('未指定目标用户。');
            return false;
        }
        
   	 	if ($this->cur_user->id == $id) {
        	$this->clientError('不能取消屏蔽自己');
        	return;
        }
        
        $this->userBeingUnblock = User::staticGet('id', $id);
        if (! $this->userBeingUnblock) {
            $this->clientError('目标用户不存在。');
            return false;
        }
        return true;
    }

    /**
     * Handle request
     *
     * Shows a page with list of favorite notices
     *
     * @param array $args $_REQUEST args; handled in prepare()
     *
     * @return void
     */
    function handle($args)
    {
        parent::handle($args);
        
        $result = $this->cur_user->unblock($this->userBeingUnblock);
        if (!$result) {
            $this->serverError('取消黑名单时出错。');
            return;
        }
        
    	if ($this->boolean('ajax')) {
    		$this->showJsonResult(array('pid' => $this->userBeingUnblock->id, 
        			'action' => common_path('main/block')));
        } else {
	        common_redirect(common_path($this->cur_user->uname . '/subscribers'),
	                            303);
        }
    }
}

