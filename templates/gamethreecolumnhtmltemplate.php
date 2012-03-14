<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class GamethreecolumnHTMLTemplate extends GameBaseHTMLTemplate
{
	
	function showScripts() {
    	parent::showScripts();
	    $this->script('js/lshai_relation.js');
	    $this->script('js/lshai_tagcloud_min.js');
	    $this->script('js/lshai_search.js');
    }
    
	function showCore() {
		$this->showLeftNav();
		
		$this->elementStart('div', array('id' => 'public_contents', 'style' => 'width: 519px; border-right: 1px solid rgb(176, 176, 176);'));
		$this->showContent();
		$this->elementEnd('div');
		
    	$this->elementStart('div', array('id' => 'public_widgets'));
		$this->showRightside();
		$this->elementEnd('div');
	}
	
	function showContent() {
		
	}

	function showRightside() {
		$this->showGameBoardWidget($this->cur_user,$this->cur_game);
		
		$this->showSearchFormWidget();
    	
		$this->showGameQAndAWidget($this->cur_game, $this->game_questions);
		
    	$this->showGroupListWidget($this->game_hot_groups, '热门公会',common_path('groups/game'));
    	
    	$this->tu->showTagcloudWidget();
	}
	
	function showSearchFormWidget() {
        $this->elementStart('dl', 'widget search');
		$this->element('dt', null, '搜索您感兴趣的文字');
		
		$this->elementStart('dd', 'clearfix');	
		$this->tu->startFormBlock(array('action' => common_path('search/notice')));
		$this->element('input', array('class' => 'text', 'type' => 'text', 'name' => 'q'));
		$this->element('input', array('class' => 'submit button60 green60', 'type' => 'submit', 'value' => '搜索'));
		$this->tu->endFormBlock();
        $this->elementEnd('dd');
		$this->elementEnd('dl');
		
    }
    
	function showGameQAndAWidget($game, $questions) {
		$this->elementStart('dl', 'widget questions');
		$this->elementStart('dt');
		$this->text($game->name.'问答');
		$this->element('a', array('href' => common_path('wenda'),'class'=>'toggle'), '更多');
		$this->elementEnd('dt');
		$this->elementStart('dd');
		$this->elementStart('ul', 'clearfix');
		
		while ($questions && $questions->fetch()) {
			$this->elementStart('li');
			$qtitle = $questions->title;
			if(strlen($qtitle) > 33){
				// XXX: 如果全是字母或数字，会超过边界，但为了显示尽可能多的汉字所以取33
				$qtitle = common_cut_string($qtitle, 30);
    			$qtitle .= '...';
			}
			$this->element('a', array('href' => common_path('wenda/' . $questions->id)), $qtitle);
    		$this->element('span', null, $questions->answer_count . '个回答');
			$this->elementEnd('li');
		}
		
		$this->elementEnd('ul');
		
		$this->elementStart('div', 'op clearfix');
		$this->element('a', array('href' => common_path('wenda/new')), '我要提问');
		$this->element('a', array('href' => common_path('wenda')), '我要回答');
		$this->elementEnd('div');
		$this->elementEnd('dd');
		$this->elementEnd('dl');	
	}
	
	function showGroupListWidget($group, $title="", $url="#", $id=false) {
    	$this->elementStart('dl', 'widget groups');
		$this->elementStart('dt');
		$this->text($title);
		if ($url != '#') {
			$this->element('a', array('class' => 'toggle', 'href' => $url), '更多');
		}
		$this->elementEnd('dt');
		
		$this->elementStart('dd');		
		$this->elementStart('ul');
    	$cnt = 0;
		while ($group->fetch()){
			$cnt ++;
	
			$this->elementStart('li');
			$this->elementStart('div', 'avatar');

			$this->elementStart('a', array('href' => common_path('group/' . $group->id)));
        	$this->element('img', 
        						array('height' => '48', 'width' => '48',
		    						'src' => $group->stream_logo ? $group->stream_logo : User_group::defaultLogo(GROUP_LOGO_STREAM_SIZE), 
		    						'title' => '访问' . $group->nickname . GROUP_NAME(). '主页'),
        						null); 
        	$this->elementEnd('a');   
        	$this->elementEnd('div');  
			
        	$this->elementStart('p', 'nickname');
        	$this->element('a', 
        						array('href' => common_path('group/' . $group->id),
        							'title' => '访问'.$group->nickname.GROUP_NAME().'主页'),
        						$group->nickname);
        	$this->elementEnd('p');  
        	
        	$game = Game::staticGet('id', $group->game_id);
        	$game_server = Game_server::staticGet('id', $group->game_server_id);
        	$game_big_zone = Game_big_zone::staticGet('id',$game_server->game_big_zone_id);

        	$this->element('p', null, $game_big_zone->name. $game_server->name);
        	$this->element('p', null, $group->description);
    	
			$this->elementEnd('li');
			
		}
		
    	$this->elementEnd('ul');
    	$this->elementEnd('dd');
		$this->elementEnd('dl');
    }
}
?>