<?php

if (!defined('SHAISHAI')) {
    exit(1);
}


class RankHTMLTemplate extends PublictwocolumnHTMLTemplate
{
	var $femaleplayer = null;
	var $maleplayer = null;
	
	function title()
    {
    	$message = '风云榜';
    	switch($this->args['type'])
    	{
    		case 'user': $message .='-平台总人气榜';
    				break;
    		default:$message .='-游戏排行榜';
    				break;
    	}
    	return $message;
    }
    
	function showContent() {
		
		$this->showCorehead();
		
		if($this->args['type'] == 'user')
			$this->showUserRank();
		else $this->showGameRank();
		
	}

	function showCorehead() {
		$this->elementStart('h2');
		$this->elementStart('ul',array('class' => 'clearfix','id' => 'public_thirdary_nav'));
		if($this->args['type'] == 'game' || !$this->args['type'])
			$this->elementStart('li',array('class' => 'active'));
		else
			$this->elementStart('li');
			$this->element('a',array('href' => common_local_url('rank', array('type' => 'game')),'alt' => '游戏排行'),'游戏排行');
		$this->elementEnd('li');
		$this->element('li',null,'|');
		if($this->args['type'] == 'user')
			$this->elementStart('li',array('class' => 'active'));
		else
			$this->elementStart('li');
			$this->element('a',array('href' => common_local_url('rank', array('type' => 'user')),'alt' => '人气排行'),'人气排行');
		$this->elementEnd('li');
		$this->element('li',null,'|');
		if($this->args['type'] == 'retweet')
			$this->elementStart('li',array('class' => 'active'));
		else
			$this->elementStart('li');
			$this->element('a',array('href' => common_local_url('rank', array('type' => 'retweet')),'alt' => '转载排行'),'转载排行');
		$this->elementEnd('li');
		$this->element('li',null,'|');
		if($this->args['type'] == 'discuss')
			$this->elementStart('li',array('class' => 'active'));
		else
			$this->elementStart('li');
			$this->element('a',array('href' => common_local_url('rank', array('type' => 'discuss')),'alt' => '讨论排行'),'讨论排行');
		$this->elementEnd('li');
		$this->elementEnd('ul');
		$this->elementEnd('h2');
	}
	
	function showGameRank() {
		$this->elementStart('dl',array('class' => 'tops'));
		$this->element('dt',array('class' => 'title'),'游戏排行榜');
		$this->elementStart('dd',array('class' => 'clearfix'));
		$this->showAllGameRank();
		$this->showVideoGameRank();
		$this->showPicGameRank();
		$this->showMusicGameRank();
		$this->elementEnd('dd');
		$this->elementEnd('dl');
	}
	
	function showAllGameRank()
	{
		$this->elementStart('dl',array('class' => 'game'));
		$this->elementStart('dt');
		$this->text('今日最火爆游戏');
		$this->elementStart('span',array('class' => 'right_top'));
		$this->elementStart('a',array('title' => '新增消息数','href' => '#'));
		$this->raw('&#160;');
		$this->elementEnd('a');
		$this->elementEnd('span');
		$this->elementStart('span',array('class' => 'right_bottom'));
		$this->elementStart('a',array('title' => '消息总数','href' => '#'));
		$this->raw('&#160;');
		$this->elementEnd('a');
		$this->elementEnd('span');
		$this->elementEnd('dt');
		$this->elementStart('dd');
		$this->gameListshow($this->args['allgametoplist'],1);
		$this->elementEnd('dd');
		$this->elementEnd('dl');
	}
	
	function showVideoGameRank()
	{
		$this->elementStart('dl',array('class' => 'game'));
		$this->elementStart('dt');
		$this->text('视频热门游戏');
		$this->elementStart('span',array('class' => 'right_top'));
		$this->elementStart('a',array('title' => '新增消息数','href' => '#'));
		$this->raw('&#160;');
		$this->elementEnd('a');
		$this->elementEnd('span');
		$this->elementStart('span',array('class' => 'right_bottom'));
		$this->elementStart('a',array('title' => '消息总数','href' => '#'));
		$this->raw('&#160;');
		$this->elementEnd('a');
		$this->elementEnd('span');
		$this->elementEnd('dt');
		$this->elementStart('dd');
		$this->gameListshow($this->args['videogametoplist'],3);
		$this->elementEnd('dd');
		$this->elementEnd('dl');
	}
	
