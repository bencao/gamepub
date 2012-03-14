<?php

/**
 * Shaishai, the distributed microblog
 *
 * Upload flash game
 *
 * PHP version 5
 *
 * @category  FlashGame
 * @package   Shaishai
 * @author    AGun Chan <agunchan@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

class FlashuploadAction extends ShaiAction
{	
	var $catType = array('all' => 0, 'puzzle' => 1, 'act' => 2, 'shoot' => 3, 'fun' => 4, 'sport' => 5, 'chess' => 6);
	
	function handle($args)  {
        parent::handle($args);

        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
           $this->displayWith('FlashuploadHTMLTemplate'); 
        } else {
        	$swffilepath = $this->trimmed('swffilename');
        	$swffilename = substr($swffilepath, strrpos($swffilepath, '/') + 1);
        	$picfilepath = $this->trimmed('picfilename');
        	$picfilename = substr($picfilepath, strrpos($picfilepath, '/') + 1);
        	$title = $this->trimmed('title');
        	$introduction = $this->trimmed('introduction');
        	$detail = $this->trimmed('detail');
        	$type = $this->trimmed('type');
        	
        	// validation
        	if (! preg_match('/.*\.swf/i', $swffilename)) {
        		$this->showError('游戏文件不符合要求');
        		return false;
        	}
        	if (! preg_match('/.*\.(jpg|png|gif|jpeg)/i', $picfilename)) {
        		$this->showError('截图文件不符合要求');
        		return false;
        	}
        	if (empty($title) || mb_strlen($title, 'utf-8') > 40) {
        		$this->showError('标题长度应在1~40个字符之间');
        		return false;
        	}
        	if (empty($introduction) || mb_strlen($introduction, 'utf-8') > 280) {
        		$this->showError('简介长度应在1~280个字符之间');
        		return false;
        	}
        	if (empty($detail)) {
        		$this->showError('操作说明不能为空');
        		return false;
        	}
        	if ((! array_key_exists($type, $this->catType)) || $type == 'all') {
        		$this->showError('游戏类型不正确');
        		return false;
        	}
        	
        	// now the flash file was uploaded by jquery.uploadify and saved in user's tmp directory under file/tmp/:userid, e.g file/tmp/10/00/00/gun.swf
        	// move flash file from tmp directory to final user directory under file/:userid/flash, e.g file/10/00/00/flash/gun.swf
        	$targetPath = Avatar::flashsubpath($this->cur_user->id);
			$fromPath = Avatar::tmpsubpath($this->cur_user->id);
			rename($fromPath . $swffilename, $targetPath . $swffilename);
			rename($fromPath . $picfilename, $targetPath . $picfilename);
        	
        	// save new flash
        	$flash = Flash::saveNew($this->cur_user->id, '', $title, $this->catType[$type], 
        		$introduction, $detail, common_path($targetPath . $swffilename), 
        		common_path($targetPath . $picfilename));
			
        	// save a new notice which point to the flash gameplay page, content_type = 5
        	$notice = Notice::saveNew($this->cur_user->id, '我上传了一个很好玩的小游戏《' . $title . '》，不玩会后悔的哦！', 
        		'', 'web', true, array('content_type' => 5 , 'addRendered' => '<div class="video_message"><a target="_blank" class="smallimagebtn" href="' . common_path('flash/' . $flash->id) . '"><img class="smallimage" width="120" height="90" src="' . $flash->picpath . '"><em></em></a></div>'));
			
        	$flash->updateNoticeId($notice->id);
        	
        	// redirect user to flash gameplay page
        	common_redirect(common_path('flash/' . $flash->id), 303);
			
        }
        
    }
    
    function showError($msg) {
    	$this->addPassVariable('error_msg', $msg);
    	$this->displayWith('FlashuploadHTMLTemplate');
    }
}