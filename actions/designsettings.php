<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

//require_once INSTALLDIR . '/lib/validatetool.php';

/**
 * Change password
 *
 * @category Settings
 * @package  LShai
 */

class DesignsettingsAction extends SettingsAction
{
    
	function getViewName() {
		return 'DesignsettingsHTMLTemplate';
	}

    /**
     * Handle a post
     *
     * Validate input and save changes. Reload the form with a success
     * or error message.
     *
     * @return void
     */
    function handlePost()
    {
    	if ($this->arg('ajax')) {
    		if ($this->arg('del')) {
    			$this->deleteDesign(true);
    		}
	    	if ($this->arg('apply')) {
			    $this->applyDesign(true);
			} else if ($this->arg('save')) {
		  		$this->saveDesign(true);
			} else if ($this->arg('restoredefault')) {
			  	$this->restoreGameDefaults(true);
			}  
    	} else if ($this->arg('restoredefault')) {
		  	$this->restoreGameDefaults();
		} else if ($this->arg('delete')) {
		  	$this->deleteDesign();
		} else if ($this->arg('save')) {
		  	$this->saveDesign();
		} else if ($this->arg('apply')) {
		    $this->applyDesign();
		}
    }
    
	function showForm($msg=null, $success=false)
    {
    	$this->cur_design = $this->cur_user->getDesign();
//    	if ($this->cur_design) {
//    		$this->cur_design = Design::defaultGameDesign($this->owner_game);
//    	}
    	$this->addPassVariable('design', $this->cur_design);
    	$this->addPassVariable('owner_design', $this->cur_design);
    	$this->addPassVariable('self_designs', User_self_design::getDesignsByUser($this->cur_user));
    	
    	parent::showForm($msg, $success);
    }
    
    function applyDesign($isAjax = false) {
    	$design = $this->trimmed('design_id');
    	
    	if ($this->cur_user->applyDesign($design)) {
    		if ($isAjax) {
    			$this->showJsonResult(array('result' => 'true', 'url' => common_path($this->cur_user->uname)));
    		} else {
    			$this->showForm('应用外观成功', true);
    		}
    	} else {
    		if ($isAjax) {
    			$this->showJsonResult(array('result' => 'false'));
    		} else {
    			$this->showForm('应用外观失败');
    		}
    	}
    }
    
    function _validate() {
    	$this->design_name = $this->trimmed('design_name');
    	$this->backgroundcolor = $this->trimmed('backgroundcolor');
    	$this->backgroundimage = $this->trimmed('backgroundimage');
    	$this->disposition = $this->trimmed('disposition');
    	$this->contentcolor = $this->trimmed('contentcolor');
    	$this->sidebarcolor = $this->trimmed('sidebarcolor');
    	$this->textcolor = $this->trimmed('textcolor');
    	$this->linkcolor = $this->trimmed('linkcolor');
		$this->navcolor = $this->trimmed('navcolor');
		$this->bgrepeat = $this->trimmed('bgrepeat', false);
		$this->bgfix = $this->trimmed('bgfix', false);
		$this->showimage = $this->trimmed('showimage', 0);
    	
    	return true;
    }
    
	function _moveBackgroundImage($tmpsubpath, $newsubpath, $oldpath) {
    	$backimagefilename = substr($oldpath, strrpos($oldpath, '/') + 1);
	    		
	    $newpath = $newsubpath . $backimagefilename;
	    		
    	rename($tmpsubpath . $backimagefilename, $newpath);
    	
    	return $newpath;
    }
    
    function _prepareCssDir() {
    	$newsubpath = Avatar::csssubpath($this->cur_user->id);
    	
    	if (! file_exists($newsubpath)) {
			mkdir($newsubpath, 0777, true);
		}
		
		return $newsubpath;
    }
    
