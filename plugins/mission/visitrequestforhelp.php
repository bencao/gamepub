<?php

require_once INSTALLDIR . '/plugins/mission/basemission.php';

class Mission_Visitrequestforhelp extends BaseMission {
	
	public function __construct($cur_user) {
		parent::__construct($cur_user);
		$this->clazz = 'visitrequestforhelp';
	}
	
	public function missionTitle() {
		return '帮助其他游友';
	}
	
	public function missionAward() {
		return '1个铜G币';
	}
	
	public function missionBriefDescription() {
		return '看一下都有谁在求助，帮TA一把';
	}
	
	public function missionDescription() {
		return '酒馆地带精彩纷呈，让我们一起看一看酒馆里是否有游友需要您的帮助？如何导航：点击网页左上方“酒馆地带”→点击左侧栏目中的“游戏求助”。';
	}
	
	public function missionFinishCondition() {
		return '查看一次“游戏求助”';
	}
	
	public function redirectURL() {
		return common_local_url('requestforhelp');
	}
	
	public function award() {
		User_grade::addScore($this->cur_user->id, 10);
		return '在这里您可以帮助游友，也可以向游友求助。只要在您的发言中把话题选为“求助”，您的发言就会进入这里。<br />恭喜您获得了1个铜G币的奖励！';
	}
}