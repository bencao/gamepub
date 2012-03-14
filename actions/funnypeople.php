<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class FunnypeopleAction extends ShaiAction
{
    
    var $page = null;
    
	function __construct() {
		parent::__construct();
		$this->no_anonymous = false;
	}

    function isReadOnly($args)
    {
        return true;
    }

    function prepare($args)
    {
        if (! parent::prepare($args)) {return false;}
        
    	if($this->arg('page') && !is_numeric($this->arg('page'))) {
            $this->clientError('您访问的页数不存在.', 403);
            return;
	     }
        $this->page = ($this->arg('page')) ? ($this->arg('page')+0) : 1;

        common_set_returnto($this->selfUrl());

        return true;
    }

    function handle($args)
    {
        parent::handle($args);
        $area = $this->trimmed('area');
        
        if (!common_current_user()) 
        {  	
//        	$femaleplayer = common_stream('webuser:random100female',
//					array("Profile", "getRandom100"), array('F'));
//					
//			$maleplayer = common_stream('webuser:random100male',
//					array("Profile", "getRandom100"), array('M'));
			$femaleplayer = Profile::getRandom100('F');
					
			$maleplayer = Profile::getRandom100('M'); 
        } else {
       	 	if($area == 'gameserver') 
        	{
//        		$femaleplayer = common_stream('gameserver:random100female:' . $this->cur_user->game_server_id,
//					array("Game_server", "getRandom100"), array($this->cur_user->game_server_id, 'F', null, null));
//				$maleplayer = common_stream('gameserver:random100male:' . $this->cur_user->game_server_id,
//					array("Game_server", "getRandom100"), array($this->cur_user->game_server_id, 'M', null, null));
        		$femaleplayer = Game_server::getRandom100($this->cur_user->game_server_id, 'F');
				$maleplayer = Game_server::getRandom100($this->cur_user->game_server_id, 'M');
        	} else if($area == 'game') {
//        		$femaleplayer = common_stream('game:random100female:' . $this->cur_user->game_id,
//					array("Game", "getRandom100"), array($this->cur_user->game_id, 'F', null, null));
//				$maleplayer = common_stream('game:random100male:' . $this->cur_user->game_id,
//					array("Game", "getRandom100"), array($this->cur_user->game_id, 'M', null, null));
				$femaleplayer = Game::getRandom100($this->cur_user->game_id, 'F');
				$maleplayer = Game::getRandom100($this->cur_user->game_id, 'M');
        	} else {
        		//缓存在数据类里已经实现。不需在外部实现。
//        		$femaleplayer = common_stream('webuser:random100female',
//					array("Profile", "getRandom100"), array('F', null, null), 3600*2);
//					
//				$maleplayer = common_stream('webuser:random100male',
//					array("Profile", "getRandom100"), array('M', null, null), 3600*2);
				$femaleplayer = Profile::getRandom100('F');
					
				$maleplayer = Profile::getRandom100('M'); 
        	}
        	
        	$excludeids = Subscription::getSubscribedids($this->cur_user->id);
        	array_push($excludeids, $this->cur_user->id);
        
        	$femaleplayer = array_diff($femaleplayer, $excludeids);
        	
        	$maleplayer = array_diff($maleplayer, $excludeids);
        	
        }
		
        $femaleplayerprofiles = Profile::getProfileByIds(common_random_fetch($femaleplayer,20));
        $maleplayerprofiles = Profile::getProfileByIds(common_random_fetch($maleplayer,20));
        if (common_current_user()) {
	        $game = Game::staticGet('id',$this->cur_user->game_id);
	        $gameserver = Game_server::staticGet('id',$this->cur_user->game_server_id);
	        $gamename = $game->name;
	        $servername = $gameserver->name;
        	$this->addPassVariable('gamename', $gamename);
			$this->addPassVariable('servername',$servername);
        }
        
        $this->addPassVariable('area', $area);
		$this->addPassVariable('femaleplayer',$femaleplayerprofiles);
		$this->addPassVariable('maleplayer',$maleplayerprofiles);
    	$this->displayWith('FunnyPeopleHTMLTemplate'); 
    }
}
