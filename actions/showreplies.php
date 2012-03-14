<?php
/**
 * Shaishai, the distributed microblog
 *
 * Show target user's list of replies
 *
 * PHP version 5
 *
 * @category  Login
 * @package   Shaishai
 * @author    Andray Ma <andray09@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Show target user's list of replies
 *
 * @category Personal
 * @package  Shaishai
 * @author    Andray Ma <andray09@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

class ShowrepliesAction extends ProfileAction
{
	function __construct() {
		parent::__construct();
		$this->no_anonymous = false;
	}
	
    function isReadOnly($args)
    {
        return true;
    }

    function prepare($args)
    {
        if (! parent::prepare($args)) {return false;}

        if ($this->cur_user && $this->owner->id == $this->cur_user->id) {
        	$this->clientError('请在您的主页上点击"回复"来查看您的回复。');
            return false;
        }

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
        
        $notice = $this->owner->getReplies(($this->cur_page-1) * NOTICES_PER_PAGE,
                               NOTICES_PER_PAGE + 1);
        $total = $this->owner->replyCount();
                               
		$this->addPassVariable('notice', $notice);
		$this->addPassVariable('total', $total);                                       
		$this->addPassVariable('user', $this->owner);
		$this->addPassVariable('profile', $this->profile);
		
		$this->displayWith('ShowrepliesHTMLTemplate');
    }
    
}