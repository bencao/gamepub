<?php

require_once INSTALLDIR . '/plugins/mission/basemission.php';

class Mission_Visitexperiences extends BaseMission {
	public function __construct($cur_user) {
		parent::__construct($cur_user);
		$this->clazz = 'visitexperiences';
	}
	
	public function missionTitle() {
		return '学习游戏经验';
	}
	
	public function missionAward() {
		return '1个铜G币';
	}
	
	public function missionBriefDescription() {
		return 'GamePub上有很多精华的游戏攻略和技巧，去看看吧！';
	}
	
	public function missionDescription() {
		return '酒馆地带精彩纷呈，让我们一起看一看酒馆里的游友们贡献出了多少游戏经验？如何导航：点击网页左上方“酒馆地带”→点击左侧栏目中的“游戏经验”。';
	}
	
	public function missionFinishCondition() {
		return '查看一次“游戏经验”';
	}
	
	public function redirectURL() {
		return common_local_url('experiences');
	}
	
	public function award() {
		User_grade::addScore($this->cur_user->id, 10);
		return '这些经验是否对您有帮助？您也可以写出自己的经验，成为人人敬仰的游戏达人。只要在您的发言中把话题选为“经验”，您的发言就会进入这里。<br />恭喜您获得了1个铜G币的奖励！';
	}
}