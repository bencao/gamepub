<?php

if (!defined('SHAISHAI')) { exit(1); }

class HofrandvipAction extends ShaiAction
{
	function __construct() {
		parent::__construct();
		$this->no_anonymous = false;
	}
	
    function handle($args)
    {
    	parent::handle($args);
    	
    	$latestNotices = Notice::getLatestVipNoticeIds(20);
		
		if (! empty($latestNotices)) {
			$starNoticeId = common_random_fetch($latestNotices, 1);
			$starNotice = Notice::staticGet('id', $starNoticeId[0]);
		} else {
			$starNotice = false;
		}
		
    	if ($starNotice) {
    		$star = $starNotice->getProfile();
    		
    		$this->view = new XMLStringer();
	    	$this->view->elementStart('div', 'card');
	    	$this->view->elementStart('div', 'avatar');
	    	$this->view->elementStart('a', array('href' => $star->profileurl, 'title' => '访问' . $star->nickname . '的个人主页'));
	    	$this->view->element('img', array('src' => $star->avatarUrl(AVATAR_STREAM_SIZE), 'width' => '48', 'height' => '48'));
	    	$this->view->elementEnd('a');
	    	$this->view->elementEnd('div');
	    	$this->view->elementStart('div', 'op');
    		if (! $this->cur_user) {
	    		$this->view->element('a', array('href' => common_path('register?ivid=' . $star->id), 'class' => 'trylogin subscribe', 'title' => '关注'), '关注');
	    	} else if ($this->cur_user->isSubscribed($star)) {
	    		$this->view->element('div', array('class' => 'subscribed'), '关注中');
	    	} else {
	    		$this->view->elementStart('form', array('method' => 'post',
	                                           'class' => 'subscribe',
	                                           'action' => common_path('main/subscribe')));
	        	$this->view->elementStart('fieldset');
	        	$this->view->element('legend', null, '关注');
	        	$this->view->element('input', array('type' => 'hidden', 'name' => 'token', 'value' => common_session_token()));
	    		$this->view->element('input', array('type' => 'hidden', 'name' => 'subscribeto', 'value' => $star->id));
	        	$this->view->element('input', array('class' => 'submit', 'type' => 'submit', 'value' => '关注'));
				$this->view->elementEnd('fieldset');
        		$this->view->elementEnd('form');
	    	}
	    	$this->view->elementEnd('div');
	    	$this->view->elementEnd('div');
	    	
	    	$this->view->elementStart('p', 'nickname');
	    	$this->view->element('a', array('href' => $star->profileurl, 'title' => '访问' . $star->nickname . '的个人主页'), $star->nickname);
	    	$this->view->elementEnd('p');
	    	$this->view->elementStart('p', 'info');
	    	$this->view->element('span', null, $star->getGameString());
	    	$this->view->text($star->subscriberCount() . '人关注');
	    	$this->view->elementEnd('p');
	    	$this->view->elementStart('p', 'msg');
	    	$this->view->element('a', array('href' => common_path('discussionlist/' . $starNotice->id), 'target' => '_blank'), $starNotice->content);
	    	$this->view->elementEnd('p');
	    	
	    	$this->showJsonResult(array('html' => $this->view->getString()));
    	}
    	
    	
    }
}

?>