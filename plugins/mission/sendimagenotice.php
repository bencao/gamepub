<?php

require_once INSTALLDIR . '/plugins/mission/basemission.php';

class Mission_Sendimagenotice extends BaseMission {
	public function __construct($cur_user) {
		parent::__construct($cur_user);
		$this->clazz = 'sendimagenotice';
	}
	
	public function missionTitle() {
		return '发图片消息';
	}
	
	public function missionAward() {
		return '1个铜G币';
	}
	
	public function missionBriefDescription() {
		return '有经典图片？快快跟大家分享~';
	}
	
	public function missionDescription() {
		return '话说网络时代，无图无真相，既然我们已经发出了文字消息，不妨再放出图片让大家信服。<br />点击“我的空间”，回到您的空间，在输入框下方找到“插入图片”按钮，点击她，再点击“选择”按钮，选择您想要展现的图片。友情提示：图片大小请勿超过1M。';
	}
	
	public function missionFinishCondition() {
		return '发出一条图片消息';
	}
	
	public function redirectURL() {
		return common_local_url('home');
	}
	
	public function check() {
		$notices = new Notice();
		$notices->whereAdd('user_id = ' . $this->cur_user->id);
		$notices->whereAdd('is_banned = 0');
		$notices->whereAdd('content_type = 4');
		$notices->whereAdd('topic_type <> 4');
//        $notices->is_delete = 0;
        $cnt = (int) $notices->count('distinct id');
    	
		return $cnt >= 1 && parent::check();
	}
	
	public function award() {
		User_grade::addScore($this->cur_user->id, 10);
		return '上传图片就是这么简单，立刻上传图片，秀出风采！<br />恭喜您获得了1个铜G币的奖励！';
	}
}