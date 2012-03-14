<?php

require_once INSTALLDIR . '/plugins/mission/basemission.php';

class Mission_Sendaudionotice extends BaseMission {
	public function __construct($cur_user) {
		parent::__construct($cur_user);
		$this->clazz = 'sendaudionotice';
	}
	
	public function missionTitle() {
		return '发音乐消息';
	}
	
	public function missionAward() {
		return '1个铜G币';
	}
	
	public function missionBriefDescription() {
		return '跟大家分享，“我在听什么”？';
	}
	
	public function missionDescription() {
		return '发出您的第一个音乐消息，成长为游戏酒馆的老鸟，彻底摆脱菜鸟身份吧！<br />点击“我的空间”，回到您的空间，在输入框下方找到“插入音乐”按钮，点击她，把您想要发布的音乐网址粘贴进去，输入您对这音乐的心情、看法，点击发送就大功告成了！';
	}
	
	public function missionFinishCondition() {
		return '发出一条音乐消息';
	}
	
	public function redirectURL() {
		return common_local_url('home');
	}
	
	public function check() {
		$notices = new Notice();
		$notices->whereAdd('user_id = ' . $this->cur_user->id);
		$notices->whereAdd('is_banned = 0');
		$notices->whereAdd('content_type = 2');
		$notices->whereAdd('topic_type <> 4');
//        $notices->is_delete = 0;
        $cnt = (int) $notices->count('distinct id');
    	
		return $cnt >= 1 && parent::check();
	}
	
	public function award() {
		User_grade::addScore($this->cur_user->id, 10);
		return '会分享音乐消息的您，已经是游戏酒馆的老鸟了。喜欢音乐的您可以随意分享您喜欢的音乐，独乐乐不如众乐乐。<br />恭喜您获得了1个铜G币的奖励！';
	}
}