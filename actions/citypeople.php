<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class CitypeopleAction extends ShaiAction
{
	function __construct() {
		parent::__construct();
		$this->cache_allowed = false;
	}

    function isReadOnly($args)
    {
        return true;
    }

    function handle($args)
    {
        parent::handle($args);
        $area = $this->trimmed('area');
        if($this->cur_user)
        {
        	$cur_profile = Profile::staticGet('id',$this->cur_user->id);
        	if($cur_profile->location == null)
        		$this->addPassVariable('notlocated', 1);	
        }
        
        $this->addPassVariable('notlocated', 0);
        if($area == 'gameserver') 
        { //getRandom100 是根据 关注度排名的前100用户。
        	$femaleplayer = Game_server::getRandom100($this->cur_user->game_server_id,'F',$cur_profile->province,$cur_profile->city,$this->cur_user->id);
			$maleplayer = Game_server::getRandom100($this->cur_user->game_server_id,'M',$cur_profile->province,$cur_profile->city,$this->cur_user->id);
        } else if($area == 'game') {

			$femaleplayer = Game::getRandom100($this->cur_user->game_id,'F',$cur_profile->province,$cur_profile->city,$this->cur_user->id);
			$maleplayer = Game::getRandom100($this->cur_user->game_id,'M',$cur_profile->province,$cur_profile->city,$this->cur_user->id);
        } else {
        	

			$femaleplayer = Profile::getRandom100('F',$cur_profile->province,$cur_profile->city,$this->cur_user->id);
			$maleplayer = Profile::getRandom100('M',$cur_profile->province,$cur_profile->city,$this->cur_user->id);
        }
		
        $excludeids = Subscription::getSubscribedids($this->cur_user->id);
        array_push($excludeids, $this->cur_user->id);
        
        $femaleplayer = array_diff($femaleplayer, $excludeids);
        	
        $maleplayer = array_diff($maleplayer, $excludeids);
        	
        $femaleplayerprofiles = Profile::getProfileByIds(common_random_fetch($femaleplayer,20));
        $maleplayerprofiles = Profile::getProfileByIds(common_random_fetch($maleplayer,20));
        $game = Game::staticGet('id',$this->cur_user->game_id);
        $gameserver = Game_server::staticGet('id',$this->cur_user->game_server_id);
        $gamename = $game->name;
        $servername = $gameserver->name;
        
        $this->addPassVariable('area', $area);
		$this->addPassVariable('gamename', $gamename);
		$this->addPassVariable('servername',$servername);
		$this->addPassVariable('femaleplayer',$femaleplayerprofiles);
		$this->addPassVariable('maleplayer',$maleplayerprofiles);
    	$this->displayWith('CityPeopleHTMLTemplate'); 
    }
}
