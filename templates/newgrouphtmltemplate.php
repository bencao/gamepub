<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class NewgroupHTMLTemplate extends GroupsbaseHTMLTemplate
{
    function title()
    {
        return '新建' . GROUP_NAME();
    }
	
    function showContent()
    {    	
		$this->elementStart('h2');
    	$this->text('我的' . GROUP_NAME());
    	$this->element('span', null, ' -- 创建'. GROUP_NAME());
        $this->elementEnd('h2');
    	
    	$this->elementStart('div', 'grouptypes');
    	
    	$this->elementStart('div', 'gametype');
    	$this->element('p', 'title', '游戏' . GROUP_NAME());
    	$this->elementStart('p');
    	$this->element('strong', null, '需求等级：');
    	$this->text('二级');
    	$this->elementEnd('p');
    	$this->elementStart('p');
    	$this->element('strong', null, '描述：');
    	$this->text('成员间以讨论游戏为主，交流游戏相关内容，一般直接与游戏中的' . GROUP_NAME() . '对应。为保证游戏中' . GROUP_NAME() . '的名称不被恶意抢注，需要2个人一起共同创建。');
    	$this->elementEnd('p');
    	$this->elementStart('p');
    	$this->element('strong', null, '当前情况：');
    	$this->text($this->arg('game_msg'));
    	$this->elementEnd('p');
    	if ($this->arg('gamegroup')) {
    		$this->element('a', array('href' => common_local_url('newgamegroup'), 'class' => 'button76 green76'), '创建');
    	}
    	$this->elementEnd('div');
    	
    	$this->elementStart('div', 'lifetype');
    	$this->element('p', 'title', '生活' . GROUP_NAME());
    	$this->elementStart('p');
    	$this->element('strong', null, '需求等级：');
    	$this->text('二级');
    	$this->elementEnd('p');
    	$this->elementStart('p');
    	$this->element('strong', null, '描述：');
    	$this->text('成员间以讨论生活中的话题为主，展示游友们的另一面。生活群的话题比较随意，一人即可成功创建。');
    	$this->elementEnd('p');
    	$this->elementStart('p');
    	$this->element('strong', null, '当前情况：');
    	$this->text($this->arg('life_msg'));
    	$this->elementEnd('p');
    	if ($this->arg('lifegroup')) {
    		$this->element('a', array('href' => common_local_url('newlifegroup'), 'class' => 'button76 green76'), '创建');
    	}
    	$this->elementEnd('div');
    	
    	$this->elementEnd('div');
    }
    

	function showStyleSheets() {
    	parent::showStyleSheets();
    	$this->cssLink('css/settings.css','default','screen, projection');
    }
    
    function showScripts() {
		parent::showScripts();
		$this->script('js/jquery.validate.min.js');
		$this->script('js/lshai_categoryselect.js');
		$this->script('js/lshai_newgroup.js');
	}
    
}
