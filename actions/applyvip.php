<?php


if (!defined('SHAISHAI')) {
	exit(1);
}

define('MAX_ORIGINAL', 480);

require_once INSTALLDIR.'/lib/validatetool.php';

/**
 * Upload an avatar
 *
 * We use jCrop plugin for jQuery to crop the image after upload.
 *
 */

class ApplyvipAction extends ShaiAction
{
	var $errorMsg = null;

	function handle($args){
		parent::handle($args);
		if($_SERVER['REQUEST_METHOD']=='POST'){
			if ($this->_validateForm()) {
				$this->saveNew();
			} else {
				$this->showForm($this->errorMsg);
			}
		}else{
			$this->showForm();
		}
	}

	function saveNew(){
		$fp = new Apply_vip();
		$fp->url = $this->url;
		$fp->phone_number = $this->phone_number;
		$fp->email = $this->email;
		$fp->description = $this->description;
		$fp->uid = $this->cur_user->id;
		$fp->created = common_sql_now();

		$fp->insert();
		$urls = explode('*',$this->url);
		$this->addPassVariable('urls', $urls);
		$this->showForm('提交成功！我们会在三个工作日内确认您的请求。', true);
	}

	function _validateForm(){
		if(! array_key_exists('filenum',$_POST)){
			$this->errorMsg = '请上传身份认证的图片';
			return false;
		}
		$this->url = $this->trimmed('fileurl');
		$this->url = substr($this->url,0,strlen($this->url)-1);
		 
		$this->phone_number = $this->trimmed('phone_number');
		if( empty($this->phone_number) ||
		! isNum($this->phone_number) ||
		strlen($this->phone_number)!=11){
			$this->errorMsg = '您输入的电话号码(11位数字)不正确';
			return false;
		}

		$this->email = $this->trimmed('email');
		if(! isValidEmail($this->email)){
			$this->errorMsg = '您输入的电子邮件不正确';
			return false;
		}
		//urf-8是啥意思
		$this->description = $this->trimmed('description','utf-8');
		if(empty($this->description)){
			$this->errorMsg = '请填写您的申请原因和身份说明';
			return false;
		}
		if (mb_strlen($this->description) > 1000) {
			$this->errorMsg = '申请原因身份说明太长了，尝试缩短它，然后再保存！';
			return false;
		}

		return true;
	}

	function showForm($msg=null, $success=false)
	{
		$this->view = TemplateFactory::get('ApplyvipHTMLTemplate');
		$this->view->show($this->paras, $msg, $success);
	}
	 

}
