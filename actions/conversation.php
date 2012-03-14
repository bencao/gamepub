<?php
/**
 * Shaishai, the distributed microblog
 *
 * Display a conversation in the browser
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
 * Display a conversation in the browser
 *
 * @category Personal
 * @package  Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

class ConversationAction extends OwnerdesignAction
{ 
    var $conversation_id;
    var $conversation_notice;

//	function __construct() {
//		parent::__construct();
//		$this->no_anonymous = false;
//	}
	
    /**
     * Initialization.
     *
     * @param array $args Web and URL arguments
     *
     * @return boolean false if id not passed in
     */

    function prepare($args)
    {
        if (! parent::prepare($args)) {return false;}
        $this->conversation_id = $this->trimmed('id');
        if (empty($this->conversation_id)) {
            return false;
        }
        return true;
    }

    /**
     * Handle the action
     *
     * @param array $args Web and URL arguments
     *
     * @return void
     */

    function handle($args)
    {
        parent::handle($args);

        $this->conversation = Notice::staticGet('id', $this->conversation_id);

        if ($this->is_own) {
        	Reply::setReadByNoticeidAndUserid($this->conversation_id, $this->cur_user->id);
        }
        
        $this->addPassVariable('user', $this->cur_user);
		$this->addPassVariable('conversation', $this->conversation);
    	
		$this->displayWith('ConversationHTMLTemplate');
		  
    }	
}