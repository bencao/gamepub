<?php
/**
 * Shaishai, the distributed microblog
 *
 * Base class for deleting things
 *
 * PHP version 5
 *
 * @category  Login
 * @package   Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Base class for deleting things
 *
 * @category Personal
 * @package  Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

class DeleteAction extends ShaiAction
{ 
	var $user         = null;
    var $notice       = null;
    var $profile      = null;
    var $user_profile = null;
    
    function prepare($args)
    {
        if (! parent::prepare($args)) {return false;}
        
    	if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        	$this->clientError('消息删除只支持POST方法.');
        	return false;
        }

        $this->user   = common_current_user();
        $notice_id    = $this->trimmed('nid');
        if($notice_id && !is_numeric($notice_id)) {
            $this->clientError('您访问的链接错误.', 403);
            return false;
        }
        $this->notice = Notice::staticGet($notice_id);

        if (! $this->notice) {
            $this->clientError('该消息已被删除。', 403);
            return false;
        }

        $this->profile      = $this->notice->getProfile();
        $this->user_profile = $this->user->getProfile();

        return true;
    }

    function handle($args)
    {
        parent::handle($args);
        
        if ($this->notice->user_id != $this->user_profile->id) {
        	$this->clientError('您不能删除此消息。', 403);
            exit;
        }
    }
}