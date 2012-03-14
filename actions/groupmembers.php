<?php

if (!defined('SHAISHAI')) {
    exit(1);
}


/**
 * List of group members
 *
 * @category Group
 * @package  ShaiShai
 * @author   Andray Ma <andray09@gmail.com>
 */
class GroupmembersAction extends GroupDesignAction
{
    function handle($args)
    {
        parent::handle($args);
        $this->displayWith('GroupmembersHTMLTemplate');
    }
}
