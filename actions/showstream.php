<?php
/**
 * Shaishai, the distributed microblog
 *
 * User profile page
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
 * User profile page
 *
 * @category Personal
 * @package  Shaishai
 * @author    Andray Ma <andray09@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

class ShowstreamAction extends ProfileAction
{
	var $tag;
	var $filter_content;
	var $owner_profile;
	var $notice;
	var $total;
	
	// for search track
	var $search_request_id;
	var $search_from_notice_id;
	
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
    	
    	$this->tag = $this->trimmed('tag', 0);
		$this->filter_content = $this->trimmed('filter_content', 0);
		
    	if ($this->filter_content && !is_numeric($this->filter_content)) {
            $this->clientError('您不能访问此链接.', 403);
            return false;
        }
        
    	if ($this->tag && !is_numeric($this->tag)) {
            $this->clientError('您不能访问此链接.', 403);
            return false;
        }
        
        $this->owner_profile = $this->owner->getProfile();
        
        $this->search_request_id = $this->trimmed('s');
        if ($this->search_request_id && !is_numeric($this->search_request_id)) {
        	$this->clientError('s必须为数字', 403);
            return false;
        }
        
    	$this->search_from_notice_id = $this->trimmed('n');
    	if ($this->search_from_notice_id && !is_numeric($this->search_from_notice_id)) {
    		$this->clientError('n必须为数字', 403);
            return false;
    	}
    	return true;
    }
    
    function handle($args)
    {
    	parent::handle($args);
    	
    	$this->_handleSearchRecordTrack();
    	
    	$this->_handleVisitedNum();

    	$this->notice = $this->owner->getNotices(($this->cur_page-1)*NOTICES_PER_PAGE, NOTICES_PER_PAGE + 1,
     	  			0, 0, null, $this->filter_content, 0, 4, $this->tag);
    	$this->total = $this->owner->noticeCountByType($this->filter_content, $this->tag);
    					
		$this->addPassVariable('notice', $this->notice);
		$this->addPassVariable('total', $this->total);
		$this->addPassVariable('profile', $this->owner_profile);
		$this->addPassVariable('tag', $this->tag);
		$this->addPassVariable('filter_content', $this->filter_content);
		
		$this->cur_design = $this->owner->getDesign();
    	$this->addPassVariable('design', $this->cur_design);
    	$this->addPassVariable('self_designs', User_self_design::getDesignsByUser($this->owner));
    	$this->addPassVariable('official_designs', Official_design::getOfficialDesigns());
    	
		$this->displayWith('ShowstreamHTMLTemplate');
    }
    
    function _handleVisitedNum() {
    	if (! $this->is_own) {
    		$this->owner_profile->increaseVisitedNum();
    	}
    }
    
    function _handleSearchRecordTrack() {
    	if ($this->search_request_id) {
    		$sr = Search_request::staticGet('id', $this->search_request_id);
    		if ($sr && $sr->source == '0') {
    			// from notice search
    			Notice_search_target::saveNew($sr->id, $this->search_from_notice_id, $this->owner->id);
    		}
    	}
    }
    
}