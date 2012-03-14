<?php
/**
 * Shaishai, the distributed microblog
 *
 * Class for deleting a outbox message
 *
 * PHP version 5
 *
 * @category  Login
 * @package   Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @author    Ben Cao <benb88@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Class for deleting a outbox message
 *
 * @category Personal
 * @package  Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

class DeleteoutboxAction extends ShaiAction
{
    var $message      = null;
    

    function handle($args)
    {
        parent::handle($args);
        
        $notice_id    = $this->trimmed('nid');
        if($notice_id) {
        	if(!is_numeric($notice_id)) {
	            $this->clientError('您访问的链接错误.', 403);
	            return;
	        }
	        $this->message = Message::staticGet($notice_id);
	        if (!$this->message) {
	            $this->showJsonResult(array('result' => 'false', 'msg' => '没有此站内信'));
	            return;
	        }
	        
	       	$this->message->deleteOutbox();
	        
	        if($this->boolean('ajax'))
	        	$this->showJsonResult(array('result' => 'true', 'deleted' => $this->message->id));
	        else
	            common_redirect(common_path($this->cur_user->uname . '/outbox'));
        }
        
        if ($this->trimmed('delall')) {
			Message::deleteAllOutbox($this->cur_user->id, MESSAGES_PER_PAGE);
				             
	        if($this->boolean('ajax'))
	        	$this->showJsonResult(array('result' => 'true'));
	        else
	            common_redirect(common_path($this->cur_user->uname . '/outbox'));
        }
    }
}