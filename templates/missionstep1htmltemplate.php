<?php

if (!defined('SHAISHAI')) {
	exit(1);
}

class Missionstep1HTMLTemplate extends RegisterWizardHTMLTemplate
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
    	$this->game_job = $args['game_job'];
    	$this->game_org = $args['game_org'];
    	$this->game_jobs = $args['game_jobs'];
		parent::show($args);
	}
	
	function showTab() {
		$this->elementStart('ul', 'steps clearfix');
		$this->elementStart('li', 'first active');
		$this->element('span', null, '1');
		$this->element('a', array('href' => common_local_url('missionstep1')),'完善个人资料');
		$this->elementEnd('li');
		$this->elementStart('li');
		$this->element('span', null, '2');
		$this->element('a', array('href' => common_local_url('missionstep2')),'添加个人头像');
		$this->elementEnd('li');
		$this->elementStart('li');
		$this->element('span', null, '3');
		$this->element('a', array('href' => common_local_url('missionstep3')),'填写兴趣爱好');
		$this->elementEnd('li');
		$this->elementStart('li', 'last');
		$this->element('a', array('href' => common_local_url('missionstep4')),'完成任务');
		$this->elementEnd('li');
		$this->elementEnd('ul');
	}
	
	function showIntro() {
//		$gradeinfo = $this->cur_user_profile->completeness; 
		$this->elementStart('div', 'intro clearfix');
		$this->element('div', 'text', '完善您的个人资料，让其他玩家更好的了解您。');
//		$this->elementStart('div', 'progress');
//		$this->element('strong', null, '资料完成度 ' . $this->cur_user_profile->completeness . '%');
//		$this->elementStart('span');
//		$this->element('em', array('style' => 'width:' . $this->cur_user_profile->completeness . '%;'));
//		$this->elementEnd('span');
//		$this->elementEnd('div');
		$this->elementEnd('div');
	}
	
	function showForm() {
		$this->tu->startFormBlock(array('method' => 'post',
                                           'id' => 'form_settings_profile',
                                           'class' => 'settings',
                                           'action' => common_local_url('missionstep1')), '修改个人资料');
    	
    	$this->_showGameJob();
    	
    	$this->_showGameOrganization();
		
		$this->_showLocation();
    	
    	$this->_showBirthday();
    	
//    	$this->_showHomepage();
    	
//    	$this->_showSchool();
//    	
//    	$this->_showOccupation();
    	
    	$this->_showBio();
    	
    	$this->elementStart('div', 'op');
    	
    	$this->_showSubmit();
    	
    	$this->elementEnd('div');
    	
    	$this->tu->endFormBlock();
	}
	
	function showContent() {
		$this->elementStart('div', array('id' => 'tocomplete'));
		$this->showTab();
		$this->showIntro();
		$this->showPageTip();
		$this->showForm();
		
		$this->elementEnd('div');
	}
	
	function _showGameOrganization() {
    	$this->elementStart('p', 'clearfix');
    	
    	$this->element('label', array('for' => 'game_org', 'class' => 'label60'), GROUP_NAME());
    	
    	$this->element('input', array('class' => 'text200',
    					'id' => 'game_org',
    					'type' => 'text', 
    					'name' => 'game_org',
    					'maxlength' => '255', 
    					'value' => $this->game_org));
    	$this->elementEnd('p');
    }
    
    function _showGameJob() {
    	$this->elementStart('p', 'clearfix');
    	
    	$this->element('label', array('for' => 'game_job', 'class' => 'label60'), JOB_NAME());
    	
    	$this->elementStart('select', 
			array('name' => 'game_job', 
				'id' => 'game_job'));
		$this->option('', '请选择', $this->game_job);
		foreach ($this->game_jobs as $gj) {
			$this->option($gj, $gj, $this->game_job);
		}
        $this->elementEnd('select');
        
    	$this->elementEnd('p');
    }
    
    function _showLocation() {
    	$this->elementStart('p', 'clearfix');
    	
    	$this->element('label', array('for' => 'province', 'class' => 'label60'), '居住地');
    	
		$prov = ($this->arg('province')) ? $this->arg('province') : $this->cur_user_profile->province;
		$city = ($this->arg('city')) ? $this->arg('city') : $this->cur_user_profile->city;
		$dist = ($this->arg('district')) ? $this->arg('district') : $this->cur_user_profile->district;
		
		$this->element('input', array('id' => 'province', 'name' => 'province' , 'type' => 'hidden', 
						'value' => $prov));
		$this->element('input', array('id' => 'city','name' => 'city' , 'type' => 'hidden',
						'value' => $city));
		$this->element('input', array('id' => 'district', 'name' => 'district' ,'type' => 'hidden', 
						'value' => $dist));
		
		$this->element('input', array('id' => 'location', 'name' => 'location' ,'type' => 'text', 
						'value' => Profile::location($prov, $city, $dist), 'class' => 'text200'));
		
    	$this->elementEnd('p');
    }
    
    function _showBirthday() {
    	$this->elementStart('p', 'clearfix');
    	
    	$this->element('label', array('for' => 'birthday', 'class' => 'label60'), '生日');
    	
    	$this->element('input', array('id' => 'birthday',
    					'type' => 'text', 
    					'name' => 'birthday', 
    					'class' => 'text200',
    					'value' => ($this->arg('birthday')) ? $this->arg('birthday') : $this->cur_user_profile->birthday));
    	
    	$this->elementEnd('p');
    }
    
    function _showBio() {
    	$this->elementStart('p', array('class' => 'clearfix', 'style' => 'height:90px;'));
    	
    	$this->element('label', array('for' => 'bio', 'class' => 'label60'), '简介');
    	
		$this->elementStart('textarea', array('id' => 'bio', 'name' => 'bio', 'cols' => '28', 'rows' => '3'));
		$this->text($this->arg('bio') ? $this->arg('bio') : $this->cur_user_profile->bio);
		$this->elementEnd('textarea');
		
    	$this->elementEnd('p');
    }
    
    function _showSchool() {
    	$this->elementStart('p', 'clearfix school');

    	$this->element('label', array('for' => 'school', 'class' => 'label60'), '学校');
    	
    	$this->element('input', array('type' => 'text', 'name' => 'school', 'id' => 'school', 'class' => 'text200',
    				'value' => ($this->arg('school')) ? $this->arg('school') : $this->cur_user_profile->school));
    	
    	$this->elementEnd('p');
    	
    }
    
    function _showOccupation() {
    	$this->elementStart('p', 'clearfix occupation');
    	
    	$this->element('label', array('for' => 'occupation', 'class' => 'label60'), '行业');
    	
    	$this->element('input', array('type' => 'text', 'name' => 'occupation', 'id' => 'occupation', 'class' => 'text200',
    				'value' => ($this->arg('occupation')) ? $this->arg('occupation') : $this->cur_user_profile->occupation));
    	
    	$this->elementEnd('p');
    }
    
    function _showSubmit() {
    	$this->element('input', array('type' => 'submit', 'class' => 'submit button76 green76', 'value' => '继续任务'));
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
    
	function showScripts() {
    	parent::showScripts();
    	$this->script('js/jquery.ui.datepicker.min.js');
		$this->script('js/ui.datepicker-zh-CN.js');
		$this->script('js/lshai_cityschooldata.js');
		$this->script('js/lshai_cityschoolselect.js');
//		$this->script('js/lshai_occupation.js');
		$this->script('js/jquery.validate.min.js');
		$this->script('js/lshai_profilesettings.js');
    	
    }
}