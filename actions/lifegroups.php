<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Hottest life groups
 *
 * Show the hottest life groups on the site
 *
 * @category Personal
 * @package  ShaiShai
 * @author   Andray Ma <andray09@gmail.com>
 * @author	 AGun Chan <agunchan@gmail.com>
 * @link     http://www.lshai.com
 */

class LifegroupsAction extends ShaiAction
{
    function isReadOnly($args)
    {
        return true;
    }

    function handle($args)
    {
        parent::handle($args);                
        $offset = ($this->cur_page - 1) * GROUPS_PER_PAGE;
        $limit =  GROUPS_PER_PAGE;
        
        $total = User_group::getLifeGroupsCount();
        $groups_life = User_group::getLifeGroups($offset, $limit);
        
        $this->addPassVariable('total', $total);
        $this->addPassVariable('groups_life', $groups_life);
        $this->displayWith('LifegroupsHTMLTemplate');
    }
}
