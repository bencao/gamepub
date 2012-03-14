<?php
/**
 * Shaishai, the distributed microblog
 *
 * Play flash game
 *
 * PHP version 5
 *
 * @category  FlashGame
 * @package   Shaishai
 * @author    AGun Chan <agunchan@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

require_once INSTALLDIR.'/lib/noticelist.php';
require_once INSTALLDIR.'/lib/discusslist.php';

class FlashplayHTMLTemplate extends BasicHTMLTemplate
{
	var $root_notice;
	var $dis_list = null;
	var $total_dis;
	var $flash;
	var $hot_flash;
	var $owner_profile;
	var $catName = array(1 => '益智', 2 => '动作', 3 => '射击', 4 => '搞笑', 5 => '体育', 6 => '棋牌');
	var $catEname = array(1 => 'puzzle', 2 => 'act', 3 => 'shoot', 4 => 'fun', 5 => 'sport', 6 => 'chess');
	
	function show($args) {
		$this->root_notice = $args['root_notice'];
		$this->dis_list = $args['dis_list'];
		$this->total_dis = $args['total_dis'];
		$this->flash = $args['flash'];
		$this->owner_profile = $args['owner_profile'];
		$this->hot_flash = $args['hot_flash'];
		parent::show($args);
	}
	
	function showShaishaiScripts() {
		parent::showShaishaiScripts();
		$this->script('js/ZeroClipboard.js');
		$this->script('js/lshai_flashplay.js');
		$this->script('js/lshai_dislist.js');
		$this->script('js/lshai_relation.js');
	}
	
	function showShaishaiStylesheets() {
		parent::showShaishaiStylesheets();
		$this->cssLink('css/minigame.css', 'default');
	}
	
    function title()
    {
        return $this->owner_profile->nickname . '分享的游戏 - ' . $this->flash->title;
    }

    function showEmptyList()
    {
        $message = '没有找到此游戏.';

        $this->elementStart('div', 'instruction');
        $this->raw(common_markup_to_html($message));
        $this->elementEnd('div');
    }
    
    function _showPlaying() {
    	$this->elementStart('div', 'player rounded5');
    	$this->elementStart('div', array('id' => 'mini', 'fid' => $this->flash->id));
    	$this->elementStart('object', array('classid' => 'clsid:D27CDB6E-AE6D-11cf-96B8-444553540000',
    		'codebase' => 'http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,28,0',
    		'width' => '640', 'height' => '480', 'id' => 'flasharea'));
    	$this->element('param', array('name' => 'movie', 'value' => $this->flash->path));
    	$this->element('param', array('name' => 'quality', 'value' => 'high'));
    	$this->element('param', array('name' => 'allowScriptAccess', 'value' => 'never'));
    	$this->element('embed', array('src' => $this->flash->path, 'quality' => 'high',
    		'pluginspage' => 'http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash',
    		'width' => '640', 'height' => '480', 'allowScriptAccess' => 'never'
    	));
		$this->elementEnd('object');
		
    	$this->elementEnd('div');
    	$this->elementStart('ul');
    	$this->elementStart('li');
    	$this->element('a', array('href' => '#', 'class' => 'fullscreen'), '全屏');
    	$this->elementEnd('li');
    	$this->elementStart('li');
    	$this->element('a', array('href' => '#', 'class' => 'reset'), '重玩');
    	$this->elementEnd('li');
    	$this->elementStart('li');
    	$this->element('a', array('href' => common_local_url('newnotice'), 'class' => 'recommend'), '推荐此游戏');
    	$this->elementEnd('li');
    	$this->elementStart('li');
    	$this->element('a', array('href' => '#', 'class' => 'copyurl', 'id' => 'copyurl'), '复制地址');
    	$this->elementEnd('li');
    	$this->elementStart('li');
    	$this->element('a', array('href' => '#', 'class' => 'todiscuss'), '评论' . ($this->total_dis ? '(' . $this->total_dis . ')' : ''));
    	$this->elementEnd('li');
    	$this->elementEnd('ul');
    	$this->elementEnd('div');
    }
    
    function _showInfo() {
    	$this->elementStart('dl', 'info rounded5');
    	$this->elementStart('dt', 'title');
    	$this->element('a', array('href' => '#', 'class' => 'toinfo'), '游戏详情');
    	$this->element('a', array('href' => '#', 'class' => 'toop'), '游戏操作');
    	$this->elementEnd('dt');
    	$this->elementStart('dd');
    	$this->elementStart('dl');
    	$this->elementStart('dt');
    	$this->element('h2', null, $this->flash->title);
    	$this->elementEnd('dt');
    	$this->elementStart('dd');
    	$this->element('p', null, '类别：' . $this->catName[$this->flash->type]);
    	$this->elementStart('p', 'misc');
    	$rating = $this->flash->getRating();
    	$this->elementStart('span', array('class' => 'rating', 'title' => $rating . '/100'));
    	$this->text('人气：');
    	$this->elementStart('em');
    	$this->element('b', array('style' => 'width:' . $rating . '%;'));
    	$this->elementEnd('em');
    	$this->elementEnd('span');
    	$this->elementEnd('p');
    	$this->elementEnd('dd');
    	$this->elementEnd('dl');
    	
    	$this->elementStart('dl');
    	$this->element('dt', null, '游戏简介：');
    	$this->elementStart('dd');
    	$this->text($this->flash->introduction);
    	$this->elementEnd('dd');
    	$this->elementEnd('dl');
    	
    	$this->_showUploader();
    	
    	$this->elementEnd('dd');
    	$this->elementEnd('dl');
    }
    
    function _showUploader() {
    	$uploader = $this->owner_profile;
    	$this->elementStart('dl', 'uploader');
    	$this->element('dt', null, '上传者：');
    	$this->elementStart('dd');
    	$this->elementStart('div', 'avatar');
    	$this->elementStart('a', array('href' => $uploader->profileurl));
    	$this->element('img', array('src' => $uploader->avatarUrl(AVATAR_STREAM_SIZE), 'alt' => $uploader->nickname));
    	$this->elementEnd('a');
    	$this->elementEnd('div');
    	$this->elementStart('p', 'nickname');
    	$this->element('a', array('href' => $uploader->profileurl), $uploader->nickname);
    	$this->elementEnd('p');
    	$uploaderGame = Game::staticGet('id', $uploader->game_id);
    	$uploaderServer = Game_server::staticGet('id', $uploader->game_server_id);
    	$this->element('p', null, $uploaderGame->name . '-' . $uploaderServer->name);
    	$this->element('p', null, $uploader->location);
    	
    	if ($this->cur_user
    		&& $this->cur_user->id != $uploader->id) {
    		if ($this->cur_user->isSubscribed($uploader)) {
    			$this->element('a', array('href' => '#', 'class' => 'subscribed', 'title' => '已关注'), '已关注');
    		} else {
    			$this->tu->startFormBlock(array('class' => 'subscribe', 'action' => common_local_url('subscribe'), 'method' => 'post'), '关注');
    			$this->element('input', array('type' => 'hidden', 'name' => 'subscribeto', 'value' => $uploader->id));
    			$this->element('input', array('type' => 'submit', 'class' => 'submit', 'value' => '', 'title' => '关注'));
    			$this->tu->endFormBlock();
    		}
    	}
    	
    	$this->elementEnd('dd');
    	$this->elementEnd('dl');
    }
    
    function _showOp() {
    	$this->elementStart('dl', array('class' => 'op rounded5', 'style' => 'display:none;'));
    	$this->elementStart('dt', 'title');
    	$this->element('a', array('href' => '#', 'class' => 'toinfo'), '游戏详情');
    	$this->element('a', array('href' => '#', 'class' => 'toop'), '游戏操作');
    	$this->elementEnd('dt');
    	$this->elementStart('dd');
    	
    	$this->elementStart('dl');
    	$this->element('dt', null, '游戏操作');
    	$this->elementStart('dd');
    	$this->text($this->flash->detail);
    	$this->elementEnd('dd');
    	$this->elementEnd('dl');
    	
    	$this->elementEnd('dd');
    	$this->elementEnd('dl');
    }
    
    function showCore() {
    	$this->elementStart('div', array('id' => 'wrap_mini'));
    	$this->elementStart('div', 'miniplayground rounded5');
    	$this->_showPlaying();
    	$this->_showInfo();
    	$this->_showOp();
    	$this->elementEnd('div');
    	
    	$this->elementStart('div', 'interaction clearfix');
    	$this->_showMiniDiscuss();
    	$this->_showMiniHot();
    	$this->elementEnd('div');
    	
    	$this->elementEnd('div');	
    }
    
    function _showMiniHot() {
    	$this->elementStart('dl', 'minihot rounded5');
    	$this->elementStart('dt', 'rounded5');
    	$this->text($this->catName[$this->flash->type] . '类热门游戏');
    	$this->element('a', array('href' => common_local_url('flashgame', array('cat' => $this->catEname[$this->flash->type]))), '更多');
    	$this->elementEnd('dt');
    	$this->elementStart('dd');
    	$this->elementStart('ol');
    	while ($this->hot_flash && $this->hot_flash->fetch()) {
    		$this->elementStart('li');
    		$this->elementStart('div', 'avatar');
    		$this->elementStart('a', array('href' => common_local_url('flashplay', array('id' => $this->hot_flash->id))));
    		$this->element('img', array('src' => $this->hot_flash->picpath, 'alt' => $this->hot_flash->title));
    		$this->elementEnd('a');
    		$this->elementEnd('div');
    		$this->elementStart('p', 'nickname');
    		$this->element('a', array('href' => common_local_url('flashplay', array('id' => $this->hot_flash->id))), $this->hot_flash->title);
    		$this->elementEnd('p');
    		$this->element('p', null, '简介：' . $this->hot_flash->introduction);
    		$this->elementEnd('li');
    	}
    	$this->elementEnd('ol');
    	$this->elementEnd('dd');
    	$this->elementEnd('dl');
    }
    
    function _showMiniDiscuss() {
    	$this->elementStart('div', 'minidiscuss_outter rounded5');
    	$this->elementStart('div', 'minidiscuss rounded5');
    	$this->_showDiscussionForm();
    	$this->_showDiscussionList();
    	$this->elementEnd('div');
    	$this->elementEnd('div');
    }
    
    function _showDiscussionList() {
    	$this->elementStart('dl', 'discussions');
        $this->elementStart('dt');
        $this->raw('评论共<span>' . ($this->root_notice->discussion_num ? $this->root_notice->discussion_num : '0') . '</span>条');
        if ($this->cur_user) {
        	if ($this->cur_user->id != $this->owner_profile->id) {
        		$this->element('a', array('class' => 'report', 'href' => '#', 'title' => '举报这个游戏', 'to' => $this->owner_profile->id,
        			'url' => common_local_url('illegalreport')), '举报');
        	}
        } else {
        	$this->element('a', array('class' => 'trylogin', 'title' => '举报这个游戏',
        		'href' => common_local_url('register'), 'rel' => 'nofollow'), '举报');
        }
        $this->elementEnd('dt');
        
        $this->elementStart('dd');
        $ct = new DiscussionList($this->dis_list, $this);
        $cnt = $ct->show();
		$this->elementEnd('dd');
		$this->elementEnd('dl');
		
		$this->numpagination($this->total_dis, 'discussionlist', array('notice_id' => $this->root_notice->id));
    }
    
    function _showDiscussionForm() {
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
		$this->element('label', array('for' => 'status_check_id'), '作为一条新消息');
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
		
    	$this->element('input', array('type' => 'hidden',
                                               'value' => $this->root_notice->id,
                                               'name' => 'indiscussto'));
    	$this->element('input', array('type' => 'hidden', 'name' => 'from', 'value' => 'detail'));
    	$this->tu->endFormBlock();
    }
    
 	function showBody()
    {
        $this->elementStart('body', array('token' => common_session_token(), 'anonymous' => $this->cur_user ? '0' : '1'));
        
        if (Event::handle('StartShowHeader', array($this))) {
            $this->showHeader();
            Event::handle('EndShowHeader', array($this));
        }
        
		$this->showCore();
        
        $this->showFloatBar();
        
        $this->showWaiter();
        
        $this->elementEnd('body');
    }
}

