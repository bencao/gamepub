<?php

if (!defined('SHAISHAI')) {
	exit(1);
}

require_once INSTALLDIR.'/lib/groupeditform.php';

class GroupeditHTMLTemplate extends GroupdesignHTMLTemplate
{

	function title()
	{
		return sprintf('编辑 %s ' . GROUP_NAME() . '', $this->cur_group->uname);
	}

	function getPage()
	{
		//右边导航的位置,从0开始: 公会主页, 公会成员, 编辑公会
		return 2;
	}
	
	
	function showContent()
	{
		$this->tu->showTitleBlock('编辑' . GROUP_NAME() . '', 'groups');
		
		$navs = new NavList_GroupEdit($this->cur_group);
        $this->tu->showTabNav($navs->lists(), $this->trimmed('action'));
		 
		if($this->trimmed('msg', false)) {
			if($this->trimmed('success', false))
				$this->element('div', 'success', $this->trimmed('msg'));
			else				
				$this->element('div', 'error', $this->trimmed('msg'));
		}
		
		$this->showFormContnet();
	}

	function showFormContnet()
	{
		$this->tu->showPageInstructionBlock('使用这个表格来更新' . GROUP_NAME() . '信息，准确的信息能让好友们更容易的找到您的' . GROUP_NAME() . '哦！');
		
    	$this->elementStart('div');
    	if($this->cur_group->groupclass == 1){
			$form = new GroupGameEditForm($this, $this->cur_group, $this->cur_user);
			$form->show();
    	}else{
        	$lifeform = new GroupLifeEditForm($this,  $this->cur_group, $this->cur_user);
			$lifeform->show();
    	}
		$this->elementEnd('div');
	}

	function showStyleSheets() {
    	parent::showStyleSheets();
    	$this->cssLink('css/settings.css','default','screen, projection');
    }

	function showScripts() {
		parent::showScripts();
		$this->script('js/jquery.validate.min.js');
		$this->script('js/lshai_categoryselect.js');
		$this->script('js/lshai_editgroup.js');
	}

}