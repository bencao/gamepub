<?php
/**
 * ShaiShai, the distributed microblogging tool
 *
 * Group main page
 *
 * PHP version 5
 *
 * @category  Group
 * @package   ShaiShai
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

require_once INSTALLDIR.'/lib/noticelist.php';

define('MEMBERS_PER_SECTION', 27);

/**
 * Group main page
 *
 * @category Group
 * @package  ShaiShai
 */

class ShowgroupAction extends GroupDesignAction
{
    function isReadOnly($args)
    {
        return true;
    }

    /**
     * Prepare the action
     *
     * Reads and validates arguments and instantiates the attributes.
     *
     * @param array $args $_REQUEST args
     *
     * @return boolean success flag
     */

    function prepare($args)
    {
        if (! parent::prepare($args)) {return false;}


        common_set_returnto($this->selfUrl());

        return true;
    }

    /**
     * Handle the request
     *
     * Shows a profile for the group, some controls, and a list of
     * group notices.
     *
     * @return void
     */

    function handle($args)
    {
        parent::handle($args);
        
        $tag = $this->trimmed('tag', 0);
		$filter_content = $this->trimmed('filter_content', 0);
//		$since_id = $this->trimmed('since_id');
		
       	$this->cur_group->query('BEGIN');
       	$notice = $this->cur_group->getNotices(($this->cur_page-1)*GROUP_NOTICES_PER_PAGE, GROUP_NOTICES_PER_PAGE + 1, $filter_content, $tag);
     	
     	Group_unread_notice::setRead($this->cur_user->id, $this->cur_group->id);
     	    	 	
//     	if($since_id) {
//     		$since_id_reply = $this->trimmed('since_id_reply');
//			$since_id_inbox = $this->trimmed('since_id_inbox');
//			$since_id_sysmes = $this->trimmed('since_id_sysmes');
//     		$notice = $this->cur_group->noticeInbox(($this->cur_page-1)*NOTICES_PER_PAGE, NOTICES_PER_PAGE + 1, $since_id);
//     		$reply_result = Reply::unReadCount($this->cur_user->id, $since_id_reply);
//         	$mes_result = Message::unReadCount($this->cur_user->id, $since_id_inbox);
//         	$sysmes_result = Receive_sysmes::unReadCount($this->cur_user->id, $since_id_sysmes);
//     	}
     	$this->cur_group->query('COMMIT');
		
		$this->addPassVariable('group', $this->cur_group);
    	$this->addPassVariable('notice', $notice);
    	$this->addPassVariable('filter_content', $filter_content);
    	$this->addPassVariable('tag', $tag);
    	
    	$this->cur_design = $this->cur_group->getDesign();
    	if (! $this->cur_design) {
    		$this->cur_design = Design::defaultGameDesign(
    			Game::staticGet('id', $this->cur_group->getOwner()->game_id));
    	}
    	$this->addPassVariable('design', $this->cur_design);
    	$this->addPassVariable('self_designs', 
    		Group_self_design::getDesignsByGroup($this->cur_group));
    	$this->addPassVariable('official_designs', Official_design::getOfficialDesigns());
        
        if ($this->boolean('ajax')) {
			if($this->cur_page > 1) {
	    		$view = TemplateFactory::get('ShowgroupHTMLTemplate');
				$view->showPageNotices($this->paras);
			}     					
		} else {
    		$this->displayWith('ShowgroupHTMLTemplate');
    	}
    }

}
