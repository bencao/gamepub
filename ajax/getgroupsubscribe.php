<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class GetgroupsubscribeAction extends ShaiAction
{
	function prepare($args) {
		if (! parent::prepare($args)) {return false;}
		$this->tname = $this->trimmed('tname');
		$this->tid = $this->trimmed('tid');
		
		$this->search = $this->trimmed('search');
		if($this->search==null){
			$this->search = '';
		}	
		
		$this->total = $this->trimmed('total');
		
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
		$subs = $this->cur_user->getSubscribers(($this->cur_page - 1)*9, 9, $this->search);
		$count = $this->total;
		if($this->total == -1)
			$count = $this->cur_user->getSubscribers(0, null, $this->search)->N;
			
		
		$next = $this->cur_page + 1;
		$preview = $this->cur_page - 1;
		if($this->cur_page*9 >= $count)
			$next = 0;
		//common_debug($count.'LLLL'.$subs->N);
		
		$subscribes = array('tid' => $this->tid, 'search'=> $this->search, 'next' => $next, 'preview' => $preview, 'page' => $this->cur_page, 'total' => $count, 'item' => array());
		
		while ($subs->fetch()) {
			if (! in_array($subs->id, $exceptionIds)) {
				$subscribes['item'][] = array('avatar' => $subs->avatarUrl(AVATAR_STREAM_SIZE),
					'uid' => $subs->id, 'nickname' => $subs->nickname, 'uname' => $subs->uname);
			}
		}
		
		$this->showJsonResult($subscribes);
	}
}