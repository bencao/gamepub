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
 * @category Group
 * @package  ShaiShai
 */
class GrouplogoAction extends GroupAdminAction
{
    var $mode = null;
    var $imagefile = null;
    var $filename = null;
    var $filedata = null;
    var $msg = null;
    var $success = null;

    function handle($args)
    {
        parent::handle($args);
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->handlePost();
        } else {
            $this->showForm();
        }
    }

    function showForm($msg = null, $success = false)
    {
        $this->msg     = $msg;
        $this->success = $success;

        $this->addPassVariable('msg', $this->msg);
        $this->addPassVariable('success', $this->success);
        $this->addPassVariable('group', $this->cur_group);
        $this->displayWith('GrouplogoHTMLTemplate');
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
        // CSRF protection

        $token = $this->trimmed('token');
        if (!$token || $token != common_session_token()) {
            $this->showForm('您正在进行的会话出现问题，请再试一次。');
            return;
        }

        if ($this->arg('upload')) {
            $this->uploadLogo();
        } else if ($this->arg('crop')) {
            $this->cropLogo();
        } else if ($this->arg('delete')) {
            $this->deleteLogo();
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

    function uploadLogo()
    {
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

        $filename = Avatar::filename($this->cur_group->id,
                                     image_type_to_extension($imagefile->type),
                                     null,
                                     'group-temp-'.common_timestamp());

        $filepath = Avatar::path($filename, Avatar::groupsubpath($this->cur_group->id));

        rename($tmpsubpath . $tmpfilename, $filepath);
        
        $targetWidth = 361;
        
        $aspectRatio = $targetWidth/$imagefile->width;
        
        $renderedHeight = $imagefile->height * $aspectRatio;

        $filedata = array('filename' => $filename,
                          'filepath' => $filepath,
                          'width' => $targetWidth,
                          'height' => $renderedHeight,
                          'type' => $imagefile->type);

        $_SESSION['FILEDATA'] = $filedata;
        $this->addPassVariable('grouplogo_aspectratio', $aspectRatio);
        $this->addPassVariable('grouplogo_filedata', $filedata);
        $this->addPassVariable('grouplogo_mode', 'crop');

        $this->showForm('请选择一块矩形区域作为' . GROUP_NAME() . '的图标。', true);
    }

    /**
     * Handle the results of jcrop.
     *
     * @return void
     */

    function cropLogo()
    {
        $filedata = $_SESSION['FILEDATA'];

        if (!$filedata) {
            $this->serverError('文件数据丢失');
            return;
        }
        
        $file_d = ($filedata['width'] > $filedata['height'])
                     ? $filedata['height'] : $filedata['width'];
                     
        $aspectRatio = $this->trimmed('ar');

        // If image is not being cropped assume pos & dimentions of original
        $dest_x = $this->arg('avatar_crop_x') ? $this->arg('avatar_crop_x'):0;
        $dest_y = $this->arg('avatar_crop_y') ? $this->arg('avatar_crop_y'):0;
        $dest_w = $this->arg('avatar_crop_w') ? $this->arg('avatar_crop_w'):$file_d;
        $dest_h = $this->arg('avatar_crop_h') ? $this->arg('avatar_crop_h'):$file_d;
        
        $dest_x = ($dest_x / $aspectRatio) + 1;
        $dest_y = ($dest_y / $aspectRatio) + 1;
        $dest_w = ($dest_w / $aspectRatio) - 1;
        $dest_h = ($dest_h / $aspectRatio) - 1;
        
        $size = min($dest_w, $dest_h, MAX_ORIGINAL);

        $imagefile = new ImageFile($this->cur_group->id, $filedata['filepath']);
        $filename = $imagefile->resize($size, $dest_x, $dest_y, $dest_w, $dest_h, 1);

        if ($this->cur_group->setOriginal($filename)) {
            @unlink($filedata['filepath']);
            unset($_SESSION['FILEDATA']);
            
        $this->addPassVariable('grouplogo_mode', 'upload');

            $this->showForm(GROUP_NAME() . 'LOGO已更新。', true);
        } else {
            $this->showForm(GROUP_NAME() . 'LOGO更新失败。');
        }
    }
    
/**
     * Get rid of the current group logo.
     *
     * @return void
     */
    
    function deleteLogo()
    {

        $avatar = null;
        
        foreach ($this->getlogopath() as $avatar) {
            if ($avatar) {
        	    @unlink($avatar);
            }
        }
        $orig = clone($this->cur_group);
        $this->cur_group->original_logo = 'null';
        $this->cur_group->homepage_logo = 'null';
        $this->cur_group->stream_logo = 'null';
        $this->cur_group->mini_logo = 'null';
        $result = $this->cur_group->update($orig);

        if (!$result) {
            common_log_db_error($this->cur_group, 'UPDATE', __FILE__);
            $this->serverError('删除' . GROUP_NAME() . 'logo失败。');
        }
        $this->addPassVariable('grouplogo_mode', 'upload');
        $this->cur_group = User_group::staticGet('id', $this->trimmed('id'));
        $this->showForm('头像已删除。', true);
    }
    
    // get real path of logo
    function getlogopath()
    {
    	// get the url head
        $path = common_config('avatar', 'path');
        if ($path[strlen($path)-1] != '/') {
            $path .= '/';
        }
        if ($path[0] != '/') {
            $path = '/'.$path;
        }
        $server = common_config('avatar', 'server');
        if (empty($server)) {
            $server = common_config('site', 'server');
        }
        $urlhead = 'http://'.$server.$path;
        // get the local absolute path
        $dir = common_config('avatar', 'dir');
        if ($dir[strlen($dir)-1] != '/') {
            $dir .= '/';
        }
        
        $logopath = array();
        foreach(array($this->cur_group->original_logo, $this->cur_group->homepage_logo,
                       $this->cur_group->stream_logo, $this->cur_group->mini_logo) as $urlpath){
            $logopath[] = $dir.substr($urlpath, strlen($urlhead));
        }
        
        return $logopath;

    }

}
