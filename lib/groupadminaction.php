<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Base class for administrative actions
 *
 * Pages related to groups can be themed with a design.
 * 
 * @category Action
 *
 */
class GroupAdminAction extends GroupDesignAction 
{
    function prepare($args)
    {
        if (! parent::prepare($args)) {return false;}

        if (! $this->is_group_admin) {
            $this->clientError('只有该' . GROUP_NAME() . '管理员才有权限进行此操作！', 403);
            return false;
        }

        return true;
    }
}
