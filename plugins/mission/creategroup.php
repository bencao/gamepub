<?php

require_once INSTALLDIR . '/plugins/mission/basemission.php';

class Mission_Creategroup extends BaseMission {
	public function __construct($cur_user) {
		parent::__construct($cur_user);
		$this->clazz = 'creategroup';
	}
	
	public function missionTitle() {
		return '创建' . GROUP_NAME();
	}
	
	public function missionAward() {
		return '3个铜G币';
	}
	
	public function missionBriefDescription() {
		return '占据先机，早建' . GROUP_NAME() . '，早发展。';
	}
	
	public function missionDescription() {
		return '不论您是游戏中叱咤风云的一方豪杰，还是能凝聚人心的核心人物，在游戏酒馆中都同样可以展现您的实力。抓紧时机创立' . GROUP_NAME() . '，在游戏酒馆中扯起大旗，招兵买马。<br />点击网页左上方的“' . GROUP_NAME() . '”按钮，选择创建' . GROUP_NAME() .'，任意选择创建游戏' . GROUP_NAME() . '或者是生活' . GROUP_NAME() . '均可以完成任务。';
	}
	
	public function missionFinishCondition() {
		return '创建一个' . GROUP_NAME();
	}
	
	public function redirectURL() {
		return common_local_url('newgroup');
	}
	
	public function check() {
		$ug = new User_group();
		$ug->whereAdd('ownerid = ' . $this->cur_user->id);
    	return $ug->count() > 0 && parent::check();
	}
	
	public function award() {
		User_grade::addScore($this->cur_user->id, 30);
		return '为了保证每个游戏，每个服务器中的公会与真实游戏中的吻合，而并非抢注。游戏酒馆启用了多人共同创建机制，恶意抢注申诉机制，请大家不要恶意抢注。<br />恭喜您获得了3个铜G币的奖励！';
	}
}