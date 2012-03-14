<?php

require_once INSTALLDIR . '/plugins/mission/basemission.php';

class Mission_Visitpubarea extends BaseMission {
	public function __construct($cur_user) {
		parent::__construct($cur_user);
		$this->clazz = 'visitpubarea';
	}
	
	public function missionTitle() {
		return '看看酒馆地带';
	}
	
	public function missionAward() {
		return '1个铜G币';
	}
	
	public function missionBriefDescription() {
		return '探索一下酒馆地带，看看那儿有什么';
	}
	
	public function missionDescription() {
		return '也许与同游戏的人们互动还不能满足您的要求，没关系，游戏酒馆正是为此而设立，点击“酒馆地带”看看我们所有游友的精彩呈现。如何导航：“酒馆地带”按钮的位置在页首导航栏的左侧。';
	}
	
	public function missionFinishCondition() {
		return '查看一次“酒馆地带”';
	}
	
	public function redirectURL() {
		return common_local_url('public');
	}
	
	public function award() {
		User_grade::addScore($this->cur_user->id, 10);
		return '酒馆地带是一个综合区域，来自五湖四海，各个不同游戏的游友都可以在这上面畅所欲言，展现自我。赶快炫出自己吧！<br />恭喜您获得了1个铜G币的奖励！';
	}
}