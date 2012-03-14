<?php
/**
 * Shaishai, the distributed microblog
 *
 * Check the other's favorites
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
 * Check the other's favorites
 *
 * @category Personal
 * @package  Shaishai
 * @author    Andray Ma <andray09@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

require_once INSTALLDIR.'/lib/noticelist.php';

class ShowretweetHTMLTemplate extends ProfileHTMLTemplate
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
        if ($this->cur_page == 1) {
            return "您转载的消息";
        } else {
            return sprintf("您转载的消息, 第%d页", $this->cur_page);
        }
    }

    function showContent()
    {
    	$this->tu->showUserSummaryBlock($this->args['profile'], $this->cur_user, $this->owner);
    	
    	$pnl = new StreamNoticeList($this->args['notice'], $this);
        $cnt = $pnl->show();
	        
		$this->numpagination($this->args['total'], 'showretweet', array('uname' => $this->owner->uname));
    }
    
    function showEmptyList()
    {
        $message = '您还没有转载任何消息, 您可以转载感兴趣的消息呀 :)';
        $this->tu->showEmptyListBlock($message);
    }
}

class StreamNoticeList extends NoticeList {
	
	function show()
    {
    	$this->out->elementStart('ol', array('id' => 'notices', 'class' => 'noavatar nonickname'));
        $cnt = 0;

        while ($this->notice != null 
        	&& $this->notice->fetch() && $cnt <= NOTICES_PER_PAGE) {
            $cnt++;
           
            if ($cnt > NOTICES_PER_PAGE) {
                break;
            }
                        
            $item = $this->newListItem($this->notice);
            $item->show();
        }
        
        if (0 == $cnt) {
            $this->out->showEmptyList();
        }
        
        $this->out->elementEnd('ol');
        
        return $cnt;
    }
    
	function newListItem($notice)
    {
        return new StreamNoticeListItem($notice, $this->out);
    }
}

class StreamNoticeListItem extends NoticeListItem {
	
	function showStart()
    {
        // XXX: RDFa
        // TODO: add notice_type class e.g., notice_video, notice_image
        $liClass = 'notice';
        
    	if (!empty($this->notice->conversation)
            && $this->notice->conversation != $this->notice->id) {
            $this->out->elementStart('li', array('class' => $liClass, 'id' => 'notice-' . $this->notice->id, 'style' => 'padding-top:21px;'));	
        } else {
        	$this->out->elementStart('li', array('class' => $liClass, 'id' => 'notice-' . $this->notice->id));
        }
    }
    
	function show()
    {
        $this->showStart();
        
        $this->showNoticeInfo();
        $this->showRoot();
        $this->showNoticeBar();
		$this->showContext();
		
        $this->showEnd();
    }
}