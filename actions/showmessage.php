<?php
/**
 * Shaishai, the distributed microblog
 *
 * Show a single message
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
 * Show a single message
 *
 * @category Personal
 * @package  Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

//require_once INSTALLDIR . '/lib/mailbox.php';

class ShowmessageAction extends ShaiAction
{
	var $message = null;
	
	/**
     * The current user
     */
    
//    var $cur_user = null;
    
//    var $page = null;

    /**
     * Load attributes based on database arguments
     *
     * Loads all the DB stuff
     *
     * @param array $args $_REQUEST array
     *
     * @return success flag
     */

    function prepare($args)
    {
        if (! parent::prepare($args)) {return false;}
        
//        $this->page = 1;
        
        $id            = $this->trimmed('message');
        $this->message = Message::staticGet('id', $id);

        if (!$this->message) {
            $this->clientError('未找到此消息。', 404);
            return false;
        }

//        $this->cur_user = common_current_user();

        return true;
    }

    function handle($args)
    {
        parent::handle($args);
                        
        if ($this->cur_user && ($this->cur_user->id == $this->message->from_user || 
            $this->cur_user->id == $this->message->to_user)) {        
	    	$showmessageView = TemplateFactory::get('ShowmessageHTMLTemplate');
	        $paras = array('mailbox_user' => $this->cur_user, 
	        			'mailbox_message' => $this->message);
	        $paras = array_merge($paras, $args);
	        $showmessageView->show($paras);
        } else {
        	$this->clientError('只有发送和接受双方可以阅读此消息。', 403);
            return;
        }
    }
    
    function isReadOnly($args)
    {
        return true;
    }
    
}