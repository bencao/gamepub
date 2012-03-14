<?php

require_once INSTALLDIR . '/plugins/mission/basemission.php';

class Mission_Visitrank extends BaseMission {
	
	public function __construct($cur_user) {
		parent::__construct($cur_user);
		$this->clazz = 'visitrank';
	}
	
	public function missionTitle() {
		return '查看风云榜';
	}
	
	public function missionAward() {
		return '1个铜G币';
	}
	
	public function missionBriefDescription() {
		return '有人的地方就有江湖，看谁是英雄。';
	}
	
	public function missionDescription() {
		return '天下风云出我辈，一入江湖岁月催。进入江湖怎能默默无闻？看一看如今游戏酒馆的“风云榜”，看看都有谁在叱诧风云！如何导航：点击网页左上方“酒馆地带”→点击左侧栏目中的“风云榜”。';
	}
	
	public function missionFinishCondition() {
		return '查看一次“风云榜”';
	}
	
	public function redirectURL() {
		return common_local_url('rank');
	}
	
	public function award() {
		User_grade::addScore($this->cur_user->id, 10);
		return '风云榜整合了各种大大小小的排行，努力进入榜中，成为闪耀的新星！<br />恭喜您获得了1个铜G币的奖励！';
	}
}