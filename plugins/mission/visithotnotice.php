<?php

require_once INSTALLDIR . '/plugins/mission/basemission.php';

class Mission_Visithotnotice extends BaseMission {
	public function __construct($cur_user) {
		parent::__construct($cur_user);
		$this->clazz = 'visithotnotice';
	}
	
	public function missionTitle() {
		return '探索热门消息';
	}
	
	public function missionAward() {
		return '1个铜G币';
	}
	
	public function missionBriefDescription() {
		return '去看看目前高居前位的神帖吧。';
	}
	
	public function missionDescription() {
		return '热门消息通常都是在某方面具有很高价值的消息，可能是很重要的通知，也可能是非常娱乐的恶搞消息。我们将热门消息按照文字、音乐、视频、图片分为了四类，您不妨全部看一看。如何导航：点击网页左上方“酒馆地带”→点击左侧栏目中的“热门消息”。';
	}
	
	public function missionFinishCondition() {
		return '查看一次“热门消息”';
	}
	
	public function redirectURL() {
		return common_local_url('hotnotice');
	}
	
	public function award() {
		User_grade::addScore($this->cur_user->id, 10);
		return '您有没有掌握热门消息？或者是您随便说一句话都会成为游戏酒馆的热门？尝试一下，一切皆有可能！<br />恭喜您获得了1个铜G币的奖励！';
	}
}