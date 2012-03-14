<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class GroupsHTMLTemplate extends GroupsbaseHTMLTemplate
{
	var $groups_game;
	var $groups_life;
	
    function show($args = array()) {
		$this->groups_game = $args['groups_game'];
		$this->groups_life = $args['groups_life'];
    	parent::show($args);
    }
    
	function title()
    {
        return "我加入的GamePub" . GROUP_NAME();
    }

    function showContent()
    {    	
    	$this->elementStart('h2');
    	$this->text('我的' . GROUP_NAME());
    	$this->element('span', null, '-- 您已经加入的' . GROUP_NAME() . '，和您的圈子分享游戏的乐趣和生活的瞬间');
    	$this->elementEnd('h2');

    	$this->showGroupSearchForm();
    	
    	
    	$this->elementStart('div', array('class' => 'public_op clearfix'));
    	$this->element('a', array('id'=>'group_create', 'class' => 'create button94 green94', 'href' => common_local_url('newgroup')), '创建'. GROUP_NAME());
    	$this->element('span', null, '创建新'. GROUP_NAME(). '，与游友们建立自己的地盘。');
    	$this->element('a', array('class' => 'help', 'href' => common_local_url('doc', array('type' => 'help', 'title' => 'groups'))), '前提条件');
    	$this->elementEnd('div');
    	
    	$this->elementStart('dl', array('class' => 'grid-3'));
    	$this->element('dt', null, '我加入的游戏'  . GROUP_NAME());
    	$this->elementStart('dd');
    	$this->showGameGroups();
    	$this->elementEnd('dd');
    	$this->elementEnd('dl');    	
    	
    	$this->elementStart('dl', array('class' => 'grid-3'));
    	$this->element('dt', null, '我加入的生活' . GROUP_NAME());
    	$this->elementStart('dd');
    	
    	$this->showLifeGroups();    	
    	$this->elementEnd('dd');
    	$this->elementEnd('dl');
    	
    
    }
    
    function showGameGroups()
    {
    	if ($this->groups_game && $this->groups_game->N > 0) {
    		$gl = new GroupsList($this->groups_game, $this);
            $cnt = $gl->show();
    	} else {
    	 	$this->showEmptyList('游戏');
    	}
    }
    
    function showLifeGroups()
    {
    	if($this->groups_life && $this->groups_life->N > 0) {    	
    		$gl = new GroupsList($this->groups_life, $this);
            $cnt = $gl->show();
    	} else {
    	 	$this->showEmptyList('生活');
    	}
    }
    
    function showEmptyList($title)
    {
    	$this->tu->showEmptyListBlock('您还没有加入任何'. $title . GROUP_NAME() . '，点击左侧游戏' . GROUP_NAME() . '加入平台中您喜欢的' . $title . GROUP_NAME().'。');
    }
}