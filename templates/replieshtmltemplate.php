<?php
/**
 * Shaishai, the distributed microblog
 *
 * List of replies
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
 * List of replies
 *
 * @category Personal
 * @package  Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

require_once INSTALLDIR.'/lib/noticelist.php';

class RepliesHTMLTemplate extends PersonalHTMLTemplate
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
        return "我收到的回复";
    }
    
    function showRightSection($templateutil, $page_owner_profile) {
    	$this->tu->showTagNavigationWidget($this->cur_user, 'home', null);
    }

    function showContentLeft() {
    		
    }
     
     function showNoticeForm() {
     	
     }
     
    /**
     * Show the content
     *
     * A list of notices that are replies to the user, plus pagination.
     *
     * @return void
     */

    function showContentInfo()
    {
    	$this->tu->showTitleBlock('我收到的回复', 'conversation');
    	
    	$this->element('div', array('class' =>'split', 'style' => 'margin:0 0 -1px 0;'));
    	
//        $nl = new NoticeList($this->args['notice'], $this);
//        $cnt = $nl->show();
		$cnt = $this->showNoticeList($this->args['notice'], $this);
		
//        $this->numpagination($this->cur_page > 1, $cnt > NOTICES_PER_PAGE,
//                          $this->cur_page, 'replies',
//                          array('uname' => $this->cur_user->uname), $this->args['total'], "");
                          
        $this->numpagination($this->args['total'], 'replies', array('uname' => $this->cur_user->uname), 
				array(), NOTICES_PER_PAGE);
    }
    
	function showSecondaryPanel($page_owner_profile) {
    	$this->tu->showSecondaryPanelNavigationBlock($page_owner_profile, 'reply');
    }

    function showEmptyList()
    {

        $message = '还没有人回复您的消息.';

        $emptymsg = array();
        $emptymsg['p'] = $message;
        $emptymsg['p'] = '完善个人资料，经常更新高质量的消息，邀请更多好友将提升您在平台的人气，回复自然络绎不绝。';
        $this->tu->showEmptyListBlock($emptymsg);
    }
}