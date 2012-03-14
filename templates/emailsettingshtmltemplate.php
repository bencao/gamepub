<?php


class EmailsettingsHTMLTemplate extends SettingsHTMLTemplate
{   
	/**
     * Title of the page
     *
     * @return string Title of the page
     */

    function title()
    {
        return '设置邮件选项';
    }
    
    function showSettingsTitle() {
    	return '邮件设置';
    }
    
    function showSettingsInstruction() {
    	return '修改您的邮件相关设置';
    }
    
	function showSettingsContent() {
		
		if ($this->cur_user_profile->email == null) {
			$this->tu->startFormBlock(array('method' => 'post',
	                                          'id' => 'form_settings_email',
	                                          'class' => 'settings',
	                                          'action' =>
	                                          common_path('settings/email')), '设置邮件');
			$this->elementStart('dl');
        	$this->element('dt', null, '');
        	$this->elementStart('dd');
        	$this->_showConfirmingEmail();
        	$this->_showConfirmingResend();
        	$this->elementEnd('dd');
        	$this->elementEnd('dl');
        	$this->tu->endFormBlock();
		} else {
		
	    	$this->tu->startFormBlock(array('method' => 'post',
	                                          'id' => 'form_settings_email',
	                                          'class' => 'settings',
	                                          'action' =>
	                                          common_path('settings/email')), '设置邮件');
	        $this->elementStart('dl');
	        $this->element('dt', null, '邮件绑定');
	        $this->elementStart('dd');
	        $this->_showBindedEmail();
	        $this->_showNewEmail();
	        $this->elementEnd('dd');
	        $this->elementEnd('dl');
	        $this->_showEmailSubmit();
	        $this->tu->endFormBlock();
        
        	$this->tu->startFormBlock(array('method' => 'post',
                                          'id' => 'form_settings_email_pref',
                                          'class' => 'settings',
                                          'action' =>
                                          common_path('settings/email')), '设置邮件偏好');
	        $this->elementStart('dl');
	        $this->element('dt', null, '邮件偏好');
	        $this->elementStart('dd');
	        $this->_showPreferences();
	        $this->elementEnd('dd');
	        $this->elementEnd('dl');
	        $this->_showPreferencesSubmit();
	        $this->tu->endFormBlock();
        }
    }
    
    function _showBindedEmail() {
    	$this->elementStart('p', 'clearfix');
    	$this->element('label', 'label90', '您已绑定');
        
    	$this->element('input', array('value' => $this->cur_user_profile->email, 'readonly' => 'readonly', 'class' => 'text200', 'type' => 'text'));
    	
    	$this->elementEnd('p');
    }
    
    function _showNewEmail() {
    	$this->elementStart('p', 'clearfix');
    	$this->element('label', array('for' => 'email', 'class' => 'label90'), '绑定新邮件');
    	$this->element('input', array('type' => 'text', 'id' => 'email', 'name' => 'email', 'class' => 'text200'));
    	$this->elementEnd('p');
    }
    
    function _showEmailSubmit() {
    	$this->elementStart('div', 'op');
    	$this->element('input', array('type' => 'submit', 'value' => '修改', 'name' => 'change', 'class' => 'submit button76 green76'));
    	$this->elementEnd('div');
    }
    
    function _showConfirmingEmail() {
//    	$this->elementStart('p', 'clearfix');
//            
//    	$this->element('label', 'label90', '您申请绑定');
//        
//    	$this->element('input', array('value' => $this->arg('email_confirm_address'), 'class' => 'text200', 'type' => 'text', 'readonly' => 'readonly'));
//    	
    	$this->elementStart('p', 'clearfix');
    	
    	$this->text('您已申请绑定' . $this->arg('email_confirm_address') . '， 但尚未确认！');
    	
    	$this->elementEnd('p');
    }
    
    function _showConfirmingResend() {
    	$this->elementStart('p', 'clearfix');
    	
    	$this->text('若您没有收到确认邮件，请检查您的拉圾箱，或点击');
    	$this->element('a', array('href' => common_path('ajax/resendmail'), 'id' => 'resendmail'), '这里');
    	$this->text('重发邮件。');
    	
    	$this->elementEnd('p');
    	
    	$this->elementStart('p', array('style' => 'margin-top:20px;'));
    	
    	$this->text('上述方法仍无法解决？ 请试试其它邮箱。');
    	
    	$this->elementEnd('p');
    	
    	$this->elementStart('p', 'clearfix');
    	
//    	$this->tu->startFormBlock(
//    		array('action' => common_local_url('resendmail'), 
//    				'method' => 'post', 
//    				'id' => 'form_resendmail'),
//    		 	'修改邮件地址并重发');
    		
    	$this->element('label', 'label90', '修改邮件地址为');
    	$this->element('input', array('type' => 'text', 'name' => 'newmail', 'id' => 'newmail', 'class' => 'text200'));
    	$this->element('a', array('id' => 'sendnew', 'href' => common_path('ajax/resendmail'), 'class' => 'button76 green76', 'style' => 'float:left;margin-left:8px;'), '重新发送');
    	
//    	$this->tu->endFormBlock();
    	
    	$this->elementEnd('p');
    }
    
