<?php

require_once INSTALLDIR . '/plugins/mission/basemission.php';

class Mission_Visitfunnypeople extends BaseMission {
	public function __construct($cur_user) {
		parent::__construct($cur_user);
		$this->clazz = 'visitfunnypeople';
	}
	
	public function missionTitle() {
		return '找寻有趣游友';
	}
	
	public function missionAward() {
		return '1个铜G币';
	}
	
	public function missionBriefDescription() {
		return '如果还是单身，开始寻找您的另一半吧！';
	}
	
	public function missionDescription() {
		return '酒馆地带精彩纷呈，让我们一起看一看谁是最有趣的游友？多发消息多跟别人互动，很可能下一个就是独特的你！';
	}
	
	public function missionFinishCondition() {
		return '查看一次“有趣游友”';
	}
	
	public function redirectURL() {
		return common_local_url('funnypeople');
	}
	
	public function award() {
		User_grade::addScore($this->cur_user->id, 10);
		return '美女帅哥一大把啊！是不是流口水了呢？赶快“关注”他们，没准他/她就此成为您的挚交好友。<br />恭喜您获得了1个铜G币的奖励！';
	}
}