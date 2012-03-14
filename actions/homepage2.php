<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class Homepage2Action extends ShaiAction
{
	function __construct() {
		parent::__construct();
		$this->no_anonymous = false;
		$this->cache_allowed = false;
	}
	
	function handle($args) 
	{
		parent::handle($args);
	
		if (common_have_session() && array_key_exists('login_error', $_SESSION)) {
			$this->addPassVariable('login_error', $_SESSION['login_error']);
			unset($_SESSION['login_error']);
		}
		
		// some dynamic data is got here

		$messageHtml = common_stream('homepage:msgs', array($this, "getStreamMessages"), null, 120);
		
		$this->addPassVariable('posts', $messageHtml);

		$userHtml = common_stream('homepage:users', array($this, "getStreamUsers"), null, 120);
		
//		$userHtml = $this->getStreamUsers();
    	
		$this->addPassVariable('recents', $userHtml);
		
		$tagHtml = common_stream('homepage:tags', array($this, "getStreamTags"), null, 600);
		
		$this->addPassVariable('tags', $tagHtml);
		
		$this->addPassVariable('isOldUser', ($this->trimmed('o') && $this->trimmed('o') == 'y'));
		
		$this->displayWith('Homepage2HTMLTemplate');
	}
	
	function getStreamTags() {
		
		$tags = new Notice_tag();
        $second_tag = new Second_tag();

        #Need to clear the selection and then only re-add the field
        #we are grouping by, otherwise it's not a valid 'group by'
        #even though MySQL seems to let it slide...
        $tags->joinAdd($second_tag);
        
        $tags->selectAdd();
        $tags->selectAdd('second_tag.name as tag');
        $tags->selectAdd('second_tag.id as tagid');

        #Add the aggregated columns...
        $tags->selectAdd('max(notice_id) as last_notice_id');
        $calc='sum(exp(-(now() - created)/%s)) as weight';
        $tags->selectAdd(sprintf($calc, common_config('tag', 'dropoff')));
        $tags->whereAdd('second_tag.id >= 1000000');
//        $tags->whereAdd("not second_tag.name in ('送Q币活动', '送Q币', '拿Q币')");
        $tags->groupBy('tag');
        $tags->orderBy('weight DESC');

        $tags->limit(20);

        $tags->find();
        
        $html = '';
        while ($tags->fetch()) {
        	$html .= '<li><a href="' . common_path('hottopics/' . trim($tags->tag)) . '" title="查看与' . trim($tags->tag) . '相关的话题">' . $tags->tag . '</a></li>';
        }
        return $html;
	}

	function getStreamMessages() {
		//过滤掉公会的消息,因为有些公会设置为私有的,他人不应该看见其消息
		$notice = Notice::publicStream(0, 16, 0, 0, null, 1, 0, 4);
		$stringer1 = new XMLStringer();
		$stringer2 = new XMLStringer();
		
		$cnt = 0;
		while ($notice->fetch()) {
			$profile = $notice->getProfile();
			$profile_game = Game::staticGet('id', $profile->game_id);
			$profile_server = Game_server::staticGet('id', $profile->game_server_id);
			if ($profile) {
				$avatar = $profile->getAvatar(AVATAR_STREAM_SIZE);
				if ($cnt < 5) {
					$stringer = $stringer1;
				} else {
					$stringer = $stringer2;
				}
				$stringer->elementStart('li');
				$stringer->elementStart('div', 'avatar');
				$stringer->elementStart('a', array('href' => $profile->profileurl, 'title' => $profile->nickname));
				$stringer->element('img', array('src' => ($avatar ? $avatar->url : Avatar::defaultImage(AVATAR_STREAM_SIZE, $profile->id, $profile->sex)),
					'alt' => $profile->nickname));
				$stringer->elementEnd('a');
				$stringer->elementEnd('div');
				$stringer->elementStart('div', 'content');
				$stringer->element('a', array('href' => $profile->profileurl, 'class' => 'nickname'), $profile->nickname);
				// 过滤http://
				
				//$str = preg_replace('/\[[^\]]*\]/i', '', $notice->rendered);
				$str = preg_replace('/\<img[^\>]*\>/i', '', $notice->rendered);
				$str = preg_replace("/\n/", '', $str);
					
				preg_match('/^<span>(?<desc>.*)<\/span>.*$/isU', $str, $matches);
				$desc = trim($matches['desc']);
				
				$stringer->raw($desc);
				$stringer->elementStart('span');
				$stringer->text($profile_game->name . '-' . $profile_server->name);
				$stringer->element('a', array('href' => common_path('discussionlist/' . $notice->id), 'title' => '查看评论'), common_date_string($notice->created));
				$stringer->elementEnd('span');
				$stringer->elementEnd('div');
				$stringer->elementEnd('li');
			}
			$cnt ++;
		}
		
		return $stringer2->getString() . $stringer1->getString();
	}
	
	function getStreamUsers() {
		$recents = Profile::getRecentRegisteredPeople(0, 20);
//		$html = '';
		$xmler = new XMLStringer();
//		$cnt = 0;
		while ($recents->fetch()) {
			$xmler->elementStart('li');
			$xmler->elementStart('div', 'avatar');
			$xmler->elementStart('a', array('href' => $recents->profileurl, 'title' => $recents->nickname));
			$xmler->element('img', array('src' => $recents->avatarUrl(AVATAR_STREAM_SIZE), 'alt' => $recents->nickname, 'width' => '48', 'height' => '48'));
			$xmler->elementEnd('a');
			$xmler->elementEnd('div');
//			$xmler->elementStart('div', 'more');
//			$xmler->element('div', 'bg');
//			$xmler->elementStart('p', 'nickname');
//			$xmler->elementStart('a', array('title' => '去' . $recents->nickname . '的主页看看', 'href' => $recents->profileurl));
//			$xmler->text($recents->nickname);
//			$xmler->elementEnd('a');
//			$xmler->element('span', null, ($recents->sex == 'M' ? '男' : '女'));
//			$xmler->elementEnd('p');
//			$game = Game::staticGet('id', $recents->game_id);
//	    	$server = Game_server::staticGet('id', $recents->game_server_id);
//	    	$xmler->element('p', 'server', $game->name . '-' . $server->name);
//	    	$xmler->element('p', 'time', $recents->created);
//			$xmler->elementEnd('div');
			$xmler->elementEnd('li');
//	    	$cnt ++;
    	}
    	return $xmler->getString();
	}
}

?>