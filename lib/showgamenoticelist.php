<?php

class ShowgameNoticeList extends NoticeList
{
    function newListItem($notice)
    {
        return new ShowgameNoticeListItem($notice, $this->out);
    }
}

class ShowgameNoticeListItem extends NoticeListItem
{
    function showNickname() {
    	$this->out->elementStart('h3'); 
        $this->out->element('a', array('href' => common_path($this->profile->uname),
        			'class' => 'name', 'title' => '去' . $this->profile->nickname . '在' . common_config('site', 'name') . '的主页看看'), $this->profile->nickname);
        if ($this->profile->is_vip) {
        	$this->out->element('strong', null, 'V');
        }
        
        $game_server = Game_server::staticGet('id', $this->profile->game_server_id);   
        $game = Game::staticGet('id', $this->profile->game_id);
    	$text = sprintf('<span class="tag"><a rel="tag" target="_blank" title="去看看%s的最新动态" href="%s">%s</a>- %s</span>',
    		$game->name, common_path('game/' . $game->id), $game->name, $game_server->name);
    				
    	$this->out->raw($text);
    	
        $this->out->elementEnd('h3');
    }
}
