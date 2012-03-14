<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class GetsubscriptionAction extends ShaiAction
{
	function prepare($args) {
		if (! parent::prepare($args)) {return false;}
		$this->tname = $this->trimmed('tname');
		$this->tid = $this->trimmed('tid');
		
		if (empty($this->tname)) {
			$this->clientError('缺少名称参数');
			return false;
		}
		
		return true;
	}
	
	function handle($args) {
		$exceptionDao = $this->cur_user->getTaggedSubscriptions($this->tname);
		
		$exceptionIds = array();
		
		while ($exceptionDao->fetch()) {
			$exceptionIds[] = $exceptionDao->id;
		}
		
//		if ($this->tname == '未分组') {
//			$subs = $this->cur_user->getUntaggedSubscriptions();
//		} else {
//			$subs = $this->cur_user->getTaggedSubscriptions($this->tname);
//		}
		$subs = $this->cur_user->getSubscriptions();
		
		$subscriptions = array('tid' => $this->tid, 'item' => array());
		
		while ($subs->fetch()) {
			if (! in_array($subs->id, $exceptionIds)) {
				$subscriptions['item'][] = array('avatar' => $subs->avatarUrl(AVATAR_STREAM_SIZE),
					'uid' => $subs->id, 'nickname' => $subs->nickname, 'uname' => $subs->uname);
			}
		}
		
		$this->showJsonResult($subscriptions);
	}
}