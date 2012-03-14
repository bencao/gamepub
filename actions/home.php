<?php
/**
 * Shaishai, the distributed microblog
 *
 * Show user all notices
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
 * Show user all notices
 *
 * @category Personal
 * @package  Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

class HomeAction extends OwnerDesignAction
{ 
	var $owner_profile;
	
    function isReadOnly($args)
    {
        return true;
    }
    
    function prepare($args)
    {
    	if (! parent::prepare($args)) {return false;}
    	
    	$this->owner_profile = $this->owner->getProfile();
    	
    	if ($this->owner_profile->is_banned == 1) {
        	$this->clientError('你的账号已被封禁');
        	return false;
        }
        
        $this->_validate();
        
        return true;
    }

    function handle($args)
    {
        parent::handle($args);

		$since_id = $this->trimmed('since_id');
		$gtag = $this->trimmed('gtag', null);
		$tag = $this->trimmed('tag', 0);
		$filter_content = $this->trimmed('filter_content', 0);

       	$this->cur_user->query('BEGIN');
       	if($since_id) {
       		$notice = $this->cur_user->noticeInbox(($this->cur_page-1)*NOTICES_PER_PAGE, NOTICES_PER_PAGE + 1,
	     	  			$since_id, 0, $filter_content, $tag, $gtag);
         	$sysmes_result = Receive_sysmes::unReadCount($this->cur_user->id);
       	} else {
       		$notice = $this->cur_user->noticeInbox(($this->cur_page-1)*NOTICES_PER_PAGE, NOTICES_PER_PAGE + 1,
	     	  			0, 0, $filter_content, $tag, $gtag);
       	}

     	$this->cur_user->query('COMMIT');
		
		$this->addPassVariable('user', $this->cur_user);
    	$this->addPassVariable('notice', $notice);
    	$this->addPassVariable('filter_content', $filter_content);
    	$this->addPassVariable('tag', $tag);
    	$this->addPassVariable('gtag', $gtag);
    	
        if ($this->boolean('ajax')) {	
        	if($this->cur_page > 1) {
	    		$all_view = TemplateFactory::get('HomeHTMLTemplate');
				$all_view->ajaxShowPageNotices($this->paras);     		
	    	} else if ($since_id) {
				$all_view = TemplateFactory::get('HomeHTMLTemplate');
    			$this->addPassVariable('sysmes_result', $sysmes_result);
				$all_view->ajaxShowNoticeSinceId($this->paras); 
	    	}
        } else {
    	
        	//最新的消息id
        	$latest_notice_id = $this->cur_user->latestNoticeId();
        	$this->addPassVariable('latest_notice_id', $latest_notice_id);
    		$this->displayWith('HomeHTMLTemplate');    		
		} 
    }
    
    function _validate()
    {
        
        if($this->trimmed('since_id') && !is_numeric($this->trimmed('since_id'))) {
            $this->clientError('您不能访问此链接.', 403);
            return;
        }
        
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