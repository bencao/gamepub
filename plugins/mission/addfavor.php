<?php

require_once INSTALLDIR . '/plugins/mission/basemission.php';

class Mission_Addfavor extends BaseMission {
	public function __construct($cur_user) {
		parent::__construct($cur_user);
		$this->clazz = 'addfavor';
	}
	
	public function missionTitle() {
		return '收藏游戏酒馆';
	}
	
	public function missionAward() {
		return '1个铜G币';
	}
	
	public function missionBriefDescription() {
		return '为了更方便的访问您的酒馆小窝，把链接加入收藏夹吧。';
	}
	
	public function missionDescription() {
		return '酒馆是您舒适的家，是您个性展示和认识朋友的主要场所。把酒馆加入网页收藏夹，以后回来就更加方便咯。';
	}
	
	public function missionFinishCondition() {
		return '将游戏酒馆加入浏览器收藏夹';
	}
	
	public function redirectURL() {
		return 'javascript:window.external.AddFavorite("http://www.gamepub.cn", "GamePub，中国最大游戏社区")';
	}
	
	public function award() {
		User_grade::addScore($this->cur_user->id, 10);
		return '感谢您对酒馆的支持！<br />恭喜您获得了1个铜G币的奖励！';
	}
}