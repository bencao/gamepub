<?php
/**
 * Shaishai, the distributed microblog
 *
 * action handler for system message
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
 * action handler for system message
 *
 * @category Personal
 * @package  Shaishai
 * @author    Andray Ma <andray09@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

class SysmessageHTMLTemplate extends OwnerdesignHTMLTemplate
{
	
    /**
     * Title of the page
     *
     * @return string page title
     */
    function title()
    {
        return "系统通知";
    }
    
	function showCore() {
		$this->elementStart('div', array('id' => 'contents', 'class' => 'clearfix rounded5l tab'));
		$this->showContent();
		$this->elementEnd('div');
		
    	$this->elementStart('div', array('id' => 'widgets'));
		$this->showRightside();
		$this->elementEnd('div');
    }
    
    function showRightsidebar() {
    	$page_owner_profile = $this->cur_user->getProfile();
    	$this->tu->showOwnerInfoWidget($page_owner_profile);
    	$this->tu->showSubInfoWidget($page_owner_profile);
    	$this->tu->showToolbarWidget($page_owner_profile);
    	
    	$this->tu->showTagNavigationWidget($this->cur_user, 'home', null);
    	
//    	$navs = new NavList_PersonalMessages($page_owner_profile);
//    	$this->tu->showNavigationWidget($navs->lists(), $this->trimmed('action'));
    	
    	$this->tu->showGroupsWidget($page_owner_profile, 6);    	
    	$subscriptions = $page_owner_profile->getSubscriptions(0, 18);
		$this->tu->showUserListWidget($subscriptions, '我关注的人');	
		
		$this->tu->showInviteWidget();
    	
    	$this->tu->showTagcloudWidget();
			
//		$this->tu->showTagcloudPanelBlock();
    }
    
    function showContent() {
    	$this->tu->showTitleBlock('系统通知', 'messages');
    	$nav = new NavList_PersonalMessages($this->cur_user->getProfile());
		$this->tu->showTabNav($nav->lists(), $this->trimmed('action'));
		
        $this->showContentInfo();        
    }
    
    // show system messages
    function showContentInfo()
    {
    	$nl = new SysMesList($this->args['sysmes'], $this);
       	$cnt = $nl->show();
            
//        $this->numpagination($this->cur_page > 1, $cnt > MESSAGES_PER_PAGE,
//                              $this->cur_page, $this->trimmed('action'),
//                              null, $this->args['total'], '');
                              
        $this->numpagination($this->args['total'], $this->trimmed('action'), array(), 
				array(), MESSAGES_PER_PAGE);
    }
    
    /**
     * Instructions for using this page
     *
     * @return string localised instructions for using the page
     */

    function showEmptyList()
    {
        $emptymsg = array();
        $emptymsg['p'] = '您系统消息箱还没有消息。';
        $emptymsg['p'] = '系统消息是系统为您自动更新的一些重要通知，如平台通知、' . GROUP_NAME() . '相关信息等等';
        $this->tu->showEmptyListBlock($emptymsg);
    }
    
	function showScripts() {
    	parent::showScripts();
    	$this->script('js/lshai_tagcloud_min.js');
    }
}

class SysMesList extends NoticeList
{
    /**
     * Show the tree of notices
     *
     * @return void
     */

    function show()
    {
    	$this->out->elementStart('ol', array('id' => 'notices', 'class' => 'noavatar'));
        $cnt = 0;

        while ($this->notice != null 
        	&& $this->notice->fetch() && $cnt <= MESSAGES_PER_PAGE) {
            $cnt++;
           
            if ($cnt > MESSAGES_PER_PAGE) {
                break;
            }
                        
            $item = $this->newListItem($this->notice);
            $item->show();
        }
        
        if (0 == $cnt) {
            $this->out->showEmptyList();
        }
        
        $this->out->elementEnd('ol');
    }
    
    function newListItem($notice)
    {
        return new SysMesItem($notice, $this->out);
    }
}

class SysMesItem extends NoticeListItem
{
	var $notice = null;
    
    var $out = null;
    
    function __construct($notice, $out=null)
    {
        $this->notice  = $notice;
        $this->out = $out;
    }
    
	function showStart()
    {
        $this->out->elementStart('li', array('class' => 'notice', 'id' => 'sysmessage-' . $this->notice->id));
    }
    
    function showEnd() {
    	$this->out->elementEnd('li');
    }
    
	function showNoticeBar()
    {
    	$this->out->elementStart('div', 'bar clearfix');
    	$this->out->elementStart('div', 'info');
    	$dt = common_date_iso8601($this->notice->created);
        $this->out->element('span', array('class' => 'time timestamp',
                                          'title' => $dt, 'time' => strtotime($this->notice->created)),
                            common_date_string($this->notice->created));
//		$this->showNoticeSource();   
		$this->out->elementEnd('div');  	
		
//    	$this->showNoticeOptions();
    	$this->out->elementEnd('div');
    }
								
    function show() {
    	$this->showStart();
        
        $this->out->elementStart('h3'); 
        $this->out->text($this->getMessageType($this->notice->message_type));
        $this->out->elementEnd('h3');
        
        $this->showNoticeInfo();
        $this->showRoot();
        $this->showNoticeBar();
		$this->showContext();
		
        $this->showEnd();
    }
    
    // Get the message type by the bype number
    function getMessageType($message_type)
    {
        switch ($message_type) {
        case 0: return '广播消息: ';
        case 1: return '' . GROUP_NAME() . '系统消息: ';
        case 2: return '营销消息: ';
        case 4: return '' . GROUP_NAME() . '邀请: ';
        case 5: return '外连消息';
        default:
            return '未分类';
        }
    }
}

