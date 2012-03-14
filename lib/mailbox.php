<?php
/**
 * Shaishai, the distributed microblog
 *
 * common superclass for direct messages inbox and outbox
 *
 * PHP version 5
 *
 * @category  Login
 * @package   Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @author    Ben Cao <benb88@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

class MailboxAction extends OwnerdesignAction
{
	var $owner_profile;
    
    function prepare($args)
    {
        if (! parent::prepare($args)) {return false;}

    	if (! $this->is_own) {
            $this->clientError('只有用户自己可以访问邮箱。', 403);
            return;
        }
        
        $this->owner_profile = $this->owner->getProfile();
        
        common_set_returnto($this->selfUrl());

        return true;
    }
    
    function handle($args) {
    	parent::handle($args);
    	$this->addPassVariable('owner_profile', $this->owner_profile);
    }
    
    function isReadOnly($args)
    {
         return true;
    }
   
	function getMessages()
    {
        return null;
    } 
}