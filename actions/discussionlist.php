<?php
/**
 * Shaishai, the distributed microblog
 *
 * Display a reply list of the notice
 *
 * PHP version 5
 *
 * @category  Personal
 * @package   Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

require_once INSTALLDIR . '/lib/discusslist.php';

/**
 * Display a reply list of the notice
 *
 * @category Personal
 * @package  Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

class DiscussionlistAction extends ShaiAction
{ 	
	var $notice_id;
	var $root_notice;

	function __construct() {
		parent::__construct();
		$this->no_anonymous = false;
	}
	
    /**
     * Initialization.
     *
     * @param array $args Web and URL arguments
     *
     * @return boolean false if id not passed in
     */

    function prepare($args)
    {
        if (! parent::prepare($args)) {return false;}
        $this->notice_id = $this->trimmed('notice_id');
    	if($this->notice_id && !is_numeric($this->notice_id)) {//若不为notice_id数字
            $this->clientError('您不能访问此链接.', 403);
            return false;
        }
        $this->root_notice = Notice::staticGet('id',$this->notice_id);
        if (! $this->root_notice || $this->root_notice->is_banned ==1) {
        	$this->clientError('原消息不存在', 403);
            return false;
        }

        return true;
    }

    /**
     * Handle the action
     *
     * @param array $args Web and URL arguments
     *
     * @return void
     */

    function handle($args)
    {
        parent::handle($args);        

        $offset = ($this->cur_page - 1) * NOTICES_PER_PAGE;
        $discus_list = Discussion::disListStream($this->notice_id, $offset);
		$totaldiss =  $this->root_notice->discussion_num;//Notice::getDissCount($this->notice_id);
        
		if ($this->cur_user && $this->root_notice->user_id == $this->cur_user->id) {
			Discussion_unread::setReadByNoticeidAndReceiverid($this->notice_id, $this->cur_user->id);
		}
		
        if ($this->boolean('ajax')) {
        	$stringer = new XMLStringer();
        	
			$discusslist = new NoticeDiscussList($stringer, $discus_list, $this->root_notice, $totaldiss, $this->cur_user);
        	$discusslist->show();
        	
        	$this->showJsonResult(array('result' => 'true', 'html' => $stringer->getString()));
//        	$this->view->endHTML();
        } else {
        	$this->addPassVariable('root_id', $this->notice_id);
			$this->addPassVariable('dis_list', $discus_list);
			$this->addPassVariable('total',$totaldiss);
			$this->addPassVariable('notice_owner', $this->root_notice->getUser());
			
        	$this->displayWith('DiscussionlistHTMLTemplate');
        }
    }	
}