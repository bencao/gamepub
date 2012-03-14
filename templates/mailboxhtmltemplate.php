<?php
/**
 * Shaishai, the distributed microblog
 *
 * common superclass for direct messages inbox and outbox
 *
 * PHP version 5
 *
 * @category  Login
 * @package   Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @author    Ben Cao <benb88@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * common superclass for direct messages inbox and outbox
 *
 * @category Personal
 * @package  Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

require_once INSTALLDIR.'/lib/messagelist.php';
require_once INSTALLDIR.'/lib/shaimessageform.php';

class MailboxHTMLTemplate extends OwnerdesignHTMLTemplate
{
	var $message = null;
	var $mailbox = null;
	var $owner_profile = null;
	
    function showContentLeft() {
    		
    }
    
	function showCore() {
		$this->elementStart('div', array('id' => 'contents', 'class' => 'clearfix rounded5l tab'));
		$this->showContent();
		$this->elementEnd('div');
		
    	$this->elementStart('div', array('id' => 'widgets'));
		$this->showRightside();
		$this->elementEnd('div');
    }
    
    function showScripts() {
    	parent::showScripts();
    	//$this->script('js/lshai_message.js');
    	$this->script('js/lshai_messagesend.js');
    	$this->script('js/lshai_tagcloud_min.js');
    }
    
    function showContent() {
    	$this->mailbox = $this->getInstructions();
    	$this->tu->showTitleBlock('我的' . $this->mailbox, 'messages');
    	$nav = new NavList_PersonalMessages($this->owner_profile);
		$this->tu->showTabNav($nav->lists(), $this->trimmed('action'));
		
        $this->message = $this->args['message'];
        $this->elementStart('div', 'mbox_send');
	    $this->elementStart('a', array('href' => '#','id' => 'sendmessage', 'class' => 'silver76 button76'));//common_local_url('newmessage')
	    $this->text("发私信");
	    $this->elementEnd('a');
	    $this->elementEnd('div');
	        
//        if($this->message && $this->message->N >= 1) {
        	$this->elementStart('div', array('id' => 'sub_op')); //
        	$this->elementStart('ul', array('class' => 'op clearfix', 'style' => 'left:0;'));
        	$this->elementStart('li');
	        if ($this->mailbox == '发件箱') {
		        $this->elementStart('a', array('href' => common_local_url('deleteoutbox'), 'class' => 'delete', 'type' => 'outbox')); 
	        } else {
	        	$this->elementStart('a', array('href' => common_local_url('deleteinbox'), 'class' => 'delete', 'type' => 'inbox'));
	        }
	        $this->text('清空所有');
	        $this->elementEnd('a');
	        $this->elementEnd('li');
	        $this->elementEnd('ul');
	        $this->elementEnd('div');
//        }
        
        $nl = new MessageList($this->message, $this, $this->trimmed('action'));
        $cnt = $nl->show();
        
//        $this->numpagination($this->cur_page > 1, $cnt > MESSAGES_PER_PAGE,
//                    $this->cur_page, $this->trimmed('action'),
//                    array('uname' => $this->owner_profile->uname), $this->args['total'], '');
                    
        $this->numpagination($this->args['total'], $this->trimmed('action'), array('uname' => $this->owner_profile->uname), 
				array(), MESSAGES_PER_PAGE);
        
    }
	
    function showNoticeForm()
    {
        $message_form = new ShaiMessageForm($this);
        $message_form->show();
    }
    
	function showRightsidebar() {
		$this->tu->showOwnerInfoWidget($this->owner_profile);
    			
    	$this->tu->showSubInfoWidget($this->owner_profile, $this->is_own);
    	$this->tu->showToolbarWidget($this->owner_profile, 'mail');
    	
    	$this->tu->showTagNavigationWidget($this->cur_user, 'home', null);
    	
//    	$this->navs = new NavList_PersonalMessages($this->owner_profile);
//    	$this->tu->showNavigationWidget($this->navs->lists(), $this->trimmed('action'));
        $this->tu->showGroupsWidget($this->owner_profile, 6);
        $subscriptions = $this->owner_profile->getSubscriptions(0, 18);
        $this->tu->showUserListWidget($subscriptions, '我关注的人');	
        $this->tu->showInviteWidget();
    	
    	$this->tu->showTagcloudWidget();
    }
    
    function show($args)
    {
    	$this->owner_profile = $args['owner_profile'];
    	parent::show($args);
    }
    
	function showEmptyList()
    {
    	$emptymsg = array();
        $emptymsg['p'] = '您的发件箱还没有私有消息。';
        $emptymsg['p'] = '站内信是您私有的信息，其它用户不可见。可以用它来和您的好友说悄悄话哦！';
        $this->tu->showEmptyListBlock($emptymsg);
    }
}
