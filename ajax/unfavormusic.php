<?php

if (!defined('SHAISHAI')) { exit(1); }

class UnfavormusicAction extends ShaiAction
{
    function handle($args)
    {
    	parent::handle($args);
    	$nid = $this->trimmed('id');
    	
    	$notice = Notice::staticGet($nid);
    	
    	if (! $notice) {
    		 $this->showError($nid, '取消收藏的消息不存在');
    		 return;
    	}
    	
    	$fave = new Fave();
        $fave->query('BEGIN');
        $fave->user_id   = $this->cur_user->id;
        $fave->notice_id = $notice->id;
        if (!$fave->find(true)) {
            $this->showError($nid, '消息未被收藏');
            return;
        }
        $favegroupid = $fave->favegroup_id;
        $result = $fave->delete();
        if (!$result) {
            common_log_db_error($fave, 'DELETE', __FILE__);
            $this->showError($nid, '无法删除此收藏');
            return;
        }
        
    	Notice_heat::addHeat($notice->id, -3);
		$fave->query('COMMIT');    	
    	
        $favgroup = Fave_group::staticGet('id', $favegroupid);
    	$favgroup->blowFavesCache();
        
        //清理缓存
        $this->cur_user->blowFavesCache();
	        
    	$this->showSuccess($nid, '取消收藏成功');
    	
    }
    
	function showSuccess($id, $msg) {
    	echo "<result><id>" . $id . "</id><value>true</value><msg>" . $msg . "</msg></result>";
    }
    
    function showError($id, $msg) {
    	echo "<result><id>" . $id . "</id><value>false</value><msg>" . $msg . "</msg></result>";
    }
    
}

?>