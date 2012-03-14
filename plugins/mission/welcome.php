<?php

require_once INSTALLDIR . '/plugins/mission/basemission.php';

class Mission_Welcome extends BaseMission {
	
	public function __construct($cur_user) {
		parent::__construct($cur_user);
		$this->clazz = 'welcome';
	}
	
	public function missionTitle() {
		return '酒馆欢迎您';
	}
	
	public function missionAward() {
		return '1个铜G币';
	}
	
	public function missionBriefDescription() {
		return '查看系统消息中的欢迎语吧';
	}
	
	public function missionDescription() {
		return '欢迎来到游戏酒馆开始您的快乐之旅，请点击右侧喇叭图标(下方有“通知”两字)查看我们给您带来的真诚问候。';
	}
	
	public function missionFinishCondition() {
		return '查看一次系统通知';
	}
	
	public function check() {
		$rs = new Receive_sysmes();
		$rs->whereAdd('user_id = ' . $this->cur_user->id);
		$rs->whereAdd('is_read = 0');
		$rs->find();
    	
		return ! $rs->fetch() && parent::check();
	}
	
	public function award() {
		User_grade::addScore($this->cur_user->id, 10);
		return '恭喜您获得了1个铜G币的奖励！';
	}
}