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

class FlashuploadHTMLTemplate extends FlashgamebaseHTMLTemplate
{	
	function title()
    {
    	return '上传小游戏';
    }
    
    function show($args = array()) 
    {
    	$this->cat = 'none';
    	parent::show($args);	
    }
    
    function showShaishaiScripts() {
    	parent::showShaishaiScripts();
    	$this->script('js/jquery.validate.min.js');
    	$this->script('js/lshai_flashupload.js');
    }
    
    function showMiniContent() 
    {
		$this->elementStart('dl', 'miniupload');
		$this->element('dt', 'title', '上传游戏，分享快乐');
		$this->elementStart('dd', 'body');
		
		if ($this->trimmed('error_msg', false)) {
			$this->tu->showPageErrorBlock($this->trimmed('error_msg'));
		}
		
		//form
		$this->tu->startFormBlock(array('method' => 'post', 'action' => common_local_url('flashupload'), 'id' => 'flash_upload_form'), '上传小游戏');
    	$this->elementStart('dl');
    	
    	$this->elementStart('dt');
    	$this->element('label', null, '游戏文件：');
    	$this->elementEnd('dt');
    	$this->elementStart('dd');
    	$swffilename = $this->trimmed('swffilename', false);
    	
    	if ($swffilename && preg_match('/.*\.swf/i', $swffilename)) {
    		$this->element('span', null, '已上传');
    		$this->element('input', array('type' => 'hidden', 'name' => 'swffilename', 'id' => 'swffilename', 'value' => $swffilename));
    	} else {
	    	$this->element('input', array('type' => 'file', 'name' => 'swffile', 'id' => 'swffile'));
			$this->element('div', array('id' => 'swfFileQueue', 'uid' => $this->cur_user->id, 'class' => 'filequeue'));
			$this->element('input', array('type' => 'hidden', 'name' => 'swffilename', 'id' => 'swffilename'));
    	}
    	$this->elementEnd('dd');
    	
    	$this->elementStart('dt');
    	$this->element('label', null, '游戏截图：');
    	$this->elementEnd('dt');
    	$this->elementStart('dd');
    	$picfilename = $this->trimmed('picfilename', false);
    	
    	if ($picfilename && preg_match('/.*\.(jpg|png|gif|jpeg)/i', $picfilename)) {
    		$this->element('span', null, '已上传');
    		$this->element('input', array('type' => 'hidden', 'name' => 'picfilename', 'id' => 'picfilename', 'value' => $picfilename));
    	} else {
	    	$this->element('input', array('type' => 'file', 'name' => 'picfile', 'id' => 'picfile'));
			$this->element('div', array('id' => 'picFileQueue', 'uid' => $this->cur_user->id, 'class' => 'filequeue'));
			$this->element('input', array('type' => 'hidden', 'name' => 'picfilename', 'id' => 'picfilename'));
    	}
    	$this->elementEnd('dd');
    	
    	$this->elementStart('dt');
    	$this->element('label', null, '游戏名称：');
    	$this->elementEnd('dt');
    	$this->elementStart('dd');
    	$this->element('input', array('type'=>'text', 'maxlength' => 40,'class'=>'text200', 'name' => 'title', 'id' => 'title', 'value' => $this->trimmed('title', '')));
    	$this->elementEnd('dd');
    	
    	$this->elementStart('dt');
    	$this->element('label', null, '游戏分类：');
    	$this->elementEnd('dt');
    	$this->elementStart('dd');
		each($this->catnames);
		$cnt = 1;
		while(list($cat, $name) = each($this->catnames)) {
			if ($this->trimmed('type', '') == $cat) {
				$this->element('input', array('type'=>'radio', 'class' => 'radio', 'value'=>$cat, 'name'=>'type', 'id' => 'radio' . $cnt, 'checked' => 'checked'));
			} else { 
				$this->element('input', array('type'=>'radio', 'class' => 'radio', 'value'=>$cat, 'name'=>'type', 'id' => 'radio' . $cnt));
			}
			$this->element('label', array('for' => 'radio' . $cnt), $name);
			$cnt ++;
		}
    	$this->elementEnd('dd');
    	
    	$this->elementStart('dt');
    	$this->element('label', null, '游戏简介：');
    	$this->elementEnd('dt');
    	$this->elementStart('dd');
    	$this->element('textarea', array('class'=>'textarea376', 'name' => 'introduction'), $this->trimmed('introduction', ''));
    	$this->elementEnd('dd');
    	
    	$this->elementStart('dt');
    	$this->element('label', null, '操作说明：');
    	$this->elementEnd('dt');
    	$this->elementStart('dd');
    	$this->element('textarea', array('class'=>'textarea376', 'name' => 'detail'), $this->trimmed('detail', ''));
    	$this->elementEnd('dd');
    	
    	$this->elementEnd('dl');
    	$this->elementStart('div', 'op');
    	$this->element('input', array('class'=>'submit green76 button76', 'type'=>'submit', 'value'=>'确定'));
    	$this->elementEnd('div');
	    $this->tu->endFormBlock();
	    //end form
	    
		$this->elementEnd('dd');
		$this->elementEnd('dl');
    }

}