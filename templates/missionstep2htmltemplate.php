<?php

if (!defined('SHAISHAI')) {
	exit(1);
}

class Missionstep2HTMLTemplate extends RegisterWizardHTMLTemplate
{
	var $cur_user_profile;
	
	function title() {
		return '升级任务';
	}
	
	function greeting() {
		return '完善您的个人资料与头像，成为二级用户，享受更多特权！';
	}
	
	function show($args) {
		$this->cur_user_profile = $args['settings_cur_user_profile'];
		parent::show($args);
	}
	
	function showTab() {
		$this->elementStart('ul', 'steps clearfix');
		$this->elementStart('li', 'first');
		$this->element('span', null, '1');
		$this->element('a', array('href' => common_local_url('missionstep1')), '完善个人资料');
		$this->elementEnd('li');
		$this->elementStart('li', 'active');
		$this->element('span', null, '2');
		$this->element('a', array('href' => common_local_url('missionstep2')), '添加个人头像');
		$this->elementEnd('li');
		$this->elementStart('li');
		$this->element('span', null, '3');
		$this->element('a', array('href' => common_local_url('missionstep3')), '填写兴趣爱好');
		$this->elementEnd('li');
		$this->elementStart('li', 'last');
		$this->element('a', array('href' => common_local_url('missionstep4')), '完成任务');
		$this->elementEnd('li');
		$this->elementEnd('ul');
	}
	
	function showIntro() {
//		$gradeinfo = $this->cur_user_profile->getUserUpgradePercent(); 
		$this->elementStart('div', 'intro clearfix');
		$this->element('div', 'text', ($this->cur_user_profile->sex == 'M' ? '帅气' : '靓丽') . '的头像是混' . common_config('site', 'name') . '的必备武器哦~');
//		$this->elementStart('div', 'progress');
//		$this->element('strong', null, '资料完成度 ' . $this->cur_user_profile->completeness . '%');
//		$this->elementStart('span');
//		$this->element('em', array('style' => 'width:' . $this->cur_user_profile->completeness . '%;'));
//		$this->elementEnd('span');
//		$this->elementEnd('div');
		$this->elementEnd('div');
	}
	
	function showForm() {
		if ($this->arg('avatarsettings_isCrop')) {
            $this->showCropForm();
        } else {
            $this->showUploadForm();
        }
	}
	
	function showContent() {
		$this->elementStart('div', array('id' => 'tocomplete'));
		$this->showTab();
		$this->showIntro();
		$this->showPageTip();
		$this->showForm();
		
		$this->elementEnd('div');
	}
    
	function showPageTip()
    {
    	if ($this->arg('page_msg')) {
    		if ($this->arg('page_success')) {
//    			$this->tu->showPageHighlightBlock($this->args['page_msg']);
    		} else {
    			$this->tu->showPageErrorBlock($this->args['page_msg']);
    		}
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
        $this->element('a', array('href' => common_local_url('missionstep2'), 'title' => '取消操作，返回设置页面', 'class' => 'cancel'), '取消');
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
                                          common_local_url('missionstep2')), '修改头像');
        
        $this->_showCropHiddenVars();
        
        $this->element('p', null, '通过拖动方框和调整方框大小，截取您想从图片中提取的部分');
        
        $this->_showPreviewAndCrop();
        
        $this->_showCropSubmit();
        
        $this->tu->endFormBlock();
    }
    
    function _showOriginAvatar() {
    	$avatar = $this->cur_user_profile->getOriginalAvatar();
    	
    	$this->elementStart('dl', 'origin');
        $this->element('dt', null, '当前头像');

    	$this->elementStart('dd');
    	
    	if ($avatar) {
	        $this->element('img', array('src' => $avatar->url,
	        								'width' => '250',
	                                        'height' => 250 * $avatar->height / $avatar->width,
	                                        'alt' => $this->cur_user->nickname));
        } else {
        	 $this->element('img', array('src' => Avatar::defaultImage(AVATAR_PROFILE_SIZE, $this->cur_user_profile->id, $this->cur_user_profile->sex),
	        								'width' => '250',
	                                        'height' => '250',
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
                                          common_local_url('missionstep2')), '修改头像');
    	
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