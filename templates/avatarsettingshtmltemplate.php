<?php


if (!defined('SHAISHAI')) {
    exit(1);
}

class AvatarsettingsHTMLTemplate extends SettingsHTMLTemplate
{
	
    function title()
    {
        return '修改头像';
    }
    
    function showSettingsTitle() {
    	return '头像设置';
    }
    
    function showSettingsInstruction() {
    	return '上传您的最新靓照制作成头像，让游友们眼前一亮。真实的照片能方便您的好友找到您哦！';
    }
    
    /**
     * Content area of the page
     *
     * Shows a form for uploading an avatar.
     *
     * @return void
     */
    function showSettingsContent()
    {
        if ($this->arg('avatarsettings_isCrop')) {
            $this->showCropForm();
        } else {
            $this->showUploadForm();
        }
    }
    
    function _showPreviewAndCrop() {
        $this->elementStart('div', array('style' => 'width:'. $this->args['avatarsettings_filedata']['width'] .'px;height:' . $this->args['avatarsettings_filedata']['height'] . 'px;', 'class' => 'original'));
        $this->elementStart('div', array('id'=>'avatar_original'));
        $this->element('img', array('src' => Avatar::url($this->args['avatarsettings_filedata']['filename'], Avatar::subpath($this->cur_user->id)),
                                    'width' => $this->args['avatarsettings_filedata']['width'],
                                    'height' => $this->args['avatarsettings_filedata']['height'],
                                    'alt' => $this->cur_user->nickname));
        $this->elementEnd('div');
        $this->elementEnd('div');
        
        $this->elementStart('div', 'preview');
        $this->elementStart('div',
                            array('id' => 'avatar_preview', 'style' => 'overflow: hidden; width: 96px; height: 96px; margin-left: 5px;'));
        $this->element('img', array('src' => Avatar::url($this->args['avatarsettings_filedata']['filename'], Avatar::subpath($this->cur_user->id)),
                                    'alt' => $this->cur_user->nickname));
        $this->elementEnd('div');
        $this->elementEnd('div');
    }
    
    function _showCropSubmit() {
        $this->elementStart('div', 'op');
        $this->element('input', array('name' => 'crop', 'id' => 'crop', 'type' => 'submit', 'value' => '提交', 'class' => 'submit button76 green76'));
        $this->element('a', array('href' => common_path('settings/avatar'), 'title' => '取消操作，返回设置页面', 'class' => 'cancel'), '取消');
    	$this->elementEnd('div');
    }
    
    function _showCropHiddenVars() {
    	// some important hidden variables	
    	foreach (array('avatar_crop_x', 'avatar_crop_y',
                       'avatar_crop_w', 'avatar_crop_h') as $crop_info) {
            $this->element('input', array('name' => $crop_info,
                                          'type' => 'hidden',
                                          'id' => $crop_info));
        }
    }
    
    function showCropForm() {
    	
    	$this->tu->startFormBlock(array('enctype' => 'multipart/form-data',
                                          'method' => 'post',
                                          'id' => 'settings_cropavatar',
                                          'class' => 'form_settings',
                                          'action' =>
                                          common_path('settings/avatar')), '修改头像');
        
        $this->_showCropHiddenVars();
        
        $this->element('p', null, '通过拖动方框和调整方框大小，截取您想从图片中提取的部分');
        
        $this->_showPreviewAndCrop();
        
        $this->_showCropSubmit();
        
        $this->tu->endFormBlock();
    }
    
    function _showOriginAvatar() {
    	$avatar = $this->cur_user_profile->getAvatar(AVATAR_PROFILE_SIZE, AVATAR_PROFILE_SIZE);
    	
    	$this->elementStart('dl', 'origin');
        $this->element('dt', null, '当前头像');

    	$this->elementStart('dd');
    	
    	if ($avatar) {
	        $this->element('img', array('src' => $avatar->url,
	        								'width' => '96',
	                                        'height' => '96',
	                                        'alt' => $this->cur_user->nickname));
        } else {
        	 $this->element('img', array('src' => Avatar::defaultImage(AVATAR_PROFILE_SIZE, $this->cur_user_profile->id, $this->cur_user_profile->sex),
	        								'width' => '96',
	                                        'height' => '96',
	                                        'alt' => $this->cur_user->nickname));
        }
        $this->elementEnd('dd');
        $this->elementEnd('dl');
        
        // if have avatar show delete button
//    	if ($avatar) {
//	        $this->elementStart('tr');
//	        $this->element('td');
//	        $this->elementStart('td', 'b_cbf_sm');
//	    	$this->element('input', array('type' => 'submit', 'value' => '删除', 'name' => 'delete'));
//	    	$this->elementEnd('td');
//	    	$this->element('td');
//	    	$this->elementEnd('tr');
//        }
    }
    
    function _showAvatarUpload() {
    	$this->element('p', null, '好的形象从个性头像开始，上传您的最新靓照~。支持JPG/PNG图片，大小不能超过1MB。');
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
                                          common_path('settings/avatar')), '修改头像');
    	
        $this->elementStart('dl', 'upload');
        $this->element('dt', 'upload', '修改头像');
        
        $this->elementStart('dd', 'upload');
        
		$this->_showOriginAvatar();

        $this->_showAvatarUpload();
        
        $this->elementEnd('dd');
        $this->elementEnd('dl');
        
        $this->_showUploadSubmit();
        
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
        if ($this->arg('avatarsettings_isCrop')) {
            $this->element('script', array('type' => 'text/javascript',
                                           'src' => common_path('js/jquery.jcrop.js')));
            $this->element('script', array('type' => 'text/javascript',
                                           'src' => common_path('js/lshai_jcrop.js')));
        } else {
        	$this->script('js/lshai_uploadfile.js');
        }
    }
    
    
}

?>