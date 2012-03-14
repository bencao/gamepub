<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Invite people to a group
 *
 * This is the form for inviting people to group
 *
 * @category Group
 */

class GroupInvitationAction extends GroupAdminAction
{
    function handle($args)
    {
        parent::handle($args);
        
        $this->addPassVariable('newgroupok', $this->trimmed('newgroupok', ''));
		$this->displayWith('GroupinvitationHTMLTemplate');
    }
}

