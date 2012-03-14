<?php
/**
 * Shaishai, the distributed microblog
 *
 * Common parent of Personal and Profile actions
 *
 * PHP version 5
 *
 * @category  Login
 * @package   Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Common parent of Personal and Profile actions
 *
 * @category Personal
 * @package  Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */
class ProfileAction extends OwnerDesignAction
{
    var $profile = null;
    var $tag     = null;

    function prepare($args)
    {
        if (! parent::prepare($args)) {return false;}

        $this->profile = $this->owner->getProfile();

        if (!$this->profile) {
            $this->serverError('用户没有个人页面.');
            return false;
        }
        
        if ($this->profile->is_banned == 1) {
        	$this->clientError('用户账号已被封禁');
        	return false;
        }

        $this->tag = $this->trimmed('tag');
        
        common_set_returnto($this->selfUrl());
        return true;
    }
}
