<?php

require_once INSTALLDIR . '/plugins/mission/basemission.php';

class Mission_Visithof extends BaseMission {
	public function __construct($cur_user) {
		parent::__construct($cur_user);
		$this->clazz = 'visithof';
	}
	
	public function missionTitle() {
		return '看看名人堂';
	}
	
	public function missionAward() {
		return '1个铜G币';
	}
	
	public function missionBriefDescription() {
		return '探索一下名人堂，猜猜看会有哪些大腕儿呢？';
	}
	
	public function missionDescription() {
		return '游戏酒馆有一个特殊的地方，那里汇聚了游戏界的名人，那就是名人堂！一起去名人堂逛一逛，顺便学会怎么样“关注”您感兴趣的人吧。如何导航：点击网页左上方 “酒馆地带”→点击左侧栏目中的“名人堂”。';
	}
	
	public function missionFinishCondition() {
		return '查看一次“名人堂”';
	}
	
	public function redirectURL() {
		return common_local_url('halloffame');
	}
	
	public function award() {
		User_grade::addScore($this->cur_user->id, 10);
		return '进入名人堂，与游戏名人亲密接触。关注游戏名人，TA的一举一动尽在您的掌握中。<br />友情提示：只有您关注的人的消息才会出现在您的“我的空间”中，想要看到更多您感兴趣的信息，只要关注您感兴趣的人就可以实现。<br />恭喜您获得了1个铜G币的奖励！';
	}
}