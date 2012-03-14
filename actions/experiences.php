<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class ExperiencesAction extends ShaiAction
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
        $area = $this->trimmed('area', 'all');
        
        if ($this->cur_user) {
	        if($area == 'gameserver') 
	        {
	        	$exprnotices = Notice::getFirstTaggedNotices(($this->cur_page-1)*NOTICES_PER_PAGE,
	                                       NOTICES_PER_PAGE + 1, 0, 
	    			0, null, 0, 3, 0, $this->cur_user->game_server_id);
	        	
	        } else if($area == 'game') {
	        	$exprnotices = Notice::getFirstTaggedNotices(($this->cur_page-1)*NOTICES_PER_PAGE,
	                                       NOTICES_PER_PAGE + 1, 0, 
	    			0, null, 0, 3, $this->cur_user->game_id,0);
	        	
	        } else {
	        	$exprnotices = Notice::getFirstTaggedNotices(($this->cur_page-1)*NOTICES_PER_PAGE,
	                                       NOTICES_PER_PAGE + 1, 0, 
	    			0, null, 0, 3, 0, 0);
	        }
	        
			$game = Game::staticGet('id',$this->cur_user->game_id);
	        $gameserver = Game_server::staticGet('id',$this->cur_user->game_server_id);
	        $gamename = $game->name;
	        $servername = $gameserver->name;
	        
	        
	        $this->addPassVariable('game_id', $game->id);
	        $this->addPassVariable('server_id', $gameserver->id);
			$this->addPassVariable('gamename', $gamename);
			$this->addPassVariable('servername',$servername);
        } else {
        	$exprnotices = Notice::getFirstTaggedNotices(($this->cur_page-1)*NOTICES_PER_PAGE,
	                                       NOTICES_PER_PAGE + 1, 0, 
	    			0, null, 0, 3, 0, 0);
        }
		
        $this->addPassVariable('area', $area);
		$this->addPassVariable('exprnotices',$exprnotices);
		
		if ($this->boolean('ajax')) {
			if($this->cur_page > 1) {
	    		$exprView = TemplateFactory::get('ExperiencesHTMLTemplate');
				$exprView->showPageNotices($this->paras);     		
	    	}
		} else {
    		$this->displayWith('ExperiencesHTMLTemplate');
		} 

    }
}
