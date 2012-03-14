<?php

if (!defined('SHAISHAI')) { exit(1); }

class FavormusicAction extends ShaiAction
{
    function handle($args)
    {
    	parent::handle($args);
    	$nid = $this->trimmed('id');
    	
    	$notice = Notice::staticGet($nid);
    	
    	if (! $notice) {
    		 $this->showError($nid, '收藏的消息不存在');
    		 return;
    	}
    	
        //add commit
        $faveGroups = Fave_group::getFaveGroup($this->cur_user->id, '音乐收藏');
        if (!empty($faveGroups)) {
			$favegroup = $faveGroups[0];
			$favegroupId = $favegroup->id;
        } else {
        	$favegroup = Fave_group::addNew($this->cur_user, '音乐收藏');
        	$favegroupId = $favegroup->id;
        }
	    	        
        if ($this->cur_user->hasFave($notice)) {
            $this->showError($nid, '这条消息已收藏');
        	return;
        }	
        $fave = Fave::addNew($this->cur_user, $notice, $favegroupId);
        if (!$fave) {
        	$this->showError($nid, '无法创建收藏');
            return;
        }
        
        // adjust user score, notice owner +2
        User_grade::addScore($notice->user_id, 2);
        
        $this->notify($notice, $this->cur_user);
        
        //清理缓存
        $this->cur_user->blowFavesCache();
	        
    	$this->showSuccess($nid, '收藏成功');
    }
    
	function showSuccess($id, $msg) {
    	echo "<result><id>" . $id . "</id><value>true</value><msg>" . $msg . "</msg></result>";
    }
    
    function showError($id, $msg) {
    	echo "<result><id>" . $id . "</id><value>false</value><msg>" . $msg . "</msg></result>";
    }
    
	//某条消息被收藏时, 可以使用邮件通知, 或者通过系统通知.
    function notify($notice, $user)
    {
        $other = User::staticGet('id', $notice->user_id);
        if ($other && $other->id != $user->id) {
        	$otherProfile = $other->getProfile();
            if ($otherProfile->email && $otherProfile->emailnotifyfav) {
                mail_notify_fave($otherProfile, $user->getProfile(), $notice);
            }
            // XXX: notify by IM
            // XXX: notify by SMS
        }
    }
}

?>