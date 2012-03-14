<?php
/**
 * Shaishai, the distributed microblog
 *
 * Check owner's all notices in owner's perspect
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
 * Check owner's all notices in owner's perspect
 *
 * @category Personal
 * @package  Shaishai
 * @author    Andray Ma <andray09@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

class ShowallAction extends ProfileAction
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
		$this->_validate();
        
		$tag = $this->trimmed('tag', 0);
		$filter_content = $this->trimmed('filter_content', 0);
		
    	if (!empty($this->cur_user) && $this->cur_user->id == $this->owner->id) {
    	 	$notice = $this->owner->noticeInbox(($this->cur_page-1)*NOTICES_PER_PAGE, NOTICES_PER_PAGE + 1,
    	 	  			0, 0, $filter_content, $tag);
        } else {
        	$notice = $this->owner->noticesWithFriends(($this->cur_page-1)*NOTICES_PER_PAGE, NOTICES_PER_PAGE + 1,
        				0, 0, $filter_content, $tag);
    	}
        
		$this->addPassVariable('notice', $notice);
		$this->addPassVariable('profile', $this->owner->getProfile());
		$this->addPassVariable('tag', $tag);
		$this->addPassVariable('filter_content', $filter_content);
		
		if ($this->boolean('ajax')) {
			if($this->cur_page > 1) {
	    		$showView = TemplateFactory::get('ShowallHTMLTemplate');
	    		$showView->ajaxShowPageNotices($this->paras);   		
	    	} 
		} else {
			$this->displayWith('ShowallHTMLTemplate');
		} 
    }
    
    function _validate()
    {       
		if($this->trimmed('filter_content') && !is_numeric($this->trimmed('filter_content'))) {
            $this->clientError('您不能访问此链接.', 403);
            return;
        }
        
    	if($this->trimmed('tag') && !is_numeric($this->trimmed('tag'))) {
            $this->clientError('您不能访问此链接.', 403);
            return;
        }		
    }
}