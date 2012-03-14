<?php
/**
 * Shaishai, the distributed microblog
 *
 * Action for posting new direct messages
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
 * Action for posting new direct messages
 *
 * @category Personal
 * @package  Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

require_once INSTALLDIR . '/lib/messagelist.php';

class NewmessageHTMLTemplate extends PersonalHTMLTemplate
{ 
	/**
     * Error message, if any
     */

    var $msg = null;
    /**
     * Title of the page
     *
     * Note that this usually doesn't get called unless something went wrong
     *
     * @return string page title
     */

    function title()
    {
        return '新消息';
    }
    
    function ajaxShowMessage($args)
    {
    	$this->args = $args;
        $this->startHTML('text/xml;charset=utf-8');
        $this->elementStart('head');
        $this->element('title', null, '发送悄悄话');
        $this->elementEnd('head');
        $this->elementStart('body');

        $nli = new MessageListItem($this->args['message'], $this, 'outbox');
        $nli->show();
		
        $this->elementEnd('body');
        $this->endHTML();
    }
}