<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class GamedealAction extends GamebasicAction
{
	function __construct() {
		parent::__construct();
		$this->cache_allowed = false;
		$this->no_anonymous = true;
	}
	
    function prepare($args)
    {
        if (! parent::prepare($args)) {
        	return false;
        }
        //判断是否只显示自己的交易信息
        $this->show_self = $this->trimmed('show_self', false);
        return true;
    }

    function handle($args)
    {
        parent::handle($args);
        
        $game_server_id = $this->trimmed('game_server', false);
        $game_id = $this->trimmed('game', $this->cur_game->id);
        $game_big_zone_id = $this->trimmed('game_big_zone', false);
        if ($game_server_id) {
        	$gameserver = Game_server::staticGet('id', $game_server_id);
        } else {
        	$gameserver = false;
        }
        if ($game_big_zone_id) {
        	$gamebigzone = Game_big_zone::staticGet('id', $game_big_zone_id);
        } else {
        	$gamebigzone = false;
        }
        $game = Game::staticGet('id', $game_id);
        
        $list_games = Game::listAll();
        $list_bigzones = $game->getBigZones();
        if ($gamebigzone) {
        	$list_servers = Game::getServers($gamebigzone->id);
        } else {
        	$list_servers = array();
        }
        $list_dealtags = Deal_tag::getDealTagsByGameid($game->id);
        
        $q = $this->trimmed('q', '');
        $deal_tag = $this->trimmed('deal_tag');
        $sortstyle = $this->trimmed('sortstyle', 'datedesc');
        $deal_per_page = $this->trimmed('deal_per_page', NOTICES_PER_PAGE);
        $lowprice = $this->trimmed('lowprice', 0);
        $highprice = $this->trimmed('highprice', 0);
        $state = $this->trimmed('state', 1);
      	
      	if ($this->show_self) {
      		$total = Deal::getDealsNumofUser($this->cur_user->id,$lowprice,$highprice,$state);
        	$deals = Deal::getDealsbyUser(
        				$this->cur_user->id,
        				$lowprice,
        				$highprice,
        				$sortstyle,
        				($this->cur_page - 1) * $deal_per_page,
        				$deal_per_page,
        				$state
        			 );
      	} else {
      		$total = Deal::getDealsNum(
      					$game->id, 
      					$game_big_zone_id, 
      					$game_server_id, 
      					$deal_tag, 
      					$q,
      					array('lowprice' => $lowprice,
      						'highprice' => $highprice,
      						'state' => $state)
      				 );
        	$deals = Deal::getDeals(
        				$game->id,
        				$game_big_zone_id,
        				$game_server_id,
        				$deal_tag,
        				$q,
        				array('sortstyle' => $sortstyle,
        					'lowprice' => $lowprice,
        					'highprice' => $highprice,
        					'offset' => ($this->cur_page - 1) * $deal_per_page,
        					'limit' => $deal_per_page,
        					'state' => $state)
        			 );
      	}
      	
        $this->addPassVariable('list_games', $list_games);
        $this->addPassVariable('list_bigzones', $list_bigzones);
        $this->addPassVariable('list_servers', $list_servers);
        $this->addPassVariable('list_dealtags', $list_dealtags);
        
        $this->addPassVariable('gameserver',$gameserver);
        $this->addPassVariable('gamebigzone',$gamebigzone);
        $this->addPassVariable('game',$game);
        
        $this->addPassVariable('total',$total);
        $this->addPassVariable('deals',$deals);
        
		$this->displayWith('GameDealHTMLTemplate');
	}
}