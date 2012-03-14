<?php

if (!defined('SHAISHAI')) { exit(1); }

class ConfirmaddressHTMLTemplate extends RegisterWizardHTMLTemplate {

	function greeting()
	{
		return '确认您的邮件地址，以便找回密码和接收重要系统通知';
	}
	
	function showContent()
	{
		
		$this->elementStart('div', array('id' => 'recoverpass'));
		$this->element('h2', null, '确认成功');
		
		$this->tu->showPageHighlightBlock('您的账号' . $this->trimmed('uname') . '已经与地址' . $this->trimmed('address') . '成功绑定');
		
		$this->elementEnd('div');
	}
}