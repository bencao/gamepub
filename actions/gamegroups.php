<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Hottest game groups
 *
 * Show the hottest game groups on the site
 *
 * @category Personal
 * @package  ShaiShai
 * @author   Andray Ma <andray09@gmail.com>
 * @author   AGun Chan <agunchan@gmail.com>
 * @link     http://www.lshai.com
 */

class GamegroupsAction extends ShaiAction
{
	var $sameserver;
	
	function prepare($args)
	{
        parent::prepare($args);
		$this->sameserver = $this->trimmed('sameserver', false);
        return true;
	}

    function isReadOnly($args)
    {
        return true;
    }

    function handle($args)
    {
        parent::handle($args);      
           
        $offset = ($this->cur_page - 1) * GROUPS_PER_PAGE_GAME;
        $limit =  GROUPS_PER_PAGE_GAME;             

        $groups_game_hottest = User_group::getGameGroupsHottest(0, GROUPS_HOTTEST);
        if ($this->sameserver) {
        	$total = User_group::getGameGroupsByServerCount($this->cur_user->game_server_id);
        	$groups_game = User_group::getGameGroupsByServer($this->cur_user->game_server_id, $offset, $limit);
        } else {
        	$total = User_group::getGameGroupsByGameCount($this->cur_user->game_id);
        	$groups_game = User_group::getGameGroupsByGame($this->cur_user->game_id, $offset, $limit);
        }

        $this->addPassVariable('groups_game_hottest', $groups_game_hottest);
        $this->addPassVariable('total', $total);
        $this->addPassVariable('groups_game', $groups_game);
        $this->addPassVariable('sameserver', $this->sameserver);

        $this->displayWith('GamegroupsHTMLTemplate');
    }
}
