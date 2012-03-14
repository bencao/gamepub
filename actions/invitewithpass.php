<?php

if (!defined('SHAISHAI')) { exit(1); }

require_once 'BatchInvite/BatchInvite.php';
require_once INSTALLDIR . '/lib/mail.php';

class InvitewithpassAction extends ShaiAction
{
	function __construct() {
		parent::__construct();
		$this->cache_allowed = false;
	}
		
	function handle($args)
	{
		parent::handle($args);
		
		$user = common_current_user();
		$source = $this->trimmed('source');
		
		$username = $this->trimmed('username');
		$password = $this->trimmed('password');
		
		if ($source == 'ot') {
			// 根据邮箱判断服务商
			if (preg_match('/^(?<username>[^@]*)@(?<domain>[^\.]*)\.(?<postfix>.*)/i', $username, $matches) == 1) {
				$source = $matches['domain'];
            	$username = $matches['username'];
			} else {
				$this->showForm('您输入的邮箱地址无效');
			}
		}
		
		$availableSources = array('126', '163', 'yeah', 'tom', 'sohu', 'sina', 'qq');
		if (! in_array($source, $availableSources)) {
			$this->showForm('您选择了无效的邮件服务商，目前只支持126/163/yeah/tom/sohu/sina/qq的邮箱邀请');
			return;
		}
		
		if (empty($username) || preg_match('/@/', $username)) {
			$this->showForm('用户名无效');
			return;
		}
		
		if (empty($password)) {
			$this->showForm('密码不能为空');
			return;
		}
		
		if ($source == 'qq' && $this->trimmed('encryp') == null) {
			// special deal for qq
			echo BatchInvite::getQQReturnHTML($username, $password);
			return ;
		}
		
		$emails = BatchInvite::importFrom($source, $username, $password);
		
		if ($emails == 0) {
			$this->showForm('用指定的用户名和密码无法登陆邮箱');
		} else if (is_array($emails)) {
			if (count($emails) == 0) {
				$this->showForm('用指定的用户名和密码无法登陆邮箱');
			} else {
				
				foreach ($emails as $email) {
					mail_send_invitation($email[1], $user->getProfile());
				}
				
				$this->addPassVariable('invite_contacts', $emails);
				$this->displayWith('BatchinviteHTMLTemplate');
			}
		} else {
			$this->showForm('导入出错');
		}
	}
	
	function showForm($error='错误')
	{
		$this->addPassVariable('register_error', $error);
		$this->displayWith('InviteHTMLTemplate');
	}
}