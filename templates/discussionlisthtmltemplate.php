<?php
/**
 * Shaishai, the distributed microblog
 *
 * Display a reply list of the notice
 *
 * PHP version 5
 *
 * @category  Personal
 * @package   Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Display a reply list of the notice
 *
 * @category Personal
 * @package  Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

require_once INSTALLDIR.'/lib/discusslist.php';

class DiscussionlistHTMLTemplate extends RightsidebarHTMLTemplate

{
    /**
     * Returns the page title
     *
     * @return string page title
     */

	var $dis_list = null;
	var $notice_owner = null;
	var $notice_owner_profile = null;
	var $root_notice = null;
	var $is_own = false;
	
	function show($args) {
		$this->root_notice = Notice::staticGet('id', $args['root_id']);
		$this->dis_list = $args['dis_list'];
		$this->notice_owner = $args['notice_owner'];
		$this->notice_owner_profile = $this->notice_owner->getProfile();
		$this->is_own = ($args['cur_user'] && $this->notice_owner->id == $args['cur_user']->id);
		parent::show($args);
	}
	
	function showHeader()
    {
    	if ($this->cur_user) {
	        $this->elementStart('div', array('id' => 'header_outter_wrap'));
	        $this->elementStart('div', array('id' => 'header_inner_wrap'));
	        $this->elementStart('div', array('id' => 'header', 'class' => 'clearfix'));
	        $this->showLogo();
	        $this->showTopMenu();
	        $this->elementEnd('div');
	        $this->elementEnd('div');
	        $this->elementEnd('div');
    	} else {
    		$this->elementStart('div', array('id' => 'header_anonymous', 'class' => 'anonymous'));
    		$this->element('a', array('href' => common_path(''), 'title' => '回到登录页面', 'class' => 'lg'));
    		$this->elementStart('h2');
    		$this->raw('这是<strong>' . $this->notice_owner->nickname . '</strong>分享的消息，想随时了解到' . Profile::displayName($this->notice_owner_profile->sex, false) . '的最新动态吗？');
    		$this->elementEnd('h2');
    		$this->element('p', null, common_config('site', 'name') . '是为网游玩家量身打造的，是国内第一家以用户为中心的网游互动社区，是现在最酷最火的玩家互动平台。');
    		$this->element('p', null, '现在注册' . common_config('site', 'name') . '，与游友们分享游戏的点滴和快乐。');
    		$this->element('a', array('href' => common_path('register?ivid=' . $this->notice_owner->id), 'class' => 'toreg', 'rel' => 'nofollow', 'rel' => 'nofollow'), '');
    		$this->elementStart('p', 'tologin');
    		$this->text('已有' . common_config('site', 'name') . '账号？请');
    		$this->element('a', array('href' => common_path(''), 'class' => 'trylogin'), '登录');
    		$this->elementEnd('p');
    		$this->elementEnd('div');
    	}
    }
	
    function title()
    {
        return $this->notice_owner_profile->nickname . '说：' . common_cut_string($this->root_notice->content, 30);
    }
    
	function metaKeywords() {
		return $this->notice_owner_profile->nickname . '的消息、' . $this->notice_owner_profile->nickname . '的分享、' . $this->notice_owner_profile->nickname . '的回复、评论' . $this->notice_owner_profile->nickname . '、转载' . $this->notice_owner_profile->nickname;
	}
	
	function metaDescription() {
		return $this->notice_owner_profile->nickname . '在GamePub上发表了一条消息，您查看原文后，如果觉得精彩，可以通过“评论”、“转载”，“回复”此消息，与作者进行直接的沟通。';
	}
    
    function showRightsidebar() {
    	$this->tu->showOwnerInfoWidget($this->notice_owner_profile);
    	$this->tu->showSubInfoWidget($this->notice_owner_profile, $this->is_own);
    	
    	if (!empty($this->cur_user) && $this->cur_user->id == $this->notice_owner_profile->id) {
    		$this->tu->showToolbarWidget($this->notice_owner_profile);
    	}
    	
    	if($this->is_own) {
        	$navs = new NavList_MyNotices($this->notice_owner_profile);
    	    $this->tu->showNavigationWidget($navs->lists(), $this->trimmed('action'));
        } else {
            $navs = new NavList_Visitor($this->notice_owner_profile, $this->is_own);
    	    $this->tu->showNavigationWidget($navs->lists(), 'showstream', ! $this->cur_user);
        }
    	
    	$this->tu->showGroupsWidget($this->notice_owner_profile, 6, $this->is_own);
    	
    	$subscriptions = $this->notice_owner_profile->getSubscriptions(0, 18);
    	$this->tu->showUserListWidget($subscriptions, Profile::displayName($this->notice_owner_profile->sex, $this->is_own) . '关注的人');
    	
    	if ($this->cur_user) {
    		$this->tu->showInviteWidget();
    	}
    }
    
    function showContent()
    {   
        $this->elementStart('h2', 'discussions');
    	$this->raw($this->notice_owner_profile->nickname . '的消息<span style="font-weight:normal;font-size:12px;"> - ' . common_path('discussionlist/' . $this->arg('root_id')) . '</span>');
    	$this->elementEnd('h2');    	
    	
//      $this->out->text($profile->nickname . ' (' . $profile->uname .')于' . common_date_iso8601($rootnotice->created) . '所发消息的相关评论');
//      if ($rootnotice->profile_id != common_current_user()->id) {
//			$this->out->element('a', array('id' => 'get_illreport', 'class'=>'b_pis_a', 
//			                               'href' => '#', 'title'=>'非法消息举报'));
//    	}
        $this->elementStart('div', 'discussion_detail');
    	
        
        
        $item = new DislistRootItem($this->root_notice, $this);
        $item->show();
        
        $this->showDisForm();
        
        $this->elementStart('dl', 'discussions');
        $this->elementStart('dt');
        $this->raw('评论共<span>' . ($this->root_notice->discussion_num ? $this->root_notice->discussion_num : '0') . '</span>条');
        if ($this->cur_user) {
        	if ($this->cur_user->id != $this->notice_owner_profile->id) {
        		$this->element('a', array('class' => 'report', 'href' => '#', 'title' => '举报这条消息', 'to' => $this->notice_owner_profile->id,
        			'url' => common_path('main/illegalreport')), '举报');
        	}
        } else {
        	$this->element('a', array('class' => 'trylogin', 'title' => '举报这条消息',
        		'href' => common_path('register'), 'rel' => 'nofollow'), '举报');
        }
        $this->elementEnd('dt');
        
        $this->elementStart('dd');
        $ct = new DiscussionList($this->dis_list, $this);
        $cnt = $ct->show();
		$this->elementEnd('dd');
		$this->elementEnd('dl');
		
		$this->numpagination($this->args['total'], 'discussionlist', array('notice_id' => $this->args['notice_id']));
		
//        $this->numpagination($this->cur_page > 1, $this->args['total'] > ($this->cur_page)*NOTICES_PER_PAGE,
//                          $this->cur_page, 'discussionlist',
//                          array('notice_id' => $this->args['notice_id']), $this->args['total'], "");
		
                          
        
        
        $this->elementEnd('div');
    }
    
    function showDisForm()
    {
//        if($this->cur_user) {
			$this->tu->startFormBlock(array('method' => 'post',
	    					'action' => common_path('discuss/new?discussto=' . $this->root_notice->id),
	    					'class' => 'mydiscussion'), '发表讨论');
    		
			$this->elementStart('dl');
			$this->elementStart('dt');
			$this->element('span', null, '发评论');
			$this->element('a', array('href' => '#', 'class' => 'emotion'), '插入表情');
			$this->elementEnd('dt');
			$this->elementStart('dd');
	    	$this->element('textarea', array('class' => 'dis_form',
	                                      'name' => 'status_textarea',
	    								  'maxlength' => '280'));
			$this->elementEnd('dd');
	    	$this->elementEnd('dl');
			$this->elementStart('p');
			$this->element('input', array('name' => 'status_check',
	                                           'type' => 'checkbox',
	                                           'class' => 'checkbox',
											   'id' => 'status_check_id'));
			$this->element('label', array('for' => 'status_check_id'), '作为一条新消息');
			$this->elementEnd('p');
			
			if ($this->cur_user) {
				$this->element('input', array('id' => 'notice_action_submit',
	                                           'name' => 'status_submit',
	                                           'type' => 'submit',
											   'class' => 'submit button76 green76',
	                                           'value' => '发评论'));
			} else {
				$this->element('a', array('href' => common_path('register'), 'class' => 'trylogin submit button76 green76', 'rel' => 'nofollow'), '发评论');
			}
			
//			$this->element('a', array('href' => '#', 'title' => '插入表情', 'level' => $this->cur_user->getLevel(), 'class' => 'emotion'), '插入表情');
			
	    	$this->element('input', array('type' => 'hidden',
	                                               'value' => $this->root_notice->id,
	                                               'name' => 'indiscussto'));
	    	$this->element('input', array('type' => 'hidden', 'name' => 'from', 'value' => 'detail'));
	    	$this->tu->endFormBlock();
//        }
    }
    
