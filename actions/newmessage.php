<?php
/**
 * Shaishai, the distributed microblog
 *
 * Action for posting new direct messages
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
 * Action for posting new direct messages
 *
 * @category Personal
 * @package  Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

require_once INSTALLDIR . '/lib/mail.php';
require_once INSTALLDIR . '/lib/messagelist.php';

class NewmessageAction extends ShaiAction
{
	var $content = null;
	var $to = null;
	var $other = null;

	function prepare($args)
	{
		if (! parent::prepare($args)) {return false;}		

		return true;
	}

	/**
	 * Handle input, produce output
	 *
	 * @param array $args $_REQUEST contents
	 *
	 * @return void
	 */

	function handle($args)
	{
		parent::handle($args);
		
		try {
			$this->saveNewMessage($args);
		} catch (Exception $e) {
			$testcac = new AjaxError();
			$testcac->showError($e->getMessage());
			return;
		}
	}

	function saveNewMessage($args)
	{
		$this->preHandle();
		
		$message = Message::saveNew($this->cur_user->id, $this->other->id, $this->content, 'web');

		if (is_string($message)) {
			$this->clientError($message);
			return;
		}

		mail_notify_message($message, $this->cur_user->getProfile(), $this->other->getProfile());

		if ($this->boolean('ajax')) {
//			$newmessageView = TemplateFactory::get('NewmessageHTMLTemplate');
//			$this->addPassVariable('message', $message);
//			$newmessageView->ajaxShowMessage($this->paras);
			
			$stringer = new XMLStringer();

        	$nli = new MessageListItem($message, $stringer, 'outbox');
        	$nli->show();
        	
        	$this->showJsonResult(array('html' => $stringer->getString()));
		} else {
			$url = common_path($this->cur_user->uname . '/outbox');
			common_redirect($url, 303);
		}
	}
	
	function preHandle()
	{
		if ($_SERVER['REQUEST_METHOD'] != 'POST') {
			$this->clientError('不接受非POST方法', 403);
			return;
		}
		
		//select, status_textarea的值
		$this->content = $this->trimmed('status_textarea'); //content
		if (mb_strlen($this->content, 'utf-8') > 280) {
            $this->clientError('信息太长, 最大为280个字。');
        }
        
		$this->to = $this->trimmed('to');
		if(! $this->to || ! is_numeric($this->to)) {
			$this->clientError('缺少收件人参数.', 403);
			return;
		}

		if ($this->cur_user->id == $this->to) {
			$this->clientError('无法向自己发送悄悄话', 403);
			return;
		}

		$this->other = User::staticGet('id', $this->to);

		if (!$this->other) {
			$this->clientError('收件人不存在');
			return;
		}

		if (is_null($this->content)) {
			$this->clientError('没有内容');
			return;
		}
	}
}