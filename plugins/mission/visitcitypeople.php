<?php

require_once INSTALLDIR . '/plugins/mission/basemission.php';

class Mission_Visitcitypeople extends BaseMission {
	public function __construct($cur_user) {
		parent::__construct($cur_user);
		$this->clazz = 'visitcitypeople';
	}
	
	public function missionTitle() {
		return '查看同城游友';
	}
	
	public function missionAward() {
		return '1个铜G币';
	}
	
	public function missionBriefDescription() {
		return '找寻同城玩家，结交志同道合朋友。';
	}
	
	public function missionDescription() {
		return '认识新朋友，不忘老朋友。看一看与您同城的有哪些游友，是不是有您的老朋友？是不是有您想要结实的新朋友？如何导航：点击网页左上方“酒馆地带”→点击左侧栏目中的“同城游友”。';
	}
	
	public function missionFinishCondition() {
		return '查看一次“同城游友”';
	}
	
	public function redirectURL() {
		return common_local_url('citypeople');
	}
	
	public function award() {
		User_grade::addScore($this->cur_user->id, 10);
		return '远亲不如近邻，在网络上，同城的游友们应该就是您的近邻了，快去认识他们吧。<br />恭喜您获得了1个铜G币的奖励！';
	}
}