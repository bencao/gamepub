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

class CheckfavoritesHTMLTemplate extends ProfileHTMLTemplate
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
        return sprintf("%s收藏的消息", $this->page_owner->nickname);
    }
    
    function showRightSection($page_owner_profile) {
        $navs = new NavList_Visitor($page_owner_profile, $this->is_own);
    	$this->tu->showNavigationWidget($navs->lists(), $this->trimmed('action'));
    }

    function showContent()
    {
    	parent::showContent();
    	$this->showNotices();
    }
    
    function showEmptyList()
    {
        if (common_current_user()) {
            $message = sprintf('%s还没有收藏任何消息, 添加有趣的消息供他收藏呀 :)', $this->page_owner->nickname);
        }
        else {
            $message = sprintf('%s还没有收藏消息,为什么不<a href="' . common_path('register') . '">注册账号</a>,贴出一些有趣的消息供大家收藏 :)',
            		 $this->page_owner->nickname);
        }

        $this->tu->showEmptyListBlock($message);
    }

    function showNotices() {
    	if (!$this->page_owner->getProfile()->sharefavorites) {
    		$this->element('div', 'split');
    		$this->tu->showEmptyListBlock($this->page_owner->nickname. '设置了不对外分享收藏，您无法查看。');
    	}else {
	        $nl = new NoticeList($this->args['notice'], $this);
	        $cnt = $nl->show();
	        
	        $this->numpagination($this->args['total'], 'checkfavorites', array('uname' => $this->page_owner->uname)); 	      
    	}
    }
}