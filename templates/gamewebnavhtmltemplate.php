<?php
/**
 * Shaishai, the distributed microblog
 *
 * Show all web links of a game
 *
 * PHP version 5
 *
 * @category  Game
 * @package   Shaishai
 * @author    AGun Chan <agunchan@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

class GamewebnavHTMLTemplate extends GamethreecolumnHTMLTemplate
{
    var $gameweb;
    
	function title()
    {
    	return $this->cur_game->name . '游戏资源';
    }
    
    function show($args) 
    {
    	$this->gameweb = $args['subs'];
		parent::show($args);
    }
    
    function showContent() 
    {
    	$this->elementStart('h2');
    	$this->text('游戏资源');
    	$this->elementStart('span');
    	$this->text('-- ' . $this->cur_game->name . '常用站点导航');
    	$this->elementEnd('span');
    	$this->elementEnd('h2');
    	
    	$this->elementStart('ul', 'links');
    	$this->elementStart('li', 'hd clearfix');
    	$this->elementStart('span', 'name');
    	$this->text('常用站点');
    	$this->elementEnd('span');
    	$this->elementStart('span', 'desc');
    	$this->text('描述');
    	$this->elementEnd('span');
    	$this->elementEnd('li');
    	while ($this->gameweb->fetch()) {
    		$this->elementStart('li', 'clearfix');
			$this->elementStart('span', 'name');
    		$this->element('a', array('class' => 'webnav', 'target' => '_blank', 'href' => $this->gameweb->website, 'gwid' => $this->gameweb->id), $this->gameweb->name);
    		$this->elementEnd('span');
    		$this->elementStart('span', 'desc');
    		$this->text($this->gameweb->detail);
    		$this->elementEnd('span');
    		$this->elementEnd('li');
    	}
		$this->elementEnd('ul');
		
    	$this->elementStart('div', 'instruction');
    	$this->elementStart('p');
    	$this->text('以上是' . $this->cur_game->name . '玩家常用的网址导航。如果您还知道其它非常有价值的网址，可以');
    	$styclass = 'webapply';
    	if (! $this->cur_user) {
    		$styclass = 'trylogin';
    	}
    	$this->element('a', array('class' => $styclass, 'href' => common_path('game/' . $this->cur_game->id . '/webnew'), 'gname' => $this->cur_game->name), '点击这里');
    	$this->text('把您的信息提供给我们，如被采用，管理员会赠送1银币作为回报。');
    	$this->elementEnd('p');
    	$this->elementEnd('div');
    	
//    	$this->numpagination($this->total, 'gamewebnav', array('gameid' => $this->game_id), array(), GAMEWEB_PER_PAGE);
    }
    
	function showScripts() {
    	parent::showScripts();
 
    	$this->script('js/lshai_gamewebnav.js');
    }
}
