<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class UserfeedbackHTMLTemplate extends RegisterWizardHTMLTemplate
{
	var $msg = null;
	var $success = null;
	
	function title() {
		return '反馈意见';
	}
	
	function greeting() {
		return '诚挚感谢您提出任何意见和建议！';
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
		$this->script('js/lshai_feedback.js');
	}
	
	function _showFeedbackForm() {
		$this->tu->startFormBlock(array('id' => 'feedback', 
    		'action' => common_path('main/userfeedback'), 'method' => 'post'), '反馈');
    	
    	if ($this->msg) {
    		$this->element('div', 'error', $this->msg);
    	}
    	
    	$this->element('p', null, '您的反馈非常宝贵，开发团队在这里诚挚的请求您提出宝贵的意见。');
    	$this->element('p', null, '请您留下邮件地址，我们在处理反馈之后会与您联系！');
//    	$this->element('p', null, '客服邮箱: support@lshai.com， 客服QQ : 1279106166');
    	$this->elementStart('p');
    	$this->raw('您也可以在“我的空间”直接点击右侧“召唤小酒保”，把问题反馈给他，更快得到回复！');
    	$this->elementEnd('p');	
    	
    	$this->elementStart('dl');
    	$this->elementStart('dt');
    	$this->element('label', array('for'=>'probtype', 'class' => 'label100'), '反馈类型');
    	$this->elementEnd('dt');
    	$this->elementStart('dd', 'clearfix');
    	$this->elementStart('select', array('name' => 'type', 'id' => 'probtype'));
    	$this->option('', '请选择');
		$this->option('Feature', '建议', $this->arg('type'));
		$this->option('Issue', '疑问', $this->arg('type'));
		$this->option('WebDefect', '网页错误', $this->arg('type'));
		$this->option('HooyouDefect', '客户端问题', $this->arg('type'));
		$this->option('UninstallHooyou', '卸载客户端', $this->arg('type'));
		$this->option('RequestVip', '申请VIP', $this->arg('type'));
		$this->elementEnd('select');
    	$this->elementEnd('dd');

//    	$this->elementStart('dt');
//    	$this->element('label', array('for'=>'pdegree', 'class' => 'label100'), '严重程度 *');
//    	$this->elementEnd('dt');
//    	$this->elementStart('dd', 'clearfix');
//		$this->elementStart('select', array('name' => 'priority', 'id' => 'pdegree'));
//		$this->option('', '请选择');
//		$this->option('P1', '难以接受', $this->arg('priority'));
//		$this->option('P2', '一般', $this->arg('priority'));
//		$this->option('P3', '影响较小', $this->arg('priority'));
//		$this->elementEnd('select');
//    	$this->elementEnd('dd');
//    	
//    	$this->elementStart('dt');
//    	$this->element('label', array('for'=>'pmodule', 'class' => 'label100'), '所在模块 *');
//    	$this->elementEnd('dt');
//    	$this->elementStart('dd', 'clearfix');
//		$this->elementStart('select', array('name' => 'category', 'id' => 'pmodule'));
//		$this->option('', '请选择');
//		$this->option('Register Login', '注册与登陆', $this->arg('category'));
//		$this->option('Notices and Messages', '信息与消息', $this->arg('category'));
//		$this->option('User Relationship', '用户关系管理', $this->arg('category'));
//		$this->option('User Settings', '个人设置', $this->arg('category'));
//		$this->option('Group Management', '' . GROUP_NAME() . '管理', $this->arg('category'));
//		$this->option('Search', '搜索', $this->arg('category'));
//		$this->option('Other Modules', '其它模块', $this->arg('category'));
//		$this->elementEnd('select');
//    	$this->elementEnd('dd');
    	
    	$this->elementStart('dt');
    	$this->element('label', array('for'=>'email', 'class' => 'label100'), '您的email');
    	$this->elementEnd('dt');
    	$this->elementStart('dd', 'clearfix');
    	$this->element('input', array('name' => 'email', 'id' => 'email', 
                          'type' => 'text', 'class' => 'text200',
                          'value' => (($this->arg('email')) ? $this->arg('email') : '')));
    	$this->elementEnd('dd');
    	
//    	$this->elementStart('dt');
//    	$this->element('label', array('for'=>'pdesc', 'class' => 'label100'), '标题');
//    	$this->elementEnd('dt');
//    	$this->elementStart('dd', 'clearfix');
//    	$this->element('input', array('name'=>'subject', 'id'=>'pdesc', 
//                          'type'=>'text', 'maxlength'=>'100', 'class' => 'text200',
//                          'value'=>(($this->arg('subject')) ? $this->arg('subject') : '')));
//    	$this->elementEnd('dd');
    	
    	$this->elementStart('dt');
    	$this->element('label', array('for'=>'pdetail', 'class' => 'label100'), '描述');
    	$this->elementEnd('dt');
    	$this->elementStart('dd', 'textarea clearfix');
        $this->element('textarea', array('id' => 'pdetail',
                                              'name' => 'description', 
                                              'class' => 'textarea376'),
                            ($this->arg('description')) ? $this->arg('description') : '');
    	$this->elementEnd('dd');
    	$this->elementEnd('dl');
    	
    	$this->elementStart('div', 'op');
    	$this->element('input', array('class' => 'submit button99 green99', 'type' => 'submit', 'value' => '提交反馈'));
    	$this->elementEnd('div');
    	$this->tu->endFormBlock();
	}
	
	function _showFeedbackSuccess() {
		$this->elementStart('div', array('id' => 'recoverpass'));
		$this->element('h2', null, '用户反馈');
		$this->element('div', 'success', $this->msg);
		$this->tu->showEmptyListBlock('<a href="' . ($this->cur_user ? common_local_url('home') : common_path('')) . '" class="button99 green99" style="margin:5px;">返回我的空间</a>');
		$this->elementEnd('div');
	}
    
    function showContent() {
    	if ($this->success) {
    	    $this->_showFeedbackSuccess();
    	} else {
    		$this->_showFeedbackForm();
    	}
    }
    
}
