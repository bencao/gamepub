<?php

require_once INSTALLDIR . '/plugins/mission/basemission.php';

class Mission_Visitprofile extends BaseMission {
	public function __construct($cur_user) {
		parent::__construct($cur_user);
		$this->clazz = 'visitprofile';
	}
	
	public function missionTitle() {
		return '查看个人页面';
	}
	
	public function missionAward() {
		return '1个铜G币';
	}
	
	public function missionBriefDescription() {
		return '预览一下您的门面，能表现出您的个性不？';
	}
	
	public function missionDescription() {
		return '想要知道在别人眼中的您的页面是什么样子吗？打开个人页面，一目了然。<br />点击网页上方您的用户名，直接进入个人页面。';
	}
	
	public function missionFinishCondition() {
		return '查看一次个人页面';
	}
	
	public function redirectURL() {
		return common_local_url('showstream', array('uname' => $this->cur_user->uname));
	}
	
	public function award() {
		User_grade::addScore($this->cur_user->id, 10);
		return '这就是其他游友查看您资料的时候显示出来的页面，您也同样可以点击其他游友的用户名到他们的个人页面去参观。<br />恭喜您获得了1个铜G币的奖励！';
	}
}