<?php
/**
 * Shaishai, the distributed microblog
 *
 * Show all web links of the game
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

class GamewebnavAction extends GamebasicAction
{
    function isReadOnly($args)
    {
        return true;
    }

    function handle($args)
    {
        parent::handle($args);
        
		$this->addPassVariable('subs', Game_web::getGameWebs($this->cur_game->id));
		
		$this->displayWith('GamewebnavHTMLTemplate');
    }
}