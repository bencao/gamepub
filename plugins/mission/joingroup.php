<?php

require_once INSTALLDIR . '/plugins/mission/basemission.php';

class Mission_Joingroup extends BaseMission {
	public function __construct($cur_user) {
		parent::__construct($cur_user);
		$this->clazz = 'joingroup';
	}
	
	public function missionTitle() {
		return '加入' . GROUP_NAME();
	}
	
	public function missionAward() {
		return '1个铜G币';
	}
	
	public function missionBriefDescription() {
		return '加入兴趣团体，成为光荣的一员。';
	}
	
	public function missionDescription() {
		return '游戏酒馆中' . GROUP_NAME() . '五彩缤纷，除了与游戏同步的真实' . GROUP_NAME() . '，还有丰富多彩的生活' . GROUP_NAME() . '，选择您喜欢的加入吧。<br />点击网页左上方的“' . GROUP_NAME() . '”按钮，进入' . GROUP_NAME() . '页面后在左侧栏目中可以找到“游戏' . GROUP_NAME() . '”与“生活' . GROUP_NAME() . '”。如果没有发现你想要找的' . GROUP_NAME() . '，还可以使用搜索功能哦。';
	}
	
	public function missionFinishCondition() {
		return '加入一个其他游友创建的' . GROUP_NAME();
	}
	
	public function redirectURL() {
		return common_local_url('lifegroups');
	}
	
	public function check() {
		$ug = new User_group();
		$gm = new Group_member();
		$ug->joinAdd($gm);
		$ug->whereAdd('user_group.ownerid <> ' . $this->cur_user->id);
		$ug->whereAdd('group_member.user_id = ' . $this->cur_user->id);
		
    	return $ug->count() > 0 && parent::check();
	}
	
	public function award() {
		User_grade::addScore($this->cur_user->id, 10);
		return '您可以加入您在游戏中所属的公会，也可以加入您喜欢的生活公会。<br />恭喜您获得了1个铜G币的奖励！';
	}
}