<?php
/**
 * Shaishai, the distributed microblog
 *
 * Error view
 *
 * PHP version 5
 *
 * @category  Login
 * @package   Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @author    Ben Cao <benb88@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Error view
 * It's a little sick of inheriting dochtmltemplate, so it's a temporarily approach
 *
 * @category Personal
 * @package  Shaishai
 * @author    Guofu Xie Andray Ma <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

class ErrorHTMLTemplate extends DocHTMLTemplate
{
    function show($args = array()) {
    	$args['thistype'] = 'help';
    	parent::show($args);
    }
	
    /**
     *  To specify additional HTTP headers for the action
     *
     *  @return void
     */
    function extraHeaders()
    {
    	$ex = 'HTTP/1.1 '.$this->args['code'];
    	header($ex);
    }
    
    function showRightSection() {
	    	$navs = new NavList_Help();
	    	// error page regards like it needs help, so we show help navigation
	    	$this->tu->showNavigationWidget($navs->lists(), 'help');
    }
    
    /**
     * Display content.
     *
     * @return nothing
     */
    function showContent()
    {
    	$this->tu->showTitleBlock('操作失败', 'attention');
    	
        $this->tu->showPageErrorBlock($this->args['message']);
    	
    	$this->tu->showEmptyListBlock(
    		array('此问题可能是权限不足等非法操作导致的，您可以在<a href="'. common_path('doc/help/modules').'">[帮助系统]</a>中获得帮助。', 
    		'如果您认为这个错误是网站本身的bug导致的，请将问题 <a href="' . common_path('main/userfeedback'). '">[反馈]</a> 给我们，若此错误被确认为bug，我们将奖励您G币以示感谢。'));
    	
    }

    /**
     * Page title.
     *
     * @return page title
     */
    function title()
    {
    	return $this->args['status_string'];
    }

    function isReadOnly($args)
    {
        return true;
    }
}
