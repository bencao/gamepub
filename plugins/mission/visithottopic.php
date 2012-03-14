<?php

require_once INSTALLDIR . '/plugins/mission/basemission.php';

class Mission_Visithottopic extends BaseMission {
	public function __construct($cur_user) {
		parent::__construct($cur_user);
		$this->clazz = 'visithottopic';
	}
	
	public function missionTitle() {
		return '看看火爆话题';
	}
	
	public function missionAward() {
		return '1个铜G币';
	}
	
	public function missionBriefDescription() {
		return '现在大家谈的最潮的话题是什么？是凤姐还是犀利哥？';
	}
	
	public function missionDescription() {
		return '酒馆地带精彩纷呈，让我们一起看一看现在最火爆的话题吧！如何导航：点击网页左上方 “酒馆地带”→点击左侧栏目中的“火爆话题”。';
	}
	
	public function missionFinishCondition() {
		return '查看一次“火爆话题”';
	}
	
	public function redirectURL() {
		return common_local_url('hottopics');
	}
	
	public function award() {
		User_grade::addScore($this->cur_user->id, 10);
		return '火爆话题够不够火爆呢？不够？那您赶紧发几条更火爆的消息吧！<br />恭喜您获得了1个铜G币的奖励！';
	}
}