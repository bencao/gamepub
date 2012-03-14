<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class FeedsettingsHTMLTemplate extends SettingsHTMLTemplate
{
	function show($args) {
		$this->feedurl = $args['feedurl'];
		parent::show($args);
	}
	/**
     * Title of the page
     *
     * @return string Title of the page
     */
    function title()
    {
        return '导入设置';
    }
    
    function showSettingsTitle() {
    	return '导入设置';
    }
    
    function showSettingsInstruction() {
    	return '您可以填写博客或其他微博的Feed地址，GamePub机器人会自动将你的新消息导入到GamePub。';
    }
    
    function _showImportInput() {
    	$this->elementStart('p', 'clearfix');
    	$this->element('label', array('for' => 'confirm', 'class' => 'label60'), 'Feed地址');
        $this->element('input', array('type' => 'text', 'name' => 'feedurl', 'id' => 'feedurl', 'class' => 'text300', 'value' => $this->feedurl));
        $this->elementEnd('p');
    }
    
    function _showSubmit() {
    	$this->element('input', array('type' => 'submit', 'value' => '保存', 'name' => 'submit', 'class' => 'submit button76 green76'));
    }
    
    function showSettingsContent() {
    	
    	$this->tu->startFormBlock(array('method' => 'POST',
                                          'id' => 'form_feed',
                                          'class' => 'settings',
                                          'action' =>
                                          common_local_url('feedsettings')), '导入设置');
        
        $this->elementStart('dl');
        
        $this->element('dt', null, '');
        
        $this->elementStart('dd');
        
        $this->_showImportInput();
    	
    	$this->elementEnd('dd');
        $this->elementEnd('dl');
        
        $this->elementStart('div', 'op');
        
        $this->_showSubmit();
        
        $this->elementEnd('div');
        
        $this->tu->endFormBlock();
        
        $this->tu->startFormBlock(array('method' => 'POST',
                                          'id' => 'form_feed',
                                          'class' => 'settings',
                                          'action' =>
                                          common_local_url('feedsettings')), '导入设置');
        
        $this->elementStart('dl');
        
        $this->element('dt', null, '');
        
        $this->elementStart('dd');
        
        $this->elementStart('p', 'clearfix');
        $this->element('label', null, '您也可以手动从Feed源导入，请点击下方按钮');
        $this->elementEnd('p');
        
        $this->elementEnd('dd');
        $this->elementEnd('dl');
                                          
        $this->elementStart('div', 'op');
        
        $this->element('input', array('type' => 'submit', 'value' => '立即导入', 'name' => 'import', 'class' => 'submit button76 green76'));
        
        $this->elementEnd('div');
        
         $this->tu->endFormBlock();
    }
    
//    function showScripts() {
//    	parent::showScripts();
//    	$this->script('js/jquery.validate.min.js');
//    	$this->script('js/lshai_passwordsetting.js');
//    }
}

?>