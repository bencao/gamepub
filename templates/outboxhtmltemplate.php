<?php
/**
 * Shaishai, the distributed microblog
 *
 * action handler for message outbox
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
 * action handler for message outbox
 *
 * @category Personal
 * @package  Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

//require_once 'mailboxhtmltemplate.php';

class OutboxHTMLTemplate extends MailboxHTMLTemplate
{
    /**
     * Title of the page
     *
     * @return string page title
     */

    function title()
    {
        return "发件箱";
    }

    /**
     * instructions for using this page
     *
     * @return string localised instructions for using the page
     */

    function getInstructions()
    {
        return '发件箱';
    }
    
    function showEmptyList()
    {
    	$emptymsg = array();
        $emptymsg['p'] = '您的发件箱还没有私有消息。';
        $emptymsg['p'] = '站内信是您私有的信息，其它用户不可见。可以用它来和您的好友说悄悄话哦！';
        $this->tu->showEmptyListBlock($emptymsg);
    }
}