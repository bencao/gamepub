<?php
/**
 * copyright @ shaishai.com
 */

if (!defined('SHAISHAI')) {
    exit(1);
}


/**
 * Action for displaying the public stream
 *
 * @category Public
 * @package  SHAISHAI
 * @link     http://www.shaishai.com/
 *
 */

class GamefriendsAction extends GamebasicAction
{
    
	function __construct() {
		parent::__construct();
		$this->no_anonymous = true;
	}

    function isReadOnly($args)
    {
        return true;
    }

    function handle($args)
    {
        parent::handle($args);
        
        $sex = $this->trimmed('sex', '0');
        $agebegin = $this->trimmed('agebegin', 10);
		$ageend = $this->trimmed('ageend', 60);
		$loc = $this->trimmed('loc', '');
		$game_id = $this->trimmed('game', $this->cur_game->id);
    	$game_big_zone_id = $this->trimmed('game_big_zone', 0);
    	$game_server_id = $this->trimmed('game_server', 0);
    	
    	$this->addPassVariable('sex', $sex);
	    $this->addPassVariable('agebegin', $agebegin);
    	$this->addPassVariable('ageend', $ageend);
    	$this->addPassVariable('loc', $loc);
		$this->addPassVariable('game_id', $game_id);
		$this->addPassVariable('game_big_zone_id', $game_big_zone_id);
		$this->addPassVariable('game_server_id', $game_server_id);
        
		//当点击'更多'链接时，虽然是Get方法，但必会传sex参数为异性
        if ($_SERVER['REQUEST_METHOD'] == 'GET' && !$sex) {
        	//只推荐未关注的异性玩家
        	$cur_profile = $this->cur_user->getProfile();
        	$opp_sex = $cur_profile->sex == 'M' ? 'F' : 'M';	
        	$excludeids = Subscription::getSubscribedids($this->cur_user->id);  
        	
        	//本游戏
        	$player = Game::getRandom100($this->cur_game->id, $opp_sex);
	    	$player = array_diff($player, $excludeids);        	
	    	$gameprofiles = Profile::getProfileByIds(common_random_fetch($player,5));
        	
	    	//本游戏同服
	    	$serverplayerprofiles = null;
	    	if ($this->cur_game->id == $this->cur_user->game_id) {
	    		$player = Game_server::getRandom100($this->cur_user->game_server_id, $opp_sex);    
	    		$player = array_diff($player, $excludeids);        	
	    		$serverplayerprofiles = Profile::getProfileByIds(common_random_fetch($player,5));
	    	}
	    	
	    	//本游戏同城
	    	$player = Game::getRandom100($this->cur_game->id, $opp_sex, $cur_profile->province, $cur_profile->city);
	    	$player = array_diff($player, $excludeids);        	
	    	$cityplayerprofiles = Profile::getProfileByIds(common_random_fetch($player,5));
	    	
	    	$this->addPassVariable('curprofile', $cur_profile);
	    	$this->addPassVariable('gameplayer', $gameprofiles);
    		$this->addPassVariable('serverplayer', $serverplayerprofiles);
			$this->addPassVariable('cityplayer', $cityplayerprofiles);
    	} else {
    		$loc == '' ? false : $loc;
    		
			$profileids = Profile::getPeopleWanted(
									$sex, 
									$agebegin, 
									$ageend, 
									$loc, 
									$game_id, 
									$game_big_zone_id, 
									$game_server_id,
									($this->cur_page - 1) * PROFILES_PER_PAGE,
									PROFILES_PER_PAGE
									);
			$total =  Profile::getPeopleWantednum(
									$sex, 
									$agebegin, 
									$ageend, 
									$loc, 
									$game_id, 
									$game_big_zone_id, 
									$game_server_id
									);					
	        $profile = Profile::getProfileByIds($profileids);
			
	        $this->addPassVariable('profile', $profile);
			$this->addPassVariable('total', $total);
    	}
       	$this->displayWith('GamefriendsHTMLTemplate'); 
    }
}