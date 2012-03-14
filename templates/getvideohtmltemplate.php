<?php
/**
 * Shaishai, the distributed microblog
 *
 * get video 
 *
 * PHP version 5
 *
 * @category  Notice
 * @package   Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * get video
 *
 * @category Personal
 * @package  Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

require_once INSTALLDIR.'/lib/noticelist.php';
require_once INSTALLDIR.'/lib/discusslist.php';

class GetvideoHTMLTemplate extends RightsidebarHTMLTemplate
{
	var $owner;
	var $owner_profile;
	var $is_own;
	var $root_notice;
	var $dis_list = null;
	var $video;
	
	function show($args) {
		$this->root_notice = $args['root_notice'];
		$this->dis_list = $args['dis_list'];
		$this->video = $args['video'];
		
		$this->owner = $args['owner'];
		$this->owner_profile = $args['owner_profile'];
		$this->is_own = $this->cur_user && ($this->cur_user->id == $this->owner->id);
		parent::show($args);
	}
	/**
     * Title of the page
     *
     * @return page title, including page number if over 1
     */

    function title()
    {
        return $this->owner->nickname . '分享的视频 - ' . sprintf('%s', $this->args['video']->title);
    }

    function showEmptyList()
    {
        $message = '没有找到此视频.';

        $this->elementStart('div', 'instruction');
        $this->raw(common_markup_to_html($message));
        $this->elementEnd('div');
    }
    
	function showRightsidebar() {
		
		$this->owner_profile = $this->owner->getProfile();
//    	$page_owner_profile = $this->args['profile'];
    	
    	$this->tu->showProfileDetailWidget($this->owner_profile, 1);
    	$this->tu->showSubInfoWidget($this->owner_profile, $this->is_own);
    	
    	$videos = Video::getUserVideo($this->owner->id, 0, 3);
    	$this->showVideosWidget($this->owner, $videos);
    	
    	$notice = Notice_heat::heatOrderStream(3, 3);
    	$this->showHotVideosWidget($notice);
//    	$this->tu->showVideoFromNoticeBlock($notice, $this->args['video']->id);
    }
    
    function showContent()
    {
    	$this->tu->showUserSummaryBlock($this->owner_profile, $this->cur_user, $this->owner);
    	$this->element('div', 'split');
    	$this->showNotices();
    }

    function showNotices()
    {
        $item = new VideoNoticeItem($this->root_notice, $this, null, $this->video);
        $item->show();
        
        $this->elementStart('div', array('class' => 'discussion_detail', 'style' => 'border:0;padding:0;'));
        
        $this->showDisForm();
        
        $this->elementStart('dl', 'discussions');
        $this->elementStart('dt');
        $this->raw('评论共<span>' . ($this->root_notice->discussion_num ? $this->root_notice->discussion_num : '0') . '</span>条');
        if (!empty($this->cur_user) && $this->cur_user->id != $this->owner->id) 
        	$this->element('a', array('class' => 'report', 'href' => '#', 'title' => '非法举报', 'to' => $this->owner->id,
        		'url' => common_local_url('illegalreport')), '举报');
        $this->elementEnd('dt');
        
        $this->elementStart('dd');
        $ct = new DiscussionList($this->dis_list, $this);
        $cnt = $ct->show();
		$this->elementEnd('dd');
		$this->elementEnd('dl');
		
		$this->numpagination($this->args['total'], 'getvideo', array('id' => $this->video->id));
                          
        
        
        $this->elementEnd('div');
    }
    
	function showDisForm()
    {
//        if($this->cur_user) {
			$this->tu->startFormBlock(array('method' => 'post',
	    					'action' => common_local_url('newdiscuss', array('discussto' => $this->root_notice->id)),
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
			$this->element('lable', array('for' => 'status_check_id'), '作为一条新消息');
			$this->elementEnd('p');
			
        	if ($this->cur_user) {
				$this->element('input', array('id' => 'notice_action_submit',
	                                           'name' => 'status_submit',
	                                           'type' => 'submit',
											   'class' => 'submit button76 green76',
	                                           'value' => '发评论'));
			} else {
				$this->element('a', array('href' => common_local_url('register'), 'class' => 'trylogin submit button76 green76', 'rel' => 'nofollow'), '发评论');
			}
			
//			$this->element('input', array('id' => 'notice_action_submit',
//	                                           'name' => 'status_submit',
//	                                           'type' => 'submit',
//											   'class' => 'submit button76 green76',
//	                                           'value' => '发评论'));
			
	    	$this->element('input', array('type' => 'hidden',
	                                               'value' => $this->root_notice->id,
	                                               'name' => 'indiscussto'));
	    	$this->element('input', array('type' => 'hidden', 'name' => 'from', 'value' => 'detail'));
	    	$this->tu->endFormBlock();
//        }
    }
    
	function showScripts() {
		parent::showScripts();
    	//$this->script('js/lshai_getdetailinfo.js');
    	//$this->script('js/lshai_reportillegal.js');
    	$this->script('js/lshai_relation.js');
    	$this->script('js/lshai_showstream.js');
    	$this->script('js/ZeroClipboard.js');
    	$this->script('js/lshai_dislist.js');
	}
	
	function showHotVideosWidget($notice) {
    	$this->elementStart('dl', 'widget videos');
    	$this->elementStart('dt');
    	$this->element('a', array('href' => '#'), '其他热门视频');
    	$this->element('a', array('class' => 'unfold', 'href' => '#'));
    	$this->elementEnd('dt');
    	$this->elementStart('dd');
    	$this->elementStart('ul');
    	
        while ($notice != null && $notice->fetch()) {
            $video = Video::getVideoFromNotice($notice->id);
            
            $this->elementStart('li');
	    	$this->element('a', array('href' => common_path('share/getvideo/' . $video->id)), $video->title);
	    	$this->element('span', null, common_date_string($notice->created) . ' ' . $video->source);
	    	$this->elementEnd('li');    
        }
    	
    	$this->elementEnd('ul');
    	$this->elementEnd('dd');
    	$this->elementEnd('dl');
    }
    
	function showVideosWidget($user, $video) {
    	$this->elementStart('dl', 'widget videos');
    	$this->elementStart('dt');
    	$this->element('a', array('href' => '#'), $user->nickname . '的其他视频');
    	$this->element('a', array('class' => 'unfold', 'href' => '#'));
    	$this->elementEnd('dt');
    	$this->elementStart('dd');
    	$this->elementStart('ul');
    	
        while ($video != null && $video->fetch()) {
	    	$this->elementStart('li');
	    	$this->element('a', array('href' => common_path('share/getvideo/' . $video->id)), $video->title);
	    	$notice = Notice::staticGet('id', $video->notice_id);
	    	$this->element('span', null, common_date_string($notice->created) . ' ' . $video->source);
	    	$this->elementEnd('li');
        }
    	
    	$this->elementEnd('ul');
    	$this->elementEnd('dd');
    	$this->elementEnd('dl');
    }
}

class VideoNoticeItem extends NoticeListItem
{
	var $video = null;
	
    function __construct($notice, $out=null, $noImage=null, $video=null)
    {
        parent::__construct($notice, $out, $noImage);
        $this->video = $video;
    }
    
	function show() {
		$this->out->elementStart('dl', array('class' => 'rounded5', 'id' => 'videoplayer'));
        
        $this->out->elementStart('dt', 'title');
        $this->out->element('strong', null, $this->profile->nickname . '说: ');
        $cv = null;
        if(preg_match ('/(.*)?<div class="video_message">/isU', $this->notice->rendered, $content)) {
        	$cv = $content[1];
        }        
        $this->out->raw($cv);
        $this->out->elementEnd('dt');

        $this->out->elementStart('dd', 'body');
        $this->out->elementStart('div', 'player');
        $this->out->raw($this->video->rendered);
        $this->out->elementEnd('div');
        
//        $this->out->elementStart('dl', 'detail');
//        $this->out->element('dt', null, '此视频的相关信息');
//        $this->out->elementStart('dd');
//        $this->out->elementStart('ul');
//        $this->out->element('li', null, '标题: ' . $this->video->title);
//		$this->out->element('li', null, '来源: ' . $this->video->source);
//        $this->out->element('li', null, '分享日期: ' . substr($this->notice->created, 0, 10));
//        $this->out->elementEnd('ul');        
//        $this->out->elementEnd('dd');        
//        $this->out->elementEnd('dl');
        
        $this->out->elementStart('div', 'detail');
        $this->out->text('视频标题: ' . $this->video->title .'，来自' . $this->video->source . '。');       
        $this->out->elementEnd('div');
        
        $this->showNoticeBar();
        $this->out->elementEnd('dd');
        
        $this->out->element('input', array('class' => 'name', 'type' => 'hidden', 'value' =>$this->profile->nickname));
        $this->out->element('input', array('class' => 'uname', 'type' => 'hidden', 'value' => $this->profile->uname));
        if($this->notice->topic_type != 4) {
        	$this->out->element('input', array('name' => 'mode', 'type' => 'hidden', 'value' => ''));
        	$this->out->element('input', array('name' => 'mode_identifier', 'type' => 'hidden', 'value' => ''));
        } else {
	        //可以依据getvideo?mode_identifier来识别? 还是只提取一个就可以了呢??
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
		$this->out->elementEnd('dl'); 
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
//            	$this->showNoticeOptionSeparator();	
	            $this->showFaveForm();
//	            $this->showNoticeOptionSeparator();	
//	            $this->showDeleteLink();   
            }            
            $this->out->elementEnd('ul');
        }
    }
	
}

