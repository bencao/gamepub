<?php
/**
 * Shaishai, the distributed microblog
 *
 * Success for posting new notices
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
 * Success for posting new notices
 *
 * @category Personal
 * @package  Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

require_once INSTALLDIR.'/lib/noticelist.php';

class NewnoticeHTMLTemplate extends PersonalHTMLTemplate
{
    /**
     * Error message, if any
     */

    var $msg = null;

    /**
     * Title of the page
     *
     * Note that this usually doesn't get called unless something went wrong
     *
     * @return string page title
     */

    function title()
    {
        return '新消息';
    }
    
   /**
     * Show an Ajax-y error message
     *
     * Goes back to the browser, where it's shown in a popup.
     *
     * @param string $msg Message to show
     *
     * @return void
     */

    function ajaxErrorMsg($args, $msg)
    {
    	$this->args = $args;
    	
        $this->startHTML('text/xml;charset=utf-8', true);
        $this->elementStart('head');
        $this->element('title', null, 'AJAX错误');
        $this->elementEnd('head');
        $this->elementStart('body');
        $this->element('p', array('id' => 'error'), $msg);
        $this->elementEnd('body');
        $this->endHTML();
    }

    /**
     * Formerly page output
     *
     * This used to be the whole page output; now that's been largely
     * subsumed by showPage. So this just stores an error message, if
     * it was passed, and calls showPage.
     *
     * Note that since we started doing Ajax output, this page is rarely
     * seen.
     *
     * @param string $msg An error message, if any
     *
     * @return void
     */

    function showForm($args, $msg=null)
    {
        if ($msg && $this->boolean('ajax')) {
            $this->ajaxErrorMsg($args, $msg);
            return;
        }

        $this->msg = $msg;
        $this->show($args);
    }

    /**
     * Overload for replies or bad results
     *
     * We show content in the notice form if there were replies or results.
     *
     * @return void
     */

    function showNoticeForm()
    {
        $content = $this->trimmed('status_textarea');
        if (!$content) {
            $replyto = $this->trimmed('replyto');
            $profile = Profile::staticGet('uname', $replyto);
            if ($profile) {
                $content = '@' . $profile->uname . ' ';
            }
        }

        $notice_form = new ShaiNoticeForm($this, '', $content);
        $notice_form->show();
    }

    /**
     * Show an error message
     *
     * Shows an error message if there is one.
     *
     * @return void
     *
     * @todo maybe show some instructions?
     */

    function showPageNotice()
    {
        if ($this->msg) {
            $this->element('p', array('id' => 'error'), $this->msg);
        }
    }

    /**
     * Output a notice
     *
     * Used to generate the notice code for Ajax results.
     *
     * @param Notice $notice Notice that was saved
     *
     * @return void
     */

    function showNotice($notice)
    {
        $nli = new NoticeListItem($notice, $this);
        $nli->show();
    }
    
    function showAJAXNotice($args, $notice, $replyinbox)
    {
    	$this->args = $args;
		
//        $this->startHTML('text/xml;charset=utf-8');
//        $this->elementStart('head');
//        $this->element('title', null, '消息已发送');
//        $this->elementEnd('head');
//        $this->elementStart('body');
//        $this->showNotice($notice);
//        if($replyinbox)
//        	$this->element('p', array('data' => "{replyinbox:'true'}", 'class' => 'reply_inbox'));
//        else 
//        	$this->element('p', array('data' => "{replyinbox:'false'}", 'class' => 'reply_inbox'));
//        $this->elementEnd('body');
//        $this->endHTML();

    	$view = TemplateFactory::get('JsonTemplate');
        $view->init_document();
        $xs = new XMLStringer();
        $nli = new NoticeListItem($notice, $xs);
        $nli->show();        	
        $resultArray = array('result' => $xs->getString(), 'replyinbox' => ($replyinbox ? 'true' : 'false'));       	 	       	 	
        $view->show_json_objects($resultArray);
        $view->end_document();
    	
    }
}