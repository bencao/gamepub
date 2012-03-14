<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class ShowgroupHTMLTemplate extends GroupdesignHTMLTemplate
{

    /**
     * Title of the page
     *
     * @return string page title, with page number
     */
    function title()
    {
        if (!empty($this->cur_group->nickname)) {
            $base = $this->cur_group->nickname . ' (' . $this->cur_group->uname . ')';
        } else {
            $base = $this->cur_group->uname;
        }

        return $base.GROUP_NAME();
    }
    

    function showCore() {
		$this->elementStart('div', array('id' => 'contents', 'class' => 'clearfix rounded5l'));
		$this->showContent();
		$this->showGroupMusicPlayer();
		$this->elementEnd('div');
		
    	$this->elementStart('div', array('id' => 'widgets'));
		$this->showRightside();
		$this->elementEnd('div');
    }

    /**
     * Show the page content
     *
     * Shows a group profile and a list of group notices
     */

    function showContent()
    {
        if ($this->cur_user && $this->cur_group->hasMember($this->cur_user)) {
        	$nform = new ShaiNoticeForm($this, null, null, null, 'group', $this->cur_group->uname);
    	    $nform->show();
        	$this->showMiddlePart();
        }else{
        	$gc = '生活';
        	if($this->cur_group->groupclass){
        		$gc = '游戏';
        	}
			$this->tu->showTitleBlock($gc . GROUP_NAME() . '-' . $this->cur_group->uname, 'groups');
        	$this->showNotMemberNotice();
        }   	
        
        if ($this->cur_user && $this->cur_group->hasMember($this->cur_user)) {
        	$this->tu->showNewContentFilterBoxBlock($this->cur_user->getProfile(), $this->args['filter_content'], $this->args['tag'], 'showgroup', array('id' => $this->cur_group->id));
        }
        $this->showGroupNotices();
        
    	if ($this->is_group_admin && $this->trimmed('theme') == 'true') {
    		$this->_showThemeRoller();
    	}
    }

    function showMiddlePart()
    {
    	if(!is_null($this->cur_group->post) && $this->cur_group->post != ''){
		    $this->elementStart('div', array('id'=>'bubbles', 'class' => 'clearfix'));
		    $this->elementStart('div', array('class' => 'msg'));
		    $this->element('strong', null, '公告：');
		    $this->text($this->cur_group->post);
		    $this->elementEnd('div');
		    if($this->is_group_admin){
			    $this->elementStart('ul', array('class' => 'op clearfix'));
			    $this->elementStart('li');
			    $this->element('a', array('class' => 'close group_post_del', 'href' => common_local_url('groupdeletepost', array('id' => $this->cur_group->id))));
			    $this->elementEnd('li');
			    $this->elementStart('li');
			    $this->element('a', array('class' => 'edit', 'href' => common_local_url('groupeditpost', 
					array('id' =>$this->cur_group->id))));
			    $this->elementEnd('li');
			    $this->elementEnd('ul');
		    }
		    $this->elementEnd('div');
		    
    	}
    }
    
    function showNotMemberNotice()
    {
		$this->groupAction();
    	if($this->cur_group->grouptype){
    		$this->elementStart('div', array('class' => 'group_msg'));
    		$this->text('您不是该' . GROUP_NAME() . '的成员，目前无法向' . GROUP_NAME() . 
    			'发送消息。此' . GROUP_NAME() . '是私有的，您可以申请加入！');
    		$this->elementEnd('div');
    		$this->elementStart('div', array('class' => 'group_join'));
    		if($this->symbol == 2){
    			$this->element('a', array('class' => 'button94 green94 applied', 'href' =>'#'), '已申请');
    		}else if($this->symbol == 3){
    			$this->element('a', array('class' => 'button94 green94 apply', 'href' => 
    				common_path('group/' . $this->cur_group->id . '/applyjoin')), '申请加入');
    		}else if($this->symbol == 5){
    			$this->element('a', array('class' => 'button94 green94 applied', 'href' =>'#'), '入口已关闭');
    		}
    		$this->text('加入' . $this->cur_group->uname . GROUP_NAME() .'，快速结识志同道合的朋友。');
    		$this->elementEnd('div');
    	}else{
    		$this->elementStart('div', array('class' => 'group_msg'));
    		$this->text('您不是该' . GROUP_NAME() . '的成员，目前无法向' . GROUP_NAME() . 
    			'发送消息。此' . GROUP_NAME() . '是公有的，您可以直接加入！');
    		$this->elementEnd('div');
    		$this->elementStart('div', array('class' => 'group_join'));
    		$this->element('a', array('class' => 'button94 green94 group_join', 'href' => 
    			common_path('group/' . $this->cur_group->id . '/join')), '加入' . GROUP_NAME());
            $this->elementEnd('div');
    	}
    }
    /**
     * Show the group notices
     *
     * @return void
     */

    function showGroupNotices()
    {
        if (!$this->cur_group->grouptype || $this->cur_group->hasMember($this->cur_user))
        {
	    	$notice = $this->args['notice'];
	
	        $nl = new GroupNoticeList($this->args['notice'], $this, $this->cur_group);
	        $cnt = $nl->show();
	
	        if ($cnt > GROUP_NOTICES_PER_PAGE) {
	        	$tag = $this->args['tag'];
	       		$filter_content = $this->args['filter_content'];
		    	$params = array();
		    	if ($tag) {
		    		$params = array_merge($params, array('tag' => $tag));
		    	}
		    	if ($filter_content) {
		    		$params = array_merge($params, array('filter_content' => $filter_content));
		    	}
		        
				$this->morepagination($this, $cnt > GROUP_NOTICES_PER_PAGE, $this->cur_page, 'showgroup', array('id' => $this->cur_group->id), $params);
	        }
        }
        else
        {
        	$emptymsg = array();
        	if($this->cur_group->closed){
        		$emptymsg['dt'] = '这个' . GROUP_NAME() . '是私有' . GROUP_NAME() . '，非此' . GROUP_NAME() . '用户无法看到' . GROUP_NAME() . '内部消息！';
	            $emptymsg['dd'] = '该' . GROUP_NAME() . '的入口已经关闭，暂时无法加入。欲获得更详细的信息，请与该' . GROUP_NAME() . '的管理员联系！';
        	}else{
	            $emptymsg['dt'] = '这个' . GROUP_NAME() . '是私有' . GROUP_NAME() . '，非此' . GROUP_NAME() . '用户无法看到' . GROUP_NAME() . '内部消息！';
	            $emptymsg['dd'] = '您可以点击' . GROUP_NAME() . '申请加入按钮，输入申请内容来申请加入这个' . GROUP_NAME() . '！';
        	}
            $this->tu->showEmptyListBlock($emptymsg);
        }
    }
    
    // if there is no notice, we show this
    function showEmptyList()
    {
        $message = sprintf('这是' . GROUP_NAME() . '  %s 成员的消息列表 , 但是还没人发消息.', 
        		           $this->cur_group->uname) . ' ';
        $emptymsg = array();
        $emptymsg['p'] = $message;
        $emptymsg['p'] = '快打破这个寂静，秀出此' . GROUP_NAME() . '的第一条消息吧。';
        $this->tu->showEmptyListBlock($emptymsg);
    }

    function showPageNotices($args) {
        $this->args = $args;
        $this->cur_group = $this->args['cur_group'];
        $this->cur_page = $this->args['page'];
    		
        $view = TemplateFactory::get('JsonTemplate');
        $view->init_document();
        
        $notice = $this->args['notice'];
        if ($notice->N > 0) {
        	$xs = new XMLStringer();
        	$nl = new GroupNoticeList($this->args['notice'], $xs, $this->cur_group);
       		$cnt = $nl->show();
        	
        	$xs1 = new XMLStringer();
       		if ($cnt > GROUP_NOTICES_PER_PAGE) {
       			$tag = $this->args['tag'];
       			$filter_content = $this->args['filter_content'];
	    		$params = array();
		    	if ($tag) {
		    		$params = array_merge($params, array('tag' => $tag));
		    	}
		    	if ($filter_content) {
		    		$params = array_merge($params, array('filter_content' => $filter_content));
		    	}
	        
				$this->morepagination($xs1, $cnt > GROUP_NOTICES_PER_PAGE, $this->cur_page, 'showgroup', array('id' => $this->cur_group->id), $params);
        	}
        	       	
        	$resultArray = array('result' => 'true', 'notices' => $xs->getString(), 'pg' => $xs1->getString()); 	
        } else {
        	$resultArray = array('result' => 'false');
        }

	    $view->show_json_objects($resultArray);
        $view->end_document();
    }
    

	function showStylesheets()
    {
    	parent::showStylesheets();
        $this->cssLink('css/settings.css','default','screen, projection');
        if ($this->is_group_admin
    		&& $this->trimmed('theme') == 'true') {
    		$this->cssLink('css/farbtastic.css', 'default', 'screen, projection, tv');
    		$this->cssLink('css/themeroller.css', 'default', 'screen, projection, tv');
    	}
    }
    
    function showShaishaiScripts() {
    	parent::showShaishaiScripts();
    	
    	$this->script('js/lshai_privategroupjoin.js');
    	
    	if ($this->is_group_admin && $this->trimmed('theme') == 'true') {
    		$this->script('js/farbtastic/farbtastic.js');
    		$this->script('js/lshai_designsettings.js');
    	}
    }
    
    function _showThemeRoller() {
    	$this->elementStart('div', array('id' => 'theme_roller'));
    	$this->elementStart('div', 'theme_title');
    	$this->text('皮肤设置面板');
    	$this->element('a', array('href' => '#', 'class' => 'close'), '关闭');
    	$this->element('a', array('href' => '#', 'class' => 'min'), '最小化');
    	$this->elementEnd('div');
    	$this->elementStart('div', 'theme_body');
    	$this->design = $this->arg('design');
    	$this->_showTemplates();
    	$this->_showSelfDesigns();
    	$this->_showTheDesign();
    	$this->elementEnd('div');
    	$this->elementEnd('div');
    }
    
	function _showTemplates() {
    	$this->templates = $this->trimmed('official_designs');
    	
    	if ($this->templates && $this->templates->N > 0) {
	    	
    		$this->elementStart('dl', array('id' => 'template_designs'));
    	
        	$this->elementStart('dt');
        	$this->element('a', array('href' => '#'), '官方皮肤');
        	$this->elementEnd('dt');
        	
        	$this->elementStart('dd');
        	
    		$this->elementStart('ul', 'clearfix');
    		
	    	while ($this->templates->fetch()) {
	    		
	    		if ($this->templates->id == $this->design->id) {
	    			$this->elementStart('li', array('dsid' => $this->templates->id, 'class' => 'active'));
	    		} else {
	    			$this->elementStart('li', array('dsid' => $this->templates->id));
	    		}
	    		
	    		$this->tu->startFormBlock(array('method' => 'post',
                                          'enctype' => 'multipart/form-data',
                                          'class' => 'self_design_settings',
                                          'action' => common_local_url('groupdesignsettings', array('id' =>$this->cur_group->id))), '外观自定义');
	    		if ($this->templates->backgroundimage) {
	    			$this->elementStart('a', array('class' => 'apply', 'href' => '#', 'title' => '点击应用这个皮肤'));
	    			$this->element('img', array('alt' => '点击应用这个皮肤', 
	    				'src' => $this->templates->backgroundimage, 'height' => '75', 'width' => '100'));
	    		} else {
	    			$bgcolor = new WebColor($this->design->backgroundcolor);
	    			$this->elementStart('a', array('class' => 'apply', 'href' => '#', 'title' => '点击应用这个皮肤', 'style' => 'background-color:#' . $bgcolor->hexValue() . ';'));
	    			$this->text('点击应用这个皮肤');
	    		}
	   			$this->elementEnd('a');
	   			
	   			$this->element('input', array('type' => 'hidden', 'name' => 'design_id', 'value' => $this->templates->id));
	   			$this->element('input', array('type' => 'hidden', 'name' => 'apply', 'value' => '1'));
	   			
	   			$this->tu->endFormBlock();
	   			
	   			$this->elementStart('p');
	   			$this->text($this->templates->name);
	   			$this->elementEnd('p');
	   			
//	   			$this->element('a', array('href' => common_local_url('designsettings'), 'class' => 'delete', 'title' => '删除这个皮肤'), 'X');
	    		
	    		$this->elementEnd('li');
	    	}
	    	
	    	$this->elementEnd('ul');
	    	$this->elementEnd('dd');
	    	$this->elementEnd('dl');
    	}
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

            $ccolor = new WebColor($this->design->contentcolor);

            $this->elementStart('p', 'clearfix');
            $this->element('label', array('for' => 'swatch-2'), '内容文字');
            $this->element('input', array('name' => 'contentcolor',
                                          'type' => 'text',
                                          'id' => 'swatch-2',
                                          'class' => 'swatch text',
                                          'maxlength' => '7',
                                          'size' => '7',
                                          'value' => '#' . $ccolor->hexValue()));
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

            $tcolor = new WebColor($this->design->textcolor);
            
            $this->elementStart('p', 'clearfix');
            $this->element('label', array('for' => 'swatch-4'), '侧栏文字');
            
            $this->element('input', array('name' => 'textcolor',
                                        'type' => 'text',
                                        'id' => 'swatch-4',
                                        'class' => 'swatch text',
                                        'maxlength' => '7',
                                        'size' => '7',
                                        'value' => '#' . $tcolor->hexValue()));
            $this->elementEnd('p');

            $lcolor = new WebColor($this->design->linkcolor);

            $this->elementStart('p', 'clearfix');
            $this->element('label', array('for' => 'swatch-5'), '链接');
            $this->element('input', array('name' => 'linkcolor',
                                         'type' => 'text',
                                         'id' => 'swatch-5',
                                         'class' => 'swatch text',
                                         'maxlength' => '7',
                                         'size' => '7',
                                         'value' => '#' . $lcolor->hexValue()));
            $this->elementEnd('p');
            
            $this->elementStart('p', array('class' =>'clearfix', 'style' => 'padding-bottom:24px;'));
            $this->element('label', null, '导航条');
            $this->element('input', array('name' => 'navcolor',
            							  'id' => 'navcolor',
            							  'type' => 'hidden',
            							  'value' => strtolower($this->design->navcolor)));
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
    	
        	$this->elementStart('dt');
        	$this->element('a', array('href' => '#'), GROUP_NAME() . 'DIY皮肤');
        	$this->elementEnd('dt');
        	
        	$this->elementStart('dd', array('style' => 'display:none;'));
        	
    		$this->elementStart('ul', 'clearfix');
    		
	    	while ($this->designs->fetch()) {
	    		
	    		if ($this->designs->id == $this->design->id) {
	    			$this->elementStart('li', array('dsid' => $this->designs->id, 'class' => 'active'));
	    		} else {
	    			$this->elementStart('li', array('dsid' => $this->designs->id));
	    		}
	    		
	    		$this->tu->startFormBlock(array('method' => 'post',
                                          'enctype' => 'multipart/form-data',
                                          'class' => 'self_design_settings',
                                          'action' => common_local_url('groupdesignsettings', array('id' =>$this->cur_group->id))), '外观自定义');
	    		
	    		
//	    		$this->tu->startTable();
	    		
//	    		$this->elementStart('tr');
	    		
//	    		$this->elementStart('td', 'b_cbf_l');
//	    		$this->element('label', null, $this->designs->name);
//	    		$this->elementEnd('td');
	    	
//	    		$this->elementStart('td', 'b_cbf_t');
	    		
	    		
	    		if ($this->designs->backgroundimage) {
	    			$this->elementStart('a', array('class' => 'apply', 'href' => '#', 'title' => '点击应用这个皮肤'));
	    			$this->element('img', array('alt' => '点击应用这个皮肤', 
	    				'src' => $this->designs->backgroundimage, 'height' => '75', 'width' => '100'));
	    		} else {
	    			$bgcolor = new WebColor($this->design->backgroundcolor);
	    			$this->elementStart('a', array('class' => 'apply', 'href' => '#', 'title' => '点击应用这个皮肤', 'style' => 'background-color:#' . $bgcolor->hexValue() . ';'));
	    			$this->text('点击应用这个皮肤');
	    		}
	   			$this->elementEnd('a');
	   			
	   			$this->element('input', array('type' => 'hidden', 'name' => 'design_id', 'value' => $this->designs->id));
	   			$this->element('input', array('type' => 'hidden', 'name' => 'apply', 'value' => '1'));
	   			
	   			$this->tu->endFormBlock();
	   			
	   			$this->elementStart('p');
	   			$this->text($this->designs->name);
	   			$this->elementEnd('p');
	   			
	   			$this->element('a', array('href' => common_local_url('groupdesignsettings', array('id' =>$this->cur_group->id)), 'class' => 'delete', 'title' => '删除这个皮肤'), 'X');
	    		
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
    
    function _showTheDesign() {
    	$this->tu->startFormBlock(array('method' => 'post',
                                          'enctype' => 'multipart/form-data',
                                          'id' => 'settings_self_design',
                                          'class' => 'form_settings',
                                          'action' => common_local_url('groupdesignsettings', array('id' =>$this->cur_group->id))), '自定义皮肤');
        
        $this->elementStart('dl');
    	
        $this->elementStart('dt');
        $this->element('a', array('href' => '#'), '自定义皮肤');
        $this->elementEnd('dt');
        
        $this->elementStart('dd', array('style' => 'display:none;'));
        
    	$this->_showBackgroundUpload();
    	
    	$this->_showColorChooser();
    	
    	$this->_showOperations();
    	
    	$this->elementEnd('dd');
    	
    	$this->elementEnd('dl');
		
        $this->tu->endFormBlock();
    }
}

class GroupNoticeList extends NoticeList
{   
	var $group;
	
	function __construct($notice, $out, $group)
    {
        parent::__construct($notice, $out);
        $this->group = $group;
    }
    
	function show()
    {
    	$this->out->elementStart('ol', array('id' => 'notices'));
        $cnt = 0;

        while ($this->notice != null 
        	&& $this->notice->fetch()) {
            $cnt++;
            
            if ($cnt > GROUP_NOTICES_PER_PAGE) {
                break;
            }
                        
            $item = $this->newListItem($this->notice);
            $item->show();
        }
        
        if (0 == $cnt) {
            $this->out->showEmptyList();
        }
        
        $this->out->elementEnd('ol');
        
        return $cnt;
    }
    
    function newListItem($notice)
    {
        return new GroupNoticeListItem($notice, $this->out, $this->group);
    }
}

class GroupNoticeListItem extends NoticeListItem
{
	var $group;
	
	function __construct($notice, $out, $group)
    {
        parent::__construct($notice, $out);
        $this->group = $group;
    }
    
    function showNoticeOptions()
    {
    	if ($this->user) {
            $this->out->elementStart('ul', array('class' => 'op'));
            if($this->notice->user_id != $this->user->id) {
            	$this->showDiscusslink();
            	$this->out->element('li', null, '|');	
	            $this->showFaveForm();
//	            $this->out->element('li', null, '|');	
//            	$this->showReplyLink();
            } else {
            	$this->showDiscusslink();
            	$this->out->element('li', null, '|');	
	            $this->showFaveForm();
	            $this->out->element('li', null, '|');	
	            $this->showDeleteLink();   
            }            
            $this->out->elementEnd('ul');
        }
    }
    
	function showNickname() {
    	$this->out->elementStart('h3'); 
        $this->out->element('a', array('href' => common_local_url('showstream', array('uname' => $this->profile->uname)),
        			'class' => 'name', 'title' => '去' . $this->profile->nickname . '在' . common_config('site', 'name') . '的主页看看'), $this->profile->nickname);
        if ($this->profile->is_vip) {
        	$this->out->element('strong', null, 'V');
        }
        
        // is life group
        if ($this->group->groupclass == 0) {
	        $game_server = Game_server::staticGet('id', $this->profile->game_server_id);
	    	$game = Game::staticGet('id', $this->profile->game_id);    	
	    	$text = sprintf('<span class="tag"><a rel="tag" target="_blank" title="去看看%s的最新动态" href="%s">%s</a>- %s</span>',
    			$game->name, common_local_url('recentnews', array('gameid' => $game->id)), $game->name, $game_server->name);
	    				
	    	$this->out->raw($text);
        }
    	
        $this->out->elementEnd('h3');
    }
    
}
