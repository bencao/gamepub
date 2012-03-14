<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class UploadvideoHTMLTemplate extends RightsidebarHTMLTemplate
{	
	function title()
    {
        return "上传视频";
    }
    
	function showScripts() {
		parent::showScripts();
		$this->script('js/lshai_video.js');	
	}
     
	function showContent() {
		$this->tu->showTitleBlock('上传视频   --  目前支持IE6/7/8，搜狗，360等浏览器等上传', 'upload');
		$this->element('div', array('id' => 'sub_op'), '视频上传及审核成功之后， 标签、标题及介绍将作为消息的内容展示在您空间的消息列表中。');
		$this->showSettingsTip();
		$this->showForm();
	}
	
	function showSettingsTip()
    {
    	if ($this->arg('page_msg')) {
    		if ($this->arg('page_success')) {
    			$this->tu->showPageHighlightBlock($this->args['page_msg']);
    		} else {
    			$this->tu->showPageErrorBlock($this->args['page_msg']);
    		}
    	}
    }
	
	function showForm() {
		$sid = $this->getSid();
		if(!$sid) {
			//$this->clientError('发送错误, 请稍后再试');
			//return;
			$this->tu->showPageErrorBlock('视频上传遇到一些问题, 请稍候再试.');
		} else {
			$this->elementStart('form', array('method' => 'post', 'enctype' => "multipart/form-data",
	    					'action' => common_local_url('uploadvideo'), 'id' => 'video_upload_form', 'class' => 'editing'));
	    	$this->elementStart('fieldset');
	    	$this->element('legend', null, '上传视频');
	    	
	    	$this->elementStart('dl', 'clearfix');
	    	$this->element('dt', null, '选择文件: ');
	    	$this->elementStart('dd');
	    	$this->element('div', array('id' => 'uploadp'));
	    	$this->elementStart('div', array('id' => 'upload_flash_video'));
	    	$this->element('div', array('id' => 'uploader_video'));
	    	$this->elementEnd('div');
	    	$this->elementEnd('dd');
	    	
	    	//游戏标签
	    	$this->element('dt', null, '标签：');
	    	$this->elementStart('dd');
	    	$this->elementStart('select', array('id' => 'first_tag_select', 'name' => 'first_tag'));
	    	$this->element('option', array('value' => '0'), '请选择');
			$fts = First_tag::getFirstTags($this->cur_user->game_id);
	       	foreach ($fts as $id => $name) {
	       		$this->element('option', array('value' => $id), $name);
	        }
	        $this->element('option', array('value' => 'define'), '自定义');
	    	$this->elementEnd('select');
	    	$this->elementStart('select', array('id' => 'second_tag_select', 'name' => 'second_tag'));
	    	$this->element('option', array('value' => '0'), '请选择');
	    	$this->elementEnd('select');

//	        $this->raw('<input type="text" class="text" style="width:100px;display:none;" />');
	    	$this->elementEnd('dd');
	    	
	    	$this->element('dt', null, '标题：');
	    	$this->elementStart('dd');
	    	$this->element('input', array('class' => 'text200', 'type' => 'text', 'id' => 'video_title', 'name' => 'video_title'));
	    	$this->element('label', array('class' => 'info'), '请限制在48个字符');
	    	$this->elementEnd('dd');
	    	
	    	//cid
	    	$this->element('dt', null, '分类：');
	    	$this->elementStart('dd');
	    	$this->element('input', array('class' => 'radio', 'type' => 'radio', 'name' => 'cid', 'value' => '游戏', 'checked' => 'true'));
	    	$this->element('label', null, '游戏');
	    	$this->element('input', array('class' => 'radio', 'type' => 'radio', 'name' => 'cid', 'value' => '原创'));
	    	$this->element('label', null, '原创');
	    	$this->elementEnd('dd');
	    	
	    	$this->element('dt', null, '介绍：');
	    	$this->elementStart('dd');
	    	$this->element('textarea', array('class' => 'textarea376', 'id' => 'description', 'name' => 'description'));
	    	$this->elementEnd('dd');
	    	
	    	$this->element('dt');
	    	$this->elementStart('dd');
	    	$this->element('input', array('type' => 'hidden', 'value' => '1', 'name' => 'dosubmit'));
	    	$this->element('input', array('type' => 'hidden', 'value' => $sid, 'name' => 'sid', 'id' => 'sid'));
	    	$this->element('input', array('type' => 'hidden', 'id' => 'type', 'name' => 'type'));
	    	$this->element('input', array('type' => 'hidden', 'value' => common_session_token(), 'name' => 'token'));
	    	$this->element('input', array('class' => 'submit button76 green76', 'type' => 'submit', 'value' => '上传'));    	
	    	$this->elementEnd('dd');
	    	
	    	$this->elementEnd('dl');
	    	
	    	$this->element('input', array('type' => 'hidden', 
                                               'value' => $this->trimmed('mode', ''),
        	                                   'name' => 'mode'));
	        $this->element('input', array('type' => 'hidden',
	                                               'value' => $this->trimmed('mode_identifier', ''),
	                                               'name' => 'mode_identifier'));
	    	
	    	$this->elementEnd('fieldset');
	    	$this->elementEnd('form');
		}
	}
	
	function getSid() {
		$skey = 'aff0a6a4521232970b2c1cf539ad0a19';
    	$pass = '073b3cba749816c388dc268b553a63af';
    	
		$str =  file_get_contents('http://v.ku6vms.com/phpvms/api/getSid/skey/' . $skey . '/v/1/format/json/md5/'. 
				strtoupper(md5($skey. '1' . $pass)));

		$obj = json_decode($str, true);
		if($obj['status'] == 1)
			return $obj['sid'];
		else 
			return null;
	}
	
	function showRightsidebar() {
		$this->elementStart('dl', 'widget intro');
		$this->element('dt', null, '上传视频');
		$this->element('dd', null, '您可以选择视频文件来直接上传, 完成的时间由文件大小和您的网速而定.');
		$this->element('dd', null, '您的视频上传后, 我们会进行快递审核, 一旦通过审核, 您的消息就发送成功啦!');
		$this->elementEnd('dl');
	}

}