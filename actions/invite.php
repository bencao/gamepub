<?php
/*
 * LShai - a distributed microblogging tool
 */

if (!defined('SHAISHAI')) { exit(1); }

class InviteAction extends ShaiAction
{
    function isReadOnly($args)
    {
        return true;
    }

    function handle($args)
    {
        parent::handle($args);
        $this->addPassVariable('invite_link', common_path('register?ivid=' . $this->cur_user->id));
        
        $this->addPassVariable('myinvites', Myinviterecord::getInviteesByInviterid($this->cur_user->id));
        
        $this->displayWith('InviteHTMLTemplate');
    }
}
