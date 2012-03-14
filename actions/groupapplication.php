<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 *
 * Process application page
 *
 * @category Group
 * 
 */
class GroupapplicationAction extends GroupAdminAction
{

    function handle($args)
    {
        parent::handle($args);
        
        $offset = ($this->cur_page - 1) * PROFILES_PER_PAGE;
        $limit =  PROFILES_PER_PAGE;
       
        $this->addPassVariable('subs', $this->cur_group->getApplicants($offset, $limit));
        $this->addPassVariable('total', $this->cur_group->getApplicantsCount());
        $this->displayWith('GroupapplicationHTMLTemplate');
    }
}

