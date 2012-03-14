<?php

if (!defined('SHAISHAI')) {
	exit(1);
}

class Missionstep3HTMLTemplate extends RegisterWizardHTMLTemplate
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
		$this->elementStart('li', 'active');
		$this->element('span', null, '3');
		$this->element('a', array('href' => common_local_url('missionstep3')), '填写兴趣爱好');
		$this->elementEnd('li');
		$this->elementStart('li', 'last');
		$this->element('a', array('href' => common_local_url('missionstep4')), '完成任务');
		$this->elementEnd('li');
		$this->elementEnd('ul');
	}
	
	function showIntro() {
//		$gradeinfo = $this->cur_user_profile->getUserUpgradePercent(); 
		$this->elementStart('div', 'intro clearfix');
		$this->elementStart('div', 'text');
		$this->raw('兴趣让方便志同道合的朋友可以找到您。您用<a href="' . common_local_url('peoplesearch', null, array('advance' => '1')) . '" target="_blank">高级搜索</a>也可以找到他们！');
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
//    			$this->tu->showPageHighlightBlock($this->args['page_msg']);
    		} else {
    			$this->tu->showPageErrorBlock($this->args['page_msg']);
    		}
    	}
    }
    
	function _showSelfDefine() {
    	$this->element('dt', null, '兴趣爱好');
    	
		$this->elementStart('dd');
    	$this->elementStart('textarea', array('name' => 'user_self_define', 
			'id' => 'user_self_define', 'class' => 'textarea376'));
    	$this->text($this->trimmed('interest_self_define'));
    	$this->elementEnd('textarea');
    	$this->elementEnd('dd');
    }
    
    function _showCategories() {
    	
    	$this->element('dt', null, '感兴趣的内容');
    	
    	$this->elementStart('dd');
    	$this->elementStart('ul', 'clearfix');
    	
    	$categories = $this->arg('interest_categories');
    	
    	$currinterests = $this->arg('interest_currinterests');
    	
    	for ($i = 0; $i < count($categories); $i ++)
        {
        	$c = $categories[$i];
        	
        	$this->elementStart('li');
	        
        	if (in_array($c, $currinterests)) {
				$this->element('input', array('type' => 'checkbox', 'name' => 'userinterests[]', 'class' => 'userinterests_checkbox checkbox', 'value' => $c, 'checked' => 'checked', 'id' => 'cate' . $i));
			} else {
				$this->element('input', array('type' => 'checkbox', 'name' => 'userinterests[]', 'class' => 'userinterests_checkbox checkbox', 'value' => $c, 'id' => 'cate' . $i));
			}
			
			$this->element('label', array('for' => 'cate' . $i), $c);
        	
        	$this->elementEnd('li');
        }
        $this->elementEnd('ul');
    	$this->elementEnd('dd');
    }
    
    function _showSubmit() {
//        $this->element('input', array('type' => 'button', 'value' => '全选', 'name' => 'selAll', 'class' => 'selAll', 'forClass' => 'userinterests_checkbox'));
//        $this->element('input', array('type' => 'button', 'value' => '全不选', 'name' => 'unSelAll', 'class' => 'unSelAll', 'forClass' => 'userinterests_checkbox'));
        $this->element('input', array('type' => 'submit', 'value' => '保存', 'name' => 'save', 'class' => 'submit button76 green76'));
    }
    
    function showForm()
    {
    	$this->tu->startFormBlock(array('method' => 'post',
                                           'id' => 'settings_interest',
                                           'class' => 'form_settings',
                                           'action' => common_local_url('missionstep3')), '设置兴趣');
		$this->elementStart('dl');
    	$this->_showSelfDefine();
    	$this->_showCategories();
    	
        $this->elementEnd('dl');
        
        $this->elementStart('div', 'op');
        $this->_showSubmit();
        $this->elementEnd('div');
        
        $this->tu->endFormBlock();
    }
}