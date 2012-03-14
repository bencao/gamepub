<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class NewwendaHTMLTemplate extends RightsidebarHTMLTemplate
{
	function title() {
		return '提新问题';
	}
	
	function showShaishaiScripts() {
		parent::showShaishaiScripts();
		$this->script('js/lshai_newwenda.js');
	}
	
	function showContent() {
		$this->wenda_title = $this->trimmed('title', '');
    	$this->wenda_desc = $this->trimmed('desc', '');
    	$this->wenda_anonymous = $this->trimmed('anonymous', 0);
    	$this->wenda_award = $this->trimmed('award', 0);
    	$this->error_msg = $this->trimmed('error_msg', false);

		$this->element('h2', 'question', '酒馆问答 - 提问');
		if ($this->error_msg) {
			$this->tu->showPageErrorBlock($this->error_msg);
		}
		$this->tu->startFormBlock(array('class' => 'newquestion', 'action' => common_local_url('newwenda'), 'method' => 'post'), '新提问');
		$this->elementStart('dl', 'area');
		$this->elementStart('dt', 'head');
		$this->text('请输入您的提问');
		$this->elementStart('span');
		$this->element('em', null, 50 - mb_strlen($this->wenda_title, 'utf-8'));
		$this->text('字剩余');
		$this->elementEnd('span');
		$this->elementEnd('dt');
		$this->elementStart('dd', 'body');
		$this->element('textarea', array('class' => 'ititle', 'name' => 'title'), $this->wenda_title);
		
		if (mb_strlen($this->wenda_desc, 'utf-8') > 0) {
			$this->elementStart('dl', 'expand');
		} else {
			$this->elementStart('dl');
		}
		$this->elementStart('dt');
		$this->element('a', array('href' => '#', 'class' => 'switch'), '详细描述');
		$this->elementEnd('dt');
		$this->elementStart('dd');
		$this->element('textarea', array('class' => 'idesc', 'name' => 'desc'), $this->wenda_desc);
		$this->elementStart('div', 'op');
		$this->elementStart('span');
		$this->text('您还可以输入');
		$this->element('em', null, 280 - mb_strlen($this->wenda_desc, 'utf-8'));
		$this->text('字');
		$this->elementEnd('span');
		
		$this->elementEnd('div');
		$this->elementEnd('dd');
		$this->elementEnd('dl');
		$this->elementEnd('dd');
		$this->elementEnd('dl');
		$this->elementStart('div', 'oop clearfix');
		if ($this->wenda_anonymous) {
			$this->element('input', array('type' => 'checkbox', 'class' => 'checkbox', 'name' => 'anonymous', 'value' => '1', 'id' => 'anonymous', 
				'checked' => 'checked'));
		} else {
			$this->element('input', array('type' => 'checkbox', 'class' => 'checkbox', 'name' => 'anonymous', 'value' => '1', 'id' => 'anonymous'));
		}
		$this->element('label', array('for' => 'anonymous'), '匿名提问');
		$this->elementStart('p');
		$this->text('财富悬赏：');
		$this->elementStart('select', array('name' => 'award'));
		$this->element('option', array('value' => '0'), '0');
		$this->element('option', array('value' => '3'), '3');
		$this->element('option', array('value' => '5'), '5');
		$this->element('option', array('value' => '10'), '10');
		$this->element('option', array('value' => '20'), '20');
		$this->elementEnd('select');
		$this->text('个铜币');
		$this->elementEnd('p');
		$this->elementEnd('div');
		$this->element('input', array('type' => 'submit', 'class' => 'submit button94 orange94', 'value' => '提交问题'));
		$this->tu->endFormBlock();
	}
	
	function showRightsidebar() {
		$this->tu->showIntroWidget('提问描述', '请简明清晰的描述您的疑问，如问题较为复杂，请点击“详细描述”来进行更详细的说明。');
		$this->tu->showIntroWidget('问题有效期', '问题有效期为一个月，若到期您仍未选出“最佳答案”，问题将被自动关闭，悬赏不能退回哦！');
		$this->tu->showIntroWidget('匿名提问', '需要付出额外1铜G币的财富');
	}
}