<?php

require_once INSTALLDIR . '/plugins/mission/basemission.php';

class Mission_Usetheme extends BaseMission {
	public function __construct($cur_user) {
		parent::__construct($cur_user);
		$this->clazz = 'usetheme';
	}
	
	public function missionTitle() {
		return '定制个人主页';
	}
	
	public function missionAward() {
		return '3个铜G币';
	}
	
	public function missionBriefDescription() {
		return '打造彰显个性，独一无二的个人主页。';
	}
	
	public function missionDescription() {
		return '是否觉得大家背景都一样无法体现您的个性？别担心，游戏酒馆为您做了精心准备，背景图案，背景色调任您调！赶快打造独特个性的个人首页吧！<br />点击网页右上方“我要换肤”链接。';
	}
	
	public function missionFinishCondition() {
		return '保存并应用自己的专属皮肤';
	}
	
	public function redirectURL() {
		return common_local_url('showstream', array('uname' => $this->cur_user->uname), array('theme' => 'true'));
	}
	
	public function check() {
    	return $this->cur_user->design_id != 0 && parent::check();
	}
	
	public function award() {
		User_grade::addScore($this->cur_user->id, 30);
		return '怎么样，个性十足的页面打造出来了吧？不喜欢？随时可以更换！<br />恭喜您获得了3个铜G币的奖励！';
	}
}