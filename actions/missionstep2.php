<?php


if (!defined('SHAISHAI')) {
    exit(1);
}

define('MAX_ORIGINAL', 480);

/**
 * Upload an avatar
 *
 * We use jCrop plugin for jQuery to crop the image after upload.
 *
 */

class Missionstep2Action extends SettingsAction
{
	function prepare($args) {
		if (! parent::prepare($args)) {return false;}
		if ($this->cur_user_profile->getUserGrade() > 1) {
			$this->clientError('您的等级太高，无法做此任务了', 403);
			return false;
		}
		return true;
	}
	
    function getViewName() {
    	return 'Missionstep2HTMLTemplate';
    }
    
    /**
     * Handle a post
     *
     * We mux on the button name to figure out what the user actually wanted.
     *
     * @return void
     */

    function handlePost()
    {
		if ($this->arg('upload')) {
        	$this->uploadAvatar();
        } else if ($this->arg('crop')) {
            $this->cropAvatar();
        } else {
            $this->showForm('异常的表单提交。');
        }
    }

    /**
     * Handle an image upload
     *
     * Does all the magic for handling an image upload, and crops the
     * image by default.
     *
     * @return void
     */

    function uploadAvatar()
    {
    	// prepare
    	if (! array_key_exists('avatarfilename', $_POST)) {
    		$this->showForm('请选择要上传的文件。', false);
    		return;
    	}
    	
    	// move file to user dir
    	
    	$tmpfilepath = $this->trimmed('avatarfilename');
    	
    	$tmpfilename = substr($tmpfilepath, strrpos($tmpfilepath, '/') + 1);
    	
    	$tmpsubpath = Avatar::tmpsubpath($this->cur_user->id);
        
        $imagefile = new ImageFile(null, $tmpsubpath . $tmpfilename);
        
        if (! $imagefile) {
        	$this->showForm('文件已过期，请重新上传', false);
        	return;
        }
        
        if ($imagefile->width == 0
        	|| $imagefile->height == 0) {
        	$this->showForm('文件不合法，无法读取', false);
        	return;
        }

        $filename = Avatar::filename($this->cur_user->id,
                                     image_type_to_extension($imagefile->type),
                                     null,
                                     'tmp'.common_timestamp());

        $filepath = Avatar::path($filename, Avatar::subpath($this->cur_user->id));

        rename($tmpsubpath . $tmpfilename, $filepath);
        
        // get the target image size
        $targetWidth = 361;
        
        $aspectRatio = $targetWidth/$imagefile->width;
        
        $renderedHeight = $imagefile->height * $aspectRatio;        

        $filedata = array('filename' => $filename,
                          'filepath' => $filepath,
                          'width' => $targetWidth,
                          'height' => $renderedHeight,
                          'type' => $imagefile->type,
        				  'ratio' => $aspectRatio);

        $_SESSION['FILEDATA'] = $filedata;
        
        $this->addPassVariable('avatarsettings_filedata', $filedata);
        
        $this->addPassVariable('avatarsettings_isCrop', true);
        
        $this->showForm('选取一块矩形区域作为您的头像',
                        true);
    }

    /**
     * Handle the results of jcrop.
     *
     * @return void
     */

    function cropAvatar()
    {
    	$filedata = $_SESSION['FILEDATA'];

        if (!$filedata) {
            $this->serverError('服务器文件丢失。');
            return;
        }

        $file_d = ($filedata['width'] > $filedata['height'])
                     ? $filedata['height'] : $filedata['width'];
        
        $dest_x = $this->arg('avatar_crop_x') ? $this->arg('avatar_crop_x'):0;
        $dest_y = $this->arg('avatar_crop_y') ? $this->arg('avatar_crop_y'):0;
        $dest_w = $this->arg('avatar_crop_w') ? $this->arg('avatar_crop_w'):$file_d;
        $dest_h = $this->arg('avatar_crop_h') ? $this->arg('avatar_crop_h'):$file_d;
        
		$dest_x = ($dest_x / $filedata['ratio']) + 1;
        $dest_y = ($dest_y / $filedata['ratio']) + 1;
        $dest_w = ($dest_w / $filedata['ratio']) - 1;
        $dest_h = ($dest_h / $filedata['ratio']) - 1;
        
        $size = min($dest_w, $dest_h, MAX_ORIGINAL);

        $imagefile = new ImageFile($this->cur_user->id, $filedata['filepath']);
        $filename = $imagefile->resize($size, $dest_x, $dest_y, $dest_w, $dest_h);
        
        if ($this->cur_user_profile->setOriginal($filename)) {
            @unlink($filedata['filepath']);
            unset($_SESSION['FILEDATA']);
            
            $this->cur_user->updateCompleteness();
            
            common_redirect(common_path('main/missionstep3'), 303);
//            $this->showForm('头像已更新。', true);
        } else {
            $this->showForm('更新头像失败。');
        }
    }
}