    function saveDesign($isAjax = false) {
    	if ($this->_validate()) {
    		
    		// 启动事务
    		$this->cur_user->query('BEGIN');
    		
    		// 保存背景和css文件路径
    		$tmpsubpath = Avatar::tmpsubpath($this->cur_user->id);
    		
    		$newsubpath = $this->_prepareCssDir();
			
    		if ($this->showimage == '1') {
	    		if (! empty($this->backgroundimage)) {
		    		$this->backgroundimage = common_path($this->_moveBackgroundImage($tmpsubpath, $newsubpath, $this->backgroundimage));
		    	} else {
		    		// 继承现有backgroundimage
		    		if ($this->owner->design_id == 0) {
		    			$this->backgroundimage = common_path('theme/default/i/bg.jpg');
		    		} else {
		    			$this->backgroundimage = $this->owner_design->backgroundimage;
		    		}
		    	}
    		} else {
    			$this->backgroundimage = null;
    		}
    		
			// 新建一个皮肤
    		$design = Design::saveNew($newsubpath, array(
    					'name' => $this->design_name,
    					'backgroundcolor' => $this->backgroundcolor,
    					'backgroundimage' => $this->backgroundimage,
    					'disposition' => $this->disposition,
    					'contentcolor' => $this->contentcolor,
    					'sidebarcolor' => $this->sidebarcolor,
    					'textcolor' => $this->textcolor,
    					'linkcolor' => $this->linkcolor,
						'navcolor' => $this->navcolor,
    					'bgrepeat' => $this->bgrepeat,
    					'bgfix' => $this->bgfix
    				));
    				
    		if (! $design) {
    			$this->cur_user->query('ROLLBACK');
    			if ($isAjax) {
    				$this->showJsonResult(array('result' => 'false'));
    			} else {
    				$this->showForm('保存失败');
    			}
    			return;
    		}
    		
    		// 保存一条用户自定义皮肤信息
    		User_self_design::saveNew($this->cur_user, $design);
    		
    		// 应用个性化皮肤
    		$this->cur_user->applyDesign($design->id);
    		
    		// 提交事务
    		$this->cur_user->query('COMMIT');
    		
//    		$this->addPassVariable('owner_design', $design);
    		
    		if ($isAjax) {
    			$this->showJsonResult(array('result' => 'true', 'url' => common_path($this->cur_user->uname . '?theme=true'), 'msg' => '已成功保存您的自定义皮肤'));
    		} else {
    			$this->showForm('保存成功', true);
    		}
    	} else {
    		if ($isAjax) {
    			$this->showJsonResult(array('result' => 'false'));
    		} else {
    			$this->showForm('保存失败');
    		}
    	}
    }
    
    function deleteDesign($isAjax = false) {
    	$toDeleteDesign = $this->trimmed('design_id');
    	
    	if (User_self_design::deleteDesign($this->cur_user->id, $toDeleteDesign)) {
    		
    		if ($isAjax) {
    			$this->showJsonResult(array('result' => 'true', 'dsid' => $toDeleteDesign));
    		} else {
    			$this->showForm('删除成功', true);
    		}
    	} else {
    		if ($isAjax) {
    			$this->showJsonResult(array('msg' => '删除失败', 'result' => 'false', 'dsid' => $toDeleteDesign));
    		} else {
    			$this->showForm('删除失败');
    		}
    	}
    }
    
	function restoreGameDefaults($isAjax = false)
    {
//        $this->cur_design = Design::defaultGameDesign($this->owner_game);
//        
//        $this->cur_user->applyDesign($this->cur_design->id);

    	$this->cur_user->clearDesign();
    	
//    	$this->cur_design = $this->cur_user->getDesign();
//    	
//        $this->addPassVariable('design', $this->cur_design);
//        $this->addPassVariable('owner_design', $this->cur_design);
        
//        $this->addPassVariable('self_designs', User_self_design::getDesignsByUser($this->cur_user));
        
    	if ($isAjax) {
    		$this->showJsonResult(array('result' => 'true', 'url' => common_path($this->cur_user->uname . '?theme=true'), 'msg' => '已恢复为默认皮肤'));
    	} else {
        	$this->showForm('默认的设计已经恢复。', true);
    	}
    }
    
}