//	function showIllegal() {
//		$this->elementStart('div', array('id'=>'illegalreport', 'title'=>'非法举报', 'style'=>'display:none'));
//		
//		$this->elementStart('dl', 'b_el');
//		$notice = Notice::staticGet('id', $this->args['notice_id']);       
//        $profile = $notice->getProfile();
//		$this->element('dt', null, '您将要举报'. $profile->nickname. '的消息。');
//		$this->elementEnd('dl');
//		
//		$this->elementStart('dl', 'b_el');
//		$this->element('dt', null, '您的举报将被严格保密，我们将认真阅读您的举报信息并适当处理。');
//		$this->elementEnd('dl');
//		
//		$this->elementStart('div', 'b_cbf');
//		$form = new IllegalReportForm($this, 0, $this->args['notice_id']);
//        $form->show();
//        $this->elementEnd('div');
//        
//        $this->elementEnd('div');
//	}
	
    function showScripts() {
		parent::showScripts();
//    	$this->script('js/lshai_reportillegal.js');
    	$this->script('js/lshai_dislist.js');
    	$this->script('js/lshai_relation.js');
	}
}

class DislistRootItem extends NoticeListItem
{
	function show()
    {
        $this->showStart();
        $this->showNoticeInfo();
        $this->showRoot();
        $this->showNoticeBar();
		$this->showContext();
        $this->showEnd();
    }
    
