<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class ApplyvipHTMLTemplate extends RegisterWizardHTMLTemplate
{
	var $msg = null;
	var $success = null;
	function title(){
		return '名人认证';
	}
	
	function greeting(){
		return 'GamePub期待与高级玩家、著名游戏人士携手合作';
	}
	
	function show($args = array(), $msg = null, $success=false) {
		if ($msg) {
			$this->msg = $msg;
		}
		$this->success = $success;
		parent::show($args);
	}
	
	function showScripts() {
		parent::showScripts();
		$this->script('js/jquery.validate.min.js');
		$this->script('js/lshai_applyvip.js');
	}
	
	function showContent(){
		if($this->success){
			$this->_showFamousSuccessForm();
		}else{
			$this->_showFamousAuthenForm();
		}
	}
	
	function _showFamousAuthenForm(){
		$this->tu->startFormBlock(array('id' => 'applyvip', 
    		'action' => common_local_url('applyvip'), 'method' => 'post'), '名人认证');
    	if ($this->msg) {
    		$this->element('div', 'error', $this->msg);
    	}
    	
    	$this->element('p', null, '请您填写以下申请信息，我们将在三个工作日内对您的资料进行审核。');
    	$this->element('p', null, '一旦审核通过，我们将与您直接联系合作事宜，谢谢！');
    	
    	$this->elementStart('dl');
    	
    	$this->elementStart('dt');
    	$this->element('label', array('for'=>'phone_number', 'class' => 'label100'), '手机号码');
    	$this->elementEnd('dt');
    	$this->elementStart('dd');
    	$this->element('input', array('name' => 'phone_number', 
                          'type' => 'text', 'class' => 'text200',
                          'value' => (($this->arg('phone_number')) ? $this->arg('phone_number') : '')));
    	$this->elementEnd('dd');
    	
    	$this->elementStart('dt');
    	$this->element('label',array('for'=>'email','class'=>'label100'),'电子邮件');
    	$this->elementEnd('dt');
    	$this->elementStart('dd');
    	$this->element('input', array('name' => 'email',  
                          'type' => 'text', 'class' => 'text200',
                          'value' => (($this->arg('email')) ? $this->arg('email') : '')));
    	$this->elementEnd('dd');
    	
    	$this->elementStart('dt');
    	$this->element('label',array('for'=>'description','class'=>'label100'),'您的个人简述(资质说明)');
    	$this->elementEnd('dt');
    	$this->elementStart('dd', 'textarea clearfix');
        $this->element('textarea', array('id' => 'description',
                                              'name' => 'description', 
                                              'class' => 'textarea376'),
                            ($this->arg('description')) ? $this->arg('description') : '');
    	$this->elementEnd('dd');
    	
    	$this->elementStart('dt');
    	$this->element('p', null, '图片附件');
    	$this->elementEnd('dt');
    	$this->elementStart('dd', array('style' => 'padding-left:5px;height:auto;'));
    	$this->elementStart('div',array('class'=>'uploadfile',
    							  	    'style'=>'display:'.($this->arg('filenum') ? 'none':'inline')));
    	$this->element('input',array('id'=>'uploadify',
    							     'name'=>'uploadify',
    							     'type'=>'file',
    							     'style'=>'display:none;'));
    	$this->element('div',array('id'=>'fileQueue','uid'=>$this->cur_user->id));
    	$this->element('input',array('id'=>'filenum',
    								 'name'=>'filenum',
    								 'type'=>'hidden',
    								 'value'=>($this->arg('filenum') ? $this->arg('filenum'):'')));
    	$this->element('input',array('id'=>'fileurl',
    							     'name'=>'fileurl',
    							     'type'=>'hidden',
    								 'value'=>($this->arg('fileurl') ? $this->arg('fileurl'):'')));
    	$this->elementEnd('div');
    	$this->elementEnd('dd');
    	
    	$this->elementEnd('dl');
    	
    	$this->elementStart('div', 'op');
    	$this->element('input', array('class' => 'submit button99 green99', 'type' => 'submit', 'value' => '提交'));
    	$this->elementEnd('div');
    	$this->tu->endFormBlock();
	}
	
	function _showFamousSuccessForm(){
		$this->elementStart('div', array('id' => 'recoverpass'));
		$this->element('h2', null, 'VIP认证资料提交');
//		$this->element('p',null,'这是您上传的认证图片');
//		$this->elementStart('div');
//		$this->elementStart('ul');
//		$urls = $this->arg('urls');
//		foreach($urls as $url){
//			$this->elementStart('li');
//			$this->element('img',array('width'=>60,'height'=>60,'src'=>$url));
//			$this->elementEnd('li');
//		}
//		$this->elementEnd('ul');
//		$this->elementEnd('div');
		$this->element('div', 'success', $this->msg);
		$this->tu->showEmptyListBlock('<a href="' . ($this->cur_user ? common_local_url('home') : common_path('')) . '" class="button99 green99" style="margin:5px;">返回空间</a>');
		$this->elementEnd('div');
	}
}