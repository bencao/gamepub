<?php
/**
 * Shaishai, the distributed microblog
 *
 * List of replies
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
 * List of replies
 *
 * @category Personal
 * @package  Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */


class RepliesAction extends OwnerdesignAction
{ 
    /**
     * Prepare the object
     *
     * Check the input values and initialize the object.
     * Shows an error page on bad input.
     *
     * @param array $args $_REQUEST data
     *
     * @return boolean success flag
     */

    function prepare($args)
    {
        if (! parent::prepare($args)) {return false;}

        $uname = strtolower($this->arg('uname'));

        $user = User::staticGet('uname', $uname);

        if (! $user) {
            $this->clientError('没此用户.');
            return false;
        }
        
        if ($this->cur_user->id != $user->id) {
        	$this->clientError('只有用户自己才能查看回复');
            return false;
        }

        common_set_returnto($this->selfUrl());

        return true;
    }

    /**
     * Handle a request
     *
     * Just show the page. All args already handled.
     *
     * @param array $args $_REQUEST data
     *
     * @return void
     */

    function handle($args)
    {
        parent::handle($args);
        
		$notice = $this->cur_user->getReplies(($this->cur_page -1) * NOTICES_PER_PAGE,
                                          NOTICES_PER_PAGE + 1);
                                          
        $total = $this->cur_user->replyCount();     	                        		
		$this->addPassVariable('total', $total);
		$this->addPassVariable('notice', $notice);
		$this->addPassVariable('user', $this->cur_user);
		
		$this->displayWith('RepliesHTMLTemplate');
    }
    
    function isReadOnly($args)
    {
        return true;
    }
}