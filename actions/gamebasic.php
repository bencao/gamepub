<?php
/**
 * Shaishai, the distributed microblog
 *
 * Game Basic
 *
 * PHP version 5
 *
 * @category  Game
 * @package   Shaishai
 * @author    agunchan <agunchan@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

if (!defined('SHAISHAI')) {
    exit(1);
}


class GamebasicAction extends ShaiAction
{ 
	var $cur_game;
	
	function __construct() {
		parent::__construct();
		$this->no_anonymous = false;
	}
    
    function prepare($args)
    {
    	if (! parent::prepare($args)) {return false;}
    	
    	if($this->trimmed('gameid') && !is_numeric($this->trimmed('gameid'))) {
            $this->clientError('您不能访问此链接.', 403);
            return;
        }
		
        $game_id = $this->trimmed('gameid');
    	$this->cur_game = Game::staticGet('id', $game_id);
		if(!$this->cur_game) {
			$this->clientError('目前没有此游戏.', 403);
	        return false;
		}
		
		return true;
    }
    
    function handle($args) {
    	parent::handle($args);
    	
    	$game_hot_groups = User_group::getGameGroupsByGameHottest($this->cur_game->id, 0, 5);
    	$game_questions = Question::getQuestionByGame($this->cur_game, 0, 5);
    	
    	$this->addPassVariable('cur_game', $this->cur_game);	
    	$this->addPassVariable('hotgroups', $game_hot_groups);
    	$this->addPassVariable('questions', $game_questions);
    }
}