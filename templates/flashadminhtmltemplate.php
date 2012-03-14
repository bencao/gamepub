<?php

/**
 * Shaishai, the distributed microblog
 *
 * Delete flash
 *
 * PHP version 5
 *
 * @category  Administrator
 * @package   Shaishai
 * @author    AGun Chan <agunchan@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */


if (!defined('SHAISHAI')) {
    exit(1);
}

class FlashadminHTMLTemplate extends PublictwocolumnHTMLTemplate
{	
	function title()
    {
    	return '小游戏管理';
    }
    
    function showShaishaiScripts() {
    	parent::showShaishaiScripts();
    	$this->script('js/jquery.validate.min.js');
    	$this->script('js/lshai_flashupload.js');
    }
    
    function showContent() 
    {
    	$this->elementStart('div', 'miniwrap');
    	if ($this->trimmed('msg', false)) {
			$this->tu->showPageErrorBlock($this->trimmed('msg'));
		}
    	$this->showFlashPic();
		$this->showFlashDel();
		$this->elementEnd('div');
    }

    function showFlashPic()
    {
    	$this->elementStart('dl', 'miniupload');
		$this->element('dt', 'title', '更新小游戏截图');
		$this->elementStart('dd', 'body');
		
		//form
		$this->tu->startFormBlock(array('method' => 'post', 'action' => common_local_url('flashadmin'), 'id' => 'flash_updatepic_form'), '小游戏截图更新');
    	$this->elementStart('dl');
    	
    	$this->elementStart('dt');
    	$this->element('label', null, '游戏ID：');
    	$this->elementEnd('dt');
    	$this->elementStart('dd');
    	$this->element('input', array('type'=>'text', 'maxlength' => 40,'class'=>'text200', 'name' => 'flashid', 'id' => 'flashid', 'value' => $this->trimmed('flashid', '')));
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
    	
    	$this->elementEnd('dl');
    	$this->elementStart('div', 'op');
    	$this->element('input', array('class'=>'submit green76 button76', 'type'=>'submit', 'value'=>'确定'));
    	$this->elementEnd('div');
	    $this->tu->endFormBlock();
	    //end form
	    
		$this->elementEnd('dd');
		$this->elementEnd('dl');
    }
    
    function showFlashDel()
    {
    	$this->elementStart('dl', 'miniupload');
		$this->element('dt', 'title', '删除小游戏');
		$this->elementStart('dd', 'body');
		
		//form
		$this->tu->startFormBlock(array('method' => 'post', 'action' => common_local_url('flashadmin'), 'id' => 'flash_delete_form'), '小游戏删除');
    	$this->elementStart('dl');
    	
    	$this->elementStart('dt');
    	$this->element('label', null, '游戏ID：');
    	$this->elementEnd('dt');
    	$this->elementStart('dd');
    	$this->element('input', array('type'=>'text', 'maxlength' => 40,'class'=>'text200', 'name' => 'flashdelid', 'id' => 'flashdelid', 'value' => $this->trimmed('flashdelid', '')));
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