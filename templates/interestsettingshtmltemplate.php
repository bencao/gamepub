<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class InterestsettingsHTMLTemplate extends SettingsHTMLTemplate 
{
    
	/**
     * Title of the page
     *
     * @return string Title of the page
     */

    function title()
    {
        return '设置兴趣';
    }
    
    function showSettingsTitle() {
    	return '兴趣设置';
    }
    
    function showSettingsInstruction() {
    	return '为了更容易找到志同道合的好友，请您准确设置自己的兴趣';
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
    
    function showSettingsContent()
    {
    	$this->tu->startFormBlock(array('method' => 'post',
                                           'id' => 'settings_interest',
                                           'class' => 'form_settings',
                                           'action' => common_local_url('interestsettings')), '设置兴趣');
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

?>