<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class GamegroupsHTMLTemplate extends GroupsbaseHTMLTemplate
{
	var $groups_game_hottest;
	var $total;
	var $groups_game;	//same game or same server
	var $sameserver;
	
    function show($args = array()) {
    	$this->groups_game_hottest = $args['groups_game_hottest'];
    	$this->total = $args['total'];
		$this->groups_game = $args['groups_game'];
		$this->sameserver = $args['sameserver'];
    	parent::show($args);
    }

	function title()
    {
        return "GamePub热门游戏" . GROUP_NAME();
    }

    function showContent()
    {    	
    	$this->elementStart('h2');
    	$this->text('游戏' . GROUP_NAME());
    	$this->element('span', null, '-- GamePub热门游戏' . GROUP_NAME() . '，分享游戏中共同话题与有趣瞬间');
    	$this->elementEnd('h2');
    	
		$this->showGroupSearchForm();
    	
    	$this->elementStart('dl', array('class' => 'grid-3'));
    	$this->element('dt', null, '热门游戏'  . GROUP_NAME());
    	$this->elementStart('dd');
    	$this->showHottestGameGroups();
    	$this->elementEnd('dd');
    	$this->elementEnd('dl'); 
    	
    	$cur_game_name = $this->cur_user->getGame()->name;
    	$cur_server_name = $this->cur_user->getGameServer()->name;
    	$cur_name = $this->sameserver ? $cur_server_name : $cur_game_name;
    	$this->elementStart('dl', array('class' => 'grid-3'));
    	
    	$this->elementStart('dt');
    	$this->text($cur_game_name . GROUP_NAME());
    	if ($this->sameserver)
    		$this->element('input', array('class'=>'checkbox', 'name'=>'sameserver', 'id'=>'sameserver', 'type'=>'checkbox', 'checked'=>'checked'));
    	else 
    		$this->element('input', array('class'=>'checkbox', 'name'=>'sameserver', 'id'=>'sameserver', 'type'=>'checkbox'));
    	$this->element('label', array('class'=>'sameserver', 'for'=>'sameserver'), '只显示' . $cur_server_name . GROUP_NAME());
    	$this->elementEnd('dt');
    	$this->elementStart('dd');
    	$this->showGameGroups($cur_name);   
    	$this->elementEnd('dd');
    	
    	$this->elementEnd('dl'); 
    }
    
    function showHottestGameGroups()
    {
    	if($this->groups_game_hottest && $this->groups_game_hottest->N > 0){
    		$gl = new GroupsList($this->groups_game_hottest, $this);
            $gl->show();
    	} else {
    	 	$this->showEmptyList('平台');
    	}
    }
    
    function showGameGroups($cur_name)
    {
    	if($this->groups_game && $this->groups_game->N > 0){
    		$gl = new GroupsList($this->groups_game, $this);
            $gl->show();
            $this->tu->updownpagination($this->total, 'filtergamegroups', array(), array(), $this->cur_page, GROUPS_PER_PAGE_GAME);	
    	} else {
    	 	$this->showEmptyList($cur_name);
    	}
    }

    function showEmptyList($title)
    {
        $this->tu->showEmptyListBlock($title . '现在还没有任何游戏' . GROUP_NAME() . '，还不快抢创'.$title .'第一游戏' . GROUP_NAME() . '？');
    }
    
	function showScripts() 
	{
		parent::showScripts();
		$this->script('js/lshai_gamegroups.js');
	}
}