	function showNoticeBar()
    {
    	$this->out->elementStart('div', 'bar clearfix');
    	$this->out->elementStart('div', 'info');
    	$dt = common_date_iso8601($this->notice->created);
        $this->out->element('span', array('class' => 'time timestamp',
                                          'title' => $dt, 'time' => strtotime($this->notice->created)),
                            common_date_string($this->notice->created));                           
		$this->showNoticeSource();   
		$this->out->elementEnd('div');  	
		
    	$this->showNoticeOptions();
    	$this->out->elementEnd('div');
    }
	
    function showStart()
    {
    	if (!empty($this->notice->conversation)
            && $this->notice->conversation != $this->notice->id) {
            $this->out->elementStart('div', array('class' => 'notice', 'id' => 'notice-' . $this->notice->id, 'style' => 'padding-top:31px;', 'nid' => $this->notice->id));	
        } else {
        	$this->out->elementStart('div', array('class' => 'notice', 'id' => 'notice-' . $this->notice->id, 'nid' => $this->notice->id));
        }
    }
    
 	function showEnd()
    {
    	$this->out->element('input', array('class' => 'uname', 'type' => 'hidden', 'value' => $this->profile->uname));
    	if($this->notice->topic_type != 4) {
        	$this->out->element('input', array('name' => 'mode', 'type' => 'hidden', 'value' => ''));
        	$this->out->element('input', array('name' => 'mode_identifier', 'type' => 'hidden', 'value' => ''));
        } else {
	        $group_id = Group_inbox::getGroupId($this->notice->id);
	        if($group_id) {
		        $this->out->element('input', array('name' => 'mode', 'type' => 'hidden', 'value' => 'group'));
	        	$group = User_group::staticGet('id', $group_id);
	        	$this->out->element('input', array('name' => 'mode_identifier', 'type' => 'hidden', 'value' => $group->uname));  
	        } else {
	        	$this->out->element('input', array('name' => 'mode', 'type' => 'hidden', 'value' => ''));
        		$this->out->element('input', array('name' => 'mode_identifier', 'type' => 'hidden', 'value' => ''));
	        }      	
        }
        $this->out->elementEnd('div');
        
    }
    
	function showNoticeOptions()
    {
    	if ($this->user) {
            $this->out->elementStart('ul', array('class' => 'op'));
            if($this->notice->user_id != $this->user->id) {
            	$this->showRetweetLink();
            	$this->showNoticeOptionSeparator();	
	            $this->showFaveForm();
	            $this->showNoticeOptionSeparator();		
            	$this->showReplyLink();
            } else {	
	            $this->showFaveForm();
//	            $this->showNoticeOptionSeparator();		
//	            $this->showDeleteLink();   
            }            
            $this->out->elementEnd('ul');
        }
    }
}