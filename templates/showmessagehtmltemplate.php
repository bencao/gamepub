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

require_once 'mailboxhtmltemplate.php';

class ShowmessageHTMLTemplate extends MailboxHTMLTemplate
{
   function title()
    {                      
        if ($this->args['mailbox_user']->id == $this->args['mailbox_message']->from_user) {
            $to = $this->args['mailbox_message']->getTo();
            return "发送给" . $to->uname . "的站内信";
        } else if ($this->args['mailbox_user']->id == $this->args['mailbox_message']->to_user) {
            $from = $this->args['mailbox_message']->getFrom();
            return "来自" . $from->uname . "的站内信";                   
        }
    }
    
    function getMessageProfile()
    {
        if ($this->args['mailbox_user']->id == $this->args['mailbox_message']->from_user) {
            return $this->args['mailbox_message']->getTo();
        } else if ($this->args['mailbox_user']->id == $this->args['mailbox_message']->to_user) {
            return $this->args['mailbox_message']->getFrom();
        } else {
            // This shouldn't happen
            return null;
        }
    }
    
    /**
     * Don't show local navigation
     *
     * @return void
     */

    function showLocalNavBlock()
    {
    }
    
    /**
     * Don't show page notice
     *
     * @return void
     */

    function showPageNoticeBlock()
    {
    }

    /**
     * Don't show aside
     *
     * @return void
     */

    function showAside() 
    {
    }
    
	function getMessages() 
    {    
        $message     = new Message();
        $message->id = $this->args['mailbox_message']->id;
        $message->find();
        return $message;
    }
    
	/**
     * Don't show any instructions
     *
     * @return string
     */
     
    function getInstructions()
    {
        return '';
    }
}