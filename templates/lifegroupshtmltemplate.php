<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class LifegroupsHTMLTemplate extends GroupsbaseHTMLTemplate
{
	var $total;
	var $groups_life;	//to fetch
	
    function show($args = array()) {
    	$this->total = $args['total'];
		$this->groups_life = $args['groups_life'];
    	parent::show($args);
    }

	function title()
    {
        return "GamePub热门生活" . GROUP_NAME();
    }

    function showContent()
    {    	
    	$this->elementStart('h2');
    	$this->text('生活' . GROUP_NAME());
    	$this->element('span', null, '-- GamePub热门的生活' . GROUP_NAME() . '，分享生活中共同话题与有趣瞬间');
    	$this->elementEnd('h2');
    		
    	$this->showGroupSearchForm();
    	
    	$this->elementStart('dl', array('class' => 'grid-3'));
    	$this->element('dt', null, '热门生活' . GROUP_NAME());
    	$this->elementStart('dd');
    	$this->showLifeGroups();	
    	$this->elementEnd('dd');
    	$this->elementEnd('dl');
    }
    
    function showLifeGroups()
    {
    	if($this->groups_life && $this->groups_life->N > 0) {
    		$gl = new GroupsList($this->groups_life, $this);
    		$gl->show();
    		$this->tu->updownpagination($this->total, 'lifegroups', array(), array(), $this->cur_page, GROUPS_PER_PAGE);
    	} else {
    		$this->showEmptyList();
    	}
    }
    
    function showEmptyList()
    {
    	$this->tu->showEmptyListBlock('平台还没有任何生活' . GROUP_NAME() . '，还不快抢创GamePub第一生活' . GROUP_NAME() . '？');
    }
}