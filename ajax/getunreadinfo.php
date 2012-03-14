<?php
if (!defined('SHAISHAI')) { exit(1); }

class GetunreadinfoAction extends ShaiAction
{
	var $sync;
	
	function prepare($args) {
		if (! parent::prepare($args)) {return false;}
		$this->sync = $this->trimmed('latest_timestamp');
		return true;
	}
	
 	function handle($args)
    {
    	parent::handle($args);
		
    	// get Remind messages
    	// fetch at most 3 replies, 3 messages, 3 discussions each time
    	
    	$hasSync = $this->sync 
    		&& $this->sync != 'null'
    		&& is_numeric($this->sync);
    	
    	if ($hasSync) {
    		$since_time = common_sql_date($this->sync);
	    	
    		$repm = Reply::getUnreadReplyByUserid($this->cur_user->id, 3, $since_time);
	    	$msgm = Message::getUnreadMessageByUserid($this->cur_user->id, 3, $since_time);
	    	$dism = Discussion_unread::getDiscussionByUserid($this->cur_user->id, 3, $since_time);
    	} else {
	    	$repm = Reply::getUnreadReplyByUserid($this->cur_user->id);
	    	$msgm = Message::getUnreadMessageByUserid($this->cur_user->id);
	    	$dism = Discussion_unread::getDiscussionByUserid($this->cur_user->id);
    	}
    	
    	$reminds = array();
    	
    	while ($repm->fetch()) {
    		$sender = User::staticGet('id', $repm->sender_id);
    		$index = strtotime($repm->modified);
    		while (array_key_exists($index, $reminds)) {
    			$index ++;
    		}
    		$istringer = new XMLStringer();
    		$istringer->elementStart('li', 'rep');
    		$istringer->element('a', array('href' => common_path($sender->uname), 'target' => '_blank'), mb_substr($sender->nickname, 0, 3, 'utf-8'));
    		$istringer->element('a', array('href' => common_path('conversation/' . $repm->notice_id)), '回复');
    		$istringer->text('了你');
    		$istringer->elementEnd('li');
    		
    		$reminds[$index] = $istringer->getString();
    	}
    	
    	while ($msgm->fetch()) {
    		$sender = User::staticGet('id', $msgm->from_user);
    		$index = strtotime($msgm->modified);
    		while (array_key_exists($index, $reminds)) {
    			$index ++;
    		}
    		
    		$istringer = new XMLStringer();
    		$istringer->elementStart('li', 'mes');
    		$istringer->element('a', array('href' => common_path($sender->uname), 'target' => '_blank'), mb_substr($sender->nickname, 0, 3, 'utf-8'));
    		$istringer->text('给你发送了');
    		$istringer->element('a', array('href' => common_path($this->cur_user->uname . '/inbox')), '私信');
    		$istringer->elementEnd('li');
    		
    		$reminds[$index] = $istringer->getString();
    	}
    	
    	while ($dism->fetch()) {
    		$sender = User::staticGet('id', $dism->sender_id);
    		$index = strtotime($dism->created);
    		while (array_key_exists($index, $reminds)) {
    			$index ++;
    		}
    		
    		$istringer = new XMLStringer();
    		$istringer->elementStart('li', 'dis');
    		$istringer->element('a', array('href' => common_path($sender->uname), 'target' => '_blank'), mb_substr($sender->nickname, 0, 3, 'utf-8'));
    		$istringer->element('a', array('href' => common_path('discussionlist/' . $dism->notice_id)), '评论');
    		$istringer->text('了你的消息');
    		$istringer->elementEnd('li');
    		
    		$reminds[$index] = $istringer->getString();
    	}
    	
    	$rout = new XMLStringer();
    	
    	if (count($reminds) > 0) {
    		// order by time
	    	ksort($reminds);
	    	
	    	$rout->elementStart('dl', array('class' => 'mbox', 'style' => 'display:none;'));
	    	$rout->elementStart('dt');
	    	$rout->text('最新提醒');
	    	$rout->elementEnd('dt');
	    	$rout->elementStart('dd');
	    	$rout->elementStart('ul');
	    	foreach ($reminds as $r) {
	    		$rout->raw($r);
	    	}
	    	if (! $hasSync) {
	    		$rout->elementStart('li', 'seemore');
    			$rout->element('a', array('href' => '#'), '全部忽略');
    			$rout->elementEnd('li');
	    	}
	    	$rout->elementEnd('ul');
	    	$rout->elementEnd('dd');
	    	$rout->elementEnd('dl');
    	}
    	
    	
    	// get Group messages
    	$gun = Group_unread_notice::getUnreadGroupsByUserid($this->cur_user->id);
    	$out = new XMLStringer();
    	if ($gun && $gun->N > 0) {
    		$out->elementStart('dl', array('class' => 'mbox', 'style' => 'display:none;'));
    		$out->elementStart('dt');
    		$out->text('我加入的' . GROUP_NAME());
    		$out->element('em', null, '新消息');
    		$out->elementEnd('dt');
    		$out->elementStart('dd');
    		$out->elementStart('ul');
    		while ($gun->fetch()) {
    			$out->elementStart('li');
    			$out->elementStart('div', 'avatar');
    			$groups = User_group::staticGet('id', $gun->group_id);
    			$logo = ($groups->mini_logo) ?
        			$groups->mini_logo : User_group::defaultLogo(GROUP_LOGO_MINI_SIZE);
    			$out->element('img', array('src' => $logo));
    			$out->elementEnd('div');
    			$out->elementStart('p', 'nickname');
    			$out->element('a', array('href' => common_path('group/' . $groups->id)), $groups->getBestName());
    			$out->elementEnd('p');
    			$out->element('em', array('id' => 'group-'.$groups->id), $gun->notice_num);
    			$out->elementEnd('li');
    		}
    		
    		$out->elementStart('li', 'seemore');
    		$out->element('a', array('href' => '#'), '全部忽略');
    		$out->elementEnd('li');
    		$out->elementEnd('ul');
    		$out->elementEnd('dd');
    		$out->elementEnd('dl');
    	}
    	
        $this->showJsonResult(array(
        	'remind' => $rout->getString(),
        	'rcnt' => count($reminds),
        	'group' => $out->getString(),
        	'timestamp' => time()
        ));
    }
}
