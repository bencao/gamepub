<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

require_once INSTALLDIR.'/lib/groupeditform.php';

class NewgamegroupHTMLTemplate extends GroupsbaseHTMLTemplate
{
    function title()
    {
        return '新建游戏' . GROUP_NAME();
    }
	
    function showContent()
    {
    	$this->tu->showTitleBlock('创建' . GROUP_NAME() . '', 'favorites');
    	if($this->arg('page_msg')) {
    		if ($this->arg('page_success')) {
    			$this->tu->showPageHighlightBlock($this->args['page_msg']);
    		} else {
    			$this->tu->showPageErrorBlock($this->args['page_msg']);
    		}
    	}
    	
    	if($this->arg('issubmit')) {
    		$this->tu->showPageInstructionBlock('请填写以下信息来创建您的' . GROUP_NAME() . '，准确的信息能让好友们更容易的找到您的' . GROUP_NAME() . '哦！');
	        $form = new GroupGameEditForm($this, null, $this->cur_user);
	        $form->show();
    	}else{
    		$this->tu->showPageInstructionBlock('由于您的等级过低或者您建的游戏' . GROUP_NAME() . '数目已经达到上限，不能再创建新的游戏' . GROUP_NAME() . '。');
    	}
    }
    

	function showStyleSheets() {
    	parent::showStyleSheets();
    	$this->cssLink('css/settings.css','default','screen, projection');
    }
    
    function showScripts() {
		parent::showScripts();
		$this->script('js/lshai_cityschooldata.js');
		$this->script('js/lshai_cityschoolselect.js');
		$this->script('js/jquery.validate.min.js');
		$this->script('js/lshai_categoryselect.js');
		$this->script('js/lshai_newgroup.js');
	}
    
}
