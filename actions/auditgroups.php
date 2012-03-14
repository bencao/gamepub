<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Audit groups
 *
 * Show the audit groups on the site
 *
 * @category Personal
 * @package  ShaiShai
 * @author   Andray Ma <andray09@gmail.com>
 * @author	 AGun Chan <agunchan@gmail.com>
 * @link     http://www.lshai.com
 */

class AuditgroupsAction extends ShaiAction
{
    function isReadOnly($args)
    {
        return true;
    }

    function handle($args)
    {
    	$newgroupok = '';
    	if($this->trimmed('newgroupok')){
    		$newgroupok = $this->trimmed('newgroupok');
    	}
        parent::handle($args);                
        $offset = ($this->cur_page -1) * GROUPS_PER_PAGE;
        $limit =  GROUPS_PER_PAGE + 1;

        $groups_audit = User_group::getAuditGroups($this->cur_user->id, $offset, $limit);
        $groups_apply = User_group::getApplyGroups($this->cur_user->id, $offset, $limit);
        
        $this->addPassVariable('groups_audit', $groups_audit);
        $this->addPassVariable('groups_apply', $groups_apply);
        $this->addPassVariable('newgroupok', $newgroupok);
        
        $this->displayWith('AuditgroupsHTMLTemplate');
    }
}
