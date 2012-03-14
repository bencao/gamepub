<?php

if (!defined('SHAISHAI')) {
	exit(1);
}

class Missionstep4HTMLTemplate extends RegisterWizardHTMLTemplate
{
	var $cur_user_profile;
	
	function title() {
		return '升级任务';
	}
	
	function greeting() {
		return '完善您的个人资料与头像，成为二级用户，享受更多特权！';
	}
	
	function show($args) {
		$this->cur_user_profile = $args['settings_cur_user_profile'];
		parent::show($args);
	}
	
	function extraHeaders() {
		if ($this->arg('ok') == 'finished') {
			setcookie('ah', 'los', time()+60*60*24*30, '/main/');
		}
	}
	
	function showTab() {
		$this->elementStart('ul', 'steps clearfix');
		$this->elementStart('li', 'first');
		$this->element('span', null, '1');
		$this->element('a', array('href' => common_local_url('missionstep1')), '完善个人资料');
		$this->elementEnd('li');
		$this->elementStart('li');
		$this->element('span', null, '2');
		$this->element('a', array('href' => common_local_url('missionstep2')), '添加个人头像');
		$this->elementEnd('li');
		$this->elementStart('li');
		$this->element('span', null, '3');
		$this->element('a', array('href' => common_local_url('missionstep3')), '填写兴趣爱好');
		$this->elementEnd('li');
		$this->elementStart('li', 'active last');
		$this->element('a', array('href' => common_local_url('missionstep4')), '完成任务');
		$this->elementEnd('li');
		$this->elementEnd('ul');
	}
	
	function showIntro() {
//		$gradeinfo = $this->cur_user_profile->getUserUpgradePercent(); 
		$this->elementStart('div', 'intro clearfix');
		$this->elementStart('div', 'text');
		$this->raw('完成了任务啦？是该领取奖励的时候了！');
		$this->elementEnd('div');
//		$this->elementStart('div', 'progress');
//		$this->element('strong', null, '资料完成度 ' . $this->cur_user_profile->completeness . '%');
//		$this->elementStart('span');
//		$this->element('em', array('style' => 'width:' . $this->cur_user_profile->completeness . '%;'));
//		$this->elementEnd('span');
//		$this->elementEnd('div');
		$this->elementEnd('div');
	}
	
	function showContent() {
		$this->elementStart('div', array('id' => 'tocomplete'));
		$this->showTab();
		$this->showIntro();
		$this->showPageTip();
		$this->showForm();
		
		$this->elementEnd('div');
	}
    
	function showPageTip()
    {
    	if ($this->arg('page_msg')) {
    		if ($this->arg('page_success')) {
    			$this->tu->showPageHighlightBlock($this->args['page_msg']);
    		} else {
    			$this->tu->showPageErrorBlock($this->args['page_msg']);
    		}
    	}
    }
    
    function showForm()
    {
    	if ($this->arg('ok') == 'confirm') {
    		$this->_showConfirm();
    	} else if ($this->arg('ok') == 'finished') {
    		$this->_showFinished();
    	} else {
    		$this->_showSolution();
    	}
    }
    
    function _showSolution() {
    	$this->elementStart('ol', array('style' => 'padding:5px 50px;line-height:24px;font-size:14px;'));
    	if ($this->arg('noemail')) {
    		$this->elementStart('li', array('style' => 'list-style:decimal;'));
    		$this->text('未确认邮件 - ');
    		$mail_link = $this->arg('mail_link');
    		$this->element('a', array('href' => $mail_link['link'], 'target' => '_blank'), '去' . $mail_link['name'] . '邮箱确认');
    		$this->text('或者');
    		$this->element('a', array('href' => common_local_url('emailsettings'), 'target' => '_blank'), '看看我的邮件设置');
    		$this->elementEnd('li');
    	}
    	if ($this->arg('nobio')) {
    		$this->elementStart('li', array('style' => 'list-style:decimal;'));
    		$this->text('未填写简介 - ');
    		$this->element('a', array('href' => common_local_url('missionstep1')), '我要填写');
    		$this->elementEnd('li');
    	}
    	
//    	if ($this->arg('noorg')) {
//    		$this->elementStart('li', array('style' => 'list-style:decimal;'));
//    		$this->text('未填写' . GROUP_NAME() . ' - ');
//    		$this->element('a', array('href' => common_local_url('missionstep1')), '我要填写');
//    		$this->elementEnd('li');
//    	}
    	
    	if ($this->arg('nolocation')) {
    		$this->elementStart('li', array('style' => 'list-style:decimal;'));
    		$this->text('未填写居住地 - ');
    		$this->element('a', array('href' => common_local_url('missionstep1')), '我要填写');
    		$this->elementEnd('li');
    	}
    	
    	if ($this->arg('noavatar')) {
    		$this->elementStart('li', array('style' => 'list-style:decimal;'));
    		$this->text('未上传头像 - ');
    		$this->element('a', array('href' => common_local_url('missionstep2')), '我要上传');
    		$this->elementEnd('li');
    	}
    	if ($this->arg('nointerest')) {
    		$this->elementStart('li', array('style' => 'list-style:decimal;'));
    		$this->text('未填写兴趣 - ');
    		$this->element('a', array('href' => common_local_url('missionstep3')), '我要填写');
    		$this->elementEnd('li');
    	}
    	$this->elementEnd('ol');
    }
    
    function _showFinished() {
    	$this->elementStart('div', 'op');
    	$this->element('a', array('href' => common_local_url('home'), 'class' => 'button76 green76', 'style' => 'left:270px;position:relative;'), '返回空间');
    	$this->elementEnd('div');
    }
    
    function _showConfirm() {
    	$this->tu->startFormBlock(array('method' => 'post',
                                           'class' => 'settings',
                                           'action' => common_local_url('missionstep4')), '完成任务');
        $this->elementStart('div', 'op');
        $this->element('input', array('type' => 'submit', 'value' => '领取奖励', 'name' => 'save', 'class' => 'submit button76 green76'));
        $this->elementEnd('div');
        
        $this->tu->endFormBlock();
    }
}