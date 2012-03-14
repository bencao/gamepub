<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

require_once INSTALLDIR . '/lib/webcolor.php';

class DesignsettingsHTMLTemplate extends SettingsHTMLTemplate
{	
	/**
     * Title of the page
     *
     * @return string Title of the page
     */
    function title()
    {
        return '自定义皮肤';
    }
    
    function showSettingsTitle() {
    	return '外观设置';
    }
    
    function showSettingsInstruction() {
    	return '定制独一无二的个人页面，充分展示您的个性！';
    }
    
    function _showSubmit() {
    	$this->element('input', array('type' => 'submit', 'value' => '提交', 'class' => 'submit button76 green76'));
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

//            $ccolor = new WebColor($this->design->contentcolor);
//
//            $this->elementStart('p', 'clearfix');
//            $this->element('label', array('for' => 'swatch-2'), '内容文字');
//            $this->element('input', array('name' => 'contentcolor',
//                                          'type' => 'text',
//                                          'id' => 'swatch-2',
//                                          'class' => 'swatch text',
//                                          'maxlength' => '7',
//                                          'size' => '7',
//                                          'value' => '#' . $ccolor->hexValue()));
//            $this->elementEnd('p');

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

//            $tcolor = new WebColor($this->design->textcolor);
//            
//            $this->elementStart('p', 'clearfix');
//            $this->element('label', array('for' => 'swatch-4'), '侧栏文字');
//            
//            $this->element('input', array('name' => 'textcolor',
//                                        'type' => 'text',
//                                        'id' => 'swatch-4',
//                                        'class' => 'swatch text',
//                                        'maxlength' => '7',
//                                        'size' => '7',
//                                        'value' => '#' . $tcolor->hexValue()));
//            $this->elementEnd('p');
//
//            $lcolor = new WebColor($this->design->linkcolor);
//
//            $this->elementStart('p', 'clearfix');
//            $this->element('label', array('for' => 'swatch-5'), '链接');
//            $this->element('input', array('name' => 'linkcolor',
//                                         'type' => 'text',
//                                         'id' => 'swatch-5',
//                                         'class' => 'swatch text',
//                                         'maxlength' => '7',
//                                         'size' => '7',
//                                         'value' => '#' . $lcolor->hexValue()));
//            $this->elementEnd('p');
            
            $this->elementStart('p', array('class' =>'clearfix', 'style' => 'padding-bottom:30px;'));
            $this->element('label', null, '导航条');
            $this->element('input', array('name' => 'navcolor',
            							  'id' => 'navcolor',
            							  'type' => 'hidden',
            							  'value' => ''));
            $this->elementEnd('p');
            
            $this->elementStart('ul', 'nav_pane');
            
            foreach (array('e5630e', 'e1c80c', '91e610',
            	'23e20c', '10e766', '0ee4cd',
            	'e71025', 'e71090', 'c80ce1',
            	'6610e7', '0e24e5', '0d8fe3') as $a) {
	            if (strtolower($this->design->navcolor) == $a) {
	            	$this->elementStart('li', array('style' => 'background-color:#b8510c;'));
	            } else {
	            	$this->elementStart('li');
	            }
	            $this->element('a', array('href' => '#', 'style' => 'background-color:#' . $a . ';', 'colorvalue' => $a));
            	$this->elementEnd('li');
            }
            $this->elementEnd('ul');
            
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
    	
        	$this->element('dt', null, '我的DIY皮肤');
        
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
                                          'action' => common_path('settings/design')), '外观自定义');
	    		
	    		
//	    		$this->tu->startTable();
	    		
//	    		$this->elementStart('tr');
	    		
//	    		$this->elementStart('td', 'b_cbf_l');
//	    		$this->element('label', null, $this->designs->name);
//	    		$this->elementEnd('td');
	    	
//	    		$this->elementStart('td', 'b_cbf_t');
	    		
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
	   			
	   			$this->element('a', array('href' => common_path('settings/design'), 'class' => 'delete', 'title' => '删除该皮肤'), 'X');
	    		
//	    		$this->elementEnd('td');
//	    	
//	    		$this->elementStart('td');
	    		
//	    		$this->element('input', array('type' => 'hidden', 'name' => 'design_id', 'value' => $this->designs->id));
//	    		$this->element('input', array('type' => 'submit', 'name' => 'apply', 'value' => '应用'));
//	    		$this->element('input', array('type' => 'submit', 'name' => 'delete', 'value' => '删除'));
	    		
//	    		$this->elementEnd('td');
//	    		
//	    		$this->element('td');
//	    		
//	    		$this->elementEnd('tr');
//	    		
//	    		$this->tu->endTable();
	    		
//	    		$this->tu->endFormBlock();
	    		
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

//        $this->element('input', array('id' => 'settings_design_reset',
//                                     'type' => 'reset',
//                                     'value' => '重置',
//                                     'title' => '恢复默认',
//        							 'class' => 'reset button76 silver76'));
    	
        $this->element('input', array('type' => 'submit', 'value' => '保存', 'name' => 'save', 'class' => 'submit button76 green76'));
        
    	$this->elementEnd('div');
    }
    
    function _showTemplates() {
    	$this->tu->startFormBlock(array('method' => 'post',
                                          'enctype' => 'multipart/form-data',
                                          'id' => 'settings_design',
                                          'class' => 'form_settings',
                                          'action' => common_path('settings/design')), '外观自定义');
        
        $this->elementStart('dl');
    	
        $this->element('dt', null, '模板选择');
        
        $this->elementStart('dd');
        
        $this->elementStart('ul', 'clearfix');
        
    	$templates = $this->arg('templates');
    	
//    	foreach ($templates as $t) {
//    		$this->elementStart('li', 'clearfix');
//    		
//    		$this->elementStart('a', array('class' => 'rounded5', 'title' => 'def', 'href' => '#'));
//    		
//    		$this->element('img', array('alt' => 'def', 'src' => '#'));
//    		
//    		$this->elementEnd('a');
//    		
//    		$this->element('span', 'def');
//    		
//    		$this->elementEnd('li');
//    	}
    	
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
                                          'action' => common_path('settings/design')), '自定义皮肤');
        
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
    
    function showSettingsContent() {
    	
    	$this->design = $this->arg('design');
    	
//    	$this->_showTemplates();
    	
    	$this->_showSelfDesigns();
    	
    	$this->_showTheDesign();
    	
    }
    
    function showScripts() {
    	parent::showScripts();
//    	$this->script('js/jquery.validate.min.js');
    	$this->script('js/farbtastic/farbtastic.js');
    	$this->script('js/lshai_designsettings.js');
    }
    
    function showStyleSheets() {
    	parent::showStyleSheets();
    	$this->cssLink('css/farbtastic.css','default','screen, projection, tv');
    }
}

?>