    function _showSubPref() {
    	$this->elementStart('li');
        if ($this->cur_user_profile->emailnotifysub) {
        	$this->element('input', array('type' => 'checkbox', 'class' => 'checkbox', 'name' => 'emailnotifysub', 'id' => 'ens', 'value' => 'true', 'checked' => 'checked'));
        } else {
        	$this->element('input', array('type' => 'checkbox', 'class' => 'checkbox', 'name' => 'emailnotifysub', 'id' => 'ens', 'value' => 'true'));
        }
        $this->element('label', array('for' => 'ens'), '有人关注我');
        $this->elementEnd('li');
    }
    
    function _showFavPref() {
    	$this->elementStart('li');
        if ($this->cur_user_profile->emailnotifyfav) {
        	$this->element('input', array('type' => 'checkbox', 'class' => 'checkbox', 'name' => 'emailnotifyfav', 'id' => 'enf', 'value' => 'true', 'checked' => 'checked'));
        } else {
        	$this->element('input', array('type' => 'checkbox', 'class' => 'checkbox', 'name' => 'emailnotifyfav', 'id' => 'enf', 'value' => 'true'));
        }
        $this->element('label', array('for' => 'enf'), '有人收藏我的消息');
        $this->elementEnd('li');
    }
    
    function _showMsgPref() {
    	$this->elementStart('li');
        if ($this->cur_user_profile->emailnotifymsg) {
        	$this->element('input', array('type' => 'checkbox', 'class' => 'checkbox', 'name' => 'emailnotifymsg', 'id' => 'enm', 'value' => 'true', 'checked' => 'checked'));
        } else {
        	$this->element('input', array('type' => 'checkbox', 'class' => 'checkbox', 'name' => 'emailnotifymsg', 'id' => 'enm', 'value' => 'true'));
        }
        $this->element('label', array('for' => 'enm'), '有人给我发送悄悄话');
        $this->elementEnd('li');
    }
    
    function _showAttnPref() {
    	$this->elementStart('li');
        if ($this->cur_user_profile->emailnotifyattn) {
        	$this->element('input', array('type' => 'checkbox', 'class' => 'checkbox', 'name' => 'emailnotifyattn', 'id' => 'ena', 'value' => 'true', 'checked' => 'checked'));
        } else {
        	$this->element('input', array('type' => 'checkbox', 'class' => 'checkbox', 'name' => 'emailnotifyattn', 'id' => 'ena', 'value' => 'true'));
        }
        $this->element('label', array('for' => 'ena'), '有人回复我');
        $this->elementEnd('li');
    }
    
	function _showNudgePref() {
    	$this->elementStart('li');
        if ($this->cur_user_profile->emailnotifynudge) {
        	$this->element('input', array('type' => 'checkbox', 'class' => 'checkbox', 'name' => 'emailnotifynudge', 'id' => 'enn', 'value' => 'true', 'checked' => 'checked'));
        } else {
        	$this->element('input', array('type' => 'checkbox', 'class' => 'checkbox', 'name' => 'emailnotifynudge', 'id' => 'enn', 'value' => 'true'));
        }
        $this->element('label', array('for' => 'enn'), '有新活动或新功能');
        $this->elementEnd('li');
    }
    
    function _showPreferences() {
    	$this->elementStart('p', 'clearfix');
    	$this->element('label', null, '以下情况给我发邮件通知');
        $this->elementEnd('p');
    	$this->elementStart('ul');
        $this->_showSubPref();
        $this->_showFavPref();
        $this->_showMsgPref();
        $this->_showAttnPref();
        $this->_showNudgePref();
        $this->elementEnd('ul');
    }
    
    function _showPreferencesSubmit() {
        $this->elementStart('div', 'op');
        $this->element('input', array('type' => 'submit', 'value' => '保存', 'name' => 'save', 'class' => 'submit button76 green76'));
        $this->elementEnd('div');
    }
    
	function showScripts() {
		parent::showScripts();
		$this->script('js/lshai_resendmail.js');
	}
}

?>