	function showPicGameRank()
	{
		$this->elementStart('dl',array('class' => 'game'));
		$this->elementStart('dt');
		$this->text('图片热门游戏');
		$this->elementStart('span',array('class' => 'right_top'));
		$this->elementStart('a',array('title' => '新增消息数','href' => '#'));
		$this->raw('&#160;');
		$this->elementEnd('a');
		$this->elementEnd('span');
		$this->elementStart('span',array('class' => 'right_bottom'));
		$this->elementStart('a',array('title' => '消息总数','href' => '#'));
		$this->raw('&#160;');
		$this->elementEnd('a');
		$this->elementEnd('span');
		$this->elementEnd('dt');
		$this->elementStart('dd');
		$this->gameListshow($this->args['picgametoplist'],4);
		$this->elementEnd('dd');
		$this->elementEnd('dl');
	}
	function showMusicGameRank()
	{
		$this->elementStart('dl',array('class' => 'game'));
		$this->elementStart('dt');
		$this->text('音乐热门游戏');
		$this->elementStart('span',array('class' => 'right_top'));
		$this->elementStart('a',array('title' => '新增消息数','href' => '#'));
		$this->raw('&#160;');
		$this->elementEnd('a');
		$this->elementEnd('span');
		$this->elementStart('span',array('class' => 'right_bottom'));
		$this->elementStart('a',array('title' => '消息总数','href' => '#'));
		$this->raw('&#160;');
		$this->elementEnd('a');
		$this->elementEnd('span');
		$this->elementEnd('dt');
		$this->elementStart('dd');
		$this->gameListshow($this->args['musicgametoplist'],2);
		$this->elementEnd('dd');
		$this->elementEnd('dl');
	}
	
	function gameListshow($gamelistids,$type = 1)
	{
		$cnt = 0;
		$cur = time();
		$dt = strftime('%Y-%m-%d', $cur);
		//当天0:0:0的总秒数
		$today = strtotime($dt);		
		//一周
		$dayago = $today - 3600*24;
		$cachestr = date('Y-m-d', $dayago);
		
		$this->elementStart('ol');
		
		foreach($gamelistids as $gamelistid) {
			$cnt ++;
			$game_stat = Game_stat::staticGet('game_id',$gamelistid);
			$game_stat_history = Game_stat_history::stathisGetbyidtime($gamelistid,$cachestr);
			if($game_stat_history)
			{
				$prepicnum = $game_stat_history->pic_num;
				$prevideonum = $game_stat_history->video_num;
				$premusicnum = $game_stat_history->music_num;
				$pretextnum = $game_stat_history->text_num;
			} else {
				$prepicnum = 0;
				$prevideonum = 0;
				$premusicnum = 0;
				$pretextnum = 0;
			}
			$game = Game::staticGet('id',$gamelistid);
			switch($type)
			{
				case 4: $total = $game_stat->pic_num;
						$change = $total - $prepicnum;
						$change = ($change < 0)?0:$change;
    					break;
    			case 3: $total = $game_stat->video_num;
    					$change = $total - $prevideonum;
    					$change = ($change < 0)?0:$change;
    					break;
    			case 2: $total = $game_stat->music_num;
    					$change = $total - $premusicnum;
    					$change = ($change < 0)?0:$change;
    					break;
    			default: $total = $game_stat->music_num + $game_stat->video_num + $game_stat->pic_num + $game_stat->text_num;
    					$change = $total - $premusicnum - $prevideonum - $prepicnum -$pretextnum;
    					$change = ($change < 0)?0:$change;
    					break;
			}
			$game_id = $game->id;
			$game_name = $game->name;
        	$this->elementStart('li');
        	if($cnt <= 5)
        	{	$this->elementStart('div',array('class' => 'avatar'));
        		$this->elementStart('a',array('title' => $game_name,'href' => common_local_url('game',array('gameid' => $game_id))));
        		$this->element('img',array('src' => common_path('images/gamelogos/'.$game_id.'/logo.jpg')));
        		$this->elementEnd('a');
        		$this->elementEnd('div');
        	}
	        else 
	        { 	$this->elementStart('p',array('class' => 'nickname'));
	        	$this->element('a',array('title' => $game_name,'href' => common_local_url('game',array('gameid' => $game_id))),$game_name);
	        	$this->elementEnd('p');
	        }
        	
	        $this->element('p',array('class' => 'position'),$cnt);
	        $this->element('p',array('class' => 'change'),$change);
	        $this->element('p',array('class' => 'total'),$total);
        	$this->elementEnd('li');
        	
		}
		
		$this->elementEnd('ol');
	}
	function showUserRank() {
		
		$this->showWebUserRank();
		if(common_current_user())
		{
			$this->showGameUserRank();
		
			$this->showGameserverUserRank();
		}
		
	}
	
	function showWebUserRank() {
		$this->elementStart('dl',array('class' => 'tops'));
		$this->element('dt',array('class' => 'title'),'平台总人气榜');
		$this->elementStart('dd',array('class' => 'clearfix'));
		
		$this->showMostfocus('all');
		$this->showMostactive('all');
		$this->showMostinfluence('all');
		
		$this->elementEnd('dd');
		$this->elementEnd('dl');
	}
	
