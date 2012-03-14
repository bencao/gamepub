<?php

if (!defined('SHAISHAI')) { exit(1); }

class RecoverPasswordHTMLTemplate extends RegisterWizardHTMLTemplate {

	function greeting()
	{
		return '密码忘了怎么办？别担心，只要绑定过邮件就能找回！';
	}
	
	function showContent()
	{
		
		$this->elementStart('div', array('id' => 'recoverpass'));
		$this->element('h2', null, '找回密码');
		
		if (array_key_exists('recoverpass_reset', $this->args) && $this->args['recoverpass_reset']) {
			$this->showMessageForm(false);
		} else if (array_key_exists('recoverpass_success', $this->args) && $this->args['recoverpass_success']) {
			$this->showMessageForm(true);
		} else if (array_key_exists('recoverpass_mode', $this->args)) {
			if ($this->args['recoverpass_mode'] == 'recover') {
				$this->showRecoverForm();
			} else if ($this->args['recoverpass_mode'] == 'reset') {
				$this->showResetForm();
			}
		}
		
		$this->elementEnd('div');
	}
	
	function showMessageForm($bool) {
		$this->tu->showPageHighlightBlock($this->args['recoverpass_msg']);
		
		if ($bool) {
			$this->tu->showEmptyListBlock('<a href="' . common_local_url('home') . '" class="button99 green99">前往您的空间</a>');
		} else {
			$this->tu->showEmptyListBlock('<a href="' . $this->args['mail_link'] . '" class="button99 green99">前往收取邮件</a>');
		}
	}

	function showRecoverForm()
	{
        $this->element('p', 'intro', '忘记了您的登录密码？');
        
        if ($this->args['recoverpass_msg']) {
        	$this->tu->showPageErrorBlock($this->args['recoverpass_msg']);
        }
        
        $this->tu->startFormBlock(array('method' => 'post',
                                           'id' => 'form_password_recover',
                                           'action' => common_local_url('recoverpassword')), '密码恢复');

    	$this->element('label', array('for' => 'unameoremail'), '填写您的用户名或邮箱');

    	$this->element('input', array('type' => 'text', 
    					'name' => 'unameoremail', 
    					'id' => 'unameoremail',
    					'class' => 'text200',
    					'value' => ($this->arg('unameoremail')) ? $this->arg('unameoremail') : ''));
		$this->elementStart('div', 'op');
    	$this->element('input', array('type' => 'submit', 'name' => 'recover', 'value' => '找回密码', 'class' => 'submit button99 green99'));
    	$this->elementEnd('div');

		$this->tu->endFormBlock();
	}

	function showResetForm()
	{
        $this->element('p', 'intro', '开始重设密码');
        
        $this->tu->showPageHighlightBlock('您的身份校验已通过，请输入新密码');
        
        $this->tu->startFormBlock(array('method' => 'post',
                                           'id' => 'form_password_change',
                                           'class' => 'form_settings',
                                           'action' => common_local_url('recoverpassword')), '密码修改');
        
        $this->elementStart('table', array('cellspacing' => '0', 'cellpadding' => '0', 'border' => '0'));
    	$this->elementStart('tbody');
    	
    	$this->elementStart('tr');
    	$this->elementStart('td', 'b_cbf_l');
    	$this->element('label', array('for' => 'unameoremail'), '新密码');
    	$this->elementEnd('td');
		$this->elementStart('td', 'b_cbf_t');
    	$this->element('input', array('type' => 'password', 
    					'name' => 'newpassword', 
    					'id' => 'newpassword',
    					'size' => '28'));
    	$this->elementEnd('td');
    	$this->element('td', 'b_cbf_e', '6位或更多字符');
    	$this->elementEnd('tr');
    	
    	$this->elementStart('tr');
    	$this->elementStart('td', 'b_cbf_l');
    	$this->element('label', array('for' => 'unameoremail'), '密码确认');
    	$this->elementEnd('td');
		$this->elementStart('td', 'b_cbf_t');
    	$this->element('input', array('type' => 'password', 
    					'name' => 'confirm', 
    					'id' => 'confirm',
    					'size' => '28'));
    	$this->elementEnd('td');
    	$this->element('td', 'b_cbf_e', '再次输入密码');
    	$this->elementEnd('tr');
    	
    	$this->elementStart('tr');
    	$this->element('td');
    	$this->elementStart('td', 'b_cbf_sm');
    	$this->element('input', array('type' => 'submit', 'name' => 'reset', 
    		'class' => 'submit button99 green99', 'value' => '重设密码'));
    	$this->elementEnd('td');
    	$this->element('td');
    	$this->elementEnd('tr');
		
    	$this->elementEnd('tbody');
    	$this->elementEnd('table');
		
		$this->tu->endFormBlock();
	}
	
	function title()
    {
        switch ($this->args['recoverpass_mode']) {
         case 'reset': return '重设密码';
         case 'recover': return '找回密码';
         case 'sent': return '密码找回请求';
         case 'saved': return '密码已保存';
         default:
            return '未知的操作';
        }
    }
    
    function showScripts() {
    	parent::showScripts();
    	$this->script('js/jquery.validate.min.js');
    	$this->script('js/lshai_passwordsetting.js');
    }
}

?>