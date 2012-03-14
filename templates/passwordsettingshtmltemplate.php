<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class PasswordsettingsHTMLTemplate extends SettingsHTMLTemplate
{
	// we have passwordsetting, qqbindingsettings, confirmaddress three actions
	// return to this page, but we need to give navigation an unique point
    function show($args = array()) {
    	$args['action'] = 'passwordsettings';
    	parent::show($args);
    }
	
	/**
     * Title of the page
     *
     * @return string Title of the page
     */

    function title()
    {
        return '修改密码';
    }
    
    function showSettingsTitle() {
    	return '密码设置';
    }
    
    function showSettingsInstruction() {
    	return '为了您的账号安全，请定期修改登录密码';
    }
    
    function _showOldPassword() {
    	$this->elementStart('p', 'clearfix');
    	$this->element('label', array('for' => 'password', 'class' => 'label60'), '原密码');
        $this->element('input', array('type' => 'password', 'name' => 'oldpassword', 'id' => 'oldpassword', 'class' => 'text200'));
        $this->elementEnd('p');
    }
    
    function _showNewPassword() {
    	$this->elementStart('p', 'clearfix');
    	$this->element('label', array('for' => 'newpassword', 'class' => 'label60'), '新密码');
        $this->element('input', array('type' => 'password', 'name' => 'newpassword', 'id' => 'newpassword', 'class' => 'text200'));
        $this->elementEnd('p');
    }
    
    function _showConfirmPassword() {
    	$this->elementStart('p', 'clearfix');
    	$this->element('label', array('for' => 'confirm', 'class' => 'label60'), '确认密码');
        $this->element('input', array('type' => 'password', 'name' => 'confirm', 'id' => 'confirm', 'class' => 'text200'));
        $this->elementEnd('p');
    }
    
    function _showSubmitPassword() {
    	$this->element('input', array('type' => 'submit', 'value' => '修改密码', 'name' => 'changepass', 'class' => 'submit button76 green76'));
    }
    
    function _showQQ() {
    	$this->elementStart('p', 'clearfix');
    	$this->element('label', array('for' => 'qqid', 'class' => 'label60'), 'QQ号');
        $this->element('input', array('type' => 'text', 'name' => 'qqid', 'id' => 'qqid', 
                                      'value'=>($this->cur_user_profile->qq ? $this->cur_user_profile->qq : ''), 'class' => 'text200'));
        $this->elementEnd('p');
    }
    
    function _showQQSubmit() {
    	$this->element('input', array('type' => 'submit', 'value' => '申请绑定', 'name' => 'qqbinding', 'class' => 'submit button76 green76'));
    }
    
    function showSettingsContent() {
    	
    	$this->tu->startFormBlock(array('method' => 'POST',
                                          'id' => 'form_password',
                                          'class' => 'settings',
                                          'action' =>
                                          common_local_url('passwordsettings')), '修改密码');
        
        $this->elementStart('dl');
        
        $this->element('dt', null, '');
        
        $this->elementStart('dd');
        
        $this->_showOldPassword();
        
        $this->_showNewPassword();
        
        $this->_showConfirmPassword();
    	
    	$this->elementEnd('dd');
        $this->elementEnd('dl');
        
        $this->elementStart('div', 'op');
        
        $this->_showSubmitPassword();
        
        $this->elementEnd('div');
        
        $this->tu->endFormBlock();
        
        
//        // We also make qq binding setting in this page
//        
//        $this->tu->showPageInstructionBlock('绑定您的QQ号码后，您将拥有与QQ号相同的GamePub号，'. 
//                                            '您可以使用它来登陆，您的好友也可以通过搜索您的QQ号准确地'.
//                                            '找到您！(为保护您的隐私，您的GamePub号对其它游友不可见。)');
//        
//        $this->tu->startFormBlock(array('method' => 'POST',
//                                          'id' => 'form_qqbind',
//                                          'class' => 'settings',
//                                          'action' =>
//                                          common_local_url('qqbindingsettings')), '绑定QQ号');
//        
//        $this->elementStart('dl');
//        
//        $this->element('dt', null, '绑定QQ号');
//        
//        $this->elementStart('dd');
//        
//        $this->_showQQ();
//    	
//    	$this->elementEnd('dd');
//        $this->elementEnd('dl');
//        
//        $this->elementStart('div', 'op');
//                
//        $this->_showQQSubmit();
//        
//        $this->elementEnd('div');
//        
//        $this->tu->endFormBlock();
        
    }
    
    function showScripts() {
    	parent::showScripts();
    	$this->script('js/jquery.validate.min.js');
    	$this->script('js/lshai_passwordsetting.js');
    	
    }
}

?>