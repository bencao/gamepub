<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class RankAction extends ShaiAction
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
        $this->addPassVariable('type', $type);
        
        if(common_current_user())
        {
       		$game = Game::staticGet('id',$this->cur_user->game_id);
        	$gameserver = Game_server::staticGet('id',$this->cur_user->game_server_id);
        	$gamename = $game->name;
        	$game_id = $game->id;
        	$servername = $gameserver->name;
        	$server_id = $gameserver->id;

       	 	$this->addPassVariable('game_id', $game_id);
			$this->addPassVariable('server_id',$server_id);
			$this->addPassVariable('gamename', $gamename);
			$this->addPassVariable('servername',$servername);
        }
		
        $cur = time();
		$dt = strftime('%Y-%m-%d', $cur);
		$today = strtotime($dt);

		$time = $today - 3600*24;
		//$time = date('Y-m-d H:i:s', $yesterday);
		
        if($type == 'discuss') {
        	//暂时去掉时间限制，取出的消息不进行时间限定
        	//$webdissids = Notice::getDissNoticeOrder(20,null,null,$time);
        	$webdissids = Notice::getDissNoticeOrder(20,null,null,null);
         	$webdissnotices = Notice::getStreamByIds($webdissids);     	
         	$this->addPassVariable('webdissnotices',$webdissnotices);
         	
         	if(common_current_user()) {
         		//去除时间限定
         		$gamedissids = Notice::getDissNoticeOrder(20,$game_id,null,null);//$time);
         		$serverdissids = Notice::getDissNoticeOrder(20,null,$server_id,null);//$time);
         		$gamedissnotices = Notice::getStreamByIds($gamedissids);
         		$serverdissnotices = Notice::getStreamByIds($serverdissids);
         		$this->addPassVariable('gamedissnotices',$gamedissnotices);
         		$this->addPassVariable('serverdissnotices',$serverdissnotices);
         	}
         	
         	$this->displayWith('Rank2HTMLTemplate');
      		
        } else if($type == 'retweet') {
        	//去除时间限定
        	$webretweetids = Notice::getRetweetNoticeOrder(20,null,null,$time);    	
			$webretweetnotices = Notice::getStreamByIds($webretweetids);			
			$this->addPassVariable('webretweetnotices',$webretweetnotices);
			
			if(common_current_user()) {
				//去除时间限定
        	$gameretweetids = Notice::getRetweetNoticeOrder(20,$game_id,null,null);//$time);
         	$serverretweetids = Notice::getRetweetNoticeOrder(20,null,$server_id,null);//$time);
			$gameretweetnotices = Notice::getStreamByIds($gameretweetids);
			$serverretweetnotices = Notice::getStreamByIds($serverretweetids);
			$this->addPassVariable('gameretweetnotices',$gameretweetnotices);
			$this->addPassVariable('serverretweetnotices',$serverretweetnotices);
			}
			$this->displayWith('Rank2HTMLTemplate');
      		
        } else if($type == 'user') {
      		$this->displayWith('RankHTMLTemplate'); 
        } else {
        	
        	$allgametoplist = Game_stat::getTopGameidbyntype(15, 1);
					
			$videogametoplist = Game_stat::getTopGameidbyntype(15, 3);
			
			$picgametoplist = Game_stat::getTopGameidbyntype(15, 4);
			
			$musicgametoplist = Game_stat::getTopGameidbyntype(15, 2);
			
			$this->addPassVariable('allgametoplist', $allgametoplist);
			$this->addPassVariable('videogametoplist', $videogametoplist);
			$this->addPassVariable('picgametoplist', $picgametoplist);
			$this->addPassVariable('musicgametoplist', $musicgametoplist);
			$this->displayWith('RankHTMLTemplate'); 
        }
       
    	
    }
}
