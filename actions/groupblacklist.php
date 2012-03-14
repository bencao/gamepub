<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * List of profiles blocked from this group
 *
 * @category Group
 * @package  ShaiShai
 * @author   Andray Ma <andray09@gmail.com>
 */

class GroupblacklistAction extends GroupAdminAction
{
	
    function handle($args)
    {
        parent::handle($args);
        
        $offset = ($this->cur_page - 1) * PROFILES_PER_PAGE;
        $limit =  PROFILES_PER_PAGE + 1;

        $blocked = $this->cur_group->getBlocked($offset, $limit);
        
        $total = $this->cur_group->getBlockedCount();
        
        $this->addPassVariable('blocked', $blocked);
        
        $this->addPassVariable('total', $total);
        
        $this->displayWith('GroupblacklistHTMLTemplate');
    }

}
