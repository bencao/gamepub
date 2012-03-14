<?php

require_once INSTALLDIR . '/plugins/mission/basemission.php';

class Mission_Sendvideonotice extends BaseMission {
	public function __construct($cur_user) {
		parent::__construct($cur_user);
		$this->clazz = 'sendvideonotice';
	}
	
	public function missionTitle() {
		return '发视频消息';
	}
	
	public function missionAward() {
		return '1个铜G币';
	}
	
	public function missionBriefDescription() {
		return '发现了有趣的视频？快跟大家分享吧~';
	}
	
	public function missionDescription() {
		return '让我们鲤鱼跃龙门，从游戏酒馆的老鸟，进一步提升成游戏酒馆的大虾！赶快发布您的视频消息吧！<br />点击“我的空间”，回到您的空间，在输入框下方找到“插入视频”按钮，点击它，把您想要发布的视频地址粘贴进去，输入您对这视频的文字评论，点击发送就大功告成了！';
	}
	
	public function missionFinishCondition() {
		return '发出一条视频消息';
	}
	
	public function redirectURL() {
		return common_local_url('home');
	}
	
	public function check() {
		$notices = new Notice();
		$notices->whereAdd('user_id = ' . $this->cur_user->id);
		$notices->whereAdd('is_banned = 0');
		$notices->whereAdd('content_type = 3');
		$notices->whereAdd('topic_type <> 4');
//        $notices->is_delete = 0;
        $cnt = (int) $notices->count('distinct id');
    	
		return $cnt >= 1 && parent::check();
	}
	
	public function award() {
		User_grade::addScore($this->cur_user->id, 10);
		return '完成视频发送的您已经可以轻松玩转游戏酒馆，拉上老友，侃侃聊聊，结识新友，共同游戏。<br />恭喜您获得了1个铜G币的奖励！';
	}
}