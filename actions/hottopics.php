<?php
/**
 * Shaishai, the distributed microblog
 *
 * Show hot topic notices
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

define('SUBJECTS_PER_PAGE', 10);

/**
 * Show hot topic notices
 *
 * @category Personal
 * @package  Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */


class HottopicsAction extends ShaiAction
{    
	function __construct() {
		parent::__construct();
		$this->no_anonymous = false;
	}

    function isReadOnly($args)
    {
        return true;
    }

    /**
     * Read and validate arguments
     *
     * @param array $args URL parameters
     *
     * @return boolean success value
     */

    function prepare($args)
    {
        if (! parent::prepare($args)) {return false;}

        common_set_returnto($this->selfUrl());

        return true;
    }

    /**
     * handle request
     *
     * Show the public stream, using recipe method showPage()
     *
     * @param array $args arguments, mostly unused
     *
     * @return void
     */

    function handle($args)
    {
        parent::handle($args);
        
        $hotwords = Hotwords::getHotWords();
        
    	$tag = $this->trimmed('tag');
    	$tag_name = $this->trimmed('name');
    	if($tag) {
	    	$notice = Notice_tag::getStream($tag, (($this->cur_page-1)*NOTICES_PER_PAGE), NOTICES_PER_PAGE + 1);
			
	    	$tag_object = Second_tag::staticGet('id', $tag);
	        if(!$tag_object) {
	        	$this->clientError('您查看的标签不存在.');
	        	return;
	        }
	        $tag_name = $tag_object->name;
    	} else {
    		if (! $tag_name) {    	
	    		$tag_name = count($hotwords) > 0 ? $hotwords[0]->word : '游戏历程';
    		}	
    	}
    	
    	$nsa = new NoticesearchAction();
    	$nsa->q = $tag_name;
    	$nsa->ct = 0;
    	$nsa->cur_page = $this->cur_page;
    	$nsa->cur_user = $this->cur_user; 
    	
    	$nsa->_doSearch(null);
    	$notice = $nsa->resultset;
    	$this->addPassVariable('total', $nsa->total);
		
        $this->addPassVariable('notice', $notice);
		$this->addPassVariable('tag', $tag);
		$this->addPassVariable('tag_name', $tag_name);
		//$this->addPassVariable('tag_score', $tag_score);
		$this->addPassVariable('hotwords', $hotwords);
		
		if ($this->boolean('ajax')) {
			if($this->cur_page > 1) {
	    		$publicView = TemplateFactory::get('HottopicsHTMLTemplate');
				$publicView->showPageNotices($this->paras);     		
	    	}
		} else {
        	$this->displayWith('HottopicsHTMLTemplate');
		}
    }
}