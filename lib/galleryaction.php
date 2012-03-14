<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

define('AVATARS_PER_PAGE', 80);

class GalleryAction extends OwnerDesignAction
{
//    var $page_owner_profile = null;
//    var $page = null;
//    var $isOwn;
//    var $page_owner;
    var $owner_profile;
    var $offset;
    var $limit;
    
	function __construct() {
		parent::__construct();
		$this->no_anonymous = false;
	}

    function prepare($args)
    {
        if (! parent::prepare($args)) {return false;}
//        
//        $uname = strtolower($this->arg('uname'));
//
//        $this->page_owner = User::staticGet('uname', $uname);
//
//        if (! $this->page_owner) {
//            $this->clientError('没有此用户。', 404);
//            return false;
//        }
//
//        $this->page_owner_profile = $this->page_owner->getProfile();
//
//        if (! $this->page_owner_profile) {
//            $this->serverError('没有此用户。');
//            return false;
//        }
//
//    	if ($this->arg('page') 
//    		&& ! is_numeric($this->arg('page'))) {
//            $this->clientError('您访问的页数不存在.', 403);
//            return;
//	    }
//	    
//        $this->page = ($this->arg('page')) ? ($this->arg('page') + 0) : 1;
//        
        $this->offset = ($this->cur_page - 1) * PROFILES_PER_PAGE;
        $this->limit =  PROFILES_PER_PAGE + 1;
//        
//        $this->isOwn = $this->cur_user ? ($this->cur_user->id == $this->page_owner->id) : false;
//
//        common_debug('gallery prepare');
//        
    	# Post from the tag dropdown; redirect to a GET

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		    common_redirect($this->selfUrl(), 303);
            return;
		}
		$this->owner_profile = $this->owner->getProfile();
		
		common_set_returnto($this->selfUrl());
		
        return true;
    }

    function isReadOnly($args)
    {
        return true;
    }

    function handle($args)
    {
        parent::handle($args);
        
//		$this->addPassVariable('g_page_owner', $this->page_owner);
		$this->addPassVariable('owner_profile', $this->owner_profile);
//		$this->addPassVariable('g_is_own', $this->isOwn);
//		$this->addPassVariable('g_page', $this->page);
//		$this->addPassVariable('g_cur_user', $this->cur_user);
		$this->addPassVariable('g_sub_total', $this->getTotalPages());
		$this->addPassVariable('g_subs', $this->getSubs());
		
		$this->extraHandle();
		
		$this->displayWith($this->getViewName());
    }
    
    function extraHandle() { }
    
    // should be override
    function getSubs() {
    	return null;
    }
    
    // should be override
    function getTotalPages() {
    	return 0;
    }
    
    function getViewName() {
    	return 'BasicHTMLTemplate';
    }
}

?>