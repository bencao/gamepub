<?php
/**
 * copyright @ shaishai.com
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Action for displaying the public stream
 *
 * @category Public
 * @package  SHAISHAI
 * @link     http://www.shaishai.com/
 *
 * @see      PublicrssAction
 * @see      PublicxrdsAction
 */

class RecentNewsAction extends GamebasicAction
{
    
	function __construct() {
		parent::__construct();
		$this->no_anonymous = false;
	}

    function isReadOnly($args)
    {
        return true;
    }

    function handle($args)
    {
        parent::handle($args);

        $noticeids = Notice::getLatestDiscussionIds(($this->cur_page - 1) * NOTICES_PER_PAGE, NOTICES_PER_PAGE + 1, $this->cur_game->id);
        $notice = Notice::getStreamByIds($noticeids);
                       
		$this->addPassVariable('notice', $notice);
    	$this->displayWith('RecentnewsHTMLTemplate');
    }
}
