<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class GroupdesignsettingsHTMLTemplate extends GroupdesignHTMLTemplate
{
    function title()
    {
        return '自定义' . GROUP_NAME() . '皮肤';
    }
    
	function getPage()
	{
		//右边导航的位置,从0开始: 公会主页, 公会成员, 编辑公会
		return 2;
	}
	
	function showRightsidebar()
    {  	
		$this->groupAction();
    	
    	$this->showGroupInfo();
    	
//    	$this->showGroupNav();
    	
    	$this->showGroupMembers();
    	
    	$this->showGroupHotTags();
    }
    
	function showContent() {
		$this->tu->showTitleBlock(''. GROUP_NAME() . '外观', 'groups');
		
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
        $this->tu->showPageInstructionBlock('定制独一无二的群组页面，充分展示群组个性！');

    	$this->design = $this->arg('design');
    	
    	$this->_showSelfDesigns();
    	
    	$this->_showTheDesign();
	}
    
    function _showSubmit() {
    	$this->element('input', array('type' => 'submit', 'value' => '提交', 'class' => 'submit button76 green76'));
    }
    
    function _showName() {
    	$this->elementStart('tr');
    	$this->elementStart('td', 'b_cbf_l');
	    $this->element('label', array('for' => 'design_name'), '取个名字');
	    $this->elementEnd('td');
    	$this->elementStart('td', 'b_cbf_t');
    	$this->element('input', array('type' => 'text', 'name' => 'design_name', 'id' => 'design_name'));
    	$this->elementEnd('td');
    	$this->element('td');
    	$this->elementEnd('tr');
    }
    
    function _showBackgroundUpload() {
        $this->elementStart('div', 'bg_panel');
        $this->elementStart('p');
        $this->text('上传背景图片');
        $this->element('span', null, '(支持1M以内的JPG,PNG图片)');
        $this->elementEnd('p');
        
        $this->elementStart('p');
        $this->element('input', array('type' => 'file', 'name' => 'bgfile', 'id' => 'bgfile'));
		$this->element('div', array('id' => 'fileQueue', 'uid' => $this->cur_user->id));
		$this->element('input', array('type' => 'hidden', 'name' => 'backgroundimage', 'id' => 'backgroundimage'));
		$this->element('input', array('type' => 'hidden', 'name' => 'showimage', 'id' => 'showimage'));
        $this->elementEnd('p');
        
        $this->elementStart('p', 'attachment');
        
        $this->element('input', array('class' => 'radio', 'type' => 'radio', 'id' => 'bgl', 'name' => 'disposition', 'value' => '1'));
        $this->element('label', array('for' => 'bgl'), '居左');
        
        $this->element('input', array('class' => 'radio', 'type' => 'radio', 'id' => 'bgc', 'name' => 'disposition', 'value' => '2'));
        $this->element('label', array('for' => 'bgc'), '居中');
        
        $this->element('input', array('class' => 'radio', 'type' => 'radio', 'id' => 'bgr', 'name' => 'disposition', 'value' => '3'));
        $this->element('label', array('for' => 'bgr'), '居右');
        
        $this->element('input', array('class' => 'checkbox', 'type' => 'checkbox', 'id' => 'bgf', 'name' => 'bgrepeat'));
        $this->element('label', array('for' => 'bgf'), '平铺');
        
        $this->element('input', array('class' => 'checkbox', 'type' => 'checkbox', 'id' => 'bgfix', 'name' => 'bgfix'));
        $this->element('label', array('for' => 'bgfix'), '背景固定');
        
        $this->elementEnd('p');
        
        $this->elementStart('a', array('class' => 'do_not_show_bg', 'href' => '#'));
        $this->element('img', array('src' => common_path('theme/default/i/selfdesign_donotshowbg.png')));
        $this->elementEnd('a');
        
        $this->elementStart('a', array('class' => 'show_bg', 'href' => '#'));
        $this->element('img', array('src' => common_path('theme/default/i/selfdesign_showbg.png')));
        $this->elementEnd('a');
        
        $this->elementEnd('div');
    }
    
    function _showColorChooser() {
    	try {

            $bgcolor = new WebColor($this->design->backgroundcolor);

            $this->elementStart('div', array('id' => 'settings_design_color', 'class' => 'color_panel'));
            $this->elementStart('p', 'clearfix');
            $this->element('label', array('for' => 'swatch-1'), '背景');
            $this->element('input', array('name' => 'backgroundcolor',
                                          'type' => 'text',
                                          'id' => 'swatch-1',
                                          'class' => 'swatch text',
                                          'maxlength' => '7',
                                          'size' => '7',
                                          'value' => '#' . $bgcolor->hexValue()));
            $this->elementEnd('p');

            $sbcolor = new WebColor($this->design->sidebarcolor);

            $this->elementStart('p', 'clearfix');
            $this->element('label', array('for' => 'swatch-3'), '侧栏');
            $this->element('input', array('name' => 'sidebarcolor',
                                        'type' => 'text',
                                        'id' => 'swatch-3',
                                        'class' => 'swatch text',
                                        'maxlength' => '7',
                                        'size' => '7',
                                        'value' => '#' . $sbcolor->hexValue()));
            $this->elementEnd('p');
            
            $this->elementStart('p', 'clearfix');
		    $this->element('label', array('for' => 'design_name'), '皮肤名字');
	    	$this->element('input', array('type' => 'text', 'class' => 'text', 'name' => 'design_name', 'id' => 'design_name'));
	    	$this->elementEnd('p');
            
            $this->element('div', array('class' => 'circle', 'id' => 'color-picker', 'style' => 'display:none;'));
            
            $this->elementEnd('div');

        } catch (WebColorException $e) {
            common_log(LOG_ERR, 'Bad color values in design ID: ' . $this->design->id);
        }
    }
    
    function _showSelfDesigns() {
    	
    	$this->designs = $this->trimmed('self_designs');
    	
    	
    	if ($this->designs && $this->designs->N > 0) {
	    	
    		$this->elementStart('dl', array('id' => 'self_designs'));
    	
        	$this->element('dt', null, GROUP_NAME() . '的DIY皮肤');
        
        	$this->elementStart('dd');
        	
    		$this->elementStart('ul', 'clearfix');
    		
	    	while ($this->designs->fetch()) {
	    		
	    		if ($this->designs->id == $this->design->id) {
	    			$this->elementStart('li', array('dsid' => $this->designs->id, 'class' => 'active'));
	    		} else {
	    			$this->elementStart('li', array('dsid' => $this->designs->id));
	    		}
	    		
	    		$this->tu->startFormBlock(array('method' => 'post',
                                          'enctype' => 'multipart/form-data',
                                          'id' => 'form_settings_design',
                                          'class' => 'form_settings',
                                          'action' => common_local_url('groupdesignsettings', array('id' =>$this->cur_group->id))), '外观自定义');
	    		
	    		$this->elementStart('a', array('class' => 'apply', 'href' => '#', 'title' => '点击应用该皮肤'));
	    		$this->element('img', array('alt' => '点击应用该皮肤', 
	    			'src' => $this->designs->backgroundimage, 'height' => '75', 'width' => '100'));
	   			$this->elementEnd('a');
	   			
	   			$this->element('input', array('type' => 'hidden', 'name' => 'design_id', 'value' => $this->designs->id));
	   			$this->element('input', array('type' => 'hidden', 'name' => 'apply', 'value' => '1'));
	   			
	   			$this->tu->endFormBlock();
	   			
	   			$this->elementStart('p');
	   			$this->text($this->designs->name);
	   			$this->elementEnd('p');
	   			
	   			$this->element('a', array('href' => common_local_url('groupdesignsettings', array('id' =>$this->cur_group->id)), 'class' => 'delete', 'title' => '删除该皮肤'), 'X');
	    		
	    		
	    		$this->elementEnd('li');
	    	}
	    	
	    	$this->elementEnd('ul');
	    	$this->elementEnd('dd');
	    	$this->elementEnd('dl');
    	}
    }
    
    function _showOperations() {
    	$this->elementStart('div', 'op');

    	$this->element('input', array('type' => 'submit', 'value' => '恢复默认', 'name' => 'restoredefault', 'class' => 'restoredefault button76 silver76'));
    	
        $this->element('input', array('type' => 'submit', 'value' => '保存', 'name' => 'save', 'class' => 'submit button76 green76'));
        
    	$this->elementEnd('div');
    }
    
    function _showTemplates() {
    	$this->tu->startFormBlock(array('method' => 'post',
                                          'enctype' => 'multipart/form-data',
                                          'id' => 'settings_design',
                                          'class' => 'form_settings',
                                          'action' => common_local_url('groupdesignsettings', array('id' =>$this->cur_group->id))), '外观自定义');
        
        $this->elementStart('dl');
    	
        $this->element('dt', null, '模板选择');
        
        $this->elementStart('dd');
        
        $this->elementStart('ul', 'clearfix');
        
    	$templates = $this->arg('templates');
    	
    	$this->elementEnd('ul');
    	
    	$this->elementEnd('dd');
    	
    	$this->elementEnd('dl');
    	
    	$this->elementStart('div', 'op');
    	
    	$this->element('input', array('class' => 'submit button76 green76', 'type' => 'submit', 'value' => '保存'));
    	
    	$this->elementEnd('div');
		
        $this->tu->endFormBlock();
    }
    
    function _showTheDesign() {
    	$this->tu->startFormBlock(array('method' => 'post',
                                          'enctype' => 'multipart/form-data',
                                          'id' => 'settings_self_design',
                                          'class' => 'form_settings',
                                          'action' => common_local_url('groupdesignsettings', array('id' =>$this->cur_group->id))), '自定义皮肤');
        
        $this->elementStart('dl');
    	
        $this->element('dt', null, '自定义皮肤');
        
        $this->elementStart('dd');
        
    	$this->_showBackgroundUpload();
    	
    	$this->_showColorChooser();
    	
    	$this->elementEnd('dd');
    	
    	$this->elementEnd('dl');
    	
    	$this->_showOperations();
		
        $this->tu->endFormBlock();
    }
    
    function showSettingsTip()
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
    	$this->script('js/farbtastic/farbtastic.js');
    	$this->script('js/lshai_designsettings.js');
    }
    
    function showStyleSheets() {
    	parent::showStyleSheets();
    	$this->cssLink('css/settings.css','default','screen, projection, tv');
    	$this->cssLink('css/farbtastic.css','default','screen, projection, tv');
    }
}

?>