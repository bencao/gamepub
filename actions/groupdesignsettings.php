<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Set a group's design
 *
 * Saves a design for a given group
 *
 * @category Settings
 * @package  LShai
 */

class GroupdesignsettingsAction extends GroupAdminAction
{
    var $group = null;

    function prepare($args)
    {
        if (! parent::prepare($args)) {return false;}

        if (! $this->is_group_admin) {
            $this->clientError('只有管理员才能编辑该' . GROUP_NAME() , 403);
            return false;
        }

        return true;
    }

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
    
    function handle($args)
    {
    	parent::handle($args);
    	
    	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    		$this->handlePost();
    	} else {
    		$this->showForm();
    	}
    }
    
	function showForm($msg=null, $success=false)
    {
    	$this->cur_design = $this->cur_group->getDesign();
    	if (! $this->cur_design) {
    		$this->cur_design = Design::defaultGameDesign(
    			Game::staticGet('id', $this->cur_group->getOwner()->game_id));
    	}
    	$this->addPassVariable('design', $this->cur_design);
    	$this->addPassVariable('cur_group_design', $this->cur_design);
    	$this->addPassVariable('self_designs', 
    		Group_self_design::getDesignsByGroup($this->cur_group));
    	
    	$this->addPassVariable('msg', $msg);
    	$this->addPassVariable('success', $success);
    	$this->displayWith('GroupdesignsettingsHTMLTemplate');
    }
    
    function applyDesign($isAjax = false) {
    	$design = $this->trimmed('design_id');
    	
    	if ($this->cur_group->applyDesign($design)) {
    		if ($isAjax) {
    			$this->showJsonResult(array('result' => 'true', 'url' => common_path('group/' . $this->cur_group->id), 'msg' => '已成功应用' . GROUP_NAME() . '自定义皮肤'));
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
    	$newsubpath = Avatar::groupcsssubpath($this->cur_group->id);
    	
    	if (! file_exists($newsubpath)) {
			mkdir($newsubpath, 0777, true);
		}
		
		return $newsubpath;
    }
    
    function saveDesign($isAjax = false) {
    	// 先对提交的数据进行数据有效性检查
    	if ($this->_validate()) {
    		// 启动事务
    		$this->cur_group->query('BEGIN');
    		
    		// 保存背景和css文件路径
    		// 因为背景图上传的时候，存放在了当前用户的临时文件目录下
    		$tmpsubpath = Avatar::tmpsubpath($this->cur_user->id);
    		
    		$newsubpath = $this->_prepareCssDir();
			
    		// 进行后续处理前，需要将文件移动到群组的css文件存放目录
    		if ($this->showimage == '1') {
	    		if (! empty($this->backgroundimage)) {
		    		$this->backgroundimage = common_path($this->_moveBackgroundImage($tmpsubpath, $newsubpath, $this->backgroundimage));
		    	} else {
		    		// 继承现有backgroundimage
		    		if ($this->cur_group->design_id == 0) {
		    			$this->backgroundimage = common_path('theme/default/i/bg.jpg');
		    		} else {
		    			$this->backgroundimage = $this->cur_group_design->backgroundimage;
		    		}
		    	}
    		} else {
    			$this->backgroundimage = null;
    		}
    		
			// 新建一个皮肤，并保存皮肤对应的css文件
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
    			$this->cur_group->query('ROLLBACK');
    			if ($isAjax) {
    				$this->showJsonResult(array('result' => 'false'));
    			} else {
    				$this->showForm('保存失败');
    			}
    			return;
    		}
    		
    		// 保存一条用户自定义皮肤信息
    		Group_self_design::saveNew($this->cur_group, $design);
    		
    		// 应用个性化皮肤
    		$this->cur_group->applyDesign($design->id);
    		
    		// 提交事务
    		$this->cur_group->query('COMMIT');
    		
    		if ($isAjax) {
    			$this->showJsonResult(array('result' => 'true', 'url' => common_path('group/' . $this->cur_group->id . '?theme=true'), 'msg' => '已成功保存自定义皮肤'));
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
    	
    	if (Group_self_design::deleteDesign($this->cur_group->id, $toDeleteDesign)) {
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
    	$this->cur_group->clearDesign();
//
//        $this->addPassVariable('design', $this->cur_design);
//        
//        $this->addPassVariable('self_designs', User_self_design::getDesignsByUserId($this->cur_user->id));
        
    	if ($isAjax) {
    		$this->showJsonResult(array('result' => 'true', 'url' => common_path('group/' . $this->cur_group->id . '?theme=true'), 'msg' => '已恢复为默认皮肤'));
    	} else {
        	$this->showForm('已经恢复为默认皮肤。', true);
    	}
    }

}
