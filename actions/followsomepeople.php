<?php


if (!defined('SHAISHAI')) {
    exit(1);
}

class FollowsomepeopleAction extends ShaiAction
{
	function handle($args)
	{
		parent::handle($args);
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
//        	if ($this->_validateForm()) {
        		$this->doPost($args);
//        	} 
        } else {
        	$this->doGet($args);
        }
	}
	
	function doPost($args) {
//		$serverArray = array_merge($_SESSION['gamevipprofileids'], $_SESSION['gameactiveprofileids'],
//				$_SESSION['serveractiveprofileids'], $_SESSION['serverpopularprofileids']);
		$serverArray = array_merge($_SESSION['gamevipprofileids'], $_SESSION['servermosttalkprofileids'],
				$_SESSION['gamemosttalkprofileids']);
		$relationArray = array_merge($_SESSION['recentsprofileids'], $_SESSION['latestauthorprofileids']);
		
		// follow active profiles
		if ($this->trimmed('follow_server') && count($serverArray) > 0) {
			foreach ($serverArray as $sa) {
				Subscription::subscribeTo($this->cur_user, User::staticGet('id', $sa));
			}
		} else {
			//若没有关注同游戏的用户，则默认关注vip用户以及100066-小酒保   100235-酒馆颁奖人
			$game_vip = Game::getVips($this->cur_user->game_id);
			$game_vip = array_diff($game_vip, array($this->cur_user->id,100066,100235));
			$game_bestusers = Notice::getMosttalkUsers(2, 'game',$this->cur_user->game_id);
			$gamebestuserids = array();
			foreach($game_bestusers as $gamebestuser)
			{
				$gamebestuserids[]=$gamebestuser['user_id'];
			}
			$gamebestuserids = array_diff($gamebestuserids, array($this->cur_user->id));
			
			$default_follow = array_merge($game_vip, $gamebestuserids, array(100066, 100235));
			
			foreach ($default_follow as $df) {
				Subscription::subscribeTo($this->cur_user, User::staticGet('id', $df));
			}
			
		}
		
		// follow relation profiles
		if ($this->trimmed('follow_relation')) {
			foreach ($relationArray as $ra) {
				Subscription::subscribeTo($this->cur_user, User::staticGet('id', $ra));
			}
		}
		
		// send message to active profiles
//		if ($this->trimmed('say_to_server')) {
//			// check ban words
//			$content = $this->trimmed('say_server_word');
//			
//			$simple_content = common_filter_huoxing($content);
//			if (common_banwordCheck($simple_content)) {
//				// 有封闭字眼直接不发
//			} else {
//				if (! empty($content)) {
//					foreach ($serverArray as $sa) {
//						$this->_sendMessage($this->cur_user, User::staticGet('id', $sa), $content);
//					}
//				}
//			}
//		}
		
		// send message to relation profiles
//		if ($this->trimmed('say_to_relation')) {
//			// check ban words
//			$content = $this->trimmed('say_relation_word');
//			
//			$simple_content = common_filter_huoxing($content);
//			if (common_banwordCheck($simple_content)) {
//				// 有封闭字眼直接不发
//			} else {
//				if (! empty($content)) {
//					foreach ($relationArray as $ra) {
//						$this->_sendMessage($this->cur_user, User::staticGet('id', $ra), $content);
//					}
//				}
//			}
//		}
		$_SESSION['havefollowed'] = 1;
		common_redirect(common_path('register/invite'), 303);
	}
	
	function doGet($args) {	
		
		// VIP玩家优先推荐
		$game_vip = Game::getVips($this->cur_user->game_id);
		$game_vip = array_diff($game_vip, array($this->cur_user->id));		
		$gamevipprofiles = Profile::getProfileByIds($game_vip);
		
		$_SESSION['gamevipprofileids'] = $game_vip;
		//同服务器发消息最多资料较全用户
		$gameservermosttalkusers = Notice::getMosttalkUsers(50, 'gameserver',$this->cur_user->game_server_id);
		$gameservermostids = array();
		foreach($gameservermosttalkusers as $gameservermosttalkuser)
		{
			$gameservermostids[]=$gameservermosttalkuser['user_id'];
		}
		$gameservermostids = array_diff($gameservermostids, array($this->cur_user->id));
		//同游戏发消息最多资料较全用户
		$gamemosttalkusers = Notice::getMosttalkUsers(50, 'game',$this->cur_user->game_id);
		$gamemostids = array();
		foreach($gamemosttalkusers as $gamemosttalkuser)
		{
			$gamemostids[]=$gamemosttalkuser['user_id'];
		}
		$gamemostids = array_diff($gamemostids, array($this->cur_user->id),$gameservermostids);
		
		
		
		
//		// 同服务器活跃前50
//		$server_active_top50 = Game_server::getActiveTop50($this->cur_user->game_server_id);
//		
//		// 排除自己和serveractive
//		$server_active_top50 = array_diff($server_active_top50, array($this->cur_user->id), $game_vip);
//		
//		// 同游戏活跃前50
//		$game_active_top50 = Game::getActiveTop50($this->cur_user->game_id);
//		
//		// 排除自己
//		$game_active_top50 = array_diff($game_active_top50, array($this->cur_user->id), $server_active_top50, $game_vip);
//		
//		
//		
//		// 同服异性受欢迎前50
//		$profile = $this->cur_user->getProfile();
//		if ($profile->sex == 'M') {
//			$server_popular_top50 = Game_server::getPopularTop50($this->cur_user->game_server_id, 'F');
//		} else {
//			$server_popular_top50 = Game_server::getPopularTop50($this->cur_user->game_server_id, 'M');
//		}
//		
//		$server_popular_top50 = array_diff($server_popular_top50, array($this->cur_user->id), $game_active_top50, $server_active_top50, $game_vip);
//		
		$recents50 = Game::getRecents50($this->cur_user->game_id);
		
		$recents50 = array_diff($recents50, array($this->cur_user->id), $gameservermostids, $gamemostids, $game_vip);
		
//		// 5个服务器中最活跃用户
//		$serveractiveprofileIds = common_random_fetch($server_active_top50, 5);
//		$_SESSION['serveractiveprofileids'] = $serveractiveprofileIds;
//		$serveractiveprofiles = Profile::getProfileByIds($serveractiveprofileIds);
//		
//		// 5名最受欢迎异性玩家
//		$serverpopularprofileIds = common_random_fetch($server_popular_top50, 5);
//		$_SESSION['serverpopularprofileids'] = $serverpopularprofileIds;
//		$serverpopularprofiles = Profile::getProfileByIds($serverpopularprofileIds);
//		
//		// 5个游戏中最活跃用户
//		if ((count($serveractiveprofileIds) + count($serverpopularprofileIds)) < 5) {
//			$gameactiveprofileIds = common_random_fetch($game_active_top50, 15);
//		} else {
//			$gameactiveprofileIds = common_random_fetch($game_active_top50, 10);
//		}
//		$_SESSION['gameactiveprofileids'] = $gameactiveprofileIds;
//		$gameactiveprofiles = Profile::getProfileByIds($gameactiveprofileIds);
		
		
		
		// 5个服务器中发消息较多用户
		$servermosttalkprofileIds = common_random_fetch($gameservermostids, 5);
		$_SESSION['servermosttalkprofileids'] = $servermosttalkprofileIds;
		$servermosttalkprofiles = Profile::getProfileByIds($servermosttalkprofileIds);
		
		// 8个游戏中发消息较多用户
		if (count($servermosttalkprofileIds) < 5) {
			$gamemosttalkprofileIds = common_random_fetch($gamemostids, 15);
		} else {
			$gamemosttalkprofileIds = common_random_fetch($gamemostids, 10);
		}
		$_SESSION['gamemosttalkprofileids'] = $gamemosttalkprofileIds;
		$gamemosttalkprofiles = Profile::getProfileByIds($gamemosttalkprofileIds);
		
		// XXX: 有缘游友目前只是简单的取了最后注册的20名玩家，还需要更好的算法来改善。
		
		// 取最后发言的10名玩家
		$latestAuthorIds = Profile::getLatestNoticeAuthorIds(10);
		$latestAuthorIds = array_diff($latestAuthorIds, array($this->cur_user->id));
		$_SESSION['latestauthorprofileids'] = $latestAuthorIds;
		$latestauthorprofiles = Profile::getProfileByIds($latestAuthorIds);
		
		
		$recentsprofileIds = common_random_fetch($recents50, 10);
		$recentsprofileIds = array_diff($recentsprofileIds, $latestAuthorIds, array($this->cur_user->id));
		$_SESSION['recentsprofileids'] = $recentsprofileIds;
		$recentprofiles = Profile::getProfileByIds($recentsprofileIds);
		
		
		
//		// 
//		//10个系统推广用户, 4个兴趣, 3+3同城同校同行随机抽取
//		$sysprofiles = Profile::getActiveProfileToFollow(0, 10);
//		
//		//4个兴趣
//		$interestprofiles = Profile::getRecommendProfileToFollow(0, 4);
//		
//		//3个同城
//		$cityprofiles = Profile::getCityProfileToFollow($profile->province, $profile->city, 0, 3);
	    
		if (array_key_exists('ivid', $_SESSION)) {
			$this->addPassVariable('welcomeUser', User::staticGet('id', $_SESSION['ivid']));
		}
		
		$this->addPassVariable('cur_game', Game::staticGet('id', $this->cur_user->game_id));
		$this->addPassVariable('cur_game_server', Game_server::staticGet('id', $this->cur_user->game_server_id));
		$this->addPassVariable('gamevip', $gamevipprofiles);
		$this->addPassVariable('servermosttalk', $servermosttalkprofiles);
		$this->addPassVariable('gamemosttalk', $gamemosttalkprofiles);
//	    $this->addPassVariable('gameactive', $gameactiveprofiles);
//	    $this->addPassVariable('serveractive', $serveractiveprofiles);
//	    $this->addPassVariable('serverpopular', $serverpopularprofiles);
	    $this->addPassVariable('recent', $recentprofiles);
	    $this->addPassVariable('latestauthor', $latestauthorprofiles);
		
		$this->displayWith('FollowsomepeopleHTMLTemplate');
	}
	
	function _sendMessage($user, $other, $content) {
//        Message::saveNew($user->id, $other->id, $content, 'web');
	}
}