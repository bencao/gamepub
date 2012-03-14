<?php


if (!defined('SHAISHAI')) {
    exit(1);
}

class GamesettingsHTMLTemplate extends SettingsHTMLTemplate 
{
	/**
     * Title of the page
     * 
     * @return string Title of the page
     */
    function title()
    {
        return '修改游戏信息';
    }
    
    function _showGameOrganization() {
    	$this->elementStart('p', 'clearfix');
    	
    	$this->element('label', array('for' => 'game_org', 'class' => 'label60'), GROUP_NAME());
    	
    	$this->element('input', array('class' => 'text200',
    					'id' => 'game_org',
    					'type' => 'text', 
    					'name' => 'game_org',
    					'maxlength' => '255', 
    					'value' => $this->game_org));
    	$this->elementEnd('p');
    }
    
    function _showGameJob() {
    	$this->elementStart('p', 'clearfix');
    	
    	$this->element('label', array('for' => 'game_job', 'class' => 'label60'), JOB_NAME());
    	
    	$this->elementStart('select', 
			array('name' => 'game_job', 
				'id' => 'game_job'));
		$this->option('', '请选择', $this->game_job);
		foreach ($this->game_jobs as $gj) {
			$this->option($gj, $gj, $this->game_job);
		}
        $this->elementEnd('select');
        
    	$this->elementEnd('p');
    }
    
	function _showGame() {
		$this->elementStart('div', 'game clearfix');
    	
    	$this->element('label', array('for' => 'game_big_zone', 'class' => 'label60'), '游戏');
    	
//    	$this->elementStart('select', array('id' => 'game', 'name' => 'game'));
//    	$this->option($this->game->id, $this->game->name, $this->game->id);
//    	$this->elementEnd('select');
//    	
//    	$this->elementStart('select', 
//			array('name' => 'game_big_zone', 
//				'id' => 'game_big_zone'));
//		foreach ($this->game_big_zones as $gbz) {
//			$this->option($gbz['id'], $gbz['name'], $this->game_big_zone->id);
//		}
//        $this->elementEnd('select');
//        
//        $this->elementStart('select', 
//			array('name' => 'game_server', 
//				'id' => 'game_server'));
//		foreach ($this->game_servers as $gs) {
//			$this->option($gs['id'], $gs['name'], $this->game_server->id);
//		}
//		
//        $this->elementEnd('select');

		$this->elementStart('div', array('id' => 'game_select', 'class' => 'droper'));
		if ($this->game) {
			$this->text($this->game->name);
		} else {
			$this->text('请选择');
		}
		$this->elementEnd('div');
		
		$this->elementStart('div', array('id' => 'game_big_zone_select', 'class' => 'droper'));
		if ($this->game_big_zone) {
			$this->text($this->game_big_zone->name);
		} else {
			$this->text('请选择');
		}
		$this->elementEnd('div');
		
		$this->elementStart('div', array('id' => 'game_server_select', 'class' => 'droper'));
		if ($this->game_server) {
			$this->text($this->game_server->name);
		} else {
			$this->text('请选择');
		}
		$this->elementEnd('div');
        
		$this->element('input', array('type' => 'hidden', 'name' => 'game', 'id' => 'game', 'value' => $this->game ? $this->game->id : ''));
		$this->element('input', array('type' => 'hidden', 'name' => 'game_big_zone', 'id' => 'game_big_zone', 'value' => $this->game_big_zone ? $this->game_big_zone->id : ''));
		$this->element('input', array('type' => 'hidden', 'name' => 'game_server', 'id' => 'game_server', 'value' => $this->game_server ? $this->game_server->id : ''));
        
        $this->elementStart('div', array('id' => 'game_more', 'class' => 'more'));
        $this->elementStart('div', 'filter clearfix');
        $this->element('a', array('class' => 'hots', 'href' => '#'), '热门游戏');
        $this->element('a', array('class' => 'A', 'href' => '#'), 'A');
        $this->element('a', array('class' => 'B', 'href' => '#'), 'B');
        $this->element('a', array('class' => 'C', 'href' => '#'), 'C');
        $this->element('a', array('class' => 'D', 'href' => '#'), 'D');
        $this->element('a', array('class' => 'E', 'href' => '#'), 'E');
        $this->element('a', array('class' => 'F', 'href' => '#'), 'F');
        $this->element('a', array('class' => 'G', 'href' => '#'), 'G');
        $this->element('a', array('class' => 'H', 'href' => '#'), 'H');
        $this->element('a', array('class' => 'I', 'href' => '#'), 'I');
        $this->element('a', array('class' => 'J', 'href' => '#'), 'J');
        $this->element('a', array('class' => 'K', 'href' => '#'), 'K');
        $this->element('a', array('class' => 'L', 'href' => '#'), 'L');
        $this->element('a', array('class' => 'M', 'href' => '#'), 'M');
        $this->element('a', array('class' => 'N', 'href' => '#'), 'N');
        $this->element('a', array('class' => 'O', 'href' => '#'), 'O');
        $this->element('a', array('class' => 'P', 'href' => '#'), 'P');
        $this->element('a', array('class' => 'Q', 'href' => '#'), 'Q');
        $this->element('a', array('class' => 'R', 'href' => '#'), 'R');
        $this->element('a', array('class' => 'S', 'href' => '#'), 'S');
        $this->element('a', array('class' => 'T', 'href' => '#'), 'T');
        $this->element('a', array('class' => 'U', 'href' => '#'), 'U');
        $this->element('a', array('class' => 'V', 'href' => '#'), 'V');
        $this->element('a', array('class' => 'W', 'href' => '#'), 'W');
        $this->element('a', array('class' => 'X', 'href' => '#'), 'X');
        $this->element('a', array('class' => 'Y', 'href' => '#'), 'Y');
        $this->element('a', array('class' => 'Z', 'href' => '#'), 'Z');
        $this->elementEnd('div');
        $this->elementStart('ul', 'clearfix');
        
		$games = $this->arg('hotgames');
		foreach ($games as $g) {
			$this->elementStart('li', 'hot');
        	$this->element('a', array('href' => '#', 'gid' => $g['id']), $g['name']);
        	$this->elementEnd('li');
		}
        
        $this->elementEnd('ul');
        $this->elementEnd('div');
        
        $this->elementStart('div', array('id' => 'big_zone_more', 'class' => 'more'));
        $this->elementStart('ul', 'clearfix');
        if ($this->game_big_zones) {
        	foreach ($this->game_big_zones as $gb) {
	        	$this->elementStart('li');
	        	$this->element('a', array('href' => '#', 'bzid' => $gb['id']), $gb['name']);
	        	$this->elementEnd('li');
        	}
        }
        $this->elementEnd('ul');
        $this->elementEnd('div');
        
        $this->elementStart('div', array('id' => 'server_more', 'class' => 'more'));
        $this->elementStart('ul', 'clearfix');
        if ($this->game_servers) {
        	foreach ($this->game_servers as $gs) {
	        	$this->elementStart('li');
	        	$this->element('a', array('href' => '#', 'sid' => $gs['id']), $gs['name']);
	        	$this->elementEnd('li');
        	}
        }
        $this->elementEnd('ul');
        $this->elementEnd('div');
        
    	$this->elementEnd('div');
    }
    
