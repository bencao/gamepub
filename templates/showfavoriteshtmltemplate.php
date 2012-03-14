<?php
/**
 * Shaishai, the distributed microblog
 *
 * List of favorites
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
 * List of favorites
 *
 * @category Personal
 * @package  Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

//require_once INSTALLDIR.'/lib/personalnoticesnav.php';
require_once INSTALLDIR.'/lib/noticelist.php';

require_once INSTALLDIR.'/lib/renamefavorgroupform.php';
require_once INSTALLDIR.'/lib/deletefavorgroupform.php';


class ShowFavoritesHTMLTemplate extends PersonalHTMLTemplate
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
        return "我收藏的消息";
    }
    
    function showRightSection($templateutil, $page_owner_profile) {
    	$this->tu->showTagNavigationWidget($this->cur_user, 'home', null);
    }
    
    function showScripts() {
    	parent::showScripts();
    	$this->script('js/lshai_favorgroup.js');
    }

    function showEmptyList()
    {
        if (common_current_user()) {
            $current_user = common_current_user();
            if ($this->args['user']->id === $current_user->id) {
                $message = '此收藏夹没有收藏的瞬间.';
            } else {
                $message = sprintf('%s 还没有收藏消息  :)', $this->args['user']->nickname);
            }
        }
        else {
            $message = $this->args['user']->nickname . '还没有收藏消息,为什么不<a href="' . common_local_url('register') . '">注册账号</a>,贴出一些有趣的消息供大家收藏 :)';
        }

        $this->tu->showEmptyListBlock($message);
    }

    function showContentLeft() {
    		
    }
     
     function showNoticeForm() {
     	
     }
     
    /**
     * Show the content
     *
     * A list of notices that this user has marked as a favorite
     *
     * @return void
     */

    function showContentInfo()
    {
        $favegroups = $this->args['fave_group'];

        $this->tu->showTitleBlock('收藏', 'favorites');
        
        if (empty($favegroups)) {
            $this->elementStart('div', 'instruction guide');
            $message = "没有收藏夹.";
	        $this->raw(common_markup_to_html($message));
	        $this->elementEnd('div');
           	return;
        }
       
        $this->elementStart('div', array('id' => 'fav_nav'));//'id' => 'favor_group'));
        
        $favegroups = new ArrayWrapper($favegroups);
       	$first = true;
        $firstfavegroupId = null;
        
        $this->elementStart('div', 'scroll clearfix');
        $this->elementStart('table', array('cellspacing' => '0', 'cellpadding' => '0'));
        $this->elementStart('tbody');
        $this->elementStart('tr');
        
        while ($favegroups->fetch()) {
        	if($first) {
				$this->elementStart('td', 'active');
				$this->elementStart('div', 'td_wrap');
	            $this->element('a', array("href" => common_local_url('showfavorgroup', array('id' => $favegroups->id)), 
	            			'alt' => $favegroups->name, 'class' => 'fg_name'), $favegroups->name);
        		$this->elementStart('div', 'op');
        		
            	$first = false;
            	$firstfavegroupId = $favegroups->id;
        	} else {
        		$this->elementStart('td');
        		$this->elementStart('div', 'td_wrap');
	            $this->element('a', array('href' => common_local_url('showfavorgroup', array('id' => $favegroups->id)), 
	            			'title' => $favegroups->name, 'class' => 'fg_name'), $favegroups->name);
        		$this->elementStart('div', array('class' => 'op', 'style' => 'display:none;'));
        	}
        	$this->element('a', array('href' => '#', 'class' => 'rename'), '重命名');
        	$this->text('|');
        	$this->element('a', array('href' => common_local_url('deletefavorgroup', array('id' => $favegroups->id)), 
        				'class' => 'delete', 'title' => '删除此收藏夹，将所有内容转移至"我的收藏"'), '删除');
        	$this->elementEnd('div');
        	
        	$this->elementStart('div', array('class' => 'editing', 'style' => 'display:none;'));
        	$this->element('input', array('class' => 'text', 'type' => 'text'));
        	$this->elementStart('div', array('class' => 'op'));
        	$this->element('a', array('href' => common_local_url('renamefavorgroup', array('id' => $favegroups->id)), 
        				'class' => 'confirm'), '确定');
        	$this->text('|');
        	$this->element('a', array("href" => "#", 'class' => 'cancel'), '取消');
        	$this->elementEnd('div');
        	$this->elementEnd('div');
        	
            $this->elementEnd('div');
            $this->elementEnd('td');
        }
        
        $this->elementEnd('tr');
        $this->elementEnd('tbody');
        $this->elementEnd('table');
        $this->elementEnd('div');
        
        $this->elementStart('a', array('class' => 'raw', 'alt' => '前向翻滚', 'href' => '#'));
//        $this->elementStart('small');
//        $this->raw('◀');
//        $this->elementEnd('small');
        $this->elementEnd('a');
        $this->elementStart('a', array('class' => 'forward', 'alt' => '前后翻滚', 'href' => '#'));
//        $this->elementStart('small');
//        $this->raw('▶');
//        $this->elementEnd('small');
        $this->elementEnd('a');
        
        $this->elementEnd('div');
            
		//第一个收藏夹的内容
        $notice = Fave::getFaveGroupById($firstfavegroupId, 
				($this->cur_page-1)*NOTICES_PER_PAGE, NOTICES_PER_PAGE + 1);

        if (empty($notice)) {
            $this->serverError('无法获得收藏的消息.');
            return;
        }

//        $nl = new NoticeList($notice, $this);
//        $cnt = $nl->show();
		 $cnt = $this->showNoticeList($notice, $this);
		 
        //改写 showfavorgroup
        $this->pagination($this->cur_page > 1, $cnt > NOTICES_PER_PAGE,
                          $this->cur_page, 'showfavorgroup',
                          array('id' => $firstfavegroupId));
    }

    function showPageNotice() {
        $this->element('p', 'instructions', '一种您喜欢的共享方式');
    }
    
}