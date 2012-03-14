<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class InviteHTMLTemplate extends RegisterWizardHTMLTemplate {
    
function title()
	{
		return '邀请好友加入' . common_config('site', 'name');
	}
	
	function greeting() {
		return '邀请您的好友加入' . common_config('site', 'name') . '，玩的更开心！';
	}
	
	function _showErrorMessage() {
		if ($this->arg('register_error')) {
			$this->element('div', array('class' => 'error', 'style' => 'width:430px;margin-left:30px;'), $this->arg('register_error'));
		}
	}
	
	function _showLinkInvite() {
		
		$this->elementStart('div', 'invite');
		
		$this->element('h3', null, '发送邀请链接给朋友');
		
		$this->elementStart('p');
		$this->text('复制下面的链接，用QQ,MSN发送邀请链接');
//		$this->element('a', array('class' => 'help', 'href' => '#'), '更多说明');
		$this->element('a', array('class' => 'help', 'href' => common_local_url('doc', array('type' => 'help', 'title' => 'invite')), 'target' => '_blank'), '邀请奖励');
		$this->elementEnd('p');
		
		$this->elementStart('p', 'clearfix');
		$this->element('input', array('id' => 'ivlink', 'type' => 'text', 'class' => 'text',
				'readonly' => 'readonly', 'value' => $this->arg('invite_link')));
		$this->element('a', array('class' => 'copy button76 green76', 'href' => '#', 'id' => 'ivbtn'), '复制链接');
		$this->elementEnd('p');
		
		$this->elementEnd('div');
	}
	
	function _showEmailInvite() {
//		$this->elementStart('div', 'email_iv');
//		$this->_showTab();
//		$this->_showQQ();
//		$this->_showMSN();
//		$this->_showGmail();
//		$this->_showYahoo();
//		$this->_showOther();

		$this->elementStart('div', 'email_iv');
		$this->tu->startFormBlock(array('method' => 'post',
										   'target' => '_blank',
                                           'action' => common_local_url('invitewithpass')), '邮箱邀请');
		$this->element('h3', null, '邀请您的Email联系人一起来玩GamePub!');
		
		$this->elementStart('p', 'instruction');
		$this->text('输入邮箱地址和密码，系统将自动获取联系人并向他们发送邀请。目前支持163、126、yeah、sohu、tom、新浪邮箱。');
		$this->elementEnd('p');
		
		$this->elementStart('p', 'clearfix');
		$this->element('label', array('for' => 'qno'), '账号：');
        $this->elementStart('span');
		$this->element('input', array('type' => 'text', 'id' => 'qno', 'class' => 'text200', 'name' => 'username'));
        $this->elementEnd('span');
		$this->elementEnd('p');
		
		$this->elementStart('p', 'clearfix');
		$this->element('label', array('for' => 'qp'), '密码：');
        $this->elementStart('span');
		$this->element('input', array('type' => 'password', 'id' => 'qp', 'name' =>'password', 'class' => 'text200'));
        $this->elementEnd('span');
		$this->elementEnd('p');
		
		$this->element('input', array('class' => 'submit button76 green76', 'type' => 'submit', 'value' => '邀请'));
		
		$this->hidden('source', 'ot');
		
		$this->tu->endFormBlock();
		
		$this->elementEnd('div');
	}
	
	function _showTab() {
		$this->elementStart('ul', 'iv_tab');
		$this->elementStart('li', 'tab_qq');
		$this->element('a', array('class' => 'active', 'href' => '#'), 'QQ好友');
		$this->elementEnd('li');
		$this->elementStart('li', 'tab_msn');
		$this->element('a', array('href' => '#'), 'MSN好友');
		$this->elementEnd('li');
		$this->elementStart('li', 'tab_gmail');
		$this->element('a', array('href' => '#'), 'Gmail联系人');
		$this->elementEnd('li');
		$this->elementStart('li', 'tab_yahoo');
		$this->element('a', array('href' => '#'), 'Yahoo联系人');
		$this->elementEnd('li');
		$this->elementStart('li', 'tab_other');
		$this->element('a', array('href' => '#'), 'Email联系人');
		$this->elementEnd('li');
		$this->elementEnd('ul');
	}
	
	function _showQQ() {
		$this->tu->startFormBlock(array('method' => 'post',
                                           'id' => 'form_qq_invite',
										   'target' => '_blank',
                                           'action' => common_local_url('invitewithpass')), 'QQ邀请');
        $this->element('h3', null, '邀请您的QQ好友一起来玩!');
		$this->element('h4', null, '前往QQ邮箱，给好友发送邀请。');
		$this->elementStart('p', 'clearfix');
		$this->element('label', array('for' => 'qno'), '账号');
        $this->elementStart('span');
		$this->element('input', array('type' => 'text', 'id' => 'qno', 'class' => 'text200', 'name' => 'username', 'maxlength' => '12'));
        $this->elementEnd('span');
		$this->elementEnd('p');
		
		$this->elementStart('p', 'clearfix');
		$this->element('label', array('for' => 'qp'), '密码');
        $this->elementStart('span');
		$this->element('input', array('type' => 'password', 'id' => 'qp', 'name' =>'password', 'class' => 'text200'));
        $this->elementEnd('span');
		$this->elementEnd('p');
		
		$this->element('input', array('class' => 'submit button76 green76', 'type' => 'submit', 'value' => '邀请'));
		
		$this->hidden('source', 'qq');
		
		$this->tu->endFormBlock();
	}
	
	function _showMSN() {
		$this->tu->startFormBlock(array('method' => 'post',
                                           'id' => 'form_live_invite',
                                           'style' => 'display:none;',
											'target' => '_blank',
                                           'action' => common_local_url('liverequesttoken')), 'MSN邀请');
        $this->element('h3', null, '邀请MSN好友一起来玩!');
		$this->element('h4', null, '前往您的MSN邮箱，邀请您的好友。');
		$this->element('input', array('class' => 'submit button76 green76', 'type' => 'submit', 'value' => '邀请'));
		
		$this->tu->endFormBlock();
	}
	
	function _showGmail() {
		$this->tu->startFormBlock(array('method' => 'post',
                                           'id' => 'form_google_invite',
                                           'style' => 'display:none;',
										   'target' => '_blank',
                                           'action' => common_local_url('oauthrequesttoken')), 'Gmail邀请');
        $this->element('h3', null, '邀请您的Gmail好友一起来玩'. common_config('site', 'name') . '!');
		$this->element('h4', null, '前往Gmail，邀请您的好友。');
		$this->elementStart('p', 'clearfix');
		$this->element('label', array('for' => 'qno'), '账号');
        $this->elementStart('span');
        $this->element('input', array('type' => 'text', 'id' => 'qno', 'class' => 'text200', 'name' =>'usermail', 'maxlength' => '64'));
        $this->elementEnd('span');
		$this->elementEnd('p');
		
		$this->element('input', array('class' => 'submit button76 green76', 'type' => 'submit', 'value' => '邀请'));
		$this->hidden('service', 'gcontact');
		
		$this->tu->endFormBlock();
	}
	
	function _showYahoo() {
		$this->tu->startFormBlock(array('method' => 'post',
                                           'id' => 'form_yahoo_invite',
                                           'style' => 'display:none;',
											'target' => '_blank',
                                           'action' => common_local_url('oauthrequesttoken')), 'Yahoo邀请');
        $this->element('h3', null, '邀请您的Yahoo好友一起来玩' . common_config('site', 'name') . '!');
		$this->element('h4', null, '前往Yahoo，邀请您的好友。');
		
		$this->element('input', array('class' => 'submit button76 green76', 'type' => 'submit', 'value' => '邀请'));
		
		$this->hidden('service', 'ycontact');
		
		$this->tu->endFormBlock();
	}
	
	function _showOther() {
		$this->tu->startFormBlock(array('method' => 'post',
                                           'id' => 'form_other_invite',
                                           'style' => 'display:none;',
											'target' => '_blank',
                                           'action' => common_local_url('invitewithpass')), '邮箱邀请');
        $this->element('h3', null, '邀请您的联系人一起来玩' . common_config('site', 'name') . '!');
		$this->element('h4', null, '前往邮箱，邀请您的好友。');
		$this->elementStart('p', 'clearfix');
		$this->element('label', array('for' => 'qno'), '账号');
        $this->elementStart('span');
		$this->element('input', array('type' => 'text', 'id' => 'qno', 'class' => 'text200', 'name' => 'username'));
        $this->elementEnd('span');
		$this->elementEnd('p');
		
		$this->elementStart('p', 'clearfix');
		$this->element('label', array('for' => 'qp'), '密码');
        $this->elementStart('span');
		$this->element('input', array('type' => 'password', 'id' => 'qp', 'name' =>'password', 'class' => 'text200'));
        $this->elementEnd('span');
		$this->elementEnd('p');
		
		$this->element('input', array('class' => 'submit button76 green76', 'type' => 'submit', 'value' => '邀请'));
		
		$this->hidden('source', 'ot');
		
		$this->tu->endFormBlock();
	}
	
	function _showInviteAward()
	{
		$this->elementStart('dl', 'invite_award rounded5');
		$this->element('dt', null, '用户邀请');
		$this->element('dd', null, '邀请的朋友加入后会与您自动互相关注，而且还会有G币的奖励哦！');
		$this->elementEnd('dl');
	}
	
	function _showInviteTip()
	{
		$this->elementStart('dl', 'invite_tip rounded5');
		$this->element('dt', null, '小提示');
		$this->element('dd', null, common_config('site', 'name') . '不会存储您的密码，请放心使用！');
		$this->elementEnd('dl');
	}
	
	function _showInviteOp()
	{
		$this->elementStart('div', 'invite_op');
		$this->element('a', array('href' => common_local_url('home'), 'style' => 'font-size:16px;text-align:center;width:200px;'), '返回空间');
		$this->elementEnd('div');
	}
	
	function _showMyInvites() {
		$myinvites = $this->trimmed('myinvites');
		
		$this->elementStart('dl', 'myinvites');
		$this->elementStart('dt');
		$this->text('已邀请并注册的用户');
		$this->element('span', null, ($myinvites ? $myinvites->N : 0) . '人');
		$this->elementEnd('dt');
		
		$this->elementStart('dd');
		while ($myinvites && $myinvites->fetch()) {
			$this->raw('<span><a href="' . $myinvites->profileurl . '">' . $myinvites->nickname . '</a></span>');
		}
		$this->elementEnd('dd');
		$this->elementEnd('dl');
	}
	
	function showContent()
	{		
		$this->_showLinkInvite();
		
		$this->_showErrorMessage();
		
		$this->_showEmailInvite();
		
		$this->_showMyInvites();
		
		$this->_showInviteAward();
		
		$this->_showInviteTip();
		
		$this->_showInviteOp();
	}
	
	function showScripts()
    {
    	parent::showScripts();
    	$this->script('js/jquery.validate.min.js');
    	$this->script('js/ZeroClipboard.js');
    	$this->script('js/lshai_invite.js');
    }
}

?>