    function _showSubmit() {
    	$this->element('input', array('type' => 'submit', 'class' => 'submit button76 green76', 'value' => '保存'));
    }
    
    function showSettingsTitle() {
    	return '游戏设置';
    }
    
    function showSettingsInstruction() {
    	return '为让好友们更容易的找到您，请更新真实的个人游戏信息。';
    }
    
    function showSettingsContent() {
        
    	$this->tu->startFormBlock(array('method' => 'post',
                                           'id' => 'form_settings_game',
                                           'class' => 'settings',
                                           'action' => common_local_url('gamesettings')), '修改游戏信息');
    	
    	$this->elementStart('dl');
    	
    	$this->element('dt', null, ' ');
    	
    	$this->elementStart('dd');
    	
    	$this->_showGame();
    	
    	$this->_showGameJob();
    	
    	$this->_showGameOrganization();
    	
    	$this->elementEnd('dd');
    	
    	$this->elementEnd('dl');
    	
    	$this->elementStart('div', 'op');
    	
    	$this->_showSubmit();
    	
    	$this->elementEnd('div');
    	
    	$this->tu->endFormBlock();
    	
    }
    
    function showScripts() {
    	parent::showScripts();
    	
//		$this->script('js/jquery.validate.min.js');
		$this->script('js/lshai_gamechoose.js');
    	
    }
    
	function show($args)
    {
    	$this->game = $args['game'];
    	$this->game_big_zone = $args['game_big_zone'];
    	$this->game_server = $args['game_server'];
    	$this->game_job = $args['game_job'];
    	$this->game_org = $args['game_org'];
		$this->game_big_zones = $args['game_big_zones'];
    	$this->game_servers = $args['game_servers'];
    	$this->game_jobs = $args['game_jobs'];
    	
    	parent::show($args);
    }
}

?>