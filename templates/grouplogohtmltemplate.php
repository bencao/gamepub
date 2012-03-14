<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class GrouplogoHTMLTemplate extends GroupdesignHTMLTemplate
{
	function title()
    {
        return '' . GROUP_NAME() . 'Logo';
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
    	$this->tu->showPageInstructionBlock('为' . GROUP_NAME() . '设置一个有代表性的logo，晒出' . GROUP_NAME() . '的特色!');
    	
        if ($this->trimmed('grouplogo_mode', false) == 'crop') {
            $this->showCropForm();
        } else {
            $this->showUploadForm();
        }
	}

	function _showOriginAvatar() {
		
		$original = $this->cur_group->homepage_logo;
    	
    	$this->elementStart('dl', 'origin');
        $this->element('dt', null, '当前LOGO');

    	$this->elementStart('dd');
    	
    	if ($original) {
	        $this->element('img', array('src' => $original,
	        								'width' => '96',
	        								'height' => '96',
	                                        'alt' => $this->cur_group->nickname));
        } else {
        	 $this->element('img', array('src' => User_group::defaultLogo(GROUP_LOGO_PROFILE_SIZE),
	        								'width' => '96',
	        								'height' => '96',
	                                        'alt' => $this->cur_group->nickname));
        }
        $this->elementEnd('dd');
        $this->elementEnd('dl');
    }
    
    function _showAvatarUpload()
    {
    	$this->element('p', null, '个性群组从Logo开始，上传' . GROUP_NAME() . '的最新Logo。支持JPG/PNG图片，大小不能超过1MB。');
        $this->elementStart('p');
        $this->element('input', array('type' => 'file', 'name' => 'avatarfile', 'id' => 'avatarfile'));
		$this->element('div', array('id' => 'fileQueue', 'uid' => $this->cur_user->id));
		$this->element('input', array('type' => 'hidden', 'name' => 'avatarfilename', 'id' => 'avatarfilename'));
        $this->elementEnd('p');
        
    }
    
    function _showUploadSubmit() {
        $this->element('input', array('id'=> 'upload', 'name' => 'upload', 'type' => 'submit', 'value' => '上传', 'style' => 'display:none;'));
    }
    
    function showUploadForm()
    {

        $this->tu->startFormBlock(array('enctype' => 'multipart/form-data',
                                          'method' => 'post',
                                          'id' => 'settings_uploadavatar',
                                          'class' => 'form_settings',
                                          'action' =>
                                          common_local_url('grouplogo',
                                                           array('id' => $this->cur_group->id))), '修改' . GROUP_NAME() . 'Logo');

        $this->elementStart('dl', 'upload');
        $this->element('dt', 'upload', '修改LOGO');
        
        $this->elementStart('dd', 'upload');
        
        $this->_showOriginAvatar();
        $this->_showAvatarUpload();
        $this->elementEnd('dd');
        $this->elementEnd('dl');
        
        $this->_showUploadSubmit();
        
        $this->tu->endFormBlock();

    }
    
	function _showCropHiddenVars() {
    	// some important hidden variables	
		$this->element('input', array('type' => 'hidden', 'name' => 'ar',
        			'id' => 'ar', 'value' => $this->arg('grouplogo_aspectratio')));
    	foreach (array('avatar_crop_x', 'avatar_crop_y',
                       'avatar_crop_w', 'avatar_crop_h') as $crop_info) {
            $this->element('input', array('name' => $crop_info,
                                          'type' => 'hidden',
                                          'id' => $crop_info));
        }
    }
    
    function _showPreviewAndCrop() {
        $this->elementStart('div', array('style' => 'width:'. $this->args['grouplogo_filedata']['width'] .'px;height:' . $this->args['grouplogo_filedata']['height'] . 'px;', 'class' => 'original'));
        $this->elementStart('div', array('id'=>'avatar_original'));
        $this->element('img', array('src' => Avatar::url($this->args['grouplogo_filedata']['filename'], Avatar::groupsubpath($this->cur_group->id)),
                                    'width' => $this->args['grouplogo_filedata']['width'],
                                    'height' => $this->args['grouplogo_filedata']['height'],
                                    'alt' => $this->cur_group->uname));
        $this->elementEnd('div');
        $this->elementEnd('div');
        
        $this->elementStart('div', 'preview');
        $this->elementStart('div',
                            array('id' => 'avatar_preview', 'style' => 'overflow: hidden; width: 90px; height: 90px; margin-left: 5px;'));
        $this->element('img', array('src' => Avatar::url($this->args['grouplogo_filedata']['filename'], Avatar::groupsubpath($this->cur_group->id)),
                                    'alt' => $this->cur_group->uname));
        $this->elementEnd('div');
        $this->elementEnd('div');
        
    }
    
    
    function _showCropSubmit() {
        $this->elementStart('div', 'op');
        $this->element('input', array('name' => 'crop', 'id' => 'crop', 'type' => 'submit', 'value' => '提交', 'class' => 'submit button76 green76'));
        $this->element('a', array('href' => common_local_url('grouplogo', array('id' =>
	                                                                 $this->cur_group->id)), 'title' => '取消操作，返回设置页面', 'class' => 'cancel'), '取消');
    	$this->elementEnd('div');
    }

    function showCropForm()
    {
    	
        $this->tu->startFormBlock(array('enctype' => 'multipart/form-data',
                                          'method' => 'post',
                                          'id' => 'settings_cropavatar',
                                          'class' => 'form_settings',
                                          'action' =>
                                          common_local_url('grouplogo',
                                                           array('id' => $this->cur_group->id))), '修改' . GROUP_NAME() . 'Logo');

        $this->_showCropHiddenVars();
        
        $this->element('p', null, '通过拖动方框和调整方框大小，截取您想从图片中提取的部分');

        $this->_showPreviewAndCrop();
        
        $this->_showCropSubmit();
        
        $this->tu->endFormBlock();
    }

    /**
     * Add the jCrop stylesheet
     *
     * @return void
     */

    function showStylesheets()
    {
        parent::showStylesheets();
    	$this->cssLink('css/settings.css','default','screen, projection');
        $this->cssLink('css/jcrop.css','default','screen, projection, tv');
    }

    /**
     * Add the jCrop scripts
     *
     * @return void
     */

    function showScripts()
    {
        parent::showScripts();
        if ($this->trimmed('grouplogo_mode', false) == 'crop') {
            $this->element('script', array('type' => 'text/javascript',
                                           'src' => common_path('js/jquery.jcrop.js')));
            $this->element('script', array('type' => 'text/javascript',
                                           'src' => common_path('js/lshai_jcrop.js')));
        } else {
        	$this->script('js/lshai_uploadfile.js');
        }
    }
    
}