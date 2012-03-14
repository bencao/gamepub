<?php
/**
 * ShaiShai, the distributed microblogging tool
 *
 * Post a notice (update your status) through the API
 *
 * @category  API
 * @package   Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

require_once INSTALLDIR . '/lib/apiauth.php';

/**
 * Updates the authenticating user's log
 *
 * @category  API
 * @package   Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

class ApiLogUpdateAction extends ApiAuthAction
{
	function prepare($args)
    {
        if (! parent::prepare($args)) 
        	return false;

        $this->user = $this->auth_user;

        if (empty($this->user)) {
            $this->clientError('无此用户!', 404, $this->format);
            return false;
        }
        
        return true;
    }
    
     function handle($args)
    {
        parent::handle($args);
        $param = 'logfile';
        
    	if (isset($_FILES[$param]['error'])) {
	        try {
	        	if ($_FILES[$param]["type"] == "log") {
	        		throw new Exception('文件类型错误.');
		            return;
	        	}
	        	
		        switch ($_FILES[$param]['error']) {
		         case UPLOAD_ERR_OK: // success, jump out
		            break;
		         case UPLOAD_ERR_INI_SIZE:
		         case UPLOAD_ERR_FORM_SIZE:
		            throw new Exception(sprintf('您上传的文件过大， 最大为 %d。',
		                ImageFile::maxFileSize()));
		            return;
		         case UPLOAD_ERR_PARTIAL:
		            @unlink($_FILES[$param]['tmp_name']);
		            throw new Exception('不完整的上传。');
		            return;
		         case UPLOAD_ERR_NO_FILE:
		            // No file; probably just a non-AJAX submission.
		            return;
		         default:
		            throw new Exception('上传文件时系统错误。');
		            return;
		        }		        	          
	        } catch (Exception $e) {
	        	$this->clientError($e->getMessage(), 400, $this->format);
	            return false;
	        }
	        
	    	$basename = basename($_FILES[$param]['name']);
	        $subpath =  'desktoplog/';       	        
	        $filepath = File::path($this->user->id.'-'.$basename, $subpath);
	
	        if (move_uploaded_file($_FILES[$param]['tmp_name'], $filepath)) {
	            //return $filename;
	            $profile = $this->user->getProfile();
	            $subject = $this->user->id . '的客户端报告错误日志';
    
    			$body = $this->user->id . '的客户端报告错误日志, 在src/file/desktoplog目录下';
    			$addr = 'agunchan@gmail.com';
    			mail_to_user($profile, $subject, $body, $addr, false);
	        } else {
	            $this->clientError('文件不能移到目标目录.', 400, 'log');
	        }
        }
    }
}