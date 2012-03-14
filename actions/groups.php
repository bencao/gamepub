<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * My groups
 *
 * Show my groups on the site
 *
 * @category Personal
 * @package  ShaiShai
 * @author   Andray Ma <andray09@gmail.com>
 * @author	 AGun Chan <agunchan@gmail.com>
 * @link     http://www.lshai.com
 */

class GroupsAction extends ShaiAction
{
    function isReadOnly($args)
    {
        return true;
    }
    
    function handle($args)
    {
        parent::handle($args);                
        $offset = ($this->cur_page -1) * GROUPS_PER_PAGE;
        $limit =  GROUPS_PER_PAGE + 1;

        $groups_game = User_group::getUserGameGroups($this->cur_user->id);
        $groups_life = User_group::getUserLifeGroups($this->cur_user->id);
        
        $this->addPassVariable('groups_game', $groups_game);
        $this->addPassVariable('groups_life', $groups_life);
        
        $this->displayWith('GroupsHTMLTemplate');
    }
}