	function showMostfocus($type)
	{
		$this->elementStart('dl',array('class' => 'people'));
		$this->elementStart('dt');
		$this->text('今日最受关注榜');
		$this->element('span',array('class' => 'right_top'),'今日新增');
		$this->element('span',array('class' => 'right_bottom'),'总关注数');
		$this->elementEnd('dt');
		$this->elementStart('dd');
		$this->showContentLeft($type);
		$this->elementEnd('dd');
		$this->elementEnd('dl');
		
	}
	
	function showMostactive($type)
	{
		$this->elementStart('dl',array('class' => 'people'));
		$this->elementStart('dt');
		$this->text('今日G币帝');
		$this->element('span',array('class' => 'right_top'),'今日新增');
		$this->element('span',array('class' => 'right_bottom'),'总铜币数');
		$this->elementEnd('dt');
		$this->elementStart('dd');
		$this->showContentCen($type);
		$this->elementEnd('dd');
		$this->elementEnd('dl');
		
	}
	
	function showMostinfluence($type)
	{
		$this->elementStart('dl',array('class' => 'people'));
		$this->elementStart('dt');
		$this->text('今日最具影响力');
		$this->element('span',array('class' => 'right_top'),'今日新增');
		$this->element('span',array('class' => 'right_bottom'),'总转发数');
		$this->elementEnd('dt');
		$this->elementStart('dd');
		$this->showContentRight($type);
		$this->elementEnd('dd');
		$this->elementEnd('dl');
		
	}
	function showContentLeft($type = 'all') {
		$this->elementStart('ol');
		if($type == 'game')
		$subs = Subscription::getSubscribedOrder(15, null,$type, $this->args['game_id']);
		else if($type == 'gameserver')
		$subs = Subscription::getSubscribedOrder(15, null,$type, $this->args['server_id']);
		else  $subs = Subscription::getSubscribedOrder(15);
		$cnt = 0;
		foreach($subs as $sub) {
			$cnt ++;
			$profile_id = $sub['user_id'];
			$subnum = $sub['num'];
			$profile = Profile::staticGet('id', $profile_id);
        	$avatar = $profile->getAvatar(AVATAR_STREAM_SIZE);
        	$fullname = $profile->nickname;
        	
        	$updatenum = Subscription::getSubscribedNum($profile_id, 'day');
        	
			if ($cnt <= 5) {
	        	$this->elementStart('li', 'top5');
        		$this->elementStart('div',array('class' => 'avatar'));
	        	$this->elementStart('a',array('href' => $profile->profileurl, 'title' => '访问' . $profile->nickname . '的主页'),
	        			 $profile->nickname);
	        	$this->element('img', array('height' => '40', 'width' => '40', 
		    			'src' => ($avatar) ? $avatar->displayUrl() : Avatar::defaultImage(AVATAR_STREAM_SIZE, $profile->id, $profile->sex), 
	    				'alt' => $profile->nickname));
	        	$this->elementEnd('a');
	        	$this->elementEnd('div');
        	} else {
        		$this->elementStart('li');
        	}
        	$this->elementStart('p',array('class' => 'nickname'));
        	$this->element('a',array('href' => $profile->profileurl, 'title' => '访问' . $profile->nickname . '的主页'),
        			 $profile->nickname);
        	$this->elementEnd('p');
        	$this->element('p',array('class' => 'position'),$cnt);
        	$this->element('p',array('class' => 'change'),$updatenum);
        	$this->element('p',array('class' => 'total'),$subnum);

        	$this->elementEnd('li');
        	
		}
		$this->elementEnd('ol');
	}
	
