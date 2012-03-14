<?php
/**
 * Shaishai, the distributed microblog
 *
 * Show target user's list of replies
 *
 * PHP version 5
 *
 * @category  Login
 * @package   Shaishai
 * @author    Andray Ma <andray09@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Show target user's list of replies
 *
 * @category Personal
 * @package  Shaishai
 * @author    Andray Ma <andray09@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

require_once INSTALLDIR.'/lib/noticelist.php';

class ShowrepliesHTMLTemplate extends ProfileHTMLTemplate
{
    /**
     * Title of the page
     *
     * Includes name of user and page number.
     *
     * @return string title of page
     */

    function title()
    {
        return sprintf("%s收到的回复", $this->page_owner->nickname);
    }
    
    function showContent()
    {
    	parent::showContent();
    	$this->showNotices();
    }

    /**
     * Show the content
     *
     * A list of notices that are replies to the user, plus pagination.
     *
     * @return void
     */

    function showNotices()
    {
        $nl = new NoticeList($this->args['notice'], $this);
        $cnt = $nl->show();
                
        $this->numpagination($this->args['total'], 'showreplies', array('uname' => $this->page_owner->uname), 
				array(), NOTICES_PER_PAGE);
    }

    function showEmptyList()
    {
        $emptymsg = array();
        $emptymsg['p'] = sprintf('这个是%s收到的回复消息列表, 但是%s还没有收到任何回复消息。', 
                                  $this->page_owner->nickname, $this->page_owner->nickname);
        $emptymsg['p'] = '还不赶快回复他的消息，成为这里的第一条记录。';
        $this->tu->showEmptyListBlock($emptymsg);
    }    
}