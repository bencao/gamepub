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

require_once INSTALLDIR.'/lib/noticelist.php';

class ShowallHTMLTemplate extends ProfileHTMLTemplate
{
   	function title()
    {
        return sprintf("%s的视角", $this->page_owner->nickname);
    }

    function showEmptyList()
    {
        $emptymsg = array();
        $emptymsg['p'] = sprintf('这是%s和他关注的人的消息列表 ，但是还没人发消息。', 
        		                  $this->owner->nickname);
        $this->tu->showEmptyListBlock($emptymsg);
    }

    function showContent()
    {
    	parent::showContent();
    	
    	$arguments = array('uname' => $this->owner->uname);
    	$filter_content = $this->args['filter_content'];
    	$tag = $this->args['tag'];
    	
		$this->tu->showNewContentFilterBoxBlock($this->args['profile'], $filter_content, $tag, 'showall', $arguments);     	
		
    	$nl = new NoticeList($this->args['notice'], $this);
        $cnt = $nl->show();
        
    	$params = array();
		if ($tag) {
		    $params = array_merge($params, array('tag' => $tag));
		}
		if ($filter_content) {
		    $params = array_merge($params, array('filter_content' => $filter_content));
		}
		$this->morepagination($this, $cnt > NOTICES_PER_PAGE, $this->cur_page, 'showall', $arguments, $params);
    }
   
   	function ajaxShowPageNotices($args) {
    	$this->args = $args;
		$this->owner = $this->args['owner'];
		$this->cur_page = $this->args['page'];
    	
    	$view = TemplateFactory::get('JsonTemplate');
        $view->init_document();
        
        $notice = $this->args['notice'];
        if ($notice->N > 0) {
        	$xs = new XMLStringer();
        	$nl = new NoticeList($notice, $xs);
       		$cnt = $nl->show();
        	
        	$xs1 = new XMLStringer();
       		if ($cnt > NOTICES_PER_PAGE) {
       			$arguments = array('uname' => $this->owner->uname);
	       		$tag = $this->args['tag'];
	       		$filter_content = $this->args['filter_content'];
		    	$params = array();
		    	if ($tag) {
		    		$params = array_merge($params, array('tag' => $tag));
		    	}
		    	if ($filter_content) {
		    		$params = array_merge($params, array('filter_content' => $filter_content));
		    	}

		    	$this->morepagination($xs1, $cnt > NOTICES_PER_PAGE, $this->cur_page, 'showall', $arguments, $params);
        	}
	       	
        	$resultArray = array('result' => 'true', 'notices' => $xs->getString(), 'pg' => $xs1->getString()); 	
        } else {
        	$resultArray = array('result' => 'false');
        }
       	    	       	 	
        $view->show_json_objects($resultArray);
        $view->end_document();
    }
}