	function showContentCen($type = 'all') {
		$this->elementStart('ol');
		if($type == 'game')
		$subs = Grade_record::getHotUsers(15,$type, $this->args['game_id']);
		else if($type == 'gameserver')
		$subs = Grade_record::getHotUsers(15,$type, $this->args['server_id']);
		else $subs = Grade_record::getHotUsers(15);
		$cnt = 0;
		foreach($subs as $sub) {
			$cnt ++;
			$profile_id = $sub['user_id'];
			$score = $sub['score'];
			$profile = Profile::staticGet('id', $profile_id);
        	$fullname = $profile->uname;
        	$updatenum = $sub['changed'];
        	$avatar = $profile->getAvatar(AVATAR_STREAM_SIZE, AVATAR_STREAM_SIZE);
        	if ($cnt <= 5) {
	        	$this->elementStart('li', 'top5');
        		$this->elementStart('div',array('class' => 'avatar'));
	        	$this->elementStart('a',array('href' => $profile->profileurl, 'title' => '访问' . $profile->nickname . '的主页'),
	        			 $profile->nickname);
	        	$this->element('img', array('height' => '40', 'width' => '40', 
		    			'src' => ($avatar) ? $avatar->displayUrl() : Avatar::defaultImage(AVATAR_STREAM_SIZE, $profile->id, $profile->sex), 
	    				'alt' => $profile->nickname));
	        	$this->elementEnd('a');
	        	$this->elementEnd('div');
        	} else {
        		$this->elementStart('li');
        	}
        	$this->elementStart('p',array('class' => 'nickname'));
        	$this->element('a',array('href' => $profile->profileurl, 'title' => '访问' . $profile->nickname . '的主页'),
        			 $profile->nickname);
        	$this->elementEnd('p');
        	$this->element('p',array('class' => 'position'),$cnt);
        	$this->element('p',array('class' => 'change'),((int) ($updatenum/10)));
        	$this->element('p',array('class' => 'total'), ((int) ($score/10)));

        	$this->elementEnd('li');
        	
		}
		$this->elementEnd('ol');
		
	}
	
	function showContentRight($type = 'all') {
		$this->elementStart('ol');
		if($type == 'game')
		$subs = Notice::getInfluenceUser(15,$type, $this->args['game_id']);
		else if($type == 'gameserver')
		$subs = Notice::getInfluenceUser(15,$type, $this->args['server_id']);
		else $subs = Notice::getInfluenceUser(15);
		$cnt = 0;
		foreach($subs as $sub) {
			$cnt ++;
			$profile_id = $sub['user_id'];
			$subnum = $sub['num'];
			$profile = Profile::staticGet('id', $profile_id);
        	$avatar = $profile->getAvatar(AVATAR_STREAM_SIZE);
        	$fullname = $profile->uname;
        	$updatenum = Notice::getRetweetNum($profile_id, 'day');
        	
			if ($cnt <= 5) {
	        	$this->elementStart('li', 'top5');
        		$this->elementStart('div',array('class' => 'avatar'));
	        	$this->elementStart('a',array('href' => $profile->profileurl, 'title' => '访问' . $profile->nickname . '的主页'),
	        			 $profile->nickname);
	        	$this->element('img', array('height' => '40', 'width' => '40', 
		    			'src' => ($avatar) ? $avatar->displayUrl() : Avatar::defaultImage(AVATAR_STREAM_SIZE, $profile->id, $profile->sex), 
	    				'alt' => $profile->nickname));
	        	$this->elementEnd('a');
	        	$this->elementEnd('div');
        	} else {
        		$this->elementStart('li');
        	}
        	
        	$this->elementStart('p',array('class' => 'nickname'));
        	$this->element('a',array('href' => $profile->profileurl, 'title' => '访问' . $profile->nickname . '的主页'),
        			 $profile->nickname);
        	$this->elementEnd('p');
        	$this->element('p',array('class' => 'position'),$cnt);
        	$this->element('p',array('class' => 'change'),$updatenum);
        	$this->element('p',array('class' => 'total'),$subnum);

        	$this->elementEnd('li');
        	
		}
		$this->elementEnd('ol');
		
	}
	function showGameUserRank() {
		$this->elementStart('dl',array('class' => 'tops'));
		$this->element('dt',array('class' => 'title'),$this->args['gamename'].'玩家人气榜');
		$this->elementStart('dd',array('class' => 'clearfix'));
		
		$this->showMostfocus('game');
		$this->showMostactive('game');
		$this->showMostinfluence('game');
		
		$this->elementEnd('dd');
		$this->elementEnd('dl');
	}
	function showGameserverUserRank() {
		$this->elementStart('dl',array('class' => 'tops'));
		$this->element('dt',array('class' => 'title'),$this->args['servername'].'玩家人气榜');
		$this->elementStart('dd',array('class' => 'clearfix'));
		
		$this->showMostfocus('gameserver');
		$this->showMostactive('gameserver');
		$this->showMostinfluence('gameserver');

		$this->elementEnd('dd');
		$this->elementEnd('dl');
	}
	function showRetweetRank() {
		
		$this->showWebRetweetRank();
		$this->showGameRetweetRank();
		$this->showServerRetweetRank();
	}
	
	function showDissRank() {
		
		$this->showWebDissRank();
		$this->showGameDissRank();
		$this->showServerDissRank();
	}
	
	function showWebRetweetRank()
	{
		
	}
	
	function showGameRetweetRank()
	{
		
	}
	
	function showServerRetweetRank()
	{
		
	}
	
	function showWebDissRank()
	{
		
	}
	
	function showGameDissRank()
	{
		
	}
	
	function showServerDissRank()
	{
		
	}
}
?>