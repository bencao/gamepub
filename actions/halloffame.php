<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class HalloffameAction extends ShaiAction
{
	function __construct() {
		parent::__construct();
		$this->no_anonymous = false;
	}
	
	function handle($args) {
		parent::handle($args);
		
		$gid = $this->trimmed('gid');
		
		if (is_numeric($gid)) {
			$ids = Profile::getVipIds(20, $gid);
			$game = Game::staticGet($gid);
		} else {
			$ids = Profile::getVipIds(20);
			$game = null;
		}
		
		$profile = Profile::getProfileByIds($ids, 0, 9999, 'id desc');
		
		$latestNotices = Notice::getLatestVipNoticeIds(20);
		
		if (! empty($latestNotices)) {
			$starNoticeId = common_random_fetch($latestNotices, 1);
			$starNotice = Notice::staticGet('id', $starNoticeId[0]);
		} else {
			$starNotice = false;
		}
		
		$this->addPassVariable('hotgames', Game::listHots());
		$this->addPassVariable('vippeople', $profile);
		$this->addPassVariable('starnotice', $starNotice);
		$this->addPassVariable('cur_game', $game);
		
		$this->displayWith('HalloffameHTMLTemplate');
	}
}