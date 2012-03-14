<?php

if (!defined('SHAISHAI')) { exit(1); }

//require_once INSTALLDIR . '/lib/validatetool.php';

class UploadfileAction extends ShaiAction
{
	function __construct() {
		parent::__construct();
		$this->no_anonymous = false;
		$this->cache_allowed = false;
	}
	
	function handle($args) {
		parent::handle($args);
		if (!empty($_FILES)) {
			$user = common_current_user();
			$tempFile = $_FILES['Filedata']['tmp_name'];
			$uid = $this->trimmed('uid');
			if ($uid && is_numeric($uid)) {
				$targetPath = Avatar::tmpsubpath($uid);
				$tmpName = preg_replace('/^(.+)\.([^\.]+)$/', time() . '.$2', $_FILES['Filedata']['name']);
				$targetFile =  str_replace('//','/',$targetPath) . $tmpName;
					
				move_uploaded_file($tempFile, $targetFile);
				echo common_path($targetFile);
			} else {
				$this->clientError('缺少uid参数或uid参数不正确');
				return;
			}
		}	
	}
}

?>