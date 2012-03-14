<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class ProfileList
{
	/**
	 * Data Access Object, can fetch profile one by one
	 */
    var $profiles = null;
    
    /**
     * user who's seeing this list
     */
    var $cur_user = null;
    
    /**
     * user who own this list, may be null
     */
    var $owner = null;
    
    /**
     * whether cur_user equals to owner
     */
    var $is_own = false;
    
    /**
     * the template object
     */
    var $out = null;

    function __construct($out, $profiles, $owner, $cur_user)
    {
        $this->out = $out;
        $this->profiles = $profiles;
        $this->cur_user = $cur_user;
        $this->owner = $owner;
        $this->is_own = $cur_user && $owner && $cur_user->id == $owner->id;
    }

    /**
     * show the profiles recursi
     */
    function show()
    {
    	$this->out->elementStart('ol', array('id' => 'users'));
    	
        $cnt = 0;
        while ($this->profiles->fetch()) {
            $cnt++;
            
            if($cnt > PROFILES_PER_PAGE) {
                break;
            }
            
            $this->out->elementStart('li', array('class' => 'user', 'pid' => $this->profiles->id));
            $this->showAvatar($this->profiles);
            $this->showNickname($this->profiles);
            $this->showInfos($this->profiles);
            $this->out->elementStart('div', 'op');
            $this->showOperations($this->profiles);
            $this->out->elementEnd('div');
            $this->out->elementEnd('li');
        }
        
        $this->out->elementEnd('ol');
        
        return $cnt;
    }
    
    function _showSubscribe($profile) {
    	$this->out->tu->startFormBlock(array('method' => 'post',
                                           'class' => 'subscribe',
                                           'action' => common_path('main/subscribe')), '关注');
    	$this->out->element('input', array('type' => 'hidden', 'name' => 'subscribeto', 'value' => $profile->id));
        $this->out->element('input', array('class' => 'orange76 button76', 'type' => 'submit', 'value' => '关注', 'title' => '开始关注'.$profile->nickname));
		$this->out->tu->endFormBlock();
    }
    
    function _showSubscribed($profile){
    	$this->out->element('div', 'subscribed', '已关注');
    }
    
    function _startMoreButton($profile) {
    	$this->out->elementStart('a', array('class' => 'toggle button76 silver76', 'title' => '查看更多操作', 'href' => '#'));
    	$this->out->text('更多操作');
    	$this->out->element('small', null, '▼');
    	$this->out->elementEnd('a');
    	$this->out->elementStart('ul', array('class' => 'more rounded5', 'style' => 'display:none;'));
    }
    
  
    /**
     * to add more function in more operation tag, 
     * you can rewrite this method, add some li before close ul.
     */
    function _endMoreButton($profile) {
    	$this->out->elementEnd('ul');
    }
    
    function showOperations($profile) {
    	if (! $this->cur_user || $profile->id == $this->cur_user->id){
    		return;
    	}
    	
    	$isSubscribed = $this->cur_user->isSubscribed($profile);
    	$isBeingSubscribed = $profile->getUser()->isSubscribed($this->cur_user);
    	
    	if (! $isSubscribed) {
    		$this->_showSubscribe($profile);
    	}else{
    		$this->_showSubscribed($profile);
    	}
    	
    	$this->_startMoreButton($profile);
    	
    	if ($isSubscribed) {
	    	$this->out->elementStart('li');
	    	$this->out->element('a', array('class' => 'unsubscribe', 'title' => '取消关注', 'href' => '#', 'to' => $profile->id, 'url' => common_path('main/unsubscribe')), '取消关注');
	    	$this->out->elementEnd('li');
    	}
    	
    	if ($isBeingSubscribed) {
	    	$this->out->elementStart('li');
	    	$this->out->element('a', array('class' => 'msg', 'title' => '悄悄话', 'href' => common_path('message/new'), 'to' => $profile->id, 'nickname' => $profile->nickname), '悄悄话');
	    	$this->out->elementEnd('li');
    	}
    	
    	$this->out->elementStart('li');
    	$this->out->element('a', array('class' => 'at', 'title' => '对TA说', 'href' => common_path('notice/replyat/' . $profile->uname), 'to' => $profile->id, 'nickname' => $profile->nickname), '对TA说');
    	$this->out->elementEnd('li');
    	
    	if ($this->cur_user->hasBlocked($profile)) {
	    	$this->out->elementStart('li');
	    	$this->out->element('a', array('class' => 'unblock', 'title' => '取消黑名单', 'href' => '#', 'to' => $profile->id, 'url' => common_path('main/unblock')), '取消黑名');
	    	$this->out->elementEnd('li');
    	} else {
    		$this->out->elementStart('li');
	    	$this->out->element('a', array('class' => 'block', 'title' => '黑名单', 'href' => '#', 'to' => $profile->id, 'url' => common_path('main/block')), '黑名单');
	    	$this->out->elementEnd('li');
    	}
    	
    	$this->out->elementStart('li');
    	$this->out->element('a', array('class' => 'illegal', 'title' => '非法举报', 'href' => '#', 'to' => $profile->id, 'url' => common_path('main/illegalreport')), '非法举报');
    	$this->out->elementEnd('li');
    	
    	$this->_endMoreButton($profile);
    }
    
    function showInfos($profile) {
    	$game = Game::staticGet('id', $profile->game_id);
    	$game_server = Game_server::staticGet('id', $profile->game_server_id);
    	$this->out->element('p', null, '游戏：' . $game->name . ' - ' . $game_server->name);
    	
    	$this->out->element('p', null, '所在地: ' . (empty($profile->location) ? '迷路中...' : $profile->location));
    }
    
    function showNickname($profile) {
    	$this->out->elementStart('p', 'nickname');
    	$this->out->elementStart('strong');
    	$this->out->element('a', array('href' => $profile->profileurl, 'title' => '访问' . $profile->nickname . '的主页'), $profile->nickname);
    	$this->out->elementEnd('strong');
    	$this->out->element('span', null, $profile->sex == 'M' ? '男' : '女');
    	$this->out->element('span', null, $profile->followers . '人关注');
    	$this->out->elementEnd('p');
    }
    
    function showAvatar($profile) {
    	$this->out->elementStart('div', 'avatar');
    	$this->out->elementStart('a', array('title' => $profile->nickname . '的头像', 'href' => $profile->profileurl));
    	$avatar = $profile->getAvatar(AVATAR_STREAM_SIZE);
    	$this->out->element('img', array('height' => '48', 'width' => '48', 
    		'src' => ($avatar) ? $avatar->displayUrl() : Avatar::defaultImage(AVATAR_STREAM_SIZE, $profile->id, $profile->sex), 
    		'alt' => $profile->nickname . '的头像'));
    	$this->out->elementEnd('a');
    	$this->out->elementEnd('div');
    }
    
}