<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class Rank2Action extends ShaiAction
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
        $type = $this->trimmed('type');
        $game = Game::staticGet('id',$this->cur_user->game_id);
        $gameserver = Game_server::staticGet('id',$this->cur_user->game_server_id);
        $gamename = $game->name;
        $game_id = $game->id;
        $servername = $gameserver->name;
        $server_id = $gameserver->id;
        
        $cur = time();
		$dt = strftime('%Y-%m-%d', $cur);
		$today = strtotime($dt);

		$time = $today - 3600*24;
		//$time = date('Y-m-d H:i:s', $yesterday);
		
        if($type == 4) {
        	
        	$webdissids = Notice::getDissNoticeOrder(20,null,null,$time);
        	$gamedissids = Notice::getDissNoticeOrder(20,$game_id,null,$time);
         	$serverdissids = Notice::getDissNoticeOrder(20,null,$server_id,$time);
         	
         	$webdissnotices = Notice::getStreamByIds($webdissids);
         	$gamedissnotices = Notice::getStreamByIds($gamedissids);
         	$serverdissnotices = Notice::getStreamByIds($serverdissids);
         	
         	$this->addPassVariable('webdissnotices',$webdissnotices);
         	$this->addPassVariable('gamedissnotices',$gamedissnotices);
         	$this->addPassVariable('serverdissnotices',$serverdissnotices);
        	
        } else {
        	
        	$webretweetids = Notice::getRetweetNoticeOrder(20,null,null,$time);
        	$gameretweetids = Notice::getRetweetNoticeOrder(20,$game_id,null,$time);
         	$serverretweetids = Notice::getRetweetNoticeOrder(20,null,$server_id,$time);
        	
			$webretweetnotices = Notice::getStreamByIds($webretweetids);
			$gameretweetnotices = Notice::getStreamByIds($gameretweetids);
			$serverretweetnotices = Notice::getStreamByIds($serverretweetids);
			
			$this->addPassVariable('webretweetnotices',$webretweetnotices);
			$this->addPassVariable('gameretweetnotices',$gameretweetnotices);
			$this->addPassVariable('serverretweetnotices',$serverretweetnotices);
        }
		
        
        
        $this->addPassVariable('type', $type);
		$this->addPassVariable('gamename', $gamename);
		$this->addPassVariable('servername',$servername);

    	$this->displayWith('Rank2HTMLTemplate');
    }
}
