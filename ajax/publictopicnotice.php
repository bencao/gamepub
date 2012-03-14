<?php

if (!defined('SHAISHAI')) { exit(1); }

class PublictopicnoticeAction extends ShaiAction
{
	function __construct() {
		parent::__construct();
		$this->no_anonymous = false;
	}
	
	function prepare($args) {
		if (! parent::prepare($args)) {return false;}
		$this->q = $this->trimmed('q');
		return true;
	}
    
    function handle($args)
    {
    	parent::handle($args);
    	
    	if (common_config('site', 'yunpingsearch')) {
	    	require_once INSTALLDIR . '/lib/ShaiNoticeSearchEngine.php';
	        $engine = new ShaiNoticeSearchEngine();
	        // 缓存版
	        $ids = $engine->search($this->q);
	        if ($ids) {
    			$nids = explode(' ', trim($ids));
	        } else {
	        	$nids = $this->getNoticeIds();
	        }
    	} else {
    		$nids = $this->getNoticeIds();
    	}

    	
    	if (! empty($nids)) {
    		$stringer = new XMLStringer();
    		
	    	$noticeids = common_random_fetch($nids, 1);
	    	
	    	$notice = Notice::staticGet('id', $noticeids[0]);
	    	
		    while (! ($notice && $notice->content_type == 1)) {
		    	$noticeids = common_random_fetch($nids, 1);
		    	$notice = Notice::staticGet('id', $noticeids[0]);
		    }
	    	
	    	$profile = $notice->getProfile();
	    	$profile_game = Game::staticGet('id', $profile->game_id);
	    	$profile_server = Game_server::staticGet('id', $profile->game_server_id);
	    	
//	    	$stringer->elementStart('div', array('class' => 'related_notice', 'style' => 'display:none;'));
	        $stringer->elementStart('div', 'card');
	        $stringer->elementStart('div', 'avatar');
	        $stringer->elementStart('a', array('href' => $profile->profileurl));
	        $stringer->element('img', array('src' => $profile->avatarUrl(AVATAR_STREAM_SIZE), 'alt' => $profile->nickname));
	        $stringer->elementEnd('a');
	        $stringer->elementEnd('div');
	        $stringer->elementStart('div', 'op');
	        if (! $this->cur_user) {
	    		$stringer->element('a', array('href' => common_path('register?ivid=' . $profile->id), 'class' => 'trylogin subscribe', 'title' => '关注'), '关注');
	    	} else if ($this->cur_user->isSubscribed($profile)) {
	    		$stringer->element('div', array('class' => 'subscribed'), '关注中');
	    	} else {
		        $stringer->elementStart('form', array('method' => 'post', 'class' => 'subscribe', 'action' => common_path('main/subscribe')));
		        $stringer->elementStart('fieldset');
		        $stringer->element('legend', null, '关注');
		        $stringer->element('input', array('type' => 'hidden', 'name' => 'token', 'value' => common_session_token()));
		        $stringer->element('input', array('type' => 'hidden', 'name' => 'subscribeto', 'value' => $profile->id));
		        $stringer->element('input', array('class' => 'submit', 'type' => 'submit', 'value' => '关注'));
		        $stringer->elementEnd('fieldset');
		        $stringer->elementEnd('form');
	    	}
	        $stringer->elementEnd('div');
	        $stringer->elementEnd('div');
	        $stringer->elementStart('div', 'cts');
	        $stringer->elementStart('p', 'nickname');
	        $stringer->element('a', array('href' => $profile->profileurl), $profile->nickname);
	        $stringer->elementEnd('p');
	        $stringer->element('p', 'info', $profile_game->name . ' - ' . $profile_server->name . ' 粉丝' . $profile->followers);
	        $stringer->elementStart('p', 'msg');
	        $stringer->raw($notice->rendered);
	        $stringer->elementEnd('p');
	        $stringer->elementEnd('div');
	        
	        $this->showJsonResult(array('html' => $stringer->getString()));
    	}
    }
    
    function getNoticeIds() {
    	return common_stream('notice:publictopic:' . $this->q, array($this, "_getNoticeIds"), null, 3600);
    }
    
    function _getNoticeIds() {
    	$queryString = '(';
        $keywords = common_tokenize($this->q);
        foreach($keywords as $v){
			$queryString .= sprintf('rendered LIKE "%%%1$s%%" OR ', addslashes($v));
		}
		$queryString .= ' 1=0) AND topic_type <> 4';
		$queryString .= ' AND content_type = 1';
		$notice = new Notice();
		$notice->whereAdd($queryString);
		$notice->whereAdd('is_banned = 0');
		$notice->orderBy('created desc');
		$notice->limit(0, NOTICES_PER_PAGE + 1);
		$notice->find();
		
		$nids = array();
		while ($notice->fetch()) {
			$nids[] = $notice->id;
		}
		return $nids;
    }
}